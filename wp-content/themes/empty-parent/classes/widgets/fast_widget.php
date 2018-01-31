<?php

class classname_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "classname", "classname_component" );

function classname_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "classname", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\classname($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}