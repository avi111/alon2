<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 31/01/2018
 * Time: 22:39
 */

trait options {
	public function options() {
		foreach ( $this->options_keys() as $option ) {
			$options[ str_replace( '-', '_', sanitize_title( $option ) ) ] = of_get_option( $option );
		}

		return $options;
	}
}