<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 31/01/2018
 * Time: 10:43
 */

namespace dictionary;

use \wpdb\wpdb as wpdb;

class language_selector {
	protected static $instance;
	protected $languages;

	/**
	 * language_selector constructor.
	 */

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	protected function __construct() {
		$this->languages = \orm_dictionary_languages::get_latest();
		$this->languages = array_map( function ( $language ) {
			return (array) $language;
		}, $this->languages );

		$this->languages[] = array(
			'id'         => null,
			'language'   => 'english',
			'locale'     => 'en_US',
			'short_name' => 'en',
			'spelling' => 'English'
		);

		$this->languages = apply_filters( 'sort_languages', $this->languages );
	}

	public function get_languages( $without_current_locale = false ) {
		$all = $this->languages;
		if ( $without_current_locale ) {
			$all = array_filter( $all, function ( $language ) {
				return $language['locale'] != get_locale();
			} );
		}

		return $all;
	}

	public function get_language_names( $without_current = false ) {
		$languages = $this->get_languages( $without_current );

		return array_column( $languages, 'language' );
	}

	protected function get_language_by_locale( $locale ) {
		$this->languages[ array_search( $locale, array_column( $this->languages, 'locale' ) ) ];
	}

	public function get_url( $url, $locale ) {
		$url = rtrim( $url, '/' );

		$sign = parse_url( $url, PHP_URL_QUERY ) ? '&' : '/?';

		if ( $locale !== apply_filters( 'default_locale', get_locale() ) ) {
			$key    = apply_filters( 'set_locale_filter', 'lang' );
			$suffix = $sign . $key . '=' . $locale;
		} else {
			$suffix = '';
		}

		$return = $url . $suffix;

		return $return;
	}
}