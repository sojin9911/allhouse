<?php
// SSJ : 토스페이먼츠 PG 모듈 추가 : 2021-02-22
include_once(dirname(__FILE__).'/inc.php');

// SSJ : 토스페이먼츠 : 2021-02-15 : 결제 실패 시 오류 메세지 출력
$msg = $message ? $message : '결제도중 오류가 발생하였습니다.';
error_loc_msg("/?pn=shop.order.result" , $msg);