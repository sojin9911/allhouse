<!-- ◆ 푸터 (공통) -->
<div class="footer">
<div class="main_cs">
	<div class="layout_fix">
		<ul class="ul">

			<li class="li cs_box">
				<div class="title_box"><span class="tit">CS CENTER</span></div>
				<div class="cs_info">
					<div class="tel numcolor"><?php echo $siteInfo['s_glbtel']; ?></div>
					<div class="email hide"><a href="mailto:<?php echo $siteInfo['s_ademail']; ?>" title="이메일 보내기"><?php echo $siteInfo['s_ademail']; ?></a></div>
				</div>
				<div class="cs_time">
					<?php echo nl2br($siteInfo['s_cs_info']); ?>
				</div>
				<div class="btn_box hide">
					<ul>
						<?php // 내부패치 68번줄 kms 2019-11-05 ?>
						<li><a href="/?pn=mypage.inquiry.list" class="btn">1:1 온라인 문의</a></li>
						<li><a href="/?pn=service.partner.form" class="btn">쇼핑몰 이용안내</a></li>
					</ul>
				</div>
			</li>
			<li class="li bank_box">
				<div class="title_box"><span class="tit">BANK INFO</span></div>
				<div class="bank">
					<?php
					$NoneBank = _MQ_assoc(" select * from smart_bank_set where (1) order by bs_idx asc ");
					if(count($NoneBank) <= 0) $NoneBank = array();
					foreach($NoneBank as $k=>$v) {
					?>
						<ul>
							<li class="numcolor"><?php echo $v['bs_bank_num']; ?></li>
							<li class="font_bold"><?php echo $v['bs_bank_name']; ?></li>
							<li>예금주 : <?php echo $v['bs_user_name']; ?></li>
						</ul>
					<?php } ?>
				</div>
				<div class="btn_box hide">
					<ul>
						<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
						<?php if ( !$none_member_buy ) { ?>
						<li><a href="<?php echo (is_login()?'/?pn=mypage.order.list':'/?pn=service.guest.order.list'); ?>" class="btn">주문/배송조회</a></li>
						<?php } ?>
						<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
						<li><a href="/?pn=pages.view&type=agree&data=guide" class="btn">쇼핑몰 이용안내</a></li>
					</ul>
				</div>
			</li>
			<li class="li notice_box">
				<div class="title_box">
					<span class="tit"><a href="/?pn=board.list&_menu=notice">NOTICE</a></span>
				</div>
				<!-- 이벤트 탭은 if_event (날짜를 상태로 변경), li 5개 노출 -->
				<div class="notice_list js_main_comm_box js_main_comm_box_notice">
					<?php
					$BoardData = get_board_list('notice', 5,'N');
					if(count($BoardData) <= 0) {
					?>
						<!-- 내용없을경우 ul이 없어지고 -->
						<div class="post_none">등록된 내용이 없습니다.</div>
					<?php } else { ?>
						<ul>
							<?php
							$BoardInfo = get_board_info($BoardData[0]['b_menu']);
							foreach($BoardData as $k=>$v) {
								if(strtotime(" - ".$BoardInfo['bi_newicon_view']." day") <= strtotime($v['b_rdate'])) $is_new = true;
								else $is_new = false;
							?>
								<li>
									<div class="posting">
										<a href="/?pn=board.view&_uid=<?php echo $v['b_uid']; ?>" class="upper_link" title="<?php echo strip_tags(addslashes($v['b_title'])); ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif" alt="" /></a>
										<!-- 날짜 -->
										<span class="date"><?php echo date('m.d', strtotime($v['b_rdate'])); ?></span>
										<?php if($is_new === true) { ?>
											<!-- new 아이콘 (게시판 설정과 동일한 기간노출) -->
											<span class="new"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/main_new.gif" alt="" /></span>
										<?php } ?>
										<span class="txt"><?php echo strip_tags(stripslashes($v['b_title'])); ?></span>
									</div>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>
				</div>
			</li>
		</ul>
	</div>
</div>

	<!-- 푸터메뉴 -->
	<div class="bottom_menu">
		<div class="layout_fix">
			<ul class="clearfix">
				<!-- <?php if(is_login()) { ?>
					<li><a href="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php?_mode=logout" class="btn">로그아웃</a></li>
					<li><a href="/?pn=mypage.main" class="btn">마이페이지</a></li>
				<?php } else { ?>
					<li><a href="/?pn=member.login.form&_rurl=<?php echo urlencode('/?'.$_SERVER['QUERY_STRING']); ?>" class="btn">로그인</a></li>
					<li><a href="/?pn=member.join.agree" class="btn">회원가입</a></li>
				<?php } ?>
				<li><a href="<?php echo (is_login()?'/?pn=mypage.order.list':'/?pn=service.guest.order.list'); ?>" class="btn">주문조회</a></li> -->

				<?php if($normalpage_view['company'] == 1) { // JJC : 2020-12-16 : 일반페이지 노출여부 확인?>
					<li><a href="/?pn=pages.view&type=agree&data=company" class="btn">회사소개</a></li>
				<?php } ?>
				<li><a href="/?pn=pages.view&type=agree&data=agree" class="btn">이용약관</a></li>
				<li><a href="/?pn=pages.view&type=agree&data=privacy" class="btn font_bold">개인정보처리방침</a></li>
				<li class="hide"><a href="/?pn=pages.view&type=agree&data=deny" class="btn">이메일무단수집거부</a></li>
				<li class="hide"><a href="/?pn=pages.view&type=agree&data=guide" class="btn">이용안내</a></li>
			</ul>
		</div>
	</div>


	<div class="layout_fix">
		<ul class="copyright">
			<li class="li info">
				<!-- 회사/사이트 정보 -->
				<ul class="info_box">
					<li class="hide">
						<div class="shop_name"><?php echo $siteInfo['s_adshop']; ?></div>
						<?php if($siteInfo['sns_link_instagram'].$siteInfo['sns_link_facebook'].$siteInfo['sns_link_twitter'].$siteInfo['sns_link_blog'].$siteInfo['sns_link_cafe'].$siteInfo['sns_link_youtube'].$siteInfo['sns_link_kkp'].$siteInfo['sns_link_kks'] != '') { ?>
							<div class="sns_box">
								<ul>
									<!-- 관리자에서 sns링크 걸어둘 경우 노출 /  링크 없으면 li 삭제 -->
									<?php if($siteInfo['sns_link_instagram']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_instagram']; ?>" class="sns" title="인스타그램" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_insta.png" alt="인스타그램" /></a></li>
									<?php } ?>
									<?php if($siteInfo['sns_link_facebook']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_facebook']; ?>" class="sns" title="페이스북" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_face.png" alt="페이스북" /></a></li>
									<?php } ?>
									<?php if($siteInfo['sns_link_twitter']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_twitter']; ?>" class="sns" title="트위터" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_twitt.png" alt="트위터" /></a></li>
									<?php } ?>
									<?php if($siteInfo['sns_link_blog']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_blog']; ?>" class="sns" title="블로그" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_blog.png" alt="블로그" /></a></li>
									<?php } ?>
									<?php if($siteInfo['sns_link_cafe']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_cafe']; ?>" class="sns" title="카페" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_cafe.png" alt="블로그" /></a></li>
									<?php } ?>
									<?php if($siteInfo['sns_link_youtube']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_youtube']; ?>" class="sns" title="유튜브" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_youtube.png" alt="유튜브" /></a></li>
									<?php } ?>
									<?php if($siteInfo['sns_link_kkp']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_kkp']; ?>" class="sns" title="카카오 채널" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_kplus.png" alt="카카오 채널" /></a></li>
									<?php } ?>
									<?php if($siteInfo['sns_link_kks']) { ?>
										<li><a href="<?php echo $siteInfo['sns_link_kks']; ?>" class="sns" title="카카오 스토리" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_kstory.png" alt="카카오 스토리" /></a></li>
									<?php } ?>
								</ul>
							</div>
						<?php } ?>
					</li>
					<li>
						<span class="txt">상호명 : <?php echo $siteInfo['s_company_name']; ?></span>
						<span class="txt">대표자: <?php echo $siteInfo['s_ceo_name']; ?></span>
						<span class="txt">개인정보책임자: <?php echo $siteInfo['s_privacy_name']; ?></span>
						<span class="txt">
								사업자 등록번호: <?php echo $siteInfo['s_company_num']; ?>
								<a class="hide" href="#none" onclick="window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?=str_replace("-","",$siteInfo['s_company_num'])?>', 'communicationViewPopup', 'width=750, height=700;'); return false;" class="btn">사업자 정보확인</a>
							</span>
						<?php if($siteInfo['s_fax']) { ?><span class="txt">팩스: <?php echo $siteInfo['s_fax']; ?></span><?php } ?>
					</li>
					<?php if($siteInfo['s_view_network_company_info'] == 'Y') { ?>
						<li>
						<span class="txt">주소 : <?php echo $siteInfo['s_company_addr']; ?></span>
						<span class="txt">대표번호: <?php echo $siteInfo['s_glbtel']; ?></span>
						<span class="txt">이메일 주소: <?php echo $siteInfo['s_ademail']; ?></span>
						</li>
					<?php } ?>
					<!-- 
					<?php if($siteInfo['s_view_network_company_info'] == 'Y') { ?>
						<li>
							<span class="txt">통신판매업 신고번호 : <?php echo $siteInfo['s_company_snum']; ?></span>
							<span class="txt">
								사업자 등록번호: <?php echo $siteInfo['s_company_num']; ?>
								<a href="#none" onclick="window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?=str_replace("-","",$siteInfo['s_company_num'])?>', 'communicationViewPopup', 'width=750, height=700;'); return false;" class="btn">사업자 정보확인</a>
							</span>
						</li>
					<?php } ?>
					 -->
					<li class="copy">Copyright(c) 2018 <?php echo $system['host']; ?>. All Rights Reserved.</li>
				</ul>
			</li>
			<?php
			/*
				/program/wrap.footer.php 에서 정의
				$escrow_icon : 에스크로 이미지
				$escrow_link : 에스크로 확인 URL
			*/
			if($escrow_link) {
			?>
				<li class="li pg hide">
					<!-- PG정보 -->
					<div class="pg_box">
						<div class="ic_pg"><img src="<?php echo $escrow_icon; ?>" alt="" /></div>
						<div class="pc_txt">
							<div class="txt">고객님은 안전거래를 위해 현금 등으로 결제시 저희 쇼핑몰에서 가입한 구매 안전 서비스를 이용하실 수 있습니다.</div>
							<a href="#none" onclick="<?php echo $escrow_link; ?>" class="btn">구매안전 서비스 가입 사실 확인</a>
						</div>
					</div>
				</li>
			<?php } ?>
		</ul>

		<?php
		/*
			/program/wrap.footer.php 에서 정의
			$ssl_etc : 기타 인증서
			$escrow_icon : ssl 이미지
			$escrow_link : ssl 확인 URL
		*/
		if($ssl_etc || $ssl_icon) {
		?>
			<!-- 보안서버관련 각 li안에 스크립트가 나오도록, 테스트요함 -->
			<ul class="ssl">
				<li>
					<?php if($ssl_etc) { ?>
						<?php echo $ssl_etc; // 기타 ?>
					<?php } else { ?>
						<?php if($ssl_link) { ?><a href="#none" onclick="<?php echo $ssl_link; ?>"><?php } ?>
						<img src="<?php echo $ssl_icon; ?>" alt="ssl" />
						<?php if($ssl_link) { ?></a><?php } ?>
					<?php } ?>
				</li>
			</ul>
		<?php } ?>
	</div>
</div>
<!-- /푸터 (공통) -->
<?php include_once(OD_PROGRAM_ROOT.'/inc.footer.php'); // 스킨 내부파일로 직접 include 하지 마세요. ?>
