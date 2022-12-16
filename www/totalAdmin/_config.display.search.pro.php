<?php 
	include_once('inc.php');

	/*
		# DB :: smart_setup 
		- s_search_option
		- s_search_display
		- s_search_mobile_display
		- s_search_diff_orderby
		- s_search_diff_maxcnt
		- s_search_diff_option
	*/

	// -- 콤마제거
	$s_search_diff_maxcnt = delComma($s_search_diff_maxcnt); // 다른고객이 많이 찾은상품 최대개수

	// -- 분류설정 확인 
	$s_search_option = count($s_search_option) > 0 ? implode(",",$s_search_option) : '';

	$s_query = "
			s_search_option = '".$s_search_option."'
		,	s_search_display = '".$s_search_display."'
		,	s_search_mobile_display = '".$s_search_mobile_display."'
		,	s_search_diff_orderby = '".$s_search_diff_orderby."'
		,	s_search_diff_maxcnt = '".$s_search_diff_maxcnt."'
		,	s_search_diff_option = '".$s_search_diff_option."'
	";

	_MQ_noreturn(" update smart_setup set  ".$s_query." where s_uid = '1' ");
	error_loc("_config.display.search.php");
?>