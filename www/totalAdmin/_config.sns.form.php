<?php
include_once('wrap.header.php');
$HTTPS_Check = ($system['ssl_use'] == 'Y'?true:false);
?>
<form action="_config.sns.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>스팸방지 구글 API</th>
					<td>
						<span class="fr_tx">사이트 키 :</span>
						<input type="text" name="recaptcha_api" class="design" value="<?php echo $siteInfo['recaptcha_api']; ?>" style="width:315px">
						<div class="clear_both"></div>
						<span class="fr_tx">시크릿 키 :</span>
						<input type="text" name="recaptcha_secret" class="design" value="<?php echo $siteInfo['recaptcha_secret']; ?>" style="width:315px">
						<div class="dash_line"></div>
						<a href="https://www.onedaynet.co.kr/manual/hyssence3/pages/guide_API.html#1_1" class="c_btn h27 black" target="_blank">API 발급 가이드 바로가기</a>
					</td>
				</tr>
				<tr>
					<th>페이스북 KEY</th>
					<td>
						<?php if($siteInfo['s_ssl_status'] == '대기' || $siteInfo['s_ssl_check'] == 'N') { ?>
							<span class="ssl_state none">보안 서버 미적용</span>
						<?php } else if($siteInfo['s_ssl_status'] == '진행' &&  $siteInfo['s_ssl_check'] == 'Y') { ?>
							<span class="ssl_state ok">보안 서버 적용중</span>
						<?php } else if($siteInfo['s_ssl_status'] == '만료') { ?>
							<span class="ssl_state end">보안 서버 만료</span>
						<?php } else { ?>
							<span class="ssl_state none">보안 서버 미적용</span>
						<?php } ?>

						<label class="design">
							<input type="checkbox" name="s_facebook_login_use" value="Y"<?php echo ($siteInfo['s_facebook_login_use'] == 'Y' && $siteInfo['s_ssl_status'] == '진행' && $siteInfo['s_ssl_check'] == 'Y'?' checked':null); ?><?php echo ($siteInfo['s_ssl_status'] != '진행' || $siteInfo['s_ssl_check'] != 'Y'?' onclick="if(confirm(\'페이스북 로그인 기능을 사용하기 위해서는 보안인증서(SSL)가 서버에 설치되어 있어야 합니다.\\n\\n보안서버 설정 페이지로 이동하시겠습니까?\')) location.href=\'_config.ssl.default_form.php\'; return false;"':null); ?>>
							페이스북 로그인 사용
						</label>
						<div class="divi"></div>
						<label class="design">
							<input type="checkbox" name="facebook_share_use" value="Y"<?php echo ($siteInfo['facebook_share_use'] == 'Y'?' checked':null); ?>>
							페이스북 공유하기 사용
						</label>

						<div class="dash_line"></div>
						<div class="clear_both"></div>

						<span class="fr_tx">앱 아이디 :</span>
						<input type="text" name="s_facebook_key" class="design" value="<?php echo $siteInfo['s_facebook_key']; ?>" style="width:250px">
						<div class="clear_both"></div>
						<span class="fr_tx">앱 시크릿 :</span>
						<input type="text" name="s_facebook_secret" class="design" value="<?php echo $siteInfo['s_facebook_secret']; ?>" style="width:250px">
						<div class="dash_line"></div>
						<a href="https://www.onedaynet.co.kr/manual/hyssence3/pages/guide_API.html#1_2" class="c_btn h27 black" target="_blank">API 발급 가이드 바로가기</a>

						<div class="tip_box">
							<?php echo _DescStr('앱도메인: <u>'.$system['host'].'</u>'); ?>
							<?php echo _DescStr('로그인 리디렉션 URI: <u>http://'.$system['host'].'/addons/sns_login/facebook/callback.php</u>'.($HTTPS_Check === true?", <u>https://".$system['ssl_domain'].':'.$system['ssl_port']."/addons/sns_login/facebook/callback.php</u>":null)); ?>
							<?php echo _DescStr('페이스북 로그인을 정상적으로 이용하기 위해서는 보안서버가 설치되어 있어야 합니다. '); ?>
							<?php echo _DescStr('공유 기능을 사용할 경우 상품 상세페이지에 해당 SNS로 상품을 공유할 수 있는 버튼이 노출됩니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>카카오톡 API</th>
					<td>
						<label class="design">
							<input type="checkbox" name="kakao_login_use" value="Y"<?php echo ($siteInfo['kakao_login_use'] == 'Y'?' checked':null); ?>>
							카카오 로그인 사용
						</label>
						<div class="divi"></div>
						<label class="design">
							<input type="checkbox" name="kakao_share_use" value="Y"<?php echo ($siteInfo['kakao_share_use'] == 'Y'?' checked':null); ?>>
							카카오 공유하기 사용
						</label>
						<div class="clear_both"></div>
						<span class="fr_tx">REST :</span>
						<input type="text" name="kakao_api" class="design" value="<?php echo $siteInfo['kakao_api']; ?>" style="width:250px">
						<div class="clear_both"></div>
						<span class="fr_tx">Javascript :</span>
						<input type="text" name="kakao_js_api" class="design" value="<?php echo $siteInfo['kakao_js_api']; ?>" style="width:250px">
						<div class="dash_line"></div>
						<a href="https://www.onedaynet.co.kr/manual/hyssence3/pages/guide_API.html#1_3" class="c_btn h27 black" target="_blank">API 발급 가이드 바로가기</a>

						<div class="tip_box">
							<?php echo _DescStr('Redirect URI: <u>http://'.$system['host'].'/addons/sns_login/kakao/callback.php</u>'.($HTTPS_Check === true?", <u>https://".$system['ssl_domain'].':'.$system['ssl_port']."/addons/sns_login/kakao/callback.php</u>":null)); ?>
							<?php echo _DescStr('공유 기능을 사용할 경우 상품 상세페이지에 해당 SNS로 상품을 공유할 수 있는 버튼이 노출됩니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>네이버 로그인</th>
					<td>
						<label class="design">
							<input type="checkbox" name="nv_login_use" value="Y"<?php echo ($siteInfo['nv_login_use'] == 'Y'?' checked':null); ?>>
							네이버 로그인 사용
						</label>
						<div class="clear_both"></div>
						<span class="fr_tx">Client ID :</span>
						<input type="text" name="nv_login_key" class="design" value="<?php echo $siteInfo['nv_login_key']; ?>" style="width:200px">
						<div class="clear_both"></div>
						<span class="fr_tx">Client Secret :</span>
						<input type="text" name="nv_login_secret" class="design" value="<?php echo $siteInfo['nv_login_secret']; ?>" style="width:200px">
						<div class="dash_line"></div>
						<a href="https://www.onedaynet.co.kr/manual/hyssence3/pages/guide_API.html#1_4" class="c_btn h27 black" target="_blank">API 발급 가이드 바로가기</a>

						<div class="tip_box">
							<?php echo _DescStr("서비스 URL : <u>http://".$system['host'].'</u>'); ?>
							<?php echo _DescStr("Callback URL <u>http://".str_replace('www.', '', $system['host'])."/addons/sns_login/naver/callback.php</u>".($HTTPS_Check === true?", <u>https://".$system['ssl_domain'].':'.$system['ssl_port']."/addons/sns_login/naver/callback.php</u>":null).""); ?>
						</div>
					</td>
				</tr>
				<tr id="set_instagram">
					<th>기타 SNS 공유 설정</th>
					<td>
						<label class="design">
							<input type="checkbox" name="twitter_share_use" value="Y"<?php echo ($siteInfo['twitter_share_use'] == 'Y'?' checked':null); ?>>
							트위터 공유 사용
						</label>
						<div class="divi"></div>
						<label class="design">
							<input type="checkbox" name="pinter_share_use" value="Y"<?php echo ($siteInfo['pinter_share_use'] == 'Y'?' checked':null); ?>>
							핀터레스트 공유 사용
						</label>
						<div class="clear_both"></div>
						<div class="tip_box">
							<?php echo _DescStr("공유 기능을 사용할 경우 상품 상세페이지에 해당 SNS로 상품을 공유할 수 있는 버튼이 노출됩니다."); ?>
						</div>
					</td>
				</tr>
				<tr id="set_instagram" style="display:none;">
					<th>인스타그램</th>
					<td>
						<label class="design">
							<input type="checkbox" name="instagram_main_use" value="Y"<?php echo ($siteInfo['instagram_main_use'] == 'Y'?' checked':null); ?>>
							메인 인스타그램 노출
						</label>
						<div class="clear_both"></div>

						<table>
							<colgroup>
								<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
							</colgroup>
							<tbody>
								<tr>
									<th>API 발급</th>
									<td>
										<a href="https://www.instagram.com/developer/clients/register/" class="c_btn h27 line" target="_blank">발급 페이지 바로가기</a>
										<a href="http://www.onedaynet.co.kr/p/doc/insta_manual.pdf" class="c_btn h27 gray" target="_blank">연동 매뉴얼보기</a>
									</td>
									<th>CLIENT ID</th>
									<td>
										<input type="text" name="insta_client" value="<?php echo $siteInfo['insta_client']; ?>" class="design js_insta_client" style="width:230px">
										<a href="#insta_auth" class="c_btn h27 black js_insta_sync">연동하기</a>

										<script type="text/javascript">
											$('.js_insta_sync').on('click', function(e) {
												e.preventDefault();
												InstaAPI();
											});

											function InstaAPI() {
												var clt = $('.js_insta_client').val();
												if(!clt) {
													alert('클라이언트 아이디를 입력 바랍니다.');
													$('.js_insta_client').focus();
													return false;
												}
												window.open('https://instagram.com/oauth/authorize/?client_id='+clt+'&amp;redirect_uri=<?php echo urlencode('http://'.$system['host'].OD_ADDONS_DIR.'/insta_api/'); ?>&amp;response_type=token', 'relation', 'width=1120, height=800, scrollbars=yes');
											}
										</script>
									</td>
								</tr>
								<tr>
									<th>사용자 이름</th>
									<td><input type="text" name="instagram_id" value="<?php echo $siteInfo['instagram_id']; ?>" class="design js_insta_id disabled" style="width:100%"  readonly>
										<?php echo _DescStr("연동 후 자동으로 입력됩니다. "); ?>
									</td>
									<th>Access Token</th>
									<td><input type="text" name="insta_token" value="<?php echo $siteInfo['insta_token']; ?>" class="design js_insta_token disabled" style="width:100%"  readonly>
										<?php echo _DescStr("연동 후 자동으로 입력됩니다. "); ?>
									</td>
								</tr>
								<tr>
									<th>입력 정보</th>
									<td colspan="3">

										<table class="it_only_text">
											<colgroup>
												<col width="150"/><col width="*"/>
											</colgroup>
											<tbody>
												<tr>
													<th>Application Name</th>
													<td class="js-clipboard js_i1" data-clipboard-target=".js_i1" oncut="return false;" onpaste="return false;"><?php echo reset(explode('.', $system['host'])); // 어플명(도메인의 주소로 한다...) ?></td>
												</tr>
												<tr>
													<th>Description</th>
													<td class="js-clipboard js_i2" data-clipboard-target=".js_i2" oncut="return false;" onpaste="return false;"><?php echo $siteInfo['s_adshop']; // 사이트명 ?></td>
												</tr>
												<tr>
													<th>Company Name</th>
													<td class="js-clipboard js_i3" data-clipboard-target=".js_i3" oncut="return false;" onpaste="return false;"><?php echo $siteInfo['s_company_name']; // 회사명 ?></td>
												</tr>
												<tr>
													<th>Website URL</th>
													<td class="js-clipboard js_i4" data-clipboard-target=".js_i4" oncut="return false;" onpaste="return false;"><?php echo 'http://'.$system['host']; // 홈페이지 주소 ?></td>
												</tr>
												<tr>
													<th>Valid redirect URIs</th>
													<td class="js-clipboard js_i5" data-clipboard-target=".js_i5" oncut="return false;" onpaste="return false;"><?php echo 'http://'.$system['host'].OD_ADDONS_DIR.'/insta_api/'; // 인스타그램 연동 주소 ?></td>
												</tr>
												<tr>
													<th>Privacy Policy URL</th>
													<td class="js-clipboard js_i6" data-clipboard-target=".js_i6" oncut="return false;" onpaste="return false;"><?php echo 'http://'.$system['host'].'/?pn=pages.view&type=agree&data=privacy'; // 개인정보 취급방침 ?></td>
												</tr>
												<tr>
													<th>Contact email</th>
													<td class="js-clipboard js_i7" data-clipboard-target=".js_i7" oncut="return false;" onpaste="return false;"><?php echo $siteInfo['s_ademail']; // 담당자 이메일 ?></td>
												</tr>
											</tbody>
										</table>
										<div class="tip_box">
											<?php echo _DescStr("발급 페이지의 입력폼에서 정보를 입력해주세요. "); ?>
										</div>
									</td>
								</tr>
							</tbody>
						</table>

						<div class="tip_box">
							<?php echo _DescStr("연동 하고자 하는 아이디로 인스타그램(https://www.instagram.com/)에 로그인 후, 위 발급URL로 접속합니다."); ?>
							<?php echo _DescStr("API발급을 위해 개발자 등록을 먼저 해주세요. (한 아이디당 최초 한번)"); ?>
							<?php echo _DescStr("개발자 등록 완료 후, 'Register'을 클릭하고 'Details' 탭에서 해당 정보를 입력해주세요."); ?>
							<?php echo _DescStr("'Security' 탭에서 'Disable implicit OAuth'를 체크 해제하고 'Register'를 클릭하면 CLIENT ID가 발급됩니다."); ?>
							<?php echo _DescStr("발급받은 CLIENT ID를 입력하고 '연동하기'를 클릭한 후 새창에서 'Authorize' 버튼을 눌러주세요."); ?>
							<?php echo _DescStr(" '사용자이름'과 'Access Token'이 자동으로 입력이 되면 꼭 페이지 하단의 확인버튼을 클릭해야 정보가 저장됩니다."); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>소셜미디어 링크</th>
					<td>
						<table>
							<colgroup>
								<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
							</colgroup>
							<tbody>
								<tr>
									<th>인스타그램</th>
									<td><input type="text" name="sns_link_instagram" class="design" value="<?php echo $siteInfo['sns_link_instagram']; ?>" style="width:100%"></td>
									<th>페이스북</th>
									<td><input type="text" name="sns_link_facebook" class="design" value="<?php echo $siteInfo['sns_link_facebook']; ?>" style="width:100%"></td>
								</tr>
								<tr>
									<th>트위터</th>
									<td><input type="text" name="sns_link_twitter" class="design" value="<?php echo $siteInfo['sns_link_twitter']; ?>" style="width:100%"></td>
									<th>YouTube</th>
									<td><input type="text" name="sns_link_youtube" class="design" value="<?php echo $siteInfo['sns_link_youtube']; ?>" style="width:100%"></td>
								</tr>
								<tr>
									<th>블로그</th>
									<td><input type="text" name="sns_link_blog" class="design" value="<?php echo $siteInfo['sns_link_blog']; ?>" style="width:100%"></td>
									<th>카페</th>
									<td><input type="text" name="sns_link_cafe" class="design" value="<?php echo $siteInfo['sns_link_cafe']; ?>" style="width:100%"></td>
								</tr>
								<tr>
									<th>카카오톡 채널</th>
									<td><input type="text" name="sns_link_kkp" class="design" value="<?php echo $siteInfo['sns_link_kkp']; ?>" style="width:100%"></td>
									<th>카카오 스토리</th>
									<td><input type="text" name="sns_link_kks" class="design" value="<?php echo $siteInfo['sns_link_kks']; ?>" style="width:100%"></td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr('스킨에 따라 노출 되지 않을 수 있습니다.', 'black'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- 저장 -->
	<?php echo _submitBTNsub(); ?>
	<!-- 저장 -->
</form>

<?php include_once('wrap.footer.php'); ?>