<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich
 * Date: 09/11/2017
 * Time: 10:58
 */

namespace factory;


class site {
	private static $instance;
	protected $site;
	protected $design;

	/**
	 * site constructor.
	 */
	private function __construct() {
		if ( is_multisite() ) {
			$this->site = get_site();
		} else {
			$array = array(
				'url',
				'wpurl',
				'description',
				'rdf_url',
				'rss_url',
				'rss2_url',
				'atom_url',
				'comments_atom_url',
				'comments_rss2_url',
				'pingback_url',
				'stylesheet_url',
				'stylesheet_directory',
				'template_directory',
				'template_url',
				'admin_email',
				'charset',
				'html_type',
				'version',
				'language',
				'is_rtl',
				'name',
			);
			array_walk( $array, function ( $show ) {
				$this->site[ $show ] = get_bloginfo( $show );
			} );
		}
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function build() {
		$this->getDesign();

		return $this;
	}

	public function render() {
		if ( is_object( $this->design ) ) {
			$this->design->header();
			$this->design->body();
			$this->design->footer();
		} else {
			get_header();
			echo apply_filters( 'get_body', '' );
			get_footer();
		}
	}

	public function enqueue() {
		$class = get_class( $this->getDesign() );
		if(method_exists($class,'css')) {
			add_filter( 'css_enqueues', array( $class, 'css' ) );
		}

		if(method_exists($class,'js')) {
			add_filter( 'js_enqueues', array( $class, 'js' ) );
		}
	}

	/**
	 * @return mixed
	 */
	public function getDesign() {
		if(!$this->design) {
			$design = apply_filters( 'site_design', '' );
			if ( ! $design ) {
				$design = 'default_design';
			}
			$class  = sprintf( '\design\%s', $design );
			if ( class_exists( $class ) ) {
				$this->design = new $class();
			}
		}

		return $this->design;
	}


}