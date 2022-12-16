<?PHP
include "../include/inc.php";
include_once('inc.header.php');

// --- JJC004 - 모바일 검출에 모바일 전용 관리자페이지 불러오기 ---
if($_REQUEST['_pcmode']=="chk") {
	samesiteCookie("AuthAdminNoMobile","chk",0,"/" , "." . str_replace("www." , "" , $system['host']));
}
else if($_REQUEST['_mobilemode']=="chk") {
	samesiteCookie("AuthAdminNoMobile","",time()-3600,"/" , "." . str_replace("www." , "" , $system['host']));
	error_loc("/addons/m.totalAdmin/");
}
else {
	require_once '../include/Mobile_Detect/Mobile_Detect.php';
	$detect = new Mobile_Detect;
	if ( $detect->isMobile() ) {
		error_loc("/addons/m.totalAdmin/");
	}
}
// --- JJC004 - 모바일 검출에 모바일 전용 관리자페이지 불러오기 ---


# 로그인 후 다시 메인 접근시 로그인 skip 처리
if(($_COOKIE["AuthAdmin"] || $_COOKIE["AuthCompany"]) && $_mode != 'autologin') {
	if($_COOKIE["AuthAdmin"]) $userType = 'master';
	else $userType = 'com';
}


if($userType == "com") {

	if($_mode=='autologin' && $_COOKIE["AuthAdmin"]) { $row = _MQ("SELECT * FROM smart_company WHERE cp_id = '$_id'"); }
	else { $row = _MQ("SELECT * FROM smart_company WHERE cp_id='$_id' and cp_pw=password('$_pw')"); }
	if(sizeof($row) == 0 ) {
		error_msg('입력하신 아이디나 비밀번호가 일치하지 않습니다.\\n\\n다시 입력해 주세요.');
	} else {
		samesiteCookie("AuthCompany", $_id,0,"/");
		error_loc("../subAdmin/_attach/_product.list.php?menu_idx=2");
	}

} else if($userType == "master") {

	// -- 운영자 검색
	$row = _MQ("select *from smart_admin where a_id = '".$_id."' and a_pw = password('".$_pw."') ");

	if($_id == $row[a_id])  {
		samesiteCookie("AuthAdmin", $row[a_uid],0,"/");
		AdminLogin($row[a_uid]);
	}
	else {
	  if( $_id != "" || $_pw != "") error_msg('입력하신 정보가 맞지않습니다.\\n\\n Caps Lock, 한/영 키의 상태를 확인하시고 다시 입력하여 주십시오.');
	}


	if(
		($_id == $row[a_id] || $_pw == $row[a_pw] )
		||
		trim($siteAdmin['a_id']) != ''
	)  {
		  error_loc(OD_ADMIN_URL."/_main.php?menu_idx=1");
	}

}	else	{

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="kr" lang="kr" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>관리자페이지에 오신것을 환영합니다.</title>
	<link href="./css_del/adm_style.css" rel="stylesheet" type="text/css" />
	<SCRIPT src="../include/js/jquery-1.7.1.min.js"></SCRIPT>
	<SCRIPT src="../include/js/jquery/jquery.validate.js"></SCRIPT>
</head>
<body>
<div id="login">

	<!-- 로그인폼 전체하얀박스 -->
	<div class="box">

		<!-- 로그인이미지 -->
		<div class="left"><img src="./images_del/login_img.gif" alt="로그인이미지" title="" /></div>

		<!-- 로그인폼 -->
		<div class="right">

<form name=frm method=post action=<?=$PHP_SELF?>>
			<div class="form_box">

				<div class="type_choice">
					<label><input type="radio" name="userType" value="master" checked> 관리자</label>
					<?php if($SubAdminMode === true) { ?>
					&nbsp;&nbsp;&nbsp;
					<label><input type="radio" name="userType" value="com"> 입점업체</label>
					<?php } ?>
				</div>

				<div class="input_box">
					<input type="text" name="_id" class="input_text" value="ID" />
					<input type="text" name="_pw" class="input_text" value="PASSWORD" />
				</div>

				<span class="btn_login"><input type="submit" name="" value="" class="btn" /></span>
			</div>
</form>


		</div>
		<!-- // 로그인폼 -->

	</div>
	<!-- //로그인폼 전체하얀박스 -->

	<!-- 업체 카피라잇 -->
	<div class="copyright">&copy; ONEDAYNET.CO.KR. ALL RIGHTS RESERVED.</div>

	<!-- 경고문구 -->
	<div class="warning">
		<span class="icon"></span>
		<div class="text">
			<ul>
				<li>본 페이지는 관리자 인증 페이지 입니다.</li>
				<li>익스플로러 8.0 이상, 해상도 1280 * 1024 에 최적화 되었습니다.</li>
				<li>인증 획득시 정보에 대한 보안을 반드시 지키셔야 하며 어길시 민형사상의 책임을 질 수 있습니다. </li>
				<? if($siteInfo[s_login_page_phone]||$siteInfo[s_login_page_email]) { ?>
				<li>유지보수 및 사용상 문의사항 - <?="고객센터: <b>".$siteInfo[s_login_page_phone]."</b>"?> <?="이메일문의: <b>".$siteInfo[s_login_page_email]."</b>"?></li>
				<? } ?>
			</ul>
		</div>
	</div>

</div>
</body>
</html>




<SCRIPT LANGUAGE="JavaScript">
    $(document).ready(function(){

		// - 아이디 클릭적용 ---
		$('input[name=_id]')
			.blur(function(){($(this).val() == "") ? $(this).val("ID") : "";})
			.focus(function() {($(this).val() == "ID") ? $(this).val("") : "";});
		// - 아이디 클릭적용 ---

		$('input[name=_id]').trigger('focus');

		// - 로그인 박스 validate ---
        $("form[name=frm]").validate({
            rules: {
                _id: {required: function() {($("input[name=_id]").val() == "ID") ? $("input[name=_id]").val("") : ""; return true; }},
                _pw: {required: function() { ($("input[name=_pw]").val() == "PASSWORD") ? $("input[name=_pw]").val("") : "";return true;}}
            },
            messages: {
                _id: { required: "ID를 입력해주시기 바랍니다." },
                _pw: { required: "PASSWORD를 입력해주시기 바랍니다."}
            }
        });
		// - 로그인 박스 validate ---
	});

	// - jquery validator 경고창 띄우기 (jquery validate 공통) ---
	jQuery.validator.setDefaults({
		onkeyup:false,
		onclick:false,
		onfocusout:false,
		showErrors:function(errorMap, errorList){
			var caption = $(errorList[0].element).attr('name');
			alert(errorList[0].message);
		}
	});
	// - jquery validator 경고창 띄우기 (jquery validate 공통) ---

</SCRIPT>
<script type="text/javascript" src="../include/js/login_passwordtotext.js"></script><!-- 익스플로서에서 this.type='password' 적용안되는 오류 처리 js -->

<?PHP
	}
?>