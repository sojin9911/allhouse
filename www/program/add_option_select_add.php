<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



$option_type_chk = $pr[option_type_chk];

// ----------------- 사전체크 ---------------------//
// 필수 변수 체크
if( !$code || !$uid) {
    echo "error1"; //잘못된 접근입니다.
    exit;
}

$app_uid = $uid;

// 넘어온 정보의 중복체크
$cntr = _MQ(" select count(*) as cnt from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_pouid='" . $app_uid . "' and pto_is_addoption = 'Y' ");
if($cntr[cnt] > 0 ) {
    echo "error2"; //이미 선택한 옵션입니다.
    exit;
}

// 선택옵션이 1개이상 선택되었는지 체크
$cntr2 = _MQ(" select count(*) as cnt from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."'  and pto_is_addoption != 'Y' ");
if($cntr2[cnt] <= 0 ) {
    echo "error6"; // 상세옵션을 먼저 선택해 주시기 바랍니다.
    exit;
}
// ----------------- 사전체크 ---------------------//





// 상품정보, 옵션정보 추출
include_once(OD_PROGRAM_ROOT."/add_option_select.top_inc.php");




//현재옵션의 재고수량 -- 옵션이 있을 경우에만
if( $app_uid ) {
    $option_stock = $arr_option_data[$app_uid]['option_cnt'] - 1; // 한개를 추가하므로 - 1을 적용함
    if($option_stock < 0 ) {
        echo "error3"; //선택 옵션의 재고량이 부족합니다.
        exit;
    }
}

// 부모 pouid LMH002
$_addoption_parent = _MQ_result(" select pto_pouid from smart_product_tmpoption where pto_is_addoption != 'Y' order by pto_uid desc limit 1 ");

// 넘어온 정보 추가 LMH002 (pto_addoption_parent 추가)
$sque = "
    insert smart_product_tmpoption set 
        pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."',
		pto_pouid = '". $app_uid ."',
        pto_pcode='" . $code . "',
        pto_cnt=1,
        pto_poptionname1 ='".$arr_option_data[$app_uid]['option_name1']."',
		pto_poptionname2 ='".$arr_option_data[$app_uid]['option_name2']."',
		pto_poptionname3 ='".$arr_option_data[$app_uid]['option_name3']."',
		pto_poption_supplyprice ='".$arr_option_data[$app_uid]['option_supplyprice']."',
        pto_poptionprice ='".$arr_option_data[$app_uid]['option_price']."',
		pto_is_addoption = 'Y',
		pto_addoption_parent = '".$_addoption_parent."'
";
        //pto_poptionprice ='".($arr_option_data[$app_uid]['option_price']+$r[price])."' // 옵션가격 추가형 => 옵션가격 비추가형
_MQ_noreturn($sque);



// 옵션목록 적용
include_once(dirname(__FILE__)."/option_select.bottom_inc.php");
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행