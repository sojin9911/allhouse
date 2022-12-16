<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }



# 기본처리
member_chk();
//if(is_login()) $indr = $mem_info; // 개인정보 추출


# 데이터 조회
$que = " select o.* , oc.oc_tid, oc.oc_content, ( select ool_tid from smart_order_onlinelog where ool_ordernum = o.o_ordernum order by ool_uid desc limit 1 ) as ool_tid
				from smart_order as o
				left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum)
				where o.o_ordernum='{$ordernum}' and o.o_mid='".get_userid()."' ";
$row = _MQ($que); // 주문결제정보
if( count($row)==0 ) { error_loc_msg('/?pn=shop.order.list','잘못된 접근입니다.'); }

$sres = _MQ_assoc("
	select op.*,o.*, p.p_name,p.p_cpid, p.p_img_list_square , p.p_code, p.p_coupon,p.p_stock, p.p_shoppingPay, p_shoppingPay_use
	from smart_order as o
	left join smart_order_product as op on (op.op_oordernum = o.o_ordernum )
	left join smart_product as p on ( p.p_code=op.op_pcode )
	where op_oordernum='{$ordernum}'
	group by op_pcode
	order by op_uid
"); // 주문상품정보



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행