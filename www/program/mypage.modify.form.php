<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
include_once(OD_ADDONS_ROOT.'/sns_login/sns_login.hook.php');
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }

// 로그인 체크
member_chk();

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


# 기본처리
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

	if(in_array(str_replace($sns_short, '', $mem_info['in_id']), array($mem_info['fb_encid'], $mem_info['ko_encid'], $mem_info['nv_encid']))) {
		$sns_join_key = reset(explode(str_replace($sns_short, '', $mem_info['in_id']), $mem_info['in_id']));
	}
	if(count($SNSField) > 0) {
		foreach($SNSField as $k=>$v) {
			if($v['short'] == $sns_join_key) $sns_join_type = $k;
		}
	}
	foreach($sns_channel as $k=>$v) {

		if($mem_info[$SNSField[$k]['join']] == 'Y' && $mem_info[$SNSField[$k]['id']]) {
			$$v = "";
			$SNSField[$k]['callback_url'] = '';
		}
		else {
			$$v = OD_ADDONS_URL.'/sns_login/'.$k.'/callback.php';
			$SNSField[$k]['callback_url'] = OD_ADDONS_URL.'/sns_login/'.$k.'/callback.php';
		}

		$SNSField[$k]['login_use'] = 'N';
		if($SNSField[$k]['config_secret'] != 'nope' && !$siteInfo[$SNSField[$k]['config_secret']]) continue;
		if($siteInfo[$SNSField[$k]['config_use']] == 'N' || !$siteInfo[$SNSField[$k]['config_key']]) continue;
		$SNSField[$k]['login_use'] = 'Y';
		$sns_login_count++;
		$is_sns_login_form = true; // sns로그인 사용여부
	}

	/*
		echo $sns_facebook_sync.'<br>';
		echo $sns_kakao_sync.'<br>';
		echo $sns_naver_sync.'<br>';
	*/
}



$_ATUH_TYPE_ = 'modify';
include_once(OD_PROGRAM_ROOT.'/member.join.auth.php'); // 2018-10-04 SSJ :: KCP 휴대폰 본인인증 추가
include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행