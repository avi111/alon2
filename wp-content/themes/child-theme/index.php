<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26/08/2017
 * Time: 21:45
 */

get_header();

if ( is_home() || is_front_page() ) {
	dynamic_sidebar( 'homepage' );
}

if(is_single() || is_page()){
	dynamic_sidebar( 'single' );
}

get_footer();