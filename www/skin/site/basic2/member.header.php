<?php
// 탑네비 메뉴 리스트
$TopNavArr = array();
$TopNavArr = array(
	'<span class="mh_list_tit">쇼핑정보</span>'=>array(
		'link'=>'/?pn=mypage.pending_list',
		'hit'=>($pn == 'mypage.pending_list'?true:false)
	),
	'<span class="mh_list_cont btn">나의 보관함</span>'=>array(
		'link'=>'/?pn=mypage.pending_list',
		'hit'=>($pn == 'mypage.pending_list'?true:false)
	),
	'<span class="mh_list_cont btn">주문목록/배송조회</span>'=>array(
		'link'=>'/?pn=mypage.order.list',
		'hit'=>(in_array($pn,array('mypage.order.list','mypage.order.view'))?true:false)
	),
	'<span class="mh_list_cont btn">취소/반품/교환 내역</span>'=>array(
		'link'=>'/?pn=mypage.cancel_list',
		'hit'=>($pn == 'mypage.cancel_list'?true:false)
	),
	'<span class="mh_list_cont btn hide">환불/입금 내역</span>'=>array(
		'link'=>'/?pn=mypage.refund_list',
		'hit'=>($pn == 'mypage.refund_list'?true:false)
	),
	'<span class="mh_list_cont btn">미입고 내역</span>'=>array(
		'link'=>'/?pn=mypage.stock_list',
		'hit'=>($pn == 'mypage.stock_list'?true:false)
	),
	'<span class="mh_list_cont btn">찜리스트</span>'=>array(
		'link'=>'/?pn=mypage.wish.list',
		'hit'=>($pn == 'mypage.wish.list'?true:false)
	),
// 내부패치 68번줄 kms 2019-11-05
//	'문의내역'=>array(
//		'link'=>'/?pn=mypage.inquiry.list',
//		'hit'=>($pn == 'mypage.inquiry.list'?true:false)
//	),
	'<span class="mh_list_tit">쪽지함</span>'=>array(
		'link'=>'/?pn=message_list',
		'hit'=>($pn == 'message_list'?true:false)
	),
	'<span class="mh_list_cont btn">쪽지 보내기</span>'=>array(
		'link'=>'/?pn=message_write',
		'hit'=>($pn == 'message_write'?true:false)
	),
	'<span class="mh_list_cont btn">받은 쪽지함</span>'=>array(
		'link'=>'/?pn=message_list',
		'hit'=>(in_array($pn,array('message_list','message_list_contents'))?true:false)
	),
	'<span class="mh_list_cont btn">보낸 쪽지함</span>'=>array(
		'link'=>'/?pn=recived_note',
		'hit'=>(in_array($pn,array('recived_note','recived_note_contents'))?true:false)
	),


	'<span class="mh_list_tit">예치금 관리</span>'=>array(
		'link'=>'/?pn=deposit',
		'hit'=>($pn == 'deposit'?true:false)
	),
	'<span class="mh_list_cont btn">예치금 현황</span>'=>array(
		'link'=>'/?pn=deposit',
		'hit'=>($pn == 'deposit'?true:false)
	),

	'<span class="mh_list_tit">고객센터</span>'=>array(
		'link'=>'/?pn=mypage.inquiry.list',
		'hit'=> ($pn == 'mypage.inquiry.list' || $pn == 'mypage.inquiry.form' ?true:false) 
	),
	'<span class="mh_list_cont btn">1:1 문의</span>'=>array(
		'link'=>'/?pn=mypage.inquiry.list',
		'hit'=> ($pn == 'mypage.inquiry.list' || $pn == 'mypage.inquiry.form' ?true:false) 
	),


	'<span class="mh_list_tit">회원정보</span>'=>array(
		'link'=>'/?pn=mypage.modify.form',
		'hit'=>($pn == 'mypage.modify.form'?true:false)
	),
	'<span class="mh_list_cont btn">회원정보변경</span>'=>array(
		'link'=>'/?pn=mypage.modify.form',
		'hit'=>($pn == 'mypage.modify.form'?true:false)
	),
	'<span class="mh_list_cont btn">회원탈퇴</span>'=>array(
		'link'=>'/?pn=mypage.leave.form',
		'hit'=>($pn == 'mypage.leave.form'?true:false)
	),
	'<span class="mh_list_cont btn">배송지 관리</span>'=>array(
		'link'=>'/?pn=shipping',
		'hit'=>($pn == 'shipping'?true:false)
	),


	'<span class="mh_list_tit">나의 상품문의</span>'=>array(
	),
	'<span class="mh_list_cont btn">나의 상품문의</span>'=>array(
		'link'=>'/?pn=mypage.qna.list',
		'hit'=>($pn == 'mypage.qna.list'?true:false)
	)

	// '상품후기'=>array(
	// 	'link'=>'/?pn=mypage.eval.list',
	// 	'hit'=>($pn == 'mypage.eval.list'?true:false)
	// ),
	// '적립금'=>array(
	// 	'link'=>'/?pn=mypage.point.list',
	// 	'hit'=>($pn == 'mypage.point.list'?true:false)
	// ),
	// '쿠폰'=>array(
	// 	'link'=>'/?pn=mypage.coupon.list',
	// 	'hit'=>($pn == 'mypage.coupon.list'?true:false)
	// ),
	// '로그인기록'=>array(
	// 	'link'=>'/?pn=mypage.login.log',
	// 	'hit'=>($pn == 'mypage.login.log'?true:false)
	// )
);
?>
<!-- ◆공통탭메뉴 -->
<div class="c_tab_box">
	<h2>마이페이지</h2>
	<!-- 활성화시 li에 hit 클래스 추가 -->
	<ul>
		<?php foreach($TopNavArr as $tn_k=>$tn_v) { ?>
			<li<?php echo ($tn_v['hit'] === true?' class="hit"':null); ?>><a href="<?php echo $tn_v['link']; ?>" class="mh_go"><?php echo $tn_k; ?></a></li>
		<?php } ?>
	</ul>
</div>
<!-- / 공통탭메뉴 -->