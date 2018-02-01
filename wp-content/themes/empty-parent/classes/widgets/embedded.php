<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/09/2017
 * Time: 11:24
 */

namespace widgets;

class embedded {

	protected $templates_list;
	protected $templates;

	/**
	 * embedded constructor.
	 */
	public function __construct() {
		$this->create_tables_if_not_exists();
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'wp_ajax_template_list', array( $this, 'template_list_ajax' ) );
	}

	public function template_list_ajax() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;

		if ( wp_verify_nonce( $nonce, 'templates' ) ) {
			$params = isset( $_POST['params'] ) ? $_POST['params'] : false;
			$action = isset( $_POST['what'] ) ? $_POST['what'] : false;

			$this->{$action}( $params );
		}
		exit();
	}

	protected function edit( $params ) {
		$name = isset( $params['name'] ) ? $params['name'] : false;
		$id   = isset( $params['id'] ) ? $params['id'] : false;
		if ( $id ) {
			$result = \orm_templates::update( $id, array(
				'template_name' => $name
			) );
			if ( $result !== false ) {
				echo $name;
			}
		} else {
			wp_die( 'false' );
		}
	}

	protected function add( $params ) {
		/**
		 * when params=array(), a notice will appear
         * PHP Notice:  wpdb::prepare was called incorrectly. The query argument of wpdb::prepare() must have a placeholder. in C:\Users\User\Documents\avraham\wp-includes\functions.php on line 4139
         * that's because we enter all nulls to templates table, since it all have defaults (all indexes)
		 */
		$result = \orm_templates::create(array());

		if ( $result ) {
			global $wpdb;
			echo $wpdb->insert_id;
		} else {
			wp_die( 'false' );
		}

	}

	protected function delete( $params ) {
		$id     = $params;
		$result = true;
		foreach ( $params as $id ) {
			$result &= \orm_templates::delete( $id );
		}
		if ( $result ) {
			echo 'true';
		} else {
			wp_die( 'false' );
		}
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function widgets_init() {
		require_once( ABSPATH . 'wp-admin/includes/widgets.php' );

		if(class_exists('orm_templates')) {
			$this->templates = \orm_templates::get_latest();
			foreach ( $this->templates as $template ) {
				register_sidebar( array(
					'name'          => ! $template->template_name ? ( "template " . $template->id ) : $template->template_name,
					'id'            => "template_{$template->id}",
					'before_widget' => '',
					'after_widget'  => '',
					'before_title'  => '',
					'after_title'   => '',
				) );
			}
		}
	}

	protected function create_tables_if_not_exists() {
		global $wpdb;
		$db = DB_NAME;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$map = \orm\map::get_instance();
		if ( ! in_array( $wpdb->prefix . 'templates', $map->getTables() ) ) {
			$queries = array(
				"CREATE TABLE IF NOT EXISTS `{$db}`.`{$wpdb->prefix}templates` ( `id` INT NOT NULL AUTO_INCREMENT , `template_name` VARCHAR(255) NULL DEFAULT NULL ,`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
			);
			foreach ( $queries as $query ) {
				$dbdelta = dbDelta( $query );
			}
			if ( class_exists( '\orm\map' ) ) {
				\orm\map::get_instance()->refresh();
				foreach (
					array(
						'templates',
					) as $table
				) {
					$build = new \orm\build( "{$wpdb->prefix}{$table}" );
					$build->execute();
				}

			}
		}

		return false;
	}

	public function admin_menu() {
		$hook = add_menu_page(
			apply_filters( "page_title_templates", "templates" ),
			apply_filters( "menu_title_templates", "templates" ),
			'manage_options',
			"templates.php",
			array( $this, 'output' ),
			apply_filters( "icon_url_templates", '' )
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}

	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => apply_filters( "templates_items", "templates items" ),
			'default' => 5,
			'option'  => "templates_per_page"
		];

		add_screen_option( $option, $args );

		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		$this->templates_list = new \widgets\templates_list_table();

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		     || 'delete' === $this->current_action()
		) {
			$this->templates_list->prepare_items();
		}
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
		$this->show();
	}

	public function show() {
		js_vars( array(
			'items'      => \array_map( function ( $template ) {
				return array(
					'name' => ( ! $template->template_name ? $template->id : $template->template_name ),
					'id'   => $template->id
				);
			}, $this->templates ),
			'nonce'      => wp_create_nonce( 'templates' ),
			'admin_ajax' => admin_url( 'admin-ajax.php' )
		) );
		echo "<script src='https://unpkg.com/vue@2.4.2/dist/vue.js'></script>";
		include_full_view( 'widgets', 'template' );
	}

	protected function form() {
		global $wpdb;
		?>
        <h2><?php echo str_replace( '_', ' ', ucfirst( 'templates' ) ); ?></h2>
        <br class="clear">
        <a href="<?php
		echo admin_url( sprintf( "admin.php?page=%s.php&id=new", $wpdb->prefix . 'templates' ) );
		?>" class="button-secondary">Add New</a>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
							<?php
							$this->templates_list->prepare_items();
							$this->templates_list->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
		<?php
	}
}