<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "미확인 입금자";
include_once($SkinData['skin_root'].'/service.header.php'); // 상단 헤더 출력
?>


<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	

	<!-- ◆게시판 목록 (미확인입금자 리스트) -->
	<div class="c_board_list deposit_list">
		<!-- 리스트 제어 -->
		<div class="c_list_ctrl">
			<div class="tit_box">
				<!-- 게시판명 -->
				<!-- <span class="tit">공지사항</span> -->
				<!-- 게시판 목록 수 -->
				<div class="total">TOTAL <strong><?php echo number_format($TotalCount); ?></strong></div>
			</div>
			<div class="ctrl_right">
				<!-- <div class="date">
					입금일자 / 클릭시 달력 노출
					<div class="input_box">
						<input type="date" name="" class="input_design" value="" placeholder="입금일자">
						<span class="shape"></span>
					</div>
				</div> -->
				<div class="search">
					<form role="search" method="get" action="/">
					<input type="hidden" name="pn" value="service.deposit.list">
						<input type="search" name="search_name" value="<?php echo $search_name; ?>" class="input_search" placeholder="입금자명">
						<input type="submit" name="" value="" class="btn_search" title="검색">
					</form>
				</div>
				<?php if($search_date || $search_name){ ?>
				<!-- 검색한 후 노출 / 검색 전 숨김 -->
				<a href="/?pn=service.deposit.list" class="all_btn">전체목록</a>
				<?php } ?>
				<!-- <a href="" class="write_btn">1:1 온라인 문의</a> -->
			</div>
		</div>

		<?php if(count($res)>0){ ?>
			<div class="table_list">
				<!-- ul반복 -->
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
					<ul>
						<li class="num"><?php echo $_num; ?></li>
						<li class="name"><?php echo $on_name; ?></li>
						<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
						<li class="bank"><?php echo $v['on_bank']; ?></li>
						<?php } ?>
						<li class="price"><span class="price_num"><?php echo number_format($v['on_price']); ?>원</span></li>
						<li class="date_box"><span class="date"><?php echo date('Y-m-d', strtotime($v['on_date'])); ?></span></li>
					</ul>
				<?php } ?>
			</div>
		<?php }else{ ?>
			<!-- 내용 없을때 -->
			<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
		<?php } ?>

	</div>




	
	
	<!-- 페이지네이트 (상품목록 형) -->
	<div class="c_pagi">
		<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
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
<!-- /공통페이지 섹션 -->