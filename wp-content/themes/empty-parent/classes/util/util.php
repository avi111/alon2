<?php

namespace util;

class util {
	public static function __callStatic( $name, $arguments=array() ) {
		$method = new ReflectionMethod(static::class, $name);
		if(count($method->getParameters())){
		    self::$name($arguments);
        } else {
		    self::$name();
        }
	}


	public static function current_url() {
		$url = "http" . ( ( $_SERVER['SERVER_PORT'] == 443 ) ? "s://" : "://" ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		return $url;
	}

	public static function startsWith( $haystack, $needle, $case = true ) {
		if ( $case ) {
			return strpos( $haystack, $needle, 0 ) === 0;
		}

		return stripos( $haystack, $needle, 0 ) === 0;
	}

	public static function endsWith( $haystack, $needle, $case = true ) {
		$expectedPosition = strlen( $haystack ) - strlen( $needle );

		if ( $case ) {
			return strrpos( $haystack, $needle, 0 ) === $expectedPosition;
		}

		return strripos( $haystack, $needle, 0 ) === $expectedPosition;
	}

	public static function get_id_by_src($image_src){
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='%s'";
		$id = $wpdb->get_var($wpdb->prepare($query,$image_src));
		return $id;
    }

	public static function get_page_by_slug( $page_slug, $output = OBJECT, $post_type = 'page' ) {
		global $wpdb;
		$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $page_slug, $post_type ) );
		if ( $page ) {
			return get_post( $page, $output );
		}

		return null;
	}

	public static function getPermalink() {
		global $actual_site;
		$url = explode( $actual_site, "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" )[1];
		$url = explode( '?', $url );

		return $url[0];
	}

	public static function isPage( $page ) {
		return self::getPermalink() == '/' . $page . '/';
	}

	public static function recentPosts( $what = 0 ) {
		$query = $GLOBALS['wp_query'];
		echo '<ul class="recent-posts' . ( $what == 'titles' ? ' titles-only' : '' ) . ( $what == 'images' ? ' images-only' : '' ) . '">';

		foreach ( $query->posts as $onePost ) {
			$post = post::createByPost( $onePost );
			?>
            <li>
                <h1><?php echo $post->title(); ?></h1>
				<?php
				if ( $what == 'titles' ) {
					?>
                    <div><?php echo $post->content(); ?></div>
					<?php
				}

				if ( $what == 'images' ) {
					?>
                    <div><?php echo $post->showImage(); ?></div>
					<?php
				}
				?>
            </li>
			<?php
		}
		echo '</ul>';
	}

	public static function menu_name( $menu ) {
		$menu_name = $menu . '-he';
		if ( isset( $_GET['l'] ) ) {
			$l = $_GET['l'];
			if ( $l == 'en' ) {
				$menu_name = $menu . '-en';
			}
		}

		return $menu_name;
	}

	public static function get_menu( $menu_name ) {
	    global $post;
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			$menu_list = '<ul id="menu-' . $menu_name . '">';

			foreach ( (array) $menu_items as $key => $menu_item ) {
				$title     = $menu_item->title;
				$url       = $menu_item->url;
				$current   = $url == get_permalink( $post->ID );
				$menu_list .= '<li><a href="' . $url . '" class="special' . ( $current ? ' act' : '' ) . '">' . $title . '</a></li>';
			}
			$menu_list .= '</ul>';
		} else {
			$menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
		}

		return $menu_list;
	}

	public static function getMenu( $menu ) {
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu ] ) ) {
			$menu       = wp_get_nav_menu_object( $locations[ $menu ] );
			$menu_items = wp_get_nav_menu_items( $menu->term_id );
			if ( ! is_array( $menu_items ) ) {
				$menu_items = array();
			}

			return $menu_items;
		}
		return false;
	}

	public static function getImage( $img ) {
		return get_stylesheet_directory_uri() . VIEWS_GLOBALS . 'images/' . $img;
	}

	public static function showImage( $src, $link = '', $title = '', $alt = '', $width = 0, $height = 0 ) {
		if ( $link && is_numeric( $link ) ) {
			$width  = $link;
			$link   = '';
			$alt    = '';
			$title  = '';
			$height = 'auto';
		}

		echo $link ? '<a href="' . $link . '">' : '';
		echo '<img src="' . $src . '"  title="' . $title . '" alt="' . $alt . '"';
		if ( $width && ! $height ) {
			echo $width ? ' width="' . $width . '"' : ' ';
			echo $height ? ' height="auto" ' : '';
		}
		if ( $width && $height ) {
			echo $width ? ' width="' . $width . '"' : '" ';
			echo $height ? ' height="' . $height . '"' : '" ';
		}
		echo '>';
		echo $link ? '</a>' : '';
	}

	public static function cast( $destination, $sourceObject ) {
		if ( is_string( $destination ) ) {
			$destination = new $destination();
		}
		$sourceReflection      = new ReflectionObject( $sourceObject );
		$destinationReflection = new ReflectionObject( $destination );
		$sourceProperties      = $sourceReflection->getProperties();
		foreach ( $sourceProperties as $sourceProperty ) {
			$sourceProperty->setAccessible( true );
			$name  = $sourceProperty->getName();
			$value = $sourceProperty->getValue( $sourceObject );
			if ( $destinationReflection->hasProperty( $name ) ) {
				$propDest = $destinationReflection->getProperty( $name );
				$propDest->setAccessible( true );
				$propDest->setValue( $destination, $value );
			} else {
				$destination->$name = $value;
			}
		}

		return $destination;
	}

	static public function getComponentTemplate( $component, $template, $element, $referer ) {
		global $pass;
		$pass = array( $element, $referer );
		get_template_part( CONTROLLERS_COMPONENTS . $component . '/' . $template );
	}

	static public function google_map( $input ) {
		if ( $input ) {
			$width  = explode( "width", $input );
			$height = explode( "frameborder", $width[1] );
			$output = $width[0] . 'width="100%" height="263" frameborder' . $height[1];
		}

		return $output;
	}

	static public function flatten( array $array ) {
		$return = array();
		array_walk_recursive( $array, function ( $a ) use ( &$return ) {
			$return[] = $a;
		} );

		return $return;
	}

	static public function modal( $modal, $close = 'close', $class ) {
		?>
        <div class="modal fade" id="<?php echo $modal; ?>" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
					<?php get_template_part( CONTROLLERS . 'popups/' . $modal ); ?>
                </div>
            </div>
        </div>
		<?php
	}

	static public function toCamelCase($str, $capitaliseFirstChar = false)
	{
		if ($capitaliseFirstChar) {
			$str[0] = strtoupper($str[0]);
		}
		return preg_replace('/_([a-z])/e', "strtoupper('\\1')", $str);
	}

	static public function kebab_case_from_camel_case($input) {
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}
}