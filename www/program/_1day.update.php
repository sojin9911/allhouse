<?php
ini_set('memory_limit','512M');
/*
	포인트 & 휴면 & 자동정산 처리 :: 하루 한번 실행
	/program/_auto_load.php 에서 1일 1회 실행
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');



# 조건 처리
if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) return; // 동일 서버안에서만 동작 하도록 => curl_async을 통해서만 실행됨
if($siteInfo['daily_update_date'] == date('Y-m-d', time())) return; // 이미 실행됬다면 pass
_MQ_noreturn(" update smart_setup set daily_update_date = now() where s_uid = 1 "); // 실행일 갱신



# 솔루션 데일리 처리
point_update(); // 포인트 업데이트
coupon_update(); // 쿠폰 업데이트
coupon_set_update(); // 쿠폰설정 업데이트 :: 사용기간이 지난 쿠폰 처리
couponIssuedAutoType3(); // 자동쿠폰 발급 타입 :: 생일축하
couponIssuedAutoType5(); // 자동쿠폰발급 타입 :: 출석체크
member_sleep_backup();  // 휴면계정 처리
include_once(OD_PROGRAM_ROOT.'/_settle.update.php'); // 자동정산처리 ::

// KAY :: 에디터 이미지 관리 :: 2021-06-21
include_once(OD_PROGRAM_ROOT.'/_1day.update_editor_img_update.php');


# 최근 본 상품 설정(통합관리자 -> 환경설정 -> 상품/배송 설정 -> 최근 본 상품 설정)
_MQ_noreturn(" delete from smart_product_latest where (1) and date_add(pl_rdate, interval +".($siteInfo['s_today_view_time'] > 0?$siteInfo['s_today_view_time']:24)." hour) <= curdate() ");



# 보안서버 상태정보 체크
//$arr = ssl_condition_info();
//$ssl_condition = CurlExecHeader($arr['ssl_domain'].'/program/_ping.php'); // 200 이 아니면 비정상
//if($ssl_condition != 200 ) _MQ_noreturn(" update smart_setup set s_ssl_check = 'N', s_ssl_admin_loc = 'N', s_ssl_pc_loc = 'N', s_ssl_m_loc = 'N' where s_uid = 1 ");// 접속비정상일 경우 비적용함..



# 보안서버 기간에 따른 만료처리 + 보안인증서를 사용 할 수 없는 상태에서는 페이스북 로그인 기능을 끈다.
if( $siteInfo['s_ssl_edate'] < DATE("Y-m-d") ) {
	_MQ_noreturn("
		update smart_setup set
			s_ssl_check = 'N', s_ssl_admin_loc = 'N', s_ssl_pc_loc = 'N', s_ssl_m_loc = 'N', s_ssl_status = '만료' ,
			s_facebook_login_use = 'N'
		where s_uid = '1'
	");
}



# 옵션 정리  - 상품 - 옵션 연결이 없는 옵션 삭제
$arr_pocde = array();
$po_res = _MQ_assoc(" SELECT po.po_pcode FROM smart_product_option as po LEFT JOIN smart_product as p ON ( p.p_code = po.po_pcode ) WHERE p.p_code IS NULL GROUP BY po.po_pcode");
if(count($po_res) <= 0) $po_res = array();
foreach($po_res as $k=>$v){
	$arr_pocde[$v['po_pcode']]++;
}
if(sizeof($arr_pocde) > 0 ) {
	_MQ_noreturn(" DELETE FROM smart_product_option WHERE po_pcode IN ('". implode("' , '" , array_keys($arr_pocde)) ."') ");
}



# 2019-03-07 SSJ :: 찜상품중 삭제된 상품 정리
$del_wish_res = _MQ_assoc(" select pw.pw_pcode from smart_product_wish as pw left join smart_product as p on ( pw.pw_pcode = p.p_code ) where p.p_code is null group by pw.pw_pcode ");
if(count($del_wish_res) > 0){ foreach($del_wish_res as $k=>$v){ _MQ_noreturn(" delete from smart_product_wish where pw_pcode = '". $v['pw_pcode'] ."' "); }}



// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
if($siteInfo['s_order_auto_cancel_term'] > 0) {
    $ores = _MQ_assoc("
        select
            *
        from smart_order
        where
            o_canceled!='Y' and
            o_paystatus = 'N' and
            DATE_ADD(o_rdate  , INTERVAL + ". $siteInfo['s_order_auto_cancel_term'] ." day) < NOW()
    ");
    foreach($ores as $k=>$v){

        // 공통취소
        //		넘길변수
        //			-> 취소위치 : _loc (관리자일 경우 - admin / 사용자일 경우 - user)
        //			-> 주문번호 : _ordernum
        //			-> 주문정보 : $osr
        //		return 정보
        //			-> 성공여부 : cancel_status = Y/N
        //			-> 메시지 : cancel_msg
        $_loc = "admin";
        $_ordernum = $v['o_ordernum'] ;
        $osr = $v ;
        include(OD_PROGRAM_ROOT."/pg.cancel.inc.php");
    }
}
// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----



// PG 사별로 결제는 되었으나 상태가 결제 대기이고 하루전인 주문 결제 완료로 변경 kms 2019-04-22
switch($siteInfo['s_pg_type']){
	case "daupay" :
		include_once(OD_PROGRAM_ROOT.'/check.pg.daupay.php');
		break;
	case "lgpay" :
		include_once(OD_PROGRAM_ROOT.'/check.pg.lgpay.php');
		break;
	case "inicis" :
		include_once(OD_PROGRAM_ROOT.'/check.pg.inicis.php');
		break;
}

// SSJ : 자동 배송완료 패치 : 2021-02-01
auto_delivery_complete();
