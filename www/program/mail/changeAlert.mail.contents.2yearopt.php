<?php
unset($mailling_app_content,$mailling_content);
$arrMailItem = array();

$__first = mb_substr($mem_info['in_name'],0,1,'utf8'); // 보안처리
$__last = mb_substr($mem_info['in_name'],2,mb_strlen($mem_info['in_name'], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($mem_info['in_name'],'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

// ----------------------- 수신동의 상태 변경에 따른 처리 ---------------------------
$mailling_app_content = "";
if( $arr_var['mode'] == "mail" ) { // 메일
	$mailling_app_content .= "
				<tr>
					<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>이메일</td>
					<td style='vertical-align:top;font-weight:600; padding:4px 0;'>".($arr_var['pass'] == "Y" ? "수신동의" : "수신거부")."</td>
				</tr>
	";
}
if( $arr_var['mode'] == "sms" ) { // SMS
	$mailling_app_content .= "
				<tr>
					<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>문자</td>
					<td style='vertical-align:top;font-weight:600; padding:4px 0; '>".($arr_var['pass'] == "Y" ? "수신동의" : "수신거부")."</td>
				</tr>
	";
}
// ----------------------- 수신동의 상태 변경에 따른 처리 끝 ---------------------------

$mailling_content = "
		<!-- ● Common Box -->
		<div style='padding:30px;'>
		<table style='width:100%;border-spacing:0; font-size:12px; font-family:\"돋움\",Dotum; line-height:17px;'>
			<tbody>
				<tr>
					<td style='text-align:center; border-bottom:1px solid #ddd; padding:3px 0 36px;'>
						<!-- 메일링 타이틀 이미지 -->
						<!-- 타이틀 이미지 없을때 기본 타이틀 이미지 default_txt.jpg 사용 -->
						<div class='' style='max-width:100%; display:inline-block'><img src='".$app_HTTP_URL."//images/mailing/default_txt.jpg' alt='알려드립니다.' style='width:100%'/></div>
					</td>
				</tr>
				<tr>
					<td style='line-height:19px; word-wrap:break-word; word-break:keep-all; font-size:15px; letter-spacing:-1px; color:#666; padding-top:50px;'>
						<!-- 이름 다음에는 br 두개 들어감 -->
						<strong style='color:#333;font-weight:600'>".$arrMailItem['name']."님</strong><br/> <br/>
						매 2년마다 이루어지는 재수신동의로 발송되는 이메일을 통해 광고성 정보 수신동의 상태가 변경되었음을 알려드립니다.<br/>
						변경된 수신정보상태는 아래와 같습니다.
					</td>
				</tr>
				<tr>
					<td>
						<!-- 광고성 정보 수신동의 상태 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:47px;'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #555; color:#333; font-size:15px; font-weight:600; padding-bottom:8px'>광고성 정보 수신동의 상태</th>
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
												".$mailling_app_content."
												<tr>
													<td style='vertical-align:top; padding:4px 0;'>설정변경일</td>
													<td style='vertical-align:top; font-weight:600; padding:4px 0;line-height:20px; letter-spacing:0px;'>".date("Y.m.d",strtotime($mem_info['m_opt_date']))."</td>
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