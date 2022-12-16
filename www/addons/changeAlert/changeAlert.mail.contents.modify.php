<?php /* program/mail/ 로 옮겨감 */

	// 파일명 : changeAlert.mail.contents.modify.php
	// 정보수정 시 광고성 정보 수신동의 상태 - 정보 추가
	// $id 정보가 있어야 함.
	// $_mailling / $_sms 정보 있어야 함.
	// 정보수정이 이루어지기 전이라 설정변경은 m_opt_date를 쓰지 않고 date("Y-m-d") 적용

	include_once("inc.php");
	$mailing_url = "http://".$_SERVER[HTTP_HOST];
	$row_setup = $siteInfo;

	if( $id ) {

		// 회원정보 추출
		$row_member = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");
		$email = $row_member['in_email'];


		// - 메일발송 ---
		if( mailCheck($email) && ( $row_member['in_emailsend'] <> $_mailling || $row_member['in_smssend'] <> $_sms ) && ( $_mailling || $_sms ) ){

			$mail_string = '';
			// 메일링 수신여부 체크
			if($row_member['in_emailsend'] <> $_mailling && $_mailling ) {
				$mail_string .= '
						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							EMAIL : ' . ($_mailling == "Y" ? "수신동의" : "수신거부") . '
						</dd>
				';
			}
			// 문자 수신여부 체크
			if($row_member['in_smssend'] <> $_sms && $_sms ) {
				$mail_string .= '
						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							SMS : ' . ($_sms == "Y" ? "수신동의" : "수신거부") . '
						</dd>
				';
			}

			$mailling_content = '
			<div style="margin:40px 50px 50px 50px;">
				<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">' . $row_member['in_name'] .'님!</strong><br />
				정보수정을 통하여 광고성 정보 수신동의 상태가 변경되었음을 알려드립니다.<br />
				변경된 수신정보상태는 아래와 같습니다.
			</div>


				<div style="margin:40px 50px 50px 50px;">
					<dl style="margin-top:30px">
						<!-- 내용작은 타이틀 -->
						<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/pages/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">광고성 정보 수신동의 상태</dt>

						' . $mail_string . '

						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							설정변경일 : '. date("Y-m-d") .'
						</dd>
						'.$_deny_msg.'
					</dl>
				</div>
			';

			$_title = "[".$row_setup[s_adshop]."] 정보수정으로 수신동의 상태가 변경되었습니다.";
			$_content = get_mail_content($mailling_content);
			mailer( $email , $_title , $_content );

		}
		// - 메일발송 ---


	}

?>