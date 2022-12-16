<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// - 주문번호저장정보 추출 ---
session_start();
if(!$ordernum) $ordernum = $_SESSION["session_ordernum"];
// - 주문번호저장정보 추출 ---


// 주문번호 추출 후 세션 초기화
$_SESSION['session_ordernum'] = '';
unset($_SESSION['session_ordernum']);


# 데이터 조회
$que = " select o.* , oc.oc_tid, oc.oc_content, ( select ool_tid from smart_order_onlinelog where ool_ordernum = o.o_ordernum order by ool_uid desc limit 1 ) as ool_tid
				from smart_order as o
				left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum)
				where o.o_ordernum='{$ordernum}' ";
if(is_login()) $que .= " and o.o_mid='".get_userid()."' ";
$row = _MQ($que); // 주문결제정보
if( count($row)==0 ) { error_loc_msg('/','필수 정보가 누락되었습니다. 메인페이지로 이동합니다.'); }

// 주문상품정보 추출
$sres = _MQ_assoc("
	select 
		op.*,o.*, p.p_name,p.p_cpid, p.p_img_list_square , p.p_code, p.p_coupon,p.p_stock, p.p_shoppingPay, p_shoppingPay_use
	from smart_order as o
	inner join smart_order_product as op on (op.op_oordernum = o.o_ordernum )
	inner join smart_product as p on ( p.p_code=op.op_pcode )
	where op_oordernum='{$ordernum}'
	group by op_pcode
	order by op_uid
");

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행