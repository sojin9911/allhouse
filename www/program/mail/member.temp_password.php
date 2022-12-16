<?php

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
						<div class='' style='max-width:100%; display:inline-block'><img src='".$app_HTTP_URL."/images/mailing/password.jpg' alt='임시 비밀번호를 안내해 드립니다.' style='width:100%'/></div>
					</td>
				</tr>
				<tr>
					<td style='padding:4px 0; line-height:19px; word-wrap:break-word; word-break:keep-all; font-size:15px; letter-spacing:-1px; color:#666; padding-top:47px;'>
						고객님의 안전한 정보 관리를 위해 임시 비밀번호로 접속후 <!-- 마이페이지  나의정보수정 페이지로 링크 추가 --><a href='".$app_HTTP_URL."/?pn=mypage.modify.form' style='font-weight:600; color:#333; text-decoration:none;' target='_blank'>'마이페이지 &gt; 정보수정'</a>에서 <strong style='font-weight:600; color:#333;'>'비밀번호변경'</strong>을 해주세요.
					</td>
				<tr>
				<tr>
					<td>
						<!-- 임시비밀번호 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:40px;'>
							<thead>
								<tr>
									<!-- 타이틀 -->
									<th colspan='3' style='text-align:left; color:#333; font-size:15px; font-weight:600; padding-bottom:15px'>임시 비밀번호</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<!-- 비밀번호 -->
									<td style='padding:20px 30px; border:1px solid #ddd; background:#f5f5f5; color:#333; font-weight:600; letter-spacing:0'>
										${tmp_pw}
									</td>
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