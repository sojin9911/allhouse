<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location hide">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<li>상품문의</li>
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
					<!--보낸 쪽지함 start-->					
					<div class="content">
						<link type="text/css" rel="stylesheet" href="/home/allhouse/www/skin/site/basic2/css/c_design.css">
						<div class="">
							<div class="board_zone_tit">
								<h2>
									받은 쪽지함
								</h2>
							</div>
							<div class="board_zone_cont">
								<div class="board_zone_list">
									<div class="date_check_box">
										<form name="frmSearch" method="get" action="list.php">
											<input type="hidden" name="bdId" value="">
											<input type="hidden" name="memNo" value="">
											<input type="hidden" name="noheader" value="">

											<h3> 조회기간 </h3>
											<div class="date_check_list" data-target-name="rangDate[]">
												<button type="button" data-value="0">오늘</button>
												<button type="button" data-value="7">7일</button>
												<button type="button" data-value="15">15일</button>
												<button type="button" data-value="30">1개월</button>
												<button type="button" data-value="90">3개월</button>
												<button type="button" data-value="365">1년</button>
											</div>
											<!-- //date_check_list -->
											<div class="date_check_calendar">
												<input type="text" id="picker2" name="rangDate[]" class="anniversary js_datepicker" value=""> ~ <input type="text" name="rangDate[]" class="anniversary js_datepicker" value="">
											</div>
											<!-- //date_check_calendar -->

											<button type="submit" class="btn_date_check"><em>조회</em></button>
										</form>
									</div>
									<!-- //date_check_box -->


									<div class="board_list_qa" align="">
										<table class="board_list_table">
											<colgroup>
												<col style="width:70px;"> <!-- 문의날짜 -->
												<col style="width:100px;">
												<col><!-- 제목 -->
												<col style="width:100px;">  <!-- 작성자 -->
												<col style="width:80px;"> <!-- 문의상태 -->

											</colgroup>
											<thead>
											<tr>
												<th>번호</th>
												<th>보낸사람</th>
												<th>제목</th>
												<th>작성일</th>
												<th>조회</th>
											</tr>
											</thead>
											<tbody>

											<tr>
												<td>6</td>
												<td>올하우스</td>
												
												<td class="board_tit">
													<a href="/?pn=message_list_contents">
														<strong>전체테스트</strong>
													</a>
												</td>
												<td>2022-04-15 06:11</td>
												<td>읽지않음</td>
											</tr>
											<tr>
												<td>5</td>
												<td>올하우스</td>
												
												<td class="board_tit">
													<a href="/?pn=message_list_contents">
														<strong>asdasd</strong>
													</a>
												</td>
												<td>2022-04-15 06:09</td>
												<td>읽지않음</td>
											</tr>
											<tr>
												<td>4</td>
												<td>올하우스</td>
												
												<td class="board_tit">
													<a href="/?pn=message_list_contents">
														<strong>zxcxzc</strong>
													</a>
												</td>
												<td>2022-04-15 02:55</td>
												<td>읽지않음</td>
											</tr>
											<tr>
												<td>3</td>
												<td>올하우스</td>
												
												<td class="board_tit">
													<a href="/?pn=message_list_contents">
														<strong>ㅁㄴㅇㅁㄴㅇ</strong>
													</a>
												</td>
												<td>2022-04-15 01:08</td>
												<td>읽지않음</td>
											</tr>
											<tr>
												<td>2</td>
												<td>올하우스</td>
												
												<td class="board_tit">
													<a href="/?pn=message_list_contents">
														<strong>asdasd</strong>
													</a>
												</td>
												<td>2022-04-15 01:03</td>
												<td>읽지않음</td>
											</tr>
											<tr>
												<td>1</td>
												<td>올하우스</td>
												
												<td class="board_tit">
													<a href="/?pn=message_list_contents">
														<strong>ㅁㅇㄴㅁㄴㅇ</strong>
													</a>
												</td>
												<td>2022-04-15 00:59</td>
												<td>읽지않음</td>
											</tr>

											</tbody>
										</table>

									</div>
									<!-- //board_list_qa -->
									
									<!-- //pagination -->
								</div>
								<!-- //board_zone_list -->


							</div>
							<!-- //board_zone_cont -->
						</div>
					</div>
		  	</div>
				<!-- 페이지네이트 (상품목록 형) -->
				<div class="c_pagi">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
				</div>
    	</div>
				<!-- /상품평가/상품문의 리스트 -->

			
		</div>
		<!-- /공통페이지 섹션 -->
	</div>
</div>



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