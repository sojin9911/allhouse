<?php
unset($mailling_content,$mailling_app_content);
$arrMailItem = array();
$__first = mb_substr($join_name,0,1,'utf8'); // 보안처리
$__last = mb_substr($join_name,2,mb_strlen($join_name, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['joinName'] = $__first."*".(mb_strlen($join_name,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

if( !is_array($mem_info) || count($mem_info) < 1){
	$mem_info = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");
}

// ------------------------ 광고성 이메일/SMS 수신동의 상태 표기 --------------------------
$mailling_app_content = "
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
									<tr>
										<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>이메일</td>
										<td style='vertical-align:top;font-weight:600; padding:4px 0;'>".($mem_info['in_emailsend'] == "Y" ? "수신동의" : "수신거부")."</td>
									</tr>
									<tr>
										<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>문자</td>
										<td style='vertical-align:top;font-weight:600; padding:4px 0; '>".($mem_info['in_smssend'] == "Y" ? "수신동의" : "수신거부")."</td>
									</tr>
									<tr>
										<td style='vertical-align:top; padding:4px 0;'>설정변경일</td>
										<td style='vertical-align:top; font-weight:600; padding:4px 0;line-height:20px; letter-spacing:0px;'>".date("Y.m.d",strtotime( rm_str($mem_info['m_opt_date']) > 0 ? $mem_info['m_opt_date'] : date('Y-m-d') ))."</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
";
// ------------------------ 광고성 이메일/SMS 수신동의 상태 표기 끝 --------------------------

$mailling_content = "
		<!-- ● Common Box -->
		<div style='padding:30px;'>
		<table style='width:100%;border-spacing:0; font-size:12px; font-family:\"돋움\",Dotum; line-height:17px;'>
			<tbody>
				<tr>
					<td style='text-align:center; border-bottom:1px solid #ddd; padding:3px 0 36px;'>
						<!-- 메일링 타이틀 이미지 -->
						<!-- 타이틀 이미지 없을때 기본 타이틀 이미지 default_txt.jpg 사용 -->
						<div class='' style='max-width:100%; display:inline-block'><img src='".$app_HTTP_URL."/images/mailing/join.jpg' alt='회원가입을 진심으로 환영합니다.' style='width:100%'/></div>
					</td>
				</tr>
				<tr>
					<td style='font-size:15px; letter-spacing:-1px; color:#666; padding-top:45px; line-height:19px;'>
						<!-- 이름 다음에는 br 두개 들어감 -->
						<strong style='font-weight:600; color:#333;'><!-- 이름 -->".$arrMailItem['joinName']."님 안녕하세요.</strong><br/><br/>
						<!-- 쇼핑몰이름 -->
						<strong style='font-weight:600; color:#333; letter-spacing:-0.5px'>".$siteInfo[s_adshop]."</strong> 회원가입이 정상적으로 완료 되었습니다.<br/>
						앞으로 고객님께 보다 좋은 상품을 제공하기 위해서 최선을 다 하겠습니다.<br/>
						회원가입을 진심으로 환영합니다.
					</td>
				</tr>
				<tr>
					<td>
						<!-- 이용안내 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:60px;'>
							<thead>
								<tr>
									<!-- 타이틀 -->
									<th style='text-align:left; color:#333; font-size:15px; font-weight:600; padding-bottom:15px'>이용안내</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<!-- 내용 -->
									<td style='padding:20px 30px; border:1px solid #ddd; background:#f5f5f5; color:#666; letter-spacing:0; word-wrap:break-word; word-break:keep-all;'>
										가입하신 정보는 <!-- 마이페이지  나의정보수정 링크 --><a href='".$app_HTTP_URL."/?pn=mypage.modify.form' style='font-weight:600; color:#333; text-decoration:none' target='_blank'>마이페이지 &gt; 정보수정</a> 에서 수정이 가능합니다.
									</td>
								<tr>

							</tbody>
						</table>
					</td>
				</tr>

				".$mailling_app_content."

			</tbody>
		</table>
		</div>
		<!-- / Common Box -->
";
