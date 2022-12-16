<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


$ordernum = $ordernum ? $ordernum : $_ordernum;
$vrow = _MQ(" select * from smart_order where o_ordernum = '".$ordernum."' ");

// 가상계좌 결제일 경우 메일 및 SMS 발송
if( $vrow['o_paymethod'] == 'virtual' ) {
	$_oemail = $vrow['o_oemail'];
	$_rhp = $vrow['o_rhp']; $_rtel = $vrow['o_rtel'];
	$_ordernum = $vrow['o_ordernum'];
	if( mailCheck($_oemail) ){
		// $_ordernum ==> 주문번호
		$_type = "virtual"; // 결제확인처리
		include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
		$_title = "[".$siteInfo[s_adshop]."] 가상계좌 결제를 하셨습니다. 기한내에 입금해주시면 주문이 완료됩니다!";
		$_content = $mailing_app_content;
		$_content = get_mail_content($_content);
		mailer( $_oemail , $_title , $_content );
	}

	// 문자 발송
	$sms_to = $_rhp ? $_rhp : $_rtel;
	shop_send_sms($sms_to, 'order_virtual', $_ordernum);
}



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행