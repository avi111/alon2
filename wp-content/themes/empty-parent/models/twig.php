<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 01/02/2018
 * Time: 22:52
 */


$util = \engines\twig\util::getInstance();

$util->filter( 'component', function ( $input ) {
	return get_component( $input );
} );

$util->func( 'component', function ( $input, $atts ) {
	return get_component( $input, $atts );
} );