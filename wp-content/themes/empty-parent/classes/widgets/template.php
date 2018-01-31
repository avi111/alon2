<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/09/2017
 * Time: 14:43
 */

namespace widgets;


class template extends \orm_templates {
	protected $widgets;
	protected $new_link;
	protected $all_widgets;

	protected function get_instance( $id ) {
		global $wpdb;
		$query   = "SELECT					
					`{$wpdb->prefix}templates`.`template_name`,
					`{$wpdb->prefix}templates`.`created`,
					`{$wpdb->prefix}templates`.`id`
					FROM `{$wpdb->prefix}templates`					 
					WHERE `{$wpdb->prefix}templates`.`id`=%s";
		$prepare = $wpdb->prepare( $query, $id );
		$results = $wpdb->get_results( $prepare );

		if ( ! $results ) {
			$this->empty = true;
		} else {
			$this->id     = $id;
			$widgets      = array_combine( array_column( $results, 'widget_name' ), array_column( $results, 'atts' ) );
			$widget_boxes = array_column( $results, 'widget_box' );

			$widgets      = count( $widgets ) ? $widgets : array();
			$widget_boxes = implode( '', array_values( $widget_boxes ) ) ? $widget_boxes : array();

			$this->created       = $this->template_property( $results, 'created' );
			$this->template_name = $this->template_property( $results, 'template_name' );

			$this->new_link = admin_url( sprintf( "admin.php?page=templates.php&id=new" ) );

			global $wp_registered_widgets;
			$this->all_widgets = array_map( function ( $widget ) {
				return array(
					'name' => $widget['name'],
					'id'   => $widget['id'],
				);
			}, $wp_registered_widgets );

			foreach ( array_keys( $widgets ) as $key => $widget ) {
				if ( isset( $widget_boxes[ $key ] ) && $widget_boxes[ $key ] > 0 ) {
					$this->widgets[ $widget_boxes[ $key ] - 1 ][] = array(
						'name' => $widget,
						'atts' => $widgets[ $widget ]
					);
				}
			}
		}
	}

	public function getName() {
		return ! $this->template_name ? $this->id : $this->template_name;
	}

	protected function template_property( $array, $property ) {
		$return = array_column( $array, $property );
		$return = array_values( $return );
		$return = array_unique( $return );

		return array_pop( $return );
	}

	/**
	 * @return mixed
	 */
	public function getWidgets() {
		return $this->widgets;
	}

	/**
	 * @return mixed
	 */
	public function getWidgetBoxes() {
		return $this->widget_boxes;
	}

	public function sortBoxes() {
		$unique = array_unique( $this->widget_boxes );
		asort( $unique );

		return $unique;
	}


}