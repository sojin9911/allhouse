<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

# 처리 할 수 없는 상태인 경우(로그인 상태, 본인인증 미승인 상태)
if(is_login()) error_loc("/");
//if($siteInfo['s_join_auth_use'] == 'Y') { // 본인인증 사용시
//	if($_POST['result_cd'] != 'B000') error_loc_msg('/?pn=member.join.auth', '본인인증이 되지 않았습니다.');
//	$_birth = date('Y-m-d', strtotime($_POST['birthday']));
//	if($_POST['gender'] == '1') $_sex = 'M';
//	if($_POST['gender'] == '2') $_sex = 'F';
//	if($_POST['tel_no']) $auth_hp = tel_format($_POST['tel_no']);
//}



// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---



# 기본처리
$is_sns_login = ($_COOKIE['AuthSNSEncID']?true:false);
$login_referer = ($_SERVER['HTTP_REFERER']?parse_url($_SERVER['HTTP_REFERER']):array());
if(strpos($login_referer['query'], 'member.login.form') !== false || strpos($login_referer['query'], 'member.join.auth') !== false) $is_sns_login = true;
else $is_sns_login = false; // 로그인 관련 폼이 아닌 다른 페이지를 거쳤다면 SNS정보 증발
if($is_sns_login === true) {
	$AuthSNSEncID = $_COOKIE['AuthSNSEncID'];
	$sns_info = unserialize(onedaynet_decode($AuthSNSEncID));
	$sns_name = ($_POST['name']?$_POST['name']:$sns_info['name']); // 본인인증을 거쳤다면 본인인증의 이름값
	$sns_email = $sns_info['email'];
}
else {
	$AuthSNSEncID = '';
	$sns_name = ($_POST['name']?$_POST['name']:''); // 본인인증을 거쳤다면 본인인증의 이름값
}


# 글자수 제한 관련 변수화
$id_min_length = ((int)$siteInfo['join_id_limit_min'] >= 4?(int)$siteInfo['join_id_limit_min']:4); // 최소 글자수
$id_max_length = ((int)$siteInfo['join_id_limit_max'] > (int)$siteInfo['join_id_limit_min']?(int)$siteInfo['join_id_limit_max']:0); // 최대 글자수(최소 글자 수보다 크면 제한 작동)
$pw_min_length = ((int)$siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); // 최소 글자수
$pw_max_length = ((int)$siteInfo['join_pw_limit_max'] > (int)$siteInfo['join_pw_limit_min']?(int)$siteInfo['join_pw_limit_max']:0); // 최대 글자수(최소 글자 수보다 크면 제한 작동)


$_ATUH_TYPE_ = 'join';
include_once(OD_PROGRAM_ROOT.'/member.join.auth.php'); // 2018-10-04 SSJ :: KCP 휴대폰 본인인증 추가
include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행