<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = (empty($_mode) || $_mode == 'find_id'?'아이디찾기':'비밀번호 찾기'); // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_member">

	<!-- ◆공통 탭메뉴 -->
	<!-- 활성화시 li에 hit 클래스 추가 -->
	<div class="c_tabmenu">
		<ul>
			<li<?php echo (empty($_mode) || $_mode == 'find_id'?' class="hit"':null); ?>><a href="/?pn=<?php echo $pn; ?>&_mode=find_id" class="tab">아이디 찾기</a></li>
			<li<?php echo ($_mode == 'find_pw'?' class="hit"':null); ?>><a href="/?pn=<?php echo $pn; ?>&_mode=find_pw" class="tab">비밀번호 찾기</a></li>
		</ul>
	</div>

	<?php if(empty($_mode) || $_mode == 'find_id') { // 아이디 찾기 ?>
		<!-- ◆아이디/비밀번호찾기 -->
		<div class="c_login">
			<ul class="ul">
				<li class="li">
					<div class="login_form">
						<!-- <div class="c_group_tit">아이디 찾기</div> -->
						<form class="js_find_id_from" autocomplete="off">
							<div class="form">
								<ul>
									<li><input type="text" name="find_id_name" class="input_design" placeholder="이름" required/></li>
									<li><input type="tel" name="find_id_tel" class="input_design " placeholder="휴대폰 번호" required/></li>
								</ul>
								<input type="submit" class="btn_find" value="찾기"/>
							</div>

							<!-- 가이드 -->
							<dl class="guide">
								<dt>아이디 조회 시 안내사항</dt>
								<dd>조회 시 개인정보보호를 위해 아이디의 일부를 별표(*)로 표기합니다. </dd>
								<dd>가입 시 등록한 정보를 잊으신 경우, 고객센터로 문의해주시기 바랍니다.</dd>
							</dl>

							<!-- 결과창 (결과창 form, guide 사라지고 결과창이 나옴) -->
							<div class="result js_find_id_result" style="display: none">
								<div class="js_success" style="display: none">
									<div class="result_txt">고객님께서 조회하신 아이디는<br/><strong class="js_id">***</strong> 입니다.</div>
									<div class="sub_txt">개인정보 도용에 따른 피해 방지를 위해<br/>일부를 별표(*)로 표시하였습니다.</div>
								</div>
								<div class="js_error">
									<div class="result_txt" style="display: none">죄송합니다. 고객님<br/>조회하신 회원정보를 찾을 수 없습니다.</div>
									<div class="sub_txt">입력 정보를 다시 한 번 확인하시고<br/>아이디를 찾아보시기 바랍니다.</div>
								</div>
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" class="c_btn h40 light line js_find_id_re">다시찾기</a></li>
										<li><a href="/?pn=member.login.form" class="c_btn h40 light bold">로그인하기</a></li>
									</ul>
								</div>
							</div>
						</form>
					</div>
				</li>
			</ul>
		</div>
		<!-- /아이디/비밀번호찾기 -->
		<script type="text/javascript">
			// 아이디 찾기
			$(document).on('submit', '.js_find_id_from', function(e) {
				e.preventDefault();
				var su = $(this);
				var _name = $(this).find('input[name=find_id_name]').val();
				var _tel = $(this).find('input[name=find_id_tel]').val();
				$.ajax({
					data: {
						_mode: 'find_id',
						_name: _name,
						_tel: _tel
					},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/member.find.pro.php',
					success: function(data) {

						// 전달된 데이터를 array로 변환
						try { var result = $.parseJSON(data); }
						catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

						// 결과처리
						if(result['result'] == 'success') {
							su.find('div.form').hide();
							su.find('.guide').hide();
							su.find('div.js_find_id_result .js_success').hide();
							su.find('div.js_find_id_result .js_error').hide();
							su.find('div.js_find_id_result .js_success .js_id').text('***');
							su.find('div.js_find_id_result .js_success .js_id').text(result['id']);
							su.find('div.js_find_id_result .js_success').show();
							su.find('div.js_find_id_result').show();

							// alert 안내가 있다면
							if(result['alert'] && result['alert'] != '') alert(result['alert']);
						}
						else if(result['result'] == 'error') {
							su.find('div.form').hide();
							su.find('.guide').hide();
							su.find('div.js_find_id_result .js_success').hide();
							su.find('div.js_find_id_result .js_error').hide();
							su.find('div.js_find_id_result .js_success .js_id').text('***');
							su.find('div.js_find_id_result .js_error').show();
							su.find('div.js_find_id_result').show();

							// alert 안내가 있다면
							if(result['alert'] && result['alert'] != '') alert(result['alert']);
						}
						else { // 기타에러
							if(result['msg']) { alert(result['msg']); }
							else {
								alert('통신중 에러가 발생하였습니다.');
								if(typeof console === 'object') console.log(data);
							}
						}
					}
				});
			});
			$(document).on('click', '.js_find_id_re', function(e) {
				e.preventDefault();
				var su = $(this).closest('form');
				su.find('div.js_find_id_result').hide();
				su.find('div.js_find_id_result .js_success').hide();
				su.find('div.js_find_id_result .js_error').hide();
				su.find('div.js_find_id_result .js_success .js_id').text('***');
				su.find('div.form').show();
				su.find('.guide').show();
				su.find('input').not('input:submit').not('input[name=_type]').val('');
				su.find('input').eq(0).focus();
			});
		</script>
	<?php } else { // 비밀번호찾기 ?>
		<!-- ◆아이디/비밀번호찾기 -->
		<div class="c_login">
			<ul class="ul">
				<li class="li">
					<div class="login_form">
						<!-- <div class="c_group_tit">비밀번호 찾기</div> -->
						<form class="js_find_pw_from" autocomplete="off">
							<?php if(count($PasswordFindType) == 1) { ?>
								<input type="hidden" name="_type" value="<?php echo $PasswordFindType[0]; ?>">
							<?php } else { ?>
								<div class="type js_type">
									<ul>
										<li><label><span class="tx"><input type="radio" name="_type" value="email" checked>이메일로 찾기</label></span></li>
										<li><label><span class="tx"><input type="radio" name="_type" value="sms">문자로 찾기</span></label></li>
									</ul>
								</div>
							<?php } ?>
							<div class="form">
								<ul>
									<li><input type="text" name="find_pw_id" class="input_design" placeholder="아이디"/></li>
									<li class="js_email_field"><input type="email" name="find_pw_email" class="input_design " placeholder="이메일 주소"/></li>
									<li class="js_sms_field" style="display:none;"><input type="tel" name="find_pw_tel" class="input_design " placeholder="휴대폰 번호"/></li>
								</ul>
								<input type="submit" class="btn_find" value="찾기"/>
							</div>

							<!-- 가이드 -->
							<dl class="guide">
								<dt>비밀번호 조회 시 안내사항</dt>
								<dd class="js_email_field">입력하신 이메일로 임시 비밀번호가 발송됩니다.</dd>
								<dd class="js_sms_field" style="display: none;">입력하신 휴대폰 번호로 임시 비밀번호가 발송됩니다.</dd>
								<dd>임시비밀번호를 이용하여 로그인 후, 꼭 비밀번호를 수정해주시기 바랍니다.</dd>
							</dl>

							<!-- 결과창 (결과창 form, guide 사라지고 결과창이 나옴) -->
							<div class="result js_find_pw_result" style="display: none">
								<div class="js_success" style="display: none">
									<div class="result_txt">고객님의 임시비밀번호를<br/><strong class="js_send_data"></strong>으로 전송해드렸습니다.</div>
									<div class="sub_txt">보내드린 임시비밀번호로 로그인 후,<br/>정보수정에서 꼭 비밀번호를 수정해주세요.</div>
								</div>
								<div class="js_error" style="display: none">
									<div class="result_txt">죄송합니다. 고객님<br/>조회하신 회원정보를 찾을 수 없습니다.</div>
									<div class="sub_txt">입력 정보를 다시 한 번 확인하시고<br/>비밀번호를 찾아보시기 바랍니다.</div>
								</div>
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" class="c_btn h40 light line js_find_pw_re">다시찾기</a></li>
										<li><a href="/?pn=member.login.form" class="c_btn h40 light bold">로그인하기</a></li>
									</ul>
								</div>
							</div>
						</form>
					</div>
				</li>
			</ul>
		</div>
		<!-- /아이디/비밀번호찾기 -->
		<script type="text/javascript">
			$(document).on('submit', '.js_find_pw_from', function(e) {
				e.preventDefault();
				var su = $(this);
				var _type = (su.find('input[name=_type]').attr('type') == 'hidden'?su.find('input[name=_type]').val():su.find('input[name=_type]:checked').val());
				var _id = $(this).find('input[name=find_pw_id]').val();
				var _tel = $(this).find('input[name=find_pw_tel]').val();
				var _email = $(this).find('input[name=find_pw_email]').val();
				$.ajax({
					data: {
						_mode: 'find_pw',
						_type: _type,
						_id: _id,
						_tel: _tel,
						_email: _email
					},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/member.find.pro.php',
					success: function(data) {

						// 전달된 데이터를 array로 변환
						try { var result = $.parseJSON(data); }
						catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

						// 결과처리
						if(result['result'] == 'success') {
							su.find('div.form').hide();
							su.find('.guide').hide();
							su.find('.js_type').hide();
							su.find('div.js_find_pw_result .js_success').hide();
							su.find('div.js_find_pw_result .js_error').hide();
							su.find('div.js_find_pw_result .js_success .js_send_data').text('');
							su.find('div.js_find_pw_result .js_success .js_send_data').text(result['send']);
							su.find('div.js_find_pw_result .js_success').show();
							su.find('div.js_find_pw_result').show();

							// alert 안내가 있다면
							if(result['alert'] && result['alert'] != '') alert(result['alert']);
						}
						else if(result['result'] == 'error') {
							su.find('div.form').hide();
							su.find('.guide').hide();
							su.find('.js_type').hide();
							su.find('div.js_find_pw_result .js_success').hide();
							su.find('div.js_find_pw_result .js_error').hide();
							su.find('div.js_find_pw_result .js_success .js_send_data').text('');
							su.find('div.js_find_pw_result .js_error').show();
							su.find('div.js_find_pw_result').show();

							// alert 안내가 있다면
							if(result['alert'] && result['alert'] != '') alert(result['alert']);
						}
						else { // 기타에러
							if(result['msg']) { alert(result['msg']); }
							else {
								alert('통신중 에러가 발생하였습니다.');
								if(typeof console === 'object') console.log(data);
							}
						}
					}
				});
			});
			$(document).on('click', '.js_find_pw_re', function(e) {
				e.preventDefault();
				var su = $(this).closest('form');
				su.find('div.js_find_pw_result').hide();
				su.find('div.js_find_pw_result .js_success').hide();
				su.find('div.js_find_pw_result .js_error').hide();
				su.find('div.js_find_pw_result .js_success .js_send_data').text('');
				su.find('div.form').show();
				su.find('.guide').show();
				su.find('.js_type').show();
				su.find('input').not('input:submit').not('input[name=_type]').val('');
				su.find('input').eq(0).focus();
			});
			<?php if(count($PasswordFindType) > 1) { ?>
				$(document).on('click', '.js_find_pw_from input[name=_type]', FindPwInput);
			<?php } ?>
			$(document).ready(FindPwInput);
			function FindPwInput() {
				var su = $('.js_find_pw_from');
				var _type = (su.find('input[name=_type]').attr('type') == 'hidden'?su.find('input[name=_type]').val():su.find('input[name=_type]:checked').val());
				su.find('.js_email_field').hide();
				su.find('.js_sms_field').hide();
				if(su.find('.js_email_field input').length > 0) su.find('.js_email_field input').val('');
				if(su.find('.js_sms_field input').length > 0) su.find('.js_sms_field input').val('');
				su.find('.js_'+_type+'_field').show();
			}
		</script>
	<?php } ?>
</div>
<!-- /공통페이지 섹션 -->