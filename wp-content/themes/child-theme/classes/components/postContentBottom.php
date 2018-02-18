<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class postContentBottom extends postContent {
	protected function getContent() {
		return $this->divided->getBottom();
	}

	protected function getUnfiltered() {
		return $this->divided->getBottom(false);
	}
}