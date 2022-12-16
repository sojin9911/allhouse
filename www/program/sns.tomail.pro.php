<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 상품정보 추출
$row_product = get_product_info($_POST[pcode]);

if( !preg_match("/sns.tomail.form.php/i" , $_SERVER["HTTP_REFERER"]) ){
	error_msg("잘못된 접근입니다.");
}

if( sizeof($row_product) == 0 ){
	error_msg("잘못된 접근입니다.");
}



$que = "insert into smart_sns_log set
				sl_pcode			=	'".$_POST[pcode]."',
				sl_type				=	'".$_POST[type]."',
				sl_ip					=	'".$_SERVER[REMOTE_ADDR]."',
				sl_rdate			=	now()";

$res = mysql_query($que);


// - 메일발송 ---
if( mailCheck($_POST[toMail]) ){
	include_once(OD_MAIL_ROOT."/sns.tomail.mail.php"); // 메일 내용 불러오기 ($mailing_content)
	$_title = stripslashes($_POST[toName]." 님!! " .$_POST[fromName]."님께서 보내신 추천메일입니다.");
	$_content = get_mail_content($mailling_content);
	mailer( $_POST[toMail] , $_title , $_content );
}
// - 메일발송 ---



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
error_msgPopup_s('친구에게 추천메일를 발송하였습니다.');