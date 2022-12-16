<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "적립금";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
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
			<ul>
				<?php foreach($row as $k=>$v) { ?>
					<li>
						<div class="date">
							<strong>적립일 : <?php echo date('Y-m-d', strtotime($v['pl_rdate'])); ?></strong>
							<strong>적립 예정일 : <?php echo date('Y-m-d', strtotime($v['pl_appdate'])); ?></strong>
						</div>
						<div class="tit"><?php echo htmlspecialchars($v['pl_title']); ?></div>
						<div class="double">
							<dl>
								<dt>
									<div class="state">
										<?php if($v['pl_point'] > 0 ) { ?>
											<?php if($v['pl_status'] == 'Y') { ?>
												<span class="c_tag h22 black line">지급완료</span>
											<?php } else { ?>
												<span class="c_tag h22 light line">지급예정</span>
											<?php } ?>
										<?php } else { ?>
											<span class="c_tag h22 red line">사용완료</span>
										<?php } ?>
									</div>
								</dt>
								<dd><div class="price<?php echo ($v['pl_point'] <= 0?' if_minus':null); ?>"><?php echo number_format($v['pl_point']); ?>원</div></dd>
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