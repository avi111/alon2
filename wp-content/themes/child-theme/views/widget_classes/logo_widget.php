<?php

class logo_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "logo", "logo_component" );

function logo_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "logo", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\logo($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}