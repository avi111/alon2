<?php

class homeCard_widget extends \widgets\generic_widgets {

	function __construct() {
		parent::__construct();
	}

	public function form( $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$content = ! empty( $instance['content'] ) ? $instance['content'] : '';
		$pagenum = ! empty( $instance['page'] ) ? $instance['page'] : '';

		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'title:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php _e( esc_attr( 'content:' ) ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"
                      name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>"><?php echo esc_attr( $content ); ?></textarea>
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'page' ) ); ?>"><?php _e( esc_attr( 'page:' ) ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'page' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'page' ) ); ?>">
                <?php
                $pages = get_pages();
                foreach($pages as $page){
                    $id=$page->ID;
                    $title=$page->post_title;
                    $current = $id==$pagenum ? ' selected': '';
                    echo sprintf('<option value="%s"%s>%s</option>',$id,$current,$title);
                }
                ?>
            </select>
        </p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['page'] = ( ! empty( $new_instance['page'] ) ) ? strip_tags( $new_instance['page'] ) : '';
		$instance['content'] = ( ! empty( $new_instance['content'] ) ) ? strip_tags( $new_instance['content'] ) : '';

		return $instance;
	}

}

add_shortcode( "homeCard", "homeCard_component" );

function homeCard_component( $atts = array() ) {
	$output = apply_filters( "avraham_cache", "homeCard", $atts );
	if ( ! $output ) {
		ob_start();
		$widget = new \components\homeCard( $atts );
		$widget->show();
		$output = ob_get_clean();
	}

	return $output;
}