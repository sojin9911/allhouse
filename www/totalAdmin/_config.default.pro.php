<?php
include_once('inc.php');


// 초기값 설정
$sql_que = '';
$_view_network_company_info = ($_view_network_company_info == 'Y'?'Y':'N'); // 사업자 정보 노출
$_naver_switch = ($_naver_switch == 'Y'?'Y':'N'); // 네이버 EP 전체적용
$_daum_switch = ($_daum_switch == 'Y'?'Y':'N'); // 다음 EP 전체적용

// 사전체크
$_adid = nullchk($_adid, '관리자 아이디를 입력해 주세요.');
$_glbtel = nullchk($_glbtel, '대표번호를 입력해 주세요.');
$_glbmanagerhp = nullchk($_glbmanagerhp, '관리자 휴대폰을 입력해 주세요.');
if(checkInputValue($_glbmanagerhp, 'htel') !== true) error_msg('관리자 휴대폰 형식이 올바르지 않습니다.');
$_glbmanagerhp = tel_format($_glbmanagerhp);
$_ademail = nullchk($_ademail, '대표 이메일을 입력해 주세요.');
if(checkInputValue($_ademail, 'email') !== true) error_msg('이메일 형식이 올바르지 않습니다.');
/*
$_adshop = nullchk($_adshop, '사이트명을 입력해 주세요.');
$_company_name = nullchk($_company_name, '회사명을 입력해 주세요.');
$_ceo_name = nullchk($_ceo_name, '대표자명을 입력해 주세요.');
$_company_num = nullchk($_company_num, '사업자등록번호를 입력해 주세요.');
$_company_snum = nullchk($_company_snum, '통신판매신고번호를 입력해 주세요.');
$_item1 = nullchk($_item1, '업태를 입력해 주세요.');
$_item2 = nullchk($_item2, '종목을 입력해 주세요.');
$_company_addr = nullchk($_company_addr, '주소를 입력해 주세요.');
$_privacy_name = nullchk($_privacy_name, '개인정보관리책임자를 입력해 주세요.');
$_cs_info = nullchk($_cs_info, '고객센터 운영시간을 입력해 주세요.');
$_login_page_phone = nullchk($_login_page_phone, '관리자 로그인 페이지- 고객센터 전화번호를 입력해 주세요.');
$_login_page_email = nullchk($_login_page_email, '관리자 로그인 페이지- 관리자 이메일주소를 입력해 주세요.');
if(checkInputValue($_login_page_email, 'email') !== true) error_msg('관리자 로그인 페이지- 관리자 이메일주소 형식이 올바르지 않습니다.');
*/

$rowAdminChk = _MQ("select count(*) as cnt from smart_admin where a_id = '".$_adid."' and a_type = 'admin'");
if( $rowAdminChk['cnt'] > 0){ error_msg("이미 등록된 운영자 아이디 입니다.");  }

if($_change_apw == 'Y') {
	if($_adpwd != $_adpwd_ck) error_msg('비밀번호와 비밀번호확인이 일치하지 않습니다.');
	$sql_que .= ", s_adpwd = password('{$_adpwd}') ";

	// -- smart_amdin 테이블 업데이트
	$queAdminModify .= " , a_pw = password('".$_adpwd."') ";
}

// 입점업체 기능이 off라면 기본설정 값에서 수시로 입점업체 정보를 업데이트 한다.
if($SubAdminMode === false) {
	_MQ_noreturn("
		update smart_company set
			  cp_name = '{$_adshop}'
			, cp_number = '{$_company_num}'
			, cp_ceoname = '{$_ceo_name}'
			, cp_address = '{$_company_addr}'
			, cp_item1 = '{$_item1}'
			, cp_item2 = '{$_item2}'
			, cp_charge = '{$_ceo_name}'
			, cp_email = '{$_ademail}'
			, cp_tel = '{$_glbtel}'
			, cp_tel2 = '{$_glbmanagerhp}'
			, cp_delivery_use = 'N'
		where cp_id = 'hyssence'
	");
}

// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====
$sql_que .= "	, s_none_member_buy = '".$_none_member_buy."' " ;
// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====

// -- 2020-03-25 SSJ :: 비회원 바로구매 시 로그인 페이지 경유 설정 추가 ----
if(IsField('smart_setup', 's_none_member_login_skip') === false){
	$add_column = array('Field'=>'s_none_member_login_skip','Type'=>'enum(\'Y\',\'N\') CHARACTER SET utf8 COLLATE utf8_general_ci','Default'=>'N','Null'=>'NO','Extra'=>'COMMENT  \'비회원 바로구매 시 로그인 페이지 경유 여부(Y-바로구매, N-로그인페이지 경유)\'');
	AddFeidlUpdate('smart_setup',$add_column);
}
$sql_que .= "	, s_none_member_login_skip = '".$_none_member_login_skip."' " ;
// -- 2020-03-25 SSJ :: 비회원 바로구매 시 로그인 페이지 경유 설정 추가 ----

// 2019-11-23 SSJ :: 대표도메인 설정 추가
$sql_que .= "	, s_ssl_domain = '". str_replace(array("http://","https://"), "", $_ssl_domain) ."'" ;
// 2019-11-23 SSJ :: 대표도메인 설정 추가

// 설정값 업데이트
$que = "
	update smart_setup set
		  s_adid = '{$_adid}'
		, s_glbtel = '{$_glbtel}'
		, s_glbmanagerhp = '{$_glbmanagerhp}'
		, s_ademail = '{$_ademail}'
		, s_adshop = '{$_adshop}'
		, s_company_name = '{$_company_name}'
		, s_ceo_name = '".trim($_ceo_name)."'
		, s_company_num = '{$_company_num}'
		, s_company_snum = '{$_company_snum}'
		, s_item1 = '{$_item1}'
		, s_item2 = '{$_item2}'
		, s_company_addr = '{$_company_addr}'
		, s_fax = '{$_fax}'
		, s_view_network_company_info = '{$_view_network_company_info}'
		, s_privacy_name = '{$_privacy_name}'
		, s_cs_info = '{$_cs_info}'
		, s_login_page_phone = '{$_login_page_phone}'
		, s_login_page_email = '{$_login_page_email}'
		, member_login_cnt = '{$member_login_cnt}'
		, s_naver_switch = '{$_naver_switch}'
		, s_daum_switch = '{$_daum_switch}'
		, s_glbtlt = '{$_glbtlt}'
		, s_glbdsc = '{$_glbdsc}'
		, s_glbkwd = '{$_glbkwd}'
		, s_gmeta = '{$_gmeta}'
		{$sql_que}
	where s_uid = '1'
";
_MQ_noreturn($que);

// -- smart_admin 의 master 아이디도 업데이트 해준다.
_MQ_noreturn("update smart_admin set a_id = '".$_adid."' ".$queAdminModify." where a_type = 'master' ");

// 설정페이지 이동
error_loc('_config.default.form.php');