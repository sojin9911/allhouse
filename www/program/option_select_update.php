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
if( !$code or !$uid or !$cnt ) {
    echo "error1"; //잘못된 접근입니다.
    exit;
}



// 상품정보, 옵션정보 추출
include_once(OD_PROGRAM_ROOT."/option_select.top_inc.php");

// 선택재고량은 0 초과여야 함
if( $app_cnt > 0 ) {

	//현재옵션의 재고수량
	$ptores = " select pto_pouid from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_uid ='" . $uid . "' ";
	$ptor = _MQ($ptores);
	$pto_pouid = $ptor[pto_pouid];

	if( $pto_pouid ) {
		$option_stock = $arr_option_data[$pto_pouid]['option_cnt'] - $app_cnt ; // cnt 개를 추가하므로 - cnt을 적용함
		if($option_stock < 0 ) {
			echo "error3"; //선택 옵션의 재고량이 부족합니다.
			exit;
		}
	}
	else {
		$option_type_chk = "none";
	}

	// 수량 업데이트
	$sque = "update smart_product_tmpoption set pto_cnt='" . $app_cnt . "' where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_uid='" . $uid . "' ";
	_MQ_noreturn($sque);

}




// 옵션목록 적용
include_once(OD_PROGRAM_ROOT."/option_select.bottom_inc.php");

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행