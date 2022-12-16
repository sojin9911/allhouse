<?php // -- LCY :: 2017-09-20 -- 운영자관리 폼
		$app_current_link = '_product.common_option_set.list.php';
		include_once('wrap.header.php');
		if( in_array($_mode,array('modify','add')) == false){
			error_loc_msg("_product.common_option_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다.");
		}

		// -- 모드별 처리
		if( $_mode == 'modify'){ // 수정일 시
			$row = _MQ("select *from smart_common_option_set where cos_uid = '".$_uid."' ");
			if( count($row) < 1 ) {error_loc_msg("_product.common_option_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다.");  }
		}else{ // 추가일시

		}

?>

		<form action="_product.common_option_set.pro.php" name="frm" id="frm"  method="post">
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<input type="hidden" name="_mode" value="<?=$_mode?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<?php if($_mode == 'modify') { ?>
		<input type="hidden" name="_uid" value="<?=$_uid?>">
		<?php } ?>


		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">옵션관리명</th>
						<td>
							<input type="text" name="_name" class="design" style="" value="<?=$row['cos_name']?>" />
							<?php echo _DescStr("옵션관리명을 입력해 주세요."); ?>
						</td>
					</tr>

					<tr>
						<th class="ess">옵션노출방식</th>
						<td>
							<?php echo _InputRadio( '_type' , array('option','addoption'), (in_array($row['cos_type'], array('option','addoption')) == false ? 'option':$row['cos_type']) , '' , array('일반옵션', '추가옵션') , ''); ?>
							<div class="tip_box">
								<?php echo _DescStr("옵션노출방식은 한번 등록 시 변경이 불가능합니다.",'black'); ?>
							</div>
						</td>
					</tr>

					<tr>
						<th class="ess">옵션차수</th>
						<td>

							<div style="clear:both;">
								<?php echo _InputRadio( '_depth' , array('1','2','3'), (in_array($row['cos_depth'], array('1','2','3')) == false ? '1':$row['cos_depth']) , ' class="_option_type_chk" ' , array('1차옵션', '2차옵션','3차옵션') , ''); ?>
							</div>

							<div class="option_type" style="clear:both; <?=(in_array($row['cos_depth'] , array('1','2','3')) ? '' : 'display:none;')?>">
								<div class="dash_line init_depth1_type init_depth_type"><!-- 점선라인 --></div>
								<dl class="init_depth1_type init_depth_type">
									<dt>1차 옵션유형</dt>
									<dd>
										<div class="lineup-center">
											<?php echo _InputRadio('cos_option1_type', array('normal' , 'color', 'size'), ($row['cos_option1_type']?$row['cos_option1_type']:'normal'), ' class="btn_hide_input " ', array('일반' , '컬러', '사이즈')); ?>
										</div>
									</dd>
								</dl>

								<dl class="init_depth2_type init_depth_type">
									<dt>2차 옵션유형</dt>
									<dd>
										<div class="lineup-center">
											<?php echo _InputRadio('cos_option2_type', array('normal' , 'color', 'size'), ($row['cos_option2_type']?$row['cos_option2_type']:'normal'), ' class="btn_hide_input " ', array('일반' , '컬러', '사이즈')); ?>
										</div>
									</dd>
								</dl>

								<dl class="init_depth3_type init_depth_type">
									<dt>3차 옵션유형</dt>
									<dd>
										<div class="lineup-center">
											<?php echo _InputRadio('cos_option3_type', array('normal' , 'color', 'size'), ($row['cos_option3_type']?$row['cos_option3_type']:'normal'), ' class="btn_hide_input " ', array('일반' , '컬러', '사이즈')); ?>
										</div>
									</dd>
								</dl>

								<div class="dash_line init_depth3_type init_depth1_type init_depth_type"><!-- 점선라인 --></div>


							</div>

							<div class="tip_box">
								<?php echo _DescStr("옵션차수는 한번 등록 시 변경이 불가능합니다.",'black'); ?>
								<?php echo _DescStr("추가옵션의 경우 기본적으로 2차옵션만 설정 가능하며, 옵션유형은 선택할 수 없습니다."); ?>
							</div>
						</td>
					</tr>

					<tr>
						<th class="ess">옵션등록</th>
						<td>
						<?php if( $_mode != 'modify'){ ?>
							<?php echo _DescStr("옵션등록은 <em>확인</em> 버튼을 눌러 저장 후 등록이 가능합니다."); ?>
						<?php }else{ ?>
							<a href="#none" onclick="common_option_popup(); return false;" class="c_btn h27 black">옵션창 열기</a>
						<?php } ?>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<?php echo _submitBTN('_product.common_option_set.list.php'); ?>

		</form>



	<div class="ajax-data-box" data-visit-ahref=""></div>
	<script>

		$(document).ready(common_option_init)

		// 옵션노출방식이 추가옵션일경우 처리방식
		<?php if($_mode == 'add'){ ?>
		$(document).on('click','[name="_type"]', common_option_type_init);
		<?php } ?>


		// 타입에 따른 처리
		function common_option_type_init()
		{
			// 처리확인
			var _type = $('input[name="_type"]:checked').val();
			if( _type == 'addoption'){
				$('#_depth1').attr('disabled','disabled');
				$('#_depth3').attr('disabled','disabled');
				$('[name="_depth"]').prop('checked',false);
				$('#_depth2').prop('checked',true);

				$('.init_depth_type').hide(); // 숨기기
			}else{
				$('#_depth1').removeAttr('disabled');
				$('#_depth3').removeAttr('disabled');
				$('.init_depth_type').show();
			}
			onoff_option();
		}


		// -- 조건에 따른 초기화 실행 --
		function common_option_init()
		{
				var _mode = $('[name="_mode"]').val();
				if( _mode == 'modify'){

					// -- 옵션노출방식 :: 수정일경우 변경못하도록
					$('[name="_type"]').each(function(i,v){
						var val = $(v).val();
						var chkVal = $(v).prop('checked');
						if( chkVal == false){ $(v).attr('disabled','disabled'); }
					});

					// -- 옵션차수선택 :: 수정일경우 변경못하도록
					$('[name="_depth"]').each(function(i,v){
						var val = $(v).val();
						var chkVal = $(v).prop('checked');
						if( chkVal == false){ $(v).attr('disabled','disabled'); }
					});

					// -- 옵션유형 :: 수정일경우 변경못하도록
					$('.option_type input[type="radio"]').each(function(i,v){
						var val = $(v).val();
						var chkVal = $(v).prop('checked');
						if( chkVal == false){ $(v).attr('disabled','disabled'); }
					});

				}

		}

		// -- 공통옵션 등록 팝업
		function common_option_popup()
		{
			var pass_common_type = $('[name="_type"]:checked').val();
			var pass_common_mode = $('[name="_depth"]:checked').val();
			var pass_common_uid = $('[name="_uid"]').val();
			window.open('_product.common_option.form.php?pass_common_mode='+pass_common_mode+'&pass_common_uid=' + pass_common_uid+'&pass_common_type='+pass_common_type ,'option','width=1120,height=638,scrollbars=yes');
		}

	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						_name: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.


				},
				messages: {
						_name : { required: '옵션관리명을 입력해 주세요.' }
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});




		// 옵션설정에 따른 노출
		function onoff_option() {

			var _type = $('input[name="_type"]:checked').val();


			// 옵션사용하지 않을 경우 옵션유형 모두 닫기
			if($('._option_type_chk:checked').val() == 'nooption') {
				$(".option_type").hide();
			}
			else {
				$(".option_type").show(); // 옵션유형 div 열기
				$(".init_depth_type").hide(); // 옵션유형 항목 일단 모두 닫기
				if($('._option_type_chk:checked').val() == '1') {
					$(".init_depth1_type").show(); // 1차만 열기
				}
				else if($('._option_type_chk:checked').val() == '2') {
					$(".init_depth1_type").show(); $(".init_depth2_type").show(); // 1차,2차 열기
				}
				else if($('._option_type_chk:checked').val() == '3') {
					$(".init_depth_type").show(); // 모두 열기
				}
			}

			// 추가옵션일 경우 옵션의 유형은 가린다.
			if( _type == 'addoption'){
				$(".init_depth_type").hide(); // 모두 닫기
			}

		}
		$(document).ready(onoff_option);
		$(document).on('click', '._option_type_chk', onoff_option);


	</script>




<?php
	include_once('wrap.footer.php');

		// 주소찾기 - 우편번호찾기 박스
	include_once OD_ADDONS_ROOT."/newpost/newpost.search.php";
?>