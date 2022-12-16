<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<div class="title">커뮤니티</div>
			<!-- 로케이션 -->
			<div class="c_location">
				<ul>
					<li>홈</li>
					<li>커뮤니티</li>
					<li>제휴문의</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->






		<?php include_once($SkinData['skin_root'].'/community.header.php'); // -- 공통해더 --  ?>


		<form name="frm_partner" id="frm_partner" method=post action="<?php echo OD_PROGRAM_URL.'/service.partner.pro.php'; ?>" enctype="multipart/form-data" target="common_frame"  >
			<input type="hidden" name="_menu" value="partner">
			<!-- ◆게시판 쓰기 (공통) -->
			<div class="c_form c_board_form">
				<!-- 리스트 제어 -->
				<div class="c_list_ctrl">
					<div class="tit_box">
						<!-- 게시판명 -->
						<span class="tit">제휴문의</span>
					</div>
				</div>
				<table>
					<colgroup>
						<col width="150"/><col width="*"/><col width="150"/><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<th class="ess"><span class="tit ">이름/상호명</span></th>
							<td ><?php // 내부패치 68번 진행 로그인시 로그인정보 가져오기 kms 2019-11-06 ?>
								<input type="text" name="_comname" class="input_design" placeholder="이름/상호명을 입력해주세요."  value="<?php echo $mem_info['in_name']; ?>" style="width:180px" />
								<div class="tip_txt">개인은 이름을 업체일 경우에는 상호명을 입력하세요.</div>
							</td>
							<th class="ess"><span class="tit ">연락처</span></th>
							<td >
								<input type="text" name="_tel" class="input_design" placeholder="연락처를 입력해주세요."  value="<?php echo $mem_info['in_tel2']; ?>" style="width:160px" />
								<div class="tip_txt">바로 연락받으실 수 있는 연락처를 기재해주십시오.</div>
							</td>
						</tr>
						<tr>
							<th class="ess"><span class="tit ">이메일 주소</span></th>
							<td colspan="3">
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
									<input type="text" name="_email_prefix" class="input_design js_email_prefix" placeholder="이메일 아이디" value="<?php echo $_email_prefix; ?>" style="width:150px"/>
									<span class="mail_icon">＠</span>

									<select name="_email_suffix_select" class="js_email_suffix_select">
										<?php foreach($email_suffix as $ek=>$ev) { ?>
											<option value="<?php echo $ev; ?>"<?php echo ($_email_suffix == $ev?' selected':(!in_array($_email_suffix, $email_suffix) && $ev == 'direct'?' selected':null)); ?>><?php echo ($ev == 'direct'?'직접입력':str_replace('@', '', $ev)); ?></option>
										<?php } ?>
									</select>

									<!-- 직접입력 선택시 노출 / 그 전에는 숨김 -->
									<input type="text" name="_email_suffix_input" class="input_design js_email_suffix_input" style="width:150px; display: none;"/>
								</div>
							</td>
						</tr>
						<tr>
							<th class="ess"><span class="tit ">문의제목</span></th>
							<td colspan="3">
								<input type="text" name="_title" class="input_design" value="<?php echo $_email_suffix; ?>"  placeholder="제목을 입력해주세요." />
							</td>
						</tr>
						<tr>
							<th class="ess"><span class="tit ">내용</span></th>
							<td colspan="3">
								<!-- 에디터들어감 -->
								<div class="textarea_box"><textarea name="_content" rows="10" style="" class="textarea_design" placeholder=""></textarea></div>
								<div class="tip_txt black">글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가해 주시기 바랍니다.</div>
							</td>
						</tr>
						<tr>
							<th class=""><span class="tit ">첨부파일</span></th>
							<td colspan="3">
								<!-- 파일첨부 추가 삭제시 if_add 클래스 추가 / form_file 반복 -->
								<div class="duplicate if_add">
									<div class="form_file list-files" data-mode="add">
										<div class="input_file_box">
											<input type="text" id="fakeFileTxt1" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="<?php echo implode(",",$arrUpfileConfig['ext']['file']); ?>파일만 등록 가능합니다. 용량이 많을때에는 파일만 대용량이메일로 보내주시기 바랍니다.">
											<div class="fileDiv">
												<input type="button" class="buttonImg" value="파일찾기">
												<input type="file" class="realFile" name="addFile[]" onchange="javascript:document.getElementById('fakeFileTxt1').value = this.value">
											</div>
										</div>
										<!-- 추가 -->
										<span class="add_btn_box"><a href="#none" onclick="return false;" class="c_btn h30 black exec-addfile">+ 추가</a></span>
									</div>
								</div>

							</td>
						</tr>
						<?php if( $partnerData['recaptchaUse'] === true) { ?>
						<tr class="tr-recaptcha">
							<th class="ess"><span class="tit">스팸방지</span></th>
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


			<!-- ◆개인정보처리방침 안내 -->
			<div class="c_agree" >
				<div class="agree_form">
					<div class="c_group_tit">
						<span class="tit">개인정보처리방침 안내</span>

					</div>
					<div class="form">
						<div class="text_box">
							<textarea cols="" rows="12" name="" readonly="" class="textarea_design"><?php echo $partnerData['partnerAgree']; ?></textarea>
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
					<li><a href="#none" onclick="return false;" class="c_btn h55 color js_partner_submit" data-switch="on">문의하기</a></li>
				</ul>
			</div>


		</form>
	</div>
</div>
<!-- /공통페이지 섹션 -->
<script>
	// 이메일 항목제어
	$(document).ready(function(){
		join_email_form_view();

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");

	});
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

	// -- 파일 삭제
	$(document).on('click','.exec-delfile',function(){
		$(this).closest('.list-files[data-mode="add"]').remove();
		$('.list-files[data-mode="add"]').each(function(i,v){
			$(v).find('.files-idx').text(i+1);
		});
	});

	// -- 파일 추가 --
	$(document).on('click','.exec-addfile',function(){
		var idx = $('.list-files').length*1;
		var buid = $('#frmBbs [name="_uid"]').val();
		var upfileCnt = <?php echo $arrUpfileConfig['cnt']; ?>;

		if( idx >= upfileCnt){ alert("파일첨부는 최대 <?php echo $arrUpfileConfig['cnt']; ?>개 까지 첨부가능합니다.\n등록된 파일이 있으실경우 삭제 하신 후 추가해 주세요."); return false; }
		var url = '<?php echo OD_PROGRAM_URL.'/board.ajax.php'; ?>';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'execAddfile',idx : idx , buid : buid  }, success: function(data){
	      	if( data.rst == 'success') {
		      	$('.list-files:last-child').after(data.html);
		      	return true;
		      }else{
		      	return false;
		      }
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
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