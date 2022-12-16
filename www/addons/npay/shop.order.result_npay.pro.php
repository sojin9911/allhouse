<?php
// LDD NPAY
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
$_path_str = $_SERVER['DOCUMENT_ROOT'];
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/npay.class.php');

if($pass_type == 'wish') { // 찜하기

	$Npay->set(array(array($pcode, $option_select_cnt)));
	$Npay->WishAdd();
	//ViewArr($Npay->debug());
	exit;
}
else if($pass_type == 'view') { // 상품페이지에서 구매

	// LCY : 네이버페이 사용유무 추가 : 2020-10-20 - 체크 강화
	$npayChk = _MQ("select count(*) as cnt from smart_product where npay_use = 'Y' and p_code = '".$pcode."' ");
	if( $npayChk['cnt'] < 1){ error_msg("해당 상품은 네이버페이로 구매가 불가능 합니다.");  }

	$Npay->set(array(array($pcode, $option_select_cnt)));
	//ViewArr($Npay->debug());

	

}
else { // 장바구니에서 구매

	$Rpcode = $pcode;
	$Rpcode = array_filter(explode(',', $Rpcode));
	$pcode = array();
	$chk_pcode = array(); // 상품 코드 중복 검사
	foreach($Rpcode as $k=>$v) {
		
		if(trim($v) == '') continue;
		if($chk_pcode[$v] == true) continue; // 상품 코드 중복 검사

		// LCY : 네이버페이 사용유무 추가 : 2020-10-20 - 체크 강화
		$npayChk = _MQ("select count(*) as cnt from smart_product where npay_use = 'Y' and p_code = '".$v."' ");
		if( $npayChk['cnt'] < 1){ continue; }

		$pcode[] = array($v);
		$chk_pcode[$v] = true; // 상품 코드 중복 검사
	}	


	// LCY : 네이버페이 사용유무 추가 : 2020-10-20 - 체크 강화
	if( count($chk_pcode) < 1){ error_msg("현재 네이버페이로 구매가능한 상품이 없습니다.");  }	

	

	$Npay->set($pcode, 'cart');
	//ViewArr($Npay->debug());
}


// 테스트 영역
/*
$Param = $Npay->debug();
ViewArr($Param);
foreach(explode('&', $Param['config']['Parameter']['param']) as $k=>$v) {
	echo $v.'<br>';
}
*/

$run = $Npay->run(); // 실행
if(trim($run) != '') echo $run; // 에러가 있을 경우