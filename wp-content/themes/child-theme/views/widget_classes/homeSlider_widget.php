<?php

class homeSlider_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "homeSlider", "homeSlider_component" );

function homeSlider_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "homeSlider", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\homeSlider($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}