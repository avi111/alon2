<?php

namespace post;

class post {

	//put your code here
	public $customs;
	public $image;
	public $image_id;
	public $responsive_image;
	public $ID;
	public $post_type;
	public $post_name;
	public $post_title;
	public $post_content;
	public $post_excerpt;
	public $post_date;
	public $post_author;
	public $author;
	public $post_status;
	public $terms;

	public function __construct( $id, $sanitized = true ) {
		$post              = get_post( $id );
		$this->ID          = $post->ID;
		$this->post_type   = $post->post_type;
		$this->post_title  = $post->post_title;
		$this->post_name   = $post->post_name;
		$this->post_author = $post->post_author;
		$this->author      = new \WP_User( $this->post_author );
		$this->author_name = $this->author->display_name;
		$this->author_url  = get_author_posts_url( $this->post_author );

		$this->post_content = $post->post_content;
		if ( $sanitized ) {
			$this->post_content = $this->content();
		}
		$this->post_excerpt = $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post->post_content );
		$this->post_date    = $post->post_date;
		$this->post_status  = $post->post_status;
		$this->customs      = get_post_custom( $this->ID );
		$this->image_id     = get_post_thumbnail_id( $id );
		$this->image        = wp_get_attachment_image_src( $this->image_id, 'full' );
		if ( is_array( $this->image ) && $this->image_id ) {
			$this->responsive_image = wp_make_content_images_responsive( sprintf( '<img class="wp-image-%d" src="%s"/>', $this->image_id, $this->image[0] ) );
		}
		$this->terms = array();
		foreach ( get_object_taxonomies( $this->post_type ) as $taxonomy ) {
			$this->terms[ $taxonomy ] = wp_get_post_terms( $this->ID, $taxonomy );
		}
	}

	public static function create( $id ) {
		$instance = new self( $id );

		return $instance;
	}

	public function getSingleMeta( $key ) {
		if ( isset( $this->customs[ $key ] ) ) {
			$array = $this->customs[ $key ];

			return array_pop( $array );
		} else {
			return false;
		}
	}

	public function title() {
		return $this->post_title;
	}

	public function ID() {
		return $this->ID;
	}

	public function permalink() {
		return get_permalink( $this->ID );
	}

	public function excerpt() {
		return $this->post_excerpt;
	}

	public function content() {
		$content = apply_filters( 'the_content', $this->post_content );
		if ( ! $content ) {
			$content = do_shortcode( shortcode_unautop( wpautop( wptexturize( $this->post_content ) ) ) );
		}

		return $content;
	}

	public function getResponsiveImage( $max = false ) {
		$image = $this->responsive_image;
		if ( is_array( $this->image ) && $max && is_numeric( $max ) ) {
			$image = str_replace( $this->image[1] . 'px', $max . 'px', $this->responsive_image );
		}

		return $image;
	}

	public function showImage( $link = '', $title = '', $alt = '', $width = 0, $height = 0 ) {
		\util\util::showImage( $this->image[0] );
	}

	public function getIMage( $size = false ) {
		$image=false;

		if ( $size ) {
			$array = wp_get_attachment_image_src( $this->image_id, $size );
			$image = $array[0] ?? null;
		}

		if ( ! $image ) {
			$image = $this->image[0];
		}

		return $image;
	}


	public function terms( $taxonomy, $first = false ) {
		$terms = wp_get_post_terms( $this->ID, $taxonomy );
		if ( ! is_wp_error( $terms ) ) {
			if ( count( $terms ) && $first ) {
				return $terms[0];
			} else {
				return $terms;
			}
		} else {
			return false;
		}
	}

	public function get_post_author() {
		return new \WP_User( $this->post_author );
	}
}
