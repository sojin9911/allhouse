<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$page_title -> 페이지 대표 타이틀 => (스킨/pages.view.php)
	$page_menu -> 페이지 메뉴정보 => (스킨/pages.view.php -> /program/pages.view.php에서 지정)
*/
// KAY :: 2022-08-02 :: 일반페이지 타이틀 변경
if($type=='agree'){
	// 이용안내 타이틀
	$page_title = '약관 및 정책';
}else{
	if($page_row['np_menu']=='default'){
		// 회사소개 타이틀
		$page_title = '회사소개';
	}else{
		// 단독메뉴일 경우 => 관리자에서 설정한 페이지명 노출
		$page_title = $page_row['np_title'];
	}
}
?>
<!-- ******************************************
     공통페이지 상단(공통)
  -- ****************************************** -->
<div class="c_page_tit<?php echo (count($page_menu) <= 0?' if_nomenu':' if_open'); ?> js_top_nav_wrap"><!-- 열리면 if_open / 메뉴없으면 if_nomenu -->
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
		<div class="tit"><?php echo $page_title; ?></div>
		<?php if(count($page_menu) > 1) { ?>
			<a href="#none" class="btn_ctrl js_top_nav_toggle" title="메뉴 열고닫기"></a><!-- (없으면숨김) -->
		<?php } ?>
	</div>

	<?php if(count($page_menu) > 1) { ?>
		<!-- 메뉴열기 (없으면숨김) -->
		<div class="nav_box">
			<div class="inner">
			<!-- li 3개 채워서 ul반복 -->
				<ul>
					<?php
					$TopNavNum = 0;
					foreach($page_menu as $tn_k=>$tn_v) {
						if($TopNavNum > 0 && $TopNavNum%3 === 0) {
							echo '</ul><ul>';
							$TopNavNum = 0;
						}
						$TopNavNum++;
					?>
						<li<?php echo ($tn_v['hit'] === true?' class="hit"':null); ?>><a href="<?php echo $tn_v['link']; ?>" class="btn"><?php echo $tn_v['title']; ?></a></li>
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