<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// 전체 재고 확인
$cnt_que = " select ifnull(sum(pto_cnt),0) as sum from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_is_addoption != 'Y' ";
$cnt_r = _MQ($cnt_que);
if($r[p_stock] < $cnt_r[sum]) {
	echo "error4"; //재고량이 부족합니다.
	exit;
}
if(!$arr_option_data[$app_uid]['option_cnt'] && $p_option_type_chk == "none") {
	$arr_option_data[$app_uid]['option_cnt'] = $r[p_stock];
}
$sque = " select * from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' order by pto_is_addoption desc, pto_uid asc ";
$sres = _MQ_assoc($sque);
$price_sum = 0;
$cnt_sum = 0;

// {{{회원등급혜택}}}
$groupSetInfo = getGroupSetInfo();
// {{{회원등급혜택}}}


@include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행