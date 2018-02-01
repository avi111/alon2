<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/08/2017
 * Time: 21:34
 */

namespace orm;

class map {

	protected $tables;
	protected $data;
	private static $instance;

	/**
	 * map constructor.
	 */
	private function __construct() {
		$this->refresh();
	}

	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @return mixed
	 */
	public function getTables() {
		return $this->tables;
	}

	public function refresh(){
		global $wpdb;
		foreach ( $wpdb->get_col( "SHOW TABLES" ) as $table ) {
			$tables[ $table ] = $wpdb->get_results( "DESCRIBE $table" );
		}
		$this->data = $tables;
		$this->tables=array_keys($this->data);
	}

}