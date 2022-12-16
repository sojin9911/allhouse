<?php
// 탑네비 메뉴 리스트
$TopNavArr = array();
if(is_login() === true) { // 로그인 후

	$TopNavArr = array(
		'로그아웃'=>array(
			'link'=>OD_PROGRAM_URL.'/member.login.pro.php?_mode=mobile_logout',
			'hit'=>false
		),
		'정보수정'=>array(
			'link'=>'/?pn=mypage.modify.form',
			'hit'=>($pn == 'mypage.modify.form'?true:false)
		)
	);

	$TopNavArr = array(
		/*
		'로그아웃'=>array(
			'link'=>OD_PROGRAM_URL.'/member.login.pro.php?_mode=mobile_logout',
			'hit'=>false
		),
		*/
		'나의 보관함'=>array(
			'link'=>'/?pn=mypage.pending_list',
			'hit'=>($pn == 'mypage.pending_list'?true:false)
		),
		'주문목록/배송조회'=>array(
			'link'=>'/?pn=mypage.order.list',
			'hit'=>(in_array($pn,array('mypage.order.list','mypage.order.view'))?true:false)
		),
		'취소/반품/교환 내역'=>array(
			'link'=>'/?pn=mypage.cancel_list',
			'hit'=>($pn == 'mypage.cancel_list'?true:false)
		),
		'환불/입금 내역'=>array(
			'link'=>'/?pn=mypage.refund_list',
			'hit'=>($pn == 'mypage.refund_list'?true:false)
		),
		'미입고 내역'=>array(
			'link'=>'/?pn=mypage.stock_list',
			'hit'=>($pn == 'mypage.stock_list'?true:false)
		),
		// 내부패치 68번줄 kms 2019-11-05
		'찜 리스트'=>array(
			'link'=>'/?pn=mypage.wish.list',
			'hit'=>($pn == 'mypage.wish.list'?true:false)
		),
// 내부패치 68번줄 kms 2019-11-05
//		'문의내역'=>array(
//			'link'=>'/?pn=mypage.inquiry.list',
//			'hit'=>($pn == 'mypage.inquiry.list'?true:false)
//		),
		'쪽지 보내기'=>array(
			'link'=>'/?pn=message_write',
			'hit'=>($pn == 'message_write'?true:false)
		),
		'받은 쪽지함'=>array(
			'link'=>'/?pn=message_list',
			'hit'=>($pn == 'message_list' || $pn == 'message_list_contents' ?true:false)
		),
		'보낸 쪽지함'=>array(
			'link'=>'/?pn=recived_note',
			'hit'=>($pn == 'recived_note' || $pn == 'recived_note_contents' ?true:false)
		),
		'예치금 현황'=>array(
			'link'=>'/?pn=deposit',
			'hit'=>($pn == 'deposit'?true:false)
		),
		'1:1 문의'=>array(
			'link'=>'/?pn=mypage.inquiry.list',
			'hit'=>($pn == 'mypage.inquiry.list' || $pn == 'mypage.inquiry.form' ?true:false)
		),
		'회원정보변경'=>array(
			'link'=>'/?pn=mypage.modify.form',
			'hit'=>($pn == 'mypage.modify.form'?true:false)
		),
		'회원탈퇴'=>array(
			'link'=>'/?pn=mypage.leave.form',
			'hit'=>($pn == 'mypage.leave.form'?true:false)
		),
		'배송지 관리'=>array(
			'link'=>'/?pn=shipping',
			'hit'=>($pn == 'shipping'?true:false)
		),
		'나의 상품문의'=>array(
			'link'=>'/?pn=mypage.qna.list',
			'hit'=>($pn == 'mypage.qna.list'?true:false)
		),
	);
}
else { // 로그인 전

	$TopNavArr = array(
		//'로그인'=>array(
		//	'link'=>'/?pn=member.login.form',
		//	'hit'=>($pn == 'member.login.form'?true:false)
		//),
		//'아이디찾기'=>array(
		//	'link'=>'/?pn=member.find.form&_mode=find_id',
		//	'hit'=>($pn == 'member.findid.form' || ($pn == 'member.find.form' && (!$_mode || $_mode == 'find_id'))?true:false)
		//),
		//'비밀번호찾기'=>array(
		//	'link'=>'/?pn=member.find.form&_mode=find_pw',
		//	'hit'=>($pn == 'member.findpw.form' || ($pn == 'member.find.form' && ($_mode == 'find_pw'))?true:false)
		//),
		//'회원가입'=>array(
		//	'link'=>'/?pn=member.join.agree',
		//	'hit'=>(in_array($pn, array('member.join.agree', 'member.join.form', 'member.join.auth'))?true:false)
		//)
	);
}
?>
<!-- ******************************************
     공통페이지 상단(공통)
  -- ****************************************** -->
<div class="c_page_tit<?php echo in_array($pn,array('mypage.main')) ? ' if_open' : null ?><?php echo (count($TopNavArr) <= 0?' if_nomenu':null); ?> js_top_nav_wrap"><!-- 열리면 if_open / 메뉴없으면 if_nomenu -->
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
		<div class="tit"><?php echo $page_title; ?></div>
		<?php if(count($TopNavArr) > 0) { ?>
			<a href="#none" class="btn_ctrl js_top_nav_toggle" title="메뉴 열고닫기"></a><!-- (없으면숨김) -->
		<?php } ?>
	</div>

	<?php if(count($TopNavArr) > 0) { ?>
		<!-- 메뉴열기 (없으면숨김) -->
		<div class="nav_box">
			<div class="inner">
			<!-- li 3개 채워서 ul반복 -->
				<ul>
					<?php
					$TopNavNum = 0;
					foreach($TopNavArr as $tn_k=>$tn_v) {
						if($TopNavNum > 0 && $TopNavNum%3 === 0) {
							echo '</ul><ul>';
							$TopNavNum = 0;
						}
						$TopNavNum++;
					?>
						<li<?php echo ($tn_v['hit'] === true?' class="hit"':null); ?>><a href="<?php echo $tn_v['link']; ?>" class="btn"><?php echo $tn_k; ?></a></li>
					<?php } ?>
					<?php
					// 잔여개수 채우기
					if($TopNavNum < 3) {
						for($TopNav=$TopNavNum; $TopNav<3; $TopNav++) {
					?>
						<li></li>
					<?php }} ?>
				</ul>
			</div>
		</div>
	<?php } ?>
</div>
<!-- / 서브 상단(공통) -->
<script type="text/javascript">
	$(document).on('click', '.js_top_nav_toggle', function(e) {
		e.preventDefault();
		$(this).closest('.js_top_nav_wrap').toggleClass('if_open');
	});
</script>