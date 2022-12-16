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
/*
 $sns_join_type = direct or facebook or naver or kakao
*/
$sns_join_type = 'direct';
$sns_short = array('nv_', 'fb_', 'ko_');
$sns_join_key = 'direct';
$is_sns_login_form = false; // sns로그인 사용여부
$sns_channel = array();
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
		$is_sns_login_form = true; // sns로그인 사용여부
	}

	/*
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

# 전체약관 ---------------------------------------------------
$agree_arr = arr_policy('all');


# 선택약관 ---------------------------------------------------
$agree_other = array();
if($agree_arr['join_optional']['po_use'] == 'Y' && count($agree_arr['join_optional']['data']) > 0) { // 개인정보수집 및 이용 동의
	$agree_other['join_optional'] = array(
		'title' => '개인정보수집 및 이용 동의',
		'agree' => array()
	);
	foreach($agree_arr['join_optional']['data'] as $aok=>$aov) {
		$agree_other['join_optional']['agree'][] = array('uid'=>$aov['po_uid'], 'title'=>$aov['po_title'], 'content'=>$aov['po_content']);
	}
}
if($agree_arr['join_csinfo']['po_use'] == 'Y' && count($agree_arr['join_csinfo']['data']) > 0) { // 개인정보 처리ㆍ위탁 동의
	$agree_other['join_csinfo'] = array(
		'title' => '개인정보 처리ㆍ위탁 동의',
		'agree' => array()
	);
	foreach($agree_arr['join_csinfo']['data'] as $aok=>$aov) {
		$agree_other['join_csinfo']['agree'][] = array('uid'=>$aov['po_uid'], 'title'=>$aov['po_title'], 'content'=>$aov['po_content']);
	}
}
if($agree_arr['join_thirdinfo']['po_use'] == 'Y' && count($agree_arr['join_thirdinfo']['data']) > 0) { // 개인정보 제3자 제공 동의
	$agree_other['join_thirdinfo'] = array(
		'title' => '개인정보 제3자 제공 동의',
		'agree' => array()
	);
	foreach($agree_arr['join_thirdinfo']['data'] as $aok=>$aov) {
		$agree_other['join_thirdinfo']['agree'][] = array('uid'=>$aov['po_uid'], 'title'=>$aov['po_title'], 'content'=>$aov['po_content']);
	}
}


# 개인정보처리방침 하단 테이블
// 고정필수 항목
$privacy_table = array();
$privacy_table['회원가입'][0] = array();
$privacy_table['회원가입'][0]['required'] = 'Y';
$privacy_table['회원가입'][0]['name'] = '서비스 이용 및 상담';
$privacy_table['회원가입'][0]['destruction'] = '회원 탈퇴 이후 부정 이용을 방지하기 위해 1년간 보존';
$privacy_table['회원가입'][0]['item'] = array();
$privacy_table['회원가입'][0]['item'][] = '아이디';
$privacy_table['회원가입'][0]['item'][] = '비밀번호';
$privacy_table['회원가입'][0]['item'][] = '이름';
$privacy_table['회원가입'][0]['item'][] = '휴대폰 번호';
$privacy_table['회원가입'][0]['item'][] = '이메일';

// 필수여부가 유동적으로 바뀌는 항목
$JoinFlow = array();
$JoinFlowArr = array(
	'전화번호'=>'join_tel',
	'주소'=>'join_addr',
	'생일'=>'join_birth',
	'성별'=>'join_sex'
);
$JoinFlowArr2 = array();
foreach($JoinFlowArr as $jfak=>$jfav) {
	if($siteInfo[$jfav] == 'Y') {
		$JoinFlow[$jfak] = $jfav;
		$JoinFlowV[$jfak] = $siteInfo[$jfav.'_required'];
	}
}
if(count($JoinFlowV) > 0) {
	if(array_search('N', array_values($JoinFlowV)) === false) { // 모든 항목이 필수 일때
		foreach($JoinFlowV as $jfk=>$jfv) {
			$privacy_table['회원가입'][0]['item'][] = $jfk;
		}
	}
	else if(array_search('Y', array_values($JoinFlowV)) === false) { // 모든 항목이 비필수 일때

		// 비필수 항목 값 추가
		$privacy_table['회원가입'][1] = array();
		$privacy_table['회원가입'][1]['required'] = 'N';
		$privacy_table['회원가입'][1]['name'] = '상품 배송';
		$privacy_table['회원가입'][1]['destruction'] = '회원 탈퇴 이후 부정 이용을 방지하기 위해 1년간 보존';
		$privacy_table['회원가입'][1]['item'] = array();
		foreach($JoinFlowV as $jfk=>$jfv) {
			$privacy_table['회원가입'][1]['item'][] = $jfk;
		}
	}
	else { // 항목이 섞여 있는 경우

		// 필수, 비필수 항목 값 추가
		$privacy_table['회원가입'][1] = array();
		$privacy_table['회원가입'][1]['required'] = 'N';
		$privacy_table['회원가입'][1]['name'] = '상품 배송';
		$privacy_table['회원가입'][1]['destruction'] = '회원 탈퇴 이후 부정 이용을 방지하기 위해 1년간 보존';
		$privacy_table['회원가입'][1]['item'] = array();
		foreach($JoinFlowV as $jfk=>$jfv) {
			$privacy_table['회원가입'][($jfv == 'Y'?'0':'1')]['item'][] = $jfk;
		}
	}
}


# 이용약관 다음 페이지 설정 -> 2018-10-04 SSJ :: 본인인증 위치 member.join.form.php로 위치변경
//if($siteInfo['s_join_auth_use'] == 'Y') $next_pn = 'member.join.auth'; // 휴대폰인증 사용시
//else $next_pn = 'member.join.form';
$next_pn = 'member.join.form';


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행