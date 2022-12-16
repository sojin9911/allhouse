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
					<li>적립금</li>
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

					<!-- ◆마이페이지 리스트 탑 -->
					<div class="mypage_list_top">
								<ul>
									<li class="price">나의 총 적립금 <?php echo number_format($mem_info['in_point']); ?>포인트</li>
									<li><?php echo number_format($siteInfo['s_pointusevalue']); ?>포인트 이상 누적되었을경우 현금처럼 사용가능합니다.</li>
								</ul>
							</div>




							<?php if(count($row) <= 0) { ?>
								<!-- 내용 없을때 -->
								<div class="c_none"><span class="gtxt">적립된 내역이 없습니다.</span></div>
							<?php } else { ?>
								<!-- ◆마이페이지 리스트 공통 -->
								<div class="c_mypage_list">
									<table>
										<colgroup>
											<col width="12%"/><col width="*"/><col width="12%"/><col width="12%"/><col width="12%"/>
										</colgroup>
										<thead>
											<tr>
												<th scope="col">적립일</th>
												<th scope="col">적립내용</th>
												<th scope="col">적립구분</th>
												<th scope="col">적립금내역</th>
												<th scope="col">적립예정일</th>
											</tr>
										</thead>
										<tbody>
											<!-- 적립금 내역 사용했을 경우 price 클래스에 if_minus 클래스 추가 -->
											<?php foreach($row as $k=>$v) { ?>
												<tr>
													<td class="date"><?php echo date('Y-m-d', strtotime($v['pl_rdate'])); ?></td>
													<td class="tit"><?php echo htmlspecialchars($v['pl_title']); ?></td>
													<td class="state">
														<?php if($v['pl_point'] > 0 ) { ?>
															<?php if($v['pl_status'] == 'Y') { ?>
																<span class="c_tag h22 black line">지급완료</span>
															<?php } else { ?>
																<span class="c_tag h22 light line">지급예정</span>
															<?php } ?>
														<?php } else { ?>
															<span class="c_tag h22 red line">사용완료</span>
														<?php } ?>
													</td>
													<td class="price<?php echo ($v['pl_point'] <= 0?' if_minus':null); ?>"><?php echo number_format($v['pl_point']); ?>원</td>
													<td class="date"><?php echo date('Y-m-d', strtotime($v['pl_appdate'])); ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							<?php } ?>


							<!-- 페이지네이트 (상품목록 형) -->
							<div class="c_pagi">
								<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
							</div>
						</div>
					</div>
					<!-- /공통페이지 섹션 -->

				</div> <!--right_sec_wrap-->
			</div>

		</div> <!--mypage_section-->
