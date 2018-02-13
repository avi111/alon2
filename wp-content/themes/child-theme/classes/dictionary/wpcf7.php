<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 13/02/2018
 * Time: 21:38
 */

namespace dictionary;


class wpcf7 {

	/**
	 * wpcf7 constructor.
	 */
	public function __construct() {
		add_filter( 'wpcf7_contact_form', array( $this, 'filter_wpcf7_form_elements' ), 10, 1 );
	}

	public function filter_wpcf7_form_elements( $form ) {

		$prop = $form->get_properties();

		// messages translation
		if ( isset( $prop['messages'] ) && is_array( $prop['messages'] ) ) {
			foreach ( $prop['messages'] as $key => $value ) {
				$prop['messages'][ $key ] = ( new word( $value ) )->getValue();
			}
		}

		// placeholders tranbslation

		preg_match_all( '"([^\"]*(\\.[^\"]*)*)"', $prop['form'], $pat_array );

		$placeholders = array_filter( isset( $pat_array[0] ) ? $pat_array[0] : array(), function ( $pat ) {
			if ( strpos( $pat, '<label' ) !== false || strpos( $pat, 'label>' ) !== false ) {
				return false;
			}
			if ( ! $pat ) {
				return false;
			}

			if ( in_array( $pat, array( ']' ) ) ) {
				return false;
			}

			return true;
		} );

		foreach ( $placeholders as $key => $instance ) {
			$word         = ( new word( $instance ) )->getValue();
			$word         = apply_filters( 'placeholder_' . strtolower( $instance ), $word );
			$prop['form'] = str_replace( $instance, $word, $prop['form'] );
		}

		// checkboxes translation
		$instances = $this->GetBetween( $prop['form'], 'include_blank "', '"]' );

		$options = explode( "\"", $instances );
		$options = array_filter( $options, function ( $element ) {
			return $element;
		} );

		foreach ( $options as $key => $value ) {
			$options[ $key ] = ( new word( $value ) )->getValue();
		}
		$prop['form'] = str_replace( $instances, implode( "\"", $options ), $prop['form'] );

		$instances = $this->getInbetweenStrings( 'use_label_element "', '"', $prop['form'] );
		foreach ( $instances as $key => $instance ) {
			$prop['form'] = str_replace( $instance, ( new word( $instance ) )->getValue(), $prop['form'] );
		}

		$form->set_properties( $prop );

		return $form;
	}

	protected function getInbetweenStrings( $start, $end, $str ) {
		$matches = array();
		$regex   = "/$start(.*)$end/i";
		preg_match_all( $regex, $str, $matches );

		return $matches[1];
	}

	protected function GetBetween( $content, $start, $end ) {
		$r = explode( $start, $content );
		if ( isset( $r[1] ) ) {
			$r = explode( $end, $r[1] );

			return $r[0];
		}

		return '';
	}

}