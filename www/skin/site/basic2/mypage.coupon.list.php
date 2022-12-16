<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<!-- 마이페이지 메인으로 이동 -->
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<li>쿠폰</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->

		<!-- ◆공통탭메뉴 -->
		<?php include_once($SkinData['skin_root'].'/member.header.php'); // -- 공통해더 --  ?>
		<!-- / 공통탭메뉴 -->


		<!-- ◆마이페이지 리스트 탑 -->
		<div class="mypage_list_top">
			<ul>
				<li class="price">사용가능한 쿠폰 <?php echo number_format($TotalCountReady); ?>장</li>
				<li>할인쿠폰은 조건에 따라 주문시 바로 사용가능합니다.</li>
			</ul>
		</div>




		<?php if(count($row) <= 0) { ?>
			<!-- 게시판 공통 내용없음 / table 없어지고 노출 -->
			<div class="c_none"><span class="gtxt">쿠폰 내역이 없습니다.</span></div>
		<?php } else { ?>
			<!-- ◆마이페이지 리스트 공통 -->
			<div class="c_mypage_list">
				<table>
					<colgroup>
						<col width="120"/><col width="120"/><col width="200"/><col width="*"/><col width="*"/><col width="110"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col">지급일</th>
							<th scope="col">만료일</th>
							<th scope="col">쿠폰번호</th>
							<th scope="col">쿠폰내용</th>
							<th scope="col">할인혜택</th>
							<th scope="col">사용여부</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($row as $k=>$v) {
								$couponSetData = $v['coup_ocsinfo'] != '' ? unserialize(stripslashes($v['coup_ocsinfo'])) : array();
								$printCouponSetBoon = printCouponSetBoon($couponSetData);
								$printCouponName = $couponSetData['ocs_name'] == '' ? $v['coup_name'] : $couponSetData['ocs_name'];
						?>
							<tr>
								<td class="date"><?php echo date('Y-m-d', strtotime($v['coup_rdate'])); ?></td>
								<td class="date"><?php echo date('Y-m-d', strtotime($v['coup_expdate'])); ?></td>
								<td class="coupon_num"><?php echo $v['coup_uid']; ?></td>
								<td class="coupon"><?php echo $printCouponName; ?></td>
								<td class="price">
								<?php echo $printCouponSetBoon; ?>
								</td>
								<td class="state">
									<?php if($v['coup_use'] == 'Y') { ?>
										<span class="c_tag h22 red line">사용완료</span>
									<?php } else if($v['coup_use'] == 'W') { ?>
										<span class="c_tag h22 light line">사용대기</span>
									<?php } else if($v['coup_use'] == 'E') { ?>
										<span class="c_tag h22 light line">사용만료</span>
									<?php } else { ?>
										<span class="c_tag h22 black line">미사용</span>
									<?php } ?>
								</td>
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