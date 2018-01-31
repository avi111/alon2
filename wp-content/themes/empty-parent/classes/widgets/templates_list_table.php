<?php

namespace widgets;

class templates_list_table extends \WP_List_Table {
	protected $columns;
	protected $primary;
	protected $screen;
	static protected $table;
	static protected $class;

	/** Class constructor */
	public function __construct() {
		global $wpdb;
		$map         = \orm\map::get_instance()->getData();
		$data        = $map["{$wpdb->prefix}templates"];
		self::$table = 'wp_templates';

		global $wpdb;
		self::$class   = str_replace( $wpdb->prefix, 'orm_', self::$table );
		$this->columns = array( 'template_name' );
		$this->primary = array_search( 'PRI', array_column( $data, 'Key' ) );

		parent::__construct( [
			'singular' => apply_filters( "singular_wp_templates", "wp_templates item" ),
			'plural'   => apply_filters( "plural_wp_templates", "wp_templates items" ),
			'ajax'     => false
		] );
	}

	public static function get_all( $per_page = 5, $page_number = 1 ) {
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}_templates";
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
		$sql    .= " LIMIT $per_page";
		$sql    .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	public static function delete_one( $id ) {
		$class = self::$class;

		return $class::delete( $id );
	}

	public static function record_count() {
		$class = self::$class;

		return $class::count();
	}

	public function no_items() {
		echo apply_filters( "no_items_wp_templates", "No wp_templates items found" );
	}

	function column_name( $item ) {
		$delete_nonce = wp_create_nonce( "delete_wp_templates" );
		$title        = '<strong>' . $item['name'] . '</strong>';
		$actions      = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&wp_templates=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}

	public function column_default( $item, $column_name ) {
		if ( $column_name == 'template_name' ) {
			$url = admin_url( sprintf( "admin.php?page=%s&id=%s", $_GET['page'], $item[ 'id' ] ) );
			$instance=new \widgets\template($item['id']);
			return sprintf( '<a href="%s">%s</a>', $url, $instance->getName() );
		} else {
			if ( isset( $item[ $column_name ] ) ) {
				return wp_trim_words( $item[ $column_name ] );
			}
		}
		return false;
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item[ $this->columns[0] ]
		);
	}

	function get_columns() {
		$columns                  = array( 'cb' => '<input type="checkbox" />' );
		$columns['template_name'] = 'template_name';

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns['template_name'] = 'template_name';

		return $sortable_columns;
	}

	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( "wp_templates_per_page", 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );


		$this->items = self::get_all( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, "delete_wp_templates" ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_one( absint( $_GET["wp_templates"] ) );

				wp_redirect( esc_url( \util::current_url() ) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_one( $id );

			}

			wp_redirect( esc_url( \util::current_url() ) );
			exit;
		}
	}
}