<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/10/2017
 * Time: 19:20
 */

namespace engines;


class controller {

	static public $engines;

	/**
	 * controller constructor.
	 */
	public function __construct() {

		$folders       = glob( sprintf( '%s/*', dirname( __FILE__ ) ), GLOB_ONLYDIR );
		self::$engines = array_map( function ( $folder ) {
			return basename( $folder );
		}, $folders );

		array_walk( self::$engines, array( $this, 'init_engine' ) );
	}

	protected function init_engine( $engine ) {
		$class = sprintf( '\engines\%s\%s', $engine, $engine );

		if ( of_get_option( $engine ) ) {
			$class::init();
			$func=array( $class,'of_option' );
			add_filter( 'of_options', $func, 10 );
		}
	}

	static public function options(){
		$options=[];

		foreach(self::$engines as $engine){
			$formatted=apply_filters("{$engine}_name_format",ucfirst($engine));
			$options[] = array(
				'name' => __( sprintf('%s Integration',$formatted), 'avraham' ),
				'id'   => $engine,
				'std'  => '1',
				'type' => 'checkbox'
			);
		}

		return $options;
	}
}