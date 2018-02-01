<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/10/2017
 * Time: 00:15
 */

namespace options;


class options {
	public function __construct() {
		require dirname(__FILE__).'/options-framework.php';
	}

	static public function init() {
		new static();
	}
}