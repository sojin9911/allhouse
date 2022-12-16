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
 				<!--경로 /home/allhouse/www/skin/site/basic2/recived_note_contents.php-->			
					<div class="content">
						<link type="text/css" rel="stylesheet" href="/home/allhouse/www/skin/site/basic2/css/c_message.css">
						<div class="">
							<div class="board_zone_tit">
								<h2>받은 쪽지함</h2>
							</div>
							<div class="recive_zone_cont">
								<div class="recive_zone_list">
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


									<div class="recive_list_qa">
										<div class="recive_list_info">
											<h3>전체테스트</h3>
											<p>
												<span>2022-04-15 06:11</span>
												<span>올하우스</span>
											</p>
										</div>
										<div class="recive_txt">
											<p>받은 쪽지함 내용입니다 받은 쪽지함 내용입니다 받은 쪽지함 내용입니다 보낸쪽지함 내용입니다 </p>
										</div>
										<div class="recive_btn_area">
											<button>답장</button>
											<button>목록</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>