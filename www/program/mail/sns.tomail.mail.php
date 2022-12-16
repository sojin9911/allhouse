<?php
unset($mailling_content);
$arrMailItem = array();
$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

// -------------------- 수신/발신자 정보처리 --------------------------
if( trim($_POST['toName']) != ''){ // 보낸이 이름
	$__first = mb_substr($_POST['toName'],0,1,'utf8'); // 보안처리
	$__last = mb_substr($_POST['toName'],2,mb_strlen($_POST['toName'], 'utf8' ),'utf8'); // 보안처리
	$arrMailItem['toName'] = $__first."*".(mb_strlen($_POST['toName'],'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
}else{  $arrMailItem['toName'] = '비공개'; }


if( trim($_POST['fromName']) != ''){ // 받는이 이름
	$__first = mb_substr($_POST['fromName'],0,1,'utf8'); // 보안처리
	$__last = mb_substr($_POST['fromName'],2,mb_strlen($_POST['fromName'], 'utf8' ),'utf8'); // 보안처리
	$arrMailItem['fromName'] = $__first."*".(mb_strlen($_POST['fromName'],'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
}else{  $arrMailItem['fromName'] = '비공개'; }
// -------------------- 수신/발신자 정보처리 끝 --------------------------
$mailling_content = "

		<!-- ● Common Box -->
		<div style='padding:30px;'>
		<table style='width:100%;border-spacing:0; font-size:12px; font-family:\"돋움\",Dotum; line-height:17px;'>
			<tbody>
				<tr>
					<td style='text-align:center; border-bottom:1px solid #ddd; padding:3px 0 36px;'>
						<!-- 메일링 타이틀 이미지 -->
						<!-- 타이틀 이미지 없을때 기본 타이틀 이미지 default_txt.jpg 사용 -->
						<div class='' style='max-width:100%; display:inline-block'><img src='".$app_HTTP_URL."/images/mailing/share_pro.jpg' alt='상품을 추천합니다.' style='width:100%'/></div>
					</td>
				</tr>
				<tr>
					<td style='line-height:19px; word-wrap:break-word; word-break:keep-all; font-size:15px; letter-spacing:-1px; color:#666; padding-top:50px;'>
						<!-- 이름 다음에는 br 두개 들어감 -->
						<strong style='color:#333;font-weight:600'>".$arrMailItem['toName']."님</strong><br/> <br/>
						<!-- 추천인 이름 -->
						<strong style='color:#333;font-weight:600'>".$arrMailItem['fromName']."</strong>님께서 보내신 상품 추천메일입니다.<br/>
						자세한 상품정보는 링크를 통해 <!-- 쇼핑몰이름 -->".$siteInfo[s_adshop]."에서 확인해주세요.
					</td>
				</tr>
				<tr>
					<td>
						<!-- 주문정보 리스트 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:42px;'>
							<colgroup>
								<col width='23%'/><col width='*'/>
							</colgroup>
							<thead>
								<!-- 타이틀 -->
								<tr>
									<th colspan='3' style='text-align:left; border-bottom:1px solid #555; color:#333; font-size:15px; font-weight:600; padding-bottom:8px'>추천상품</th>
								</tr>
							</thead>
							<tbody>
								<!-- tr반복 -->
								<tr>
									<!-- 상품이미지 -->
									<td style='padding:20px 0; border-bottom:1px solid #ddd; vertical-align:top; text-align:center'>
										<!-- 상품으로 이동 -->
										<a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$row_product['p_code']."' style='overflow:hidden; border:1px solid #e6e6e6; box-sizing:border-box; display:block; margin-right:30px' target='_blank'>
											<img src='".get_img_src($row_product['p_img_list_square'], IMG_DIR_PRODUCT)."' alt='".stripslashes($row_product['p_name'])."' style='width:100%; float:left; min-width:60px'/>
										</a>
										<a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$row_product['p_code']."' style='text-decoration:none; border:1px solid #666; color:#666; padding:3px 5px; margin:8px 0 0; display:block; margin-right:30px; letter-spacing:-0.5px'>상품바로가기</a>
									</td>
									<!-- 상품 주문정보 -->
									<td style='padding:20px 0; border-bottom:1px solid #ddd; vertical-align:top; line-height:17px'>
										<table style='width:100%;border-spacing:0;'>
											<colgroup>
												<col width='20%'/><col width='*'/>
											</colgroup>
											<tbody>
												<!-- 주문정보 tr 반복 -->
												<tr>
													<td style='vertical-align:top; padding:4px 0'>상품명</td>
													<!-- 상품 링크 -->
													<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'><a href='' style='font-weight:600; text-decoration:none; color:#666' target='_blank'>".stripslashes($row_product['p_name'])."</a></td>
												</tr>
												<tr>
													<td style='vertical-align:top; padding:4px 0'>상품설명</td>
													<!-- 옵션 br 구분 -->
													<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>
														".stripslashes($row_product['p_subname'])."
													</td>
												</tr>
												<tr>
													<td style='vertical-align:top; padding:4px 0'>상품금액</td>
													<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>".number_format($row_product['p_price'])."원</td>
												</tr>
												<tr>
													<td style='vertical-align:top; padding:4px 0'>추천내용</td>
													<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>".nl2br(strip_tags($textarea))."</td>
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