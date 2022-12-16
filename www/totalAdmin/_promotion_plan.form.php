<?php

	$app_current_link = '_promotion_plan.list.php';

	include_once('wrap.header.php');

	// - 수정 ---
	if( $_mode == "modify" ) {
		$que = " select * from smart_promotion_plan where pp_uid='${uid}'  ";
		$row = _MQ($que);

		// smart_table_text를 통해 pp_content(PC내용), pp_content_m(모바일 내용) 정보 가져옴
		$row = array_merge($row , _text_info_extraction( "smart_promotion_plan" , $row['pp_uid'] ));

	}
	// - 수정 ---

	else {
		// 추가일 경우
		$uid = '0';

		// 기획전 추가 시 - 적용한 기획전 상품 삭제 --> uid가 0인 경우 삭제
		_MQ_noreturn(" delete from smart_promotion_plan_product_setup where ppps_ppuid = '". $uid ."' ");
	}


?>



<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_promotion_plan.pro.php" >
<input type="hidden" name="_mode" value="<?=$_mode?>">
<input type="hidden" name="uid" value="<?=$uid?>">
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">


	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>기획전 설정</strong></div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="140"><col width="*"><col width="140"><col width="*"><col width="140"><col width="*">
			</colgroup>
			<tbody>

				<tr>

					<th>기획전명</th>
					<td>
						<input type="text" name="_title" class="design" value="<?php echo stripslashes(strip_tags($row['pp_title'])); ?>" placeholder="" >
					</td>

					<th>진행기간</th>
					<td >
						<input type="text" name="_sdate" value="<?=$row['pp_sdate']?>" class="design js_pic_day" style="width:85px" readonly>
						<span class="fr_tx">-</span>
						<input type="text" name="_edate" value="<?=$row['pp_edate']?>" class="design js_pic_day" style="width:85px" readonly>
					</td>

					<th>기획전노출여부</th>
					<td >
						<?php echo _InputRadio( '_view' , array('Y','N'), ($row['pp_view'] ? $row['pp_view'] : 'Y') , '' , array('노출','숨김') , ''); ?>
					</td>

				</tr>

				<tr>
					<th>기획전목록이미지</th>
					<td colspan="5">
						<?php echo _PhotoForm('..'.IMG_DIR_BANNER, 'pp_img', $row['pp_img'], 'style="width:250px"'); ?>
						<?php echo _DescStr('사이트 스킨에 따라 크기는 자동 조절될 수 있습니다. <span style="font-weight:600;">기준 사이즈(515px * 190px)</span>'); ?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>기획전 내용</strong></div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">

		<!-- ● 내부탭 -->
		<div class="c_tab">
			<ul>
				<li class="hit"><a href="#none" class="btn tab_menu" data-idx="pc" data-trigger="N"><strong>기획전내용(PC)</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="mobile" data-trigger="Y"><strong>기획전내용(MOBILE)</strong></a></li>
			</ul>
			<div class="c_tip" style="float:left; margin:10px 0 0 10px;">MOBILE 미등록 시 PC내용이 MOBILE에 적용됩니다. <span style="font-weight:600;">기준 사이즈(1050px * Free)</span></div>
		</div>

		<table class="table_form">
			<tbody>
				<tr>
					<td >
						<!-- PC -->
						<div class="tab_conts" data-idx="pc">
							<textarea name="_content" class="input_text SEditor" style="width:100%;height:300px;" ><?php echo stripslashes($row['pp_content']); ?></textarea>
						</div>
						<!-- MOBILE -->
						<div class="tab_conts" data-idx="mobile" style="display:none">
							<textarea name="_content_m" class="input_text SEditor" style="width:100%;height:300px;" ><?php echo stripslashes($row['pp_content_m']); ?></textarea>
						</div>
					</td>
				</tr>
			</tbody>
		</table>


		<!-- 기획전 탭 적용 -->
		<script type="text/javascript">
			// 텝메뉴
			$(document).on('click', '.tab_menu', function() {
				$parent = $(this).closest('.data_form');
				var idx = $(this).data('idx');
				// 탭변경
				$parent.find('.tab_menu').closest('li').removeClass('hit');
				$parent.find('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
				// 입력항목변경
				$parent.find('.tab_conts').hide();
				$parent.find('.tab_conts[data-idx='+ idx +']').show();

				// 부모창이 display:none; 일때 높이 오류 수정
				var trigger_cont_editor = $(this).data('trigger')=='Y' ? true : false;
				if(trigger_cont_editor){
					$('.tab_conts[data-idx='+ idx +'] .SEditor').each(function(){
						var id = $(this).attr('id');
						if(oEditors.length > 0){
							oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
						}
					});
					$(this).data('trigger','N');
				}
			});
		</script>
		<!-- 기획전 탭 적용 -->



	</div>






	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>기획전 상품관리</strong></div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="promotion_plan_product_setup_del(); return false;" class="c_btn h27 gray">선택삭제</a>
				<a href="#none" onclick="promotion_plan_product_setup_add(); return false;" class="c_btn h27 red">상품추가</a>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->



		<!-- 상품목록 -->
		<div ID="product_table_id">
			<?php
					//$uid = $uid
					include_once("_promotion_plan.product_ajax.php");
			?>
		</div>


	</div>




	<?php echo _submitBTN('_promotion_plan.list.php'); ?>


</form>





	<!-- 기획전 상품 관련 함수 -->
	<script type="text/javascript">

		//기획전 상품 등록
		function promotion_plan_product_setup_add() {
			window.open('_promotion_plan.product_pop.php?uid=<?=$uid?>' , 'relation', 'width=1120, height=800,scrollbars=yes');
		}

		//기획전 상품 삭제
		function promotion_plan_product_setup_del() {
			if($('.class_pcode').is(":checked")){
				if(confirm("정말 삭제하시겠습니까?")){
					$("input[name='smart_promotion_plan_product_setup_mode']").val("mass_delete"); // 일괄삭제
					$("form[name='frm']").attr("action" , "_promotion_plan.product_pro.php");
					$("form[name='frm']").attr("target" , "common_frame");
					document.frm.submit();
					$("form[name='frm']").attr("action" , "_promotion_plan.pro.php");
					$("form[name='frm']").attr("target" , "");
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}


		// 기획전 상품 목록 보기
		function promotion_plan_product_setup_view(uid) {
			$.ajax({
				url: "_promotion_plan.product_ajax.php", cache: false, type: "POST",
				data: "uid=" + uid ,
				success: function(data){
					$("#product_table_id").html(data);
				}
			});
		}

	</script>
	<!-- 기획전 상품 관련 함수 -->




<?php include_once('wrap.footer.php'); ?>