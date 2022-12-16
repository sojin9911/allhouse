<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 거래번호
$ocl = _MQ("select oc_tid from smart_order_cardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
$tno = $ocl[oc_tid]; // PG사 거래 번호

// --------- JJC : 부분취소 개선 : 2021-02-10 ---------
//// 취소할 금액
//$tmp = _MQ(" select sum( op_price * op_cnt + op_delivery_price + op_add_delivery_price) as sum from smart_order_product where IF(op_cancel_type = 'pg' , op_cancel != 'Y' , 1 ) and op_oordernum = '".$_ordernum."' ");
//$tmp2 = _MQ(" select sum(op_usepoint) as sum from smart_order_product where op_oordernum = '".$_ordernum."' ");
//$_cancel_price = trim($_total_amount);
////$_confirm_price = ($tmp[sum] - $tmp2[sum] - $_cancel_price) > 0 ? ($tmp[sum] - $tmp2[sum] - $_cancel_price ) : 0;
//$_confirm_price = ($tmp[sum] - $tmp2[sum]) > 0 ? ($tmp[sum] - $tmp2[sum]) : 0; // 2017-06-05 ::: KCP 부분 취소 오류 수정 ::: JJC
// 취소금액
$_cancel_price = trim($_total_amount); 

// 승인금액
$tmp = _MQ("
	SELECT 
		IFNULL( (SUM( op_price * op_cnt + op_delivery_price + op_add_delivery_price - op_usepoint - op_use_discount_price) - SUM( cl_price )) ,0) AS sum
	FROM smart_order_product 
	LEFT JOIN (SELECT cl_price ,cl_oordernum , cl_pcode FROM smart_order_coupon_log WHERE cl_type = 'product' ) AS tbl ON (cl_oordernum = op_oordernum AND cl_pcode = op_pcode)
	WHERE 
		IF(op_cancel_type = 'pg' , op_cancel != 'Y' , 1 ) and 
		op_oordernum = '".$_ordernum."'
");
$_confirm_price = ($tmp['sum'] > 0) ? $tmp['sum'] : 0;
// --------- JJC : 부분취소 개선 : 2021-02-10 ---------

#############################################################################################
## KCP 결제 취소 START
#############################################################################################
require_once PG_DIR."/kcp/cfg/site_conf_inc.php";       // 환경설정 파일 include
require_once PG_DIR."/kcp/files/pp_ax_hub_lib.php";     // library [수정불가]

$c_PayPlus = new C_PP_CLI;
$c_PayPlus->mf_clear();

$tno     = trim($tno);
$tran_cd = "00200000";
$cust_ip = getenv("REMOTE_ADDR"); // 요청 IP

$c_PayPlus->mf_set_modx_data( "tno",      $tno );		// KCP 원거래 거래번호
$c_PayPlus->mf_set_modx_data( "mod_type", "STPC" );		// 원거래 변경 요청 종류
$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip );	// 변경 요청자 IP
$c_PayPlus->mf_set_modx_data( "mod_desc", "cancel" );	// 변경 사유

$c_PayPlus->mf_set_modx_data( "mod_mny", $_cancel_price );	// 취소요청금액
$c_PayPlus->mf_set_modx_data( "rem_mny", $_confirm_price );	// 취소가능잔액

$c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key ,  $tran_cd,    "", $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib", $ordernum, $cust_ip, "3", 0, 0, $g_conf_key_dir, $g_conf_log_dir);

$res_cd  = $c_PayPlus->m_res_cd;
$res_msg = iconv("euckr","utf8",$c_PayPlus->m_res_msg);	// 결과 메세지

// 취소 성공 여부
$is_pg_status = $res_cd == "0000" ? true : false;

if( $is_pg_status === true ) {
	$amount			= $c_PayPlus->mf_get_res_data( "amount"       ); // 총 금액
	$panc_mod_mny	= $c_PayPlus->mf_get_res_data( "panc_mod_mny" ); // 부분취소 요청금액
	$panc_rem_mny	= $c_PayPlus->mf_get_res_data( "panc_rem_mny" ); // 부분취소 가능금액
}

// 취소결과 로그 기록
card_cancle_log_write($tno,$res_msg);	// 카드거래번호 , 결과 메세지


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행