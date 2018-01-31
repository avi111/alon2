<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/10/2017
 * Time: 00:18
 */

namespace engines\twig;

use engines\engine;

class twig extends engine {
	static public function init() {
		\engines\twig\twig::getInstance();
	}

	private static $instance = null;
	private $loader;
	private $env;
	private $templates;
	private $cache;

	public static function getInstance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	private function __construct() {
		$twig = locate_template( 'classes/engines/twig/vendor/autoload.php', true, true );
		if ( of_get_option( 'widgets_twig' ) ) {
			$folder =  get_stylesheet_directory() . '/views/widgets/';
			if ( ! file_exists( $folder ) ) {
				wp_mkdir_p( $folder );
			}
			$this->set_folder( $folder, false );
			$this->add_path( '' );
		}
	}

	static public function of_option( $options ) {
		$engine    = 'twig';
		$formatted = apply_filters( "{$engine}_name_format", ucfirst( $engine ) );

		if ( of_get_option( $engine ) ) {
			$options[] = array(
				'name' => __( $formatted, 'avraham' ),
				'type' => 'heading'
			);
			$options[] = array(
				'name' => __( sprintf( 'Widgets %s Interface', $formatted ), 'avraham' ),
				'id'   => 'widgets_twig',
				'std'  => '1',
				'type' => 'checkbox'
			);
		}

		return $options;
	}

	static public function execute( $class, $viewbag ) {
		$return = '';
		$path   = "/views/widgets/{$class}.twig";
		if ( class_exists( '\engines\twig\twig' ) && of_get_option( 'twig' ) && file_exists( get_stylesheet_directory() . $path ) ) {
			$twig   = static::getInstance();
			$return = $twig->render( 'widgets', $class, $viewbag );
		}

		return $return;
	}


	public function set_folder( $folder, $cache = true, $templates = true ) {
		if ( $templates ) {
			$this->templates = $folder;
		} else {
			wp_mkdir_p( $this->templates = $folder . 'templates/' );
		}

		if ( $cache ) {
			$this->cache = get_stylesheet_directory() . '/cache/';
		} else {
			if ( $cache === false ) {
				$this->cache = $cache;
			} else {
				wp_mkdir_p( $this->cache = $folder . 'cache/' );
			}
		}

		unset( $this->loader );
		unset( $this->env );

		$this->loader = new \Twig_Loader_Filesystem( $this->templates );
		$this->env    = new \Twig_Environment( $this->loader, array(
			'cache' => $this->cache,
		) );
	}

	public function add_path( $inner, $name = false ) {
		$path = trim( "{$this->templates}/{$inner}", '/' );
		if ( ! $name ) {
			$name = basename( $path );
		}
		$this->loader->addPath( $path, $name );

	}

	public function render( $name, $file, $viewbag = array() ) {
		$render = $this->env->render( "@{$name}/$file.twig", $viewbag );

		return $render;
	}

	public function echo( $name, $file, $viewbag = array() ) {
		$render = $this->env->render( "@{$name}/$file.twig", $viewbag );
		echo $render;
	}

	/**
	 * @return mixed
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * @return mixed
	 */
	public function getEnv()
	{
		return $this->env;
	}
}
