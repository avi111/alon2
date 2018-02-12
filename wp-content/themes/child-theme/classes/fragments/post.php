<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 12/02/2018
 * Time: 22:08
 */

namespace fragments;


class post extends \post\post {
	static public function get( $slug ) {
		$post = \util\util::get_page_by_slug( $slug, OBJECT, 'fragment' );

		return new static( $post->ID );
	}
}