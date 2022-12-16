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
					<div class="board_zone_tit">
						<h2>미입고 내역</h2>
					</div>
					<div class="my_locker">
						<h3>미입고 내역 총 <span class="cnt_clr">1</span>건</h3>
						<table>
							<colgroup>
								<col>
								<col width="10%">
								<col width="35%">
							</colgroup>
							<tr>
								<th>주문번호</th>
								<th>상품이미지</th>
								<th>상품명</th>
								<th>주문수량</th>
								<th>재고량</th>
								<th>부족재고</th>
								<th>품절여부</th>
							</tr>
							<tr>
								<td class="order_number"><a href="">28371-34235-74800</a></td>
								<td>
									<div class="img_wrap">
										<img src="<?php echo $SkinData['skin_url']; ?>/images/sample/thumb2.jpg" alt="">
									</div>
								</td>
								<td>벌룬곰세트</td>
								<td>4</td>
								<td>0</td>
								<td>4</td>
								<td>품절</td>
							</tr>
						</table>
					</div>
							


							 

				</div>
				<!-- 페이지네이트 (상품목록 형) -->
				<div class="c_pagi">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
				</div>
			</div>
		
		</div>
				

			
	</div>
		<!-- /공통페이지 섹션 -->
</div>




