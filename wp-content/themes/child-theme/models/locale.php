<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 01/02/2018
 * Time: 21:14
 */

add_filter( 'default_locale', function () {
	if ( ! is_admin() ) {
		return 'he_IL';
	} else {
		return 'en_US';
	}

} );

add_filter( 'language_attributes', function ( $output, $doctype ) {
	$language = new \dictionary\language_handler();
	if ( $language->getRtl() ) {
		$output .= ' dir="rtl"';
	}

	return $output;
}, 10, 2 );

add_filter('placeholder_name',function($word){
	if(!is_admin()) {
		return $word . '*';
	} else {
		return $word;
	}
});

add_filter('placeholder_phone',function($word){
	if(!is_admin()) {
		return $word . '*';
	} else {
		return $word;
	}
});

new \dictionary\admin();
new \dictionary\wpcf7();