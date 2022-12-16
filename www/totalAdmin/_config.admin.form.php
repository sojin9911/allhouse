<?php // -- LCY :: 2017-09-20 -- 운영자관리 폼
		$app_current_link = '_config.admin.list.php';
		include_once('wrap.header.php');   
		if( in_array($_mode,array('modify','add')) == false){ error_loc_msg("_config.admin.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다."); }

		// -- 모드별 처리
		if( $_mode == 'modify'){ // 수정일 시
			$rowAdmin = _MQ("select *from smart_admin where a_uid = '".$_uid."'  ");
			if( count($rowAdmin) < 1){ error_loc_msg("_config.admin.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "운영자 정보가 없습니다." ); }
		}

?>

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>운영자 기본정보</strong><!-- 메뉴얼로 링크 --> </div>

		
		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<form name="frm" id="frm" action="_config.admin.pro.php" method="post">
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<input type="hidden" name="_mode" value="<?=$_mode?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<?php if($_mode == 'modify') { ?>
		<input type="hidden" name="_uid" value="<?=$rowAdmin['a_uid']?>"> <?php // -- 관리자 고유번호 ?>
		<?php } ?>
			<table class="table_form">	
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">운영자 아이디</th>
						<td>
							<input type="text" name="_id" class="design" style="" value="<?=$rowAdmin['a_id']?>" />
						</td>
						<th clas=<?=$_mode == 'add' ? 'ess':''?>>운영자 비밀번호</th>
						<td>
						<?php if($_mode == 'modify'){ ?> 
							<label class="design"><input type="checkbox" name="_changePw" class="on-change-pw" value="Y"> 변경</label>
							<div class="change-pw-wrap" style="display: none;">
								<div class="dash_line"><!-- 점선라인 --></div>
								<span class="fr_tx">비밀번호 변경 :</span> <input type="password" name="_pw" class="design" value="" style="width:100px">
								<div class="clear_both"></div>
								<span class="fr_tx">비밀번호 확인 :</span> <input type="password" name="_rpw" class="design" value="" style="width:100px">
								<?php echo _DescStr('6자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.', 'black'); ?>
							</div>
						<?php }else{ ?>
							<span class="fr_tx">패스워드 변경 :</span> <input type="password" name="_pw" class="design" value="" style="width:100px">
							<div class="clear_both"></div>
							<span class="fr_tx">패스워드 확인 :</span> <input type="password" name="_rpw" class="design" value="" style="width:100px">
							<?php echo _DescStr('6자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.', 'black'); ?>
						<?php } ?>
						</td>						
					</tr>
					<tr>
						<th class="ess">운영자 이름</th>
						<td>
							<input type="text" name="_name" class="design" style="" value="<?=$rowAdmin['a_name']?>" />
						</td>

						<th class="ess">승인상태</th>
						<td>
							<?php echo _InputRadio( "_use" , array('Y','N'), ($rowAdmin['a_use'] == '' ? 'N' : $rowAdmin['a_use']) , "" , array('승인','미승인') ); ?>
							<?php echo  _DescStr('<em>미승인</em>된 운영자의 경우 로그인 이용이 제한됩니다.', 'black'); ?>
						</td>

					</tr>
					<tr>
						<th class="ess">운영자 휴대폰</th>
						<td>
							<input type="text" name="_htel" class="design" style="" value="<?=tel_format($rowAdmin['a_htel'])?>" />
								<?php echo _DescStr('운영자 휴대폰은 반드시 <em>하이픈(-)</em>을 사용하여 입력해 주세요.', 'black'); ?>
						</td>						
						<th class="ess">운영자 이메일</th>
						<td><input type="text" name="_email" class="design" style="" value="<?=$rowAdmin['a_email']?>" /></td>					
					</tr>
					<tr>
						<th>직급명</th>
						<td>
							<input type="text" name="_job_title" class="design" style="" value="<?=$rowAdmin['a_job_title']?>" />
						</td>
						<th>부서명</th>
						<td>
							<input type="text" name="_corrosion_name" class="design" style="" value="<?=$rowAdmin['a_corrosion_name']?>" />
						</td>
					</tr>
				</tbody> 
			</table>
			<?php echo _submitBTN('_config.admin.list.php'); ?>
		</form>
	
		
		


<script>

	// 패스워드 변경 체크 동작
	$(document).on('click', '.on-change-pw' , function(e) {
		$('.input-admin-pw').val('');
		$('.input-admin-rpw').val('');		
		var chk = $(this).prop('checked');
		if( chk == true){ $('.change-pw-wrap').show();}
		else{  $('.change-pw-wrap').hide(); }
	});

	$(document).on('keydown','input', function(e){
		if( e.keyCode  == '13' || e.keyCode == 13){ $("form[name=frm]").submit();  }
	});

	// 폼 유효성 검사
	$(document).ready(function(){

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");


		$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					_id: { required: true }
					,_name: { required: true }
					,_pw: { required: function(){ return ($('input[name=_changePw]').is(':checked') ? true : false);} }
					,_rpw: { required: function(){ return ($('input[name=_changePw]').is(':checked') ? true : false);} }
					,_htel: { required: true }
					, _email: { required : true, email_check: true }
			},
			messages: {
					_id : { required: '아이디를 입력해 주세요.' }
					,_name : { required: '운영자 이름을 입력해 주세요.' }
					,_pw : { required: '비밀번호를 입력해 주세요.' }
					,_rpw : { required: '비밀번혹 확인을 입력해 주세요.' }
					,_htel : { required: '운영자 휴대폰번호를 입력해 주세요.' }
					, _email: { required : "이메일을 입력해 주세요.", email_check: "유효하지 않은 이메일 주소입니다" }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.

				if( $('input[name="_pw"]').val() != $('input[name="_rpw"]').val()){

					alert("입력하신 비밀번호와 확인 비밀번호가 일치하지 않습니다. ");
					return false;
				}

				form.submit();
			}
		});

	});


</script>


<?php include_once('wrap.footer.php'); ?>