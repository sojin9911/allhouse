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
					<li>출석체크</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->


		<!-- ◆공통탭메뉴 -->
		<?php include_once($SkinData['skin_root'].'/community.header.php'); // -- 공통해더 --  ?>
		<!-- / 공통탭메뉴 -->


		<div class="c_attend ">
			<?php if(!$event_trigger) { ?>

				<!-- [PC] 이벤트별 배너 (1050 x free)  -->
				<?php if($ready_img <> ''){ ?>
					<div class="banner"><img src="<?php echo $ready_img; ?>" alt="출석체크 준비중"></div>
				<?php }else{ ?>
					<!-- 배너 없을때만 노출 / 이벤트 준비중이거나 중지일때는 무조건 노출 -->
					<div class="title">출석체크</div>

					<!-- 이벤트 준비중이거나 중지일때 노출 / 이벤트 진행중일때는 div 숨김 -->
					<div class="none">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/attend_none.png" alt=""></div>
						<div class="tit">출석체크 준비중</div>
						<div class="sub_txt">현재 출석체크 이벤트를 준비중입니다.<br>성원에 감사드리며 더 좋은 혜택으로 찾아뵙겠습니다.</div>
					</div>
				<?php } ?>

			<?php }else{ ?>

				<!-- [PC] 이벤트별 배너 (1050 x free)  -->
				<?php if($title_img <> ''){ ?>
					<div class="banner"><img src="<?php echo $title_img; ?>" alt="출석체크"></div>
				<?php }else{ ?>
					<!-- <div class="banner"><img src="<?php echo $SkinData['skin_url']; ?>/images/sample/banner_attend.jpg" alt="출석체크"></div> -->
				<?php } ?>

				<!-- 출석체크 탑 -->
				<div class="attend_top">
					<ul>
						<li><a href="/?pn=mypage.main" class="mypage_btn">마이페이지 바로가기</a></li>
						<li class="year_box">
							<div class="year">
								<div class="num">
									<strong><?php echo $selected_year; ?>.<?php echo $selected_month; ?></strong>
									<!-- 클릭시 오늘날짜로 이동 -->
									<a href="/?pn=promotion.attend&selected=<?php echo $today_date; ?>" class="today">오늘로</a>
								</div>
								<!-- 이전,다음버튼 -->
								<?php if($prev_month){ ?>
									<span class="prevnext prev"><a href="<?php if($prev_month){ echo '/?pn=promotion.attend&selected=' . $prev_month_date; }else{ echo '#none'; }?>" title="이전달"><span class="icon"></span></a></span>
								<?php } ?>
								<?php if($next_month){ ?>
									<span class="prevnext next"><a href="<?php if($next_month){ echo '/?pn=promotion.attend&selected=' . $next_month_date; }else{ echo '#none'; }?>" title="다음달"><span class="icon"></span></a></span>
								<?php } ?>
							</div>
						</li>
						<!-- 클릭시 btn클래스에 hit 추가 / '출석체크 완료'로 변경 -->
						<?php if($today_status === true) { ?>
							<li class="attend_btn"><a href="#none" class="btn hit"><span class="txt">출석체크 완료</span></a></li>
						<?php } else { ?>
							<li class="attend_btn"><a href="#none" onclick="checkin(); return false;" class="btn"><span class="txt">오늘 출석체크</span></a></li>
						<?php } ?>
					</ul>
				</div>

				<!-- 기간, 총 출석일수 -->
				<div class="date_box">
					<div class="date_left">
						<!-- 이벤트 기간 -->
						<?php if($event_info['atc_limit'] == 'N'){ ?>
							<span class="date">이벤트 종료시까지</span>
						<?php } else { ?>
							<span class="date"><?php echo date('Y.m.d',strtotime($event_start)); ?> ~ <?php echo date('Y.m.d',strtotime($event_end)); ?></span>
						<?php } ?>
						<span class="total">총 출석체크 : <strong><?php echo number_format($chk_total_cnt); ?></strong>일</span>
					</div>
					<div class="guide">※ 조건 만족 시 지급되는 혜택은 하단 안내사항을 확인해주세요.</div>
				</div>


				<!-- 출석체크 달력 -->
				<!-- 오늘날짜에는 today 클래스 추가 / 출석체크 한 날짜에는 hit 클래스 추가 / 날짜가 없는 td에는 no 클래스 추가 -->
				<div class="attend_calendar">
					<table>
						<thead>
							<tr>
								<th class="sun">SUN</th>
								<th>MON</th>
								<th>TUE</th>
								<th>WED</th>
								<th>THU</th>
								<th>FRI</th>
								<th class="sat">SAT</th>
							</tr>
						</thead>
						<tbody>
							<?php
								// 이번달의 첫일, 말일
								$this_first_day = $selected_year . "-" . $selected_month . "-01";
								$this_end_day = $selected_year . "-" . $selected_month . "-" . date("t" , strtotime($selected_date));
								// 첫일의 요일
								$this_first_week = date('w', strtotime($this_first_day));

								// 이벤트의 첫일, 말일
								$event_first_day = $event_start;
								$event_end_day = $event_end;
								// 이벤트첫일의 요일
								$event_first_week = date('w', strtotime($event_first_day));

								// 첫일까지 공백 추가 --  한달씩 출력 ****************************************************************
								for($i=0; $i<$this_first_week; $i++){ echo "<td class='no'></td>"; }

								// 날짜 출력
								for($i=1; $i<=date("t" , strtotime($selected_date)); $i++){
									// 현재날짜
									$td_cell_date = date("Y-m-d", strtotime($selected_year . "-" . $selected_month . "-" . $i));
									// 요일추출
									$_week = date('w', strtotime($td_cell_date));
									if($i > 1 && $_week ==0){ echo "</tr><tr>"; }

									// 날짜
									$date_print = date('d',strtotime($td_cell_date));

									// 클래스 설정
									$app_class = "";
									if($event_start <= $td_cell_date && $event_end >= $td_cell_date){
										// 오늘 날짜
										if($td_cell_date == $today_date) $app_class .= " today";

										// 출석체크시
										$chk = _MQ_result(" select count(*) from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_date = '".$td_cell_date."' and atl_event = '".$event_uid."' ");
										if($chk > 0) $app_class .= " hit";
									}

									// 출력
									echo "<td class='". $app_class ."'>" . $date_print . "</td>";
								}
								// 첫일까지 공백 추가 --  한달씩 출력 ****************************************************************

								// 나머지 공백추가
								for($i=$_week; $i < 6; $i++){ echo "<td class='no'></td>"; }

							?>
						</tbody>
					</table>
				</div>




				<!-- ◆페이지 이용도움말 -->
				<div class="c_user_guide">
					<div class="guide_box">
						<!-- 도움말 제목+내용은 dl묶음으로 반복해서 사용 -->
						<dl>
							<dt>출석체크 이벤트 참여 안내사항</dt>
							<dd>출석체크는 PC와 모바일은 구분없이 1일 1회만 인정됩니다.</dd>
							<!-- 관리자 설정에 따라 둘중 한가지 노출 -->
							<?php if($event_info['atc_type'] == 'T'){ ?>
								<dd>이벤트 기간동안 출석 합산일이 일정기간 이상인 회원에게 혜택이 지급되는 <strong>누적 참여형 이벤트</strong>입니다.</dd>
							<?php }else{ ?>
								<dd>이벤트 기간동안 연속으로 일정기간 출석한 회원에게 혜택이 지급되는 <strong>연속 참여형 이벤트</strong> 입니다.</dd>
							<?php } ?>
							<!-- 관리자 설정에 따라 둘중 한가지 노출 -->
							<?php if($event_info['atc_limit'] == 'Y'){ ?>
								<dd>본 출석체크는 <strong><?php echo date('Y년 m월 d일',strtotime($event_start)); ?> 부터 <?php echo date('Y년 m월 d일',strtotime($event_end)); ?> 까지
								(<?php echo (strtotime($event_end) - strtotime($event_start)) / (60 * 60 *24) +1;?>일)</strong> 진행되며, 쇼핑몰 운영 상 별도 고지없이 조기 종료될 수 있습니다. </dd>
							<?php }else{ ?>
								<dd>본 출석체크는 <strong>기간제한이 없는 이벤트</strong>로서 쇼핑몰 운영 상 별도 고지없이 조기 종료될 수 있습니다.</dd>
							<?php } ?>
							<!-- 관리자 설정에 따라 둘중 한가지 노출 -->
							<?php if($event_info['atc_duplicate'] == 'Y'){ ?>
								<dd>출석체크 혜택은 이벤트 기간 중 조건 <strong>만족 시마다 계속 지급</strong>됩니다.</dd>
							<?php }else{ ?>
								<dd>출석체크 혜택은 이벤트 기간 중 조건 <strong>만족 시 한번만 지급</strong>됩니다.</dd>
							<?php } ?>
						</dl>
						<dl>
							<dt>출석체크 이벤트 혜택</dt>

							<dd class="guide_table"><!-- 테이블 들어올때 클래스 guide_table -->
								<table>
									<colgroup>
										<col width="25%">
										<col width="*">
									</colgroup>
									<thead>
										<tr>
											<th scope="col">지급 조건</th>
											<th scope="col">혜택</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach($event_info['info'] as $k=>$v){
												// 지급일
												$_apply_str = '';

												// 쿠폰정보
												$_coupon_str = '';
												if($v['ata_coupon']>0){
													$_coupon = _MQ(" select * from smart_individual_coupon_set where ocs_uid = '". $v['ata_coupon'] ."' ");
													// 이벤트 쿠폰
													if($_coupon['ocs_type'] <> 'express'){
														if($_coupon['ocs_dtype'] == 'price'){ // 할인액
															$str_dprice = ' </span><strong>' . number_format( 1 * $_coupon['ocs_price']) . '원 할인</strong>';
														}else{ // 할인율
															$str_dprice = ' </span><strong>' . number_format( 1 * $_coupon['ocs_per']) . '% 할인</strong>';
														}
													}else{
														// 무료배송 쿠폰
														$str_dprice = '';
													}
													//$_coupon_str = '['. $arr_coupon_type[$_coupon['ocs_type']] .'] ' . trim(stripslashes($_coupon['ocs_name'])) . $str_dprice;
													// 2019-09-05 SSJ :: 쿠폰타입 사용안함
													$_coupon_str = trim(stripslashes($_coupon['ocs_name'])) . $str_dprice;
													// 2018-12-12 SSJ :: 지급일 노출 위치 변경
													if($v['ata_coupon_delay'] > 0) $_coupon_str .= '<em>'.$v['ata_coupon_delay'].'일 후 지급</em>';
													else $_coupon_str .= '<em>당일 지급</em>';

													// 쿠폰 지급일
													//if($_apply_str <> '') $_apply_str .= ' / ';
													//if($v['ata_coupon_delay'] > 0) $_apply_str .= $v['ata_coupon_delay'].'일 후';
													//else $_apply_str .= '당일';
												}

												// 포인트
												$_point_str = '';
												if($v['ata_point']>0){
													$_point_str = number_format( 1 * $v['ata_point']) . '포인트';
													// 2018-12-12 SSJ :: 지급일 노출 위치 변경
													if($v['ata_point_delay'] > 0) $_point_str .= '</strong><em>'.$v['ata_point_delay'].'일 후 지급</em>';
													else $_point_str .= '</strong></em>당일 지급<em>';

													// 포인트 지급일
													//if($_apply_str <> '') $_apply_str .= ' / ';
													//if($v['ata_point_delay'] > 0) $_apply_str .= $v['ata_point_delay'].'일 후';
													//else $_apply_str .= '당일';
												}
										?>
												<tr>
													<td><?php echo $v['ata_days']?>일 이상 출석 시</td>
													<td style="text-align:left">
														<?php if($v['ata_coupon'] > 0){ ?><div class="attend_coupon"><div class="in"><span class="ti"><?php echo $_coupon_str; ?></div></div><?php } ?>
														<?php if($v['ata_point'] > 0){ ?><div class="attend_coupon this_point"><div class="in"><span class="ti">포인트 지급</span><strong><?php echo $_point_str; ?></div></div><?php } ?>
													</td>
													<!-- <td><?php echo $_apply_str; ?></td> -->
												</tr>
										<?php } ?>

									</tbody>
								</table>
							</dd>
						</dl>
					</div>
				</div>

			<?php } ?>

		</div>
	</div>
</div>
<!-- /공통페이지 섹션 -->



<?php if(!$event_trigger){ ?>
	<script>
	// 출석체크
	function checkin(){
		alert('출석체크 이벤트가 종료되었습니다.');
		return false;
	}
	</script>
<?php }else if(!is_login()){ ?>
	<script>
	// 출석체크
	function checkin(){
		login_alert("<?php echo urlencode('/?pn='.$pn)?>");
		return false;
	}
	</script>
<?php }else if($event_trigger){ ?>
	<script>
	// 출석체크
	function checkin(){
		$.ajax({
			data: {'mode':'checkin','today_date':'<?php echo $today_date; ?>','uid':'<?php echo $event_uid; ?>'},
			type: 'POST',
			cache: false,
			dataType: 'JSON',
			url: '<?php echo OD_PROGRAM_DIR; ?>/promotion.attend.pro.php',
			success: function(data) {
				if( data['code']=='OK' ) {
					alert(data['msg']); location.reload();
				} else {
					alert(data['msg']);
				}
			}
		});
		return false;
	}
	</script>
<?php } ?>