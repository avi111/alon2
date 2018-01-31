<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26/08/2017
 * Time: 23:32
 */

namespace parent_theme;

use factory\enqueues;
use options\options;

class init {

	protected $supports;
	protected $textdomain;
	protected $lang;

	/**
	 * init constructor.
	 */
	public function __construct() {

		// definitions

		if ( locate_template( 'front/css/' ) ) {
			define( 'STYLES', '/front/css/' );
		}
		if ( locate_template( 'front/js/' ) ) {
			define( 'SCRIPTS', '/front/js/' );
		}

		if ( locate_template( 'front/js/' ) ) {
			define( 'IMAGES', get_stylesheet_directory_uri() . '/front/images/' );
		}

		define( 'MODEL', 'models/' );
		define( 'VIEWS', '/views/' );
		define( 'CONTROLLERS', '/controllers/' );
		define( 'WIDGETS', '/views/widgets/' );
		define( 'TEMPLATES', '/views/templates/' );

		$this->supports = array(
			'post-thumbnails',
			'custom-logo',
			'html5',
			'title-tag',
			'customize-selective-refresh-widgets',
			'menus',
			'sidebars'
		);

		$this->supports = apply_filters( 'inject_supports', $this->supports );

		$this->lang       = $this->get_default( 'lang' );
		$this->textdomain = $this->get_default( 'avraham' );

		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'init', array( $this, 'loadModels' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueues' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
		add_action( 'wp_footer', array( $this, 'style_php' ) );
		add_action( 'after_setup_theme', array( $this, 'setLanguagesFolder' ) );
		add_filter( 'locale', array( $this, 'addLocaleFilter' ) );
		add_filter( 'wpcf7_form_elements', array( $this, 'mycustom_wpcf7_form_elements' ) );
		add_filter( 'util', array( $this, 'util' ) );
		add_filter( 'filter_generator_pairs', array( $this, 'generate_filters' ) );
		add_filter( 'of_options', array( $this, 'of_options' ), 2 );
		add_filter( 'avraham_cache', array( $this, 'handle_avraham_cache' ), 10, 2 );

		$factory = \factory\site::get_instance();
		$factory->enqueue();

		$enqueues = new \factory\enqueues();
		$enqueues->bootstrap()->enqueue();

		\options\options::init();

		new \engines\controller();

		new \addons\security_and_performance();

		if ( of_get_option( 'db_cache' ) ) {
			new \cache\db_cache();
		}

		if ( of_get_option( 'templates' ) ) {
			new \widgets\widget_init();
			new \widgets\embedded();

			if ( of_get_option( 'widgets_cache' ) ) {
				new \cache\widgets_cache();
			}
		}

		if ( of_get_option( 'orm' ) ) {
			new \orm\custom_tables_class();

			foreach ( \get_orm_map()->getTables() as $table ) {
				$build = new \orm\build( $table );
				$build->execute();
				$route = new \orm\route( $table );
			}

			$orm  = 'classes/orm/';
			$base = get_stylesheet_directory() . '/' . $orm;
			if ( file_exists( $base ) ) {
				require_dir( $base, $orm );
			}

			new \orm\list_tables();
		}

		new \minify\css();
		new \minify\js();

		//add_action( 'init', array( $this, 'set_default_locale' ) );

	}

	protected function get_default( $key ) {
		$defaults = DEFAULTS;
		if($defaults && is_array(DEFAULTS)) {
			return $defaults[ $key ] ?? false;
		} else {
			return;
		}
	}

	public function set_default_locale() {
		setlocale( LC_MONETARY, get_locale() );
		$my_local_settings = localeconv();
		if ( $my_local_settings['int_curr_symbol'] == "" ) {
			$default_locale = apply_filters( 'default_locale', 'en_US' );
			setlocale( LC_MONETARY, $default_locale );
		}
	}

	public function of_options( $options ) {
		$options[] = array(
			'name' => __( 'Theme Modules', 'avraham' ),
			'type' => 'heading'
		);

		$options[] = array(
			'name' => __( 'ORM', 'avraham' ),
			'id'   => 'orm',
			'std'  => '1',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'    => __( 'minifier', 'avraham' ),
			'id'      => 'minifier',
			'std'     => 'none',
			'type'    => 'radio',
			'options' => array(
				'none'   => __( 'no', 'avraham' ),
				'always' => __( 'every pageload', 'avraham' ),
				'cached' => __( 'cached', 'avraham' )
			)
		);

		$options = array_merge( $options, \engines\controller::options() );

		$options[] = array(
			'name' => __( 'Database Query Cache', 'avraham' ),
			'id'   => 'db_cache',
			'std'  => '1',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Templates Builder', 'avraham' ),
			'id'   => 'templates',
			'std'  => '1',
			'type' => 'checkbox'
		);

		if ( of_get_option( 'templates' ) ) {
			$options[] = array(
				'name' => __( 'Templates Builder', 'avraham' ),
				'type' => 'heading'
			);
			$options[] = array(
				'name' => __( 'Widgets HTML Static Cache', 'avraham' ),
				'id'   => 'widgets_cache',
				'std'  => '1',
				'type' => 'checkbox'
			);
		}


		return $options;
	}

	public function generate_filters( $pairs ) {
		if ( is_array( $pairs ) ) {
			foreach ( $pairs as $class => $methods ) {
				if ( is_array( $methods ) ) {
					$priorities = array_map( function ( $key, $value ) {
						return is_numeric( $key ) ? 0 : $value;
					}, array_keys( $methods ), array_values( $methods ) );

					$methods = array_map( function ( $key, $value ) {
						return is_numeric( $key ) ? $value : $key;
					}, array_keys( $methods ), array_values( $methods ) );

					$priorities = array_combine( array_values( $methods ), $priorities );

					$array = array_combine( array_map( function ( $method ) use ( $class ) {
						return $class ? "$class::$method" : '';
					}, array_values( $methods ) ), array_values( $methods ) );

					if ( ! is_array( $array ) ) {
						return;
					}

					foreach ( $array as $path => $name ) {
						$params = isset( $priorities[ $name ] ) ? $priorities[ $name ] : 0;
						( new \filters\filters( array(
							$path
						), $params ) )->generate_filter( $name );
					}
				}
			}
		}
	}

	public function util( $function, $args = array() ) {
		$class = '\util\util';

		return $class::$function( $args );
	}

	public function handle_avraham_cache( $shortcode, $atts ) {
		$cache_path        = get_stylesheet_directory() . '/cache/';
		$logged            = is_user_logged_in() ? '-login' : '-logout';
		$mobile            = wp_is_mobile() ? '-mobile' : '-desktop';
		$widget_cache_path = $cache_path . $shortcode . $logged . $mobile . '.html';
		$array             = defined( 'WIDGET_CACHE_ARRAY' ) ? WIDGET_CACHE_ARRAY : array();
		if ( in_array( $shortcode, $array ) ) {
			if ( is_array( $atts ) ) {
				extract( $atts );
			}
			if ( file_exists( $widget_cache_path ) && $content = file_get_contents( $widget_cache_path ) ) {
				return $content;
			} else {
				ob_start();
				$locate = locate_template( WIDGETS . "$shortcode.php" );
				if ( $locate ) {
					include_once get_stylesheet_directory() . WIDGETS . "$shortcode.php";
				}

				foreach ( \engines\controller::$engines as $engine ) {
					$class  = sprintf( '\engines\%s\%s', $engine, $engine );
					$path   = WIDGETS . "$shortcode.$engine";
					$locate = locate_template( $path );
					if ( of_get_option( 'widgets_' . $engine ) && $locate && class_exists( $class ) ) {
						$viewbag = apply_filters( $shortcode . '_widget', $atts );
						echo $class::execute( '/' . $path, $viewbag );
					}
				}

				$output = do_shortcode( ob_get_clean() );
				if ( ! file_exists( $cache_path ) ) {
					wp_mkdir_p( $cache_path );
				}

				$write = file_put_contents( $widget_cache_path, $output );

				return $output;
			}
		} else {
			return false;
		}
	}

	public function theme_supports() {
		$array = $this->supports;
		if ( is_array( $array ) ) {
			foreach ( $array as $element ) {
				add_theme_support( $element );
			}
		}
	}

	public function loadModels() {
		require_dir( CHILD_ROOT . "models" );
		require_dir( PARENT_ROOT . "models" );
	}

	public function enqueues() {
		if ( locate_template( ( is_admin() ? 'admin/' : '' ) . 'style.css' ) ) {
			wp_enqueue_style( 'avraham', get_stylesheet_directory_uri() . ( is_admin() ? '/admin' : '' ) . '/style.css' );
		}
		if ( locate_template( ( is_admin() ? 'admin/' : '' ) . 'script.js' ) ) {
			wp_enqueue_script( 'avraham', get_stylesheet_directory_uri() . ( is_admin() ? '/admin' : '' ) . '/script.js', false, true );
		}
	}

	public function style_php() {
		get_template_part( 'style' );
	}

	public function setLanguagesFolder() {
		$this->textdomain = apply_filters( 'theme_textdomain', $this->textdomain );
		load_theme_textdomain( $this->textdomain, get_stylesheet_directory() . '/resources/languages' );
	}

	public function addLocaleFilter( $locale ) {
		$this->lang = apply_filters( 'set_locale_filter', $this->lang );
		if ( isset( $_GET[ $this->lang ] ) ) {
			return esc_attr( $_GET[ $this->lang ] );
		}

		$locale = apply_filters( 'default_locale', $locale );

		return $locale;
	}

	public function mycustom_wpcf7_form_elements(
		$form
	) {
		$form = do_shortcode( $form );

		return $form;
	}
}