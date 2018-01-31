<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 28/12/2017
 * Time: 23:03
 */

namespace minify;

abstract class minify {

	/**
	 * minify constructor.
	 */

	protected $option;
	protected $minifier;
	protected $cache;
	protected $file;
	protected $type;

	public function __construct() {
		$this->setFile();
		$this->type   = strtoupper( ( pathinfo( static::class ) )['basename'] );
		$this->option = of_get_option( 'minifier' );
		$this->cache  = get_stylesheet_directory() . '/cache/';

		if ( in_array( $this->option, array( 'always', 'cache' ) ) ) {
			$this->load();
		}

		if ( $this->option == 'always' || ( $this->option == 'cache' && ! file_exists( $this->cache . $this->file ) ) ) {
			$this->add_loader_tag();
			add_action( 'wp_footer', array( $this, 'execute' ), 999 );
		}

		$this->setMinifier();
	}


	abstract protected function setFile();

	abstract protected function add_loader_tag();

	abstract public function load();

	protected function url() {
		$url = str_replace( ABSPATH, home_url( '/' ), $this->cache . $this->file );

		return $url;
	}

	protected function setMinifier() {
		$class          = sprintf( '\MatthiasMullie\Minify\%s', $this->type );
		$this->minifier = new $class();
	}

	protected function url_to_path( $url ) {
		$path = str_replace( home_url(), ABSPATH, $url );
		$path = str_replace( '//', '/', $path );

		return $path;
	}

	public function execute() {
		$cache = $this->cache;
		if ( ! file_exists( $cache ) ) {
			wp_mkdir_p( $cache );
		}

		$path = $cache . $this->file;
		$this->minifier->minify( $path );
	}
}