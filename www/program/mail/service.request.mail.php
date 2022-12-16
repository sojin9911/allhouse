<?php
$__first = mb_substr($_comname,0,1,'utf8'); // 보안처리
$__last = mb_substr($_comname,2,mb_strlen($_comname, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['comname'] = $__first."*".(mb_strlen($_comname,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
$arrMailItem['content'] = nl2br($_content); // 내용
$arrMailItem['admcontent'] = nl2br($_admcontent); // 답변내용

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
						<div class='' style='max-width:100%; display:inline-block'><img src='".$app_HTTP_URL."/images/mailing/ad_answer.jpg' alt='제휴문의에 관해 답변드립니다.' style='width:100%'/></div>
					</td>
				</tr>
				<tr>
					<td style=' line-height:19px; word-wrap:break-word; word-break:keep-all; font-size:15px; letter-spacing:-1px; color:#666; padding-top:50px;'>
						<!-- 이름 다음에는 br 두개 들어감 -->
						<strong style='color:#333;font-weight:600'>".$arrMailItem['comname']."님</strong><br/><br/>
						<!-- 쇼핑몰이름 -->
						<strong style='color:#333;font-weight:600'>".$siteInfo[s_adshop]."</strong> 에 문의해주셔서 감사합니다.<br/>
						요청해주신 문의에 대한 관리자의 답변내용을 다음과 같이 전달해드립니다.
					</td>
				</tr>
				<tr>
					<td>
						<!-- 문의내용 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:40px;'>
							<thead>
								<tr>
									<!-- 타이틀 -->
									<th colspan='3' style='text-align:left; color:#333; font-size:15px; font-weight:600; padding-bottom:15px'>문의내용</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<!-- 문의내용 -->
									<td style='padding:20px 30px; border:1px solid #ddd; background:#f5f5f5; color:#666; font-weight:400; letter-spacing:0'>".$arrMailItem['content']."</td>
								<tr>
							</tbody>
						</table>

						<!-- 답변내용 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:40px;'>
							<thead>
								<tr>
									<!-- 타이틀 -->
									<th colspan='3' style='text-align:left; color:#333; font-size:15px; font-weight:600; padding-bottom:15px'>답변내용</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<!-- 답변내용 -->
									<td style='padding:20px 30px; border:1px solid #ddd; background:#f5f5f5; color:#666; font-weight:400; letter-spacing:0'>".$arrMailItem['admcontent']."</td>
								<tr>
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