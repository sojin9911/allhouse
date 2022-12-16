<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_point.list.php';
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$row = _MQ(" select *, indr.in_name from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) where pl_uid = '{$_uid}' ");
	// 지급상태
	$status_icon = $arr_adm_button['적립예정'];
	if($row['pl_status']=='Y') $status_icon = $arr_adm_button['적립완료'];
	else if($row['pl_status']=='C') $status_icon = $arr_adm_button['적립취소'];

	// 수정불가 - 적립완료, 적립취소, 삭제
	$trigger_mod = true;
	if($row['pl_status'] == 'Y' || $row['pl_status'] == 'C' || $row['pl_delete'] == 'Y') $trigger_mod = false;


}else{
	$_mode = 'add';
	$trigger_mod = true;
}

?>
<form name="frm" action="_point.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<?php if($_mode == 'modify'){ ?>
				<tr>
					<th>지급상태</th>
					<td>
						<?php echo $status_icon; ?>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th class="ess">제목</th>
					<td>
						<input type="text" name="_title" class="design" value="<?php echo $row['pl_title']; ?>" style="width:400px">
					</td>
				</tr>
				<tr>
					<th class="ess">지급적립금</th>
					<td>
						<?php if($trigger_mod){ ?>
							<input type="text" name="_point" class="design number_style" value="<?php echo number_format($row['pl_point']); ?>" style="width:85px">
						<?php }else{  ?>
							<?php echo number_format($row['pl_point']); ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th class="ess">지급예정일</th>
					<td>
						<?php if($trigger_mod){ ?>
							<input type="text" name="_appdate" class="design js_datepic" value="<?php echo $row['pl_appdate']; ?>" style="width:85px">
						<?php }else{  ?>
							<?php echo $row['pl_appdate']; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th class="ess">적용유저</th>
					<td>
						<?php if($_mode == 'add'){ ?>
							<a href="#none" onclick="window.open('_point.search.php?','','width=1100px,height=800px,left=100px,scrollbars=yes'); return false;" class="c_btn h27 blue icon icon_plus">회원검색</a>
							<div class="dash_line"><!-- 점선라인 --></div>
							<textarea name="_inid" rows="4" class="design"><?php echo $row['pl_inid']; ?></textarea>
							<div class="tip_box">
								<?php echo _DescStr('회원검색버튼을 눌러 적립금을 지급할 회원을 추가해 주시기 바랍니다. '); ?>
								<?php echo _DescStr('또는 회원아이디를 콤마(,)로 구분하여 직접 입력해 주시기 바랍니다. '); ?>
							</div>
						<?php }else{ ?>
							<?php echo showUserInfo($row['pl_inid'],$row['in_name']); ?>
						<?php } ?>
					</td>
				</tr>
				<?php if($_mode == 'modify'){ ?>
				<tr>
					<th>참고사항</th>
					<td>
						<div class="tip_box">
							<?php echo _DescStr('등록시간 : ' . $row['pl_rdate']); ?>
							<?php if($row['pl_status']=='Y'){ ?>
								<?php echo _DescStr('지급시간 : ' . $row['pl_adate']); ?>
								<?php echo _DescStr('회원적립금 : ' . number_format($row['pl_point_before']) . ($row['pl_point_apply'] < 0 ? ' - ' : ' + ') . number_format(abs($row['pl_point_apply'])) . ' = ' . number_format($row['pl_point_after'])); ?>
							<?php } ?>
						</div>
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
				<?php if($_mode == 'add'){ ?>
					_title : { required: true }
					,_point : { required: true }
					,_appdate : { required: true }
					,_inid : { required: true }
				<?php }else if($row['pl_status'] == 'N' && $row['pl_delete']=='N'){ ?>
					_title : { required: true }
					,_point : { required: true }
					,_appdate : { required: true }
				<?php }else{ ?>
					_title : { required: true }
				<?php } ?>
			},
			messages: {
				<?php if($_mode == 'add'){ ?>
					_title : { required: '제목을 입력해주시기 바랍니다.' }
					,_point : { required: '지급적립금를 입력해주시기 바랍니다.' }
					,_appdate : { required: '지급예정일을 입력해주시기 바랍니다.' }
					,_inid : { required: '적용유저를 입력해주시기 바랍니다.' }
				<?php }else if($row['pl_status'] == 'N' && $row['pl_delete']=='N'){ ?>
					_title : { required: '제목을 입력해주시기 바랍니다.' }
					,_point : { required: '지급적립금를 입력해주시기 바랍니다.' }
					,_appdate : { required: '지급예정일을 입력해주시기 바랍니다.' }
				<?php }else{ ?>
					_title : { required: '제목을 입력해주시기 바랍니다.' }
				<?php } ?>
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