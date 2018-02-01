<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 01/09/2017
 * Time: 23:10
 */

namespace orm;


class route {
	protected $table;
	protected $db_table;

	/**
	 * route constructor.
	 */
	public function __construct( $table ) {
		global $wpdb;
		$prefix         = $wpdb->prefix;
		$this->table    = str_replace( $prefix, '', $table );
		$this->db_table = "orm_$this->table";
		$this->custom_tables_api_route();

		add_action( 'rest_api_init', array( $this, 'custom_tables_api_route' ) );
	}

	public function custom_tables_api_route() {
		global $wpdb;
		$vals = array_keys( get_object_vars( $wpdb ) );
		rest_get_server();
		if ( ! in_array( $this->table, $vals ) ) {
			\register_rest_route( '/', '/' . $this->table . '/(?P<id>\d+)', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_one' ),
				'args'     => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						}
					),
				),
			) );

			\register_rest_route( 'wp/v2/', '/' . $this->table . '/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_all' ),
				'args'     => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						}
					),
				),
			) );

			\register_rest_route( 'wp/v2/', '/' . $this->table . '/limit/(?P<limit>\d+)', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_all' ),
				'args'     => array(
					'limit' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						}
					),
				),
			) );

			\register_rest_route( 'wp/v2/', '/' . $this->table . '/offset/(?P<offset>\d+)/limit/(?P<limit>\d+)', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_all' ),
				'args'     => array(
					'limit' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						}
					),
					'offset' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						}
					),
				),
			) );
		}

	}

	public function get_one( $request ) {
		$id    = $request['id'];
		$class = $this->db_table;
		$data  = new $class( $id );
		if ( empty( $data ) || $data->is_empty() ) {
			return new \WP_Error( "no_{$this->table}_item", "Invalid $this->table item", array( 'status' => 404 ) );
		}
		$response = new \WP_REST_Response();
		$response->set_data( $data->reveal() );
		$response = apply_filters( 'rest_prepare_' . $this->table, $response, $data, $request );

		return $response;
	}

	public function get_all( $request ) {
		$params = $request->get_params();

		$limit     = isset( $params['limit'] ) ? $params['limit'] : 0;
		$offset     = isset( $params['offset'] ) ? $params['offset'] : 0;

		$class = $this->db_table;
		$data  = $class::get_latest( $limit,$offset );
		if ( empty( $data ) ) {
			return new \WP_Error( "no_{$this->table}_items", "No $this->table items", array( 'status' => 404 ) );
		}
		$response = new \WP_REST_Response();
		$response->set_data( $data );
		$response = apply_filters( 'rest_prepare_' . $this->table, $response, $data, $request );

		return $response;
	}
}