<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 결제 결과 처리는 DB처리페이지 (shop.order.result_daupay.pro.php) 에서 처리한다.
$que = "select * from smart_order where o_ordernum='". $ordernum ."'";
$r = _MQ($que);
if(  in_array($r[o_paymethod], array('card','iche','hpp')) == true ) {
	if($r[o_paystatus] == "Y") error_loc_nomsgPopup("/?pn=shop.order.complete");
	else error_loc_msgPopup("/","결제는 정상적으로 되었으나, 일시적인 오류가 발생하였습니다. 관리자에게 문의하세요.");
} else if($r[o_paymethod] == "virtual") {

	error_loc_nomsgPopup("/?pn=shop.order.complete");

}

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행