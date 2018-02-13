<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 30/01/2018
 * Time: 00:36
 */

namespace dictionary;

use \wpdb\wpdb as wpdb;

class key_handler {
	protected $id;
	protected $key;
	protected $value;
	protected $unsanitized;

	/**
	 * key_handler constructor.
	 *
	 * @param $key
	 */
	public function __construct( $key,$unsanitized=false ) {
		$this->key = $key;
		$this->unsanitized=$unsanitized;

		global $wpdb;
		$table = \orm_dictionary_keys::getTable();

		$db=wpdb::get();
		$record = $db->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE dictionary_key=%s", $key ), ARRAY_A );
		if ( $record ) {
			$this->id = $record['id'] ?? null;
			$this->key - $record['dictionary_key'] ?? null;
			$this->value = $record['value'] ?? null;
		}

		if ( ! $this->id && $this->key) {
			$insert = $wpdb->insert( $table,
				array(
					'dictionary_key' => $this->key,
					'value'          => $this->unsanitized
				),
				array(
					'%s',
					'%s'
				)
			);
		}
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}
}