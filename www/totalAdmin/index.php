<?php
include_once('inc.php');

// -- 통합관리자 로그인 페이지 보안서버 추가 2019-07-03 LCY --
AutoHTTPSMove('admin');
// -- 통합관리자 로그인 페이지 보안서버 추가 2019-07-03 LCY --


// --- JJC004 - 모바일 검출에 모바일 전용 관리자페이지 불러오기 ---
if($_REQUEST['_pcmode'] == 'chk') {
	samesiteCookie('AuthAdminNoMobile', 'chk', 0, '/', '.'.str_replace('www.', '', $system['host']));
}
else if($_REQUEST['_mobilemode'] == 'chk') {
	samesiteCookie('AuthAdminNoMobile', '', time()-3600, '/', '.'.str_replace('www.', '', $system['host']));
	error_loc(OD_ADDONS_URL.'/m.totalAdmin/_intro.php');
}
else {
	require_once($_SERVER['DOCUMENT_ROOT'].'/include/Mobile_Detect/Mobile_Detect.php');
	$detect = new Mobile_Detect;
	if($detect->isMobile()) error_loc(OD_ADDONS_URL.'/m.totalAdmin/_intro.php');
}
// --- JJC004 - 모바일 검출에 모바일 전용 관리자페이지 불러오기 ---



# 로그인 후 다시 메인 접근시 로그인 skip 처리
if(($_COOKIE['AuthAdmin'] || $_COOKIE['AuthCompany']) && $_mode != 'autologin') {
	if($_COOKIE['AuthAdmin']) $userType = 'master';
	else $userType = 'com';
}



// 공백 제거
$_id = trim($_id);
$_pw = trim($_pw);

# 로그인 처리
if($userType == 'com') { // 입점로그인

	if($_mode=='autologin' && $_COOKIE['AuthAdmin']) $row = _MQ(" select * from smart_company where cp_id = '{$_id}' ");
	else if($_id && $_pw ) $row = _MQ(" select * from smart_company where cp_id = '{$_id}' and cp_pw = password('{$_pw}'); ");

	if(count($row) <= 0 && $_COOKIE['AuthCompany']) {
		error_loc(OD_SUB_ADMIN_URL.'/_product.list.php'); // 이미 입점업체 로그인 중 이라면 바로 이동
	}
	else if(count($row) <= 0) {
		error_msg('입력하신 아이디나 비밀번호가 일치하지 않습니다.\\n\\n다시 입력해 주세요.');
	}
	else {
		samesiteCookie('AuthCompany', $_id, 0, '/');
		SubAdminLogin($_id); // 입점관리자 세션 로그인 처리
		error_loc(OD_SUB_ADMIN_URL.'/_product.list.php');
	}
}
else if($userType == "master") { // 관리자로그인

	if($_id && $_pw ) $row = _MQ(" select * from smart_admin where a_id = '{$_id}' and a_pw = password('{$_pw}') "); // -- 운영자 검색
	if(!$row['a_id'] && $_COOKIE['AuthAdmin']) { // 통합관리자에 이미 로그인 중 이라면 바로 이동
		error_loc(OD_ADMIN_URL.'/_main.php');
	}
	else if($row['a_id'] && $_id == $row['a_id'])  {
		if($row['a_use'] == 'N') error_msg('승인되지 않은 운영자 계정입니다.'); // -- 미승인일경우
		samesiteCookie('AuthAdmin', $row['a_uid'], 0, '/');
		AdminLogin($row['a_uid']); // 통합관리자 세션 로그인 처리
	}
	else {
	  if($_id != '' || $_pw != '') error_msg('입력하신 정보가 맞지않습니다.\\n\\n Caps Lock, 한/영 키의 상태를 확인하시고 다시 입력하여 주십시오.');
	}
	if(($_id == $row['a_id'] || db_password($_pw) == $row['a_pw']) || trim($siteAdmin['a_id']) != '') error_loc(OD_ADMIN_URL.'/_main.php');
}
# 파비콘
$Favicon = info_banner('site_favicon', 1, 'data');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title><?=$siteInfo['s_adshop']?> 관리자</title>
	<!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width">


	<?php if($Favicon[0]['b_img']) { ?>
		<!-- 홈아이콘 -->
		<link rel="apple-touch-icon-precomposed" href="<?php echo IMG_DIR_BANNER.$Favicon[0]['b_img']; ?>" />
		<link rel="shortcut icon" href="<?php echo IMG_DIR_BANNER.$Favicon[0]['b_img']; ?>" type="image/x-icon"/>
	<?php echo PHP_EOL; } ?>


	<!-- 자동으로 전화 링크되는것 방지 -->
	<meta name="format-detection" content="telephone=no" />

	<!-- 디자인css 순서지킬것 -->
	<link href="<?php echo OD_ADMIN_URL; ?>/css/default_setting.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/login.css" rel="stylesheet" type="text/css" />

	<script src="/include/js/jquery-1.7.1.min.js"></script>
</head>
<body>
<!-- 로그인전체 -->
<div class="login_wrap">
	<!-- 중앙으로 띄우는 영역 -->
	<div class="floating_box">
		<!-- 쇼핑몰이름 -->
		<div class="login_sitename"><?=$siteInfo['s_adshop']?></div>
		<ul class="table">
			<li class="left_box">
				<div class="visual">
					<div class="tt">TOTAL ADMIN</div>
					<div class="st">본 페이지는 인증이 필요한 관리자 페이지 입니다.</div>
					<div class="cs">SERVICE CENTER : <?=$siteInfo['s_glbtel']?></div>
					<div class="cs">E-MAIL : <?php echo $siteInfo['s_login_page_email']; ?></div>
					<!-- 쇼핑몰 바로가기 버튼 -->
					<a href="/" class="shop_go" target="_blank" title="내 쇼핑몰 바로가기"><span class="txt">내 쇼핑몰 바로가기</span></a>
				</div>
			</li>
			<li class="right_box">
				<div class="form_box">
					<form name="form_login" id="form_login" action="<?=$PHP_SELF?>" method="post" autocomplete="off">
					<div class="tt">관리자 로그인</div>

					<?php if( $SubAdminMode === true) { ?>
					<!-- 통합관리자,입점관리자 / 입점 관리자 없을 경우에는 log_tab이 없어지면 됩니다. -->
					<div class="log_tab">
						<label class="tab_btn">
							<input type="radio" name="userType" value="master" class='js' checked=""/>
							<span class="tab">통합 관리자</span>
						</label>

						<label class="tab_btn">
							<input type="radio" name="userType" class='js' value="com" />
							<span class="tab">입점 관리자</span>
						</label>
					</div>
					<?php }else{ ?>
					<input type="hidden" name="userType" value="master">
					<?php } ?>

					<ul class="log_box">
						<li><span class="tx id"></span><input type="text" name="_id" class="design js" placeholder="아이디"/></li>
						<li>
							<span class="tx pw"></span><input type="password" name="_pw" class="design js" autocomplete="new-password" placeholder="비밀번호"/>
							<!-- 비밀번호 분실 가이드 -->
							<div class="log_guide">
								<span class="guide_btn"></span>
								<div class="guide_box">
									<div class="pw_tt">비밀번호 분실 시</div>
									<?php // - 통합 관리자 : 원데이넷 별도 문의<br/> ?>
									<?php if( $SubAdminMode === true) { ?>
									- 입점 관리자 : 쇼핑몰 고객센터로 직접 문의<br/>
									<?php } ?>
									- 쇼핑몰 고객센터 : <em><?=$siteInfo['s_glbtel']?></em>
								</div>
							</div>
						</li>
					</ul>
					<input type="submit" name="" class="btn_login" value="LOGIN" onclick="form_login.submit();" />
					</form>
				</div>
			</li>
		</ul>
		<div class="copyright">ⓒ <?=$siteInfo['s_adshop']?> ALL RIGHTS RESERVED.</div>
	</div>
	<!-- / 중앙으로 띄우는 영역 -->
</div>
<!-- / 로그인전체 -->



</body>
</html>

