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
<!-- ******************************************
     공통페이지 상단(공통)
  -- ****************************************** -->
<div class="c_page_tit js if_open"><!-- 열리면 if_open / 메뉴없으면 if_nomenu -->
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1);return false;" class="btn_back" title="뒤로"></a>
		<div class="tit">커뮤니티</div>
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
