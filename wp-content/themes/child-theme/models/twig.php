<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 12/02/2018
 * Time: 21:44
 */

$util = \engines\twig\util::getInstance();

$util->filter( 'translate', function ( $input ) {
	$word = new \dictionary\word( $input );

	return $word->getValue();
} );

$util->filter( 'content', function ( $input ) {
	return apply_filters( 'the_content', $input );
} );

require_once get_template_directory() . '/models/twig.php';