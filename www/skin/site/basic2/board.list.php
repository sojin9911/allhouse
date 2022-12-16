
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix board_wrap">

		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<div class="title"><a href="<?php echo $boardHeaderData['viewTypeLink']; ?>" class="tit"><?php echo $boardHeaderData['viewTypeName'];?></a></div>
			<!-- 로케이션 -->
			<div class="c_location">
				<ul>
					<li>홈</li>
					<li><?php echo $boardHeaderData['viewTypeName'];?></li>
					<li><?php echo $boardInfo['bi_name'];?></li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->

		<?php 
			//include_once($SkinData['skin_root'].'/'.$boardInfo['bi_view_type'].'.header.php'); // -- 공통해더 -- 
			echo $BoardSkinData; // -- 스킨데이터 호출 :: program/board.list.php 에서 호출 -- 
		?>

		
		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
		<!-- / 페이지네이트 (상품목록 형) -->

	</div>
</div>
<!-- /공통페이지 섹션 -->

<?php include_once(OD_PROGRAM_ROOT.'/board.auth_pop.php'); // -- 비밀번호 팝업 --   ?>


