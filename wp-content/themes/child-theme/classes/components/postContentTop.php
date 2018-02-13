<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class postContentTop extends component {

	protected $atts;
	public $get;
	public $title;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
		global $post;
		$divided = new \post\divide( $post->ID );

		$this->get = $divided->getTop();
		$this->title = $post->post_title;

		if(!is_single()){
			$this->get = (new \dictionary\word($this->get))->getValue();
			$this->title = (new \dictionary\word($this->title))->getValue();
		}
	}
}