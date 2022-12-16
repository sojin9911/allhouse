<?php
if($_REQUEST['_view'] == 'send') {
	$app_current_name = '개별/전체 메일 발송';
	$app_current_link = '_mailing_premium.view.php?_view=send';
}
else if( $_REQUEST['_view'] == 'log') {
	$app_current_name = '메일 발송 내역 보기';
	$app_current_link = '_mailing_premium.view.php?_view=log';
}
else {
	$app_current_name = '프리미엄 메일 발송';
	$app_current_link = '_mailing_premium.view.php';
}
include_once('wrap.header.php');

// {{{2018-09-27::메일링보완패치}}} - {
if(in_array($siteInfo['s_mailid'],array('test','tester')) == true){
	_MQ_noreturn( " update smart_setup set s_mailid = '', s_mailpw = '', s_mailuse = 'N' where s_uid = 1");
	// 사이트 정보 호출
	$siteInfo = get_site_info();
}
// {{{2018-09-27::메일링보완패치}}} - }

# AMail 로그인 정보
$amail_id = $siteInfo['s_mailid'];
$amail_pw = $siteInfo['s_mailpw'];
if(!$amail_id || !$amail_pw) error_loc_msg('_config.mail.form.php', '계정확인을 먼저 해 주셔야 합니다.');
$amail_id = 'new_'.$amail_id;

# URL 설정
$AMailMainUrl = 'http://partners.postman.co.kr:90/home/login_partner.jsp?cooperation_id=OD&user_id='.urlencode($amail_id).'&user_nm='.urlencode(iconv('utf-8', 'euc-kr', $siteInfo['s_adshop'])).'&user_no='.urlencode($siteInfo['s_glbtel']).'&user_email='.urlencode($siteInfo['s_ademail']).'&user_domain='.urlencode($_SERVER['HTTP_HOST']).'&user_tel='.urlencode($siteInfo[s_glbtel]).'&user_cell='.urlencode($siteInfo['s_glbmanagerhp']); // 기본 접속 경로
$AMailSendUrl = 'http://partners.postman.co.kr:90/send/send_email_view.jsp'; // 메일 발송 경로
$AMailLogUrl = 'http://partners.postman.co.kr:90/report/report_list.jsp'; // 발송결과 경로
?>
<div class="data_summery">
	<div class="tip_box">
		<?php echo _DescStr('프리미엄 메일링은 익스플로러에서만 동작합니다.'); ?>
		<?php echo _DescStr('익스플로러 10 을 사용중이신분은 호환성보기를 활성화 하시기 바랍니다.'); ?>
	</div>
</div>

<div class="in_iframe">
	<div class="inner">
		<iframe name="pass_postman" src="<?php echo $AMailMainUrl; ?>" width="900px" height="1000" frameborder="0" scrolling="auto"></iframe>
	</div>
</div>

<?php if($_view) { ?>
	<script type="text/javascript">
		setTimeout(function(){
			<?php if($_view == 'send') { ?>
				pass_postman.location.href = '<?php echo $AMailSendUrl; ?>';
			<?php } else if($_view == 'log') { ?>
				pass_postman.location.href = '<?php echo $AMailLogUrl; ?>';
			<?php } ?>
		}, 100);
	</script>
<?php } ?>
<?php include_once('wrap.footer.php'); ?>