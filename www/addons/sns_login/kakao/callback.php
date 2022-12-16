<?php
# LDD: 2017-10-16 카카오 로그인 - 콜백
/*
	REST API KEY: $siteInfo['kakao_api']
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once(dirname(__FILE__).'/../sns_login.hook.php');

// 필요항목 정리
$sns_login_addon_type = str_replace(array(str_replace('//', '/', OD_ADDONS_ROOT), '/sns_login/'), '', dirname(__FILE__));
if($siteInfo[$SNSField[$sns_login_addon_type]['config_use']] == 'N' || !$siteInfo[$SNSField[$sns_login_addon_type]['config_key']]) error_msgPopup($SNSField[$sns_login_addon_type]['name'].' 로그인 기능이 OFF 상태입니다.'); // 사용여부
$redirect = $system['url'].OD_ADDONS_DIR.'/sns_login/'.$sns_login_addon_type.'/callback.php'; // LCY : 크롬80 업데이트로 인한 SNS로그인 콜백URL 보완패치 : 2020-09-01
$key = $siteInfo[$SNSField[$sns_login_addon_type]['config_key']];
$base = 'https://kauth.kakao.com';
$base2 = 'https://kapi.kakao.com';
$Curl = $base.'/oauth/authorize'; // 콜백 URL
$TUrl = $base.'/oauth/token'; // access token URL
$PUrl = $base2.'/v2/user/me'; // 프로필 URL
$code = $_REQUEST['code'];
$token = $_REQUEST['token'];
if($_mode) {
	samesiteCookie("SNSOauthMode", $_mode, 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	$SNSOauthMode = $_COOKIE['SNSOauthMode'] = $_mode;
}


// 로그인 요청
if(!$code) die(header("location: {$Curl}?client_id={$key}&redirect_uri={$redirect}&response_type=code"));


// 토큰요청
$Param = array(
	'grant_type'=>'authorization_code',
	'client_id'=>$key,
	'redirect_uri'=>$redirect,
	'code'=>$code
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $TUrl);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($Param));
//curl_setopt($ch, CURLOPT_POSTFIELDSIZE, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// JJC : 2022-08-11 : https 보완
$arr_url = parse_url($TUrl);
if( $arr_url[scheme] == "https") {curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);}

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
));
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$response = (array)json_decode($response);
$access_token = $response['access_token'];
if(!$access_token) error_loc('callback.php');



// 사용자 정보 조회
$Param = array(
	'access_token'=>$access_token
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $PUrl);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($Param));
//curl_setopt($ch, CURLOPT_POSTFIELDSIZE, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// JJC : 2022-08-11 : https 보완
$arr_url = parse_url($PUrl);
if( $arr_url[scheme] == "https") {curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);}

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
));
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$response = json_decode($response, true);
$SNSLoginData = $response;
$sns_id = $SNSLoginData['id'];
// SSJ : 카카오톡 로그인 이메일 항목 변경 : 2021-05-24
if($SNSLoginData['kakao_account']['email'] <> ''){ $SNSLoginData['kaccount_email'] = $SNSLoginData['kakao_account']['email']; }
$sns_info = array(
	'type'=>'kakao',
	'id'=>$sns_id,
	'name'=>$SNSLoginData['properties']['nickname'],
	'email'=>$SNSLoginData['kaccount_email']
);



// 모드가 있다면 모드 전달
if($_COOKIE['SNSOauthMode']) {
	$SNSOauthMode = $_COOKIE['SNSOauthMode'];
	samesiteCookie('SNSOauthMode', '', time() -3600, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
}


// 로그인 처리
include_once(dirname(__FILE__).'/login.pro.php');