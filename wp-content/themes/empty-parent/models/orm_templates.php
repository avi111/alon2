<?php

if ( ! class_exists( 'orm_templates' ) ) {
	class orm_templates implements crud {
		protected $id;
		protected $template_name;
		protected $created;

		function get_id() {
			return $this->id;
		}

		function get_template_name() {
			return $this->template_name;
		}

		function get_created() {
			return $this->created;
		}

		protected $empty;

		public function __construct( $id = 0 ) {
			$this->empty = false;
			if ( ! $id ) {
				return;
			}
			$this->get_instance( $id );
		}

		protected function get_instance( $id ) {
			global $wpdb;
			$query   = "SELECT * FROM wp5k_templates WHERE id=%s";
			$prepare = $wpdb->prepare( $query, $id );
			$results = $wpdb->get_results( $prepare );
			$results = array_pop( $results );
			if ( ! $results ) {
				$this->empty = true;
			} else {
				$this->id            = $results->id;
				$this->template_name = $results->template_name;
				$this->created       = $results->created;
			}
		}

		static protected $table;
		static protected $primary;
		static protected $columns;
		static protected $types;

		public function reveal() {
			$output = get_object_vars( $this );
			unset( $output['empty'] );

			return $output;
		}

		public function is_empty() {
			return $this->empty;
		}

		/**
		 * @return mixed
		 */
		static public function getTable() {
			if ( ! self::$table ) {
				global $wpdb;
				self::$table = str_replace( 'orm_', $wpdb->prefix, static::class );

			}

			return self::$table;
		}

		/**
		 * @return mixed
		 */
		public static function getPrimary() {
			if ( ! self::$primary ) {
				$map           = \orm\map::get_instance()->getData();
				$data          = $map[ self::getTable() ];
				self::$columns = array_column( $data, 'Field' );
				self::$types   = array_column( $data, 'Type' );

				self::$types   = array_map( function ( $type ) {
					return str_replace( array(
						0,
						1,
						2,
						3,
						4,
						5,
						6,
						7,
						8,
						9,
						'(',
						')'
					), array_fill( 0, 11, '' ), $type );
				}, self::$types );
				self::$primary = self::$columns[ array_search( 'PRI', array_column( $data, 'Key' ) ) ];
			}

			return self::$primary;
		}

		static public function get_latest( $limit = 0, $offset = 0 ) {
			if ( ! is_numeric( $limit ) || $limit < 0 ) {
				$limit = 0;
			}
			if ( ! is_numeric( $offset ) || $offset < 0 ) {
				$offset = 0;
			}
			global $wpdb;
			$table = self::getTable();
			$query = "SELECT * FROM {$table}";
			if ( $limit || $offset ) {
				$query .= " LIMIT %d,%d";
				$query = $wpdb->prepare( $query, $limit, $offset );
			}

			$results = $wpdb->get_results( $query );

			return $results;
		}

		static public function count() {
			global $wpdb;
			$sql     = sprintf( "SELECT COUNT(*) FROM %s", self::getTable() );
			$results = $wpdb->get_var( $sql );

			return $results;
		}

		static public function read( $id ) {
			$instance = new static( $id );

			return $instance->reveal();
		}

		static public function create( $array ) {
			global $wpdb;
			list( $data, $types ) = self::prepare_to_db( $array, 'create' );

			return $wpdb->insert( self::getTable(), $data, $types );
		}

		static public function update( $id, $array ) {
			global $wpdb;
			list( $data, $types ) = self::prepare_to_db( $array );

			return $wpdb->update(
				self::getTable(),
				$data,
				array( self::getPrimary() => $id ),
				$types,
				array( '%d' )
			);
		}

		static public function delete( $id ) {
			global $wpdb;

			return $wpdb->delete(
				self::getTable(),
				[ self::getPrimary() => $id ],
				[ '%d' ]
			);
		}

		static protected function getTypes() {
			return self::$types;
		}


		static protected function prepare_to_db( $array, $action = false ) {
			$primary = self::getPrimary();
			$data    = array();
			$types   = array();
			foreach ( self::$columns as $column ) {
				if ( isset( $array[ $column ] ) && self::$types[ array_search( $column, self::$columns ) ] != 'timestamp' && $column != $primary ) {
					$data[ $column ]  = $array[ $column ];
					$types[ $column ] = self::$types[ array_search( $column, self::$columns ) ];
				} else {
					if ( $action == 'create' ) {
						$data[ $column ]  = null;
						$types[ $column ] = self::$types[ array_search( $column, self::$columns ) ];
					}
				}
			}

			$types = array_map( function ( $type ) {
				switch ( $type ) {
					case 'int':
						return '%d';
					case 'float':
						return '%f';
					case 'varchar':
						return '%s';
					case 'timestamp':
					default:
						return false;
				}

				return false;
			}, $types );

			return array( $data, $types );
		}
	}
}
?>