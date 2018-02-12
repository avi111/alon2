<?php
function constructor($id=0){
	$this->empty=false;
	if(!$id){
		return;
	}
	$this->get_instance($id);
}

function get_instance($id){
	global $wpdb;
	$query="SELECT * FROM $table WHERE $primary=%s";
	$prepare=$wpdb->prepare($query,$id);
	$db=\wpdb\wpdb::get();
	$results=$db->get_results($prepare);
	$results=array_pop($results);
	if(!$results){
		$this->empty=true;
	} else {
		$assignments;
	}
}