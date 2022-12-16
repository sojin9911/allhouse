<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- 댓글 -->
<div class="reply_box">
	<div class="top_tit">
		<div class="tit">댓글쓰기</div>
		<div class="sub_txt">본 게시물의 취지에 맞지않는 글은 예고없이 삭제 및 수정될 수 있습니다.</div>
	</div>
	<div class="reply_form">
		<form name="boardComment" onsubmit="return false;">
			<input type="hidden" name="_boardSkin" value="<?php echo $boardInfo['_board_skin']; // 스킨명 ?>">
			<input type="hidden" name="_buid" value="<?php echo $_buid; // 스킨명 ?>">
			<input type="hidden" name="_menu" value="<?php echo $_menu; // 스킨고유아이디 ?>">
			<input type="hidden" name="_auth" value="<?php echo $commentData['writeAuthType']; // 스킨고유아이디 ?>">
			<textarea cols="" rows="" name="_content" maxlength="<?php echo $varCommentWriteLen; ?>" class="textarea_design" placeholder="<?php echo $commentData['placeholder']; ?>" <?php echo $commentData['writeAttr'] ;?>></textarea>
			<input type="submit" name="" class="btn_ok submit_comment" value="등록" />
		</form>
	</div>
	
	<?php if( count($listComment) > 0) {  ?>
	<!-- 댓글리스트 / 댓글 없을땐 div 숨김 -->
	<div class="reply_list">
		<ul>
		<?php foreach($listComment as $k=>$v) {?>
			<li>
				<span class="name"><?php echo $v['writer']; ?></span>
				<span class="date"><?php echo $v['rdate']?></span> 
				<?php if( $v['deleteAuth'] === true) { ?> 
				<!-- 댓글삭제버튼/ 내글일때만 노출 -->
				<a href="#none" onclick="return false;" class="btn_delete delete-comment" data-uid="<?php echo $v['uid']  ?>" title="댓글삭제"></a>
				<?php } ?>
				<div class="conts"><?php echo $v['content'];?></div>
			</li>
		<?php } ?>
		</ul>
	</div>
	<?php } ?>

</div>