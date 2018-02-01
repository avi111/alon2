<?php

class header_contact_details_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "header_contact_details", "header_contact_details_component" );

function header_contact_details_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "header_contact_details", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\header_contact_details($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}