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
			$this->post    = new post( $this->id );
			$this->content = $this->post->post_content;
		}

		return $this->post;
	}

	/**
	 * @return mixed
	 */
	public function getTop() {
		return $this->top;
	}

	/**
	 * @return mixed
	 */
	public function getBottom() {
		return $this->bottom;
	}

	protected function divide() {
		$exploded     = explode( self::delimiter, $this->content );
		$this->top    = array_shift( $exploded );
		$this->bottom = implode( '', $exploded );
	}

}