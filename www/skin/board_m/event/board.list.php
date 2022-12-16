<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆게시판 목록 (이벤트) -->
<div class="c_board_list">

	<div class="c_list_ctrl">
		<div class="tit_box">
			<!-- <span class="tit"><?php echo $page_title; ?></span> -->
			<div class="total">TOTAL <strong><?php echo number_format($TotalCount); ?></strong></div>
		</div>
		<form name="boardSearch">
			<input type="hidden" name="pn" value="board.list">
			<input type="hidden" name="_menu" value="<?php echo $_menu ?>">
			<div class="ctrl_right">

				<!-- KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정-->
				<?php if($boardInfo['bi_category_use']=='Y'&&$boardInfo['bi_category']){ ?>
				<div class="select" style="margin-right:5px;">
					<?php echo _InputSelect( "b_category" , array_values($_categoryload), $b_category,"", array_values($_categoryload) ,"카테고리"); ?>
				</div>

				<script>
					// -- 카테고리 검색
					$(document).on('change','[name="b_category"]',function(){
						if( $(this).val() == '' || $(this).val() == undefined){ location.href='/?pn=board.list&_menu=<?php echo $_menu; ?>'; }
						else{ location.href='/?pn=board.list&_menu=<?php echo $_menu; ?>&b_category='+$(this).val(); }
					});
				</script>
				<?php } ?>

				<div class="select">
					<select name="searchMode">
						<option value="tc" <?=$searchMode == '' || $searchMode == 'tc' ? 'selected' : null?>>제목 + 내용</option>
						<option value="t" <?=$searchMode == 't' ? 'selected' : null ?>>제목</option>
						<option value="c" <?=$searchMode == 'c' ? 'selected' : null ?>>내용</option>
					</select>
					<span class="shape"></span>
				</div>
				<div class="search">
					<input type="text" name="searchWord" value="<?php echo $searchWord; ?>" class="input_search"  placeholder="검색어를 입력해주세요.">
					<input type="submit" name="" value="" class="btn_search" title="검색">
				</div>
				<?php if( in_array($searchMode,array('t','c','tc')) == true) { ?>
				<!-- 검색한 후 노출 / 검색 전 숨김 -->
				<a href="/?pn=board.list&_menu=<?php echo $_menu; ?>" class="all_btn">전체목록</a>
				<?php } ?>
				<?php if( $boardAuthChk['write'] === true) { ?>
				<a href="/?pn=board.form&_mode=add&_menu=<?php echo $_menu; ?>&_PVSC=<?php echo $_PVSC; ?>" class="write_btn">글쓰기</a>
				<?php } ?>
			</div>
		</form>
	</div>
	<script>
		// -- 게시물 검색 :: 공통
		$(document).on('submit','form[name="boardSearch"]',function(){
			var sw = $(this).find('[name="searchWord"]').val();
			if( sw.replace(/\s/gi,'') == ''){ alert("검색어를 입력해 주세요."); $(this).find('[name="searchWord"]').focus(); return false; }
			return true;
		});
	</script>

	<?php if( count($listPost) < 1) { ?>

	<!-- 내용 없을때 -->
	<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
	<?php }else{ ?>

	<div class="event_list">
		<ul>
		<?php foreach($listPost as $k=>$v) {  ?>
			<!-- 종료된 경우 li에 if_end 클래스 추가 -->
			<li class="<?php echo $v['trClass']; ?><?php echo $v['eventClose'] === true ? ' if_end':null;  ?>">
				<div class="event_box">
					<a href="<?php echo $v['postUrl']; ?>" class="upper_link<?php echo $v['secretEvtClass'];?>" data-uid="<?php echo $v['uid'] ?>" data-mode="view" title="<?php echo $v['title']; ?>"></a>
					<!-- 이벤트 썸네일  / 모바일 없으면 PC불러옴 -->
					<div class="thumb">
						<div class="event_bg"></div>
						<?php if( $v['eventClose'] === true ) { ?>
						<span class="event_txt">종료된 이벤트입니다.</span>
						<?php } ?>
						<div class="real"><?php echo $v['thumb'] ?></div>
						<div class="fake"><img src="<?php echo OD_SITE_SKIN_URL; ?>/images/c_img/fake_event.gif" alt="" /></div>
					</div>
					<div class="tit_info">
						<div class="tit_box">
							<!-- 아이콘 -->
							<span class="icon">
								<?php if($v['iconSecret'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_secret.png" alt="비밀글"> <?php } ?>
								<?php if($v['iconPhoto'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_photo.png" alt="사진첨부"> <?php } ?>
								<?php if($v['iconFile'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_file.png" alt="첨부파일"> <?php } ?>
								<?php if($v['iconNew'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_new.png" alt="새글"> <?php } ?>
								<?php  if($v['iconReply'] === true) { ?> <span class="ic_reply"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_reply.png" alt="댓글"><?php echo $v['talkCnt']; ?></span><?php } ?>
							</span>
							<span class="tt">
								<!-- KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정-->
								<?php if($boardInfo['bi_category_use'] == 'Y' && $boardInfo['bi_category'] != "" && $v['category']){ ?>
									[<?php echo $v['category'];?>]
								<?php } ?>
								<?php echo $v['title']; ?>
							</span>
						</div>
						<div class="sub_txt"><?php echo $v['content'] ?></div>
					</div>
					<!-- 디데이 / 기간 -->
					<div class="date_info">
						<!-- 이벤트 기간일 경우 if_day 클래스 추가 및 'D-DAY' 문구 변경 / 마감일 경우 if_close 클래스 추가 및 '마감' 문구 변경 -->
						<span class="d_day<?php  echo $v['eventClose'] === true ? ' if_close': ($v['eventStatusVal'] == 'ing' ? ' if_day':null) ?>"><?php echo $v['eventDay'] ?></span>
						<span class="date"><?php echo $v['eventDate']; ?></span>
					</div>
				</div>
			</li>
		<?php } ?>
		</ul>
	</div>
	<?php } ?>

</div>