<?php
/*
* SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

$desc = _MQ_assoc(" desc smart_order_onlinelog ");
if($desc[4]['Type'] == 'varchar(30)'){
	$query = " ALTER TABLE `smart_order_onlinelog` CHANGE `ool_tid` `ool_tid` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '승인번호' ";
	_MQ_noreturn($query);
	error_loc_msg("/", "정상적으로 DB가 수정되었습니다.");
}else{
	error_loc_msg("/", "이미 수정된 항목입니다.");
}