<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 15/02/2018
 * Time: 23:05
 */

namespace fragments;

class shortcode {

	/**
	 * shortcode constructor.
	 */
	public function __construct() {
		add_shortcode( 'fragment', array( $this, 'shortcode' ) );
	}

	public function shortcode( $atts, $content ) {
		$atts = shortcode_atts( array(
			'id' => ''
		), $atts, 'fragment' );
		$id   = $atts['id'] ? sprintf( ' id="%s"', $atts['id'] ) : '';

		$content = str_replace( '”', '"', esc_sql( html_entity_decode( $content ) ) );

		global $wpdb;
		$db    = \wpdb\wpdb::get();
		$query = $wpdb->prepare( "SELECT post_name FROM $wpdb->posts WHERE post_title = %s AND post_type= 'fragment' AND post_status='publish'", $content );
		$slug  = $db->get_var( $query );

		$translation = ( new \dictionary\word( $slug ) )->getValue();

		return sprintf( '<div%s class="fragment">%s</div>', $id, $translation );

	}
}