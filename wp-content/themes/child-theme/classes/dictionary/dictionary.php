<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 11/02/2018
 * Time: 20:26
 */

namespace dictionary;


class dictionary {

	protected $keys;
	protected $languages;
	protected $values;
	protected $table;

	/**
	 * dictionary constructor.
	 */
	public function __construct() {
		$this->keys      = \orm_dictionary_keys::get_latest();
		$this->languages = \orm_dictionary_languages::get_latest();
		$this->values    = \orm_dictionary_values::get_latest();

		foreach ( $this->keys as $key ) {
			$key_id = $key->id;
			$value  = $key->value;
			if ( ! $value ) {
				$value = ucfirst( $key->dictionary_key );
			}

			$this->table[ $key->dictionary_key ] = array( '0' => $value );

			foreach ( $this->languages as $language ) {
				$lang_id = $language->id;

				$value = array_filter( $this->values, function ( $value ) use ( $lang_id, $key_id ) {
					return $value->language == $lang_id && $value->dictionary_key == $key_id;
				} );

				$value                                           = array_shift( $value );
				$value                                           = is_object( $value ) ? $value->value : null;
				$this->table[ $key->dictionary_key ][ $lang_id ] = $value;
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * @return array|null|object
	 */
	public function getLanguages() {
		return $this->languages;
	}
}