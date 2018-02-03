<?php

class languageSelector_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "languageSelector", "languageSelector_component" );

function languageSelector_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "languageSelector", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\languageSelector($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}