<?php

/**
 * Class WP_Tabbed_Helper
 * taken from https://wordpress.org/plugins/wp-tab-widget/
 */

class WP_Tabbed_Helper {

	function __construct() {
		add_action( 'wp_ajax_wp_tabbed_get_settings_form', array( __CLASS__, 'ajax_form' ) );
	}

	public static function ajax_form() {

		$nonce = isset( $_REQUEST['_nonce'] ) ? $_REQUEST['_nonce'] : '';
		if ( ! wp_verify_nonce( $nonce, 'wp-tabbed-widget' ) ) {
			die( 'Security check' );
		}

		$widget   = $_REQUEST['widget'];
		$settings = self::get_form_settings( $widget );
		if ( ! $settings ) {
			// ?>
            <div class="warning not-settings"><?php _e( 'Select widget to show settings', 'wp-tabbed-widget' ); ?></div>
			<?php
		} else {
			echo apply_filters( 'wp_tabbed_tab_settings', $settings );
		}

		die();
		// wp_die(); // this is required to terminate immediately and return a proper response
	}

	static function get_form_settings( $widget_class, $data = array() ) {
		$widget = false;
		if ( $widget_class == '' ) {
			return false;
		}
		if ( is_string( $widget_class ) && class_exists( $widget_class ) ) {
			$widget = new $widget_class;
		}
		if ( is_object( $widget_class ) ) {
			$widget = $widget_class;
		}

		if ( ! method_exists( $widget, 'form' ) ) {
			return false;
		}

		$widget->number = uniqid();

		ob_start();
		ob_end_clean();
		ob_start();

		echo '<div class="widget-inside">';
		echo '<div class="form">';
		echo '<div class="widget-content">';

		$widget->form( $data );
		echo '</div>';

		echo '<input type="hidden" class="id_base" value="' . esc_attr( $widget->id_base ) . '">';
		echo '<input type="hidden" class="widget-id" value="' . esc_attr( uniqid( 'tab-' ) ) . '">';
		echo '</div>';
		echo '</div>';


		$form = ob_get_clean();

		return $form;
	}

}


class WP_Tabbed_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wp-tabbed-widget', // Base ID
			'Grid Widget', // Name
			array(
				'Display a grid widget',
				'classname' => 'wp-tabbed-widget'
			), // Args
			array(
				'width' => 630
			)
		);
	}

	/**
	 * Widget admin settings form
	 *
	 * @param array $instance
	 */
	public function form( $instance ) {
		$this_widget = get_class( $this );
		global $wp_widget_factory;

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( ' jquery-ui-sortable' );
		wp_enqueue_script( 'wp-tabbed-admin', get_template_directory_uri() . '/views/js/widgets/admin-tabs.js', array( 'jquery' ), '1.0', 'true' );
		wp_enqueue_style( 'wp-tabbed-admin', get_template_directory_uri() . '/views/css/widgets/admin.css' );

		wp_localize_script( 'wp-tabbed-admin', 'WP_Tabbed_Widget_Settings', array(
			'id'       => $this->id_base,
			'untitled' => __( 'Untitled', 'wp-tabbed-widget' ),
			'nonce'    => wp_create_nonce( 'wp-tabbed-widget' ),
		) );

		$instance = wp_parse_args( $instance, array(
			'title'          => '',
			'current_active' => 0,
			'tabs'           => array(),
		) );

		if ( ! is_array( $instance['tabs'] ) ) {
			$instance['tabs'] = array();
		}

		$id = uniqid( 'wptw-' );

		$tabs_html = '';

		?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Justify Content (leave blank for
                flex-start)</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'class' ); ?>">Wrapper Class</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>"
                   name="<?php echo $this->get_field_name( 'class' ); ?>" type="text"
                   value="<?php echo esc_attr( isset( $instance['class'] ) ? $instance['class'] : '' ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'wrapper' ); ?>">Wrapper ID</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'wrapper' ); ?>"
                   name="<?php echo $this->get_field_name( 'wrapper' ); ?>" type="text"
                   value="<?php echo esc_attr( isset( $instance['wrapper'] ) ? $instance['wrapper'] : '' ); ?>">
        </p>

        <div class="wp-tw-tabs <?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>">
            <input class="base_tab_id" id="<?php echo $this->get_field_id( 'base_tab_id' ); ?>"
                   name="<?php echo $this->get_field_name( 'base_tab_id' ); ?>" type="hidden"
                   value="<?php echo esc_attr( time() ); ?>">

            <script type="text/html" class="title-tpl">
				<?php echo $this->_tab_title(); ?>
            </script>
            <script type="text/html" class="settings-tpl">
				<?php echo $this->_tab_content(); ?>
            </script>
            <input class="current_active" name="<?php echo $this->get_field_name( 'current_active' ); ?>" type="hidden"
                   value="<?php echo esc_attr( $instance['current_active'] ); ?>">
            <ul class="wp-tw-nav">
                <li class="ui-state-disabled add-new-tab">
                    <span class="dashicons dashicons-plus"></span>
                </li>
				<?php
				foreach ( $instance['tabs'] as $k => $data ) {
					if ( ! isset ( $data['settings'] ) ) {
						$data['settings'] = array();
					}
					$title = isset( $data['settings']['title'] ) ? $data['settings']['title'] : '';
					echo $this->_tab_title( $title );
					$tabs_html .= $this->_tab_content( $data['widget_class'], $data['settings'] );
				}
				?>
            </ul>

			<?php if ( ! count( $instance['tabs'] ) ) { ?>
                <div class="no-tabs">
                    <div class="warning">
						<?php _e( 'No tabs yet, Click <span class="dashicons dashicons-plus"></span> button to add new tab', 'wp-tabbed-widget' ); ?>
                    </div>
                </div>

			<?php } ?>
            <div class="wrapper">
                <div class="wp-tw-tab-contents">
					<?php
					if ( $tabs_html ) {

						echo apply_filters( 'wp_tabbed_settings_tabs', $tabs_html, $instance );

					} else {

					}
					?>
                </div>
            </div>
        </div>
		<?php
	}

	function _tab_title( $title = '' ) {
		if ( $title == '' ) {
			$title = __( 'Untitled', 'wp-tabbed-widget' );
		}

		return '<li class="wp-tw-title">
                    <span class="wp-tw-label">' . esc_html( $title ) . '</span>
                    <input type="hidden" class="tab-value" name="' . $this->get_field_name( 'tabs[]' ) . '" >
                    <a href="#" class="wp-tw-remove"><span class="dashicons dashicons-no-alt"></span></a>
                </li>';
	}

	function _tab_content( $widget_class = '', $data = array() ) {
		global $wp_widget_factory;
		$this_widget = get_class( $this );
		ob_start();
		?>
        <div class="wp-tw-tab-content">
            <label for="widget-wp-tabbed-widget-2-nav_menu"><?php _e( 'Select widget:', 'wp-tabbed-widget' ); ?></label>
            <select class="widget_type" name="widget_class">
                <option value=""><?php _e( '— Select —', 'wp-tabbed-widget' ); ?></option>
				<?php foreach ( $wp_widget_factory->widgets as $k => $widget ) {
					if ( $k == $this_widget ) {
						continue;
					}
					?>
                    <option <?php selected( $widget_class, $k ); ?>
                            value="<?php echo esc_attr( $k ) ?>"><?php echo esc_html( $widget->name ); ?></option>
					<?php
				} ?>
            </select>
            <span class="spinner"></span>

            <div class="tabbed-widget-settings widget">
				<?php
				if ( $widget_class != '' ) {
					echo WP_Tabbed_Helper::get_form_settings( $widget_class, $data );
				} else {
					?>
                    <div class="warning not-settings"><?php _e( 'Select widget to show settings', 'wp-tabbed-widget' ) ?></div>
					<?php
				}
				?>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			$flex = apply_filters( 'widget_title', $instance['title'] );
		}

		if ( ! empty( $instance['class'] ) ) {
			$class = ' ' . $instance['class'];
		} else {
			$class = '';
		}

		if ( ! empty( $instance['wrapper'] ) ) {
			$wrapper = $instance['wrapper'];
		} else {
			$wrapper = '';
		}

		$flex = isset( $flex ) ? $flex : 'flex-start';

		$instance = wp_parse_args( $instance, array(
			'title' => '',
			'tabs'  => array(),
		) );

		wp_enqueue_style( 'wp-tabbed', get_template_directory_uri() . '/views/css/widgets/tabbed.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wp-tabbed', get_template_directory_uri() . '/views/js/widgets/tabbed.js', array( 'jquery' ), '1.0', true );

		?>
        <div class="wp-tabbed-tabs"<?php echo $wrapper ? sprintf( ' id="%s"', $wrapper ) : ''; ?>>
            <div class="wp-tabbed-contents<?php echo $class; ?>"
                 style="display: flex; width:100%; justify-content: <?php echo $flex; ?>;">
				<?php
				global $wp_widget_factory;
				foreach ( $instance['tabs'] as $k => $data ) {
					if ( ! isset ( $data['settings'] ) ) {
						$data['settings'] = array();
					}

					if ( isset( $data['settings']['title'] ) ) {
						$data['settings']['title'] = '';
					}

					$widget_class = isset( $data['widget_class'] ) ? $data['widget_class'] : false;
					echo '<div class="wp-tabbed-cont tab-' . esc_attr( $k ) . '">';
					if ( isset( $wp_widget_factory->widgets[ $widget_class ] ) ) {
						$widget_obj             = $wp_widget_factory->widgets[ $widget_class ];
						$_args                  = $args;
						$_args['before_widget'] = str_replace( $this->id_base, $widget_obj->widget_options['classname'], $_args['before_widget'] );
						$_args['before_title']  = '<h2 class="widget-title">';
						$_args['after_title']   = '</h2>';
						$_args['widget_id']     = $args['widget_id'] . '-tab-' . $k;
						$widget_obj->id         = $args['widget_id'] . '-tab-' . $k;
						if ( method_exists( $widget_obj, 'widget' ) ) {
							$widget_obj->widget( $_args, $data['settings'] );
						}

					}
					echo '</div>';

					?>
					<?php
				}
				?>
            </div>
        </div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance            = array();
		$instance['title']   = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['class']   = ( ! empty( $new_instance['class'] ) ) ? sanitize_text_field( $new_instance['class'] ) : '';
		$instance['wrapper'] = ( ! empty( $new_instance['wrapper'] ) ) ? sanitize_text_field( $new_instance['wrapper'] ) : '';

		global $wp_widget_factory;
		if ( isset( $new_instance['tabs'] ) ) {
			foreach ( $new_instance['tabs'] as $k => $tab ) {
				$settings = wp_parse_args( $tab, array( 'widget_class' => '', 'widget-tab-anonymous' => '' ) );
				$keys     = array_keys( $settings );

				$key = array_search( 'widget_class', $keys );
				if ( false !== $key ) {
					unset( $keys[ $key ] );
				}

				$data = array();

				foreach ( ( array ) $keys as $key ) {
					if ( isset( $settings[ $key ] ) ) {

						$s = $settings[ $key ];
						if ( is_array( $s ) ) {
							$data = current( $s );
						} else {
							$data = array();
						}

					}
				}

				if ( isset( $wp_widget_factory->widgets[ $settings['widget_class'] ] ) ) {
					$data = $wp_widget_factory->widgets[ $settings['widget_class'] ]->update( $data, array() );
					if ( $data['title'] == '' ) {
						$data['title'] = $wp_widget_factory->widgets[ $settings['widget_class'] ]->name;
					}
				}

				$instance['tabs'][ $k ]['widget_class'] = $settings['widget_class'];
				$instance['tabs'][ $k ]['settings']     = $data;
			}
		} else {

		}

		$instance['current_active'] = isset( $new_instance['current_active'] ) ? intval( $new_instance['current_active'] ) : 0;

		return $instance;
		// return $instance;
	}

}

add_action( 'widgets_init', function () {
	register_widget( 'WP_Tabbed_Widget' );
} );

if ( is_admin() ) {
	new WP_Tabbed_Helper();
}