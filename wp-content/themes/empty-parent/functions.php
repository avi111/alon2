<?php

define( 'PARENT_ROOT', TEMPLATEPATH . '/' );

if ( ! defined( 'CHILD_ROOT' ) ) {
	define( 'CHILD_ROOT', STYLESHEETPATH . '/' );
}

locate_template( 'models/init.php', true, true );