<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage_main">
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
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->




		<div class="mypage_section">
			<div class="left_sec">
				<!-- ◆공통탭메뉴 -->
				<?php
					// PC 탑 네비
					include_once($SkinData['skin_root'].'/member.header.php');
				?>
				<!-- / 공통탭메뉴 -->
			</div>




			<div class="right_sec">
				<div class="right_sec_wrap">
					<div class="mypage-lately-info">
						<div class="mypage-lately_tit">
							<h3>예치금 현황</h3>
						</div>
					</div>
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
					<div class="lately_info-cont c_mypage_list">
						<p class="lately_info_tit"><strong>2022-05-08 ~ 2022-10-07</strong>까지의 거래잔액 사용내역 총 <strong><?php echo number_format($TotalCount);?></strong>건</p>
						<div class="lately_info_header">
							<button id="point_btn">예치금 충전</button>
						</div>
						<table class="lately_table">
							<colgroup>
								<col style="width:12%;">
								<!--날짜-->
								<col style="width:12%;">
								<!--유형-->
								<col style="width:46%;">
								<!--내용-->
								<col style="width:15%;">
								<!--예치금 내역-->
								<col style="width:15%;">
								<!--잔여예치금-->
							</colgroup>
							<thead>
								<tr>
									<th>날짜</th>
									<th>유형</th>
									<th>내용</th>
									<th>거래잔액 내역</th>
									<th>잔여 거래잔액</th>
								</tr>
							</thead>
							<tbody class="point_table">
    						<?php foreach($row as $k=>$v) { ?>
								<tr>
									<td><?php echo date('Y-m-d', strtotime($v['pl_rdate'])); ?></td>
									<td align=center>
									<?php if($v['pl_point'] > 0 ) { ?>
										<?php if($v['pl_status'] == 'Y') { ?>
											<span class="c_tag h22 black line" style="float:initial">지급완료</span>
										<?php } else { ?>
											<span class="c_tag h22 light line" style="float:initial">지급예정</span>
										<?php } ?>
									<?php } else { ?>
										<span class="c_tag h22 red line" style="float:initial">사용완료</span>
									<?php } ?>
                                    </td>
									<td><?php echo htmlspecialchars($v['pl_title']); ?></td>
									<td class="price <?php echo ($v['pl_point'] <= 0?'if_minus':''); ?>"><?php echo number_format($v['pl_point']); ?>원</td>
									<td class="td-price-light">
                                        <?php echo ($v['pl_status']=='Y'?number_format($v['pl_point_after']):'-'); ?>
                                        <?php echo ($v['pl_status']=='Y' && $v['pl_point'] <> $v['pl_point_apply'] ? '<br><span class="t_red">(보정 : '.number_format($v['pl_point_apply']-$v['pl_point']).')</span>' : ''); ?>
                                    </td>
								</tr>
    						<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
      </div>
    </div>
  </div>
</div>
<div class="jh_popup">
  <form class="jh_box">
			<ul class="jh_box_inner">
				<li class="jh_title_line"><h3 class="jh_titile">예치금 충전</h3> <button class="jh_xBtn" type="button">X</button></li>
				<li class="jh_formline"><label for="" class="jh_lbl"><span class="mint_sqr"></span><p>이름</p></label> <span class="name_margin"><?php echo $mem_info['in_name']; ?></span></li>
				<li  class="jh_formline">
					<label class="jh_lbl">
						<span class="mint_sqr"></span><p>현재 예치금</p>
					</label>  
					<span class="now_jg">
						
					</span>
				</li>
				<li  class="jh_formline">
					<label class="jh_lbl"><span class="mint_sqr"></span><p>입금은행</p></label>  
					<!--<input class="jh_input" id="bank_name" type="text">-->
					<select name="" id="bank_slct" class="jh_input">
						<option value="신한은행">신한은행</option>
						<option value="우리은행">우리은행</option>
						<option value="농협">농협</option>
					</select>
				</li>
				<li  class="jh_formline"><label class="jh_lbl"><span class="mint_sqr"></span><p>충전금액</p></label> <input  class="jh_input" type="text"></li>
				<li  class="jh_formline"><label class="jh_lbl"><span class="mint_sqr"></span><p>예금주명</p></label> <input  class="jh_input" type="text"></li>
			</ul>
			<ul class="popup_btnbox">
				<li><button type="button" id="jh_cBtn">취소</button></li>
				<li><button type="button" id="jh_hBtn">확인</button></li>
			</ul>
			
	</form>
</div>
<script>
	//팝업창 버튼 누르면 보이게 하는 스크립트
	const point_xBtn = document.querySelector('.jh_xBtn');
	const point_popup = document.querySelector('.jh_popup');
	const point_popup_btn = document.getElementById('point_btn');
	

	point_xBtn.addEventListener('click',function hide_jh(){
		point_popup.style.display = 'none';
	});
	point_popup_btn.addEventListener('click',function show_jh(){
		point_popup.style.display = 'block';
	});

	//잔금 보이게 하는 스크립트
	let point_table = document.querySelectorAll('.td-price-light');
	let now_jg = document.querySelector('.now_jg');
	
	now_jg.innerText = point_table[0].innerText;



	const jh_cBtn = document.getElementById('jh_cBtn');
  jh_cBtn.addEventListener('click',function(){
		point_popup.style.display = 'none';
	});
</script>