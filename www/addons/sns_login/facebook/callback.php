<?php
# LDD: 2017-10-12 페이스북 로그인 - 콜백
/*
	앱 아이디: $siteInfo['s_facebook_key']
	앱 시크릿: $siteInfo['s_facebook_secret']
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once(dirname(__FILE__).'/../sns_login.hook.php');

// 필요항목 정리
$sns_login_addon_type = str_replace(array(str_replace('//', '/', OD_ADDONS_ROOT), '/sns_login/'), '', dirname(__FILE__));
if($siteInfo[$SNSField[$sns_login_addon_type]['config_use']] == 'N' || !$siteInfo[$SNSField[$sns_login_addon_type]['config_key']] || !$siteInfo[$SNSField[$sns_login_addon_type]['config_secret']]) error_msgPopup($SNSField[$sns_login_addon_type]['name'].' 로그인 기능이 OFF 상태입니다.'); // 사용여부
$redirect = $system['url'].OD_ADDONS_DIR.'/sns_login/'.$sns_login_addon_type.'/callback.php'; // LCY : 크롬80 업데이트로 인한 SNS로그인 콜백URL 보완패치 : 2020-09-01
$key = $siteInfo[$SNSField[$sns_login_addon_type]['config_key']];
$secret = $siteInfo[$SNSField[$sns_login_addon_type]['config_secret']];
$base = 'https://graph.facebook.com';
$CUrl = 'https://www.facebook.com/v2.10/dialog/oauth'; // 콜백 URL
$TUrl = $base.'/oauth/access_token'; // access token URL
$PUrl = $base.'/me'; // 프로필 URL
$code = $_REQUEST['code'];
$token = $_REQUEST['token'];
if($_mode) {
	samesiteCookie("SNSOauthMode", $_mode, 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	$SNSOauthMode = $_COOKIE['SNSOauthMode'] = $_mode;
}

// 로그인 요청
if(!$code) die(header("location: {$CUrl}?client_id={$key}&redirect_uri=".urlencode($redirect)."&response_type=code&display=popup&scope=email"));



// 토큰요청
$Param = array(
	'client_id'=>$key,
	'client_secret'=>$secret,
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
	'access_token'=>$access_token,
	'fields'=>'id,cover,name,first_name,last_name,age_range,link,gender,locale,picture,email'
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
$sns_info = array(
	'type'=>$sns_login_addon_type,
	'id'=>$sns_id,
	'name'=>$SNSLoginData['name'],
	'email'=>$SNSLoginData['email']
);



// 이메일이 넘어오지 않은경우 권한 재요청
if(!$response['email']) {
	error_loc_msg("https://www.facebook.com/v2.10/dialog/oauth?client_id={$key}&redirect_uri={$redirect}&response_type=code&display=popup&auth_type=rerequest&scope=email", '사이트 이용을 위해서 이메일 권한이 필요합니다.\\n이메일 권한을 승인 부탁 드립니다.');
}



// 모드가 있다면 모드 전달
if($_COOKIE['SNSOauthMode']) {
	$SNSOauthMode = $_COOKIE['SNSOauthMode'];
	samesiteCookie('SNSOauthMode', '', time() -3600, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
}



// 로그인 처리
include_once(dirname(__FILE__).'/login.pro.php');