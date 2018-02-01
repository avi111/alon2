<?php

class header_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "header", "header_component" );

function header_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "header", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\header($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}