<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 13/02/2018
 * Time: 22:45
 */

namespace post;


class divide {
	protected $id;
	protected $post;
	protected $top;
	protected $bottom;
	protected $content;
	protected $title;

	const delimiter = '<!--more-->';

	/**
	 * divide constructor.
	 *
	 * @param $id
	 */
	public function __construct( $id ) {
		$this->id = $id;
		$this->getPost();
		$this->divide();
	}

	/**
	 * @return mixed
	 */
	protected function getPost() {
		if ( ! $this->post ) {
			$this->post    = new post( $this->id, false );
			$this->content = $this->post->post_content;
		}

		return $this->post;
	}

	/**
	 * @return mixed
	 */
	public function getTop( $filtered = true ) {
		if ( $filtered ) {
			return apply_filters( 'the_content', $this->top );
		} else {
			return $this->top;
		}
	}

	/**
	 * @return mixed
	 */
	public function getBottom( $filtered = true ) {
		if ( $filtered ) {
			return apply_filters( 'the_content', $this->bottom );
		} else {
			return $this->bottom;
		}
	}

	protected function divide() {
		$exploded     = explode( self::delimiter, $this->content );
		$this->top    = array_shift( $exploded );
		$this->bottom = implode( '', $exploded );
	}

	/**
	 * @return mixed
	 */
	public function getTitle() {
		if ( ! $this->title ) {
			$this->title = $this->getPost()->post_title;
		}

		return $this->title;
	}

}