<?php
	if(sizeof($res) < 1 ){
		echo '<div class="c_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>';

	}else{
?>
	<ul>
	<?php
		foreach( $res as $k=>$v ){
			unset($qna_btn,$reply_content);

			$num = $TotalCount - $k;

			$qna_content_uid = 'view_'.$v['pt_uid'];
			$qna_title = stripslashes(htmlspecialchars($v['pt_title']));
			$qna_id = LastCut2($v['pt_inid'], 3);
			$qna_rdate = date('Y-m-d',strtotime($v['pt_rdate']));
			$qna_content = nl2br(stripslashes(htmlspecialchars($v['pt_content'])));
			if($v['pt_inid'] == get_userid())	$qna_btn = '<li><a href="#none" onclick="qna_del(\''.$v['pt_uid'].'\'); return false; " class="c_btn h35 line">삭제</a></li>';
			$qna_img = $v['pt_img'] ? '<div class="img_box"><img src="'. get_img_src($v['pt_img'], IMG_DIR_PRODUCT).'" alt=""></div>' : '';

			// 리플 추출
			$reply_icon = '<span class="c_tag h22 light">답변대기</span>';
			$reply_r = _MQ_assoc("select * from smart_product_talk where pt_depth=2 and pt_relation = '".$v['pt_uid']."'");
			foreach($reply_r as $kk=>$vv) {

				$reply_content .= '
						<ul class="reply">
							<li>
								<span class="admin">답변내용</span>
								<span class="date">'. date('Y-m-d',strtotime($vv['pt_rdate'])) .'</span>
							</li>
							<li>'. nl2br(stripslashes(htmlspecialchars($vv['pt_content']))) .'</li>
						</ul>
				';

				$reply_icon = '<span class="c_tag h22 orange">답변완료</span>';

			}

			unset($new_icon);
			if(time()-strtotime($v['pt_rdate']) < 60*60*24*2){
				$new_icon .= '<img src="'. $SkinData['skin_url'] .'/images/c_img/ic_new.gif" alt="새글">';
			}
			$new_icon .= $v['pt_img'] ? '<img src="'. $SkinData['skin_url'] .'/images/c_img/ic_image.gif" alt="사진첨부">' : '';
			/* <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_secret.gif" alt="비밀글">
				<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_photo.gif" alt="사진첨부">
				<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_image.gif" alt="사진첨부">
				<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_new.gif" alt="새글"> */


		?>
				<li id="<?php echo $qna_content_uid; ?>" data-hit="false">
					<div class="posting">
						<a href="#none" onclick="qna_show('<?php echo $qna_content_uid; ?>'); return false;" class="upper_link" title="내용보기"></a>
						<div class="tag">
							<?php echo $reply_icon; ?>
						</div>
						<div class="tit_box">
							<!-- 아이콘 -->
							<span class="icon">
								<?php echo $new_icon; ?>
							</span>
							<span class="tit"><?php echo $qna_title; ?></span>
						</div>
						<span class="name"><?php echo $qna_id; ?></span>
					</div>
					<!-- 내용보기 팝업시 나타날 내용 -->
					<div class="popup_html" style="display:none;">
						<div class="conts_box">
							<ul>
								<li>
									<span class="name"><?php echo $qna_id; ?></span>
									<span class="date"><?php echo $qna_rdate; ?></span>
								</li>
								<li><div class="tit"><?php echo $qna_title; ?></div></li><!-- 제목 -->
								<li>
									<?php echo $qna_img; ?>
									<?php echo $qna_content; ?>
								</li>
							</ul>
							<!-- 관리자답변있을때만 나옴 -->
							<?php echo $reply_content; ?>
						</div>
						<div class="c_btnbox">
							<ul>
								<?php echo $qna_btn; ?>
								<li><a href="#none" class="c_btn h35 light close">닫기</a></li>
							</ul>
						</div>
					</div>
				</li>
		<?php } ?>
	</ul>


<?php } ?>


<?php
	// 별도의 페이지 네이트 사용
	$first	= "1";
	$prev		= $listpg > 1 ? $listpg-1 : 1;
	$next		= $listpg < $Page ? $listpg+1 : $Page;
	$last		= $Page;
?>
<!-- ◇상세게시판 페이지네이트 -->
<div class="c_pagi_view">

	<?php
		// 총 개수가 페이지 기본 설정 초과일 경우에만 페이지네이트 노출
		if( $TotalCount > $listmaxcount ) {
	?>
	<!-- 클릭가능한 페이지가 있으면 if_click -->
	<span class="pagi<?php echo ($listpg == $prev ? '' : ' if_click');?>"><a href="#none" onclick="qna_view(<?php echo $prev; ?>); return false;" class="prev " title="이전 페이지"></a></span>
	<span class="pagi<?php echo ($listpg == $next ? '' : ' if_click');?>"><a href="#none" onclick="qna_view(<?php echo $next; ?>); return false;" class="next" title="다음 페이지"></a></span>
	<?}?>

	<div class="btn_area"><a href="#none" onclick="qna_write_form_view(); return false;" class="c_btn h40 dark">상품문의 등록하기</a></div>
</div>
<!-- ◇상세게시판 페이지네이트 -->

