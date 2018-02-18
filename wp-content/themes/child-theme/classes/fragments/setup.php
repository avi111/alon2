<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 12/02/2018
 * Time: 21:54
 */

namespace fragments;


class setup {

	/**
	 * setup constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'custom_post_type' ), 0 );
		new shortcode();
	}

	// Register Custom Post Type
	public function custom_post_type() {
		$args = array(
			'label'              => __( 'Fragments', 'avraham' ),
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_position'      => 5,
			'show_in_admin_bar'  => true,
			'can_export'         => true,
			'has_archive'        => true,
			'publicly_queryable' => false,
		);

		register_post_type( 'fragment', $args );

	}
}