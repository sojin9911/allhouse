<?php
/*
	/program/_auto_load.php -> curl_async -> /program/_point.update.php 에서 include 1일 1회 실행
*/
# ------------------------------> DEV 변경 하세요. <---------------------- :: 결제 수단 자동화(결제 수단 추가 할때마다 수정 해줘야 하는 불편함을 가지고 있음)
# 자동정산처리
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


// 사용안함 처리시 실행하지 않도록 처리
if($siteInfo['s_product_auto_on'] == 'N') return;

actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


/*
$siteInfo['s_product_auto_C']
$siteInfo['s_product_auto_L']
$siteInfo['s_product_auto_B']
$siteInfo['s_product_auto_G']
$siteInfo['s_product_auto_V']

$siteInfo['s_product_auto_H']
$siteInfo['s_product_auto_P']
*/

//echo '배송상품<br>';
// 조건 데이터 호출 (상품)
$_queP = "
		select
			op.op_uid, op.op_oordernum, op.op_senddate , op_completedate
		from
			`smart_order_product` as op left join
			`smart_order` as o on(op.op_oordernum = o.o_ordernum)
		where
			op.op_cancel = 'N' and
			op.op_settlementstatus not in ('ready', 'complete') and
			o_status = '배송완료' and
			timestamp(op.op_completedate) > 0 and
			DATE_ADD(op.op_completedate , INTERVAL
				(
					case o.o_paymethod
					when 'card' then " . $siteInfo['s_product_auto_C'] . "
					when 'iche' then " . $siteInfo['s_product_auto_L'] . "
					when 'online' then " . $siteInfo['s_product_auto_B'] . "
					when 'point' then " . $siteInfo['s_product_auto_G'] . "
					when 'virtual' then " . $siteInfo['s_product_auto_V'] . "
					when 'hpp' then " . $siteInfo['s_product_auto_H'] . "
					when 'payco' then " . $siteInfo['s_product_auto_P'] . "
					end
				)
			day) <= CURDATE()
		";
$AutoOrderProduct = _MQ_assoc($_queP);
if(count($AutoOrderProduct) <= 0) $AutoOrderProduct = array();
foreach($AutoOrderProduct as $k=>$v) {

	_MQ_noreturn(" update `smart_order_product` set `op_settlementstatus` = 'ready' where op_uid = '{$v['op_uid']}' ");
	/*
	echo " update `smart_order_product` set `op_settlementstatus` = 'ready' where op_uid = '{$v['op_uid']}' "
	.' / 배송일 : '.$v['op_senddate']
	.' / 주문코드 : '.$v['op_oordernum']
	.'<br>';
	*/
}


//echo '<hr>총 처리 될 개수: '.(count($AutoOrderProduct)+count($AutoOrdercoupon)).'개';

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행