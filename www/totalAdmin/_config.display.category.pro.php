<?php 
	include_once('inc.php');

	/*
		# DB :: smart_setup 
		- s_search_display
	*/

	$s_query = "
		s_category_display = '".$s_category_display."'
		,s_category_display_mobile = '".$s_category_display_mobile."'
	";

	_MQ_noreturn(" update smart_setup set  ".$s_query." where s_uid = '1' ");
	error_loc("_config.display.category.php");
?>