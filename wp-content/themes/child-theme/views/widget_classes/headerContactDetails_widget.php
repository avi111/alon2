<?php

class headerContactDetails_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "headerContactDetails", "headerContactDetails_component" );

function headerContactDetails_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "headerContactDetails", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\headerContactDetails($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}