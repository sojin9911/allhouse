<?php 
	$app_current_link = '_product_icon.list.php';
	include_once('wrap.header.php');

	// 상품아이콘에 자동적용아이콘 추가
	$arr_product_icon_type2 = array_merge(array('product_coupon_small_icon'=>'자동적용 아이콘 - 상품쿠폰 ( 40 x 20 )', 'product_freedelivery_small_icon'=>'자동적용 아이콘 - 무료배송 ( 40 x 20 )', 'product_promotion_small_icon'=>'자동적용 아이콘 - 기획전 ( 40 x 20 )'), $arr_product_icon_type);

	if( $_mode == "modify" ) {
		$que = "  select * from smart_product_icon where pi_uid='". $_uid ."' ";
		$r = _MQ($que);
	}else{
		$_mode = 'add';
		$r['pi_type'] = array_shift(array_keys($arr_product_icon_type));
	}

?>



	<!-- ● 단락타이틀 -->

	<form name="frm" method="post" action="_product_icon.pro.php" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">	
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>아이콘 유형</th>
						<td>
							<?php 
								if(in_array($r['pi_type'],array_keys($arr_product_icon_type))){
									echo _InputSelect( '_type' , array_keys($arr_product_icon_type) , $r['pi_type'] , '' , array_values($arr_product_icon_type) , '-유형선택-');
								}else{
									echo $arr_product_icon_type2[$r['pi_type']];
									echo '<input type="hidden" name="_type" value="'. $r['pi_type'] .'">';
								}
							?>
						</td>
					</tr>
					<tr>
						<th>아이콘 타이틀</th>
						<td>
							<input type="text" name="_title" class="design" value="<?php echo $r['pi_title']; ?>" placeholder="" style="width:500px">

						</td>
					</tr>
					<tr>
						<th>노출순위</th>
						<td>
							<?php if(in_array($r['pi_type'],array_keys($arr_product_icon_type))){ ?>
								<input type="text" name="_idx" class="design number_style" value="<?php echo ($r['pi_idx']?$r['pi_idx']:1); ?>" placeholder="" style="width:50px"><span class="fr_tx">순위</span>
								<?php echo _DescStr('노출순위가 낮을수록 먼저 나오며, 순위가 같으면 최근 등록한 아이콘이 먼저 나옵니다.'); ?>
							<?php }else{ ?>
								<?php echo _DescStr('자동적용 아이콘은 1개만 등록가능 합니다. '); ?>
								<input type="hidden" name="_idx" value="1">
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th>아이콘 이미지(PC)</th>
						<td>
							<?php echo _PhotoForm( '..'.IMG_DIR_ICON , '_img'  , $r['pi_img'] , 'style="width:250px"'); ?>
						</td>
					</tr>
					<tr>
						<th>아이콘 이미지(MOBILE)</th>
						<td>
							<?php echo _PhotoForm( '..'.IMG_DIR_ICON , '_img_m'  , $r['pi_img_m'] , 'style="width:250px"'); ?>
							<div class="tip_box">
								<?php echo _DescStr('아이콘 이미지(MOBILE)를 등록하지 않으면 모바일에서도 아이콘 이미지(PC)의 아이콘이 노출됩니다.'); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>

		</div>
		
		<?php echo _submitBTN('_product_icon.list.php' , ($_type ? '_type='.$_type : '')); ?>
	
	</form>


<script>

	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						_type: { required: true }
						,_title: { required: true }
						,_idx: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						_type : { required: '아이콘유형를 선택해주시기 바랍니다.' }
						,_title : { required: '아이콘 타이틀을 입력해주시기 바랍니다.' }
						,_idx : { required: '순위를 입력해주시기 바랍니다.' }
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});

</script>


<?php include_once('wrap.footer.php'); ?>