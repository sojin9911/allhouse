<?php

unset($mailling_content);
$arrMailItem = array();
$__first = mb_substr($_name,0,1,'utf8'); // 보안처리
$__last = mb_substr($_name,2,mb_strlen($_name, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($_name,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
$arrMailItem['content'] = str_replace("{회원아이디}" , $_id , str_replace("{회원명}" , $_name , stripslashes($siteInfo['s_2year_opt_content_top'])));

$arrMailItem['_URL'] = $app_HTTP_URL . "/addons/2yearOpt/2yearOpt.php";

$arrMailItem['_URL_mail_Y'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|mail§pass|Y" ) : enc( 'e' , "id|" . $_id . "§mode|mail§pass|Y" )) ; // 메일수신동의
$arrMailItem['_URL_mail_N'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|mail§pass|N" ) : enc( 'e' , "id|" . $_id . "§mode|mail§pass|N" )) ; // 메일수신거부

$arrMailItem['_URL_sms_Y'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|sms§pass|Y" ) : enc( 'e' , "id|" . $_id . "§mode|sms§pass|Y" )) ; // 문자수신동의
$arrMailItem['_URL_sms_N'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|sms§pass|N" ) : enc( 'e' , "id|" . $_id . "§mode|sms§pass|N" )) ; // 문자수신거부

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

$mailling_content = "

		<!-- ● Common Box -->
		<div style='padding:30px;'>
		<table style='width:100%;border-spacing:0; font-size:12px; font-family:\"돋움\",Dotum; line-height:17px; '>
			<tbody>
				<tr>
					<td style='text-align:center; border-bottom:1px solid #ddd; padding:3px 0 36px;'>
						<!-- 메일링 타이틀 이미지 -->
						<!-- 타이틀 이미지 없을때 기본 타이틀 이미지 default_txt.jpg 사용 -->
						<div class='' style='max-width:100%; display:inline-block'><img src='".$app_HTTP_URL."/images/mailing/default_txt.jpg' alt='알려드립니다.' style='width:100%'/></div>
					</td>
				</tr>
				<tr>
					<td style='line-height:19px; word-wrap:break-word; word-break:keep-all; font-size:15px; letter-spacing:-1px; color:#666; padding-top:50px;'>
						<!-- 이름 다음에는 br 두개 들어감 -->
						<strong style='color:#333;font-weight:600'>".$arrMailItem['name']."님</strong><br/><br/>
						<!-- 상단내용 / 관리자에서 입력하는 부분 / 없으면 div 숨김 -->
						<div class='editor' style='padding-bottom:20px'>".$arrMailItem['content']."</div>
						<!-- 수신동의여부 체크에 관한 문구 -->
						본 메일의 수신동의여부 체크 버튼을 통해 수신여부 설정이 가능합니다. <br/>
						수신동의여부 설정은 한번만 가능하며 이후에는 <!-- 마이페이지  나의 정보수정 링크 --><a href='".$app_HTTP_URL."/?pn=mypage.modify.form' target = '_blank' style='color:#333; font-weight:600; text-decoration:none'>'마이페이지 > 나의 정보수정'</a>에서 설정 가능합니다.
					</td>
				</tr>

				<tr>
					<td>
						<!-- 수신동의여부 체크 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:47px;'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #555; color:#333; font-size:15px; font-weight:600; padding-bottom:8px'>수신동의여부 체크</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding:15px 0; border-bottom:1px solid #ddd;'>
										<table style='width:100%;border-spacing:0; '>
											<colgroup>
												<col width='110'/><col width='*'/>
											</colgroup>
											<tbody>
												<tr>
													<td style='vertical-align:middle; padding:4px 0; letter-spacing:0px;'>이메일수신여부</td>
													<td style='vertical-align:middle; padding:4px 0;'><a href='".$arrMailItem['_URL_mail_Y']."' style='color:#cd3726;text-decoration:none;border:1px solid #cd3726; padding:3px 8px 2px; font-weight:400; display:inline-block; margin:0 2px' target = '_blank' >수신동의 클릭</a> <a href='".$arrMailItem['_URL_mail_N']."' style='color:#999;text-decoration:none;border:1px solid #999; padding:3px 8px 2px; font-weight:400; display:inline-block; margin:0 2px' target = '_blank'>수신거부 클릭</a></td>
												</tr>
												<tr>
													<td style='vertical-align:middle; padding:4px 0; letter-spacing:0px;'>문자수신여부</td>
													<td style='vertical-align:middle; padding:4px 0;'><a href='".$arrMailItem['_URL_sms_Y']."' style='color:#cd3726;text-decoration:none;border:1px solid #cd3726; padding:3px 8px 2px; font-weight:400; display:inline-block; margin:0 2px' target = '_blank'>수신동의 클릭</a> <a href='".$arrMailItem['_URL_sms_N']."' style='color:#999;text-decoration:none;border:1px solid #999; padding:3px 8px 2px; font-weight:400; display:inline-block; margin:0 2px' target = '_blank'>수신거부 클릭</a></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>

			</tbody>
		</table>
		</div>
		<!-- / Common Box -->
";

?>