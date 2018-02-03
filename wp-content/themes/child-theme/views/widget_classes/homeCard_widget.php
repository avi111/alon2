<?php

class homeCard_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "homeCard", "homeCard_component" );

function homeCard_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "homeCard", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\homeCard($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}