<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$agree_arr -> 전체약관 => (/program/mmember.join.agree.php에서 지정)
	$privacy_table -> 개인정보처리방침 하단 출력될 내용변수 => (/program/member.join.agree.php에서 지정)
	$agree_other -> 선택약관 변수 => (/program/member.join.agree.php에서 지정)
	$next_pn -> 이용약관 다음 페이지를 지정한 변수 => (/program/member.join.agree.php에서 지정)


	$is_sns_login_form -> SNS로그인 사용여부 => (/program/member.join.agree.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/member.join.agree.php에서 지정)
*/
$page_title = '회원가입'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<form name="join_agree" class="js_join_agree" action="/" method="get" autocomplete="off">
	<input type="hidden" name="pn" value="<?php echo $next_pn; ?>">
	<!-- ◆공통페이지 섹션 -->
	<div class="c_section c_member">
		<!-- ◆이용약관 -->
		<div class="c_agree">
			<?php if($is_sns_login_form === true) { // 소셜로그인 가능 시 작동 ?>
				<!-- 소셜 로그인(맞춤) / 기본은 소셜 로그인 없음 -->
				<div class="c_sns_login">
					<div class="sns_btn ">
						<ul>
							<?php
							if($SNSField['naver']['login_use'] == 'Y') {
								$sns_callback_url = $SNSField['naver']['callback_url'];
							?>
								<li>
									<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn naver" title="네이버 로그인">
										<span class="sns"></span>
										<span class="en">네이버 로그인</span>
									</a>
								</li>
							<?php } ?>
							<?php
							if($SNSField['kakao']['login_use'] == 'Y') {
								$sns_callback_url = $SNSField['kakao']['callback_url'];
							?>
								<li>
									<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn kakao" title="카카오톡 로그인">
										<span class="sns"></span>
										<span class="en">카카오톡 로그인</span>
									</a>
								</li>
							<?php } ?>
							<?php
							if($SNSField['facebook']['login_use'] == 'Y') {
								$sns_callback_url = $SNSField['facebook']['callback_url'];
							?>
								<li>
									<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn face" title="페이스북 로그인">
										<span class="sns"></span>
										<span class="en">페이스북 로그인</span>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php } ?>

			<div class="agree_form">
				<!-- 선택일 경우 if_select 클래스 추가 및 선택 텍스트 변경 -->
				<div class="c_group_tit">
					<span class="tit">이용약관</span><span class="sub_tt">(필수)</span>
					<!-- 이용약관페이지로 이동 -->
					<a href="/?pn=pages.view&type=agree&data=agree" class="btn" target="_blank">전체보기</a>
				</div>
				<div class="form">
					<div class="text_box">
						<textarea rows="12" class="textarea_design" readonly="readonly"><?php echo stripslashes(htmlspecialchars(ConfigReplace($agree_arr['agree']['po_content']))); ?></textarea>
					</div>
					<div class="agree_check"><label><input type="checkbox" name="join_agree" value="Y"/>위의 내용을 읽고 이에 동의합니다.</label></div>
				</div>
			</div>



			<!-- 개인정보처리방침 -->
			<div class="agree_form">
				<div class="c_group_tit">
					<span class="tit">개인정보 수집 및 이용 동의</span><span class="sub_tt">(필수)</span>
					<!-- 개인정보처리방침페이지로 이동 -->
					<a href="/?pn=pages.view&type=agree&data=privacy" class="btn" target="_blank">전체보기</a>
				</div>
				<div class="form">
					<div class="text_box">
						<textarea rows="12" class="textarea_design" readonly="readonly"><?php echo stripslashes(htmlspecialchars(ConfigReplace($agree_arr['join_privacy']['po_content']))); ?></textarea>
					</div>

					<?php
					// $privacy_table -> 개인정보처리방침 하단 출력될 내용변수 => (/program/member.join.agree.php에서 지정)
					if(count($privacy_table) > 0) {
					?>
						<!-- 개인정보수집 항목 -->
						<div class="agree_add_info">
							<table>
								<colgroup>
									<col width="10%">
									<?php if(count($jtv) > 1){ ?>
									<col width="10%">
									<?php } ?>
									<col width="25%">
									<col width="*">
								</colgroup>
								<thead>
									<?php foreach($privacy_table as $jtk=>$jtv) { ?>
										<tr>
											<th scope="col"<?php echo (count($jtv) > 1?' colspan="'.count($jtv).'"':null); ?>>구분</th>
											<th scope="col">이용 목적</th>
											<th scope="col">수집 항목</th>
										</tr>
									<?php } ?>
								</thead>
								<tbody>
									<?php foreach($privacy_table as $jtk=>$jtv) { ?>
										<?php foreach($jtv as $jtsk=>$jtsv) { ?>
											<tr>
												<?php if(count($jtv) > 1 && $jtsk <= 0) { ?>
													<td rowspan="<?php echo count($jtv); ?>"><?php echo $jtk; // 구분명 ?></td>
												<?php } ?>
												<td><?php echo ($jtsv['required'] == 'Y'?'필수':'선택'); // 필수여부 ?></td>
												<td><?php echo $jtsv['name']; // 이용목적 ?></td>
												<td><?php echo implode(', ', $jtsv['item']); // 수집항목 ?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<?php foreach($privacy_table as $jtk=>$jtv) { ?>
										<tr>
											<th scope="col" colspan="<?php echo (count($jtv) > 1?'4':'3'); ?>">보존 및 파기</th>
										</tr>
										<tr>
											<td colspan="<?php echo (count($jtv) > 1?'4':'3'); ?>"><?php echo $jtv[0]['destruction']; // 보존 및 파기 ?></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					<?php } ?>

					<div class="agree_check"><label><input type="checkbox" name="join_privacy" value="Y"/>위의 내용을 읽고 이에 동의합니다.</label></div>
				</div>
			</div>


			<?php
			// $agree_other -> 선택약관 변수 => (/program/member.join.agree.php에서 지정)
			// 선택약관 처리
			if(count($agree_other) > 0) {
			?>
				<?php foreach($agree_other as $sak=>$sav) { ?>
					<div class="agree_form">
						<!-- 선택일 경우 if_select 클래스 추가 및 선택 텍스트 변경 -->
						<div class="c_group_tit if_select">
							<span class="tit"><?php echo $sav['title']; ?></span><span class="sub_tt">(선택)</span>

						</div>
						<?php
						if(count($sav['agree']) > 0) {
							foreach($sav['agree'] as $ssak=>$ssav) {
						?>
							<div class="form">
								<div class="tit"><?php echo $ssav['title']; ?></div>
								<div class="text_box">
									<textarea rows="12" class="textarea_design" readonly="readonly"><?php echo stripslashes(htmlspecialchars(ConfigReplace($ssav['content']))); ?></textarea>
								</div>
								<div class="agree_check"><label><input type="checkbox" name="agree_other[]" value="<?php echo $ssav['uid']; ?>"/>위의 내용을 읽고 이에 동의합니다.</label></div>
							</div>
						<?php }} ?>
					</div>
				<?php } ?>
			<?php } ?>

			<!-- 전체 동의 / 클릭시 위에 전체 선택 -->
			<div class="agree_form if_total">
				<div class="agree_check"><label><input type="checkbox" name="all_check" value="Y" class="js_all_check"/>위의 모든 내용을 읽고 확인 후, 전체 동의합니다.</label></div>
			</div>
			<div class="auth_tip"><span class="tx">현재 <u>14세 미만의 회원은 가입이 제한</u>되어 있으며,<br/>사이트 이용에 제약이 있을 수 있습니다.</span></div>

			<div class="c_btnbox ">
				<ul>
					<li><a href="#none" onclick="history.go(-1); return false;" class="c_btn h55 black line">취소</a></li>
					<li><a href="#none" onclick="$(this).closest('form').submit(); return false;" class="c_btn h55 black ">다음단계</a></li>
				</ul>
			</div>
		</div>
		<!-- /이용약관 -->
	</div>
	<!-- /공통페이지 섹션 -->
</form>

<script type="text/javascript">
	$(document).ready(function() {
		$('.js_join_agree').validate({
			rules: {
				  join_agree: { required: true }
				, join_privacy: { required: true }
			},
			messages: {
				  join_agree: { required: '이용약관에 동의해주시기 바랍니다' }
				, join_privacy: { required: '개인정보수집 및 이용에 동의해주시기 바랍니다' }
			}
		});
	});
	$(document).on('click', '.js_all_check', AgreeAllCheck);
	function AgreeAllCheck(trigger) {
		var ck = $('.js_all_check').is(':checked');
		if(typeof trigger != 'object') ck = trigger;
		if(ck === true) $('.js_join_agree').find('input:checkbox').not('.js_all_check').prop('checked', true);
		else $('.js_join_agree').find('input:checkbox').not('.js_all_check').prop('checked', false);
	}
</script>