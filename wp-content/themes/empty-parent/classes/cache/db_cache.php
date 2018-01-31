<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28/09/2017
 * Time: 23:08
 */

namespace cache;


class db_cache {
	protected $folder;

	/**
	 * db_cache_results constructor.
	 *
	 * @param $query
	 */
	public function __construct() {
		add_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ), 10, 2 );
		add_filter( 'posts_results', array( $this, 'posts_results' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );

		$this->folder = WP_CONTENT_DIR . "/uploads/cache/queries/";
	}

	public function save_post( $id, $post, $bool ) {
		array_map('unlink', glob("{$this->folder}/*"));
	}

	public function posts_results( $posts, $query ) {
		$md5 = md5( serialize( $query->query_vars ) );
		if ( $md5 && ! is_admin() ) {
			$file      = $md5 . '.txt';
			$full_path = $this->folder . $file;
			if ( ! file_exists( $full_path )) {
				$output = serialize( $posts );
				self::create_folders_up_to_path( $full_path );
				$write = file_put_contents( $full_path, $output );
			}
		}

		return $posts;
	}


	public function posts_pre_query( $something, $query ) {
		$md5 = md5( serialize( $query->query_vars ) );
		if(isset( $_GET['override_cache'] ) && $_GET['override_cache'] ){
			array_map('unlink', glob("{$this->folder}/*"));
		}
		if ( $md5 ) {
			$file      = $md5 . '.txt';
			$full_path = $this->folder . $file;
			if ( file_exists( $full_path ) && $content = file_get_contents( $full_path ) ) {
				return unserialize( $content );
			}
		}

		return null;
	}

	static public function create_folders_up_to_path( $path ) {
		$pathinfo = pathinfo( $path );
		$unexists = array();
		$checked  = isset( $pathinfo['dirname'] ) ? $pathinfo['dirname'] : false;

		while ( $checked && ! file_exists( $checked ) ) {
			$unexists[] = substr( $checked, strrpos( $checked, '/' ), strlen( $checked ) );
			$checked    = substr( $checked, 0, strrpos( $checked, '/' ) );
		}

		$success = true;
		while ( count( $unexists ) ) {
			$checked = $checked . array_pop( $unexists );
			$success &= mkdir( $checked );
			$checked .= '/';
		}

		return $success;
	}
}