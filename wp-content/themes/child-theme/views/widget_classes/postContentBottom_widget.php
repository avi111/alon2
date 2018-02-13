<?php

class postContentBottom_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

}

add_shortcode( "postContentBottom", "postContentBottom_component" );

function postContentBottom_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "postContentBottom", $atts );
	if ( ! $output ) {
		ob_start();
		$widget=new \components\postContentBottom($atts);
		$widget->show();
		$output=ob_get_clean();
	}

	return $output;
}