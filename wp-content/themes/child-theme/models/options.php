<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich (http://www.levkovich.co.il)
 * Date: 30/01/2018
 * Time: 00:08
 */

add_filter( 'of_options', function($options){
	$options[] = array(
		'name' => 'Site',
		'type' => 'heading'
	);

	$options[] = array(
		'name' => 'logo',
		'id' => 'site_logo',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => 'phone',
		'id'   => 'phone',
		'class' => 'mini',
		'type' => 'text'
	);

	$options[] = array(
		'name' => 'appointments phone',
		'id'   => 'appointments_phone',
		'class' => 'mini',
		'type' => 'text'
	);

	return $options;
}, 12 );