<?php
unset($mailling_content,$mailling_app_content);
$arrMailItem = array();
$__first = mb_substr($_name,0,1,'utf8'); // 보안처리
$__last = mb_substr($_name,2,mb_strlen($_name, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($_name,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
$arrMailItem['id'] = LastCut($_id,2);

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
						<strong style='color:#333;font-weight:600'>".$siteInfo['s_adshop']."</strong> 의 온라인 계정이 휴면상태로 변경되었습니다.<br/>
						계정 정보를 확인하시고, 웹사이트를 방문하셔서 로그인하시면 휴면상태를 해지할 수 있습니다.
					</td>
				</tr>
				<tr>
					<td>
						<!-- 휴면 전환된 계정정보 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:47px;'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #555; color:#333; font-size:15px; font-weight:600; padding-bottom:8px'>휴면 전환된 계정정보</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding:15px 0; border-bottom:1px solid #ddd;'>
										<table style='width:100%;border-spacing:0; '>
											<colgroup>
												<col width='100'/><col width='*'/>
											</colgroup>
											<tbody>
												<tr>
													<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>이름</td>
													<td style='vertical-align:top;font-weight:600; padding:4px 0;'>".$arrMailItem['name']."</td>
												</tr>
												<tr>
													<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>아이디 </td>
													<td style='vertical-align:top;font-weight:600; padding:4px 0; '>".$arrMailItem['id']."</td>
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