<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/10/2017
 * Time: 21:18
 */

namespace widgets;


class buttons {
	protected $name;

	/**
	 * buttons constructor.
	 *
	 * @param $name
	 */
	public function __construct() {
		$this->name = 'widgets';
		add_action( 'admin_init', array( $this, 'tinymce_button' ) );
	}

	public function tinymce_button() {
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'register' ) );
			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
			add_action( 'admin_footer', array( $this, 'add_tinymce_button' ) );
		}
	}

	public function mce_external_plugins( $plugin_array ) {
		$plugin_array[ $this->name ] = get_template_directory_uri() . '/views/js/widgets/tinymce.js';

		return $plugin_array;
	}

	public function register( $buttons ) {
		global $classname;
		foreach($classname as $name) {
			array_push( $buttons, $name );
		}

		return $buttons;
	}

	public function add_tinymce_button() {
		?>
        <script>
            var widgets = [<?php
				global $classname;
				$widgets = array_map( function ( $class ) {
					return "'" . $class . "'";
				}, $classname );
				echo implode( ',', $widgets );
				?>];
        </script>
		<?php
	}


}