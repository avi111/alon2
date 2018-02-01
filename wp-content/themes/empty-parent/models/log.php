<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/09/2017
 * Time: 22:50
 */

function avraham_log( $msg, $filename ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG == true ) {
		$log = new \log\log();
		$log( $msg, $filename );
		unset( $log );
	}
}

function microtime_diff( $end = null ) {
	$start = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : false;
	if ( $start ) {
		$end      = microtime();
		$exploded = explode( " ", $start );

		if ( isset( $exploded[0] ) ) {
			$start_usec = $exploded[0];
		} else {
			$start_usec = 0;
		}

		if ( isset( $exploded[1] ) ) {
			$start_sec = $exploded[1];
		} else {
			$start_sec = 0;
		}

		list( $end_usec, $end_sec ) = explode( " ", $end );
		$diff_sec  = intval( $end_sec ) - intval( $start_sec );
		$diff_usec = floatval( $end_usec ) - floatval( $start_usec );

		return floatval( $diff_sec ) + $diff_usec;
	}

	return false;
}

register_shutdown_function( function () {
	if(!is_admin() && apply_filters('enable_loadtime_log','')) {
		avraham_log( sprintf( "total time: %ss", microtime_diff() ), 'timing' );
	}
} );