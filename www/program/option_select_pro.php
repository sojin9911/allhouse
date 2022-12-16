<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// _type ::: up/down
// code
// uid
// cnt
if( $_type == "up" ) {
	$app_cnt = $cnt +1;
}
else if( $_type == "down" ){
	$app_cnt = $cnt -1;
}



// ----------------- 사전체크 ---------------------//
// 필수 변수 체크
if( !$code or !$uid ) {
		echo "error1"; //잘못된 접근입니다.
		exit;
}
// 상품정보, 옵션정보 추출
include_once(OD_PROGRAM_ROOT."/option_select.top_inc.php");


$pto_pouid =  $uid;

$option_stock = $arr_option_data[$pto_pouid]['option_cnt'] - $app_cnt ; // cnt 개를 추가하므로 - cnt을 적용함
if($option_stock < 0 ) {
	echo "error3"; //선택 옵션의 재고량이 부족합니다.
	exit;
}

if($app_cnt < 1) {
	$sque = "delete from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_pouid='" . $pto_pouid . "' ";
	_MQ_noreturn($sque);
} else {

	$ptores = " select count(*) as cnt from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_pouid ='" . $pto_pouid . "' ";
	$ptor = _MQ($ptores);

	if( $ptor[cnt] ) {	 // 이미 입력된 값이면 수정

		 $sque = "update smart_product_tmpoption set pto_cnt='" . $app_cnt . "' where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_pouid='" . $pto_pouid . "' ";
		_MQ_noreturn($sque);

	} else {

		$sque = "insert into smart_product_tmpoption set 
						pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."',
						pto_pouid = '". $pto_pouid ."',
						pto_pcode='" . $code . "',
						pto_cnt='".$app_cnt."',
						pto_poptionname1 ='".mysql_real_escape_string($arr_option_data[$pto_pouid]['option_name1'])."',
						pto_poptionname2 ='".mysql_real_escape_string($arr_option_data[$pto_pouid]['option_name2'])."',
						pto_poptionname3 ='".mysql_real_escape_string($arr_option_data[$pto_pouid]['option_name3'])."',
						pto_poption_supplyprice ='".$arr_option_data[$pto_pouid]['option_supplyprice']."',
						pto_poptionprice ='".$arr_option_data[$pto_pouid]['option_price']."'";

		_MQ_noreturn($sque);

	}

}

$ptores = " select sum(pto_cnt*pto_poptionprice) as total_price from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_pcode ='" . $code . "' ";
$ptor = _MQ($ptores);


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
echo number_format($ptor['total_price']);	// 옵션 합계 금액 