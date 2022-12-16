<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$agree_nomal_page -> 약관관련페이지에 출력 되는 일반페이지의 아이디 배열 => (/program/pages.view.php에서 지정)
	$page_content -> 페이지 제목, 내용정보 => (/program/pages.view.php에서 지정)
	$page_menu -> 페이지 메뉴정보 => (/program/pages.view.php에서 지정)
*/
// KAY :: 2022-08-02 :: 타이틀 변경 -> /pages.header.php 에서 지정
//$page_title = $page_content['title']; // 페이지 대표 타이틀
include_once($SkinData['skin_root'].'/pages.header.php'); // PC 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_user">
	
	<!-- ◆이용안내 / 에디터 들어감 -->
	<div class="c_user_box">
		<div class="editor">
			<?php echo $page_content['content']; ?>
		</div>
	</div>
</div>