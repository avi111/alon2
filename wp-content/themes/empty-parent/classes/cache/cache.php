<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/09/2017
 * Time: 00:11
 */

namespace cache;


class cache {
	protected $destination;
	protected $callback;
	protected $path;

	/**
	 * cache constructor.
	 *
	 * @param $destination
	 * @param $callback
	 */
	public function __construct( $callback, $destination, $path = false ) {
		$this->destination = $destination;
		$this->callback    = $callback;
		$this->path        = $path;
	}

	public function cache() {
		$function          = $this->callback;
		$cache_path        = get_stylesheet_directory() . "/{$this->path}/";
		$logged            = is_user_logged_in() ? '-login' : '-logout';
		$mobile            = wp_is_mobile() ? '-mobile' : '-desktop';
		$widget_cache_path = $cache_path . $this->destination . $logged . $mobile . '.html';

		if ( file_exists( $widget_cache_path ) && $content = file_get_contents( $widget_cache_path ) ) {
			return $content;
		} else {
			ob_start();
			$function();
			$output = do_shortcode( ob_get_clean() );
			if ( ! file_exists( $cache_path ) ) {
				wp_mkdir_p( $cache_path );
			}
			if ( ! chmod( $cache_path, 0755 ) ) {
				return false;
			}

			$write = file_put_contents( $widget_cache_path, $output );

			return $output;
		}
	}
}