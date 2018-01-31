<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 03/01/2018
 * Time: 23:47
 */

namespace minify;


class css extends minify {
	protected function setFile() {
		$this->file = 'style.css';
	}

	protected function add_loader_tag() {
		add_filter( 'style_loader_tag', array( $this, 'loader_tag' ), 10, 1 );
	}

	public function load() {
		add_action( 'wp_head', array( $this, 'wp_head' ) );
	}

	public function wp_head(){
		echo sprintf( '<link rel="stylesheet" href="%s">', $this->url() );
	}

	protected function regex() {
		$re = '/^.*?href=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?.*?$/i';
		return $re;
	}

	public function loader_tag( $input ) {
		preg_match_all( $this->regex(), $input, $matches, PREG_SET_ORDER, 0 );

		if ( count( $matches ) ) {
			$url  = $matches[0][1];
			$path=$this->url_to_path($url);
			$this->minifier->add( $path );

			return false;
		}

		return $input;
	}


}