<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 18/09/2017
 * Time: 00:09
 */

namespace widgets;


class edit_template {

	protected $id;
	protected $template;

	/**
	 * edit_template constructor.
	 */
	public function __construct( $id ) {
		$this->id       = $id;
		$this->template = new \widgets\template( $this->id );
	}

	public function show() {
		print_r($this);
	}
}