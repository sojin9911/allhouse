<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$_rurl -> 로그인 후 이동 할 주소 => (/program/member.login.form.php에서 지정)
	$is_sns_login_form -> SNS로그인 사용여부 => (/program/member.login.form.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/member.login.form.php에서 지정)
*/
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_member">
	<div class="layout_fix"id="member_login-width">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit" >
			<div class="title"id="member_login-tit">로그인</div>
			<!-- 로케이션 -->
			<!--
			<div class="c_location">
				<ul>
					<li>홈</li>
					<li>멤버십</li>
					<li>로그인</li>
				</ul>
			</div>
		</div>
		-->
		<!-- /공통페이지 타이틀 -->

			<!-- ◆로그인 -->
			<?php // === 비회원 구매 설정 kms 2019-06-24 ==== ?>
			<div class="c_login" <?php echo ($none_member_buy === true || $isNoneMemberBuy === true ?  "if_none_member" : "") ;  ?>">
			<ul class="ul">
				<li class="li">
					<div class="login_form">
						<div class="c_group_tit">회원 로그인</div>
						<form action="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php" class="js_login_form" method="post" target="common_frame" autocomplete="off">
							<input type="hidden" name="_mode" value="login">
							<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">
							<div class="form login-form-width">
								<ul>
									<li><input type="text" name="login_id" class="input_design"<?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' value="'.$_COOKIE['AuthSDIndividualIDChk'].'"':null); ?> placeholder="아이디" required /></li>
									<li><input type="password" name="login_password" class="input_design" placeholder="비밀번호" autocomplete="new-password" required /></li>
								</ul>
								<input type="submit" class="btn_login" value="로그인"/>
							</div>

							<div class="save_id">
								<input type="checkbox" name="login_id_chk" id="save-id_checkbox" value="Y"<?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' checked':null); ?> />
								<label id="save-id_label" for="save-id_checkbox">아이디 저장</label>		
								<!--<div class="exp">※ 회원가입을 하시면 다양한 혜택을 받으실 수 있습니다.</div>-->
							</div>
						</form>
						<div class="sns-login">
							<a href="#"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/pc_kakao.png" alt="카카오 아이디 로그인"></a>
							<!--기능 없이 버튼만 있습니다-->
						</div>
						<div class="c_btnbox">
							<ul>
								<li><a href="/?pn=member.join.agree" class="c_btn h40 dark bold">회원가입</a></li>
								<li><a href="/?pn=member.find.form" class="c_btn h40 light bold">아이디 찾기</a></li>
								<li><a href="/?pn=member.find.form" class="c_btn h40 light bold">비밀번호 찾기</a></li>
								<!--원래 아이디/비밀번호 찾기 페이지가 같은 페이지였는데 수정할때는 페이지가 따로 되어있어서
									버튼만 각각 만들어두고 링크는 같은 아이디/비밀번호찾기 페이지 링크 걸었습니다. 
								-->
							</ul>
						</div>

						<?php if($isNoneMemberBuy){ ?>
						<!-- 2020-03-25 SSJ :: 비회원 주문 버튼 추가 -->
						<div class="c_btnbox">
							<ul>
								<li style="width:100%; padding-left:0;"><a href="/?pn=shop.order.form" class="c_btn h40 color bold">비회원 구매하기</a></li>
							</ul>
						</div>
						<?php } ?>

						<?php
						if($is_sns_login_form === true) {
							$sns_login_count_class = $sns_login_count;
							if($sns_login_count_class >= 4) $sns_login_count_class = 2;
						?>
							<!-- 소셜 로그인(맞춤) / 기본은 소셜 로그인 없음 -->
							<div class="c_sns_login">
								<div class="c_group_tit">소셜 로그인</div>

								<!-- 소셜로그인 버튼 li 반복 / 2,4개일때 if_col2, 3개 일때 if_col3 클래스 추가 -->
								<div class="sns_btn<?php echo ($sns_login_count_class > 1?' if_col'.$sns_login_count_class:null); ?>">
									<ul>
										<?php
										if($SNSField['naver']['login_use'] == 'Y') {
											$sns_callback_url = $SNSField['naver']['callback_url'];
										?>
											<li>
												<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn naver" title="네이버 로그인">
													<span class="sns">
														<span class="en">Naver Login</span><span class="kr">네이버 로그인</span>
													</span>
												</a>
											</li>
										<?php } ?>
										<?php
										if($SNSField['kakao']['login_use'] == 'Y') {
											$sns_callback_url = $SNSField['kakao']['callback_url'];
										?>
											<li>
												<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn kakao" title="카카오톡 로그인">
													<span class="sns">
														<span class="en">Kakaotalk Login</span><span class="kr">카카오톡 로그인</span>
													</span>
												</a>
											</li>
										<?php } ?>
										<?php
										if($SNSField['facebook']['login_use'] == 'Y') {
											$sns_callback_url = $SNSField['facebook']['callback_url'];
										?>
											<li>
												<a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn face" title="페이스북 로그인">
													<span class="sns">
														<span class="en">Facebook Login</span><span class="kr">페이스북 로그인</span>
													</span>
												</a>
											</li>
										<?php } ?>
									</ul>
								</div>
							</div>
						<?php } ?>
					</div>
				</li>
				<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
				<li class="li" style="<?php echo ($none_member_buy === true || $isNoneMemberBuy === true ?  "display:none;" : "") ;  ?>">
					<div class="login_form">
						<div class="c_group_tit">비회원 주문조회 하기</div>

						<div class="form" autocomplete="off">
							<form name="guest_order" action="/?pn=member.login.form" class="js_guest_order_form " method="post">
							<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">
								<div class="login-form-width">
									<ul>
										<li><input type="text" name="_oname" class="input_design" placeholder="주문자명" value="<?php echo $_oname; ?>" required/></li>
										<li><input type="text" name="_onum" class="input_design js_onum" placeholder="주문번호" value="<?php echo $_onum; ?>" required/></li>
									</ul>
								<input type="submit" class="btn_order js_order_btn" value="확인"/>
								</div>
								<p><img class="form_img-tramsform" src="http://comondev.com/data/skin/front/allhouse/img/member/icon_caution.png"> 주문번호와 비밀번호를 잊으신 경우, 고객센터로 문의하여 주시기 바랍니다.</p>
							</form>
						</div>

						<!-- 가이드 -->
						<!--
						<dl class="guide">
							<dt>비회원 주문/배송조회 안내사항</dt>
							<dd>비회원으로 상품을 구매하신 경우에만 주문(배송)조회가 가능합니다.</dd>
							<dd>주문자명 및 주문번호가 기억나지 않는 경우 고객센터로 연락해주시기 바랍니다.</dd>
							<dd>비회원 구매 시에는 쇼핑몰의 할인/적립 등의 혜택을 받으실 수 없습니다.</dd>
						</dl>
						-->
					</div>
				</li>
			</ul>
		</div>
		<!-- /로그인 -->

	</div>
</div>
</div>
<!-- /공통페이지 섹션 -->



<?php
	# 비회원 주문조회
	if($_oname && $_onum) {
		$is_order = true;
		if($is_order) include_once(OD_PROGRAM_ROOT.'/service.guest.order.view.php');
	}
?>

<script type="text/javascript">

	// 비회원 주문번호 유효성체크
	$(document).on('click','.js_order_btn',function(){
		var onum = $('.js_onum').val();
		var pattern = /[0-9]{5}-[0-9]{5}-[0-9]{5}/;
		var onum_chk = pattern.test(onum);

		if(onum && onum_chk!=true){
			alert('올바른 주문번호를 입력해주세요.');
			return false;
		}
	});

	$(document).ready(function() {
		// 로그인
		$('.js_login_form').validate({
			rules: {
				login_id: { required: true },
				login_password: { required: true }
			},
			messages: {
				login_id: { required: '아이디를 입력하세요' },
				login_password: { required: '패스워드를 입력하세요' }
			}
		});

		// 비회원 주문조회
		$('.js_guest_order_form').validate({
			rules: {
				_oname: { required: true },
				_onum: { required: true }
			},
			messages: {
				_oname: { required: '주문자의 이름을 입력하세요' },
				_onum: { required: '주문번호를 입력하세요' }
			}
		});
	});
</script>