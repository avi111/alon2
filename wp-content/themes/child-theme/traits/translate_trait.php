<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 31/01/2018
 * Time: 22:05
 */

use \dictionary\word as word;

trait translate {
	public function translate( $key ) {
		$word = new word( $key,false );

		return $word->getValue();
	}

	public function translations() {
		foreach ( $this->translations_keys() as $translation ) {
			$translations[ word::make_key($translation) ] = $this->translate( word::make_key($translation) );
		}

		return $translations;
	}
}