<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 31/01/2018
 * Time: 10:09
 */

$logo = of_get_option( 'site_logo' );
if ( $logo ) {
	echo wp_make_content_images_responsive( sprintf( '<img class="logo" src="%s" alt="Logo">', $logo ) );
}