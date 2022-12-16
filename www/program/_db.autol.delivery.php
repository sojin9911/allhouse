<?php
/*
* SSJ : 자동 배송완료 패치 : 2021-02-01
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


if(!isset($siteInfo['s_delivery_auto'])){

	$query = "
		ALTER TABLE smart_setup
		  ADD `s_delivery_auto` int(4) NOT NULL COMMENT '자동 배송완료 처리 설정';
	";
	_MQ_noreturn($query);
	error_loc_msg("/", "정상적으로 DB가 추가되었습니다.");
}else{
	error_loc_msg("/", "이미 추가된 항목입니다.");
}