<?php // LCY 2018-03-08 -- 쿠폰설정
include_once('inc.php');
/*
	s_coupon_use : 쿠폰사용여부 (Y사용,N)
	s_coupon_view : 쿠폰노출 설정 (all:전체,member:회원)
	s_coupon_ordercancel_return :  주문취소에 따른 복원 사용여부 (Y:사용,N:미사용)
*/

$s_coupon_use = in_array($s_coupon_use,array('Y','N')) == true ? $s_coupon_use : 'N';
$s_coupon_view = in_array($s_coupon_view,array('all','member')) == true ? $s_coupon_view : 'all';
$s_coupon_ordercancel_return = in_array($s_coupon_ordercancel_return,array('Y','N')) == true ? $s_coupon_ordercancel_return : 'N';


_MQ_noreturn("update smart_setup set s_coupon_use = '".$s_coupon_use."' , s_coupon_view = '".$s_coupon_view."' , s_coupon_ordercancel_return = '".$s_coupon_ordercancel_return."'  where s_uid = 1; "); // 저장

error_frame_loc("_coupon_config.php");

?>