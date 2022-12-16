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
<!-- ******************************************
     공통페이지 상단(공통)
  -- ****************************************** -->
<div class="c_page_tit if_open js"><!-- 열리면 if_open / 메뉴없으면 if_nomenu -->
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1);return false;" class="btn_back" title="뒤로"></a>
		<div class="tit">고객센터</div>
		<a href="#none" class="btn_ctrl js" title="메뉴 열고닫기"></a><!-- (없으면숨김) -->
	</div>

	<!-- 메뉴열기 (없으면숨김) -->
	<div class="nav_box">
		<div class="inner">
			<!-- li 3개 채워서 ul반복 -->
			<ul>
			<?php
			$maxCnt = 3; // 몇개씩 반복
			$forCnt = 0;
			$padCnt = (count($TopNavArr)%$maxCnt) != 0 ? $maxCnt - (count($TopNavArr)%$maxCnt) : 0;
			foreach($TopNavArr as $k=>$v) {
				if($forCnt != 0 && ($forCnt%3) == 0){ echo "</ul><ul>"; }
				$forCnt ++;
			?>
				<li class="<?php echo $v['hit'] === true ? 'hit':null?>"><a href="<?php echo $v['link'] ?>" class="btn"><?php echo $k ?></a></li>
			<?php } echo $padCnt > 0 ? implode(array_fill(0,$padCnt,"<li></li>")) : null; // 남은 공간만큼 li로 채우기 ?>
			</ul>
		</div>
	</div>
</div>
<!-- / 서브 상단(공통) -->

<script>
	$(document).on('click','.js.btn_ctrl',function(){
		var chkOpen = $('.js.c_page_tit').hasClass('if_open');
		var chkNomenu = $('.js.c_page_tit').hasClass('if_nomenu');
		if( chkNomenu == true){ return false; }
		if( chkOpen == true){ $('.js.c_page_tit').removeClass('if_open'); }
		else{ $('.js.c_page_tit').addClass('if_open'); }
	});
</script>
