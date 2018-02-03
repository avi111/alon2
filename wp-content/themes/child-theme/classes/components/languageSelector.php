<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;

use \dictionary\language_selector as langsel;


class languageSelector extends component {

	protected $atts;
	protected $languages;
	protected $array;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;

		$language_selector = langsel::get_instance();
		$this->languages   = $language_selector->get_languages( true );
	}

	public function get_array() {
		if ( ! $this->array ) {
			$about = home_url( 'about' );

			$array = array();
			$language_selector = langsel::get_instance();

			foreach ( $this->languages as $language ) {
				$array[ ucfirst( $language['spelling'] ) ] = $language_selector->get_url( $about, $language['locale'] );
			}

			$this->array = $array;
		}

		return $this->array;
	}
}