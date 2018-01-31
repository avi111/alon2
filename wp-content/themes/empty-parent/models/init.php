<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26/08/2017
 * Time: 23:35
 */

foreach (
	array(
		'files',
		'interfaces',
		'config',
		'middleware',
		'test'
	) as $file
) {
	locate_template( "models/$file.php", true, true );
}
new \parent_theme\init();