<?php
class db extends PDO {
    private $error;
    private $sql;
    private $bind;
    private $errorCallbackFunction;
    private $errorMsgFormat;

    public function __construct($dsn="", $user="", $passwd="") {
        $options = array(
                PDO::ATTR_PERSISTENT => true, 
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try {
                parent::__construct($dsn, $user, $passwd, $options);
        }
         catch (PDOException $e) {
                        $this->error = $e->getMessage();
        }
    }

    private function debug() {
        if(!empty($this->errorCallbackFunction)) {
            $error = array("Error" => $this->error);
            if(!empty($this->sql))
                    $error["SQL Statement"] = $this->sql;
            if(!empty($this->bind))
                    $error["Bind Parameters"] = trim(print_r($this->bind, true));

            $backtrace = debug_backtrace();
            if(!empty($backtrace)) {
                    foreach($backtrace as $info) {
                            if($info["file"] != __FILE__)
                                    $error["Backtrace"] = $info["file"] . " at line " . $info["line"];	
                    }		
            }

            $msg = "";
            if($this->errorMsgFormat == "html") {
                    if(!empty($error["Bind Parameters"]))
                            $error["Bind Parameters"] = "<pre>" . $error["Bind Parameters"] . "</pre>";
                    $css = trim(file_get_contents(dirname(__FILE__) . "/error.css"));
                    $msg .= '<style type="text/css">' . "\n" . $css . "\n</style>";
                    $msg .= "\n" . '<div class="db-error">' . "\n\t<h3>SQL Error</h3>";
                    foreach($error as $key => $val)
                            $msg .= "\n\t<label>" . $key . ":</label>" . $val;
                    $msg .= "\n\t</div>\n</div>";
            }
            elseif($this->errorMsgFormat == "text") {
                    $msg .= "SQL Error\n" . str_repeat("-", 50);
                    foreach($error as $key => $val)
                            $msg .= "\n\n$key:\n$val";
            }

            $func = $this->errorCallbackFunction;
            $func($msg);
        }
    }

    public function delete($table, $where, $bind="") {
            $sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
            $this->run($sql, $bind);
    }

    private function filter($table, $info) {
            $driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
            if($driver == 'sqlite') {
                    $sql = "PRAGMA table_info('" . $table . "');";
                    $key = "name";
            }
            elseif($driver == 'mysql') {
                    $sql = "DESCRIBE " . $table . ";";
                    $key = "Field";
            }
            else {	
                    $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
                    $key = "column_name";
            }	

            if(false !== ($list = $this->run($sql))) {
                    $fields = array();
                    foreach($list as $record)
                            $fields[] = $record[$key];
                    return array_values(array_intersect($fields, array_keys($info)));
            }
            return array();
    }

    private function cleanup($bind) {
            if(!is_array($bind)) {
                if(!empty($bind)) $bind = array($bind);
                else $bind = array();
            }
            return $bind;
    }

    public function insert($table, $info) {
            $fields = $this->filter($table, $info);
            $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
            $bind = array();
            foreach($fields as $field)
                    $bind[":$field"] = $info[$field];
            return $this->run($sql, $bind);
    }

    public function run($sql, $bind="") {
            $this->sql = trim($sql);
            $this->bind = $this->cleanup($bind);
            $this->error = "";
            try {
                $pdostmt = $this->prepare($this->sql);
                if($pdostmt->execute($this->bind) !== false) {
                    if(preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->sql)){return $pdostmt->fetchAll(PDO::FETCH_ASSOC);}
                    elseif(preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $this->sql)){return $pdostmt->rowCount();}
                    else{
                        return $this->query($sql);
                    }
                }	
            }
            catch (PDOException $e) {
                    $this->error = $e->getMessage();	
                    $this->debug();
                    return false;
            }
    }

    public function select($table, $where="", $bind="", $fields="*") {
            $sql = "SELECT " . $fields . " FROM " . $table;
            if(!empty($where))
                    $sql .= " WHERE " . $where;
            $sql .= ";";
            return $this->run($sql, $bind);
    }

    public function setErrorCallbackFunction($errorCallbackFunction, $errorMsgFormat="html") {
            //Variable functions for won't work with language constructs such as echo and print, so these are replaced with print_r.
            if(in_array(strtolower($errorCallbackFunction), array("echo", "print")))
                    $errorCallbackFunction = "print_r";

            if(function_exists($errorCallbackFunction)) {
                    $this->errorCallbackFunction = $errorCallbackFunction;	
                    if(!in_array(strtolower($errorMsgFormat), array("html", "text")))
                            $errorMsgFormat = "html";
                    $this->errorMsgFormat = $errorMsgFormat;	
            }	
    }

    public function update($table, $info, $where, $bind="") {
            $fields = $this->filter($table, $info);
            $fieldSize = sizeof($fields);

             $sql = "UPDATE " . $table . " SET ";
            for($f = 0; $f < $fieldSize; ++$f) {
                    if($f > 0)
                            $sql .= ", ";
                            $sql .= $fields[$f] . " = :update_" . $fields[$f]; 
            }

            $sql .= " WHERE " . $where . ";";

            $bind = $this->cleanup($bind);
            foreach($fields as $field)
                    $bind[":update_$field"] = $info[$field];

            return $this->run($sql, $bind);
    }
    /* 
     * Backup the db OR just a table 
     * 
     */
    function backup_tables($database,$compression=false,$path){
        
        $table=array();
        //get all of the tables
        $this->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING );

        //Script Variables
        $BACKUP_PATH = $path;
        $nowtimename = $database?$database:  time();
        //create/open files
        if ($compression) {
            $zp = gzopen($BACKUP_PATH.$nowtimename.'.sql.gz', "w9");
        } else {
            $handle = fopen($BACKUP_PATH.$nowtimename.'.sql','a+');
        }
        //array of all database field types which just take numbers 
        $numtypes=array('tinyint','smallint','mediumint','int','bigint','float','double','decimal','real');

        //get all of the tables
        if(empty($tables)) {
            $pstm1 = $this->query('SHOW TABLES');
            while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
        } 
        else {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        //cycle through the table(s)

        foreach($tables as $table) {
            $result = $this->query('SELECT * FROM '.$table);
            $num_fields = $result->columnCount();
            $num_rows = $result->rowCount();
            $return="";
            //uncomment below if you want 'DROP TABLE IF EXISTS' displayed
            //$return.= 'DROP TABLE IF EXISTS `'.$table.'`;'; 
            //table structure
            $pstm2 = $this->query('SHOW CREATE TABLE '.$table);
            $row2 = $pstm2->fetch(PDO::FETCH_NUM);
            $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
            $return.= "\n\n".$ifnotexists.";\n\n";


            if ($compression) {
                gzwrite($zp, $return);
            }
            else {
                fwrite($handle,$return);
            }
            $return = "";
            //insert values
            if ($num_rows)
            {
                $return= 'INSERT INTO `'.$table."` (";
                $pstm3 = $this->query('SHOW COLUMNS FROM '.$table);
                $count = 0;
                $type = array();
                while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) 
                {
                    if (stripos($rows[1], '(')) {
                        $type[$table][] = stristr($rows[1], '(', true);
                    } 
                    else 
                        $type[$table][] = $rows[1];
                    $return.= $rows[0];
                    $count++;
                    if ($count < ($pstm3->rowCount())) {
                        $return.= ", ";
                    }
                }
                $return.= ")".' VALUES';
                if ($compression) {
                    gzwrite($zp, $return);
                }
                else {
                    fwrite($handle,$return);
                }
                $return = "";
            }

            while($row = $result->fetch(PDO::FETCH_NUM)) 
            {
                $return= "\n\t(";
                for($j=0; $j<$num_fields; $j++) 
                {
                    $row[$j] = addslashes($row[$j]);
                    //$row[$j] = preg_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) {
                        //if number, take away "". else leave as string
                        if (in_array($type[$table][$j], $numtypes))
                            $return.= $row[$j] ; else $return.= '"'.$row[$j].'"' ;
                    }
                    else {
                        $return.= '""';
                    }
                    if ($j<($num_fields-1)) {
                        $return.= ',';
                    }
                }
                $count++;
                if ($count < ($result->rowCount())) 
                {
                    $return.= "),";
                }
                else 
                {
                    $return.= ");";
                }
                if ($compression) 
                {
                    gzwrite($zp, $return);
                }
                else {
                    fwrite($handle,$return);
                }
                $return = "";
            }
            $return="\n\n-- ------------------------------------------------ \n\n";
            if ($compression)
            {
                gzwrite($zp, $return);
            }
            else
            {
                fwrite($handle,$return);
            }
            $return = "";
            }
                $error1= $pstm2->errorInfo();
                $error2= $pstm3->errorInfo();
                $error3= $result->errorInfo();
                echo $error1[2];
                echo $error2[2];
                echo $error3[2];
            if ($compression)
            {
                gzclose($zp);
            }
            else
            {
                fclose($handle);
            }
    }
}	
?>
