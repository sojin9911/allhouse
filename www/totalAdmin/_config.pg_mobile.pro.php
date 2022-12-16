<?php 
	include "./inc.php";

/*
	-- 휴대폰 결제
	s_pg_mobile_use	= 휴대폰 결제 사용여부
	s_pg_mobile_type	= 휴대폰 결제 모듈 (pg,other)  기본-pg, 별도의 외부 모듈 - other :: 고정값 pg
*/

	if( in_array($s_pg_mobile_use, array('Y','N')) == false){ $s_pg_mobile_use = 'N';  }

	$sque = "
		s_pg_mobile_use = '".$s_pg_mobile_use."'
	";

	_MQ_noreturn("update smart_setup set ".$sque." where s_uid = '1' ");

	error_loc("_config.pg_mobile.form.php");




