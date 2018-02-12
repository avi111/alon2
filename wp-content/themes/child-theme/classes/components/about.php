<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class about extends component {

	protected $atts;
	protected $fragment;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts     = $atts;
		$this->fragment = \fragments\post::get( $this->basename() );
	}

	/**
	 * @return static
	 */
	public function getFragment() {
		return $this->fragment;
	}
}