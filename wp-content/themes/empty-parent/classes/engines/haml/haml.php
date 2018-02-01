<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06/10/2017
 * Time: 19:36
 */

namespace engines\haml;

use engines\engine;

class haml extends engine {
	static public $folder;
	protected $file;
	protected $contentVariables;

	public function __construct( $file, $contentVariables = array() ) {
		$this->file             = $file;
		$this->contentVariables = $contentVariables;
	}

	static public function init() {
		\engines\haml\haml::setFolder( get_stylesheet_directory() . '/cache/' );
		\engines\haml\singleton::getInstance();
	}

	static public function of_option( $options ) {
		$engine    = 'haml';
		$formatted = apply_filters( "{$engine}_name_format", ucfirst( $engine ) );

		if ( of_get_option( $engine ) ) {
			$options[] = array(
				'name' => __( $formatted, 'avraham' ),
				'type' => 'heading'
			);
			$options[] = array(
				'name' => __( sprintf( 'Widgets %s Interface', $formatted ), 'avraham' ),
				'id'   => 'widgets_haml',
				'std'  => '1',
				'type' => 'checkbox'
			);
		}

		return $options;
	}

	static public function execute( $class, $viewbag ) {
		$return = '';
		$file   = "/views/widgets/{$class}.haml";
		if ( class_exists( '\engines\haml\haml' ) && of_get_option( 'haml' ) && file_exists( get_stylesheet_directory() . $file ) ) {
			$haml   = new static( get_stylesheet_directory() . $file, $viewbag );
			$return = $haml->parse();
		}

		return $return;
	}


	/**
	 * @return mixed
	 */
	public static function getFolder() {
		return self::$folder;
	}

	/**
	 * @param mixed $folder
	 */
	public static function setFolder( $folder ) {
		self::$folder = $folder;
	}


	public function parse() {
		$parser  = new \HamlPHP( new \FileStorage( self::$folder ) );
		$content = $parser->parseFile( $this->file );
		if(!file_exists(self::$folder)){
			wp_mkdir_p(self::$folder);
		}
		return $parser->evaluate( self::$folder, $content, $this->contentVariables );
	}

}