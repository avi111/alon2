<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class homeCard extends component {

	protected $atts;
	/**
	 * widget_class constructor.
	 */
	public function __construct($atts) {
		$this->atts=$atts;
	}
}