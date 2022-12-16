<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<!-- 마이페이지 메인으로 이동 -->
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location hide">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<?php // 내부패치 68번줄 kms 2019-11-05 ?>
					<li>1:1 온라인 문의</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->



		<div class="mypage_section">
			<div class="left_sec">
				<!-- ◆공통탭메뉴 -->
				<?php include_once($SkinData['skin_root'].'/member.header.php'); // -- 공통해더 --  ?>
				<!-- / 공통탭메뉴 -->
			</div>




			<div class="right_sec">
				<div class="right_sec_wrap">
					<?php // c_mypage_list -> c_board_list kms 2019-11-05 ?>
					<!-- ◆마이페이지 리스트 공통 -->
					<div class="c_board_list">
						<!-- 리스트 제어 -->
						<div class="c_list_ctrl">
							<div class="tit_box">
								<?php // 내부패치 68번줄 kms 2019-11-05 ?>
								<span class="tit">1:1 문의</span>
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
									<?php // 내부패치 68번줄 kms 2019-11-05 ?>
									<a href="/?pn=mypage.inquiry.form&_PVSC=<?php echo $_PVSC; ?>" class="write_btn">문의하기</a>
								</div>
							</form>
						</div>

						<?php if(count($row) <= 0) { ?>
							<!-- 내용 없을때 위 div 숨기고 노출 -->
							<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
						<?php } else { ?>
							<table>
								<colgroup>
									<col width="60"/><col width="110"/><col width="*"/><col width="12%"/><col width="80"/>
								</colgroup>
								<thead>
									<tr>
										<th scope="col">번호</th>
										<th scope="col">답변여부</th>
										<th scope="col">문의내용</th>
										<th scope="col">작성일</th>
										<th scope="col">보기</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach($row as $k=>$v) {
										$_num = $TotalCount-$count-$k;
									?>
										<!-- 보기 클릭시 if_open 클래스 추가 / 한번더 클릭시 닫기 -->
										<tr class="js_view" data-uid="<?php echo $v['r_uid']; ?>">
											<td class="num"><?php echo $_num; ?></td>
											<td class="state">
												<?php if($v['r_status'] == '답변완료') { ?>
													<span class="c_tag h22 black line">답변완료</span>
												<?php } else { ?>
													<span class="c_tag h22 light line">답변대기</span>
												<?php } ?>
											</td>
											<td class="tit"><a href="#none" class="inquiry_tit js_detail_btn"><?php echo htmlspecialchars($v['r_title']); ?></a></td>
											<td class="date"><?php echo date('Y-m-d', strtotime($v['r_rdate'])); ?></td>
											<!-- 열렸을때 타이틀 닫기로 변경 -->
											<td class="arrow"><a href="#none" class="arrow_btn js_detail_btn" title="열기"><span class="icon"></span></a></td>
										</tr>
										<!-- 보기 클릭시 노출 -->
										<tr class="view_box js_detail_view" style="display: none;" data-uid="<?php echo $v['r_uid']; ?>" data-hit="false">
											<td colspan="5">
												<div class="view_tit"><?php echo htmlspecialchars($v['r_title']); ?></div>
												<div class="view_txt"><?php echo nl2br(stripslashes(htmlspecialchars($v['r_content']))); ?></div>
												<?php
												$getBoardFile = getFilesRes('smart_request', $v['r_uid'].'_user');
												if(count($getBoardFile) > 0) {
												?>
													<div class="file_down user_file">
														<div class="tit">첨부파일</div>
														<div class="link_box">
															<?php foreach($getBoardFile as $kk=>$vv) { ?>
																<a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$vv['f_uid']; ?>" class="link"><?php echo $vv['f_oldname']; ?></a>
															<?php } ?>
														</div>
													</div>
												<?php } ?>
												<?php if($v['r_status'] == '답변완료' && $v['r_admcontent']) { ?>
													<!-- 댓글 -->
													<div class="reply">
														<span class="admin">
															<span class="name">운영자</span><span class="date_num"><?php echo date('Y-m-d', strtotime($v['r_admdate'])); ?></span>
														</span>
														<?php echo nl2br(stripslashes(htmlspecialchars($v['r_admcontent']))); ?>
														<?php
														$getBoardFile = getFilesRes('smart_request', $v['r_uid']);
														if(count($getBoardFile) > 0) {
														?>
															<div class="file_down admin_file">
																<div class="tit">첨부파일</div>
																<div class="link_box">
																	<?php foreach($getBoardFile as $kk=>$vv) { ?>
																		<a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$vv['f_uid']; ?>" class="link"><?php echo $vv['f_oldname']; ?></a>
																	<?php } ?>
																</div>
															</div>
														<?php } ?>
													</div>
												<?php } ?>
												<!-- 내글일때 노출 -->
												<a href="#none" onclick="inquiry_del(<?php echo $v['r_uid']; ?>); return false;" class="c_btn h22 light line">삭제</a>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						<?php } ?>
					</div>
				</div>
					<!-- 페이지네이트 (상품목록 형) -->
			<div class="c_pagi">
				<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
			</div>
			</div>
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
		}
	});


	// 문의삭제
	function inquiry_del(uid) {
		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/mypage.inquiry.pro.php",
				cache: false,
				type: "POST",
				data: "_mode=delete&uid=" + uid ,
				success: function(data){
					if( data == "no data" ) {
						alert('등록하신 글이 아닙니다.');
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