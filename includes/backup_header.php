<!DOCTYPE html>
<!--[if lt IE 7]>
    <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
    <html class="no-js lt-ie9"> <![endif]-->
    <!--[if gt IE 8]><!-->

    <html <?php language_attributes(); ?>><!--<![endif]-->
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="<?php bloginfo('charset'); ?>" />
        <?php $wl_theme_options = enigma_parallax_get_options(); ?>
        <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="screen" />
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>

        <!-- Start Mobile Menu -->
        <div class="menu-resp-mobile-menu-container">
            <?php wp_nav_menu( array(
              'theme_location' => 'primary',
              'menu_class' => 'resp-mobile-menu',
              )
              );    ?>
              <!--div class="search-box">
                <?php get_search_form(); ?>
            </div-->
        </div>
        <!-- End Mobile Menu -->


        <div>
            <!-- Header Section -->
            <div class="top_fix">
                <div class="header_section affix-top transition">
                    <div id="header">
                        <!-- Logo & Contact Info -->
                        <div class="row ">
                            <div class="col-md-3 col-sm-3 col-xs-12 wl_rtl" >
                                <span class="menu-icon x" id="icon">
                                    <span class="navicon"></span>
                                </span>
                                <div claSS="logo">
                                    <a href="<?php echo esc_url(home_url( '/' )); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                                        <?php if($wl_theme_options['upload_image_logo']){ ?>
                                        <img class="img-responsive" src="<?php echo esc_url($wl_theme_options['upload_image_logo']); ?>" style="height:<?php if($wl_theme_options['height']!='') { echo $wl_theme_options['height']; }  else { "80"; } ?>px; width:<?php if($wl_theme_options['width']!='') { echo $wl_theme_options['width']; }  else { "200"; } ?>px;" />
                                        <?php } else {
                                            echo get_bloginfo('name');
                                        } ?>
                                    </a>
                                    <p><?php bloginfo( 'description' ); ?></p>
                                </div>
                            </div>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <!-- Navigation  menus -->
                                <div class="navigation_menu transition"  data-spy="affix" data-offset-top="95" id="enigma_nav_top">
                                    <div class="navbar-container" >
                                        <nav class="navbar navbar-default " role="navigation">
                                            <div id="menu" class="collapse navbar-collapse ">
                                                <?php
                                                $defaults = array(
															'theme_location'  => 'primary',
															'menu'            => '',
															//'container'       => 'div',
															//'container_class' => 'menu-{menu slug}-container',
															//'container_id'    => ,
															'menu_class'      => 'nav navbar-nav',
															//'menu_id'         => ,
															'echo'            => true,
															//'fallback_cb'     => 'wp_page_menu',
															'before'          => '',
															'after'           => '',
															'link_before'     => '',
															'link_after'      => '',
															'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
															'depth'           => 0,
															'walker'          =>new enigma_parallax_nav_walker());
															wp_nav_menu( $defaults );
                                                 /*wp_nav_menu( array(
<<<<<<< .mine
                                                    'theme_location' => 'primary',
                                                    'menu_class' => 'nav navbar-nav',
                                                    'fallback_cb' => 'enigma_parallax_fallback_page_menu',
                                                    'walker' => new enigma_parallax_nav_walker(),
                                                    )
                                                    );*/    ?>   
                                                    <div class="search-box">
                                                        <?php get_search_form(); ?>
                                                    </div>           
                                                </div>    
                                            </nav>
                                        </div>
                                    </div>                                
=======
                                                  'theme_location' => 'primary',
                                                  'menu_class' => 'nav navbar-nav',
                                                    //'fallback_cb' => 'enigma_parallax_fallback_page_menu',
                                                    //'walker' => new enigma_parallax_nav_walker(),
                                                  )
                                                  );    ?>
                                                  <div class="search-box">
                                                    <?php get_search_form(); ?>
                                                </div>
                                            </div>
                                        </nav>
                                    </div>
>>>>>>> .r154
                                </div>
                            </div>
                        </div>
                        <!-- /Logo & Contact Info -->
                    </div>
                </div>
                <!-- /Header Section -->
            </div>

