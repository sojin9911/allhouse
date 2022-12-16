<?php include_once('inc.header.php'); ?>
<!-- ●●●헤더(공통)-->
<div class="header">
	<!-- 사이트명 / 관리자메인 -->
	<div class="site_name">
		<a href="<?php echo OD_SUB_ADMIN_URL; ?>" class="btn">
			<?php echo $siteInfo['s_adshop']; ?>
			<strong>입점관리자</strong>
		</a>
	</div>
	<div class="right_btn">
		<ul>
			<!-- 관리자접속정보 -->
			<!-- li반복 -->
			<li class="li">
				<a href="#none" onclick="return false;" class="btn" style="cursor:default"><strong><?php echo $subAdmin['cp_name']; ?></strong> <span class="id"><?php echo $subAdmin['cp_id']; ?></span></a>
			</li>
			<?php if(AdminLoginCheck('value') === true) { // 통합관리자에 로그인 되있다면 통합관리자 접근 링크 생성 ?>
				<li class="li"><a href="/totalAdmin/_main.php" class="btn" target="_blank">통합관리자</a></li>
			<?php } ?>
			<li class="li"><a href="/" class="btn" target="_blank">홈페이지</a></li>
			<li class="li"><a href="logout.php" class="btn">로그아웃</a></li>
		</ul>
	</div>
</div>
<!-- /●●●헤더(공통)-->



<!-- ●●●네비(공통)-->
<div class="nav">
	<div class="layout_fix">
		<div class="nav_box if_sub_admin_menu">
			<ul>
				<?php foreach($subAdminArr as $k=>$v) { ?>
					<li<?php echo ($Find1Depth['link'] == $v['link']?' class="hit"':null); ?>><a href="<?php echo $v['link']; ?>" class="btn"><?php echo $v['name']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<!-- ●●●네비(공통)-->



<?php if($Find1Depth['link']) {  ?>
<!-- ●●●컨텐츠영역 / close_btn 클릭시 if_hide 클래스 추가 -->
<div class="container js-menu-container">
	<!-- 왼쪽메뉴 -->
	<div class="aside">
		<!-- 1차메뉴명 -->
		<div class="title_box"><?php echo $Find1Depth['name']; ?></div>
		<!-- 카테고리메뉴 -->
		<div class="ctg_box">
			<ul class="ul">
				<!-- li반복 -->
				<?php foreach($Find1Depth['sub'] as $k=>$v) { ?>
					<!-- 클릭시 if_open클래스 추가 -->
					<li class="li<?php echo ($Find2Depth['link'] == $v['link']?' if_open':null); ?> ">
						<div class="depth2_box"><a href="<?php echo $v['link']; ?>" class="tt"><?php echo $v['name']; ?></a></div>
						<!-- 3차카테고리 -->
						<div class="depth3_box">
							<ul>
								<!-- 활성화시 hit클래스 추가 -->
								<?php foreach($v['sub'] as $kk=>$vv) { ?>
									<li<?php echo ($Find3Depth['link'] == $vv['link']?' class="hit"':null); ?>><a href="<?php echo $vv['link']; ?>" class="btn"><?php echo $vv['name']; ?></a></li>
								<?php } ?>
							</ul>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<!-- 왼쪽메뉴 -->

	<!-- ●오른쪽컨텐츠 -->
	<div class="section">
		<div class="page_top">
			<!-- 클릭후 title 메뉴열기로 텍스트 변경 -->
			<a href="#none" onclick="return false;" class="close_btn js-menu-ctl" title="메뉴닫기"></a>
			<div class="tit"><?php echo ($Find3Depth['name']?$Find3Depth['name']:$Find2Depth['name']); ?></div>
			<span class="location"><?php echo implode(" > ", array($Find1Depth['name'], $Find2Depth['name'], $Find3Depth['name'])); ?></span>
		</div>
		<!-- 하단 컨텐츠 -->
<?php } ?>