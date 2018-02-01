<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 28/11/2017
 * Time: 21:00
 */

function phpunit(){
	$phpunit=defined('PHPUNIT') && PHPUNIT;
	return $phpunit;
}