<?php
	if(sizeof($res) < 1 ){
		echo '<div class="c_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>';

	}else{
?>
	<table>
		<colgroup>
			<col width="50"><col width="83"><col width="*"><col width="120"><col width="83"><col width="34">
		</colgroup>
		<tbody>
			<!-- 클릭시 board_box열림 / if_open 클래스 추가 -->
			<?php
			foreach( $res as $k=>$v ){
				unset($eval_btn,$reply_content);

				$num = $TotalCount - $count - $k;

				$eval_content_uid = 'view_'.$v['pt_uid'];
				$eval_point = $v['pt_eval_point'];
				$eval_title = stripslashes(htmlspecialchars($v['pt_title']));
				$eval_id = LastCut2($v['pt_inid'], 3);
				$eval_rdate = date('Y-m-d',strtotime($v['pt_rdate']));
				$eval_content = nl2br(stripslashes(htmlspecialchars($v['pt_content'])));
				if($v['pt_inid'] == get_userid())	$eval_btn = '<div><a href="#none" onclick="eval_del(\''.$v['pt_uid'].'\'); return false;" class="c_btn h22 line">삭제</a></div>';
				$eval_img = $v['pt_img'] ? '<div class="photo"><img src="'. get_img_src($v['pt_img'], IMG_DIR_PRODUCT).'" alt=""></div>' : '';

				// 리플 추출
				$reply_r = _MQ_assoc("select * from smart_product_talk where pt_depth=2 and pt_relation = '".$v['pt_uid']."'");
				if(count($reply_r) <= 0) $reply_r = array();
				foreach($reply_r as $kk=>$vv) {

					$reply_content .= '
							<div class="reply">
								<span class="admin">
									<span class="name">'. $vv['pt_writer'] .'</span><span class="date">'. date('Y-m-d',strtotime($vv['pt_rdate'])) .'</span>
									<!-- <a href="" class="btn_delete" title="댓글삭제"></a> -->
								</span>
								'. nl2br(stripslashes(htmlspecialchars($vv['pt_content']))) .'
							</div>
					';

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
					<tr class="eval_box_area" id="<?php echo $eval_content_uid; ?>" data-hit="false">
						<td class="num"><?php echo $num; ?></td>
						<!-- 별점 -->
						<td><span class="mark"><span class="star" style="width:<?php echo $eval_point; ?>%"></span></span></td>
						<!-- 타이틀 -->
						<td class="title">
							<div class="posting">
								<a href="#none" onclick="eval_show('<?php echo $eval_content_uid; ?>'); return false;" class="upper_link" title="<?php echo $eval_title; ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif" alt=""></a>
								<!-- 아이콘 -->
								<span class="icon">
									<?php echo $new_icon; ?>
								</span>
								<span class="tit"><?php echo $eval_title; ?></span>
							</div>
						</td>
						<td class="name"><?php echo $eval_id; ?></td>
						<td><span class="date"><?php echo $eval_rdate; ?></span></td>
						<!-- 클릭 후 내용닫기로 타이틀 변경 -->
						<td><a href="#none" onclick="eval_show('<?php echo $eval_content_uid; ?>'); return false;" title="내용열기" class="arrow"></a></td>
					</tr>
					<tr class="board_box">
						<td colspan="6">
							<!-- 타이틀 -->
							<span class="title"><?php echo $eval_title; ?></span>
							<!-- 첨부이미지 -->
							<?php echo $eval_img; ?>
							<!-- 내용 -->
							<?php echo $eval_content; ?>
							<!-- 댓글 -->
							<?php echo $reply_content; ?>
							<!-- 삭제버튼 -->
							<?php echo $eval_btn; ?>
						</td>
					</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php
		// 별도의 페이지 네이트 사용
		$for_start_num = $Page <= 10 || $listpg <= 5 ? 1 : (($Page - $listpg) < 5 ? $Page-9 : $listpg-5);
		$for_end_num = $Page < ($for_start_num + 9) ? $Page : ($for_start_num + 9) ;
		$first	= "1";
		$prev		= $listpg > 1 ? $listpg-1 : 1;
		$next		= $listpg < $Page ? $listpg+1 : $Page;
		$last		= $Page;
	?>

	<!-- ◇상세게시판 페이지네이트 -->
	<div class="c_pagi_view">
		<span class="lineup">
			<span class="nextprev">
				<!-- 클릭할페이지가 생기면 클래스명추가 click -->
				<span class="btn<?php echo ($listpg == $first ? '' : ' click');?>">
					<span title="처음페이지" class="no"><span class="icon ic_first"></span></span>
					<a href="#none" onclick="eval_view(<?php echo $first; ?>); return false;" title="처음페이지" class="ok"><span class="icon ic_first"></span></a>
				</span>
				<span class="btn<?php echo ($listpg == $prev ? '' : ' click');?>">
					<span title="이전페이지" class="no"><span class="icon ic_prev"></span></span>
					<a href="#none" onclick="eval_view(<?php echo $prev; ?>); return false;" title="이전페이지" class="ok"><span class="icon ic_prev"></span></a>
				</span>
			</span>

			<span class="number">
				<?php
				for($ii=$for_start_num;$ii<=$for_end_num;$ii++) {
					if($ii != $listpg)	echo '<a href="#none" onclick="eval_view('.$ii.')" class="">'. $ii .'</a>';
					else echo '<a href="#none" class="hit">'. $ii .'</a>';
				}
				?>
			</span>

			<!-- 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
			<span class="nextprev">
				<!-- 클릭할페이지가 생기면 클래스명추가 click -->
				<span class="btn<?php echo ($listpg == $next ? '' : ' click');?>">
					<span title="다음페이지" class="no"><span class="icon ic_next"></span></span>
					<a href="#none" onclick="eval_view(<?php echo $next; ?>); return false;" title="다음페이지" class="ok"><span class="icon ic_next"></span></a>
				</span>
				<span class="btn<?php echo ($listpg == $last ? '' : ' click');?>">
					<span title="마지막페이지" class="no"><span class="icon ic_last"></span></span>
					<a href="#none" onclick="eval_view(<?php echo $last; ?>); return false;" title="마지막페이지" class="ok"><span class="icon ic_last"></span></a>
				</span>
			</span>
		</span>
	</div>
	<!-- ◇상세게시판 페이지네이트 -->


<?php } ?>