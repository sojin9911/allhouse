<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$agree_arr -> 전체약관 => (/program/mmember.join.agree.php에서 지정)
	$privacy_table -> 개인정보처리방침 하단 출력될 내용변수 => (/program/member.join.agree.php에서 지정)
	$agree_other -> 선택약관 변수 => (/program/member.join.agree.php에서 지정)
	$next_pn -> 이용약관 다음 페이지를 지정한 변수 => (/program/member.join.agree.php에서 지정)
*/
?>
<form name="join_form" class="js_join_form" action="<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php" method="post" target="common_frame" autocomplete="off">
    <input type="hidden" name="_mode" value="join">
    <input type="hidden" name="_ordr_idxx" value=""><!-- 2018-10-04 SSJ :: 본인인증 사용 시 -->
	<!-- ◆공통페이지섹션 -->
	<div class="c_section c_member">
		<div class="layout_fix">
			<div class="mja_wrap">
				<!-- ◆공통페이지 타이틀 -->
				<div class="c_page_tit">
					<div class="title">회원가입</div>
					<!-- 단계별 페이지 -->
					<div class="c_process">
						<ul>
													<!-- 해당 페이지일 경우 li hit클래스 추가 / li 없을때 num 숫자 순서대로 넘버링 -->
													<?php if($siteInfo['s_join_auth_use'] == 'Y' && false) { // 본인인증 사용시 ?>
															<li ><span class="num">01</span><span class="tit">약관동의</span></li>
															<li><span class="num">02</span><span class="tit">본인인증</span></li>
															<li class="hit"><span class="num">03</span><span class="tit">정보입력</span></li>
															<li><span class="num">04</span><span class="tit">가입완료</span></li>
													<?php } else { ?>
															<li ><span class="num">01</span><span class="tit">약관동의</span></li>
															<li class="hit"><span class="num">02</span><span class="tit">정보입력</span></li>
															<li><span class="num">03</span><span class="tit">가입완료</span></li>
													<?php } ?>
											</ul>
					</div>
				</div>
				<!-- /공통페이지 타이틀 -->


				<!-- ◆정보입력 -->
				<div class="member_cont">
				<div class="c_group_tit"><span class="tit">기본정보</span><span class="sub_txt">체크된 항목은 필수 항목입니다. 꼭 입력해주시기 바랍니다.</span></div>
				<div class="c_form">
					<table>
						<colgroup>
							<col width="150"/><col width="*"/><col width="150"/><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<!-- 필수일 경우 th에 ess 클래스 추가 -->
								<th class="ess"><span class="tit">아이디</span></th>
								<td colspan="3">
									<div class="input_box">
										<input type="text" name="join_id" class="input_design js_join_id" placeholder="" style="width:200px"/>
										<a href="#none" class="c_btn h30 light js_id_overlap_check">아이디 중복체크</a>
										<input type="hidden" name="join_id_check" class="js_join_id_check" value="">
									</div>
									<div class="tip_txt black"style="display:none">아이디는 한번 가입한 이후에는 변경할 수 없습니다.</div>
									<div class="tip_txt" style="display:none">
										<?php
										$id_length_text = '영문, 숫자로 '.$id_min_length.'자 이상 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
										if($id_max_length > 0) $id_length_text = '영문, 숫자로 '.$id_min_length.'자~'.$id_max_length.'자 이내로 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
										echo $id_length_text;
										?>
									</div>
								</td>
							</tr>
							<tr>
								<th class="ess"><span class="tit">비밀번호</span></th>
								<td>
									<input type="password" name="join_pw" class="input_design js_join_pw" placeholder="" autocomplete="new-password" />
									<div class="tip_txt "style="display:none">
										<?php
										$pw_length_text = '숫자, 영문';
										if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) $pw_length_text .= '(대문자 '.$siteInfo['join_pw_up_length'].'자 이상 포함)';
										if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) $pw_length_text .= ', 특수문자(~!@#$%^&*()_+|<>?:{} 중 '.$siteInfo['join_pw_up_length'].'자 이상)';
										if($pw_max_length > 0) $pw_length_text .= '을 포함하여 '.$pw_min_length.'자~'.$pw_max_length.'자 이내로 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
										else $pw_length_text .= '을 포함하여 '.$pw_min_length.'자 이상 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
										echo $pw_length_text;
										?>
									</div>
								</td>
							</tr>
							<tr>
								<th class="ess"><span class="tit">비밀번호 확인</span></th>
								<td>
									<input type="password" name="join_repw" class="input_design js_join_repw" placeholder="" autocomplete="new-password"/>
									<div class="tip_txt "style="display:none">동일하게 다시 한 번 입력해주세요.</div>
								</td>
							</tr>
							<tr>
								<th class="ess"><span class="tit">이름</span></th>
									<td<?php echo ($siteInfo['join_birth'] == 'N'?' colspan="3"':null); ?>>
											<div class="input_box">
													<?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
															<input type="text" name="join_name" value="" class="input_design js_auth_before auth_name" placeholder="" style="width:120px" readonly />
													<?php } else { ?>
															<input type="text" name="join_name" class="input_design" placeholder="" style="width:120px" />
													<?php } ?>
													<div class="tip_txt if_beside"style="display:none">실명을 입력해주세요.</div>
											</div>
									</td>
								</tr>
								<tr>
									<?php if($siteInfo['join_birth'] == 'Y') { ?>
									<th<?php echo ($siteInfo['join_birth_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">생년월일</span></th>
									<td>
											<?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
											<input type="text" name="_birth" class="input_design if_date js_auth_before auth_birth" value="" placeholder="" style="width:120px" readonly />
											<?php } else { ?>
											<!-- 달력나옴 -->
											<input type="text" name="_birth" class="input_design if_date js_pic_day_max_today" value="" placeholder="" style="width:120px" readonly />
											<?php } ?>
									</td>
									<?php } ?>
								</tr>
								<tr>
								<th class="ess"><span class="tit">매장명</span></th>
									<td>
										<div class="input_box">
										<input type="text" name="nickNm" class="input_design js_join_pw" maxlength="20" value="">
										</div>
									</td>
								</tr>
								<tr>
									<th class="ess"><span class="tit">사업자번호</span></th>
									<td>
										<div class="input_box">
										<input type="text" name="busiNo" class="input_design js_join_pw"id="busiNo" data-pattern="gdNum" maxlength="10" placeholder="- -없이 입력하세요." value="" data-overlap-businofl="" data-charlen="10" data-oldbusino="">
										</div>
									</td>
								</tr>
								<tr>
										<th class="ess"><span class="tit">사업자등록증 업로드</span></th>
										<td>
											<div class="input_box">
											<input type="file" name="busiFiles" class="input_design js_join_pw" id="busiFiles">
											</div>
										</td>
								</tr>
								<tr>
									<th class="ess"><span class="tit">대표번호</span></th>
									<td class="member_address">
										<div class="address_postcode">
											<input type="text" id="cellPhone" class="input_design js_join_pw" name="cellPhone" maxlength="12" placeholder="- 없이 입력하세요." data-pattern="gdNum" value="">
										</div>
										<div class="form_element">
										<input type="checkbox" id="smsFl" name="smsFl" value="y">
											<label for="smsFl" class="check_s ">정보/이벤트 SMS 수신에 동의합니다.</label>
										</div>
										<label>※ 입금 확인 및 주문 상태 문자가 대표번호로 발송되니, 꼭 실제 사용하는 휴대폰번호를 입력해주세요!</label>
									</td>
								</tr>
								<tr>
									<th class="ess"><span class="tit">전화번호(집)</span></th>
									<td>
										<div class="member_warning">
											<input type="text" id="phone" class="input_design js_join_pw"name="phone" maxlength="12" placeholder="- 없이 입력하세요." data-pattern="gdNum" value="">
										</div>
									</td>
								</tr>
								<tr>
									<th class="ess"><span class="tit">전화번호(매장)</span></th>
									<td>
										<div class="member_warning">
											<input type="text" id="comPhone"class="input_design js_join_pw" name="comPhone" maxlength="12" placeholder="- 없이 입력하세요." data-pattern="gdNum" value="">
										</div>
									</td>
								</tr>
								<?php if($siteInfo['join_addr'] == 'Y') { ?>
								<tr>
									<th <?php echo ($siteInfo['join_addr_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">주소</span></th>
									<td>
										<div class="input_box">
											<input type="text" name="join_zonecode" id="_zonecode" class="input_design" value="" style="width:70px" readonly="readonly"/>
											<a href="#none" onclick="new_post_view(); return false;" class="c_btn h30 light">주소검색</a>
										</div>

										<div class="input_full">
											<input type="text" name="join_address_doro" id="_addr_doro" class="input_design" placeholder="도로명 주소" readonly="readonly"/>
											<input type="text" name="join_address2" id="_addr2" class="input_design" placeholder="나머지 주소" />
										</div>
									</td>
							</tr>
							<tr>
								<th class="ess"><span class="tit">이메일 주소</span></th>
								<td colspan="3">
									<div class="input_box mail">
										<input type="text" name="_email_prefix" class="input_design js_email_prefix" placeholder="이메일 아이디" style="width:150px"/>
										<span class="mail_icon">＠</span>
										<select name="_email_suffix_select" class="js_email_suffix_select">
											<option value="">선택해주세요</option>
											<?php foreach($email_suffix as $ek=>$ev) { ?>
												<option value="<?php echo $ev; ?>"><?php echo ($ev == 'direct'?'직접입력':str_replace('@', '', $ev)); ?></option>
											<?php } ?>
										</select>
										<!-- 직접입력 선택시 노출 / 그 전에는 숨김 -->
										<input type="text" name="_email_suffix_input" class="input_design js_email_suffix_input" style="width:150px; display: none;"/>
										<a href="#none" class="c_btn h30 light js_email_overlap_check">이메일 중복체크</a>
									</div>
									<div class="tip_txt black">주문 등과 관련된 중요한 메일이 발송됩니다.</div>
									<input type="hidden" name="join_email" class="js_join_email" value="">
									<input type="hidden" name="join_email_check" class="js_join_email_check" value="">
								</td>
							</tr>
							<?php if($siteInfo['join_sex'] == 'Y' || $siteInfo['join_tel'] == 'Y') { ?>
								<tr style="display:none">
									<?php if($siteInfo['join_sex'] == 'Y') { ?>
										<th<?php echo ($siteInfo['join_sex_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">성별</span></th>
										<td<?php echo ($siteInfo['join_sex'] == 'Y' && $siteInfo['join_tel'] == 'N'?' colspan="3"':null); ?>>
												<label class="label_design"><input type="radio" name="_sex" value="M"<?php echo ($siteInfo['s_join_auth_use'] == 'Y'?' onclick="return false;" class="js_auth_before auth_sex"':null); ?>/><span class="txt">남성</span></label>
												<label class="label_design"><input type="radio" name="_sex" value="F"<?php echo ($siteInfo['s_join_auth_use'] == 'Y'?' onclick="return false;" class="js_auth_before auth_sex"':null); ?>/><span class="txt">여성</span></label>
										</td>
									<?php } ?>
									</tr>
									<tr style="display:none">
									<?php if($siteInfo['join_tel'] == 'Y') { ?>
										<th<?php echo ($siteInfo['join_tel_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">전화번호</span></th>
										<td<?php echo ($siteInfo['join_sex'] == 'N'?' colspan="3"':null); ?>>
											<input type="text" name="join_tel" class="input_design" placeholder="전화번호" style="width:180px" />
											<div class="tip_txt ">휴대폰 이외 유선전화가 필요한 경우 입력해주세요.</div>
										</td>
									<?php } ?>
								</tr>
							<?php } ?>
								
							<tr style="display:">
								<th class="ess"><span class="tit">휴대폰 번호</span></th>
									<td>
										<div class="input_box">
												<?php if($siteInfo['s_join_auth_use'] == 'Y'){ ?>
														<input type="text" name="join_tel2" class="input_design auth_phone js_auth_before" placeholder="휴대폰 번호" style="width:180px" readonly/>
														<input type="button" onclick="auth_type_check();" class="c_btn h30 light" value="휴대폰 본인인증">
												<?php }else{ ?>
														<input type="text" name="join_tel2" class="input_design" placeholder="휴대폰 번호" style="width:180px" />
												<?php } ?>
										</div>
										<div class="tip_txt black">주문 등과 관련된 중요한 문자가 발송됩니다.</div>
									</td>
								</tr>
								<tr style="display:none">
									<th><span class="tit">SMS 수신</span></th>
									<td>
										<label class="label_design"><input type="radio" name="join_smssend" value="Y" checked/><span class="txt">수신</span></label>
										<label class="label_design"><input type="radio" name="join_smssend" value="N"/><span class="txt">수신거부</span></label>
										<div class="tip_txt ">비정기적으로 문자 서비스를 제공합니다.</div>
									</td>
							</tr>
							<tr style="display:none">
								<th><span class="tit">이메일 수신</span></th>
								<td colspan="3">
									<label class="label_design"><input type="radio" name="join_emailsend" value="Y" checked/><span class="txt">수신</span></label>
									<label class="label_design"><input type="radio" name="join_emailsend" value="N"/><span class="txt">수신거부</span></label>
									<div class="tip_txt if_beside">비정기적으로 메일링 서비스를 제공합니다.</div>
								</td>
							</tr>
							<tr style="display:none">
									<?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>
									<th<?php echo ($siteInfo['join_addr_required'] == 'Y'?' class="ess"':null); ?>><span class="tit">지번 주소</span></th>
									<td>
										<div class="input_box" style="display:none;">
											<input type="text" name="join_zip1" id="_post1" class="input_design" value="" style="width:50px" readonly="readonly"/>
											<span class="dash">-</span>
											<input type="text" name="join_zip2" id="_post2" class="input_design" value="" style="width:50px" readonly="readonly"/>
											<div class="tip_txt if_beside">구 우편번호</div>
										</div>
										<div class="input_full">
											<input type="text" name="join_address1" id="_addr1" class="input_design" placeholder="지번 주소" readonly="readonly"/>
										</div>
										<div class="tip_txt">주소검색을 통해 자동으로 입력됩니다.</div>
									</td>
								</tr>
							<?php } ?>
							<?php if($siteInfo['join_spam'] == 'Y' && $siteInfo['recaptcha_api'] && $siteInfo['recaptcha_secret']) { ?><!-- 2020-05-14 SSJ :: 회원가입 정책 스팸방지 설정 적용 -->
								<tr>
									<th class="ess"><span class="tit">스팸방지</span></th>
									<td colspan="3">
										<!-- 스팸방지 들어감 -->
										<script src="//www.google.com/recaptcha/api.js"></script>
										<div class="g-recaptcha" data-sitekey="<?php echo $siteInfo['recaptcha_api']; ?>"></div>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<div class="c_group_tit"><span class="tit">부가정보</span><span class="sub_txt">체크된 항목은 필수 항목입니다. 꼭 입력해주시기 바랍니다.</span></div>
							<div class="c_form">
								<table>	
									<colgroup>
											<col width="150"/><col width="*"/><col width="150"/><col width="*"/>
										</colgroup>
								<tbody>
									<tr>
										<th class="ess"><span class="tit">업체구분</span></th>
										<td>
											<div class="member_warning">
											<input type="text" class="input_design" name="ex2" id="ex2" value="">
												<select name="ex1" class="chosen-select" style="display: none;">
													<option value="">선택</option>
													<option value="보세점운영"style="display:none">보세점운영</option>
													<option value="브랜드(체인점)운영"style="display:none">브랜드(체인점)운영</option>
												</select><div style="display:none"class="chosen-container chosen-container-single chosen-container-single-nosearch" style="width: 121px;" title=""><a class="chosen-single"><span>선택</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off" readonly=""></div><ul class="chosen-results"><li class="active-result result-selected" data-option-array-index="0" style="">선택</li><li class="active-result" data-option-array-index="1" style="">보세점운영</li><li class="active-result" data-option-array-index="2" style="">브랜드(체인점)운영</li></ul></div></div>
											</div>
										</td>
									</tr>
									<tr>
										<th class="ess"><span class="tit">홈페이지 주소</span></th>
										<td>
											<div class="member_warning">
												<input type="text" class="input_design" name="ex2" id="ex2" value="">
											</div>
										</td>
									</tr>
							</tbody>
						</table>
					</div>
					<!-- //addition_info_sec -->
				</div>
			
                
				<div class="c_btnbox ">
					<ul>
						<li><a href="#none" onclick="history.go(-1); return false;" class="c_btn h55 black line">이전단계</a></li>
						<li><a href="#none" class="c_btn h55 black js_submit" accesskey="s">가입완료</a></li>
					</ul>
				</div>
				<!-- /정보입력 -->
			</div>
		</div>
	</div>
	<!-- /공통페이지 섹션 -->
</form>
</div>

<script type="text/javascript">
	// 아이디 중복체크
	$(document).on('click', '.js_id_overlap_check', function(e) {
		e.preventDefault();
		var _id = $('.js_join_id').val();
		_id = _id.trim();
		$('.js_join_id').val(_id);
		if(!_id) {
			alert('아이디를 입력해주세요.');
			$('.js_join_id').focus();
			return false;
		}
		$.ajax({
			data: {
				_mode: 'id_check',
				_id: _id
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
					$('.js_join_id_check').val(1);
					alert(msg);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
				else {
					$('.js_join_id_check').val('');
					alert(msg);

					// 아이디 항목에 포커스 주기
					$('.js_join_id').focus();

					// 다시 들어간 포커스를 글자 맨뒤로 밀기 위한 처리
					$('.js_join_id').val('');
					$('.js_join_id').val(_id);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
			}
		});
	});


	// 이메일 항목제어
	$(document).on('change', '.js_email_suffix_select', join_email_form_view);
	function join_email_form_view() {
		var i_value = $('.js_email_prefix').val();
		var s_value = $('.js_email_suffix_select option:selected').val();
		var save_value = $('.js_join_email').val();
		var r_val = '';
		if(save_value != i_value.replace('@', '')+'@'+$('.js_email_suffix_input').val().replace('@', '')) $('.js_join_email_check').val('');
		if(s_value == 'direct') {
			$('.js_email_suffix_input').show();
		}
		else {
			$('.js_email_suffix_input').val(s_value);
			$('.js_email_suffix_input').hide();
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
			$('.js_email_suffix_input').show();
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
		var UserInput = $('.js_email_suffix_input').is(':visible');
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

		// - 영문+숫자 - 특정위치에서 alphanumeric가 증발하는 경우 있음으로 재지정
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
			var pattern = /[0-9a-zA-Z]$/i;
			return this.optional(element) || pattern.test(value);
		}, "영문, 숫자로 입력해주세요");


		// 벨리데이션
		$('.js_join_form').validate({
			ignore: 'input[type=text]:hidden',
			rules: {
				join_id: {
					  required : true
					, alphanumeric: true
					, minlength: <?php echo $id_min_length; ?>
					<?php if($id_max_length > 0) { ?>, maxlength: <?php echo $id_max_length; ?><?php } echo PHP_EOL; ?>
				}
				, join_id_check: { required : true }
				, join_name: { required : true }
				, join_pw: {
					  required : true
					, minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
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
					  required : true
					, minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
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
					, join_address1: { required : true }
					, join_address_doro: { required : true }
					, join_zonecode: { required : true }
					, join_address2: { required : true }
				<?php } ?>
			},
			messages: {
				join_id: {
					  required : '아이디를 입력해주세요'
					, minlength: '아이디는 최소 <?php echo $id_min_length; ?>자 이상 입력해주세요'
					<?php if($id_max_length > 0) { ?>, maxlength: '아이디는 최대 <?php echo $id_max_length; ?>자 이하로 입력해주세요'<?php } echo PHP_EOL; ?>
					, alphanumeric: '아이디는 영문, 숫자로 입력해주세요'
				}
				, join_id_check: { required : '아이디 중복검사를 해주세요' }
				, join_name: { required : '이름을 입력해주세요' }
				, join_pw: {
					  required : '비밀번호를 입력해주세요'
					, minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
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
					  required : '비밀번호 확인을 입력해주세요'
					, minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
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
				<?php if($siteInfo['join_tel'] == 'Y' && $siteInfo['join_tel_required'] == 'Y'){ ?>
					, join_tel: { required: '전화번호를 입력해주세요', tel_check: '전화번호 형식이 유효하지않습니다' }
				<?php } ?>
				, _email_prefix: { required : '이메일 아이디를 입력해주세요' }
				, _email_suffix_input: { required : '이메일 주소를 '+($('.js_email_suffix_input').is(':visible')?'입력':'선택')+'해주세요' }
				, join_email: { required : '이메일 주소를 입력해주세요', email_check: '유효하지 않은 E-Mail주소입니다' }
				, join_email_check: { required : '이메일 중복검사를 해주세요' }
				, join_emailsend: { required : '이메일 수신여부를 선택해주세요' }
				<?php if($siteInfo['join_addr'] == 'Y' && $siteInfo['join_addr_required'] == 'Y'){ ?>
					, join_address1: { required : '주소검색을 통하여 기본주소를 입력해주세요' }
					, join_address_doro: { required : '주소검색을 통하여 도로명주소를 입력해주세요' }
					, join_zonecode: { required : '주소검색을 통하여 새 우편번호를 입력해주세요' }
					, join_address2: { required : '나머지주소를 입력해주세요' }
				<?php } ?>
			}
            ,submitHandler: function(form){
                // do other things for a valid form
                if(typeof kcp_submit == 'function') if(!kcp_submit()) return false;
                form.submit();
            }
		});
	});
</script>
<?php include_once(OD_ADDONS_ROOT.'/newpost/newpost.search.php'); // 다음주소찾기 ?>