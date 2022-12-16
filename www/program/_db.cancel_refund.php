<?php
// KAY :: 2021-07-14 :: 에디터 이미지 관리
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

$table = "smart_sms_set";

$sms_chk = _MQ("select count(*) as cnt from smart_sms_set where ss_uid='cancel_part_request' ");

if($sms_chk['cnt']=='0'){
	_MQ_noreturn("
		INSERT INTO `smart_sms_set` (
			`ss_uid` ,
			`ss_status` ,
			`ss_text`
		)
		VALUES (
			'cancel_part_request' ,  'Y',  '[{사이트명}] 주문하신 상품 중 일부 상품의 주문취소요청이 정상적으로 접수 되었습니다. [주문번호: {주문번호}] [취소요청된상품: {주문상품명}]'
		);
	");	
	viewarr("데이터가 추가되었습니다.");
}else{
	viewarr("이미 추가된 데이터입니다.");
}

$sms_admin_chk = _MQ("select count(*) as cnt from smart_sms_set where ss_uid='admin_cancel_part_request' ");
if($sms_chk['cnt']=='0'){

	_MQ_noreturn("
		INSERT INTO `smart_sms_set` (
			`ss_uid` ,
			`ss_status` ,
			`ss_text`
		)
		VALUES (
			'admin_cancel_part_request' ,  'Y',  '[{사이트명}] 주문하신 상품 중 일부 상품의 주문취소요청이 접수되었습니다. [주문번호: {주문번호}] [취소요청된상품: {주문상품명}]'
		);
	");
	
	viewarr("데이터가 추가되었습니다.");	
}else{
	viewarr("이미 추가된 데이터입니다.");
}