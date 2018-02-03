<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/10/2017
 * Time: 19:12
 */

namespace components;


abstract class component {
	protected function basename() {
		$class = basename( str_replace( '\\', '/', static::class ) );

		return $class;
	}

	public function show() {
		$class     = $this->basename();
		$widgets   = get_stylesheet_directory() . '/views/widgets/*.*';
		$files     = glob( $widgets );
		$file      = array_filter( $files, function ( $file ) use ( $class ) {
			$info     = pathinfo( $file );
			$filename = $info['filename'];

			return $filename === $class;
		} );
		$file      = array_pop( $file );
		$file      = pathinfo( $file );
		$extension = $file['extension'];
		$path      = "views/widgets/{$class}.{$extension}";
		$locate    = locate_template( $path );
		$id        = method_exists( static::class, 'id' );
		if ( $id ) {
			$hash = sanitize_title( \util\util::kebab_case_from_camel_case( $this->basename() ) ) . '_' . spl_object_hash( $this );
			echo sprintf( '<div id="%s">', $hash );
		}
		if ( $extension != 'php' ) {
			if ( of_get_option( "widgets_{$extension}" ) && $locate ) {
				$viewbag = apply_filters( $class . '_widget', $this->atts );
				$adapter = sprintf( '\engines\%s\%s::execute', $extension, $extension );
				echo $adapter( $class, $viewbag );
			}
		} else {
			$widget = $this;
			$path   = "controllers/widgets/{$class}.php";
			require locate_template( $path );
			require $locate;
		}
		if ( $id ) {
			echo '</div>';
		}
	}
}