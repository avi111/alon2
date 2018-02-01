<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/08/2017
 * Time: 21:44
 */

namespace orm;


class build {

	protected $class;
	protected $table;
	protected $prefix;
	protected $map;
	protected $data;
	protected $columns;
	protected $primary;

	public function __construct( $table ) {
		$base     = get_stylesheet_directory() . '/classes/orm/';
		$filename = "{$base}{$this->class}.php";
		if ( file_exists( $filename ) ) {
			return;
		}
		$this->prefix  = 'orm_';
		$this->table   = $table;
		$this->class   = $this->extract_classname( $table );
		$this->map     = \orm\map::get_instance()->getData();
		$this->data    = $this->map[ $this->table ];
		$this->columns = array_column( $this->data, 'Field' );

		$primary       = array_search( 'PRI', array_column( $this->data, 'Key' ) );
		$this->primary = $this->data[ $primary ]->{'Field'};
	}

	public function set_prefix( $prefix ) {
		$this->prefix = $prefix;
	}

	protected function extract_classname( $table ) {
		global $wpdb;
		$prefix = $this->prefix ? $this->prefix : '';

		return $prefix . strtolower( str_replace( $wpdb->prefix, '', $table ) );
	}

	protected function get_template( $template ) {
		$current_dir = dirname( __FILE__ );
		$filename    = "$current_dir/templates/$template.php";
		if ( file_exists( $filename ) ) {
			return file_get_contents( $filename );
		} else {
			throw new \Exception( 'file is missing' );
		}
	}

	public function execute() {
		$base     = get_stylesheet_directory() . '/classes/orm/';
		$filename = "{$base}{$this->class}.php";
		if ( str_replace( $this->prefix, '', $this->class ) == 'templates' ) {
			return;
		}
		global $wpdb;
		$vars = array_keys( get_object_vars( $wpdb ) );
		if ( in_array( str_replace( $this->prefix, '', $this->class ), $vars ) ) {
			return;
		}
		if ( ! file_exists( $filename ) && array_key_exists( $this->table, $this->map ) ) {
			$content = $this->get_template( 'skeleton' );
			$content = str_replace( 'name', $this->class, $content );

			$attributes = array();
			$getters    = array();

			foreach ( $this->columns as $column ) {
				$attributes[] = "protected $$column;";
				$getters[]    = str_replace( '{column}', $column, 'function get_{column}(){ return $this->{column}; }' );
			}

			$content = str_replace( 'private $attributes;', implode( "\r\n", $attributes ), $content );
			$content = str_replace( 'private $getters;', implode( "\r\n", $getters ), $content );
			if ( ! file_exists( $base ) ) {
				wp_mkdir_p( $base );
			}

			$content = str_replace( 'private $constructor;', $this->get_constructor(), $content );

			file_put_contents( $filename, $content );
		}
	}

	protected function get_constructor() {
		$content = $this->get_template( 'constructor' );
		$content = str_replace( '<?php', '', $content );
		$content = str_replace( '$primary', $this->primary, $content );
		$content = str_replace( '$table', $this->table, $content );
		$content = str_replace( 'function constructor', 'public function __construct', $content );
		$content = str_replace( 'function get_instance', 'protected function get_instance', $content );

		$assigns = array();
		foreach ( $this->columns as $column ) {
			$assigns[] = str_replace( '{column}', $column, '$this->{column} = $results->{column};' );
		}

		$content = str_replace( '$assignments;', implode( "\r\n", $assigns ), $content );

		return $content;
	}
}