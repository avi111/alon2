<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich
 * Date: 22/11/2017
 * Time: 13:43
 */

// TODO: please test this

namespace factory;


class enqueues {
	protected $parent_folder;
	protected $hierarachy;
	protected $js_folder;
	protected $css_folder;

	/**
	 * enqueues constructor.
	 */
	public function __construct( $parent_folder = false, $hierarachy = false ) {
		$this->parent_folder = $parent_folder;
		$this->hierarachy    = $hierarachy;

		if ( ! $this->parent_folder ) {
			$this->parent_folder = sprintf( '%s/views/assets', get_stylesheet_directory() );
		}
		if ( ! $this->hierarachy ) {
			$this->css_folder = sprintf( '%s/%s', $this->parent_folder, 'css' );
			$this->js_folder  = sprintf( '%s/%s', $this->parent_folder, 'js' );
		}
		if ( ! file_exists( $this->parent_folder ) ) {
			wp_mkdir_p( $this->parent_folder );
		}
	}

	public function bootstrap( $mode = false ) {
		if ( $mode ) {
			switch ( $mode ) {
				case 'css':
					$this->css_folder = sprintf( '%s/%s', $this->parent_folder, $this->hierarachy );
					break;
				case 'js':
					$this->js_folder = sprintf( '%s/%s', $this->parent_folder, $this->hierarachy );
					break;
			}
		}

		if ( ! file_exists( $this->css_folder ) ) {
			wp_mkdir_p( $this->css_folder );
		}

		if ( ! file_exists( $this->js_folder ) ) {
			wp_mkdir_p( $this->js_folder );
		}

		return $this;
	}

	public function enqueue() {
		$array = apply_filters( 'css_enqueues', array() );
		if ( ! $array ) {
			$array = array();
		}

		$css = array_merge(
			$array, glob( sprintf( '%s/*.css', $this->css_folder ) )
		);

		array_map( array(
			$this,
			'css_map'
		), $css
		);

		$array = apply_filters( 'js_enqueues', array() );
		if ( ! $array ) {
			$array = array();
		}

		$js=array_merge(
			$array, glob( sprintf( '%s/*.js', $this->js_folder ) )
		);

		array_map( array(
			$this,
			'js_map'
		), $js
		);

		return $this;
	}

	protected function css_map( $file ) {
		if ( file_exists( $file ) ) {
			return $this->css_map_file( $file );
		}

		if ( filter_var( $file, FILTER_VALIDATE_URL ) ) {
			return $this->css_map_url( $file );
		}

		return array();
	}

	protected function js_map( $file ) {
		if ( file_exists( $file ) ) {
			return $this->js_map_file( $file );
		}

		if ( filter_var( $file, FILTER_VALIDATE_URL ) ) {
			return $this->js_map_url( $file );
		}

		return array();
	}

	protected function js_map_file( $file ) {
		$info = pathinfo( $file );
		$url  = str_replace( '\\', '/', str_replace( ABSPATH, site_url(), $file ) );

		add_action( 'wp_enqueue_scripts', function () use ( $info, $url ) {
			$handle    = isset( $info['filename'] ) ? $info['filename'] : sanitize_title( $url );
			$deps      = apply_filters( sprintf( '%s_%s_deps', $handle, get_current_blog_id() ), apply_filters( 'global_deps', array() ) );
			$ver       = apply_filters( sprintf( '%s_%s_ver', $handle, get_current_blog_id() ), false );
			$in_footer = apply_filters( sprintf( '%s_%s_in_footer', $handle, get_current_blog_id() ), true );

			if($info['extension']=='js') {
				wp_enqueue_script( $handle, $url, $deps, $ver, $in_footer );
			}
		} );
	}

	protected function js_map_url( $url ) {
		add_action( 'wp_enqueue_scripts', function () use ( $url ) {
			$info      = pathinfo( $url );
			$handle    = isset( $info['filename'] ) ? $info['filename'] : sanitize_title( $url );
			$deps      = apply_filters( sprintf( '%s_%s_deps', $handle, get_current_blog_id() ), apply_filters( 'global_deps', array() ) );
			$ver       = apply_filters( sprintf( '%s_%s_ver', $handle, get_current_blog_id() ), false );
			$in_footer = apply_filters( sprintf( '%s_%s_in_footer', $handle, get_current_blog_id() ), true );

			if($info['extension']=='js') {
				wp_enqueue_script( $handle, $url, $deps, $ver, $in_footer );
			}
		} );
	}

	protected function css_map_file( $file ) {
		$info = pathinfo( $file );
		$url  = str_replace( '\\', '/', str_replace( ABSPATH, site_url(), $file ) );

		if ( file_exists( $file ) ) {
			add_action( 'wp_enqueue_scripts', function () use ( $info, $url ) {
				$handle = isset( $info['filename'] ) ? ( $info['filename'] . '-css' ) : sanitize_title( $url );
				$deps   = apply_filters( sprintf( '%s_%s_deps', $handle, get_current_blog_id() ), array() );
				$ver    = apply_filters( sprintf( '%s_%s_ver', $handle, get_current_blog_id() ), false );
				$media  = apply_filters( sprintf( '%s_%s_media', $handle, get_current_blog_id() ), 'all' );

				if($info['extension']=='css') {
					wp_enqueue_style( $handle, $url, $deps, $ver, $media );
				}
			} );
		}
	}

	protected function css_map_url( $url ) {
		add_action( 'wp_enqueue_scripts', function () use ( $url ) {
			$info   = pathinfo( $url );
			$handle = isset( $info['filename'] ) ? ( $info['filename'] . '-css' ) : sanitize_title( $url );
			$deps   = apply_filters( sprintf( '%s_%s_deps', $handle, get_current_blog_id() ), array() );
			$ver    = apply_filters( sprintf( '%s_%s_ver', $handle, get_current_blog_id() ), false );
			$media  = apply_filters( sprintf( '%s_%s_media', $handle, get_current_blog_id() ), 'all' );

			if($info['extension']=='css') {
				wp_enqueue_style( $handle, $url, $deps, $ver, $media );
			}
		} );
	}
}