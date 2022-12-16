<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---



# 데이터 조회
if($_COOKIE["AuthAdmin"]) {
	if($_mode == "print") $ordernum_list = $ordernum; // 개별인쇄
	else if($_mode == "indprint" && get_userid()) $ordernum_list = $ordernum; // 관리자 접속한 상태에서 개별회원 로그인 후 인쇄할 경우
	else if($_mode == "indprint" && !get_userid()) $ordernum_list = $ordernum; // 관리자 접속한 상태에서 인쇄할 경우
	else $ordernum_list = implode("','",array_keys($chk_ordernum)); // _mode == mass_print
	$que = " select * from smart_order where o_ordernum in ('{$ordernum_list}') order by o_rdate desc ";
}
else if($_mode == "indprint") {
	if(is_login()) $que = " select * from smart_order where o_ordernum = '{$ordernum}' and o_mid='".get_userid()."' ";
	else $que = " select * from smart_order where o_ordernum = '{$ordernum}' ";
}
$row_array = _MQ_assoc($que);
if(!$row_array[0][o_ordernum]) error_msg("해당 주문이 없습니다.");



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행