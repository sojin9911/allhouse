<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
include_once(OD_ADDONS_ROOT.'/sns_login/sns_login.hook.php');



// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


# 기본처리
if(is_login()) error_loc("/");

// 2020-03-25 SSJ :: 비회원 주문 버튼 추가
$isNoneMemberBuy = false;
if($siteInfo['s_none_member_buy'] == "Y" && !is_login() && $siteInfo['s_none_member_login_skip'] <> 'Y' && $_rurl == enc('e', 'pn=shop.order.form')) {
	$isNoneMemberBuy = true;
}
/*
 $sns_join_type = direct or facebook or naver or kakao
*/
$sns_join_type = 'direct';
$sns_short = array('nv_', 'fb_', 'ko_');
$sns_join_key = 'direct';
$is_sns_login_form = false; // sns로그인 사용여부
$sns_channel = array();
$sns_login_count = 0; // sns로그인 사용개수
if(count($SNSField) > 0) {
	foreach($SNSField as $k=>$v) {
		if($v['short']) {
			$sns_short[] = $v['short'];
			$sns_channel[$k] = "sns_{$k}_sync";
		}
	}
	foreach($sns_channel as $k=>$v) {
		$$v = OD_ADDONS_URL.'/sns_login/'.$k.'/callback.php';
		$SNSField[$k]['callback_url'] = OD_ADDONS_URL.'/sns_login/'.$k.'/callback.php';
		$SNSField[$k]['login_use'] = 'N';

		if($SNSField[$k]['config_secret'] != 'nope' && !$siteInfo[$SNSField[$k]['config_secret']]) continue;
		if($siteInfo[$SNSField[$k]['config_use']] == 'N' || !$siteInfo[$SNSField[$k]['config_key']]) continue;
		$SNSField[$k]['login_use'] = 'Y';
		$sns_login_count++;
		$is_sns_login_form = true; // sns로그인 사용여부
	}

	/*
		$$v->
			echo $sns_facebook_sync.'<br>';
			echo $sns_kakao_sync.'<br>';
			echo $sns_naver_sync.'<br>';
	*/
}

/*
# 중간페이지가 있는 SNS 로그인
$is_sns_login = ($_COOKIE['AuthSNSEncID']?true:false);
$login_referer = ($_SERVER['HTTP_REFERER']?parse_url($_SERVER['HTTP_REFERER']):array());
if(strpos($login_referer['query'], 'member.login.form') !== false) $is_sns_login = true;
else $is_sns_login = false; // 로그인 관련 폼이 아닌 다른 페이지를 거쳤다면 SNS정보 증발
$is_sns_login_form = false; // sns로그인 사용여부
if((($siteInfo['s_facebook_login_use'] == 'Y' && $siteInfo['s_facebook_key'] && $siteInfo['s_facebook_secret']) || ($siteInfo['kakao_login_use'] == 'Y' && $siteInfo['kakao_api']) || ($siteInfo['nv_login_use'] == 'Y' && $siteInfo['nv_login_key'] && $siteInfo['nv_login_secret'])) && $is_sns_login === false) $is_sns_login_form = true;
*/

// 로그인 후 이동 위치
if(isset($_rurl)) $_rurl = (preg_match('/login|join|find|pro.php/i', $_rurl)?'':$_rurl);
$_rurl = (isset($_rurl)?$_rurl:(preg_match('/login|join|find|pro.php/i', $_SERVER['REQUEST_URI'])?'':$_SERVER['REQUEST_URI']));

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행