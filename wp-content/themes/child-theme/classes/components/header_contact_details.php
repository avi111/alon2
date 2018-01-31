<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class header_contact_details extends translated {

	use \id;
	protected $atts;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
	}

	public function translations_keys() {
		return array(
			'address',
			'make an appointment',
			'phone at clinic'
		);
	}

	public function options_keys() {
		return array(
			'phone',
			'appointments_phone'
		);
	}
}