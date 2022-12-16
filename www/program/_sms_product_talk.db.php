<?php
/*
	포인트 & 휴면 & 자동정산 처리 :: 하루 한번 실행
	/program/_auto_load.php 에서 1일 1회 실행
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


// db항목체크
$arr = array(
	// 2019-04-09 SSJ :: 상품후기 접수 시
	"product_review"				=> "[{사이트명}] 고객님 상품후기가 접수되었습니다. [상품: {후기(문의)상품명}]"
	,"admin_product_review"			=> "[{사이트명}] {회원명} 님께서 상품후기를 등록하셨습니다. [상품: {후기(문의)상품명}]"
	// 2019-04-09 SSJ :: 상품문의 접수 시
	,"product_talk"				=> "[{사이트명}] 고객님 상품문의가 접수되었습니다. 빠른 시간내에 문의에 대한 답변을 드리겠습니다. 감사합니다. [상품: {후기(문의)상품명}]"
	,"admin_product_talk"			=> "[{사이트명}] {회원명} 님께서 상품문의를 접수하셨습니다. 관리자페이지에서 답변을 등록해 주십시오. [상품: {후기(문의)상품명}]"
);
$res = _MQ_assoc(" select ss_uid from smart_sms_set where ss_uid in ('". implode("','" , array_keys($arr)) ."') ");
$sms_set = array();
if(count($res) > 0){
	foreach($res as $k=>$v){
		$sms_set[$v['ss_uid']] = 'Y';
	}
}

$app_cnt = 0;
foreach($arr as $k=>$v){
	if($sms_set[$k] <> 'Y'){
		$app_cnt++;
		$_status = strpos($k, 'admin') !== false ? 'Y' : 'N';
		_MQ_noreturn(" insert into smart_sms_set set ss_uid = '". $k ."' ,ss_status = '". $_status ."' ,ss_text = '". $v ."' ,kakao_status = 'N' ");
	}
}

if($app_cnt > 0){
	error_loc_msg('/', 'DB가 수정되었습니다.');
}else{
	error_loc_msg('/', '이미 실행된 파일입니다.');
}