<?php

class language_selector_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "language_selector", "language_selector_component" );

function language_selector_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "language_selector", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\language_selector($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}