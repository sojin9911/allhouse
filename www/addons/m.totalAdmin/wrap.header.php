<?php

	include dirname(__FILE__)."/inc.header.php";

?>
<body>
<div class="wrap ">


<?php
	// 슬라이드 메뉴 부분
	include dirname(__FILE__)."/_slide.php";
?>

	<iframe name="common_frame" src="about:blank" style="display:none;width:0;height:0;"></iframe>


	<!-- ● 헤더타이틀 영역 -->
	<div class="header">
		<!-- 왼쪽공간 -->
		<span class="left_box">
			<a href="#none" onclick="sitemap_ctl();" title="슬라이드메뉴보기" class="btn_slide"></a>
		</span>
		<!-- 가운데공간 메인으로링크 -->
		<div class="center_box">
			<a href="_main.php" class="txt_box"><strong><?=$siteInfo['s_adshop']?></strong></a>
		</div>
		<!-- 오른쪽공간 -->
		<span class="right_box">
			<a href="/" target="_blank" title="내홈페이지" class="btn_myhome"></a>
		</span>
	</div>
	<!-- / 헤더타이틀 영역 -->




<?php

?>
	<!-- ●●●●● 레이아웃 상단 -->
	<div class="common_pages_top">

		<div class="this_page_name">
			<a href="#none" onclick="history.go(-1);return false" class="btn_back" title="뒤로"><span class="shape"></span></a>
			<div class="txt"><?=$app_current_page_name?></div>



<? if(preg_match("/_order./i" , $CURR_FILENAME)) : ?>
			<a href="#none" onclick="view_submenu();return false" class="btn_openmenu" title="메뉴열기"><span class="shape"></span></a>
			<div class="open_menu" >
				<ul>
					<!-- 해당메뉴일때 hit -->
					<li><a href="_order.list.php" class="menu <?=($currleft_r['m2_link'] == "/totalAdmin/_order.list.php" ? "hit" : "")?>">주문관리</a></li>
					<li><a href="_order.list.php?style=b" class="menu <?=($currleft_r['m2_link'] == "/totalAdmin/_order.list.php?style=b" ? "hit" : "")?>">무통장주문관리</a></li>
				</ul>
			</div>
<script>
	// --- 슬라이드 서브메뉴 열기 ---
	function view_submenu() {
		patt = /block/g;
		if(patt.test($(".open_menu").css("display"))) {
			$(".open_menu").slideUp(300);
			$('.this_page_name').removeClass('if_open_menu');
		}
		else {
			$(".open_menu").slideDown(300);
			$('.this_page_name').addClass('if_open_menu');
		}
	}
</script>
<?endif;?>


		</div>		

	</div>
	<!-- / 레이아웃 상단 -->