<?php 
$page_title = $boardInfo['bi_name']; // 게시판명
include_once($SkinData['skin_root'].'/'.$boardInfo['bi_view_type'].'.header.php'); // 상단 헤더 출력 
?>


<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">

	<?php 
		echo $BoardSkinData; // -- 스킨데이터 호출 :: program/board.list.php 에서 호출 -- 
	?>

	<!-- 페이지네이트 (상품목록 형) -->
	<div class="c_pagi">
		<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
	</div>
	<!-- / 페이지네이트 (상품목록 형) -->

</div>
<!-- /공통페이지 섹션 -->

<?php include_once(OD_PROGRAM_ROOT.'/board.auth_pop.php'); // -- 비밀번호 팝업 --   ?>

