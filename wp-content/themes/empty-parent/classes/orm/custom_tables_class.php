<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 01/09/2017
 * Time: 00:06
 */

namespace orm;

class custom_tables_class {
	/**
	 * custom_tables_class constructor.
	 */

	protected $success;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'init' ) );
	}

	public function init() {
		add_menu_page(
			'Generate Tables',
			'Generate Tables',
			'manage_options',
			'custom_tables.php',
			array( $this, 'output' )
		);
	}

	public function output() {
		$viewbag = $this->calculate();
		if ( $viewbag ) {
			if($_REQUEST['action']=='table_create'){
				$success=$this->success;
			}
			include dirname( __FILE__ ) . "/views/{$_REQUEST['action']}.php";
		} else {
			include dirname( __FILE__ ) . '/views/initial_form.php';
		}
	}

	protected function calculate() {
		$nonce = 'custom_tables';
		if ( isset( $_REQUEST['action'] ) && isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], $nonce ) ) {
			switch ( $_REQUEST['action'] ) {
				case 'table_settings':
					if ( ! isset( $_REQUEST['table_name'] ) || ! isset( $_REQUEST['columns_number'] ) ) {
						return false;
					}
					if ( ! $_REQUEST['columns_number'] ) {
						return false;
					}
					if(!is_numeric($_REQUEST['columns_number'])){
						return false;
					}
					if ( isset( $_REQUEST['prefix'] ) ) {
						global $wpdb;
						$_REQUEST['table_name'] = $wpdb->prefix . $_REQUEST['table_name'];
					}
					break;
				case 'table_create':
					if ( ! isset( $_REQUEST['table_name'] ) || ! isset( $_REQUEST['data'] ) ) {
						return false;
					}
					if ( ! is_array( $_REQUEST['data'] ) ) {
						return false;
					}
					$calc = array_map( array( $this, 'validate' ), $_REQUEST['data'] );

					if ( ! array_sum( $calc ) ) {
						return false;
					}

					$this->success=$this->make();
					break;
			}
		} else {
			return false;
		}

		return $_REQUEST;
	}

	protected function validate( $array ) {
		$valid  = array_key_exists( 'column_name', $array ) &&
		          array_key_exists( 'type', $array ) &&
		          array_key_exists( 'default', $array ) &&
		          array_key_exists( 'index', $array ) &&
		          array_key_exists( 'nullable', $array ) &&
		          array_key_exists( 'auto_increment', $array );
		$return = $valid ? 1 : 0;

		return $return;
	}

	protected function make() {
		global $wpdb;
		$collate = $wpdb->get_charset_collate();

		extract( $_REQUEST );
		$db    = DB_NAME;
		$table = $table_name;

		$prepares   = array();
		$prepares[] = $table;

		foreach ( $data as $element ) {
			extract( $element );
			$type = strtoupper( $type );

			switch ( $type ) {
				case 'INT':
				case 'VARCHAR':
					$size = "(255)";
					break;
				default:
					$size = '';
					break;
			}

			switch ( $index ) {
				case 'primary':
					$index_value[] = $wpdb->prepare( "PRIMARY KEY (%s)", $column_name );
					break;
				case 'unique':
					$index_value[] = $wpdb->prepare( "UNIQUE (%s)", $column_name );
					break;
				case 'index':
					$index_value[] = $wpdb->prepare( "INDEX (%s)", $column_name );
					break;
			}
			$null = ( isset( $nullable ) && $nullable == 'on' ) ? 'NULL' : "NOT NULL";
			if ( $default ) {
				if ( $default != 'define' ) {
					$default = " DEFAULT $default";
				} else {
					if ( isset( $default_define ) ) {
						$default = " DEFAULT $default_define";
					} else {
						$default = '';
					}
				}
			} else {
				$default = '';
			}

			$auto_increment = ( isset( $auto_increment ) && $auto_increment == 'on' ) ? " AUTO_INCREMENT" : '';

			$columm[] = $wpdb->prepare( "%s $type$size $null$default$auto_increment", $column_name );
		}

		$indexes = implode( ', ', $index_value );
		$columns = implode( ', ', $columm );
		$query   = "CREATE TABLE %s.%s ($columns, $indexes) ENGINE = InnoDB CHARACTER SET utf8 COLLATE $collate";
		$prepare = $wpdb->prepare( $query, $db, $table );
		$prepare=str_replace('\'','`',$prepare);
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$dbdelta=dbDelta( $prepare );
		if(class_exists('\orm\map')) {
			\orm\map::get_instance()->refresh();
			$build = new \orm\build( $table );
			$build->execute();
		}
		return array_pop($dbdelta);
	}
}