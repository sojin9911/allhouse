<?php /* program/mail/ 로 옮겨짐 */
	
	/*
		# 매 2년마다 수신동의 설정 mail content
		# 넘겨질 정보
			_id : 회원아이디
			_name : 회원명
	*/
	$mailing_url = "http://".$_SERVER[HTTP_HOST];

	
	$_id = $_id ? $_id : "admin";
	$_name = $_name ? $_name : "운영자";


	// --- 타이틀 ---
	$_title = "[".$siteInfo['s_adshop']."]" . stripslashes($siteInfo['s_2year_opt_title']);
	// --- 타이틀 ---


	// --- 상단 본문 ---
	$mailing_title = str_replace("{회원아이디}" , $_id , str_replace("{회원명}" , $_name , stripslashes($siteInfo['s_2year_opt_content_top'])));
	// --- 상단 본문 ---


	// --- 중단 본문 ---
	// 암호화  ==> onedaynet_encode
	// 복호화  ==> onedaynet_decode
	$_URL = $mailing_url . "/addons/2yearOpt/2yearOpt.php";	

	$_URL_mail_Y = $_URL . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|mail§pass|Y" ) : enc( 'e' , "id|" . $_id . "§mode|mail§pass|Y" )) ; // 메일수신동의
	$_URL_mail_N = $_URL . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|mail§pass|N" ) : enc( 'e' , "id|" . $_id . "§mode|mail§pass|N" )) ; // 메일수신거부

	$_URL_sms_Y = $_URL . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|sms§pass|Y" ) : enc( 'e' , "id|" . $_id . "§mode|sms§pass|Y" )) ; // 문자수신동의
	$_URL_sms_N = $_URL . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|sms§pass|N" ) : enc( 'e' , "id|" . $_id . "§mode|sms§pass|N" )) ; // 문자수신거부





	$mailling_content = '
		<!-- 메일링제목 -->
		<div style="background:#eee; height:110px; text-align:center; margin:20px 0; border:solid 1px #ddd;">
			<span style="display:inline-block; font-family:\'나눔고딕\',\'돋움\'; font-size:27px; font-weight:600; color:#333; letter-spacing:-1px; line-height:110px;  padding-left:70px">
				'.$mailing_title.'
			</span>
		</div>
		<!--// 메일링제목 -->


		<div style="margin:40px 50px 50px 50px;">
			<dl style="margin-top:30px">
				<!-- 내용작은 타이틀 -->
				<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/pages/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">수신동의여부 체크</dt>
				<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
					메일수신여부 : &nbsp;
						<a href="'.$_URL_mail_Y.'" target="_blank" style="color:blue;">[수신동의 클릭]</a>
						,
						<a href="'.$_URL_mail_N.'" target="_blank" style="color:red;">[수신거부 클릭]</a>
				</dd>
				<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
					문자수신여부 : &nbsp;
						<a href="'.$_URL_sms_Y.'" target="_blank" style="color:blue;">[수신동의 클릭]</a>
						,
						<a href="'.$_URL_sms_N.'" target="_blank" style="color:red;">[수신거부 클릭]</a>
				</dd>
				<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:400; color:#333; margin:0; border:1px solid #ddd; border-top:0">
					( 이메일을 통한 수신동의/거부는 링크 클릭을 통해 가능하시며, 최초 클릭한 당일만 적용이 가능하십니다. )
				</dd>
			</dl>
		</div>
	';
	// --- 중단 본문 ---


	// --- 매 2년마다 수신동의 설정 내용 ---


?>