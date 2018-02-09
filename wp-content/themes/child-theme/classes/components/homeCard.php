<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


use post\post;

class homeCard extends component {

	protected $atts;
	protected $title;
	protected $page;
	protected $content;
	protected $image;
	protected $url;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
	}

	/**
	 * @return null
	 */
	public function getTitle() {
		if ( ! $this->title ) {
			$this->title = $this->atts['title'] ?? null;
		}

		return ucfirst( $this->title );
	}

	/**
	 * @return mixed
	 */
	public function getPage() {
		if ( ! $this->page ) {
			$page = $this->atts['page'] ?? null;
			if ( $page ) {
				$this->page = new post( $page );
			}
		}

		return $this->page;
	}

	/**
	 * @return null
	 */
	public function getContent() {
		if ( ! $this->content ) {
			$content       = $this->atts['content'] ?? null;
			$this->content = apply_filters( 'the_content', $content );
		}

		return $this->content;
	}

	/**
	 * @return mixed
	 */
	public function getImage( $size = 'thumbnail' ) {
		if ( ! $this->image ) {
			$page = $this->getPage();
			if ( $page ) {
				$this->image = $page->getIMage( $size );
			}
		}

		return $this->image;
	}

	/**
	 * @return mixed
	 */
	public function getUrl() {
		if ( ! $this->url ) {
			$page = $this->getPage();
			if ( $page ) {
				$this->url = $page->permalink();
			}
		}

		return $this->url;
	}


}