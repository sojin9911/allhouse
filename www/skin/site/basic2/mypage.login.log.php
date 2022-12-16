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
					<li>로그인기록</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->

		<!-- ◆공통탭메뉴 -->
		<?php include_once($SkinData['skin_root'].'/member.header.php'); // -- 공통해더 --  ?>
		<!-- / 공통탭메뉴 -->



		<?php if(count($row) <= 0) { ?>
			<!-- 내용 없을때 -->
			<div class="c_none"><span class="gtxt">로그인 기록 내역이 없습니다.</span></div>
		<?php } else { ?>
			<!-- ◆마이페이지 리스트 공통 -->
			<div class="c_mypage_list">
				<table>
					<colgroup>
						<col width="18%"/><col width="*"/><col width="12%"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col">로그인 날짜</th>
							<th scope="col">로그인 IP</th>
							<th scope="col">성공여부</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($row as $k=>$v) { ?>
							<tr>
								<td class="date"><?php echo $v['lc_rdate']; ?></td>
								<td class="tit"><?php echo $v['lc_ip']; ?></td>
								<td class="state">
									<?php if($v['lc_type'] == 'individual') { ?>
										<span class="c_tag h23 black line">성공</span>
									<?php } else if($v['lc_type'] == 'deny') { ?>
										<span class="c_tag h23 light line">실패</span>
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
</div>
<!-- /공통페이지 섹션 -->