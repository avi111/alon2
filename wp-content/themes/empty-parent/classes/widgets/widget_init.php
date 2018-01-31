<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/09/2017
 * Time: 00:32
 */

namespace widgets;


class widget_init {

	/**
	 * generic constructor.
	 */
	public function __construct() {
		global $classname;
		$classname = array();
		$this->generate();
		add_action( 'widgets_init', array( $this, 'initiate_widgets' ) );
	}

	public function generate() {
		$folder = get_stylesheet_directory() . VIEWS . 'widgets';
		if ( file_exists( $folder ) ) {
			$files = scandir( $folder );
			foreach ( $files as $file ) {
				if ( ! ( $file == '.' || $file == '..' ) ) {
					$fname     = explode( ".", $file );
					$shortcode = $fname[0];
					if ( isset( $fname[1] ) ) {
						switch ( $fname[1] ) {
							case 'php':
								$this->classStack( $shortcode );
								$this->wp_widget( $shortcode );
								$this->widget_class( $shortcode );
								break;
							case 'haml':
								if ( of_get_option( 'widgets_haml' ) ) {
									$this->classStack( $shortcode );
									$this->wp_widget( $shortcode );
									$this->widget_class( $shortcode );
								}
								break;
							case 'twig':
								if ( of_get_option( 'widgets_twig' ) ) {
									$this->classStack( $shortcode );
									$this->wp_widget( $shortcode );
									$this->widget_class( $shortcode );
								}
								break;
						}
					}

				}
			}
		}
	}

	protected function classStack( $class ) {
		global $classname;
		array_push( $classname, $class );
	}

	public function initiate_widgets() {
		global $classname;
		$path = 'views/widget_classes/';
		require_dir( get_stylesheet_directory() . '/' . $path, $path );

		new \widgets\buttons();

		foreach ( $classname as $name ) {
			if ( locate_template( "views/widget_classes/{$name}_widget.php", true, true ) ) {
				register_widget( "{$name}_widget" );

			}
		}

		$controllers = glob( get_stylesheet_directory() . '/controllers/widgets/*.php' );
		foreach ( $controllers as $controller ) {
			$fileinfo = pathinfo( $controller );
			$filename = isset( $fileinfo['filename'] ) ? $fileinfo['filename'] : false;
			if ( $filename ) {
				add_filter( $filename . '_widget', function ( $atts ) use ( $controller, $filename ) {
					$class  = "\components\\$filename";
					$widget = new $class( $atts );
					if ( is_array( $atts ) && count( $atts ) ) {
						extract( $atts );
						unset( $atts );
					}
					include $controller;
					unset( $controller );
					unset( $class );
					unset( $filename );

					return get_defined_vars();
				} );
			}
		}
	}

	protected function wp_widget( $shortcode ) {
		$path    = dirname( __FILE__ );
		$content = file_get_contents( "$path/fast_widget.php" );
		$content = str_replace( 'classname', $shortcode, $content );

		if ( ! file_exists( get_stylesheet_directory() . '/views/widget_classes/' ) ) {
			wp_mkdir_p( get_stylesheet_directory() . '/views/widget_classes/' );
		}
		if ( ! chmod( get_stylesheet_directory() . '/views/widget_classes/', 0755 ) ) {
			return false;
		}

		$widget_file = get_stylesheet_directory() . "/views/widget_classes/{$shortcode}_widget.php";

		if ( ! file_exists( get_stylesheet_directory() . '/controllers/widgets/' ) ) {
			wp_mkdir_p( get_stylesheet_directory() . '/controllers/widgets/' );
		}
		if ( ! chmod( get_stylesheet_directory() . '/controllers/widgets/', 0755 ) ) {
			return false;
		}

		$controller = get_stylesheet_directory() . "/controllers/widgets/{$shortcode}.php";
		if ( ! file_exists( $widget_file ) ) {
			file_put_contents( $widget_file, $content );
		}
		if ( ! file_exists( $controller ) ) {
			file_put_contents( $controller, '<?php' . PHP_EOL );
		}

		return true;
	}

	protected function widget_class( $shortcode ) {
		$path    = dirname( __FILE__ );
		$content = file_get_contents( "$path/widget_class.php" );
		$content = str_replace( 'classname', $shortcode, $content );

		if ( ! file_exists( get_stylesheet_directory() . '/classes/components/' ) ) {
			mkdir( get_stylesheet_directory() . '/classes/components/' );
		}
		if ( ! chmod( get_stylesheet_directory() . '/classes/components/', 0755 ) ) {
			return false;
		}

		$widget_file = get_stylesheet_directory() . "/classes/components/{$shortcode}.php";

		if ( ! file_exists( $widget_file ) ) {
			file_put_contents( $widget_file, $content );
		}

		return true;
	}
}