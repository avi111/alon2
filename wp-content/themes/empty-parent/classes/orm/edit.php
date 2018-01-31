<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05/09/2017
 * Time: 23:07
 */

namespace orm;

class edit {
	protected $id;
	protected $class;
	protected $table;
	protected $instance;
	protected $columns;
	protected $primary;
	protected $types;
	protected $empty;

	/**
	 * edit constructor.
	 *
	 * @param $id
	 */
	public function __construct( $list_table ) {
		if ( $list_table instanceof \orm\single_list_table ) {
			$this->init( $list_table );
		} else {
			return;
		}
	}

	protected function init( $list_table ) {
		if ( ! isset( $_GET['id'] ) ) {
			return;
		}
		$this->id = $_GET['id'];

		global $wpdb;
		$this->table    = $list_table->getTable();
		$class          = $this->class = str_replace( $wpdb->prefix, 'orm_', $this->table );
		$item           = ( new $class( $this->id ) );
		$this->instance = $item->reveal();

		if ( $item->is_empty() && $this->id!='new' ) {
			$this->empty = true;

			return;
		}

		$map           = \orm\map::get_instance()->getData();
		$data          = $map[ $list_table->getTable() ];
		$this->columns = array_column( $data, 'Field' );
		$this->primary = array_search( 'PRI', array_column( $data, 'Key' ) );
		$this->types   = array_column( $data, 'Type' );
	}

	protected function get_template( $template ) {
		$current_dir = dirname( __FILE__ );
		$filename    = "$current_dir/templates/$template.php";
		if ( file_exists( $filename ) ) {
			return file_get_contents( $filename );
		} else {
			throw new \Exception( 'file is missing' );
		}
	}

	/**
	 * @return mixed
	 */
	public function is_empty() {
		return $this->empty;
	}

	public function view() {
		$fields = array();
		if ( ! count( $this->columns ) ) {
			$this->columns = array();
		}

		foreach ( $this->columns as $key => $value ) {
			$type = str_replace( array(
				0,
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9,
				'(',
				')'
			), array_fill( 0, 11, '' ), $this->types[ $key ] );

			if ( strpos( $type, 'int' ) !== false ) {
				$type = 'int';
			}

			if ( strpos( $type, 'float' ) !== false ) {
				$type = 'float';
			}

			if ( strpos( $type, 'text' ) !== false || strpos( $type, 'varchar' ) !== false ) {
				$type = 'text';
				if ( apply_filters( 'orm_edit_editor', false, $this->table, $value ) ) {
					$type = 'editor';
				}
			}

			if ( $this->columns[ $this->primary ] == $value ) {
				$edit_field=apply_filters( 'edit_primary_key', false, $this->table );
				if ( !$edit_field ) {
					$fields[ $value ] = $this->primary_field( $value );
				}
			}

			if ( $edit_field ) {
				switch ( $type ) {
					case 'editor':
						$fields[ $value ] = $this->editor( $value, true );
						break;
					case 'text':
						if ( strpos( $value, 'title' ) !== false ) {
							$fields[ $value ] = $this->title( $value );
						} else {
							$fields[ $value ] = $this->editor( $value );
						}
						break;
					case 'int':
						$fields[ $value ] = $this->number( $value, 'int' );
						break;
					case 'float':
						$fields[ $value ] = $this->number( $value, 'float' );
						break;
					case 'timestamp':
					case 'datetime':
						$fields[ $value ] = $this->timestamp( $value );
						break;
					default:
						break;
				}
			}
		}

		echo $this->content( $fields );

	}

	protected function content( $fields ) {
		$content = $this->get_template( 'edit' );
		$url     = admin_url( sprintf( "admin.php?page=%s.php&id=new", $this->table ) );

		$content = str_replace( '#url', $url, $content );
		$content = str_replace( 'Edit class', sprintf( 'Edit %s', str_replace( '_', ' ', apply_filters( "{$this->class}_name", $this->class ) ) ), $content );
		$content = str_replace( 'fields_placeholder', implode( '', $fields ), $content );
		$content = str_replace( 'value="action"', 'value="update"', $content );

		return $content;
	}

	protected function wp_editor( $content, $field ) {
		wp_editor( $content, $field, array(
			'_content_editor_dfw' => "{$this->class}_editor_{$field}'",
			'drag_drop_upload'    => true,
			'tabfocus_elements'   => 'content-html,save-post',
			'editor_height'       => 300,
			'tinymce'             => array(
				'resize'             => false,
				'wp_autoresize_on'   => true,
				'add_unload_trigger' => false,
			),
		) );
	}

	protected function editor( $field, $wp_editor = false ) {
		$content = $this->instance[ $field ];
		ob_start();
		echo sprintf( '<p for="%s">Enter %s here</p>', $field, apply_filters( "{$this->class}_field_$field", "$field" ) );
		if ( $wp_editor ) {
			$this->wp_editor( $content, $field );
		} else {
			echo sprintf( '<textarea id="%s" name="%s" rows="6" style="width:100%%;">%s</textarea>', $field, $field, $content );
		}

		return ob_get_clean();
	}

	protected function title( $field ) {
		$value = $this->instance[ $field ];
		$title = $this->get_template( 'title' );
		$title = str_replace( 'id_id', $field, $title );
		$title = str_replace( 'value_value', $value, $title );
		$title = str_replace( 'filtered', apply_filters( "{$this->class}_field_$field", $field ), $title );

		return $title;
	}

	protected function number( $field, $type ) {
		$value   = $this->instance[ $field ];
		$number  = $this->get_template( 'number' );
		$number  = str_replace( 'id_id', $field, $number );
		$number  = str_replace( 'value_value', $value, $number );
		$step    = apply_filters( "{$this->class}_step_$field", 0.1 );
		$pattern = $type == 'float' ? sprintf( 'pattern="%s" step="%s" ', '[0-9]+([\.,][0-9]+)?', $step ) : '';
		$number  = str_replace( '{pattern}', $pattern, $number );
		$number  = str_replace( 'filtered', apply_filters( "{$this->class}_field_$field", $field ), $number );
		$number  = str_replace( 'input_title', apply_filters( "{$this->class}_input_title_$field", "This should be a number with up to $step decimal places." ), $number );

		return $number;
	}

	protected function timestamp( $field ) {
		$input = $this->title( $field );

		return $input;
	}

	protected function primary_field( $field ) {
		$value = $this->instance[ $field ];
		$input = sprintf( '<input type="hidden" name="%s" value="%s" disabled>', $field, $value );

		return $input;
	}

	public function update() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return false;
		}

		$class = $this->class;

		switch ( $_REQUEST['action'] ) {
			case 'update':
				$class::update( $this->id, $this->prepare() );
				$this->instance = ( new $class( $this->id ) )->reveal();
				$this->view();
				break;
			case 'save':
				$vars = $_POST;
				unset( $vars['action'] );
				unset( $vars['page'] );
				unset( $vars[ $this->primary ] );

				global $wpdb;
				if ( ! implode( '', $vars ) ) {
					echo sprintf( "no {$this->table} item was created since you entered empty fields" );

					return false;
				} else {
					$id = $class::create( $vars );
					if ( $id ) {
						echo sprintf(
							"new {$this->table} item was created with id {$wpdb->insert_id}. <a href='%s'>edit it</a>, or <a href='%s'>go to the items list</a>",
							admin_url( "admin.php?page={$this->table}.php&id={$wpdb->insert_id}" ),
							admin_url( "admin.php?page={$this->table}.php" ) );
					} else {
						echo sprintf( "failed to create new {$this->table} item due to an error: {$wpdb->last_error}" );

						return false;
					}
				}
				break;
		}

		return true;
	}

	protected function prepare() {
		$array = array();
		foreach ( $this->columns as $column ) {
			if ( isset( $_REQUEST[ $column ] ) ) {
				$array[ $column ] = $_REQUEST[ $column ];
			}
		}

		return $array;
	}
}