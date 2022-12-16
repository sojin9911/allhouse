<?php
/*
* SSJ : 현금영수증 필수발행 패치 : 2021-02-01
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


if(!isset($siteInfo['s_force_cashbill_use'])){

	$query = "
		ALTER TABLE smart_setup
		  ADD `s_force_cashbill_use` enum('Y','N') NOT NULL default 'N' COMMENT '현금영수증 필수 발행 사용여부',
		  ADD `s_force_cashbill_price` int(11) NOT NULL COMMENT '현금영수증 필수 발행 제한금액';
	";
	_MQ_noreturn($query);
	error_loc_msg("/", "정상적으로 DB가 추가되었습니다.");
}else{
	error_loc_msg("/", "이미 추가된 항목입니다.");
}