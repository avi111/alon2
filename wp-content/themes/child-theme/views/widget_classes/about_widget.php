<?php

class about_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "about", "about_component" );

function about_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "about", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\about($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}