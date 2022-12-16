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
<!-- ◆공통페이지 타이틀 -->
<div class="c_page_tit">
	<div class="title"><?php echo $page_title; ?></div>
</div>
<!-- / 공통페이지 타이틀 -->

<?php if(count($page_menu) > 1) { ?>
	<!-- ◆공통탭메뉴 -->
	<div class="c_tab_box">
		<ul>
			<!-- 활성화시 li에 hit 클래스 추가 -->
			<?php foreach($page_menu as $mk=>$mv) { ?>
				<li<?php echo ($mv['hit'] === true?' class="hit"':null); ?>><a href="<?php echo $mv['link']; ?>" class="btn"><?php echo $mv['title']; ?></a></li>
			<?php } ?>
		</ul>
	</div>
	<!-- / 공통탭메뉴 -->
<?php } ?>