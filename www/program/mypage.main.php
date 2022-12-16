<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }


# 기본처리
member_chk();
if(is_login()) $indr = $mem_info; // 개인정보 추출


# 주문통계 - 결제상태별
$order_status['결제대기'] = $order_status['결제완료'] = $order_status['배송중'] = $order_status['배송완료'] = $order_status['주문취소'] = 0;
// SSJ : 주문/결제 통합 패치 : 2021-02-24 : arr_order_payment_type 변수적용
$r = _MQ_assoc(" select * from smart_order where o_mid='".get_userid()."' and !(o_paymethod in ('". implode("','", $arr_order_payment_type) ."') and o_paystatus ='N' and o_canceled='N') ");
foreach($r as $k => $v){
	if($v['o_status']=='배송준비') $order_status['배송중']++;
	else if($v['o_status']=='배송대기') $order_status['결제완료']++;
	else $order_status[$v['o_status']]++;
}
/****
# 주문통계 - 진행상태별
$arr_status = array();
$res = _MQ_assoc(" select o_status,o_canceled,o_ordernum $s_query");
foreach( $res as $k=>$v ){
	$arr_status[totalOrder]++;		// 전체 주문수
	if($v[o_canceled] != "Y" && in_array($v[o_status],$arr_order_status_ordering))  $arr_status[ingOrder]++;	// 현재 진행중인 주문
	if($v[o_canceled] == "Y")  $arr_status[cancelOrder]++;	// 취소된 주문

	if($v[o_canceled] != "Y")	{
		// 배송중 상태가 아니더라도 교환/반품 신청을 한적이 있는 주문은 결과확인을 위하여 노출시킨다.
		$tmp = _MQ("select count(*) as cnt from smart_order_product where op_oordernum='".$v[o_ordernum]."' and op_complain != '' limit 1");
		if($tmp[cnt]) $arr_status[complainOrder]++;// 교환/반품 가능상태의 주문
	}
}
****/



// SSJ : 주문/결제 통합 패치 : 2021-02-24 : arr_order_payment_type 변수적용
$s_query = " from smart_order as o where o_mid='".get_userid()."' and !(o_paymethod in ('". implode("','", $arr_order_payment_type) ."') and o_paystatus ='N' and o_canceled='N')  ";
$res = _MQ(" select count(*) as cnt $s_query ");
$que = "
	select
		o.* ,
		(select count(*) from smart_order_product as op where op.op_oordernum=o.o_ordernum) as op_cnt,
		(
			select
				p.p_name
			from smart_order_product as op
			inner join smart_product  as p on ( p.p_code=op.op_pcode )
			where op.op_oordernum=o.o_ordernum order by op.op_uid asc limit 1
		) as p_name
	$s_query
	ORDER BY o_rdate desc limit 0 , 5
";
$res = _MQ_assoc($que);



# 나의 찜한 상품 추출
$listmaxcount = 6;
$pwque = "
	select
		pw.*, p.* ,
		(select count(*) from smart_product_wish as pw2 where pw2.pw_pcode=pw.pw_pcode) as cnt_product_wish
	from smart_product_wish as pw
	inner join smart_product as p on ( p.p_code=pw.pw_pcode )
	where pw.pw_inid='". get_userid() ."'
	order by pw_uid desc limit 0 , $listmaxcount
";
$myWishList = _MQ_assoc($pwque);




include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행