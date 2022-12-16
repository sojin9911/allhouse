<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$_rurl -> 로그인 후 이동 할 주소 => (/program/member.login.form.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/member.login.form.php에서 지정)
*/
$page_title = '비회원 주문조회'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_member">

	<!-- ◆공통 탭메뉴 -->
	<!-- 활성화시 li에 hit 클래스 추가 -->
	<div class="c_tabmenu">
		<ul>
			<li><a href="/?pn=member.login.form" class="tab">회원 로그인</a></li>
			<li class="hit"><a href="/?pn=service.guest.order.list" class="tab">비회원 주문조회</a></li>
		</ul>
	</div>

	<form class="js_guest_order_form" action="/?pn=<?php echo $pn; ?>" method="post">
		<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">
		<!-- ◆로그인 -->
		<div class="c_login">
			<ul class="ul">
				<li class="li">
					<div class="login_form">
						<div class="form">
							<ul>
								<li><input type="text" name="_oname" class="input_design" value="<?php echo $_oname; ?>" placeholder="주문자명"/></li>
								<li><input type="tel" name="_onum" class="input_design js_onum" value="<?php echo $_onum; ?>" placeholder="주문번호"/></li>
							</ul>
							<input type="submit" class="btn_order js_order_btn" value="주문조회"/>
						</div>

						<!-- 가이드 -->
						<dl class="guide">
							<dt>비회원 주문/배송조회 안내사항</dt>
							<dd>비회원으로 상품을 구매하신 경우에만 주문(배송)조회가 가능합니다.</dd>
							<dd>주문자명 및 주문번호가 기억나지 않는 경우 고객센터로 연락해주시기 바랍니다.</dd>
							<dd>비회원 구매 시에는 쇼핑몰의 할인/적립 등의 혜택을 받으실 수 없습니다.</dd>
						</dl>
					</div>
				</li>
			</ul>
		</div>
		<!-- /로그인 -->
	</form>
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