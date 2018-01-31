<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 03/01/2018
 * Time: 23:58
 */

namespace minify;


class js extends minify {
	protected function setFile() {
		$this->file = 'script.js';
	}

	protected function add_loader_tag() {
		add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 3 );
	}

	public function script_loader_tag( $tag, $handle, $src ) {
		$path = $this->url_to_path( $src );
		$this->minifier->add( $path );

		return;
	}

	public function load() {
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
	}

	public function wp_footer() {
		echo sprintf( '<script type="text/javascript" src="%s"></script>', $this->url() );
	}
}