<?php
	/**
	 * Created by PhpStorm.
	 * User: Avi Levkovich
	 * Date: 28/03/2017
	 */

	namespace taxonomy;


	class term {
		public $slug;
		public $taxonomy;
		protected $term;
		protected $name;
		protected $id;
		protected $posts;
		public $postType;
		public $status;

		public function __construct( $slug, $taxonomy = "category" ) {
			$this->slug     = $slug;
			$this->taxonomy = $taxonomy;
			$this->setTerm();
		}

		public function setTerm() {
			$this->term = get_term_by( 'slug', $this->slug, $this->taxonomy );

			if ( is_object( $this->term ) ) {
				$this->name = $this->term->name;
				$this->id   = $this->term->name;
			}
		}

		public function getPosts( $number = 0 ) {
			if ( ! $this->posts || ! $number ) {
				$this->setPosts( $number );
			}

			return $this->posts;
		}

		protected function setPosts( $number ) {
			if ( ! $number || $number < 0 ) {
				$number = - 1;
			}
			if ( ! $this->postType ) {
				$this->postType = 'post';
			}
			$args        = array(
				'post_type'      => $this->postType,
				'tax_query'      => array(
					array(
						'taxonomy' => $this->taxonomy,
						'field'    => 'slug',
						'terms'    => $this->slug,
					),
				),
				'posts_per_page' => $number,
				'post_status'    => $this->status,
			);
			$this->posts = get_posts( $args );
		}
	}