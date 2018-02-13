<?php

class postContentTop_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "postContentTop", "postContentTop_component" );

function postContentTop_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "postContentTop", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\postContentTop($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}