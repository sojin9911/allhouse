<?php
# 스킨의 파일을 바로 부를 경우 사용
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// LDD: 2019-01-18 네이버페이 패치 - 1시간에 1번씩 네이버페이 체크
if(file_exists(OD_ADDONS_ROOT.'/npay/_npay_order_sync.php') && $siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real') {
	if(strtotime($siteInfo['npay_sync_date'].'+1 hours') <= time()) {
		curl_async('http://'.$system['host'].OD_ADDONS_DIR.'/npay/_npay_order_sync.php');
	}
}


// 1일 1회 처리
if($siteInfo['daily_update_date'] != date('Y-m-d', time())) {

	// 기본 프로세스
	curl_async('http://'.$system['host'].OD_PROGRAM_DIR.'/_1day.update.php');

	// [JJC] 2년마다 수신동의
	if($siteInfo['s_2year_opt_use'] == 'Y') {
		curl_async('http://'.$system['host'].OD_ADDONS_DIR.'/2yearOpt/inc.2year_opt.php');
	}

	// LCY :: 2017-12-08 -- 회원 등급업데이트
	if(date('Y-m-d',strtotime($siteInfo['groupset_apply_rdate'])) != date('Y-m-d', time()) && $siteInfo['groupset_autouse'] == 'Y' ) {
		curl_async('http://'.$system['host'].OD_PROGRAM_DIR.'/inc.member_groupset_auto.php');
	}
}


# 2017-07-12 ::: SSL 페이지 변별 후 자동이동 - 보안서버 ::: JJC
function AutoHttpsMoveHook() {
	global $_device_mode;
	AutoHTTPSMove($_device_mode);
}
addHook('wrap.header.php.start', 'AutoHttpsMoveHook'); // 후킹 등록