<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;

class postContentTop extends postContent {
	public $title;

	public function __construct( $atts ) {
		parent::__construct( $atts );
		$this->title = ( new \dictionary\word( $this->divided->getTitle() ) )->getValue();
	}


	protected function getContent() {
		return $this->divided->getTop();
	}

	protected function getUnfiltered() {
		return $this->divided->getTop( false );
	}


}