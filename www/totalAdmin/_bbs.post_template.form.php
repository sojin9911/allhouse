<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
if($_REQUEST['_mode'] == 'modify') {
	$app_current_name = '게시글 양식 수정';
}else{
	$app_current_name = '게시글 양식 등록';
}
$app_current_link = '_bbs.post_template.list.php';
include_once('wrap.header.php');

// 변수 설정
if($_mode == 'modify'){
	$r = _MQ("select *from smart_bbs_template where bt_uid = '".$_uid."' ");
	if( count($r) < 1 ){ error_msg("게시글 양식이 존재하지 않습니다."); }
}else{
	$_mode = 'add';
}
?>
<form name="frmBbsPostTemplate" id="frmBbsPostTemplate" action="_bbs.post_template.pro.php" method="post"  autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">

	<!-- ●단락타이틀 -->
	<div class="group_title"><strong><?php echo $app_current_name;?></strong><!-- 메뉴얼로 링크 --> </div>

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">분류</th>
					<td>
						<?php echo _InputSelect( '_type' , array('shop','admin') , $r['bt_type']  , '' , array('쇼핑몰 게시글 양식','관리자 양식') , '');?>
					</td>
				</tr>

				<tr>
					<th class="ess">제목</th>
					<td>
						<input type="text" name="_title" class="design bold t_black" placeholder="제목" value="<?php echo $r['bt_title'] ?>" style="width:500px;">
						<?=_DescStr("제목의 경우 게시글에 적용되지 않으며 관리 목적으로 사용됩니다.");?>
					</td>
				</tr>

				<tr>
					<th class="ess">내용</th>
					<td>
						<textarea name="_content" class="input_text SEditor" style="width:100%;height:300px; display: none;"><?php echo stripslashes($r['bt_content']); ?></textarea>
					</td>
				</tr>
				</tr>

			</tbody>
		</table>
	</div>

	<?php echo _submitBTN('_bbs.post_template.list.php'); ?>
</form>

<script type="text/javascript">

	$(document).ready(function() {

		// -  validate ---
		$('form[name=frmBbsPostTemplate]').validate({
			ignore: '.ignore',
			rules: {
				_type : {required : true  }
				, _title : {required : true  }
				, _content: { required: true }
			},
			messages: {
				_type : {required : '분류를 선택해 주세요.'  }
				, _title : {required : '제목을 입력해 주세요.'  }
				, _content: { required: '내용을 입력해 주세요.' }
			},
			submitHandler : function(form) {
				form.submit();
			}
		});
		// - validate ---
	});



</script>
<?php
		include_once('wrap.footer.php');
?>