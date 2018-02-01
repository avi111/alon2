<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/09/2017
 * Time: 01:02
 */

namespace cache;


class widgets_cache {

	/**
	 * widgets_cache constructor.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'widget_cache' ) );

		add_action( 'init', array( $this, 'handle_widget_cache_option' ) );
	}

	public function widget_cache() {
		wp_add_dashboard_widget(
			'delete_widget_cache',
			'delete component cache',
			array( $this, 'delete_widget_cache_func' ),
			null //'prefix_dashboard_widget_handle'
		);

		wp_add_dashboard_widget(
			'choose_widgets_to_cache',
			'choose widgets to cache',
			array( $this, 'choose_widgets_to_cache_func' ),
			null //'prefix_dashboard_widget_handle'
		);
	}

	public function handle_widget_cache_option() {
		if ( isset( $_POST['choose_widgets_to_cache_func'] )  && isset($_POST['widgets'])) {
			$_POST['widgets'] = array_filter( $_POST['widgets'] );
			if ( isset( $_POST['widgets'] ) && is_array( $_POST['widgets'] ) ) {
				update_option( 'widgets_cache', implode( ',', $_POST['widgets'] ) );
			} else {
				update_option( 'widgets_cache', '' );
			}
		}

		define( 'WIDGET_CACHE_ARRAY', explode( ',', get_option( 'widgets_cache' ) ) );
	}

	public function delete_widget_cache_func() {
		if ( isset( $_POST['delete_widget_cache_func'] ) && isset( $_POST['widgets'] ) && is_array( $_POST['widgets'] ) ) {
			$this->delete_widget_cache_action( $_POST['widgets'] );
		}
		?>
        <form method="post">
            <p>this is a multiple select field so you can choose more than one with a ctrl+click</p>
            <input type="hidden" name="delete_widget_cache_func" value="1">
            <select multiple name="widgets[]" style="width:100%; height:200px;">
				<?php
				foreach ( WIDGET_CACHE_ARRAY as $widget ) {
					?>
                    <option value="<?php echo $widget; ?>"><?php echo $widget; ?></option>
					<?php
				}
				?>
            </select>
            <div style="clear: both;"></div>
            <button type="submit">delete</button>
        </form>
		<?php
	}

	public function delete_widget_cache_action( $array ) {
		?>
        <ul>
			<?php
			foreach ( $array as $shortcode ) {
				$this->delete_all_caches_for_widget( $shortcode );
			}
			?>
        </ul>
        <hr/>
		<?php
	}

	public function delete_all_caches_for_widget( $shortcode ) {
		$cache_path = get_stylesheet_directory() . '/cache/';
		foreach ( array( '-login', '-logout' ) as $logged ) {
			foreach ( array( '-mobile', '-desktop' ) as $mobile ) {
				$this->delete_cache( $cache_path, $shortcode, $logged, $mobile );
			}
		}
	}

	protected function delete_cache( $cache_path, $shortcode, $logged, $mobile ) {
		$widget_cache_path = $cache_path . $shortcode . $logged . $mobile . '.html';
		if ( file_exists( $widget_cache_path ) ) {
			if ( unlink( $widget_cache_path ) ) {
				echo sprintf( '<li>deleted successfully %s component for state %s + %s</li>', $shortcode, trim( $logged, '-' ), trim( $mobile, '-' ) );
			} else {
				echo sprintf( '<li>couldn\'t delete %s component for state %s + %s</li>', $shortcode, trim( $logged, '-' ), trim( $mobile, '-' ) );
			}
		} else {
			echo sprintf( '<li>%s component for state %s + %s is not exists</li>', $shortcode, trim( $logged, '-' ), trim( $mobile, '-' ) );
		}
	}

	public function choose_widgets_to_cache_func() {
		?>
        <form method="post">
            <p>this is a multiple select field so you can choose more than one with a ctrl+click</p>
            <input type="hidden" name="choose_widgets_to_cache_func" value="1">
            <select multiple name="widgets[]" style="width:100%; height:200px;">
                <option value="">none</option>
				<?php

				$path = get_stylesheet_directory() . WIDGETS;

				$glob = glob( "$path*.php" );
				if ( of_get_option( 'haml' ) && of_get_option( 'widgets_haml' ) ) {
					$glob = array_merge( $glob, glob( "$path*.haml" ) );
				}
				if ( of_get_option( 'twig' ) && of_get_option( 'widgets_twig' ) ) {
					$glob = array_merge( $glob, glob( "$path*.twig" ) );
				}

				foreach ( $glob as $file ) {
					$widget = str_replace( $path, '', $file );
					$widget = str_replace( '.php', '', $widget );
					$widget = str_replace( '.haml', '', $widget );
					$widget = str_replace( '.twig', '', $widget );

					$selected = in_array( $widget, WIDGET_CACHE_ARRAY ) ? ' selected' : '';
					?>
                    <option value="<?php echo $widget; ?>"<?php echo $selected; ?>><?php echo $widget; ?></option>
					<?php
				}
				?>
            </select>
            <div style="clear: both;"></div>
            <button type="submit">save</button>
        </form>
		<?php
	}
}