<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


# 기본처리
member_chk();
if(is_login()) $indr = $mem_info; // 개인정보 추출

# 주문통계 - 결제상태별
$order_status['접수대기'] = $order_status['접수완료'] = $order_status['배송중'] = $order_status['배송완료'] = $order_status['주문취소'] = 0;
// SSJ : 주문/결제 통합 패치 : 2021-02-24 : arr_order_payment_type 변수적용
$r = _MQ_assoc(" select * from smart_order where o_mid='".get_userid()."' and !(o_paymethod in ('". implode("','", $arr_order_payment_type) ."') and o_paystatus ='N' and o_canceled='N') ");
foreach($r as $k => $v){
	if($v['o_status']=='배송준비') $order_status['배송중']++;
	else if($v['o_status']=='구매발주') $order_status['접수완료']++;
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


# 기간변수
$today = date('Y-m-d');
$week = date('Y-m-d',strtotime('-7 day'));
$month1 = date('Y-m-d',strtotime('-1 month'));
$month3 = date('Y-m-d',strtotime('-3 month'));
$month6 = date('Y-m-d',strtotime('-6 month'));
$year = date('Y-m-d',strtotime('-1 year'));

# 기간검색
if(!$date && !$o_rdate_end && !$o_rdate_start) $date = "all";
if($date == "all") {$o_rdate_end = $today;$o_rdate_start = $year;}
else if($date == "today") {$o_rdate_end = $today;$o_rdate_start = $today;}
else if($date == "week")	{$o_rdate_end = $today;$o_rdate_start = $week;}
else if($date == "month1") {$o_rdate_end = $today;$o_rdate_start = $month1;}
else if($date == "month3") {$o_rdate_end = $today;$o_rdate_start = $month3;}
else if($date == "year") {$o_rdate_end = $today; $o_rdate_start = $year;}
if(!$date){
	if($o_rdate_end == $today && $o_rdate_start == $today) $date = 'today';
	else if($o_rdate_end == $today && $o_rdate_start == $week) $date = 'week';
	else if($o_rdate_end == $today && $o_rdate_start == $month1) $date = 'month1';
	else if($o_rdate_end == $today && $o_rdate_start == $month3) $date = 'month3';
	else if($o_rdate_end == $today && $o_rdate_start == $year) $date = 'year';
	else if($o_rdate_end == $today && $o_rdate_start == $year) $date = 'all';
}



// SSJ : 주문/결제 통합 패치 : 2021-02-24 : arr_order_payment_type 변수적용
$s_query = " from smart_order as o where o_mid='".get_userid()."' and !(o_paymethod in ('". implode("','", $arr_order_payment_type) ."') and o_paystatus ='N' and o_canceled='N')  "; // 검색 체크
if($o_rdate_start) $s_query .= " and o_rdate >= '".$o_rdate_start." 00:00:00' ";
if($o_rdate_end) $s_query .= " and o_rdate <= '".$o_rdate_end." 23:59:59' ";
if( $o_status <> "" ){
	if($o_status=='접수완료'){
		$s_query .= " and (o_status = '접수완료' or o_status = '구매발주') ";
	}else{
		$s_query .= " and o_status = '". $o_status ."' ";
	}

	if( $o_status == "주문취소" ){
		$s_query .= " and o_canceled = 'Y' ";
	}else{
		$s_query .= " and o_canceled != 'Y' ";
	}
}
/*
if(!$pass_ing) $pass_ing = "Y"; // 주문상태 초기정리
if( $pass_ing == "Y" ) $s_query .= " and o_status in ('".implode("','",$arr_order_status_ordering)."') and o_canceled!='Y'";
if( $pass_ing == "N" ) $s_query .= " and o_canceled='Y'";
if( $pass_ing == "complain" ) $s_query .= " and (select count(*) as cnt from smart_order_product as op where op.op_oordernum=o_ordernum and op_complain != '') and o_canceled!='Y'";
*/


# 데이터 조회
$listmaxcount = 10 ;
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt $s_query ");
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);
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
	ORDER BY o_rdate desc limit $count , $listmaxcount
";
$res = _MQ_assoc($que);


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행