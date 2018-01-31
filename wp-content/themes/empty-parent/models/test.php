<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 28/11/2017
 * Time: 21:17
 */

trait testible {
	public function test( $method, $args = false ) {
		if ( phpunit() && in_array( $method, $class_methods = get_class_methods( static::class ) ) ) {
			if ( $args ) {
				$return = $this->$method( $args );
			} else {
				$return = $this->$method();
			}
		} else {
			$return = null;
		}

		return $return;
	}
}