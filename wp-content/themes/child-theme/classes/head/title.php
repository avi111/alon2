<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 01/02/2018
 * Time: 23:53
 */

namespace head;

use \dictionary\word as word;

class title {
	protected $page_title;
	protected $site_name;
	public $html_title;

	/**
	 * title constructor.
	 */
	public function __construct() {
		global $post;
		$title = $post->post_title;
		if ( is_front_page() ) {
			$title = 'home';
		}
		$this->page_title = ( new word( $title ) )->getValue();
		$this->site_name  = ( new word( get_bloginfo() ) )->getValue();
		$this->set_html_title();
	}

	protected function set_html_title() {
		//$html_title = wp_get_document_title();
		$html_title = sprintf( '%s - %s', $this->page_title, $this->site_name );

		$html_title = implode( ' ', array_map( function ( $item ) {
			return ucfirst( $item );
		}, explode( ' ', $html_title ) ) );

		$this->html_title = $html_title;
	}
}