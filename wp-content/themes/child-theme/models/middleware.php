<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 29/01/2018
 * Time: 23:11
 */

add_filter( 'show_list_tables', function ( $array, $tables ) {
	global $wpdb;
	$show = array(
		$wpdb->prefix . 'home_sliders',
	);

	$tables = array_intersect( $show, $tables );

	return $tables;
}, 10, 2 );

add_filter( 'orm_edit_editor', function ( $return, $table, $field ) {
	global $wpdb;
	if ( $table == $wpdb->prefix . 'archive' ) {
		switch ( $field ) {
			case 'post_content':
			case 'post_excerpt':
				return true;
		}
	}

	return $return;
}, 10, 3 );

add_filter( 'edit_primary_key', function ( $original, $table ) {
	global $wpdb;

	if ( in_array( $table, array(
		$wpdb->prefix . 'home_sliders',
	) ) ) {
		return true;
	}

	return $original;
}, 10, 2 );

require_once 'locale.php';
require_dir( CHILD_ROOT . 'traits', 'traits/' );
require_dir( CHILD_ROOT . 'interfaces', 'interfaces/' );
new \fragments\setup();