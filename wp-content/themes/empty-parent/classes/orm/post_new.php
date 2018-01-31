<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06/09/2017
 * Time: 00:24
 */

namespace orm;


class post_new extends edit{
	protected function primary_field( $field ) {
		return;
	}

	protected function content($fields){
		$content = $this->get_template( 'edit' );
		$content = str_replace( '<a href="#url" class="page-title-action">Add New</a>', '', $content );
		$content = str_replace( 'Edit class', sprintf('Add %s',str_replace('_',' ',apply_filters( "{$this->class}_name", $this->class ))), $content );
		$content = str_replace( 'fields_placeholder', implode( '', $fields ), $content );
		$content = str_replace( 'value="Update"', 'value="Save"', $content );
		$content = str_replace( 'value="action"', 'value="save"', $content );
		return $content;
	}

	protected function timestamp( $field ) {
		// TODO
	}
}