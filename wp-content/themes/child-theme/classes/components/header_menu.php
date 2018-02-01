<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;

use \dictionary\word as word;

class header_menu extends translated {

	use \id;
	protected $atts;
	protected $menu;
	protected $array;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
		$this->setMenu();
	}

	protected function setMenu() {
		$this->menu = \util\util::getMenu( 'header' );

		$array = array();
		foreach ( $this->menu as $item ) {
			$title         = strtolower( $item->title );
			$word          = new word( $title );
			$key           = $word->getValue();
			$array[ $key ] = $item->url;
		}

		$this->array = $array;
	}

	public function get_menu() {
		return $this->array;
	}
}