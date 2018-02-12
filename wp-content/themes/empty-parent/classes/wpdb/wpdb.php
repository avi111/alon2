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
		$this->wpdb = $wpdb;
	}

	public function query( $query ) {
		$results = apply_filters( 'posts_pre_query', null, $query );
		if ( $results ) {
			return $results;
		} else {
			$posts = $this->wpdb->query( $query );
			$posts = apply_filters( 'posts_results', $posts, $query );

			return $posts;
		}
	}

	public function get_var( $query = null, $x = 0, $y = 0 ) {
		$results = apply_filters( 'posts_pre_query', null, $query );
		if ( $results ) {
			return $results;
		} else {
			$posts = $this->wpdb->get_var( $query, $x, $y );
			$posts = apply_filters( 'posts_results', $posts, $query );

			return $posts;
		}
	}

	public function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		$results = apply_filters( 'posts_pre_query', null, $query );
		if ( $results ) {
			return $results;
		} else {
			$posts = $this->wpdb->get_row( $query, $output, $y );
			$posts = apply_filters( 'posts_results', $posts, $query );

			return $posts;
		}
	}

	public function get_col( $query = null, $x = 0 ) {
		$results = apply_filters( 'posts_pre_query', null, $query );
		if ( $results ) {
			return $results;
		} else {
			$posts = $this->wpdb->get_col( $query, $x );
			$posts = apply_filters( 'posts_results', $posts, $query );

			return $posts;
		}
	}

	public function get_results( $query = null, $output = OBJECT ) {
		$results = apply_filters( 'posts_pre_query', null, $query );
		if ( $results ) {
			return $results;
		} else {
			$posts = $this->wpdb->get_results( $query, $output );
			$posts = apply_filters( 'posts_results', $posts, $query );

			return $posts;
		}
	}

}