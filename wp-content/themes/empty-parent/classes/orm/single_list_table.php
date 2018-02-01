<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/09/2017
 * Time: 23:55
 */

namespace orm;


class single_list_table {

	protected $table;
	protected $short;
	protected $filename;
	protected $class;
	protected $obj;

	/**
	 * single_list_table constructor.
	 */
	public function __construct( $table ) {
		$this->table = $table;
		global $wpdb;
		$prefix         = $wpdb->prefix;
		$this->short    = str_replace( $prefix, '', $table );
		$this->base     = get_stylesheet_directory() . '/views/wp_list_tables/';
		$this->filename = "{$this->base}{$this->table}_list_table.php";
		$this->class    = "{$this->table}_list_table";

		if ( apply_filters( 'generate_admin_menu_list', $this->short ) ) {
			add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
	}

	public function admin_menu() {
		$hook = add_menu_page(
			apply_filters( "page_title_$this->table", $this->short ),
			apply_filters( "menu_title_$this->table", $this->short ),
			'manage_options',
			"$this->table.php",
			array( $this, 'output' ),
			apply_filters( "icon_url_$this->table", '' )
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}

	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => apply_filters( "{$this->table}_items", "{$this->table} items" ),
			'default' => 5,
			'option'  => "{$this->table}_per_page"
		];

		add_screen_option( $option, $args );

		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		if ( ! ( file_exists( $this->filename ) && class_exists( $this->class ) ) ) {
			$this->build();
			require_once $this->filename;
		}

		$class     = $this->class;
		$this->obj = new $class();

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		     || 'delete' === $this->current_action()
		) {
			$this->obj->prepare_items();
		}
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	protected function current_action() {
		if ( isset( $_REQUEST['filter_action'] ) && ! empty( $_REQUEST['filter_action'] ) ) {
			return false;
		}

		if ( isset( $_REQUEST['action'] ) && - 1 != $_REQUEST['action'] ) {
			return $_REQUEST['action'];
		}

		if ( isset( $_REQUEST['action2'] ) && - 1 != $_REQUEST['action2'] ) {
			return $_REQUEST['action2'];
		}

		return false;
	}

	public function output() {
		?>
        <div class="wrap">

			<?php
			if ( isset( $_POST['action'] ) ) {
				$edit = new \orm\edit( $this );
				if ( ! $edit->update() ) {
					$edit = new \orm\post_new( $this );
					$edit->view();
				}
			} else {
				if ( ! isset( $_GET['id'] ) ) {
					$this->form();
				} else {
					if ( ! is_numeric( $_GET['id'] ) ) {
						$edit = new \orm\post_new( $this );
						$edit->view();
					} else {
						$edit = new \orm\edit( $this );
						if ( $edit->is_empty() ) {
							$this->form();
						} else {
							$edit->view();
						}
					}
				}
			} ?>

        </div>
		<?php
	}

	protected function form() {
		?>
        <h2><?php echo str_replace( '_', ' ', ucfirst( $this->short ) ); ?></h2>
        <br class="clear">
        <a href="<?php
		echo admin_url( sprintf( "admin.php?page=%s.php&id=new", $this->table ) );
		?>" class="button-secondary">Add New</a>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
							<?php
							$this->obj->prepare_items();
							$this->obj->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
		<?php
	}

	protected function build() {
		$content = $this->get_template( 'list_table' );
		$content = str_replace( 'Custom_Table_List', $this->class, $content );
		$content = str_replace( 'tabletable', $this->table, $content );

		if ( ! file_exists( $this->base ) ) {
			mkdir( $this->base );
		}

		if ( ! file_exists( $this->filename ) ) {
			file_put_contents( $this->filename, $content );
		}

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

	/**
	 * @return mixed
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * @return mixed
	 */
	public function getShort() {
		return $this->short;
	}
}