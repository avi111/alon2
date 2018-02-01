<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26/08/2017
 * Time: 23:05
 */

function avraham_autoload( $class_name ) {
	$class_name = str_replace( '/', '\\', $class_name );
	$filepath   = '/' . implode( '/', explode( '\\', $class_name ) );
	$path       = 'classes' . $filepath . '.php';
	$locate     = locate_template( $path, true, true );
}

if ( ! function_exists( 'require_dir' ) ) {
	function require_dir( $dir, $path = MODEL ) {
		if ( file_exists( $dir ) ) {
			$files = scandir( $dir );
			foreach ( $files as $file ) {
				if ( ! ( $file == '.' || $file == '..' ) && substr( $file, - 4 ) === '.php' ) {
					locate_template( $path . $file, true, true );
				}
			}
		}
	}
}

function get_orm_map() {
	return \orm\map::get_instance();
}

spl_autoload_register( 'avraham_autoload' );

function include_view( $extension, $folder, $file ) {
	$template_name = "/views/$extension/$folder/$file.$extension";
	if ( file_exists( STYLESHEETPATH . '/' . $template_name ) ) {
		$located = STYLESHEETPATH . '/' . $template_name;
	} elseif ( file_exists( TEMPLATEPATH . '/' . $template_name ) ) {
		$located = TEMPLATEPATH . '/' . $template_name;
	} else {
		$located='';
	}
	if ( file_exists( $located ) ) {
		$content = file_get_contents( $located );
		switch($extension){
			case 'html':
				$before='';
				$after='';
				break;
			case 'js':
				$before='<script>';
				$after='</script>';
				break;
			case 'css':
				$before='<style>';
				$after='</style>';
				break;
		}
		echo $before.$content.$after;
	}
}

function include_full_view($folder, $file){
	foreach(array('html','js','css') as $extension){
		include_view($extension, $folder, $file);
	}
}

function js_vars($array){
	echo '<script>';
	foreach($array as $key=>$element){
		if(is_array($element)){
			$element=json_encode($element);
			echo "var $key=JSON.parse('$element');";
		} else {
			echo "var $key='$element';";
		}
	}
	echo '</script>';
}