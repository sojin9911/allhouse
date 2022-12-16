<?php
	if(sizeof($res) < 1 ){
		echo '<div class="c_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>';

	}else{
?>
	<ul>
	<?php
		foreach( $res as $k=>$v ){
			unset($eval_btn,$reply_content);

			$num = $TotalCount - $k;

			$eval_content_uid = 'view_'.$v['pt_uid'];
			$eval_point = $v['pt_eval_point'];
			$eval_title = stripslashes(htmlspecialchars($v['pt_title']));
			$eval_id = LastCut2($v['pt_inid'], 3);
			$eval_rdate = date('Y-m-d',strtotime($v['pt_rdate']));
			$eval_content = nl2br(stripslashes(htmlspecialchars($v['pt_content'])));
			if($v['pt_inid'] == get_userid())	$eval_btn = '<li><a href="#none" onclick="eval_del(\''.$v['pt_uid'].'\'); return false; " class="c_btn h35 line">삭제</a></li>';
			$eval_img = $v['pt_img'] ? '<div class="img_box"><img src="'. get_img_src($v['pt_img'], IMG_DIR_PRODUCT).'" alt=""></div>' : '';

			// 리플 추출
			$reply_r = _MQ_assoc("select * from smart_product_talk where pt_depth=2 and pt_relation = '".$v['pt_uid']."'");
			if(count($reply_r) <= 0) $reply_r = array();
			foreach($reply_r as $kk=>$vv) {

				$reply_content .= '
						<ul class="reply">
							<li>
								<span class="admin">관리자 답변</span>
								<span class="date">'. date('Y-m-d',strtotime($vv['pt_rdate'])) .'</span>
							</li>
							<li>'. nl2br(stripslashes(htmlspecialchars($vv['pt_content']))) .'</li>
						</ul>
				';

			}

			unset($new_icon);
			if(time()-strtotime($v['pt_rdate']) < 60*60*24*2){
				$new_icon .= '<img src="'. $SkinData['skin_url'] .'/images/c_img/ic_new.png" alt="새글">';
			}
			$new_icon .= $v['pt_img'] ? '<img src="'. $SkinData['skin_url'] .'/images/c_img/ic_image.png" alt="사진첨부">' : '';
			/* <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_secret.gif" alt="비밀글">
				<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_photo.gif" alt="사진첨부">
				<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_image.gif" alt="사진첨부">
				<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_new.gif" alt="새글"> */

		?>
				<li id="<?php echo $eval_content_uid; ?>" data-hit="false">
					<div class="posting">
						<a href="#none" onclick="eval_show('<?php echo $eval_content_uid; ?>'); return false;" class="upper_link" title="내용보기"></a>
						<div class="tit_box">
							<!-- 아이콘 -->
							<span class="icon">
								<?php echo $new_icon; ?>
							</span>
							<span class="tit"><?php echo $eval_title; ?></span>
						</div>
						<div class="sub_box"><?php echo strip_tags($eval_content); ?></div>
						<div class="info_box">
							<span class="mark"><span class="star" style="width:<?php echo $eval_point; ?>%"></span></span>
							<span class="name"><?php echo $eval_id; ?></span>
						</div>
					</div>
					<!-- 내용보기 팝업시 나타날 내용 -->
					<div class="popup_html" style="display:none;">
						<div class="conts_box">
							<ul>
								<li>
									<span class="mark"><span class="star" style="width:<?php echo $eval_point; ?>%"></span></span>
									<span class="name"><?php echo $eval_id; ?></span>
									<span class="date"><?php echo $eval_rdate; ?></span>
								</li>
								<li><div class="tit"><?php echo $eval_title; ?></div></li><!-- 제목 -->
								<li>
									<?php echo $eval_img; ?>
									<?php echo $eval_content; ?>
								</li>
							</ul>
							<!-- 관리자답변있을때만 나옴 -->
							<?php echo $reply_content; ?>
						</div>
						<div class="c_btnbox">
							<ul>
								<?php echo $eval_btn; ?>
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
	<span class="pagi<?php echo ($listpg == $prev ? '' : ' if_click');?>"><a href="#none" onclick="eval_view(<?php echo $prev; ?>); return false;" class="prev " title="이전 페이지"></a></span>
	<span class="pagi<?php echo ($listpg == $next ? '' : ' if_click');?>"><a href="#none" onclick="eval_view(<?php echo $next; ?>); return false;" class="next" title="다음 페이지"></a></span>
	<?}?>

	<div class="btn_area"><a href="#none" onclick="eval_write_form_view(); return false;" class="c_btn h40 dark">상품후기 등록하기</a></div>
</div>
<!-- ◇상세게시판 페이지네이트 -->

