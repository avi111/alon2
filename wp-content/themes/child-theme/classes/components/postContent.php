<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 18/02/2018
 * Time: 22:18
 */

namespace components;


abstract class postContent extends component {
	protected $atts;
	protected $divided;
	public $get;

	abstract protected function getContent();

	abstract protected function getUnfiltered();

	public function __construct( $atts ) {
		$this->atts = $atts;

		global $post;
		$this->divided = new \post\divide( $post->ID );

		$this->get  = $this->getContent();
		$unfiltered = $this->getUnfiltered();

		$re = '/(\[fragment.*\])(.*)(\[\/fragment\])/';

		preg_match_all( $re, $unfiltered, $matches );

		$sum = array_sum( array_map( function ( $item ) {
			return count( $item );
		}, $matches ) );

		if ( ! is_single() ) {
			if ( $sum ) {
				foreach ( $matches[2] as $key => $value ) {
					$translation       = $value; // ( new \dictionary\word( $value ) )->getValue();
					$fragments[ $key ] = $matches[1][ $key ] . $translation . $matches[3][ $key ];
				}

				$not_fragments = explode( chr( 1 ), str_replace( $matches[0], chr( 1 ), $unfiltered ) );

				$not_fragments = array_map( function ( $element ) {
					$output = implode( explode( '\r\n', $element ) );
					$output = implode( explode( PHP_EOL, $output ) );

					return $output;
				}, $not_fragments );

				$not_fragments = array_filter( $not_fragments );

				foreach ( $not_fragments as $key => $value ) {
					$translated_not_fragments[ $key ] = ( new \dictionary\word( $value ) )->getValue();
				}


				if ( isset( $translated_not_fragments ) ) {
					$unfiltered = str_replace( $not_fragments, $translated_not_fragments, $unfiltered );
				}

				if ( isset( $fragments ) ) {
					$unfiltered = str_replace( $matches[0], $fragments, $unfiltered );
				}
				$this->get = apply_filters( 'the_content', $unfiltered );
			} else {
				$this->get = ( new \dictionary\word( $this->get ) )->getValue();
			}
		}
	}
}