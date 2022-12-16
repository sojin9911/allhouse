<?php // -- LCY :: 2017-09-20 -- 회원등급관리 폼
		$app_current_link = '_member_group_set.list.php';
		include_once('wrap.header.php');

		if( in_array($_mode,array('modify','add')) == false){ error_loc_msg("_member_group_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다."); }

		// -- 모드별 처리
		if( $_mode == 'modify'){ // 수정일 시
			$row = _MQ("select *from smart_member_group_set where mgs_uid = '".$_uid."'  ");
			if( count($row) < 1){ error_loc_msg("_member_group_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "회원등급 정보가 없습니다." ); }
			$printRank = $row['mgs_rank'];
		}else{ // 추가일시
			// -- 등록된 등급중 가장 큰 순서를 가져온다.
			$rowRank = _MQ_result("select mgs_rank from smart_member_group_set order by mgs_rank desc limit 0, 1");
			$printRank = $rowRank == '' ? 1 : ($rowRank+1);
		}



?>

		<form action="_member_group_set.pro.php" name="frm" id="frm" target="common_frame" method="post" ENCTYPE="multipart/form-data"> <?php // {{{회원등급추가}}} ?>
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<input type="hidden" name="_mode" value="<?=$_mode?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<input type="hidden" name="ajaxMode" value="<?=$_mode == 'add' ? 'add':'modify'?>"> <?php // -- ajax 모드 ?>
		<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>기본정보</strong><!-- 메뉴얼로 링크 --> </div>


		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<th class="ess">회원등급 순서</th>
						<td>
							<input type="hidden" name="_rank" value="<?=$printRank?>">
							<input type="text" name="_idx" class="design" style="width:50px;" value="<?php echo $row['mgs_idx'] ?>" <?php echo $printRank == 1 ? 'readonly':null ?>>
							<div class="tip_box">
								<?php echo _DescStr('등급순서의 경우 낮을 수록 제일 먼저 노출되며 기본등급의 경우 순서변경이 불가능합니다.', ''); ?>
							</div>
						</td>
					</tr>


					<tr class="ess">
						<th>회원등급 이름</th>
						<td>
							<input type="text" name="_name" class="design" style="" value="<?php echo $row['mgs_name'] ?>">
							<div class="tip_box">
								<?php echo _DescStr('관리할 회원등급 이름을 입력해 주세요.', ''); ?>
							</div>
						</td>
					</tr>

					<?php // {{{회원등급추가}}} ?>
					<tr>
						<th>(PC) 회원등급 아이콘</th>
						<td>
							<div class="tip_box">
								<?php echo _PhotoForm('../upfiles/icon', '_icon', $row['mgs_icon'], 'style="width:280px"'); ?>
								<?php echo _DescStr('이미지 사이즈 : 75 × 75 (pixel)'); ?>
							</div>
						</td>
					</tr>

					<tr>
						<th>(모바일) 회원등급 아이콘</th>
						<td>
							<div class="tip_box">
								<?php echo _PhotoForm('../upfiles/icon', '_mobile_icon', $row['mgs_mobile_icon'], 'style="width:280px"'); ?>
								<?php echo _DescStr('이미지 사이즈 : 200 × 200 (pixel)'); ?>
							</div>
						</td>
					</tr>
					<?php // {{{회원등급추가}}} ?>


				</tbody>
			</table>
		</div>

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>평가기준</strong><!-- 메뉴얼로 링크 --> </div>

				<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">구매금액</th>
						<td>
							<span class="fr_tx">특정기간 동안 총 구매금액이 </span><input type="text" name="_condition_totprice" <?=rm_str($printRank) <= 1 ? 'readonly':''?> class="design number_style" style="width:100px;" value="<?php echo $row['mgs_condition_totprice'] ?>"><span class="fr_tx">원 이상 일경우 해당 등급이 적용됩니다.</span>
						</td>
					</tr>

					<tr>
						<th>구매횟수</th>
						<td>
							<span class="fr_tx">특정기간 동안 총 구매횟수가 </span><input type="text" name="_condition_totcnt" <?=rm_str($printRank) <= 1 ? 'readonly':''?> class="design number_style" style="width:100px;" value="<?php echo $row['mgs_condition_totcnt'] ?>"><span class="fr_tx">회 이상 일경우 해당 등급이 적용됩니다.</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tip_box">
							<?php echo _DescStr('특정기간의 경우 <em>회원관리 > 회원 등급 정책</em> 에서 설정할 수 있습니다.', ''); ?>
							<?php echo _DescStr('등급평가 시 구매금액과 구매횟수에 모두 만족해야만 등급에 대한 적용이 됩니다.', 'black'); ?>
							<?php echo _DescStr('회원등급 순위가 1인 기본등급의 경우 평가기준을 설정할 수 없습니다.', 'black'); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>혜택설정</strong><!-- 메뉴얼로 링크 --> </div>

		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">할인율</th>
						<td>
							<input type="text" name="_sale_price_per" class="design" style="width:60px; text-align:right;" value="<?php echo str_replace("0.0","0",$row['mgs_sale_price_per']) ?>"><span class="fr_tx">%</span>
							<div class="tip_box">
							<?php echo _DescStr('상품상세 페이지에 설정된 할인율이 노출되며 구매전 할인율이 적용된 가격으로 장바구니에 저장됩니다. ', ''); ?>
							<?php echo _DescStr('※ 본 할인율은 사용자 페이지내 주문내역에는 별도로 표시되지 않으며 <em>통합관리자 > 주문/배송 > 주문관리 상세페이지 > 할인혜택</em>에서 확인이가능합니다. ', 'black'); ?>
							</div>

						</td>
					</tr>
					<tr>
						<th>적립률</th>
						<td>
							<input type="text" name="_give_point_per" class="design" style="width:60px; text-align:right;" value="<?php echo str_replace("0.0","0",$row['mgs_give_point_per']) ?>"><span class="fr_tx">%</span>
							<div class="tip_box">
							<?php echo _DescStr('상품상세 페이지에 설정된 적립률이 노출되며 구매전 설정된 적립률 만큼 추가된 적립금이 적용되어 장바구니에 저장됩니다. ', ''); ?>
							<?php echo _DescStr('※ 본 적립률은 사용자 페이지내 주문내역에는 별도로 표시되지 않으며 <em>통합관리자 > 주문/배송 > 주문관리 상세페이지 > 할인혜택</em>에서 확인이가능합니다.', 'black'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="tip_box">
							<?php echo _DescStr('할인율 및 적립률의 경우 결제금액이 아닌 상품금액 기준으로 혜택이 적용됩니다.', ''); ?>
							<?php echo _DescStr('할인율 및 적립률은 상품상세 페이지에 혜택 내용이 노출이 됩니다.', 'black'); ?>
							<?php echo _DescStr('기본 상품 판매가 및 옵션 판매가에 적용되며 추가옵션 판매가에는 적용되지 않습니다.', 'black'); ?>

							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php echo _submitBTN('_member_group_set.list.php'); ?>


		</form>


	<script>

	$(document).ready(mgsValidate);

	function mgsValidate()
	{
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						_name: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						_name : { required: '회원등급 이름을 입력해 주세요.' }
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}
		});
	}

	</script>




<?php
	include_once('wrap.footer.php');

?>