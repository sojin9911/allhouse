<?php
	include_once('wrap.header.php');

    // {{{2018-09-27::메일링보완패치}}} - {
    if(in_array($siteInfo['s_mailid'],array('test','tester')) == true){
        _MQ_noreturn( " update smart_setup set s_mailid = '', s_mailpw = '', s_mailuse = 'N' where s_uid = 1");
		$siteInfo = get_site_info();// 사이트 정보 재호출
    }
    // {{{2018-09-27::메일링보완패치}}} - }
?>
<form action="_config.mail.pro.php" method="post" class="defaut_form" autocomplete="off" target="common_frame" onsubmit="return mail_submit();">
	<input type="hidden" name="_mail_checking" class="js_mail_checking" value="0">
	<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<thead>
				<col width="180">
				<col width="*">
			</thead>
			<tbody>
				<tr>
					<th class="ess">사용자 정보</th>
					<td>
						<span class="fr_tx">아이디</span>
						<input type="text" name="_mailid" class="design js_mailid" value="<?php echo $siteInfo['s_mailid']; ?>" style="width:185px">
						<span class="fr_tx">비밀번호</span>
						<input type="password" name="_mailpw" class="design js_mailpw" value="<?php echo $siteInfo['s_mailpw']; ?>" style="width:185px" autocomplete="new-password">
						<a href="#none" class="c_btn h27 blue" target="_blank" onclick="idchk_onedaynet(); return false;">원데이넷 아이디 확인</a>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('원데이넷(<a href="http://www.onedaynet.co.kr" target="_blank"><u>www.onedaynet.co.kr</u></a>)에 가입한 정보를 입력해주시기 바랍니다.'); ?>
					</td>
				</tr>
				<tr>
					<th class="ess">사용여부</th>
					<td>
						<?php echo _InputRadio('_mailuse', array('Y', 'N'), ($siteInfo['s_mailuse']?$siteInfo['s_mailuse']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>

<script type="text/javascript">
	function mail_submit(frm) {
		if(!$('.js_mail_checking').val()) {
			alert('원데이넷 아이디 확인 버튼을 눌러 인증해주시기 바랍니다.');
			return false;
		}
	}
	function id_check_ok() { $('.js_mail_checking').val('1'); }
	function id_check_fail() { $('.js_mail_checking').val('0'); }
	function id_change() { alert('아이디 변경을 원하시면 아이디 확인 버튼을 눌러 인증하시기 바랍니다.'); id_check_fail(); }
	function idchk_onedaynet() {
		var ond_id = $('.js_mailid').val();
		var ond_pw = $('.js_mailpw').val();
		if(!ond_id || !ond_pw) {
			alert('원데이넷 아이디와 비밀번호를 입력하세요.');
			id_check_fail();
			return false;
		}
		$.ajax({
			data: {
				_mode: 'onedaynet_check',
				_id: ond_id,
				_pw: ond_pw
			},
			type: 'POST',
			cache: false,
			url: '_config.mail.pro.php',
			success: function(data) {
				if(data == 'no') {
					alert('존재하지 않는 회원으로 적용이 불가합니다.');
					id_check_fail();
				}
				else if(data == 'yes') {
					alert('존재하는 회원으로 적용이 가능합니다.');
					id_check_ok();
				}
				else {
					alert(data);
				}
			}
		});
	}
</script>
<?php include_once('wrap.footer.php'); ?>