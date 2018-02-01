<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 30/01/2018
 * Time: 00:36
 */

namespace dictionary;


class language_handler {
	protected $id;
	protected $locale;
	protected $language;
	protected $short_name;
	protected $rtl;
	protected $spelling;
	static protected $cache = array();

	/**
	 * language_handler constructor.
	 */
	public function __construct( $input = false ) {
		$this->set_language( $input );
	}


	public function set_language( $input ) {
		if ( ! $input ) {
			$input = get_locale();
		}
		$this->id = $this->cached( $input );

		if ( ! $this->id ) {
			if ( is_numeric( $input ) ) {
				$this->set_id( $input );
			} else {
				$this->set_locale( $input );
			}
		}
	}

	protected function cached( $input ) {
		if ( is_numeric( $input ) ) {
			$cached = self::$cache[ $input ] ?? null;
		} else {
			$cached = array_filter( self::$cache, function ( $item ) use ( $input ) {
				return $input == $item['locale'];
			} );

			$cached = array_values( $cached );
			$cached = array_shift( $cached );
		}

		if ( $cached ) {
			return $cached['id'] ?? null;
		}

		return null;
	}

	protected function set_id( $input ) {
		$orm    = new \orm_dictionary_languages( $input );
		$record = $orm->reveal();
		if ( $record ) {
			$this->id         = $record['id'] ?? null;
			$this->language   = $record['language'] ?? null;
			$this->locale     = $record['locale'] ?? null;
			$this->short_name = $record['short_name'] ?? null;
			$this->rtl        = $record['rtl'] ?? null;
			$this->spelling   = $record['spelling'] ?? null;
		}

		self::$cache[ $this->id ] = $record;
	}

	protected function set_locale( $input ) {
		global $wpdb;
		$table  = \orm_dictionary_languages::getTable();
		$record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE `locale`=%s", $input ), ARRAY_A );
		if ( $record ) {
			$this->id         = $record['id'] ?? null;
			$this->language   = $record['language'] ?? null;
			$this->locale     = $record['locale'] ?? null;
			$this->short_name = $record['short_name'] ?? null;
			$this->rtl        = $record['rtl'] ?? null;
			$this->spelling   = $record['spelling'] ?? null;
		}

		self::$cache[ $this->id ] = $record;
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
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @return mixed
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * @return mixed
	 */
	public function getShortName() {
		return $this->short_name;
	}

	/**
	 * @return mixed
	 */
	public function getRtl() {
		return $this->rtl;
	}

	/**
	 * @return mixed
	 */
	public function getSpelling() {
		return $this->spelling;
	}
}