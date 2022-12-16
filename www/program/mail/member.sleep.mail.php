<?php
unset($mailling_content,$mailling_app_content);
$arrMailItem = array();

$__first = mb_substr($r['in_name'],0,1,'utf8'); // 보안처리
$__last = mb_substr($r['in_name'],2,mb_strlen($r['in_name'], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($r['in_name'],'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

$mailling_content = "

		<!-- ● Common Box -->
		<div style='padding:30px;'>
		<table style='width:100%;border-spacing:0; font-size:12px; font-family:\"돋움\",Dotum; line-height:17px;'>
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
						<strong style='color:#333;font-weight:600'>".$arrMailItem['name']."님</strong><br/> <br/>
						<!-- 쇼핑몰이름 -->
						<strong style='color:#333;font-weight:600'>".$siteInfo['s_adshop']."</strong> 의 휴면상태계정을 해지하기 위한 인증을 요청해주셨습니다.<br/>
						인증완료를 위해 아래 인증버튼을 클릭하시기 바랍니다. <br/>
						클릭 후 정상적으로 인증을 마치신 후 로그인하시면 정상적으로 서비스를 이용하실 수 있습니다.
					</td>
				</tr>
				<tr>
					<td>
						<!-- 휴면해지 인증버튼 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin:30px 0 0px; border-bottom:1px solid #ddd'>
							<tbody>
								<tr>
									<td style='text-align:center; padding-bottom:30px'><a href='".$_AUTH_URL."' style='border:1px solid #cd3726; border-radius:5px; font-size:15px; font-weight:600; color:#cd3726; padding:20px 28px;text-decoration:none; display:inline-block' target='_blank'>휴면계정 해지를 위한 인증 버튼</a></td>
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