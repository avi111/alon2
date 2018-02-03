<?php

class headerMenu_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "headerMenu", "headerMenu_component" );

function headerMenu_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "headerMenu", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\headerMenu($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}