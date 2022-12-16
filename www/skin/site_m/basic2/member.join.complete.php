<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
if(!is_login()) error_loc("/?pn=member.login.form");
$page_title = '회원가입'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_member">
	<!-- ◆회원가입완료 -->
	<div class="c_complete">
		<div class="complete_box">
			<div class="sub_txt">회원가입이 완료되었습니다.<br/>저희 사이트의 모든 서비스를 이용하실 수 있습니다.</div>
			<div class="tit"><strong><?php echo $mem_info['in_name']; ?></strong>님의 회원가입을 진심으로 환영합니다.</div>
		</div>
		<div class="c_btnbox ">
			<ul>
				<li><a href="/" class="c_btn h55 black ">홈으로</a></li>
				<li><a href="/?pn=mypage.main" class="c_btn h55 black line">마이페이지</a></li>
			</ul>
		</div>
	</div>
	<!-- /회원가입완료 -->
</div>
<!-- /공통페이지 섹션 -->