<?php

class contactForm_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "contactForm", "contactForm_component" );

function contactForm_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "contactForm", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\contactForm($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}