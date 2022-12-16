<?php 
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// -- 고객센터로 적용된 게시판을 가져온다. 
$bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_view_type = 'service' and bi_view = 'Y' order by bi_view_idx asc ");

// 탑네비 메뉴 리스트
$TopNavArr = $normalMenu =  $boardMenu  = array();

// -- 게시판메뉴
foreach($bbsList as $k=>$v) {
	$chkHit = preg_match("/(board.)/",$pn) == true && $_menu == $v['bi_uid'] ? true : false;
	$skinNameView = $skinNameViewVal === true ? '('.$v['bi_skin'].')' : null;
	$boardMenu[$v['bi_name'].$skinNameView] = array(
		'link'=>'/?pn=board.list&_menu='.$v['bi_uid'],
		'hit'=> $chkHit
	);
}

// -- 일반메뉴
$normalMenu = array(
	'자주 묻는 질문'=>array(
		'link'=>'/?pn=faq.list',
		'hit'=>($pn == 'faq.list'?true:false)
	),
// 내부패치 68번줄 kms 2019-11-05
//	'1:1 온라인 문의'=>array(
//		'link'=>'/?pn=mypage.inquiry.form',
//		'hit'=>false
//	)	
);
// -- 미확인 입금자 리스트
if($siteInfo['s_online_notice_use'] == 'Y'){
	$normalMenu['미확인 입금자'] = array(
		'link'=>'/?pn=service.deposit.list',
		'hit'=>($pn == 'service.deposit.list'?true:false)
	);	
}
$TopNavArr = array_merge($boardMenu, $normalMenu );

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
