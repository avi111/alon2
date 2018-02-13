<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 12/02/2018
 * Time: 23:39
 */

namespace wpdb;


class wpdb {
	protected $wpdb;
	protected $enable;
	static protected $instance;

	static public function get() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * wpdb constructor.
	 */
	private function __construct() {
		global $wpdb;
		$this->wpdb   = $wpdb;
		$this->enable = $this->getController();
	}

	protected function getController() {
		$enable = of_get_option( 'db_cache' ) && ! is_admin();

		return $enable;
	}

	public function query( $query ) {
		if ( $this->enable ) {
			$results = apply_filters( 'posts_pre_query', null, $query );
			if ( $results ) {
				return $results;
			} else {
				$posts = $this->wpdb->query( $query );
				$posts = apply_filters( 'posts_results', $posts, $query );

				return $posts;
			}
		} else {
			$posts = $this->wpdb->query( $query );

			return $posts;
		}
	}

	public function get_var( $query = null, $x = 0, $y = 0 ) {
		if ( $this->enable ) {
			$results = apply_filters( 'posts_pre_query', null, $query );
			if ( $results ) {
				return $results;
			} else {
				$posts = $this->wpdb->get_var( $query, $x, $y );
				$posts = apply_filters( 'posts_results', $posts, $query );

				return $posts;
			}
		} else {
			$posts = $this->wpdb->get_var( $query, $x, $y );

			return $posts;
		}
	}

	public function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		if ( $this->enable ) {
			$results = apply_filters( 'posts_pre_query', null, $query );
			if ( $results ) {
				return $results;
			} else {
				$posts = $this->wpdb->get_row( $query, $output, $y );
				$posts = apply_filters( 'posts_results', $posts, $query );

				return $posts;
			}
		} else {
			$posts = $this->wpdb->get_row( $query, $output, $y );

			return $posts;
		}
	}

	public function get_col( $query = null, $x = 0 ) {
		if ( $this->enable ) {
			$results = apply_filters( 'posts_pre_query', null, $query );
			if ( $results ) {
				return $results;
			} else {
				$posts = $this->wpdb->get_col( $query, $x );
				$posts = apply_filters( 'posts_results', $posts, $query );

				return $posts;
			}
		} else {
			$posts = $this->wpdb->get_col( $query, $x );

			return $posts;
		}
	}

	public function get_results( $query = null, $output = OBJECT ) {
		if ( $this->enable ) {
			$results = apply_filters( 'posts_pre_query', null, $query );
			if ( $results ) {
				return $results;
			} else {
				$posts = $this->wpdb->get_results( $query, $output );
				$posts = apply_filters( 'posts_results', $posts, $query );

				return $posts;
			}
		} else {
			$posts = $this->wpdb->get_results( $query, $output );

			return $posts;
		}
	}

}