<?php
$SNSField = array(
	'facebook'=>array( // key의 명칭으로 폴더 생성
		'name'=>'페이스북', // 로그인 명칭
		'config_use'=>'s_facebook_login_use',//smart_setup 필드 - sns 사용여부
		'config_key'=>'s_facebook_key', // 환경 설정 테이블의 앱 아이디 DB필드명
		'config_secret'=>'s_facebook_secret', //환경 설정 테이블의 앱 시크릿 DB필드 명(없는경우 nope으로 처리 하고 콜백에서 사용여부 수정)
		'join'=>'fb_join', // smart_individual 필드 // 가입여부 DB필드 명
		'id'=>'fb_encid',//가입 아이디 DB필드 명
		'short'=>'fb_'//아이디 접두사
	), // 페이스북 관련 데이터 필드
	'kakao'=>array(
		'name'=>'카카오',
		'config_use'=>'kakao_login_use',
		'config_key'=>'kakao_api',
		'config_secret'=>'nope',
		'join'=>'ko_join',
		'id'=>'ko_encid',
		'short'=>'ko_'
	), // 카카오 관련 데이터 필드
	'naver'=>array(
		'name'=>'네이버',
		'config_use'=>'nv_login_use',
		'config_key'=>'nv_login_key',
		'config_secret'=>'nv_login_secret',
		'join'=>'nv_join',
		'id'=>'nv_encid',
		'short'=>'nv_'
	), // 네이버 관련 데이터 필드
);

// SNS 회원가입 처리 함수
function SNSAutoJoin($sns_info) {
	global $SNSField, $system, $siteInfo;

	//if(!$sns_info['type'] || !$sns_info['id'] || !$sns_info['name'] || !$sns_info['email']) error_msgPopup('연동 과정중 에러가 발생하였습니다.\\n다시시도 바랍니다.');
	if(!$sns_info['type'] || !$sns_info['id'] || !$sns_info['name']) error_msgPopup('연동 과정중 에러가 발생하였습니다.\\n다시시도 바랍니다.');
	$join_id = $SNSField[$sns_info['type']]['short'].$sns_info['id'];

	// LCY : 2022-01-10 : SNS 고유 식별자 패치
	if( mb_strlen($join_id) > 30 ){ $join_id = creat_num_uniqueID($SNSField[$sns_info['type']]['short']); }

	$FindID = _MQ(" select * from smart_individual where in_id = '{$join_id}' ");
	if($FindID['in_out'] == 'Y') error_msgPopup('탈퇴 된 아이디 입니다.\\n회원가입 후 연동 하거나 다른 SNS로그인을 이용바랍니다.');
	$join_email = $sns_info['email'];
	$join_name = addslashes($sns_info['name']);
	$join_emailsend = 'Y'; // 이메일 수신여부 기본값
	$join_smssend = 'N'; // 문자 수신여부 기본값
	$sque = "
		  in_id = '{$join_id}'
		, in_pw = '{$join_id}'
		, in_email = '{$join_email}'
		, in_name = '{$join_name}'
		, in_emailsend = '{$join_emailsend}'
		, in_ip = '{$_SERVER['REMOTE_ADDR']}'
		, in_rdate = now()
		, in_mdate = now()
		, in_ldate = now()
		, in_pw_rdate = now()
		, m_opt_date = now()
		, sns_join = 'Y'
		, {$SNSField[$sns_info['type']]['join']} = 'Y'
		, {$SNSField[$sns_info['type']]['id']} = '{$sns_info['id']}'
	";
	if($siteInfo['join_approve'] == 'N') $sque .= " , in_auth = 'N' "; // 승인후 로그인 처리
	_MQ_noreturn(" insert smart_individual set {$sque} ");

	// 로그인 쿠키 적용 - 로그인 처리
	if($siteInfo['join_approve'] == 'Y') { // 승인후 로그인 처리
		samesiteCookie('AuthIndividualMember', $join_id, 0, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	}
	if($_COOKIE['AuthSNSEncID']) samesiteCookie('AuthSNSEncID', '', time()-3600 , '/', '.'.str_replace('www.', '', reset(explode(':', $system['host'])))); // SNS 고유정보 제거(잔여데이터)

	// 로그인 체크
	//loginchk_insert($join_id, 'individual'); // 2019-10-02 SSJ :: 로그인 체크 위치 변경

	// - 메일발송 ---
	if(mailCheck($join_email) ){

		/*
		    # 2016-08-29 스팸방지 추가
		    # $id 변수를 받아서 처리
			# $_mailling / $_sms 정보 있어야 함.
		*/
		$_mailling = $join_emailsend; // 이메일
		$_sms = $join_smssend; // 문자
		$id = $join_id;

		// $join_id ==> 적용
		include_once(OD_MAIL_ROOT.'/member.join.mail.php'); // 메일 내용 불러오기 ($mailing_content)
		$_title = '['.$siteInfo['s_adshop'].'] SNS 회원가입을 환영합니다.';
		$_title_img = '/pages/images/mailing/title_join.gif';


		$_content = get_mail_content($mailling_content);
		mailer($join_email, $_title, $_content);
	}
	// - 메일발송 ---

	// 회원가입 축하 적립금
	shop_pointlog_insert($join_id, '회원가입', $siteInfo['s_joinpoint'], 'N', $siteInfo['s_joinpointprodate']);

	// 회원가입 쿠폰 발급
	couponIssuedAutoType4($join_id);
	if($siteInfo['join_approve'] == 'N') { // 승인후 로그인 처리
		die("<script language='javascript'>opener.location.href=('/');alert('소셜로그인 가입이 환영합니다.\\n로그인은 관리자 승인 후 가능합니다.');window.close();</script>");
	}
	else {
		// 2019-10-02 SSJ :: sns로그인 시 장바구니 쿠키 변경 추가
		// 로그인 체크
		loginchk_insert($join_id, 'individual');

		_MQ_noreturn("update smart_cart set c_cookie='". $join_id ."' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");
		samesiteCookie("AuthShopCOOKIEID", $join_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		// 2019-10-02 SSJ :: sns로그인 시 장바구니 쿠키 변경 추가

		UserLogin($join_id); // 세션로그인
		die("<script language='javascript'>opener.location.href=('/?pn=member.join.complete');alert('소셜로그인 가입이 완료되었습니다.\\n정보수정에서 부가정보를 입력해주세요.');window.close();</script>");
	}
}
addHook('sns_join.start', 'SNSAutoJoin');


// SNS 회원탈퇴 처리 함수
function SNSAutoLeave($mem_info) {
	global $SNSField, $system, $siteInfo;

	// --query 사전 준비 ---
	$sque = "
		 in_name = '탈퇴회원'
		, in_email = ''
		, in_emailsend = 'N'
		, in_smssend = 'N'
		, in_tel = ''
		, in_odate = now()
		, in_pw = '탈퇴회원'
		, in_out = 'Y'
		, in_point = '0'
		, sns_join = 'N'
		, fb_join = 'N'
		, fb_encid = ''
		, ko_join = 'N'
		, ko_encid = ''
		, nv_join = 'N'
		, nv_encid = ''
		, in_out_type  = 'member'
	";
	_MQ_noreturn(" update `smart_individual` set {$sque} where in_id = '".get_userid()."' ");
	samesiteCookie('AuthIndividualMember', '', time() - 3600 , '/' , '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	samesiteCookie('AuthShopCOOKIEID', md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	samesiteCookie('SNSOauthMode', '', time() - 3600 , '/' , '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	UserLogout(); // 세션로그아웃
	die("<script language='javascript'>opener.location.href=('/');alert('정상적으로 탈퇴처리하였습니다.\\n\\n그동안 이용해 주셔서 감사합니다.');window.close();</script>");
}
addHook('sns_leave.end', 'SNSAutoLeave');