<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "제휴문의";
include_once($SkinData['skin_root'].'/community.header.php'); // 상단 헤더 출력

?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<form name="frm_partner" id="frm_partner" method=post action="<?php echo OD_PROGRAM_URL.'/service.partner.pro.php'; ?>" enctype="multipart/form-data" target="common_frame"  >
		<input type="hidden" name="_menu" value="partner">
		<!-- ◆게시판 쓰기 (공통) -->
		<div class="c_form c_board_form">
			<table>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">이름/상호명</span></th>
						<td ><?php  // 내부패치 68번줄 kms 2019-11-06 ?>
							<input type="text" name="_comname" class="input_design" value="<?php echo $mem_info['in_name']; ?>" placeholder="이름/상호명을 입력해주세요."/>
						</td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">연락처</span></th>
						<td >
							<input type="tel" name="_tel" class="input_design" value="<?php echo $mem_info['in_tel2']; ?>" placeholder="연락처를 입력해주세요."/>
						</td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">이메일 주소</span></th>
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
												<option value="" selected="">이메일 주소 선택</option>
												<?php foreach($email_suffix as $ek=>$ev) { ?>
													<option value="<?php echo $ev; ?>"<?php echo ($_email_suffix == $ev?' selected':(!in_array($_email_suffix, $email_suffix) && $ev == 'direct'?' selected':null)); ?>><?php echo ($ev == 'direct'?'직접입력':str_replace('@', '', $ev)); ?></option>
												<?php } ?>
											</select>
										</div>
									</li>
									<!-- 직접입력 선택시 노출 / 그 전에는 숨김 -->
									<li class="other" style="display: none;"><input type="text" name="_email_suffix_input" placeholder="이메일 주소" value="<?php echo $_email_suffix; ?>" class="input_design js_email_suffix_input" /></li>
								</ul>
							</div>
						</td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">문의제목</span></th>
						<td>
							<input type="text" name="_title" class="input_design" placeholder="" />
						</td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">내용</span></th>
						<td>
							<!-- 에디터들어감 -->
							<div class="textarea_box"><textarea name="_content" rows="" style="" class="textarea_design" placeholder=""></textarea></div>
							<div class="tip_txt black">글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가해 주시기 바랍니다.</div>
						</td>
					</tr>
					<tr>
						<th class=""><span class="tit ">첨부파일</span></th>
						<td>
							<div class="tip_txt">첨부파일은 PC에서 등록 가능합니다.</div>
						</td>
					</tr>
					<?php if( $partnerData['recaptchaUse'] === true) { ?>
					<tr class="tr-recaptcha">
						<th class="ess"><span class="tit ">스팸방지</span></th>
						<td colspan="3">
							<!-- 스팸방지 들어감 -->
							<script src='https://www.google.com/recaptcha/api.js'></script>
							<div class="g-recaptcha"  data-sitekey="<?php echo $siteInfo['recaptcha_api']; ?>"></div>
							<div class="tip_txt black">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>


		<?php // 내부패치 68번 kms  2019-11-06 ?>
		<!-- ◆개인정보처리방침 안내 -->
		<div class="c_agree" >
			<div class="agree_form">
				<div class="c_group_tit">
					<span class="tit">개인정보처리방침 안내</span>

					<!-- 개인정보처리방침페이지로 이동 -->
					<a href="/?pn=pages.view&type=agree&data=privacy" class="btn" target="_blank">전체보기</a>
				</div>
				<div class="form">
					<div class="text_box">
						<textarea cols="" rows="12" name="" readonly="readonly" class="textarea_design"><?php echo $partnerData['partnerAgree']; ?></textarea>
					</div>
					<div class="agree_add_info">
						<table>
							<colgroup>
								<col width="15%">
								<col width="10%">
								<col width="18%">
								<col width="*">
								<col width="*">
							</colgroup>
							<thead>
								<tr>
									<th scope="col" colspan="2">구분</th>
									<th scope="col">이용 목적</th>
									<th scope="col">수집 항목</th>
									<th scope="col">보존 및 파기</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>광고/제휴문의</td>
									<td>필수</td>
									<td>광고/제휴문의 및 상담</td>
									<td>이름/상호명, 연락처, 이메일 주소</td>
									<td>문의 및 상담 처리에 필요한 기간 동안 보존</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="agree_check"><label><input type="checkbox" value="Y" name="_agree" /> 위 내용을 읽고 이에 동의합니다.</label></div>
				</div>
			</div>
		</div>


		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h55 black js_partner_submit" data-switch="on">문의하기</a></li>
			</ul>
		</div>
	</form>
</div>
<!-- /공통페이지 섹션 -->

<script>
	// 리캡챠의 크기를 재조정 한다.
	$(document).ready(function() { // 리캽챠의 크기를 고정한다.(늘아났다가 줄어드는 현상 방지)
		recaptcha_resize();
		$('.g-recaptcha').css({
			'width': $('input[name="_title"]').outerWidth()+'px'
		});

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");

	});
	$(function(){recaptcha_resize();}); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	$(window).resize(recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	$(window).on('orientationchange', recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	function recaptcha_resize() {
		var i_width = $('input[name="_title"]').outerWidth();
		var rscale = i_width/$('.g-recaptcha iframe').width();
		if(rscale > 1) return; // 스케일이 1보다 크지 않도록 조정
		$('.g-recaptcha').css({
			'width': i_width+'px',
			'transform': 'scale('+rscale+')',
			'-webkit-transform': 'scale('+rscale+')',
			'transform-origin': '0 0',
			'-webkit-transform-origin': '0 0'
		});
	}

	// 이메일 항목제어
	$(function(){join_email_form_view();})
	$(document).on('change', '.js_email_suffix_select', join_email_form_view);
	function join_email_form_view() {
		var i_value = $('.js_email_prefix').val();
		var s_value = $('.js_email_suffix_select option:selected').val();
		var save_value = $('.js_join_email').val();
		var r_val = '';
		if(save_value != i_value.replace('@', '')+'@'+$('.js_email_suffix_input').val().replace('@', '')) $('.js_join_email_check').val('');
		if(s_value == 'direct') {
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

	$(document).on('click','.js_partner_submit',function(){
		$('#frm_partner').submit();
	});

	$(function(){
		//document.frm_partner.reset(); // 2019-04-09 SSJ :: 폼이 리셋되면서 이메일 유효성검사 시 오류 발생
		$("#frm_partner").validate({
			ignore: "input[type=text]:hidden",
		    rules: {
			 _comname: { required: true, minlength: 2 }
			, _tel: { required: true, minlength: 8 }
			, _title: { required: true, minlength: 2 }
			, _content: { required: true, minlength: 2 }
			, _email_prefix: { required : true }
			, _email_suffix_input: { required : true }
			, join_email: { required : true, email_check: true }
		    , _agree: { required: true }
		    },
		    messages: {
				_comname: { required: "이름/상호명을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _tel: { required: "연락처를 입력하세요", minlength: "8글자 이상 등록하셔야 합니다." }
				, _title: { required: "문의제목을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _content: { required: "문의내용을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _email_prefix: { required : '이메일 아이디를 입력해주세요' }
				, _email_suffix_input: { required : '이메일 주소를 '+($('.js_email_suffix_input').is(':visible')?'입력':'선택')+'해주세요' }
				, join_email: { required : '이메일 주소를 입력해주세요', email_check: '유효하지 않은 E-Mail주소입니다' }
				, _agree: { required: "개인정보처리방침 동의후 이용가능합니다." }
		    },
			submitHandler: function(form) {
				// -- 서브밋 연속 클릭 방지
				var chk = $('.js_partner_submit').attr('data-switch');
				if( chk == 'on'){
					$('.js_partner_submit').attr('data-switch','off');
					form.submit();
					setTimeout(function(){$('.js_partner_submit').attr('data-switch','on'); },3000)
				}else{
					alert("잠시만 기다려 주세요.");
					return false;
				}
			}
		});

	})


</script>