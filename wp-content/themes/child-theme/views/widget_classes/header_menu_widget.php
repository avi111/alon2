<?php

class header_menu_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "header_menu", "header_menu_component" );

function header_menu_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "header_menu", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\header_menu($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}