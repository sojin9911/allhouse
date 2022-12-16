<?php # 이메일 수신동의, 수신거부에 대한 문구 설정
	$row_setup = $siteInfo;
	# 수신동의 기본 문구 :: 한글
	$default_set= "본메일은 발신 전용 메일입니다. 메일수신을 원치 않으시면 [__deny__]를 눌러주십시오.\nif you do not want this of email_information, please click the [__deny__]";

	// -- 수신동의 기본 문구가 없을 경우 처리
	if( $siteInfo['s_set_email_txt'] == ''){
		_MQ_noreturn("update smart_setup set s_set_email_txt = '".addslashes($default_set)."' where s_uid = '1'  ");
		 $siteInfo['s_set_email_txt'] = stripslashes($default_set);
	}

?>
<form name='frmEmailConfig' id="frmEmailConfig" method='post' action="/addons/emailCnf/_email_config.pro.php">
	<input type='hidden' name='_mode' value='setup'>
	<input type="hidden" name="default_set" id="default_set" value="<?php echo $default_set;  ?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">수신거부 문구설정</th>
					<td>
						<textarea name="_set_email_txt" id="_set_email_txt" class="design" style="width:100%;height:100px;"><?php echo stripslashes($siteInfo['s_set_email_txt']); ?></textarea>
						<div class="tip_box">
						<?=_DescStr("광고성 관련 이메일을 보낼 시 수신동의를 한 회원에게만 발송이되며, 수신동의, 거부와 관련된 문구를 반드시 명시하셔야 하며, 수신거부 기능 또한 반드시 추가하셔 합니다.")?>
						<?=_DescStr("수신동의 문구는 반드시 텍스트와 치환자로만 작성해 주셔야합니다.")?>
						<?=_DescStr("수신동의/거부에 대한 문구를 기본설정으로 되돌릴 시 <a style='color:red; cursor:pointer; font-weight:bold;' onclick='set_default()'>이곳</a> 을 클릭해 주세요.")?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="ess">치환자 안내</th>
					<td>
						<div class="tip_box">
						<?=_DescStr("<strong>[__deny__]</strong> 수신거부 에대한 기능 링크를 자동으로 생성해 줍니다.")?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<?php  echo _submitBTNsub(); ?>
</form>

<script>
	// -- 수신동의 문구 초기화
	function set_default()
	{
		var default_set = $('#default_set').val();
		$('#_set_email_txt').val(default_set);
	}
</script>
