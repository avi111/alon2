<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/09/2017
 * Time: 23:00
 */

interface crud{
	static public function create($array);
	static public function read($id);
	static public function update($id,$array);
	static public function delete($id);
}