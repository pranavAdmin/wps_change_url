<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_error', 0);
error_reporting(0);

require_once 'form.php';
/*require_once './includes/class.db.php';

$oldSiteName="http://10.2.2.53/wordpress";
$newSiteName="http://localhost/wordpress";

$obj=new db("mysql:host=localhost;dbname=wordpress", "root","");
$obj->backup_tables("bak_wordpress");

echo "No of Rows affected in the table wp_options :<b>".$obj->run("UPDATE wp_options SET option_value = replace(option_value, '".$oldSiteName."', '".$newSiteName."') WHERE option_name = 'home' OR option_name = 'siteurl'")."</b>";
echo "<br>No of Rows affected in the table wp_posts :<b>".$obj->run("UPDATE wp_posts SET post_content = replace(post_content, '".$oldSiteName."', '".$newSiteName."')")."</b>";
echo "<br>No of Rows affected in the table wp_posts ::<b>".$obj->run("UPDATE wp_posts SET guid = replace(guid, '".$oldSiteName."', '".$newSiteName."')")."</b>";
echo "<br>No of Rows affected  in the table wp_postmeta ::<b>".$obj->run("UPDATE wp_postmeta SET meta_value = replace(meta_value, '".$oldSiteName."', '".$newSiteName."')")."</b>";
*/