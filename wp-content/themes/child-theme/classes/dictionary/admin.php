<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 11/02/2018
 * Time: 20:17
 */

namespace dictionary;

use \wpdb\wpdb as wpdb;

class admin {

	protected $dictionary;

	/**
	 * admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'dictionary_page' ) );
		add_action( 'wp_ajax_delete_dictionary_record', array( $this, 'delete_dictionary_record' ) );
		add_action( 'init', function () {
			$this->dictionary = new dictionary();
		} );
	}

	public function delete_dictionary_record() {
		$key = $_POST['key'] ?? null;
		if ( $key ) {
			$handler = new key_handler( $key );
			$delete  = \orm_dictionary_keys::delete( $handler->getId() );
			exit();
		}
	}

	public function dictionary_page() {
		add_menu_page(
			'Dictionary',
			'Dictionary',
			'manage_options',
			'dictionary',
			array( $this, 'output' ),
			'dashicons-book',
			6
		);
	}

	public function output() {
		if ( ! empty( $_POST ) && check_admin_referer( 'dictionary_edit' ) ) {
			$data  = $_POST;
			$table = $this->dictionary->getTable();
			unset( $data['_wpnonce'] );
			unset( $data['_wp_http_referer'] );

			foreach ( $data as $key => $languages ) {
				foreach ( $languages as $language => $value ) {
					if ( $value == $table[ $key ][ $language ] ) {
						unset( $data[ $key ][ $language ] );
					}
				}
			}

			global $wpdb;
			$db = wpdb::get();

			foreach ( $data as $key => $languages ) {
				foreach ( $languages as $language => $value ) {
					if ( ! is_null( $table[ $key ][ $language ] ) ) {
						if ( ! $language ) {
							$sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}dictionary_keys SET value=%s WHERE dictionary_key=%s", $value, $key );
						} else {
							$sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}dictionary_values v INNER JOIN {$wpdb->prefix}dictionary_keys k ON v.dictionary_key=k.id SET v.value=%s WHERE k.dictionary_key=%s AND v.language=%d", $value, $key, $language );
						}
						$done = $db->query( $sql );

					} else {
						if ( $language ) {
							$sql  = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}dictionary_values SET 
                                                            language=%d,
                                                            dictionary_key=(SELECT id FROM {$wpdb->prefix}dictionary_keys WHERE dictionary_key=%s),
                                                            value=%s", $language, $key, $value );
							$done = $db->query( $sql );
						}
					}
				}
			}

			$this->dictionary = new dictionary();
			getView( 'dictionary/edit', $this );
		} else {
			getView( 'dictionary/edit', $this );
		}
	}

	public function table() {
		$table     = $this->dictionary->getTable();
		$languages = $this->dictionary->getLanguages();
		?>
        <form id="dictionary" action="<?php echo esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ); ?>" method="post">
            <table>
                <thead>
                <tr>
                    <th>Key</th>
                    <th>English</th>
					<?php
					foreach ( $languages as $language ) {
						echo sprintf( '<th>%s</th>', $language->language );
					}
					?>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach ( $table as $key => $row ) {
					?>
                    <tr>
                        <td><?php echo strlen( $key ) > 20 ? substr( $key, 0, 20 ) . '...' : $key; ?></td>
						<?php
						foreach ( $row as $index => $value ) {
							$value = htmlspecialchars( stripslashes( $value ) );
							echo sprintf( '<td><input name="%s[%d]" type="text" value="%s"></td>', $key, $index, $value );
						}
						?>
                        <td><a href="javascript:void(0)"
                               onclick="delete_dictionary_record('<?php echo $key; ?>')">Delete</a></td>
                    </tr>
					<?php
				}
				?>
                </tbody>
            </table>
            <div>
				<?php
				wp_nonce_field( 'dictionary_edit' );
				?>
                <input type="submit" value="Submit">
            </div>
        </form>
        <style>
            #dictionary table {
                table-layout: fixed;
                width: 100%;
            }

            #dictionary table td {
                overflow: hidden;
            }

            #dictionary input[type="text"] {
                width: 100%;
            }
        </style>

        <script>
            function delete_dictionary_record(key) {
                jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                    action: 'delete_dictionary_record',
                    key: key
                }, function (result) {
                    window.location.reload();
                })
            }
        </script>
		<?php
	}


}