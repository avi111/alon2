<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 19/12/2017
 * Time: 20:53
 */

namespace addons;


class security_and_performance {

	/**
	 * security_and_performance constructor.
	 */
	public function __construct() {
		$this->enable();
		add_filter( 'method_display_name', function ( $input ) {
			$output = $input;
			$output = ucfirst( $output );
			$output = str_replace( '_', ' ', $output );

			return $output;
		} );

		add_filter( 'of_options', array( $this, 'of_options' ), 2 );
	}

	public function of_options( $options ) {
		$options[] = array(
			'name' => __( 'Security And Performance', 'avraham' ),
			'type' => 'heading'
		);

		$options = apply_filters( 'security_and_performance_options', $options );

		return $options;
	}

	protected function enable() {
		$methods = $this->get_functions();
		foreach ( $methods as $method ) {
			$this->handle_method( $method );
		}
	}

	protected function get_functions() {
		$functions = get_class_methods( static::class );
		$functions = array_filter( $functions, function ( $function ) {
			if ( ! in_array( $function, array(
				'__construct',
				'of_options',
				'enable',
				'get_functions',
				'handle_method',
				'sdt_remove_ver_css_js'
			) ) ) {
				return $function;
			}
		} );

		return $functions;
	}

	protected function handle_method( $method ) {
		add_filter( 'security_and_performance_options', function ( $options ) use ( $method ) {
			$options[] = array(
				'name' => apply_filters( 'method_display_name', $method ),
				'id'   => 'security_and_performance_' . $method,
				'std'  => '1',
				'type' => 'checkbox'
			);

			return $options;
		} );

		if ( of_get_option( 'security_and_performance_' . $method, true ) ) {
			$this->$method();
		}
	}

	protected function remove_wp_underscore_playlist_templates_from_footer() {
		remove_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
	}

	protected function turn_off_oEmbed_auto_discovery() {
		// Turn off oEmbed auto discovery.
		add_filter( 'embed_oembed_discover', '__return_false' );
	}

	protected function dont_filter_oembed_results() {
		// Don't filter oEmbed results.
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
	}

	protected function remove_oEmbed_discovery_links() {
		// Remove oEmbed discovery links.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	}

	protected function remove_oembed_specific_js_from_both_ends() {
		// Remove oEmbed-specific JavaScript from the front-end and back-end.
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}

	protected function remove_print_emoji_styles() {
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}

	protected function remove_emoji_detection_scripts() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_action( 'init', 'smilies_init', 5 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	protected function remove_wp_generator() {
		remove_action( 'wp_head', 'wp_generator' );
	}

	protected function remove_wc_generator_tag() {
		remove_action( 'wp_head', 'wc_generator_tag' );
	}

	protected function remove_rsd_link() {
		remove_action( 'wp_head', 'rsd_link' );
	}

	protected function remove_wlwmanifest_link() {
		remove_action( 'wp_head', 'wlwmanifest_link' );
	}

	protected function remove_feed_links() {
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}

	protected function remove_wp_shortlink_wp_head() {
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	}

	protected function remove_rest_output_link_wp_head() {
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	}

	protected function disable_rest_api_link_in_http_headers() {
		remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
	}

	protected function remove_recent_comments_style() {
		add_action( 'widgets_init', function () {
			global $wp_widget_factory;
			remove_action( 'wp_head', array(
				$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
				'recent_comments_style'
			) );
		} );
	}

	protected function remove_xmlrpc() {
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}

	protected function remove_scripts_and_styles_versions() {
		add_filter( 'style_loader_src', array( $this, 'sdt_remove_ver_css_js' ), 9999 );
		add_filter( 'script_loader_src', array( $this, 'sdt_remove_ver_css_js' ), 9999 );
	}

	protected function disable_rest() {
		add_filter( 'rest_authentication_errors', function ( $access ) {
			$error = new \WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'disable-json-api' ), array( 'status' => rest_authorization_required_code() ) );

			// $endpoints = availables free access endpoints
			if ( isset( $_SERVER['REQUEST_URI'] ) && count( $endpoints = apply_filters( 'available_rest_api_endpoints', array() ) ) ) {
				preg_match_all( '/^.+\/(.+)\?.*$/', $_SERVER['REQUEST_URI'], $matches );
				if ( isset( $matches[1] ) ) {
					$endpoint = array_pop($matches[1]);
					if ( in_array( $endpoint, $endpoints ) ) {
						return $access;
					}
				}
			}


			if ( ! is_user_logged_in() ) {
				return $error;
			} else {
				$user = wp_get_current_user();
				$role = $user->roles[0];
				// currently only admin has a free access
				if ( ! in_array( $role, apply_filters( 'rest_enabled_roles', array( 'administrator' ) ) ) ) {
					return $error;
				}
			}

			return $access;
		} );
	}

	public function sdt_remove_ver_css_js( $src ) {
		if ( strpos( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		if ( strpos( $src, 'jcr=' ) ) {
			$src = remove_query_arg( 'jcr', $src );
		}

		return $src;
	}
}