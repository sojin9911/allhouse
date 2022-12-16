<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "상품후기<span class='pt_go_write'>글쓰기</span>";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board" style="padding:0;">
	<!-- ◆게시판 목록 (이벤트) -->
	<div class="c_board_list">
		<!-- 리스트 제어 -->
		<div class="c_list_ctrl for_change_btn">
			<form role="search" action="/" method="get">
				<input type="hidden" name="pn" value="<?php echo $pn; ?>">
				<input type="hidden" name="search_type" value="search_title,search_content">
				<div class="search">
					<input type="search" name="search_word" value="<?php echo $search_word; ?>" class="input_search" placeholder="검색 단어를 입력해 주세요">
					<input type="submit" value="검색" class="btn_search" title="검색">
				</div>
				<?php if(isset($search_word) && $search_word != '') { ?>
					<!-- 검색한 후 노출 / 검색 전 숨김 -->
					<a href="/?pn=<?php echo $pn; ?>" class="all_btn">전체목록</a>
				<?php } ?>
			</form>
		</div>

		<?php if(count($row) <= 0) { ?>
			<!-- 내용 없을때 -->
			<div class="c_none"><span class="gtxt">게시글이 존재하지 않습니다.</span></div>
		<?php } else { ?>
			<div class="board_review">
				<ul>
					<?php
					foreach($row as $k=>$v) {
						$_num = $TotalCount-$count-$k;
						// 후기에 등록딘 이미지 노출 없으면 상품이미지 노출 kms 2019-08-02
						$pro_img = $v['pt_img'] == "" ? get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT) : get_img_src($v['pt_img'], IMG_DIR_PRODUCT); // 후기이미지 없으면 상품이미지
						$is_wish = (is_login() ? _MQ_result("select count(*) from smart_product_wish where pw_pcode = '{$v['p_code']}' and pw_inid = '".get_userid()."' "):0); // 위시 여부
						$eval_point = get_eval_average($v['p_code']); // 평균평점
						$pro_link = '/?pn=product.view&pcode='.$v['p_code']; // 상품 링크
						$is_file = ($v['pt_img']?true:false); // 파일첨부 여부(포토후기)
						$is_new = ((time()-strtotime($v['pt_rdate'])<(60*60*24*5))?true:false);
						$talk_img = get_img_src($v['pt_img'], IMG_DIR_PRODUCT); // 첨부 이미지
						$talk_title = stripslashes(htmlspecialchars($v['pt_title'])); // 제목
						$talk_content = stripslashes(htmlspecialchars($v['pt_content'])); // 내용
						$star_persent = $v['pt_eval_point']; // 지급점수

						$reply_content = '';
						$reply_r = _MQ_assoc("select * from smart_product_talk as pt where pt_relation = '{$v['pt_uid']}' and pt_depth=2 order by pt_rdate asc");
						if(count($reply_r) <= 0) $reply_r = array();
						foreach($reply_r as $kk=>$vv) {
							if(!$vv['pt_content']) continue;
							$reply_content .= '
								<div class="reply">
									<span class="admin">
										<span class="name">'.$vv['pt_writer'].'</span><span class="date_num">'.date('Y-m-d', strtotime($vv['pt_rdate'])).'</span>
									</span>
									'.nl2br(stripslashes(htmlspecialchars($vv['pt_content']))).'
								</div>
							';
						}
						$del_btn = ($v['pt_inid'] == get_userid() ?'<a href="#none" onclick="eval_del('.$v['pt_uid'].'); return false;" class="c_btn h22 light line">삭제</a>':null);
					?>
						<li class="js_view" data-uid="<?php echo $v['pt_uid']; ?>" data-hit="false"><!-- 클릭하면 상세보기 열림 -->
							<div class="conts_box">
								<a href="#none" class="btn_open js_detail_btn" title="열기"></a>
								<ul>
									<li class="this_thumb">
										<div class="thumb_box">
											<a href="<?php echo $pro_link; ?>" class="thumb" title="<?php echo addslashes($v['p_name']); ?>" target="_blank">
												<img src="<?php echo $pro_img; ?>" alt="" />
											</a>
										</div>
									</li>
									<li>
										<span class="mark"><span class="star" style="width:<?php echo $star_persent; ?>%"></span></span>
										<div class="title">
											<div class="tit_box">
												<!-- 아이콘 -->
												<span class="icon">
													<?php if($is_file === true) { ?>
														<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_photo.png" alt="사진첨부">
													<?php } ?>
													<?php if($is_new === true) { ?>
														<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_new.png" alt="새글">
													<?php } ?>
												</span>
												<span class="tt"><?php echo $talk_title; ?></span>
											</div>
											<a href="<?php echo $pro_link; ?>" class="sub_txt" target="_blank">상품명 : <?php echo $v['p_name']; ?></a><!-- 상품으로 이동 -->
										</div>
										<div class="info">
											<div class="date"><?php echo date('Y-m-d', strtotime($v['pt_rdate'])); ?></div>
											<div class="t_hit js_eval_hit"><?php echo number_format($v['pt_hit']); ?></div>
										</div>
									</li>
								</ul>
							</div>

							<!-- 보기 클릭시 노출 -->
							<div class="view_box">
								<div class="view_tit"><?php echo $talk_title; ?></div>
								<div class="view_txt"><?php echo nl2br($talk_content); ?></div>
								<?php if($talk_img) { ?><div class="view_img"><img src="<?php echo $talk_img; ?>" alt="" /></div><?php } ?>
								<?php if(count($reply_r) > 0) { ?>
									<div class="reply_box">
										<?php echo $reply_content; ?>
									</div>
								<?php } ?>
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>
	</div>

	<!-- 페이지네이트 (상품목록 형) -->
	<div class="c_pagi">
		<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
	</div>
</div>
<!-- /공통페이지 섹션 -->


<script type="text/javascript">
	$(document).on('click', '.js_detail_btn', function(e) {
		e.preventDefault();
		var su = $(this).closest('.js_view');
		var _uid = su.data('uid');
		var _visible = su.hasClass('if_open');
		$('.js_view').removeClass('if_open');
		$('.js_detail_btn').attr('title', '열기');
		if(_visible === false) {
			su.addClass('if_open');
			su.find('.js_detail_btn').attr('title', '닫기');
			var _smode = (su.attr('data-hit') == 'false'?'update':'nocount');
			if(_smode == 'update') {
				$.ajax({
					data: {
						_mode: 'eval_hit',
						_smode: _smode,
						_uid: _uid
					},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
					success: function(data) {
						// hit수 증가
						var _num = su.find('.js_eval_hit').text();
						_num.replace(/[^0-9]/g, '')*1;
						_num = _num*1;
						su.find('.js_eval_hit').text(number_format(_num+1));

						// 중복 hit차단
						su.attr('data-hit', 'true');
					}
				});
			}
		}
	});


	// 리뷰 삭제
	function eval_del(uid) {

		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
				cache: false,
				type: "POST",
				data: "_mode=delete&uid=" + uid ,
				success: function(data){
					if( data == "no data" ) {
						alert('등록하신 글이 아닙니다.');
					}
					else if( data == "is reply" ) {
						alert('댓글이 있으므로 삭제가 불가합니다.');
					}
					else {
						alert('정상적으로 삭제하였습니다.');
						location.reload();
					}
				}
			});
		}
	}
</script>