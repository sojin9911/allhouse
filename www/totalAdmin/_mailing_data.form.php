<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_mailing_data.list.php';
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$r = _MQ(" select * from smart_mailing_data where md_uid = '{$_uid}' ");
}else{
	$_mode = 'add';
}

?>
<form name="frm" action="_mailing_data.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
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
					<th>메일링 제목</th>
					<td>
						<input type="text" name="_title" class="design" value="<?php echo $r['md_title']; ?>" style="width:400px">
						<?php echo _DescStr('광고성, 이벤트성 메일을 발송시 메일제목 앞에 "<strong>(광고)</strong>" 문구를 반드시 붙이셔야합니다. ', 'black'); ?>
					</td>
				</tr>
				<tr>
					<th>메일링 타입</th>
					<td>
						<?php echo _InputRadio('_adchk', array('Y', 'N'), ($r['md_adchk']?$r['md_adchk']:'Y'), '', array('광고성,이벤트성', '일반'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>메일링 내용</th>
					<td>
						<textarea name="_content" class="input_text SEditor" style="width:100%;height:400px;" hname='상품설명'><?php echo stripslashes($r['md_content']); ?></textarea>
					</td>
				</tr>
				<?php if($_mode=='modify'){ ?>
				<tr>
					<th>참고사항</th>
					<td>
						등록시간 : <?php echo date('Y.m.d H:i:s', strtotime($r['md_rdate'])); ?>
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
				_title: { required: true}	//메일링 제목
				,_content: { required: true}	//메일링 내용
			},
			messages: {
				_title: { required: '메일링 제목을 입력해주시기 바랍니다.'}	//메일링 제목
				,_content: { required: '메일링 내용을 입력해주시기 바랍니다.'}	//메일링 내용
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