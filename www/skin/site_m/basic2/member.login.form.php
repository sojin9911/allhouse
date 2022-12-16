<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$_rurl -> 로그인 후 이동 할 주소 => (/program/member.login.form.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/member.login.form.php에서 지정)
*/
$page_title = '로그인'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_member">
	<!-- ◆공통 탭메뉴 -->
	<!-- 활성화시 li에 hit 클래스 추가 -->
	<div class="c_tabmenu hide">
		<ul>
			<li<?php echo ($pn == 'member.login.form'?' class="hit"':null); ?>><a href="/?pn=member.login.form" class="tab">회원 로그인</a></li>
			<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ====  ?>
			<?php if ( !$none_member_buy && !$isNoneMemberBuy ) {	 ?>
			<li<?php echo ($pn == 'service.guest.order.list'?' class="hit"':null); ?>><a href="/?pn=service.guest.order.list" class="tab">비회원 주문조회</a></li>
			<?php } ?>
			<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ====  ?>
		</ul>
	</div>

	<!-- ◆로그인 -->
	<div class="c_login">
		<ul class="ul">
			<li class="li">
				<div class="login_form">
					<!-- <div class="c_group_tit">회원 로그인</div> -->
					<form action="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php" class="js_login_form" method="post" target="common_frame" autocomplete="off">
						<input type="hidden" name="_mode" value="login">
						<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">
						<div class="form">
							<ul>
								<li><input type="text" name="login_id" class="input_design"<?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' value="'.$_COOKIE['AuthSDIndividualIDChk'].'"':null); ?> placeholder="아이디" required /></li>
								<li><input type="password" name="login_password" class="input_design " placeholder="비밀번호" autocomplete="new-password" required /></li>
							</ul>
							<div class="login_option">
								<div class="keep_login">
									<label><input type="checkbox" name="login_id_chk" value="Y" <?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' checked':null); ?> />로그인 상태 유지</label>
								</div>
								<div class="save_id">
									<label><input type="checkbox" name="login_id_chk" value="Y" <?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' checked':null); ?> />아이디 저장</label>
								</div>
							</div>
							<input type="submit" class="btn_login" value="로그인" <?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' checked':null); ?> />
						</div>

					</form>

					<div class="c_btnbox">
						<a href="">
							<ul class="go_kakao_login">
								<li class="img_area"></li>
								<li class="kakao_login_btn">카카오 아이디로 로그인</li>
							</ul>
						</a>
						<ul class="go_account_modi">
							<li><a href="/?pn=member.join.agree" class="">회원가입</a></li>
							<li><a href="/?pn=member.find.form" class="">아이디찾기</a></li>
							<li><a href="/?pn=member.find.form" class="">비밀번호찾기</a></li>
						</ul>
						<?php if($isNoneMemberBuy){ ?>
							<!-- 2020-03-25 SSJ :: 비회원 주문 버튼 추가 -->
							<ul>
								<li><a href="/?pn=shop.order.form" class="c_btn h40 color">비회원 구매하기</a></li>
							</ul>
						<?php } ?>
					</div>


					<?php if($is_sns_login_form === true) { ?>
						<!-- 소셜 로그인(맞춤) / 기본은 소셜 로그인 없음 -->
						<div class="c_sns_login">
							<div class="c_group_tit">소셜 로그인</div>

							<!-- 소셜로그인 버튼 li 반복 -->

							<div class="sns_btn">
								<ul>
									<?php
									if($SNSField['naver']['login_use'] == 'Y') {
										$sns_callback_url = $SNSField['naver']['callback_url'];
									?>
										<li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn naver" title="네이버 로그인"><span class="sns"></span><span class="en">네이버</span></a></li>
									<?php } ?>
									<?php
									if($SNSField['kakao']['login_use'] == 'Y') {
										$sns_callback_url = $SNSField['kakao']['callback_url'];
									?>
										<li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn kakao" title="카카오톡 로그인"><span class="sns"></span><span class="en">카카오톡</span></a></li>
									<?php } ?>
									<?php
									if($SNSField['facebook']['login_use'] == 'Y') {
										$sns_callback_url = $SNSField['facebook']['callback_url'];
									?>
										<li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn face" title="페이스북 로그인"><span class="sns"></span><span class="en">페이스북</span></a></li>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php } ?>
				</div>
			</li>
		</ul>


		<form class="js_guest_order_form" action="/?pn=<?php echo $pn; ?>" method="post">
			<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">
			<div class="c_login">
				<ul class="ul">
					<li class="li">
						<div class="login_form">
							<dl class="guide">
								<dt>비회원 주문조회</dt>
								<dd>주문자 이름과 주문 번호를 입력해주세요</dd>
							</dl>
							<div class="form">
								<ul>
									<li><input type="text" name="_oname" class="input_design" value="<?php echo $_oname; ?>" placeholder="주문자명"/></li>
									<li><input type="tel" name="_onum" class="input_design js_onum" value="<?php echo $_onum; ?>" placeholder="주문번호"/></li>
								</ul>
								<input type="submit" class="btn_order js_order_btn" value="주문 조회"/>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<!-- /로그인 -->
		</form>
	</div>
	<!-- /로그인 -->
</div>
<!-- /공통페이지 섹션 -->

<?php
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

<script type="text/javascript">
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
	});
</script>