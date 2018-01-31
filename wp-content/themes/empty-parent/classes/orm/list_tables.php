<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/09/2017
 * Time: 23:32
 */

namespace orm;


class list_tables {
	public function __construct() {
		$tables = \get_orm_map()->getTables();
		$tables = apply_filters( 'show_list_tables', array(), $tables );
		foreach ( $tables as $table ) {
			global $wpdb;
			$prefix = $wpdb->prefix;
			$vals   = array_keys( get_object_vars( $wpdb ) );
			if (
				! in_array( str_replace( $prefix, '', $table ), $vals )
				&& ! in_array( str_replace( $prefix, '', $table ), array(
					'templates',
					'widgets_box',
					'widgets'
				) )
			) {
				new \orm\single_list_table( $table );
			}
		}
	}
}