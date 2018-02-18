<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 18/02/2018
 * Time: 22:43
 */

namespace dictionary;


class save {

	/**
	 * save constructor.
	 */
	public function __construct() {
		add_action('save_post',array($this,'save_post'), 10, 3);
	}

	public function save_post($id, $post, $bool ){
		// must refactor
	}
}