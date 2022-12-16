<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// 내부패치 68번줄 kms 2019-11-05
$page_title = "쿠폰";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
	<!-- ◆마이페이지 리스트 탑 -->
	<div class="mypage_list_top">
		<ul>
			<li class="price">사용가능한 쿠폰 <?php echo number_format($TotalCountReady); ?>장</li>
			<li>할인쿠폰은 조건에 따라 주문시 바로 사용가능합니다.</li>
		</ul>
	</div>


	<?php if(count($row) <= 0) { ?>
		<!-- 내용 없을때 -->
		<div class="c_none"><span class="gtxt">쿠폰 내역이 없습니다.</span></div>
	<?php } else { ?>
		<!-- ◆마이페이지 리스트 공통 -->
		<div class="c_mypage_list">
			<ul>
				<?php 
					foreach($row as $k=>$v) { 
						$couponSetData = $v['coup_ocsinfo'] != '' ? unserialize(stripslashes($v['coup_ocsinfo'])) : array();	
						$printCouponSetBoon = printCouponSetBoon($couponSetData);
						$printCouponName = $couponSetData['ocs_name'] == '' ? $v['coup_name'] : $couponSetData['ocs_name'];						
				?>
					<li>
						<div class="date"><strong>지급일 : <?php echo date('Y-m-d', strtotime($v['coup_rdate'])); ?></strong><strong>만료일 : <?php echo date('Y-m-d', strtotime($v['coup_expdate'])); ?></strong></div>
						<div class="tit"><?php echo $printCouponName; ?> (<?php echo $v['coup_uid']; ?>)</div>
						<div class="double">
							<dl>
								<dt>
									<div class="state">
										<?php if($v['coup_use'] == 'Y') { ?>
											<span class="c_tag h22 red line">사용완료</span>
										<?php } else if($v['coup_use'] == 'W') { ?>
											<span class="c_tag h22 light line">사용대기</span>
										<?php } else if($v['coup_use'] == 'E') { ?>
											<span class="c_tag h22 light line">사용만료</span>
										<?php } else { ?>
											<span class="c_tag h22 black line">미사용</span>
										<?php } ?>
									</div>
								</dt>
								<dd>
									<div class="price">
										<?php echo $printCouponSetBoon; ?>
									</div>
								</dd>
							</dl>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>



	<!-- 페이지네이트 (상품목록 형) -->
	<div class="c_pagi">
		<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
	</div>
</div>
<!-- /공통페이지 섹션 -->