<?php /* member.jon.mail.php로 옮겨짐 */


	// 파일명 : changeAlert.mail.contents.join.php
	// 회원가입 시 광고성 정보 수신동의 상태 - 정보 추가
	// $id 정보가 있어야 함.

	include_once("inc.php");
	$mailing_url = "http://".$_SERVER[HTTP_HOST];

	if( $id ) {

		// 회원정보 추출
		$row_member = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");

		$mailling_content .= '
			<div style="margin:40px 50px 50px 50px;">
				<dl style="margin-top:30px">
					<!-- 내용작은 타이틀 -->
					<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/pages/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">광고성 정보 수신동의 상태</dt>
					<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
						SMS : ' . ($row_member['in_smssend'] == "Y" ? "수신동의" : "수신거부") . '
					</dd>
					<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
						EMAIL : ' . ($row_member['in_emailsend'] == "Y" ? "수신동의" : "수신거부") . '
					</dd>
					<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
						설정일 : '.substr($row_member['m_opt_date'] , 0 , 10).'
					</dd>
				</dl>
			</div>
		';

	}

?>