<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class about extends component {

	protected $atts;
	protected $fragment;
	protected $content;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts     = $atts;
		$this->fragment = \fragments\post::get( 'about-homepage' );
	}

	/**
	 * @return static
	 */
	public function getFragment() {
		return $this->fragment;
	}

	/**
	 * @return mixed
	 */
	public function getContent() {
		if ( ! $this->content ) {
			$this->content = do_shortcode( sprintf( '[fragment]%s[/fragment]', $this->getFragment()->title() ) );
		}

		return $this->content;
	}


}