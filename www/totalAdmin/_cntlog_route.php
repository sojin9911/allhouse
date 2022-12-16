<?php

	include_once('wrap.header.php');
	$pass_menu = ($pass_menu ? $pass_menu : 'route'); // 출력모드


	$_addons_menu = array(
		"접속경로별" => "route", 
		"접속키워드별" => "keyword", 
//		"상세접속" => "detail", 
	);
?>	


<!-- 탭메뉴 -->
<div class="c_tab">
	<ul>
		<?php 
			foreach($_addons_menu as $k=>$v){ 
				echo '<li ' . ($pass_menu == $v ? ' class="hit"':null) . '><a href="_cntlog_route.php?pass_menu='. $v .'" class="btn"><strong>'. $k .'</strong></a></li>';
			}
		?> 
	</ul>
</div>
<!-- / 탭메뉴 -->


<!-- 내부페이지 탭메뉴 -->
<div class="new_deny_tab">
	<div class="tab_box">
		<ul>
		<?php foreach($_addons_menu as $k=>$v){ ?> 

		<?php } ?>
		</ul>
	</div>
</div>
<!-- / 내부페이지 탭메뉴 -->


<?php

	# 메뉴형태 => 파일명 
	if($pass_menu) {
		include_once("_cntlog_route." . $pass_menu . ".php");
	}



	include_once('wrap.footer.php');

?>