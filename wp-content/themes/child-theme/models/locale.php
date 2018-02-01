<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 01/02/2018
 * Time: 21:14
 */

add_filter( 'default_locale', function () {
	return 'he_IL';
} );

add_filter( 'language_attributes', function ( $output, $doctype ) {
	$language = new \dictionary\language_handler();
	if ( $language->getRtl() ) {
		$output .= ' dir="rtl"';
	}

	return $output;
}, 10, 2 );