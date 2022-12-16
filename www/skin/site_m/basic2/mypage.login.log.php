<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = '로그인기록'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
	<!-- ◆마이페이지 리스트 탑 -->
	<div class="mypage_list_top">
		<ul>
			<li class="price">최근 한달 로그인 회수 <?php echo number_format($TotalCount); ?>회</li>
			<li>개인정보보호를 위해 정기적인 비밀번호 변경을 권장합니다.</li>
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
						<div class="date"><strong>로그인 날짜 : <?php echo $v['lc_rdate']; ?></strong></div>
						<div class="double">
							<dl>
								<dt><div class="tit">IP : <?php echo $v['lc_ip']; ?></div></dt>
								<dd>
									<?php if($v['lc_type'] == 'individual') { ?>
										<div class="state"><span class="c_tag h23 black line">성공</span></div>
									<?php } else if($v['lc_type'] == 'deny') { ?>
										<div class="state"><span class="c_tag h23 light line">실패</span></div>
									<?php } ?>
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


	<!-- ◆페이지 이용도움말 -->
	<div class="c_user_guide">
		<div class="guide_box">
			<dl>
				<dt>로그인 성공/실패 기록</dt>
				<dd>로그인 성공 여부와 실패 기록에대한 아이피를 확인할 수 있습니다.</dd>
				<dd>실패건이 많을 경우 비밀번호 등을 정기적으로 수정하길 바랍니다.</dd>
			</dl>
		</div>
	</div>
</div>
<!-- /공통페이지 섹션 -->
