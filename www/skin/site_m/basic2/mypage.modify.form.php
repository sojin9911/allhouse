<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$is_sns_login_form -> SNS로그인 사용여부 => (/program/mypage.modify.form.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/mypage.modify.form.php에서 지정)
*/
$page_title = '정보수정'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">

    <form name="join_form"  action="<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php" class="js_modify_form" method="post" target="common_frame" autocomplete="off">
        <input type="hidden" name="_mode" value="modify">
        <input type="hidden" name="_ordr_idxx" value=""><!-- 2018-10-04 SSJ :: 본인인증 사용 시 -->
		<!-- ◆정보입력 -->
		<div class="c_form">
			<table>
				<tbody>
					<?php if($sns_join_type == 'direct') { ?>
						<tr>
							<!-- 필수일 경우 th에 ess 클래스 추가 -->
							<th class="ess"><span class="tit">아이디</span></th>
							<td><?php echo $mem_info['in_id']; ?></td>
						</tr>
					<?php } ?>
					<?php if($is_sns_login_form === true) { ?>
						<tr>
							<th class="ess"><span class="tit">SNS계정 연동</span></th>
							<td>
								<!-- 소셜 로그인(맞춤) / 기본은 소셜 로그인 없음 -->
								<div class="c_sns_login">
									<div class="sns_btn">
										<ul>
											<?php
											if($SNSField['naver']['login_use'] == 'Y') {
												$sns_callback_url = $SNSField['naver']['callback_url'];
											?>
												<?php if($sns_callback_url) { ?>
													<li>
														<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn naver">
															<span class="sns"></span><span class="en">네이버<br/>연동하기</span>
														</a>
													</li>
												<?php } else { ?>
													<li class="hit">
														<a href="#none" onclick="alert('이미 연동이 완료되었습니다.'); return false;" class="btn naver">
															<span class="sns"></span><span class="en">네이버<br/>연동완료</span>
														</a>
													</li>
												<?php } ?>
											<?php } ?>
											<?php
											if($SNSField['kakao']['login_use'] == 'Y') {
												$sns_callback_url = $SNSField['kakao']['callback_url'];
											?>
												<?php if($sns_callback_url) { ?>
													<li>
														<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn kakao">
															<span class="sns"></span><span class="en">카카오톡<br/>연동하기</span>
														</a>
													</li>
												<?php } else { ?>
													<li class="hit">
														<a href="#none" onclick="alert('이미 연동이 완료되었습니다.'); return false;" class="btn kakao">
															<span class="sns"></span><span class="en">카카오톡<br/>연동완료</span>
														</a>
													</li>
												<?php } ?>
											<?php } ?>
											<?php
											if($SNSField['facebook']['login_use'] == 'Y') {
												$sns_callback_url = $SNSField['facebook']['callback_url'];
											?>
												<li>
													<?php if($sns_callback_url) { ?>
														<li>
															<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn face">
																<span class="sns"></span><span class="en">페이스북<br/>연동하기</span>
															</a>
														</li>
													<?php } else { ?>
														<li class="hit">
															<a href="#none" onclick="alert('이미 연동이 완료되었습니다.'); return false;" class="btn face">
																<span class="sns"></span><span class="en">페이스북<br/>연동완료</span>
															</a>
														</li>
													<?php } ?>
												</li>
											<?php } ?>
										</ul>
									</div>
								</div>
							</td>
						</tr>
					<?php } ?>
					<tr<?php echo ($sns_join_type != 'direct' ? ' style="display:none"':null); ?>>
						<th class="ess"><span class="tit">비밀번호</span></th>
						<td>
							<input type="password" name="join_pw" class="input_design js_join_pw" placeholder="" autocomplete="new-password" />
							<div class="tip_txt ">수정을 원할 경우에만 입력해주세요.</div>
						</td>
					</tr>
					<tr<?php echo ($sns_join_type != 'direct' ? ' style="display:none"':null); ?>>
						<th class="ess"><span class="tit">비밀번호 확인</span></th>
						<td>
							<input type="password" name="join_repw" class="input_design js_join_repw" placeholder="" autocomplete="new-password"/>
							<div class="tip_txt ">동일하게 다시 한 번 입력해주세요.</div>
						</td>
					</tr>
					<tr>
                        <th class="ess"><span class="tit">이름</span></th>
                        <td>
                            <?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                                <input type="text" name="join_name" value="<?php echo $mem_info['in_name']; ?>" class="input_design js_auth_before auth_name" readonly placeholder="" />
                            <?php } else { ?>
                                <input type="text" name="join_name" value="<?php echo $mem_info['in_name']; ?>" class="input_design" readonly placeholder="" />
                            <?php } ?>
                            <div class="tip_txt">실명을 입력해주세요.</div>
                        </td>
                    </tr>
					<?php if($siteInfo['join_birth'] == 'Y') { ?>
                        <tr>
                            <th<?php echo ($siteInfo['join_birth_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">생년월일</span></th>
                            <td>
                                <?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                                    <!-- 달력나옴 -->
                                    <input type="text" name="_birth" class="input_design if_date js_auth_before auth_birth" value="<?php echo $mem_info['in_birth']; ?>" placeholder="" readonly/>
                                <?php } else { ?>
                                    <!-- 달력나옴 -->
                                    <input type="text" name="_birth" class="input_design if_date<?php echo (empty($mem_info['in_birth']) || $mem_info['in_birth'] == '0000-00-00' || $mem_info['in_birth'] == ''?' js_pic_day_max_today':null); ?>" value="<?php echo $mem_info['in_birth']; ?>" placeholder="" readonly data-position="bottom right"/>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
					<?php if($siteInfo['join_sex'] == 'Y') { ?>
                        <tr>
                            <th<?php echo ($siteInfo['join_sex_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">성별</span></th>
                            <td>
                                <label class="label_design"><input type="radio" name="_sex" value="M"<?echo ($mem_info['in_sex'] == 'M'?' checked':null); ?><?php echo (isset($mem_info['in_sex']) && $mem_info['in_sex'] != ''?' onclick="return false;"':null); ?> class="js_auth_before auth_sex"/><span class="txt">남성</span></label>
                                <label class="label_design"><input type="radio" name="_sex" value="F"<?echo ($mem_info['in_sex'] == 'F'?' checked':null); ?><?php echo (isset($mem_info['in_sex']) && $mem_info['in_sex'] != ''?' onclick="return false;"':null); ?> class="js_auth_before auth_sex"/><span class="txt">여성</span></label>
                            </td>
                        </tr>
                    <?php } ?>
					<?php if($siteInfo['join_tel'] == 'Y') { ?>
						<tr>
							<th<?php echo ($siteInfo['join_tel_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">전화번호</span></th>
							<td>
								<input type="tel" name="join_tel" class="input_design" value="<?php echo $mem_info['in_tel']; ?>" placeholder="전화번호" />
								<div class="tip_txt ">휴대폰 이외 유선전화가 필요한 경우 입력해주세요.</div>
							</td>
						</tr>
					<?php } ?>
					<tr>
                        <th class="ess"><span class="tit">휴대폰 번호</span></th>
                        <td>
                            <?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                                <div class="input_box auth">
                                    <input type="tel" name="join_tel2" class="input_design js_auth_before auth_phone" value="<?php echo $mem_info['in_tel2']; ?>" placeholder="휴대폰 번호" readonly/>
                                    <a href="#none" class="c_btn h35 light" onclick="auth_type_check(); return false;">휴대폰 본인인증</a>
                                </div>
                            <?php } else { ?>
                                <input type="tel" name="join_tel2" class="input_design" value="<?php echo $mem_info['in_tel2']; ?>" placeholder="휴대폰 번호"  />
                            <?php } ?>
                            <div class="tip_txt black">주문 등과 관련된 중요한 문자가 발송됩니다.</div>
                        </td>
                    </tr>
					<tr>
						<th><span class="tit">SMS 수신</span></th>
						<td>
							<label class="label_design"><input type="radio" name="join_smssend" value="Y"<?php echo ($mem_info['in_smssend'] == 'Y'?' checked':null); ?>/><span class="txt">수신</span></label>
							<label class="label_design"><input type="radio" name="join_smssend" value="N"<?php echo ($mem_info['in_smssend'] == 'N'?' checked':null); ?>/><span class="txt">수신거부</span></label>
							<div class="tip_txt ">비정기적으로 문자 서비스를 제공합니다.</div>
						</td>
					</tr>


					<tr>
						<th class="ess"><span class="tit">이메일 주소</span></th>
						<td>
							<div class="input_box mail">
								<input type="hidden" name="join_email_check" class="js_join_email_check" value="<?php echo ($mem_info['in_email'] != ''?'1':''); ?>">
								<input type="hidden" name="join_email" class="js_join_email" value="<?php echo $mem_info['in_email']; ?>">
								<?php
									$_email_prefix = $_email_suffix = '';
									if($mem_info['in_email']) {
										$_email_arr = explode('@', $mem_info['in_email']);
										$_email_prefix = $_email_arr[0];
										$_email_suffix = $_email_arr[1];
									}
								?>
								<ul>
									<li><input type="text" name="_email_prefix" class="input_design js_email_prefix" value="<?php echo $_email_prefix; ?>" placeholder="이메일 아이디" /></li>
									<li class="select_box">
										<span class="mail_icon">＠</span>
										<div class="select">
											<select name="_email_suffix_select" class="js_email_suffix_select">
												<option value="">이메일 주소 선택</option>
												<?php foreach($email_suffix as $ek=>$ev) { ?>
													<option value="<?php echo $ev; ?>"<?php echo ($_email_suffix == $ev?' selected':(!in_array($_email_suffix, $email_suffix) && $ev == 'direct'?' selected':null)); ?>><?php echo ($ev == 'direct'?'직접입력':str_replace('@', '', $ev)); ?></option>
												<?php } ?>
											</select>
										</div>
									</li>
									<!-- 직접입력 선택시 노출 / 그 전에는 숨김 -->
									<li class="other" style="display: none;" ><input type="text" name="_email_suffix_input" class="input_design js_email_suffix_input" value="<?php echo $_email_suffix; ?>"/></li>
									<li class="btn_box"><a href="#none" class="c_btn h35 light js_email_overlap_check">이메일 중복체크</a></li>
								</ul>
							</div>
							<div class="tip_txt black">주문 등과 관련된 중요한 메일이 발송됩니다.</div>
						</td>
					</tr>

					<tr>
						<th><span class="tit">이메일 수신</span></th>
						<td >
							<label class="label_design"><input type="radio" name="join_emailsend" value="Y"<?php echo ($mem_info['in_emailsend'] == 'Y'?' checked':''); ?>/><span class="txt">수신</span></label>
							<label class="label_design"><input type="radio" name="join_emailsend" value="N"<?php echo ($mem_info['in_emailsend'] == 'N'?' checked':''); ?>/><span class="txt">수신거부</span></label>
							<div class="tip_txt if_beside">비정기적으로 메일링 서비스를 제공합니다.</div>
						</td>
					</tr>
					<?php if($siteInfo['join_addr'] == 'Y') { ?>
						<tr>
							<th <?php echo ($siteInfo['join_addr_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">주소</span></th>
							<td>
								<div class="input_box address">
									<input type="text" name="join_zonecode" id="_zonecode" class="input_design" value="<?php echo $mem_info['in_zonecode']; ?>" style="width:70px !important" readonly="readonly"/>
									<a href="#none" onclick="post_popup_show(); return false;" class="c_btn h35 light">주소검색</a>
								</div>

								<div class="input_full">
									<input type="text" name="join_address_doro" id="_addr_doro" class="input_design" value="<?php echo $mem_info['in_address_doro']; ?>" placeholder="도로명 주소" readonly="readonly"/>
									<input type="text" name="join_address2" id="_addr2" class="input_design" value="<?php echo $mem_info['in_address2']; ?>" placeholder="나머지 주소" />
								</div>
							</td>
						</tr>
						<?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>
						<tr>
							<th <?php echo ($siteInfo['join_addr_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">지번 주소</span></th>
							<td>
								<div class="input_box"  style="display:none;">
									<input type="text" name="join_zip1" id="_post1" class="input_design" value="<?php echo $mem_info['in_zip1']; ?>" style="width:50px !important" readonly="readonly"/>
									<span class="dash">-</span>
									<input type="text" name="join_zip2" id="_post2" class="input_design" value="<?php echo $mem_info['in_zip2']; ?>" style="width:50px !important" readonly="readonly"/>
									<div class="tip_txt if_beside">구 우편번호</div>
								</div>
								<input type="text" name="join_address1" id="_addr1" class="input_design" value="<?php echo $mem_info['in_address1']; ?>" placeholder="지번 주소" readonly="readonly"/>
								<div class="tip_txt ">주소검색을 통해 자동으로 입력됩니다.</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>


		<div class="c_btnbox">
			<ul>
				<li><a href="#none" class="c_btn h55 color js_submit">정보수정 완료</a></li>
				<li><a href="/?pn=mypage.leave.form" class="c_btn h55 light">회원탈퇴</a></li>
			</ul>
		</div>
	</form>
</div>
<!-- /공통페이지 섹션 -->

<script type="text/javascript">
	// 이메일 항목제어
	$(document).ready(join_email_form_view);
	$(document).on('change', '.js_email_suffix_select', join_email_form_view);
	function join_email_form_view() {
		var i_value = $('.js_email_prefix').val();
		var s_value = $('.js_email_suffix_select option:selected').val();
		var save_value = $('.js_join_email').val();
		var r_val = '';
		if(save_value != i_value.replace('@', '')+'@'+$('.js_email_suffix_input').val().replace('@', '')) $('.js_join_email_check').val('');
		if(s_value == 'direct') {
			$('.js_email_suffix_input').val('<?php echo $_email_suffix; ?>');
			$('.js_email_suffix_input').closest('li').show();
		}
		else {
			$('.js_email_suffix_input').val(s_value);
			$('.js_email_suffix_input').closest('li').hide();
			r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
			$('.js_join_email').val(r_val);
		}
	}
	$(document).on('keyup', '.js_email_prefix', function(e) {
		var i_value = $(this).val();
		var s_value = $('.js_email_suffix_input').val();
		var r_val = '';
		$('.js_join_email_check').val('');
		if(i_value.split('@').length > 1) {
			$(this).val($(this).val().replace('@', ''));
			$('.js_email_suffix_input').val('');
			$('.js_email_suffix_select').val('direct');
			$('.js_email_suffix_input').closest('li').show();
			$('.js_email_suffix_input').focus();
		}
		r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
		$('.js_join_email').val(r_val);
	});
	$(document).on('keyup', '.js_email_suffix_input', function(e) {
		var su = $(this);
		var i_value = $('.js_email_prefix').val();
		var s_value = $(this).val().replace('@', '');
		var r_val = '';
		$('.js_join_email_check').val('');
		if(s_value) {
			$.each($('.js_email_suffix_select option'), function(k, v){
				if($(v).val() == s_value.replace('@', '')) {
					su.hide();
					$('.js_email_suffix_select').val($(v).val());
				}
			});
		}
		r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
		$('.js_join_email').val(r_val);
	});



	// 이메일 중복체크
	$(document).on('click', '.js_email_overlap_check', function(e) {
		e.preventDefault();
		var i_value = $('.js_email_prefix').val();
		var s_value = $('.js_email_suffix_input').val();
		var _email = $('.js_join_email').val();
		var UserInput = $('.js_email_suffix_input').closest('li').is(':visible');
		var o_email = '<?php echo $mem_info['in_email']; ?>';
		if(!i_value) {
			alert('이메일 아이디를 입력해주세요.');
			$('.js_email_prefix').focus();
			return false;
		}
		if(!s_value) {
			if(UserInput === true) {
				alert('이메일 주소를 입력해주세요');
				$('.js_email_prefix').focus();
			}
			else {
				alert('이메일 주소를 선택해주세요');
				$('.js_email_suffix_select').focus();
			}
			return false;
		}
		$.ajax({
			data: {
				_mode: 'email_check',
				_email: _email
			},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php',
			success: function(data) {

				// 전달된 데이터를 array로 변환
				try { var result = $.parseJSON(data); }
				catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

				if(result['msg']) {
					var msg = result['msg'];
					msg = msg.replace(/\\n/gi, '\n');
				}
				if(result['alert']) {
					var re_alert = result['alert'];
					re_alert = re_alert.replace(/\\n/gi, '\n');
				}

				if(result['result'] == 'success') {
					$('.js_join_email_check').val(1);
					alert(msg);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
				else {
					$('.js_join_email_check').val('');
					alert(msg);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
			}
		});
	});



	// 서브미트
	$(document).on('click', '.js_submit', function(e) {
		e.preventDefault();
		$(this).closest('form').submit();
	});
	$(document).ready(function() {
		// - 대문자 검증
		jQuery.validator.addMethod('upper_alpha', function(value, element, length) {
			var pattern = /[A-Z]/;
			var mc = value.match(pattern);
			if(mc == null) return this.optional(element) || false;
			return this.optional(element) || (mc.length < length?false:true);
		}, '비밀번호에는 대문자가 {0}개 이상 포함되어야합니다');

		// - 특수문자 검증
		jQuery.validator.addMethod('special_string', function(value, element, length) {
			var pattern = /[~!@#$%^&*()_+|<>?:{}]/;
			var mc = value.match(pattern);
			if(mc == null) return this.optional(element) || false;
			return this.optional(element) || (mc.length < length?false:true);
		}, '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 {0}개 이상 포함되어야합니다');

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");

		// - 전화번호 검증
		jQuery.validator.addMethod("tel_check", function(value, element) {
			var pattern = /^\d{2,3}-\d{3,4}-\d{4}$/;
			return this.optional(element) || pattern.test(value);
		}, "전화번호 형식이 유효하지않습니다.");

		// - 휴대폰 검증
		jQuery.validator.addMethod("htel_check", function(value, element) {
			var pattern = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/;
			return this.optional(element) || pattern.test(value);
		}, "휴대폰번호 형식이 유효하지않습니다.");



		// 벨리데이션
		$('.js_modify_form').validate({
			ignore: 'input[type=text]:hidden',
			rules: {
				join_name: { required : true }
				, join_pw: {
					minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
					<?php if($siteInfo['join_pw_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
						, maxlength: <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, special_string: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
				}
				, join_repw: {
					minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
					<?php if($siteInfo['join_pw_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
						, maxlength: <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, special_string: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					, equalTo: '.js_join_pw'
				}
				<?php if($siteInfo['join_birth'] == 'Y' && $siteInfo['join_birth_required'] == 'Y') { ?>
					, _birth: { required : true }
				<?php } ?>
				<?php if($siteInfo['join_sex'] == 'Y' && $siteInfo['join_sex_required'] == 'Y') { ?>
					, _sex: { required : true }
				<?php } ?>
				, join_tel2: { required : true, htel_check: true }
				<?php if($siteInfo['join_tel'] == 'Y' && $siteInfo['join_tel_required'] == 'Y'){ ?>
					, join_tel: { required : true, tel_check: true }
				<?php } ?>
				, _email_prefix: { required : true }
				, _email_suffix_input: { required : true }
				, join_email: { required : true, email_check: true }
				, join_email_check: { required : true }
				, join_emailsend: { required : true }
				<?php if($siteInfo['join_addr'] == 'Y' && $siteInfo['join_addr_required'] == 'Y'){ ?>
					, join_address_doro: { required : true }
					, join_zonecode: { required : true }
					, join_address2: { required : true }
				<?php } ?>
			},
			messages: {
				join_name: { required : '이름을 입력해주세요' }
				, join_pw: {
					minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
					<?php if($siteInfo['join_pw_limit_max'] > 4) { ?>
						, maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>자 까지만 입력가능합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
				}
				, join_repw: {
					minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
					<?php if($siteInfo['join_pw_limit_max'] > 4) { ?>
						, maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>자 까지만 입력가능합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					, equalTo: '비밀번호가 일치하지않습니다'
				}
				<?php if($siteInfo['join_birth'] == 'Y' && $siteInfo['join_birth_required'] == 'Y') { ?>
					, _birth: { required : '생년월일을 입력해주세요' }
				<?php } ?>
				<?php if($siteInfo['join_sex'] == 'Y' && $siteInfo['join_sex_required'] == 'Y') { ?>
					, _sex: { required : '성별을 선택해주세요' }
				<?php } ?>
				, join_tel2: { required: '휴대폰 번호를 입력해주세요', htel_check: '휴대폰번호 형식이 유효하지않습니다' }
				<?php if($siteInfo['join_tel'] == 'Y' && $siteInfo['join_tel_required'] == 'Y') { ?>
					, join_tel: { required: '전화번호를 입력해주세요', tel_check: '전화번호 형식이 유효하지않습니다' }
				<?php } ?>
				, _email_prefix: { required : '이메일 아이디를 입력해주세요' }
				, _email_suffix_input: { required : '이메일 주소를 '+($('.js_email_suffix_input').is(':visible')?'입력':'선택')+'해주세요' }
				, join_email: { required : '이메일 주소를 입력해주세요', email_check: '유효하지 않은 E-Mail주소입니다' }
				, join_email_check: { required : '이메일 중복검사를 해주세요' }
				, join_emailsend: { required : '이메일 수신여부를 선택해주세요' }
				<?php if($siteInfo['join_addr'] == 'Y' && $siteInfo['join_addr_required'] == 'Y'){ ?>
					, join_address_doro: { required : '주소검색을 통하여 도로명주소를 입력해주세요' }
					, join_zonecode: { required : '주소검색을 통하여 새 우편번호를 입력해주세요' }
					, join_address2: { required : '나머지주소를 입력해주세요' }
				<?php } ?>
			}
            ,submitHandler: function(form){
                // 정보 수정페이지 회원정보 변경체크 -- 본인인증 적용 체크
                if(
                    '<?php echo $mem_info["in_tel2"]; ?>' != $('input[name=join_tel2]').val()
                    ||
                    '<?php echo $mem_info["in_name"]; ?>' != $('input[name=join_name]').val()
                    ||
                    ('<?php echo $siteInfo["join_birth"]; ?>' == 'Y' && '<?php echo $mem_info["in_birth"]; ?>' != $('input[name=_birth]').val())
                    ||
                    ('<?php echo $siteInfo["join_sex"]; ?>' == 'Y' && '<?php echo $mem_info["in_sex"]; ?>' != $('input[name=_sex]:checked').val())
                )
                {
                    // do other things for a valid form
                    if(typeof kcp_submit == 'function') if(!kcp_submit()) return false;
                }
                form.submit();
            }
		});
	});
</script>
<?php include_once(OD_ADDONS_ROOT.'/newpost/newpost.search_m.php'); // 다음주소찾기 ?>