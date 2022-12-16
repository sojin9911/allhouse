<?php
//define('_SITE_SKIN_NAME', str_replace(array($_SERVER['DOCUMENT_ROOT'], OD_SITE_SKIN_DIR, OD_SITE_MSKIN_DIR, '/'), '', dirname(__FILE__))); // 현재스킨의 폴더명
$_SITE_SKIN_NAME = str_replace(array($_SERVER['DOCUMENT_ROOT'], OD_SITE_MSKIN_DIR, OD_SITE_SKIN_DIR, '/'), '', dirname(__FILE__)); // 2020-04-24 SSJ :: 스킨명 변수

// 스킨별 배너
$skin_banner_loc = array(
	// '배너구분,set_color'=>'색지정을 사용하는 배너',
	// '배너구분,set_detail_info'=>'배너설명 사용하는 배너',
	// '배너구분,set_color,set_detail_info'=>'배경색+배너설명 사용하는 배너',
	// '배너구분,set_detail_info,set_color'=>'배경색+배너설명 사용하는 배너',
	'site_top_logo'=>'[PC]공통 : 상단 로고 (가로 200 이하 x 세로 25 이하, 1개)',
	'site_top_qr'=>'[PC]공통 : 상단 QR코드 (58 x 58, 1개)',
	'site_main_visual'=>'[PC]메인 : 메인비주얼배너 (1,800 x 400, 슬라이드형 무제한)',
	'site_main_middle'=>'[PC]메인 : 메인 중간 3단 배너 (583 x 222, 노출형 무제한)',
	'site_product_middle'=>'[PC]공통 : 상품상세 중간 배너 (1,050 x free, 1개)'
);

$skin_pc_banner_loc = $skin_banner_loc; // pc
// $skin_mobile_banner_loc = $skin_banner_loc; // mobile


// 키에 스킨명을 추가
if(count($skin_banner_loc) > 0) {
	foreach($skin_banner_loc as $loc_k=>$loc_v) {
		// $skin_banner_loc[_SITE_SKIN_NAME.','.$loc_k] = $loc_v;
		$skin_banner_loc[$_SITE_SKIN_NAME.','.$loc_k] = $loc_v; //2020-04-24 SSJ :: 스킨명 변수 변경
		unset($skin_banner_loc[$loc_k]);
	}
}

// 배너변수에 머지
if(empty($merge_no)) $arr_banner_loc = array_merge($arr_banner_loc, $skin_banner_loc);
else $arr_banner_loc = $skin_banner_loc;

// 상품 아이콘 유형
$arr_product_icon_type = array(
	'product_name_small_icon' => "선택적용 아이콘( 40 x 20 )",
);