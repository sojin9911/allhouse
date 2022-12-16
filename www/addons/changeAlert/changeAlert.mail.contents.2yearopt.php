<?php /* program/mail 로이동 */

	// 파일명 : changeAlert.mail.contents.2yearopt.php
	// 매 2년 수신동의 재설정 시 광고성 정보 수신동의 상태 - 정보 추가
	// $arr_var 정보가 있어야 함.
	//	arr_var = array( id => 아이디 , mode => (mail , sms) , pass => (Y , N) )

	include_once("inc.php");
	$mailing_url = "http://".$_SERVER[HTTP_HOST];

	$id = $arr_var['id'] ;

	if( $id ) {

		// 회원정보 추출
		$row_member = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");
		$email = $row_member['in_email'];

		// - 메일발송 ---
		if( mailCheck($email) ){

			$mail_string = '';
			// 메일링 수신여부 체크
			if( $arr_var['mode'] == "mail" ) {
				$mail_string .= '
						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							EMAIL : ' . ($arr_var['pass'] == "Y" ? "수신동의" : "수신거부") . '
						</dd>
				';
			}
			// 문자 수신여부 체크
			if( $arr_var['mode'] == "sms" ) {
				$mail_string .= '
						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							SMS : ' . ($arr_var['pass'] == "Y" ? "수신동의" : "수신거부") . '
						</dd>
				';
			}

			$mailling_content = '
			<div style="margin:40px 50px 50px 50px;">
				<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">' . $row_member['in_name'] .'님!</strong><br />
				매 2년마다 이루어지는 재수신동의로 발송되는 이메일을 통해 광고성 정보 수신동의 상태가 변경되었음을 알려드립니다.<br />
				변경된 수신정보상태는 아래와 같습니다.
			</div>
				<div style="margin:40px 50px 50px 50px;">
					<dl style="margin-top:30px">
						<!-- 내용작은 타이틀 -->
						<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/pages/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">광고성 정보 수신동의 상태</dt>

						' . $mail_string . '

						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							설정변경일 : '. substr($row_member['m_opt_date'] , 0 , 10).'
						</dd>
					</dl>
				</div>
			';

			$_title = "[".$siteInfo['s_adshop']."] 수신동의 상태가 변경되었습니다.";
			$_content = get_mail_content($mailling_content);
			mailer( $email , $_title , $_content );

		}
		// - 메일발송 ---


	}

?>