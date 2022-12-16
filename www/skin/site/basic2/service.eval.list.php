<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<div class="title">커뮤니티</div>
			<!-- 로케이션 -->
			<div class="c_location">
				<ul>
					<li>홈</li>
					<li>커뮤니티</li>
					<li>상품후기</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->


		<!-- ◆공통탭메뉴 -->
		<?php include_once($SkinData['skin_root'].'/community.header.php'); // -- 공통해더 --  ?>
		<!-- / 공통탭메뉴 -->




		<!-- ◆상품평가/상품문의 리스트 -->
		<div class="c_board_list board_review">
			<!-- 리스트 제어 -->
			<div class="c_list_ctrl">
				<div class="tit_box">
					<span class="tit">상품후기</span>
					<div class="total">TOTAL <strong><?php echo number_format($TotalCount); ?></strong></div>
				</div>
				<form action="/" method="get">
					<input type="hidden" name="pn" value="<?php echo $pn; ?>">
					<div class="ctrl_right">
						<select name="search_type">
							<option value="search_title,search_content"<?php echo ($search_type == 'search_title,search_content'?' selected':null); ?>>제목 + 내용</option>
							<option value="search_title"<?php echo ($search_type == 'search_title'?' selected':null); ?>>제목</option>
							<option value="search_content"<?php echo ($search_type == 'search_content'?' selected':null); ?>>내용</option>
						</select>
						<div class="search">
							<input type="text" name="search_word" value="<?php echo $search_word; ?>" class="input_search" placeholder="검색어를 입력해주세요."/>
							<input type="submit" value="" class="btn_search" title="검색"/>
						</div>
						<?php if(isset($search_word) && $search_word != '') { ?>
							<!-- 검색한 후 노출 / 검색 전 숨김 -->
							<a href="/?pn=<?php echo $pn; ?>" class="all_btn">전체목록</a>
						<?php } ?>
					</div>
				</form>
			</div>

			<?php if(count($row) <= 0) { ?>
				<!-- 게시판 공통 내용없음 / table 없어지고 노출 -->
				<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
			<?php } else { ?>
				<table>
					<colgroup>
						<col width="65"/><col width="110"/><col width="40"/><col width="50"/><col width="*"/><col width="100"/><col width="100"/><col width="80"/><col width="55"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col">번호</th>
							<th scope="col">평가점수</th>
							<th scope="col" colspan="2">상품정보</th>
							<th scope="col">제목/상품</th>
							<th scope="col">작성자</th>
							<th scope="col">작성일</th>
							<th scope="col">조회수</th>
							<th scope="col">보기</th>
						</tr>
					</thead>
					<tbody>
						<!-- 보기 클릭시 if_open 클래스 추가 / 한번더 클릭시 닫기 -->
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
							<tr class="js_view" data-uid="<?php echo $v['pt_uid']; ?>">
								<td class="num"><?php echo number_format($_num); ?></td>
								<td><span class="mark"><span class="star" style="width:<?php echo $star_persent; ?>%"></span></span></td>
								<!-- 찜하기 버튼 / 클릭시 a에 hit 클래스 추가  -->
								<td class="wish"><a href="#none" class="wish_btn js_wish <?=$is_wish?'hit':''?>" data-pcode="<?php echo $v['p_code']; ?>" title="<?php echo (is_wish($v['p_code'])?'찜삭제':'찜하기'); ?>"><span class="btn"></span></a></td>
								<td class="thumb_box">
									<!-- 상품보기 -->
									<a href="<?php echo $pro_link; ?>" class="thumb" title="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>" target="_blank"><img src="<?php echo $pro_img; ?>" alt="" /></a>
								</td>
								<td class="tit">
									<div class="title">
										<!-- 클릭시 열림 -->
										<div class="tit_box">
											<a href="#none" class="upper_link js_detail_btn" title="<?php echo addslashes($talk_title); ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""/></a>
											<?php if($is_new === true || $is_file === true) { ?>
												<!-- 아이콘 -->
												<span class="icon">
													<?php if($is_new === true) { ?>
														<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_new.gif" alt="새글"/>
													<?php } ?>
													<?php if($is_file === true) { ?>
														<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_photo.gif" alt="사진첨부"/>
													<?php } ?>
												</span>
											<?php } ?>
											<span class="tt"><?php echo $talk_title; ?></span>
										</div>
										<!-- 상품명 / 상품으로 이동 -->
										<a href="<?php echo $pro_link; ?>" class="sub_txt" title="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>"><?php echo htmlspecialchars($v['p_name']); ?></a>
									</div>
								</td>
								<td class="name"><?php echo LastCut2($v['pt_inid'], 3); ?></td>
								<td class="date"><?php echo date('Y.m.d', strtotime($v['pt_rdate'])); ?></td>
								<td class="num js_eval_hit"><?php echo number_format($v['pt_hit']); ?></td>
								<!-- 열렸을때 타이틀 닫기로 변경 -->
								<td class="arrow"><a href="#none" class="arrow_btn js_detail_btn" title="열기"><span class="icon"></span></a></td>
							</tr>
							<!-- 보기 클릭시 노출 -->
							<tr class="view_box js_detail_view" style="display: none;" data-uid="<?php echo $v['pt_uid']; ?>" data-hit="false">
								<td colspan="9">
									<div class="view_tit"><?php echo $talk_title; ?></div>
									<div class="view_txt"><?php echo nl2br($talk_content); ?></div>
									<?php if($talk_img) { ?><div class="view_img"><img src="<?php echo $talk_img; ?>" alt="" /></div><?php } ?>
									<?php if(count($reply_r) > 0) { ?>
										<div class="reply_box">
											<?php echo $reply_content; ?>
										</div>
									<?php } ?>
									<?php echo $del_btn; // <!-- 내글일때 노출 --> ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
		<!-- /상품평가/상품문의 리스트 -->

		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
	</div>
</div>
<!-- /공통페이지 섹션 -->



<script type="text/javascript">
	$(document).on('click', '.js_detail_btn', function(e) {
		e.preventDefault();
		var su = $(this);
		var _uid = su.closest('tr').data('uid');
		var _visible = $('.js_detail_view[data-uid='+_uid+']').is(':visible');
		$('.js_detail_view').hide();
		$('.js_view').removeClass('if_open');
		$('.js_detail_btn.arrow_btn').attr('title', '열기');
		if(_visible === false) {
			$('.js_detail_view[data-uid='+_uid+']').show();
			su.closest('tr').find('.js_detail_btn.arrow_btn').attr('title', '닫기');
			$('.js_view[data-uid='+_uid+']').addClass('if_open');
			var _smode = ($('.js_detail_view[data-uid='+_uid+']').attr('data-hit') == 'false'?'update':'nocount');
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
						var _num = su.closest('tr').find('.js_eval_hit').text();
						_num.replace(/[^0-9]/g, '')*1;
						_num = _num*1;
						su.closest('tr').find('.js_eval_hit').text(number_format(_num+1));

						// 중복 hit차단
						$('.js_detail_view[data-uid='+_uid+']').attr('data-hit', 'true');
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