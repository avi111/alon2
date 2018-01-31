<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/10/2017
 * Time: 19:17
 */

namespace engines;


abstract class engine {
	abstract static public function init();
	abstract static public function of_option($options);
	abstract static public function execute($class,$viewbag);
}