<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06/10/2017
 * Time: 19:37
 */

namespace engines\haml;


class singleton {
	private static $instance = null;

	public static function getInstance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	private function __construct() {
		require_once get_template_directory() . '/classes/engines/haml/HamlPHP.php';
		require_once get_template_directory() . '/classes/engines/haml/Storage/FileStorage.php';
	}
}