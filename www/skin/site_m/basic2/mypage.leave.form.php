<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$is_sns_login_form -> SNS로그인 사용여부 => (/program/mypage.leave.form.php에서 지정)
	$sns_join_type -> 메인 회원가입 구분 => (/program/mypage.leave.form.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/mypage.leave.form.php에서 지정)
*/
$page_title = '회원탈퇴'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
	<!-- ◆회원탈퇴 -->
	<div class="c_complete">
		<div class="complete_box">
			<div class="tit">회원탈퇴</div>
			<!-- <div class="tit">소셜계정 회원탈퇴</div> -->
			<div class="sub_txt">
				<strong><?php if($sns_join_type == 'direct') { ?>다음 정보를 입력하고 <?php } ?>회원탈퇴 버튼을 누르면 탈퇴가 완료됩니다.</strong>
				그동안 저희 서비스를 이용하여 주셔서 대단히 감사합니다. <br/>
				더욱 더 개선하여 좋은 서비스와 품질로 보답하겠습니다.
			</div>
		</div>

		<!-- 회원탈퇴 폼 -->
		<div class="leave_form">
			<?php if($sns_join_type == 'direct') { ?>
				<form action="<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php" class="js_leave_form" method="post" target="common_frame" autocomplete="off" onsubmit="return leaveSubmit();">
					<input type="hidden" name="_mode" value="delete">
					<div class="form">
						<ul>
							<li><input type="text" name="leave_id" class="input_design" placeholder="아이디" value="<?php echo $mem_info['in_id']; ?>" readonly/></li>
							<li><input type="password" name="leave_pw" class="input_design " placeholder="비밀번호" autocomplete="new-password"/></li>
						</ul>
						<!-- 회원탈퇴버튼 -->
						<input type="submit" class="btn_find" value="회원탈퇴">
					</div>
				</form>
			<?php } else { ?>
				<div class="c_sns_login">
					<div class="sns_btn">
						<ul>
							<?php
							if($SNSField['naver']['login_use'] == 'Y' && $SNSField['naver']['callback_url']) {
								$sns_callback_url = $SNSField['naver']['callback_url'];
							?>
								<li class="hit">
									<a href="#none" onclick="if(confirm('탈퇴 하시면 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) window.open('<?php echo $sns_callback_url; ?>', 'sns_leave', 'width=800, height=500'); return false;" class="btn naver">
										<span class="sns"></span><span class="en">네이버 회원탈퇴</span>
									</a>
								</li>
							<?php } ?>
							<?php
							if($SNSField['kakao']['login_use'] == 'Y' && $SNSField['kakao']['callback_url']) {
								$sns_callback_url = $SNSField['kakao']['callback_url'];
							?>
								<li class="hit">
									<a href="#none" onclick="if(confirm('탈퇴 하시면 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) window.open('<?php echo $sns_callback_url; ?>', 'sns_leave', 'width=800, height=500'); return false;" class="btn kakao">
										<span class="sns"></span><span class="en">카카오톡 회원탈퇴</span>
									</a>
								</li>
							<?php } ?>
							<?php
							if($SNSField['facebook']['login_use'] == 'Y' && $SNSField['facebook']['callback_url']) {
								$sns_callback_url = $SNSField['facebook']['callback_url'];
							?>
								<li class="hit">
									<a href="#none" onclick="if(confirm('탈퇴 하시면 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) window.open('<?php echo $sns_callback_url; ?>', 'sns_leave', 'width=800, height=500'); return false;" class="btn face">
										<span class="sns"></span><span class="en">페이스북 회원탈퇴</span>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<!-- /회원탈퇴 -->

	<!-- ◆페이지 이용도움말 -->
	<div class="c_user_guide">
		<div class="guide_box">
			<?php
			$leave_guidance = explode(PHP_EOL, $siteInfo['s_leave_guidance']);
			if(count($leave_guidance) <= 0) {
			?>
				<dl>
					<dt>회원탈퇴 주의사항</dt>
					<?php if($sns_join_type == 'direct') { ?>
						<dd>탈퇴후에는 같은 아이디로 재가입 할 수 없습니다.</dd>
					<?php } else { ?>
						<dd>탈퇴후에는 같은 소셜 아이디로 재가입 할 수 없습니다.</dd>
					<?php } ?>
					<dd>서비스를 탈퇴하시면 서비스 활동이 불가능하며 이용시 발생한 적립금 및 쿠폰등은 복원되지 않습니다.</dd>
					<dd>서비스 탈퇴 후에는 그 동안 이용하셨던 모든 내역의 조회, 상담 및 서비스 등을 이용할 수 없습니다.</dd>
					<dd>탈퇴 즉시 개인정보는 삭제되며, 어떠한 방법으로도 복원할 수 없습니다.</dd>
					<dd>전자상거래 서비스 등의 거래내역은 전자상거래등에서의 소비자보호에 관한 법률에 의거하여 보관됩니다.</dd>
				</dl>
			<?php } else { ?>
				<dl>
					<dt>회원탈퇴 주의사항</dt>
					<?php if($sns_join_type == 'direct') { ?>
						<dd>탈퇴후에는 같은 아이디로 재가입 할 수 없습니다.</dd>
					<?php } else { ?>
						<dd>탈퇴후에는 같은 소셜 아이디로 재가입 할 수 없습니다.</dd>
					<?php } ?>
					<?php foreach($leave_guidance as $k=>$v) { ?>
						<dd><?php echo htmlspecialchars($v); ?></dd>
					<?php } ?>
				</dl>
			<?php } ?>
		</div>
	</div>
</div>
<!-- /공통페이지 섹션 -->


<?php if($sns_join_type == 'direct') { ?>
	<script type="text/javascript">
		function leaveSubmit() {
			if(!$('input[name=leave_pw]').val()) {
				alert('비밀번호를 입력해주세요');
				$('input[name=leave_pw]').focus();
				return false;
			}
			if(!confirm('정말 탈퇴하시겠습니까?')) return false;
			return true;
		}
	</script>
<?php } ?>