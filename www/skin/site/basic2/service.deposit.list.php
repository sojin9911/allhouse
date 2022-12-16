<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<div class="title">미확인 입금자</div>
			<!-- 로케이션 -->
			<div class="c_location">
				<ul>
					<li>홈</li>
					<!-- <li>고객센터</li> -->
					<li>미확인 입금자</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->


		<!-- ◆공통탭메뉴 -->
		<?php include_once($SkinData['skin_root'].'/service.header.php'); // -- 공통해더 --  ?>
		<!-- / 공통탭메뉴 -->


		<!-- ◆게시판 목록 (공통) -->
		<div class="c_board_list deposit_list">
			<!-- 리스트 제어 -->
			<div class="c_list_ctrl">
				<div class="tit_box">
					<!-- 타이틀 -->
					<span class="tit">미확인 입금자</span>
					<!-- 목록 수 -->
					<div class="total">TOTAL <strong><?php echo number_format($TotalCount); ?></strong></div>
				</div>

				<form method="get" action="/">
				<input type="hidden" name="pn" value="service.deposit.list">
					<div class="ctrl_right">
						<div class="date">
							<!-- 입금일자 / 클릭시 달력 노출 -->
							<div class="input_box">
								<input type="text" name="search_date" class="input_design js_pic_day" value="<?php echo $search_date; ?>" style="width:120px" placeholder="입금일자">
							</div>
						</div>
						<div class="search">
							<input type="text" name="search_name" value="<?php echo $search_name; ?>" class="input_search" placeholder="입금자명">
							<input type="submit" name="" value="" class="btn_search" title="검색">
						</div>
						<?php if($search_date || $search_name){ ?>
						<!-- 검색한 후 노출 / 검색 전 숨김 -->
						<a href="/?pn=service.deposit.list" class="all_btn">전체목록</a>
						<?php } ?>
						<!-- 1:1 온라인 문의 버튼 -->
						<?php // 내부패치 68번줄 kms 2019-11-05 ?>
						<a href="/?pn=mypage.inquiry.list" class="qna_btn">1:1 온라인 문의</a>
					</div>
				</form>
			</div>
			
			<?php if(count($res)>0){ ?>
				<table>
					<colgroup>
						<col width="100"><col width="150">
						<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
						<col width="*">
						<?php } ?>
						<col width="*"><col width="200">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">번호</th>
							<th scope="col">입금자명</th>
							<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
							<th scope="col">은행</th>
							<?php } ?>
							<th scope="col">입금액</th>
							<th scope="col">입금일자</th>
						</tr>
					</thead> 
					<tbody>
						<?php
							foreach($res as $k=>$v) {
								$_num = $TotalCount - $count - $k ;
								// 입금자명 부분 노출
								$on_name = $v['on_name'];
								if($siteInfo['s_online_notice_privacy'] == 'Y'){
									$len = 2;
									$on_name = iconv_substr($v['on_name'], 0, $len, "utf-8");
									for($i=0; $i<(utf8_length($v['on_name'])-$len);$i++) $on_name .= '*';

								}
						?>
							<tr>
								<td class="num"><?php echo $_num; ?></td>
								<td class="name"><?php echo $on_name; ?></td>
								<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
								<td class="bank"><?php echo $v['on_bank']; ?></td>
								<?php } ?>
								<td class="price"><span class="price_num"><?php echo number_format($v['on_price']); ?>원</span></td>
								<td class="date"><?php echo date('Y-m-d', strtotime($v['on_date'])); ?></td>
							</tr>
						<?php } ?>
					</tbody> 
				</table>
			<?php }else{ ?>
				<!-- 내용 없을때 ( 게시판 공통 ) / 위 table 없어지고 노출 -->
				<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
			<?php } ?>

		</div>


	
		
		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>




		<!-- ◆페이지 이용도움말 -->
		<div class="c_user_guide">
			<div class="guide_box">
				<dl>
					<dt>미확인 입금자 목록 안내사항</dt>
					<dd>입금자/입금액이 정확하지 않은 입금자 목록 입니다.</dd>
					<dd>리스트에 성함이 있는 고객님은 1:1 온라인 문의 또는 고객센터로 문의해 주시기 바랍니다.</dd>
				</dl>
			</div>
		</div>




	</div>
</div>