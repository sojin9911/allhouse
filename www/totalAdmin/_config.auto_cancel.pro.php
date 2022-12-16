<?php

// ----- [하이센스3.0 결제취소파일 일원화 패치] : 자동주문취소 -----
include_once('inc.php');


// update
$sque = "
	update smart_setup set
		  s_order_auto_cancel_term = '" . rm_str($_order_auto_cancel_term) . "'
	where
		s_uid = 1
	";
_MQ_noreturn($sque);


// 설정페이지 이동
error_loc('_config.auto_cancel.php');