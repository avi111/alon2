<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 30/01/2018
 * Time: 00:23
 */

namespace dictionary;

class word {
	protected static $cache;
	protected $key;
	protected $language;
	protected $value;
	protected $locale;
	protected $direct;
	protected $key_record;

	/**
	 * word constructor.
	 *
	 * @param $key
	 */
	public function __construct( $key, $direct = true ) {
		$this->direct = $direct;
		$this->key    = $key;
		if ( ! $direct ) {
			$this->bootstrap();
		}
	}

	public function cached() {
		$cached = self::$cache[ $this->key ] ?? null;

		return $cached;
	}

	protected function bootstrap() {
		$this->locale = get_locale();
		$this->setLanguage();
		$this->setValue();
	}

	/**
	 * @param mixed $language
	 */
	protected function setLanguage() {
		global $wpdb;
		$this->language = new language_handler( $this->locale );
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		$this->value = $this->cached();

		if ( ! $this->value ) {
			global $wpdb;
			if ( $this->direct ) {
				$prepared = $wpdb->prepare( "SELECT l.`locale`,v.`value` as lang_value, k.`value` as `english_value` FROM `{$wpdb->prefix}dictionary_values` v INNER JOIN `{$wpdb->prefix}dictionary_languages` l ON v.`language`=l.id INNER JOIN `{$wpdb->prefix}dictionary_keys` k ON v.`dictionary_key`=k.id WHERE k.`dictionary_key`=%s", $this->key );
				$results  = $wpdb->get_row( $prepared, ARRAY_A );
				if ( $results ) {
					if ( $results['locale'] == get_locale() ) {
						$this->value = $results['lang_value'];
					} else {
						$this->value = $results['english_value'];
					}
				}
			} else {
				$table       = \orm_dictionary_values::getTable();
				$lang        = $this->language->getId();
				$key         = $this->key_record->getId();
				$prepared    = $wpdb->prepare( "SELECT `value` FROM $table WHERE `language`=%d AND `dictionary_key`=%d", $lang, $key );
				$this->value = $wpdb->get_var( $prepared );
				if ( ! $this->value ) {
					$this->value = $this->key_record->getValue();
				}
			}

			if(!$this->value) {
				$this->value = $this->key;
			}

			self::$cache[ $this->key ] = $this->value;
		}

		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	protected function setValue() {
		$key              = new key_handler( $this->key );
		$this->key_record = $key;
	}


}