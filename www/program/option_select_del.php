<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// ----------------- 사전체크 ---------------------//
// 필수 변수 체크
if( !$uid && !$code ) {
    echo "error1"; //잘못된 접근입니다.
    exit;
}

// 상품정보, 옵션정보 추출
include_once(OD_PROGRAM_ROOT."/option_select.top_inc.php");
              

// 넘어온 정보 삭제
$sque = " delete from smart_product_tmpoption where pto_uid='{$uid}' ";
mysql_query($sque);


// 삭제후 남은 옵션중 필수 옵션이 없으면 모든 추가옵션 삭제
$no_addoption_cnt = _MQ(" select count(*) as cnt from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_is_addoption = 'N' ");
if($no_addoption_cnt[cnt]==0) {
	_MQ_noreturn(" delete from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' ");
}


// 옵션목록 적용
include_once(OD_PROGRAM_ROOT."/option_select.bottom_inc.php");





actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행