<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class homeSlider extends component {

	protected $atts;
	protected $slides;
	protected $images;

	/**
	 * widget_class constructor.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
		$this->set_slides();
	}

	protected function set_slides() {
		$this->images = $this->get_images();
		$this->slides = array_map( array( $this, 'make_image' ), $this->images );
	}

	protected function make_image( $slide ) {
		$img = wp_make_content_images_responsive( wp_get_attachment_image( $slide->ID, 'full' ) );

		return $img;
	}

	public function get_slides() {
		return $this->slides;
	}

	public function get_images() {
		if ( ! $this->images ) {
			$this->images = array();

			global $wpdb;
			$this->images = $wpdb->get_results( "SELECT p.ID, p.post_title,p.post_excerpt FROM `{$wpdb->prefix}home_slider` s INNER JOIN $wpdb->posts p ON s.id=p.ID ORDER BY priority" );
		}

		return $this->images;
	}
}