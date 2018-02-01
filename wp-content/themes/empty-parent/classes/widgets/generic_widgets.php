<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/09/2017
 * Time: 00:34
 */

namespace widgets;

class generic_widgets extends \WP_Widget {
	public $currentClassname;

	function __construct() {
		$classExploded          = explode( '_', get_class( $this ) );
		$this->currentClassname = $classExploded[0];

		list( $name, $text, $description ) = $this->widget_info();
		parent::__construct( $name, $text, $description );
	}

	protected function widget_info() {
		$title=preg_split(
			'/(^[^A-Z]+|[A-Z][^A-Z]+)/',
			 $this->currentClassname,
			-1, /* no limit for replacement count */
			PREG_SPLIT_NO_EMPTY /*don't return empty elements*/
			| PREG_SPLIT_DELIM_CAPTURE /*don't strip anything from output array*/
		);

		$title = array_map( function ( $part ) {
			return ucfirst( $part );
		}, $title );
		$title=implode(' ',$title);

		$array = array(
			sanitize_title( $this->currentClassname ),
			apply_filters(sanitize_title( $this->currentClassname ).'-text',$title),
			array( 'description' => apply_filters(sanitize_title( $this->currentClassname ).'-description','&nbsp;') )
		);

		return $array;
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		get_component( $this->currentClassname, $instance );
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$params = ! empty( $instance['params'] ) ? $instance['params'] : '';
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'params' ) ); ?>"><?php _e( esc_attr( 'params:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'params' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'params' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $params ); ?>">
        </p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance           = array();
		$instance['params'] = ( ! empty( $new_instance['params'] ) ) ? strip_tags( $new_instance['params'] ) : '';

		return $instance;
	}

}