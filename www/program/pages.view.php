<?php
# 게시판 댓글등록
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 2019-11-29 SSJ :: 단독 메뉴 노출 구분 추가
$trigger_only_menu = false;
$current_menu = _MQ(" select * from smart_normal_page where np_id = '". $data ."' ");

// 2019-11-29 SSJ :: 노출 메뉴 추가
if($current_menu['np_menu'] == ''){
	// 한번더 체크
	$trigger_colmn_menu = false;
	$chk = _MQ_assoc(" desc smart_normal_page ");
	if(count($chk) > 0){
		foreach($chk as $k=>$v){
			if($v['Field'] == 'np_menu'){
				$trigger_colmn_menu = true;
				break;
			}
		}
	}
	// db 추가
	if($trigger_colmn_menu === false){
		_MQ_noreturn(" alter table smart_normal_page add column `np_menu` varchar(30) not null default 'default' comment '노출메뉴' ");
		_MQ_noreturn(" alter table smart_normal_page add index(`np_menu`) ");
	}
}

// 2019-11-29 SSJ :: 메뉴 구분 추가에 따른 $type 재설정
$type = 'agree';
if($current_menu['np_menu'] <> '') $type = $current_menu['np_menu'] == 'agree' ? 'agree' : '';

$agree_nomal_page = array('company'); // 약관페이지에 포함되는 일반 페이지 아이디
$page_content = array(); // 페이지에 노출되는 내용
$page_menu = array(); // 페이지 메뉴
if($type == 'agree') {
	/*
		# 약관관련 페이지
		- 회사소개 => 디자인 - 일반페이지 관리 - company 아이디의 일반페이지(없으면 비노출 처리 필요)
		- 이용안내 => 환경설정 - 이용안내 설정
		- 이용약관 => 환경설정 - 약관 및 정책 설정 - 이용약관
		- 개인정보처리방침 => 환경설정 - 약관 및 정책 설정 - 개인정보처리방침 (※ 회원가입에서 사용하는 정보와 다름)
		- 이메일무단수집거부 => 스킨별 수동처리
	*/
	if($data == 'guide') { // 이용안내

		$page_content['title'] = '이용안내';
		$page_content['content'] = (is_mobile()?$siteInfo['s_information_use_mobile']:$siteInfo['s_information_use_pc']);
	}
	else if($data == 'agree') { // 이용약관

		$AgreeInfo = arr_policy($data);
		$page_content['title'] = '이용약관';
		$page_content['content'] = (is_mobile()?$AgreeInfo[$data.'_html_m']['po_content']:$AgreeInfo[$data.'_html']['po_content']);
	}
	else if($data == 'privacy') { // 개인정보처리방침

		$AgreeInfo = arr_policy($data);
		$page_content['title'] = '개인정보처리방침';
		$page_content['content'] = (is_mobile()?$AgreeInfo[$data.'_html_m']['po_content']:$AgreeInfo[$data.'_html']['po_content']);
	}
	else if($data == 'deny') { // 이메일무단수집거부

		$page_content['title'] = '이메일무단수집거부';
		// $page_content['content'] = date('Y-m-d H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'].'/include/config_database.php'));

		// [LCY] 2020-03-04 -- 이메일 무단 수집 금지 -- {
		$AgreeInfo = arr_policy($data);
		$page_content['content'] = (is_mobile()?$AgreeInfo[$data.'_html_m']['po_content']:$AgreeInfo[$data.'_html']['po_content']);
		// [LCY] 2020-03-04 -- 이메일 무단 수집 금지 -- }


	}
	//else if(in_array($data, $agree_nomal_page)) { // 약관페이지에 포함되는 일반 페이지 - 유동적으로 작동되기 때문에 마지막에 실행
	else { // 약관페이지에 포함되는 일반 페이지 - 유동적으로 작동되기 때문에 마지막에 실행

		$page_row = _MQ(" select * from smart_normal_page where np_id ='{$data}' ");
		if($page_row['np_uid'] && $page_row['np_view'] == 'Y') { // 페이지가 노출 상태로 있는 경우만 추가
			$page_content['title'] = $page_row['np_title'];
			if(is_mobile() && $page_row['np_use_content'] == 'Y') $page_row['np_content_m'] = $page_row['np_content']; // PC/MOBILE 상세내용 함께 사용
			$page_content['content'] = (is_mobile()?$page_row['np_content_m']:$page_row['np_content']);
		}
	}

	// 치환자를 변환한다.
	if($page_content['content']) $page_content['content'] = ConfigReplace($page_content['content']);


	// 페이지 메뉴 생성 - 일반페이지 메뉴 추가
	if(count($agree_nomal_page) > 0) {
		$page_row = _MQ_assoc(" select * from smart_normal_page where np_menu = 'agree' and np_view = 'Y' order by np_idx asc ");
		foreach($page_row as $pk=>$pv) {
			$page_menu[] = array(
				'title'=>$pv['np_title'],
				'link'=>'/?pn=pages.view&type=agree&data='.$pv['np_id'],
				'hit'=>($data == $pv['np_id']?true:false)
			);
		}
	}

	// 페이지 메뉴 생성 - 기타페이지 메뉴 추가
	$page_menu[] = array(
		'title'=>'이용안내',
		'link'=>'/?pn=pages.view&type=agree&data=guide',
		'hit'=>($data == 'guide'?true:false)
	);
	$page_menu[] = array(
		'title'=>'이용약관',
		'link'=>'/?pn=pages.view&type=agree&data=agree',
		'hit'=>($data == 'agree'?true:false)
	);
	$page_menu[] = array(
		'title'=>'개인정보처리방침',
		'link'=>'/?pn=pages.view&type=agree&data=privacy',
		'hit'=>($data == 'privacy'?true:false)
	);
	$page_menu[] = array(
		'title'=>'이메일무단수집거부',
		'link'=>'/?pn=pages.view&type=agree&data=deny',
		'hit'=>($data == 'deny'?true:false)
	);
}
else {

	// 접속페이지가 약관쪽 페이지 라면 자동 이동
	//if(in_array($data, $agree_nomal_page)) error_loc('/?pn=pages.view&type=agree&data='.$data, 'top');
	/*
		# 일반페이지
		- 일반페이지 관리 에서 생성 한 페이지중 '약관페이지에 포함되는 일반 페이지 아이디'를 제외한 나머지
	*/
	$page_row = _MQ(" select * from smart_normal_page where np_id = '{$data}' ");
	$page_content['title'] = $page_row['np_title'];
	//$page_content['content'] = (is_mobile()?$siteInfo['s_information_use_mobile']:$siteInfo['s_information_use_pc']);
	if(is_mobile() && $page_row['np_use_content'] == 'Y') $page_row['np_content_m'] = $page_row['np_content']; // PC/MOBILE 상세내용 함께 사용
	$page_content['content'] = (is_mobile()?$page_row['np_content_m']:$page_row['np_content']); // 2018-08-13 SSJ :: 추출한 페이지의 정보를 대입
	if($page_content['content']) $page_content['content'] = ConfigReplace($page_content['content']); // 치환자를 변환한다.

	// 일반 페이지의 메뉴(메뉴는 노출이 걸려있는 페이지만) -- 순서 조정 추가 kms 2019-08-02
	$page_row_menu = _MQ_assoc(" select * from smart_normal_page where np_menu != 'agree' and np_view = 'Y' order by np_idx asc ");
	if(count($page_row_menu) > 0) {
		foreach($page_row_menu as $pk=>$pv) {
			// 2019-11-29 SSJ :: 현재 메뉴가 단독 메뉴인지 체크
			if($data == $pv['np_id'] && $pv['np_menu'] == 'only') $trigger_only_menu = true;

			// 2019-11-29 SSJ :: 현재 메뉴는 텝메뉴에 미포함
			if($pv['np_menu'] == 'only') continue;

			$page_menu[] = array(
				'title'=>$pv['np_title'],
				'link'=>'/?pn=pages.view&type=pages&data='.$pv['np_id'],
				'hit'=>($data == $pv['np_id']?true:false)
			);
		}
	}
}


// 2019-11-29 SSJ :: 단독메뉴이면 텝 메뉴 비노출
if($trigger_only_menu) $page_menu = array();


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행