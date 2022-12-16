<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

		<!-- ◆게시판 보기 (공통) -->
		<div class="c_board_view">
			<!-- 리스트 제어 -->
			<div class="c_list_ctrl">
				<div class="tit_box">
					<!-- 게시판명 -->
					<span class="tit"><?php echo $boardInfo['bi_name'] ?></span>
				</div>
			</div>

			<!-- 글제목 -->
			<div class="view_tit"><!-- <span class="ctg_tag">[카테고리]</span> -->
				<!-- KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정-->
				<?php if( $boardViewData['categoryUse'] === true && $boardViewData['category'] && $boardInfo['bi_category'] != ""){?>
					[<?php echo $boardViewData['category']; ?>]
				<?php }?>
				<?php echo $boardViewData['title']; ?>
			</div>

			<!-- 글 기본정보 -->
			<div class="view_info">
				<div class="txt_box">
					<?php if( $boardViewData['writerView'] === true) {   ?>
					<span class="txt">작성자 : <strong><?php echo $boardViewData['writer'];?></strong></span>
					<?php } ?>
					<span class="txt">작성일 : <strong><?php echo $boardViewData['rdate'] ?></strong></span>
					<span class="txt">조회수 : <strong><?php echo $boardViewData['hit']?></strong></span>
				</div>

			<?php if( $boardViewData['optionDateUse'] === true) { ?>
				<!-- 태그표기 / 사용하지 않을 경우에는 숨김 -->
				<?php if( $boardViewData['eventing'] === true) { ?>
				<span class="c_tag h22 red line">이벤트 진행</span>
				<?php }else{ ?>
				<span class="c_tag h22 light line">이벤트 종료</span>
				<?php } ?>
			<?php } ?>
			</div>

			<!-- 글 내용 -->
			<div class="conts_box editor">
				<?php
					// -- 본문에 노출될 이미지가 있다면 노출
					if( $boardViewData['viewImagesUrl'] !== ''){ echo '<img src="'.$boardViewData['viewImagesUrl'].'" alt="" />'; }
					echo $boardViewData['content'];
				?>
			</div>

			<?php if( $boardViewData['replyMode'] === true) { ?>
			<!-- 관리자 답변 -->
			<div class="answer">
				<div class="tit"><?php echo $boardViewData['replyTitle']; ?></div>
				<div class="conts">
				<?php echo $boardViewData['replyContent']; ?>
				</div>
			</div>
			<?php } ?>

			<?php if( $boardViewData['fileUploadUse'] === true && $boardViewData['filesLink'] != ''){ ?>
			<!-- 첨부파일 -->
			<div class="file_down">
				<div class="tit">첨부파일</div>
				<div class="link_box">
				<?php echo $boardViewData['filesLink']; ?>
				</div>
			</div>
			<?php } ?>


			<?php
				// 게시글 댓글을 사용한다면
				if( $boardViewData['commentUse'] === true ){
					echo '<div class="comment-reply-box">';
					include_once OD_PROGRAM_ROOT.'/board.view.comment.php';
					echo '</div>';
				}

			?>
			<?php if( $boardViewData['prevIs'] === true || $boardViewData['nextIs'] === true){ ?>
			<!-- 이전글,다음글 -->
			<div class="nextprev">
				<ul>
					<li class="prev">
						<div class="tit_box"><span class="tit if_prev">이전글</span></div>
						<?php if( $boardViewData['prevIs'] === true){ ?>
						<div class="txt"><a href="<?php echo $boardViewData['prevLink']; ?>" class="link<?php echo $boardViewData['prevSecretEvtClass'] ?>" data-mode="view" data-uid="<?php echo $boardViewData['prevUid']; ?>"><?php  echo $boardViewData['prevTitle']; ?>
						<?php if($boardViewData['prevSecretIcon'] === true) {?>
						<span class="icon"><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_secret.gif" alt="비밀글"></span>
						 <?php   } ?>
						</a></div>
						<span class="date"><?php echo $boardViewData['prevRdate']; ?></span>
						<?php }else{ ?>
						<!-- 이전글 없을때 `이전글이 없습니다` 문구 노출 -->
						<div class="txt"><?php echo $boardViewData['prevTitle'];?></div>
						<?php } ?>

					</li>
					<li class="next">
						<div class="tit_box"><span class="tit if_next">다음글</span></div>
						<?php if( $boardViewData['nextIs'] === true){ ?>
						<div class="txt"><a href="<?php echo $boardViewData['nextLink']; ?>" class="link<?php echo $boardViewData['nextSecretEvtClass'] ?>" data-mode="view" data-uid="<?php echo $boardViewData['nextUid']; ?>"><?php  echo $boardViewData['nextTitle']; ?>
						<?php if($boardViewData['nextSecretIcon'] === true) {?>
						<span class="icon"><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_secret.gif" alt="비밀글"></span>
						 <?php   } ?>
						</a></div>
						<span class="date"><?php echo $boardViewData['nextRdate']; ?></span>
						<?php }else{ ?>
						<!-- 다음글 없을때 `다음글이 없습니다` 문구 노출 -->
						<div class="txt"><?php echo $boardViewData['nextTitle'];?></div>
						<?php } ?>
					</li>
				</ul>
			</div>
			<?php } ?>
		</div>
		<!-- / 게시판 보기 (공통) -->
