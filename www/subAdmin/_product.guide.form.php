<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_product.guide.list.php';
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$r = _MQ(" select * from smart_product_guide where g_uid = '{$_uid}' ");
}else{
	$_mode = 'add';
	$r['g_user'] = '_MASTER_'; // 등록시에는 통합관리자로 등록
}
?>
<form name="frm" action="_product.guide.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>등록구분</th>
					<td>
						<?php echo _InputSelect('g_type', array_keys($arrProGuideType), $r['g_type'], '', array_values($arrProGuideType), ''); ?>
					</td>
				</tr>
				<tr>
					<th>기본노출</th>
					<td>
						<label class="design"><input type="checkbox" name="g_default" value="Y" <?php echo ($r['g_default']=='Y'?' checked ':null); ?>>상품 등록 시 기본으로 노출되도록 설정합니다.</label>
					</td>
				</tr>
				<tr>
					<th>타이틀</th>
					<td>
						<input type="text" name="g_title" class="design" value="<?php echo $r['g_title']; ?>" style="width:400px">
					</td>
				</tr>
				<tr>
					<th>상세내용</th>
					<td>
						<textarea name="g_content" class="input_text SEditor" style="width:100%;height:300px;"><?php echo stripslashes($r['g_content']); ?></textarea>
					</td>
				</tr>
				<?php if($_mode == 'modify'){ ?>
				<tr>
					<th>참고사항</th>
					<td>
						<span class="fr_bullet normal">최종수정일 : <?php echo date('Y.m.d H:i:s', strtotime($r['g_mdate'])); ?></span>
						<br class="clear_both">
						<span class="fr_bullet normal">최초등록일 : <?php echo date('Y.m.d H:i:s', strtotime($r['g_rdate'])); ?></span>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTN($app_current_link); ?>
</form>



<script type="text/javascript">
	$(document).ready(function() {
		// -  validate ---
		$('form[name=frm]').validate({
			ignore: 'input[type=text]:hidden,input[type=button]',
			rules: {
				g_type: { required: true}	// 등록구분
				,g_title: { required: true }	// 타이틀
				,g_content: { required: true }	// 상세내용
			},
			messages: {
				g_type: { required: '등록구분을 선택해주시기 바랍니다.'}	// 등록구분
				,g_title: { required: '타이틀을 입력해주시기 바랍니다.'}	// 타이틀
				,g_content: { required: '상세내용을 입력해주시기 바랍니다.'}	// 상세내용
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
		});
		// - validate ---
	});

</script>
<?php include_once('wrap.footer.php'); ?>