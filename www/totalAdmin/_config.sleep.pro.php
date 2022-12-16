<?php # LCY :: 2017-12-09 휴면 회원 정책 저장프로세서 
	include_once('inc.php');
	if( rm_str($member_sleep_period) < 12){ error_msg("휴면계정전환 개월 수는 최소 12개월 이상 설정하셔야합니다."); } 
	_MQ_noreturn("update smart_setup set member_return_type = '".$member_return_type."' , member_sleep_period = '".$member_sleep_period."', member_return_groupinit = '".$member_return_groupinit."'where s_uid = 1 " );
	error_loc("_config.sleep.php");
?>