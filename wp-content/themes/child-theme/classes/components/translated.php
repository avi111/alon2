<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 31/01/2018
 * Time: 22:44
 */

namespace components;


class translated extends component implements \IKeys, \IOptions {

	use \translate, \options;
	protected $atts;

	public function __construct( $atts ) {
		$this->atts = $atts;
	}

	public function translations_keys() {
		return array();
	}

	public function options_keys() {
		return array();
	}
}