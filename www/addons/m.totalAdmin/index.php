<?php
include_once('inc.php');


# 로그인 후 다시 메인 접근시 로그인 skip 처리
if($_COOKIE['AuthAdmin']) $userType = 'master';

if($userType == "master") {

	$row = _MQ(" select * from smart_admin where a_id = '{$_id}' and a_pw = password('{$_pw}') "); // -- 운영자 검색
	if(!$row['a_id'] && $_COOKIE['AuthAdmin']) { // 통합관리자에 이미 로그인 중 이라면 바로 이동
		error_loc('_intro.php');
	}
	else if($row['a_id'] && $_id == $row['a_id'])  {
		if($row['a_use'] == 'N') error_msg('승인되지 않은 운영자 계정입니다.'); // -- 미승인일경우
		samesiteCookie('AuthAdmin', $row['a_uid'], 0, '/');
		AdminLogin($row['a_uid']); // 통합관리자 세션 로그인 처리
	}
	else {
	  if($_id != '' || $_pw != '') error_msg('입력하신 정보가 맞지않습니다.\\n\\n Caps Lock, 한/영 키의 상태를 확인하시고 다시 입력하여 주십시오.');
	}
	if(($_id == $row['a_id'] || db_password($_pw) == $row['a_pw']) || trim($siteAdmin['a_id']) != '') error_loc('_intro.php');
}

$_login_trigger = 'N'; // 로그인 필요없는 페이지 표시
include_once('inc.header.php');
?>
<body class="login_bg">
	<div class="member_login_wrap">
		<div class="floating">
			<!-- ●●●●●●●●●● 로그인 -->
			<div class="cm_member_login">
				<form name="frm_login_page" method="post" action="<?=$_SERVER["PHP_SELF"]?>" autocomplete="off">
					<input type="hidden" name="userType" value="master">
					<div class="form_box">
						<div class="title_box">Total Admin Login</div>
						<ul>
							<li class="login_id"><input type="text" name="_id" class="input_design" placeholder="관리자 아이디"/></li>
							<li class="login_pw"><input type="password" name="_pw" class="input_design " placeholder="관리자 비밀번호" autocomplete="new-password" /></li>
						</ul>
						<input type="submit" name="" class="btn_login" value="LOGIN"/>
					</div>
				</form>

				<div class="copyright">
					<dl>
						<dt>
							본 페이지는 전체 관리자 모바일 버전입니다.<br/>
							인증 획득시 정보에 대한 보안을 반드시 지키셔야 하며 어길시<br/>
							민형사상의 책임을 질 수 있습니다.<br/>
							입점업체 및 더 자세한 관리를 위해서는 PC버전을 이용하세요.<br/>
						</dt>
						<dd>&copy; <?php echo $siteInfo['s_adshop']; ?>. ALL RIGHTS RESERVED.</dd>
					</dl>
				</div>
			</div>
			<!-- / 로그인 -->
		</div>
	</div>
</body>
<?php include_once('inc.footer.php'); ?>