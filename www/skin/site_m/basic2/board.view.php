<?php 
$page_title = $boardInfo['bi_name']; // 게시판명
include_once($SkinData['skin_root'].'/'.$boardInfo['bi_view_type'].'.header.php'); // 상단 헤더 출력 
?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	
	<?php 
		echo $BoardSkinData; // -- 스킨데이터 호출 :: program/board.view.php 에서 호출 -- 

		// -- 목록,수정,삭제,글쓰기 사용 
		echo '<div class="c_btnbox">';
		if( $boardViewData['authModify'] === true || $boardViewData['authDelete'] === true) { 
			echo '	<ul>';
			if( $boardViewData['authModify'] === true){ echo '<li><a href="'.$boardViewData['modifyLink'].'" data-uid="'.$boardViewData['uid'].'" data-mode="modify" class="c_btn h40 dark evt-modify'.$boardViewData['authClass'].'">수정</a></li>'; }
			if( $boardViewData['authDelete'] === true){ echo '<li><a href="'.$boardViewData['deleteLink'].'" data-uid="'.$boardViewData['uid'].'" data-mode="delete" class="c_btn h40 dark evt-delete'.$boardViewData['authClass'].'">삭제</a></li>';}
			echo '	</ul>';
		}
		echo '	<ul>';
		echo '		<li><a href="/?'.($_PVSC?enc('d',$_PVSC):'pn=board.list&_menu='.$_menu).'" class="c_btn h40 light line">목록으로</a></li>';
		if( $boardViewData['authWrite'] === true) { echo '<li><a href="'.$boardViewData['writeLink'].'" class="c_btn h40 color">글쓰기</a></li>'; } 
		echo '	</ul>';

		// -- 답글쓰기
		if($boardViewData['authReply'] === true ) {
			echo '	<ul>';
			echo '		<li><a href="'.$boardViewData['replyLink'].'" class="c_btn h40 black evt-reply'.$boardViewData['replyClass'].'">'.$boardViewData['replyType'].'</a></li>';  
			echo '	</ul>';		
		}

		echo '</div>';
	?>


</div>
<!-- /공통페이지 섹션 -->


<?php include_once(OD_PROGRAM_ROOT.'/board.auth_pop.php'); // -- 비밀번호 팝업 --    ?>
