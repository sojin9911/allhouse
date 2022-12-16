<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// -- 커뮤니티로 적용된 게시판을 가져온다.
$bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_view_type = 'community' and bi_view = 'Y' order by bi_view_idx asc ");

// 탑네비 메뉴 리스트
$TopNavArr = $normalMenu =  $boardMenu  = array();

// -- 게시판메뉴
foreach($bbsList as $k=>$v) {
	$chkHit = preg_match("/(board.)/",$pn) == true && $_menu == $v['bi_uid'] ? true : false;
	$boardMenu[$v['bi_name']] = array(
		'link'=>'/?pn=board.list&_menu='.$v['bi_uid'],
		'hit'=> $chkHit
	);
}

// -- 일반메뉴 첫번째
$normalMenu[0] = array(
	'상품후기'=>array(
		'link'=>'/?pn=service.eval.list',
		'hit'=>($pn == 'service.eval.list'?true:false)
	),
	'상품문의'=>array(
		'link'=>'/?pn=service.qna.list',
		'hit'=>($pn == 'service.qna.list'?true:false)
	)
);

// -- 일반메뉴 두번째
$normalMenu[1] = array(
	'제휴문의'=>array(
		'link'=>'/?pn=service.partner.form',
		'hit'=>($pn == 'service.partner.form'?true:false)
	),
);

// -- 일반메뉴 세번째
$normalMenu[2] = array(
	'출석체크'=>array(
		'link'=>'/?pn=promotion.attend',
		'hit'=>($pn == 'promotion.attend'?true:false)
	),
);


// -- 순서조절 하여 추가
$TopNavArr = array_merge($normalMenu[0] , $boardMenu , $normalMenu[1], $normalMenu[2]);
?>

<!-- ◆공통탭메뉴 -->
<div class="c_tab_box">
	<ul>
		<?php foreach($TopNavArr as $k=>$v) {?>
		<!-- 활성화시 li에 hit 클래스 추가 -->
		<li class="<?php echo $v['hit'] === true ? 'hit':null?>"><a href="<?php echo $v['link'] ?>" class="btn"><?php echo $k ?></a></li>
		<?php } ?>
	</ul>
</div>
<!-- / 공통탭메뉴 -->
