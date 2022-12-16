<?php
# 퀵뷰 처리 프로세스
// product.view.php 를 함께 사용하기 때문에 함수 변형이 필요하다.
$ErrorTrigger = false;
function error_msg($msg) {
	global $ErrorTrigger;
	echo "<script language='javascript'>alert('$msg');</script>";
	$ErrorTrigger = true;
}
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$NotInclude = true; // 후크 및 상품 상세를 불러오지 않도록 처리
$opcode = $pcode;
$pcode = $qpcode;
@include_once(OD_PROGRAM_ROOT.'/product.view.php'); // /program/product.view.php 을 호출
if($ErrorTrigger !== true) { // product.view.php 에서 에러가 발생 하지 않은 경우만 퀵뷰 출력
	@include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
}
$pcode = $opcode;

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행