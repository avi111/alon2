<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26/08/2017
 * Time: 23:06
 */

function getView( $path, $viewbag = array(), $class = null ) {
	ob_start();
	include str_replace( '\\', '/', get_stylesheet_directory() . VIEWS . "$path.php" );

	return ob_end_flush();
}

function enqueue_avraham_style( $path, $deps = false, $prefix = '' ) {
	if ( is_admin() ) {
		return;
	}
	if ( ! $prefix ) {
		$prefix = sanitize_title( $path );
	}
	if ( locate_template( STYLES . "$path.css" ) ) {
		wp_register_style(
			$prefix . '_css',
			get_stylesheet_directory_uri() . STYLES . "$path.css",
			$deps );
		wp_enqueue_style( $prefix . '_css' );
	}
}

function enqueue_avraham_script( $path, $deps = array(), $prefix = '' ) {
	if ( is_admin() ) {
		return;
	}
	if ( ! $prefix ) {
		$prefix = sanitize_title( $path );
	}

	if ( locate_template( SCRIPTS . "$path.js" ) ) {
		wp_enqueue_script(
			$prefix . '_js',
			get_stylesheet_directory_uri() . SCRIPTS . "$path.js",
			$deps );
	}
}

function get_component( $widget, $atts = array() ) {
	$output = apply_filters( "avraham_cache", $widget, $atts );
	if ( ! $output ) {
		$class  = "\components\\$widget";
		$widget = new $class( $atts );
		$widget->show();
	} else {
		echo $output;
	}
}