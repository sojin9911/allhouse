<?php
/*
* LCY : 2021-07-04 : 신용카드 간편결제 추가
* -- http://{도메인}/program/_db.easypay.db.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// smart_setup 필드추가 추가
if(!IsField('smart_setup', 's_pg_paymethod_easypay')){
	_MQ_noreturn("
		ALTER TABLE `smart_setup` ADD `s_pg_paymethod_easypay` SET( 'easypay_kakaopay', 'easypay_naverpay' ) NULL COMMENT '신용카드 간편결제 수단(easypay_kakaopay:카카오페이,easypay_naverpay:네이버페이)';
	");
	echo '<hr>smart_setup에 필드 추가완료</hr>';
}else{
	echo '<hr>smart_setup에 필드 이미추가완료</hr>';
}


// smart_order 필드 추가
if(!IsField('smart_order', 'o_easypay_paymethod_type')){
	_MQ_noreturn(" ALTER TABLE `smart_order` ADD `o_easypay_paymethod_type` VARCHAR( 30 ) NULL COMMENT '신용카드 간편결제 수단 (var.php에 정의 - arr_available_easypay_pg )';");
	_MQ_noreturn(" ALTER TABLE `smart_order` ADD INDEX ( `o_easypay_paymethod_type` ); ");


	echo '<hr>smart_order에 필드 추가완료</hr>';
}else{
	echo '<hr>smart_order에 필드 이미추가완료</hr>';
}