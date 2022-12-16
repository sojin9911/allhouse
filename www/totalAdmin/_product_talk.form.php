<?PHP
	if(!$_GET['pt_type']) $_GET['pt_type'] = '상품평가';
	$app_current_link = '_product_talk.list.php?pt_type='.$_GET['pt_type'];
	include_once('wrap.header.php');
	$app_path = '..'.IMG_DIR_PRODUCT;

	// - 수정 ---
	if( $_mode == "modify" ) {
		$que = " select *  from smart_product_talk where pt_uid='".$pt_uid."' ";
		$row = _MQ($que);
		$_str = "수정";
		$in_id = $row['pt_inid'];
		$pt_type = $row['pt_type'];
		$pt_depth = $row['pt_depth'];
		$pt_eval_point = $row['pt_eval_point'];
		$pt_writer = $row['pt_writer'];
	}
	// - 수정 ---
	// - 등록 ---
	else {
		$_str = "등록";
		$in_id = $siteInfo['s_adid'];
		$pt_writer = $siteInfo['s_adshop'];
	}
	// - 등록 ---

?>
<script language='javascript' src='../../include/js/lib.validate.js'></script>

<form name="frm" method="post" action="_product_talk.pro.php" enctype="multipart/form-data">
<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type="hidden" name="pt_uid" value="<?php echo $pt_uid; ?>">
<?PHP
	if( $pt_uid && $_mode == "add") {
		$ique = "
			select
				pt.* , p.p_name, p.p_img_list_square
			from smart_product_talk  as pt
			inner join smart_product as p on (pt.pt_pcode = p.p_code)
			where pt.pt_uid='".$pt_uid."'
		";
		$ir = _MQ($ique);

		// 이미지 체크
		$_p_img = get_img_src($ir['p_img_list_square']);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
		
		// 부모글 이미지 체크
		$_pt_img = get_img_src($ir['pt_img']);

		// 평점 -> 별로 변환
		$eval_str = eval_point_change_star( $ir['pt_eval_point'] );

?>
	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품정보</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>상품정보</th>
					<td>
						<div class="preview_thumb">
							<img src="<?php echo $_p_img; ?>" class="js_thumb_img" data-img="<?php echo $_p_img; ?>" alt=""><!-- 클릭하면 이미지 새창 -->
							<a href="#none" class="c_btn h27 js_thumb_popup" data-img="<?php echo $_p_img; ?>">이미지 보기</a>
						</div>
						<div class="clear_both">[ <?php echo $ir['pt_pcode']; ?> ] <?php echo $ir['p_name']; ?></div>
					</td>
				</tr>
				<tr>
					<th>글등록자</th>
					<td><?php echo $ir['pt_writer']; ?> (<?php echo $ir['pt_inid']; ?>)</td>
				</tr>
				<tr>
					<th>부모글제목</th>
					<td><?php echo stripcslashes(htmlspecialchars($ir['pt_title'])); ?></td>
				</tr>
				<tr>
					<th>부모글내용</th>
					<td>
						<?php if($_pt_img) { ?><div style="margin-bottom:5px;"><img src="<?=$_pt_img?>" alt="" style="max-width:300px;"/></div><?php } ?>
						<?php echo nl2br(stripcslashes(htmlspecialchars($ir['pt_content']))); ?>
					</td>
				</tr>
				<?php if($ir['pt_type'] == "상품평가" && $ir['pt_depth'] == 1) { ?>
					<tr>
						<th>평점</th>
						<td class="t_star"><?php echo $eval_str; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
<?
}
?>

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>내용작성</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">작성자</th>
					<td>
						<input type="text" name="pt_writer" class="design" value="<?php echo $pt_writer; ?>"/>
						<input type="hidden" name="pt_inid" value="<?php echo $in_id; ?>"/>
					</td>
				</tr>
				<? if($_mode=='modify' && $row['pt_type'] == '상품평가' && $row['pt_depth'] == 1) { ?>
				<tr>
					<th>이미지</th>
					<td>
						<?php
						if($row['pt_depth'] == 1) echo _PhotoForm($app_path, '_img', $row['pt_img'], 'style="width:300px"');
						else echo '<img src="'.$app_path.$row['pt_img'].'" alt="" width="80">';
						?>
					</td>
				</tr>
				<? } ?>
				<?php if($row['pt_depth'] == 1) { ?>
				<tr>
					<th class="ess"><?php echo ($_mode=='modify'?'본문':'답변'); ?>제목</th>
					<td>
						<input type="text" name="pt_title" class="design" value="<?php echo $row['pt_title']; ?>" style="width:98%;"/>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th class="ess"><?php echo ($_mode=='modify'?'본문':'답변'); ?>내용</th>
					<td><textarea name="pt_content" class="design" style="width:98%;height:200px;"><?php echo $row['pt_content']; ?></textarea></td>
				</tr>
			</tbody>
		</table>
	</div>

<?php echo _submitBTN("_product_talk.list.php"); ?>
</form>


<script>
	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						pt_writer: { required: true }
						<?php if($row['pt_depth'] == 1) { ?>
						,pt_title: { required: true }
						<?php } ?>
						,pt_content: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						pt_writer: { required: '작성자를 입력해주시기 바랍니다.' }
						<?php if($row['pt_depth'] == 1) { ?>
						,pt_title: { required: '<?php echo ($_mode=='modify'?'본문':'답변'); ?>제목을 입력해주시기 바랍니다.' }
						<?php } ?>
						,pt_content: { required: '<?php echo ($_mode=='modify'?'본문':'답변'); ?>내용을 입력해주시기 바랍니다.' }
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});
</script>


<?PHP
	include_once('wrap.footer.php');
?>