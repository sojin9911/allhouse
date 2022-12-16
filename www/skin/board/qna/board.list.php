<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<?php // $SkinData['skin_url']; -- 이미지 앞에 ?>

<!-- ◆게시판 목록 (공통) -->
<div class="c_board_list">

	<!-- 리스트 제어 -->
	<div class="c_list_ctrl">
		<div class="tit_box">
			<!-- 게시판명 -->
			<span class="tit"><?php echo $boardInfo['bi_name']; ?></span>
			<!-- 게시판 목록 수 -->
			<div class="total">TOTAL <strong><?php echo number_format($TotalCount); ?></strong></div>
		</div>
		<form name="boardSearch">
			<input type="hidden" name="pn" value="board.list">
			<input type="hidden" name="_menu" value="<?php echo $_menu ?>">
			<div class="ctrl_right">

				<!-- KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정-->
				<?php if($boardInfo['bi_category_use']=='Y'&&$boardInfo['bi_category']){ ?>
				<div class="search">
					<?php echo _InputSelect( "b_category" , array_values($_categoryload), $b_category,"", array_values($_categoryload) ,"카테고리선택"); ?>
				</div>

				<script>
					// -- 카테고리 검색
					$(document).on('change','[name="b_category"]',function(){
						if( $(this).val() == '' || $(this).val() == undefined){ location.href='/?pn=board.list&_menu=<?php echo $_menu; ?>'; }
						else{ location.href='/?pn=board.list&_menu=<?php echo $_menu; ?>&b_category='+$(this).val(); }
					});
				</script>
				<?php } ?>

				<select name="searchMode">
					<option value="tc" <?=$searchMode == '' || $searchMode == 'tc' ? 'selected' : null?>>제목 + 내용</option>
					<option value="t" <?=$searchMode == 't' ? 'selected' : null ?>>제목</option>
					<option value="c" <?=$searchMode == 'c' ? 'selected' : null ?>>내용</option>
				</select>
				<div class="search">
					<input type="text" name="searchWord" value="<?php echo $searchWord; ?>" class="input_search" placeholder="검색어를 입력해주세요.">
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
	<table>
		<colgroup>
			<col width="80"/><col width="95"/><col width="*"/><col width="120"/><col width="100"/><col width="80"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col">번호</th>
				<th scope="col">답변상태</th>
				<th scope="col">제목</th>
				<th scope="col">작성자</th>
				<th scope="col">작성일</th>
				<th scope="col">조회수</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($listPost as $k=>$v) {  ?>
			<!-- 공지사항일경우에 tr에 if_notice 클래스 추가 -->
			<tr class="<?php echo $v['trClass']; ?>">
				<td class="num"><?php echo $v['num']; ?></td>
				<td class="state">
					<?php if( $v['replayStatus'] == 'Y'){?>
					<span class="c_tag h22 red line">답변완료</span>
					<?php }else if( $v['replayStatus'] == 'R') { ?>
					<span class="c_tag h22 light line">답변대기</span>
					<?php }else{ ?>
					<span class="c_tag h22 black line">공지사항</span>
					<?php } ?>
				</td>
				<td class="tit">
					<div class="tit_box">
						<a href="<?php echo $v['postUrl']; ?>" class="upper_link<?php echo $v['secretEvtClass'];?>" data-uid="<?php echo $v['uid'] ?>" data-mode="view" title="<?php echo $v['title']; ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""></a>
						<!-- 아이콘 -->
						<span class="icon">
							<?php if( $v['iconNew'] === true){ ?><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_new.gif" alt="새글"><?php } ?>
							<?php if( $v['iconReply'] === true){ ?><span class="ic_reply"><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_reply.gif" alt="댓글"><?php echo $v['talkCnt'] ?></span><?php } ?>
							<?php if( $v['iconSecret'] === true){ ?><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_secret.gif" alt="비밀글"><?php } ?>
							<?php if( $v['iconPhoto'] === true){ ?><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_photo.gif" alt="사진첨부"><?php } ?>
							<?php if( $v['iconFile'] === true){ ?><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_file.gif" alt="첨부파일"><?php } ?>
						</span>
						<span class="tt">
							<!-- KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정-->
							<?php if($boardInfo['bi_category_use'] == 'Y' && $boardInfo['bi_category'] != "" && $v['category']){ ?>
								[<?php echo $v['category'];?>]
							<?php } ?>
							<?php echo $v['title']; ?>
						</span>
					</div>
				</td>
				<td class="id"><?php echo $v['writer'] ?></td>
				<td class="date"><?php echo $v['rdate']; ?></td>
				<td class="num"><?php echo $v['hit'];?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php } ?>
</div>
