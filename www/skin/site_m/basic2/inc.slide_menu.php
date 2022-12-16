<!-- ******************************************
  ◆ SLIDE MENU (Only Mobile)
  -- ****************************************** -->
<div class="slide_wrap_bg js_slide_wrap_bg js_slide_wrap_content" style="display:none"></div>
<div class="slide_wrap js_sliding_menu js_slide_wrap_content" style="left:-100%;">
	<div class="close_slide js_close_slide_bg" onclick="SlideLeft();" style="display: none;"></div>
	<a href="#none" onclick="SlideLeft();" onclick="return false;" class="bnt_slide_close js_ss_close_btn" title="슬라이드 닫기" style="display: none;"></a>
	<div class="slide_inner">
        <div class="mine">
            <div class="mine_box">
                <?php if(is_login()) { // 로그인 ?>
									<div class="btn_box">
													<a href="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php?_mode=logout" class="btn logout">로그아웃</a>
                            <a href="/?pn=mypage.main" class="btn login">마이페이지</a>
                        </div>
                    <div class="user_box">
                        <div class="user_name">
                            <div class="name after"><strong><?php echo $mem_info['in_name']; ?></strong>님</div>
														<div>방문을 환영합니다.</div>
                        </div>
                    </div>
                    <?php // {{{회원등급추가}}}   ?>
                <?php } else { // 로그인 전 ?>
                    <div class="user_box">
                        <a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="name before hide"><span class="icon"></span>로그인이 필요합니다.</a>
                        <div class="btn_box">
                            <a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="btn login">로그인</a>
                            <a href="/?pn=member.join.agree" class="btn login">회원가입</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="right" style="display: none;">
                <ul class="app_btn">
                    <?php // 설정은 앱에서만 노출 ?>
                    <li>
                        <a href="#none" onclick="return false;" class="btn js_onoff_event" data-target="body" data-add="if_open_set" title="설정">
                            <span class="ic"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/slide_set.svg" alt="설정" /></span>
                            <span class="tx">설정</span>
                        </a>
                    </li>
                    <?php // 알림은 웹에서는 노출시키고, 웹일땐 앱스토어로 연결 ?>
                    <li>
                        <a href="#none" onclick="" class="btn" title="알림">
                            <span class="ic"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/slide_alarm.svg" alt="알림" /></span>
                            <span class="tx">알림</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>


		<!-- 쇼핑정보 (로그인전에는 숫자정보만 안보이고 그대로 보이고 "로그인이 필요한 페이지입니다" 로 로그인넘어갔다가 페이지 이동) -->
		<?php
		// 주문배송 카운트(결제대기, 결제완료, 배송중)
		$SlideOrderCnt = get_order_ing_cnt(array('결제대기', '결제완료', '배송대기', '배송준비', '배송중'));

		// 문의내역 :: 최근일주일 내 답변이 있을 경우에만 표기
		$MyInquiry = _MQ(" select count(*) as cnt from `smart_request` where (1) and r_inid = '".get_userid()."' and r_status = '답변완료' and date(r_admdate) between date_add(now(), interval -7 day) and curdate() ");
		?>
		<div class="my_shopping hide">
			<ul>
				<li>
					<a href="/?pn=shop.cart.list" class="btn ic_cart">
						<span class="tx">장바구니</span>
						<span class="num js_cart_cnt"><?php echo $cart_cnt; ?></span><!-- 0표기안함 -->
					</a>
				</li>
				<li>
					<a href="<?php echo (is_login()?'/?pn=mypage.order.list':'/?pn=member.login.form&_rurl='.urlencode('/?pn=mypage.order.list')); ?>" class="btn ic_order">
						<span class="tx">주문배송</span>
						<?php if($SlideOrderCnt > 0) { ?>
							<span class="num"><?php echo number_format($SlideOrderCnt); ?></span>
							<!-- 0표기안함 -->
						<?php } ?>
					</a>
				</li>
				<li>
					<a href="/?pn=mypage.inquiry.list" class="btn ic_qna">
						<span class="tx">문의내역</span>
						<?php if($MyInquiry['cnt']  > 0 && is_login()) { ?>
							<!-- 최근일주일 내 답변이 있을 경우에만 표기 -->
							<span class="num"><?php echo number_format($MyInquiry['cnt']); ?></span>
						<?php } ?>
					</a>
				</li>
				<li>
					<a href="<?php echo (is_login()?'/?pn=mypage.main':'/?pn=member.login.form&_rurl='.urlencode('/?pn=mypage.main')); ?>" class="btn ic_my"><span class="tx">마이페이지</span></a>
				</li>
			</ul>
		</div>




		<div class="my_shopping">
			<ul>
				<li>
					<a href="/?pn=service.qna.list" class="btn ic_qna1">
						<span class="tx">Q&A</span>
					</a>
				</li>
				<li>
					<a href="<?php echo (is_login()?'/?pn=mypage.order.list':'/?pn=member.login.form&_rurl='.urlencode('/?pn=mypage.order.list')); ?>" class="btn ic_look_prod">
						<span class="tx">최근본상품</span>
					</a>
				</li>
				<li>
					<a href="/?pn=mypage.wish.list" class="btn ic_heart">
						<span class="tx">찜리스트</span>
						<?php if($MyInquiry['cnt']  > 0 && is_login()) { ?>
							<!-- 최근일주일 내 답변이 있을 경우에만 표기 -->
							<span class="num"><?php echo number_format($MyInquiry['cnt']); ?></span>
						<?php } ?>
					</a>
				</li>
			</ul>
		</div>


		<div class="nav_box hide">
			<ul>
				<?php if(is_login()) { ?>
					<li><a href="/?pn=mypage.order.list" class="btn">주문조회</a></li>
				<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
				<?php } else if ( !$none_member_buy ){ ?>
					<li><a href="/?pn=service.guest.order.list" class="btn">비회원 주문조회</a></li>
				<?php } ?>
				<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
				<li><a href="/?pn=service.main" class="btn">고객센터</a></li>
			</ul>
			<ul>
				<li><a href="/?pn=product.promotion_list" class="btn">쇼핑몰 기획전</a></li>
				<li><a href="/?pn=product.brand_list" class="btn">브랜드 상품</a></li>
			</ul>
		</div>


		<div class="slide_tab_menu clearfix">
			<ul>
				<li class="hit sld_go_cate"><span>CATEGORY</span></li>
				<li class="sld_go_brand"><span>BRAND</span></li>
			</ul>
		</div>


		<!-- 상품 카테고리 -->
		<div class="ctg_box">
			<?php foreach($AllCate as $k=>$v) { ?>
				<dl class="js_slide_cate<?php echo (count($v['sub']) <= 0?' if_no2':null); ?><?php echo (isset($ActiveCate['cuid'][0]) && $ActiveCate['cuid'][0] == $v['c_uid']?' if_open':null); ?>"><!-- 클릭시 if_open 클래스 추가, 2차 카테고리 노출,  2차가 없으면 if_no2해서 해당1차로 바로연결 -->
					<dt>
						<!-- 1차 -->
						<a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="ctg1<?php echo (count($v['sub']) > 0?' js_slide_cate_more':null); ?>"><?php echo $v['c_name']; ?></a>
						<a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="btn_go hide">상품보기</a>
						<a href="#none" class="btn_ctrl js_slide_cate_more" title="열고닫기"></a>
					</dt>
					<?php foreach($v['sub'] as $kk=>$vv) { ?>
						<dd><a href="/?pn=product.list&cuid=<?php echo $vv['c_uid']; ?>" class="ctg2"><?php echo $vv['c_name']; ?></a></dd>
					<?php } ?>
				</dl>
			<?php } ?>
		</div>


		<div class="slide_brand_area">
			<a href="/?pn=product.brand_list">브랜드 검색</a>
		</div>



		<div class="slide_commu_area">
			<p>COMMUNITY</p>
			<ul>
				<li><a href="/?pn=board.list&_menu=notice">공지사항</a></li>
				<li><a href="/?pn=service.eval.list">상품후기</a></li>
				<li><a href="/?pn=service.qna.list">상품문의</a></li>
				<li><a href="/?pn=faq.list">FAQ</a></li>
			</ul>
		</div>


		<!-- 사이트맵 -->
		<?php
		// 고객센터 정보 생성
		$customerBoardMenu = array();
		$customerBoard = _MQ_assoc(" select * from `smart_bbs_info` where (1) and bi_view = 'Y' and bi_view_type = 'service' order by bi_view_idx asc, bi_uid asc ");
		if(count($customerBoard) <= 0) $customerBoard = array();
		foreach($customerBoard as $k=>$v) { $customerBoardMenu[] = $v['bi_uid']; }

		// 커뮤니티 정보 생성
		$communityBoardMenu = array();
		$communityBoard = _MQ_assoc(" select * from `smart_bbs_info` where (1) and bi_view = 'Y' and bi_view_type = 'community' order by bi_view_idx asc, bi_uid asc ");
		if(count($communityBoard) <= 0) $communityBoard = array();
		foreach($communityBoard as $k=>$v) { $communityBoardMenu[] = $v['bi_uid']; }

		// 기본 hit 초기화
		$if_customer_hit = false;
		$if_mypage_hit = false;
		$if_commun_hit = false;
		$if_agree_hit = false;

		// 히트처리
		$Lpn = $pn;
		if(empty($Lpn)) $Lpn = 'main';
		if($Lpn == 'service.main' || $Lpn == 'mypage.inquiry.form' || (isset($_menu) && in_array($_menu, $customerBoardMenu))) $if_customer_hit = true;
		else if(preg_match('`mypage\.`', $Lpn)) $if_mypage_hit = true;
		else if(in_array($Lpn, array('service.eval.list', 'service.qna.list', 'service.partner.form')) || (isset($_menu) && in_array($_menu, $communityBoardMenu))) $if_commun_hit = true;
		else if($Lpn == 'pages.view' && $type == 'agree') $if_agree_hit = true;
		?>
		<div class="ctg_box if_sitemap hide">
			<div class="tit">전체 서비스</div>
			<dl class="js_slide_comun<?php echo ($if_customer_hit === true?' if_open':null); ?>"><!-- 클릭시 if_open 클래스 추가, 2차 카테고리 노출,  2차가 없으면 if_no2해서 해당1차로 바로연결 -->
				<dt>
					<a href="/?pn=service.main" class="ctg1 js_slide_comun_more">고객센터</a>
					<a href="/?pn=service.main" class="btn_go">바로가기</a><!-- 고객센터메인으로 -->
					<a href="#none" class="btn_ctrl js_slide_comun_more" title="열고닫기"></a>
				</dt>
				<?php foreach($customerBoard as $k=>$v) { ?>
					<dd><a href="/?pn=board.list&_menu=<?php echo $v['bi_uid']; ?>" class="ctg2"><?php echo $v['bi_name']; ?></a></dd>
				<?php } ?>
				<?php // 내부패치 68번줄 kms 2019-11-05 ?>
<!-- 				<dd><a href="/?pn=mypage.inquiry.form" class="ctg2">1:1 온라인 문의</a></dd> -->
				<dd><a href="/?pn=faq.list" class="ctg2">자주 묻는 질문</a></dd>
				<dd><a href="/?pn=service.deposit.list" class="ctg2">미확인 입금자</a></dd>
			</dl>
			<dl class="js_slide_comun<?php echo ($if_mypage_hit === true?' if_open':null); ?>">
				<dt>
					<a href="/?pn=mypage.main" class="ctg1 js_slide_comun_more">마이페이지</a>
					<a href="/?pn=mypage.main" class="btn_go">바로가기</a>
					<a href="#none" class="btn_ctrl js_slide_comun_more" title="열고닫기"></a>
				</dt>
				<?php if(is_login()) { ?>
					<dd><a href="/?pn=mypage.order.list" class="ctg2">주문내역</a></dd>
					<dd><a href="/?pn=mypage.point.list" class="ctg2">적립금</a></dd>
					<dd><a href="/?pn=mypage.coupon.list" class="ctg2">쿠폰</a></dd>
					<?php // 내부패치 68번줄 kms 2019-11-05 ?>
					<dd><a href="/?pn=mypage.inquiry.list" class="ctg2">1:1 온라인 문의</a></dd>
					<dd><a href="/?pn=mypage.wish.list" class="ctg2">찜한 상품</a></dd>
					<dd><a href="/?pn=mypage.eval.list" class="ctg2">상품후기</a></dd>
					<dd><a href="/?pn=mypage.qna.list" class="ctg2">상품문의</a></dd>
					<dd><a href="/?pn=mypage.modify.form" class="ctg2">정보수정</a></dd>
					<dd><a href="/?pn=mypage.login.log" class="ctg2">로그인 기록</a></dd>
					<dd><a href="/?pn=mypage.leave.form" class="ctg2">회원탈퇴</a></dd>
				<?php } else { ?>
					<dd><a href="/?pn=member.login.form" class="ctg2">로그인</a></dd>
					<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
					<?php if ( !$none_member_buy ) { ?>
					<dd><a href="/?pn=service.guest.order.list" class="ctg2">비회원 주문조회</a></dd>
					<?php } ?>
					<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
					<dd><a href="/?pn=member.find.form&_menu=find_id" class="ctg2">아이디 찾기</a></dd>
					<dd><a href="/?pn=member.find.form&_menu=find_pw" class="ctg2">비밀번호 찾기</a></dd>
					<dd><a href="/?pn=member.join.agree" class="ctg2">회원가입</a></dd>
				<?php } ?>
			</dl>
			<dl class="js_slide_comun<?php echo ($if_commun_hit === true?' if_open':null); ?>">
				<dt>
					<a href="/?pn=service.eval.list" class="ctg1 js_slide_comun_more">커뮤니티</a><!-- <a href="" class="btn_go">전체보기</a> -->
					<a href="#none" class="btn_ctrl js_slide_comun_more" title="열고닫기"></a>
				</dt>
				<dd><a href="/?pn=service.eval.list" class="ctg2">상품 후기</a></dd>
				<dd><a href="/?pn=service.qna.list" class="ctg2">상품 문의</a></dd>
				<?php foreach($communityBoard as $k=>$v) { ?>
					<dd><a href="/?pn=board.list&_menu=<?php echo $v['bi_uid']; ?>" class="ctg2"><?php echo $v['bi_name']; ?></a></dd>
				<?php } ?>
				<dd><a href="/?pn=service.partner.form" class="ctg2">제휴문의</a></dd>
			</dl>
			<dl class="js_slide_comun<?php echo ($if_agree_hit === true?' if_open':null); ?>">
				<dt>
					<a href="/?pn=service.page.view&pageid=company" class="ctg1 js_slide_comun_more">서비스 이용안내</a><!-- <a href="" class="btn_go">전체보기</a> -->
					<a href="#none" class="btn_ctrl js_slide_comun_more" title="열고닫기"></a>
				</dt>
				<?php if($normalpage_view['company'] == 1) { // JJC : 2020-12-16 : 일반페이지 노출여부 확인?>
					<dd><a href="/?pn=pages.view&type=agree&data=company" class="ctg2">회사소개</a></dd>
				<?php } ?>
				<dd><a href="/?pn=pages.view&type=agree&data=guide" class="ctg2">이용안내</a></dd>
				<dd><a href="/?pn=pages.view&type=agree&data=agree" class="ctg2">이용약관</a></dd>
				<dd><a href="/?pn=pages.view&type=agree&data=privacy" class="ctg2">개인정보처리방침</a></dd>
				<dd><a href="/?pn=pages.view&type=agree&data=deny" class="ctg2">이메일무단수집거부</a></dd>
			</dl>
		</div>


		<!-- ◆ 최근상품/찜상품 -->
		<div class="myitem hide">
			<!-- 탭메뉴 해당탭 hit-->
			<div class="tabmenu">
				<ul>
					<li class="js_slide_footer_tab_li hit"><a href="#none" class="tab js_slide_footer_tab" data-type="history">최근 본 상품<?php echo (count($LatestList) > 0?' ('.number_format(count($LatestList)).')':null); ?></a></li>
					<li class="js_slide_footer_tab_li"><a href="#none" <?php if(!is_login()) { ?>onclick="if(confirm('로그인 후 이용하실 수 있습니다.\n로그인페이지로 이동 하시겠습니까?')) location.href='/?pn=member.login.form&_rurl=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>'; return false;"<?php } ?> class="tab<?php echo (is_login()?' js_slide_footer_tab':null); ?>" data-type="wish">찜한 상품<?php echo (get_wish_cnt() > 0 && is_login()?' ('.number_format(get_wish_cnt()).')':null); ?></a></li><!-- 찜한상품은 로그인 전에는 경고창/숫자는 로그인전에는 비노출 -->
				</ul>
			</div>
			<!-- / 탭메뉴 -->

			<div class="js_slide_footer_box js_slide_footer_history_box">
				<?php
				// $LatestList -> /program/inc.header.php 에서 정의
				if(count($LatestList) <= 0) {
				?>
					<!-- 내용없을경우 모두공통 -->
					<div class="none">최근 본 상품이 없습니다.</div>
					<!-- 찜하기에서는 찜한 상품이 없습니다. -->
					<!-- / 내용없을경우 모두공통 -->
				<?php } else { ?>
					<!-- 해당아이템 리스트 (최근본상품이나 찜한상품 디자인같음) li 3개씩 묶어 롤링 -->
					<div class="slide_item_list">
						<ul>
							<?php
							foreach($LatestList as $k=>$v) {
								if($k > 0 & $k%3 === 0) echo '</ul><ul style="display:none;">';
								$_img = get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT);
							?>
								<li>
									<div class="slide_item">
										<a href="/?pn=product.view&pcode=<?php echo $v['p_code']; ?>" class="upper_link" title="<?php echo addslashes($v['p_name']); ?>"></a>
										<div class="thumb">
											<?php if($v['p_stock'] <= 0) { ?>
												<div class="soldout"><span class="tx">SOLDOUT</span></div>
											<?php } ?>
											<?php if($_img) { ?>
												<div class="real_img"><img src="<?php echo $_img; ?>" alt="<?php echo addslashes($v['p_name']); ?>" /></div>
											<?php } ?>
											<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($v['p_name']); ?>" /></div>
										</div>
										<div class="item_name"><?php echo stripslashes($v['p_name']); ?></div>
										<div class="price"><?php echo number_format($v['p_price']); ?></div>
									</div>
								</li>
							<?php } ?>
						</ul>
					</div>
					<!-- 해당아이템 리스트 -->

					<?php if(count($LatestList) > 3) { ?>
						<!-- 롤링아이콘 ; 해당아이콘 active클래스 추가, 롤링안되거나 없을때는 안보여야함 -->
						<div class="rolling_icon">
							<span class="lineup js_slide_footer_history_box_pager">
								<?php for($i=0; $i<count($LatestList)/3; $i++) { ?>
									<a href="#none" onclick="return false;" class="icon<?php echo ($i===0?' active':null); ?>" data-slide-index="<?php echo $i; ?>"></a>
								<?php } ?>
							</span>
						</div>
						<!-- / 롤링아이콘 -->
					<?php } ?>
				<?php } ?>
			</div>
			<div class="js_slide_footer_box js_slide_footer_wish_box" style="display: none;">
				<?php
				// $LatestList -> /program/inc.header.php 에서 정의
				if(count($WishList) <= 0) {
				?>
					<!-- 내용없을경우 모두공통 -->
					<div class="none">찜한 상품이 없습니다.</div>
					<!-- 찜하기에서는 찜한 상품이 없습니다. -->
					<!-- / 내용없을경우 모두공통 -->
				<?php } else { ?>
					<!-- 해당아이템 리스트 (최근본상품이나 찜한상품 디자인같음) li 3개씩 묶어 롤링 -->
					<div class="slide_item_list">
						<ul>
							<?php
							foreach($WishList as $k=>$v) {
								if($k > 0 & $k%3 === 0) echo '</ul><ul style="display:none;">';
								$_img = get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT);
							?>
								<li>
									<div class="slide_item">
										<a href="/?pn=product.view&pcode=<?php echo $v['p_code']; ?>" class="upper_link" title="<?php echo addslashes($v['p_name']); ?>"></a>
										<div class="thumb">
											<?php if($v['p_stock'] <= 0) { ?>
												<div class="soldout"><span class="tx">SOLDOUT</span></div>
											<?php } ?>
											<?php if($_img) { ?>
												<div class="real_img"><img src="<?php echo $_img; ?>" alt="<?php echo addslashes($v['p_name']); ?>" /></div>
											<?php } ?>
											<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($v['p_name']); ?>" /></div>
										</div>
										<div class="item_name"><?php echo stripslashes($v['p_name']); ?></div>
										<div class="price"><?php echo number_format($v['p_price']); ?></div>
									</div>
								</li>
							<?php } ?>
						</ul>
					</div>
					<!-- 해당아이템 리스트 -->

					<?php if(count($WishList) > 3) { ?>
						<!-- 롤링아이콘 ; 해당아이콘 active클래스 추가, 롤링안되거나 없을때는 안보여야함 -->
						<div class="rolling_icon">
							<span class="lineup js_slide_footer_wish_box_pager">
								<?php for($i=0; $i<count($WishList)/3; $i++) { ?>
									<a href="#none" onclick="return false;" class="icon<?php echo ($i===0?' active':null); ?>" data-slide-index="<?php echo $i; ?>"></a>
								<?php } ?>
							</span>
						</div>
						<!-- / 롤링아이콘 -->
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<!-- / ◆ 최근상품/찜상품 -->
	</div>
	<div class="js_slide_footer"></div>

    <?php // 설정(기능 추가) ?>
    <div class="set_open">
        <div class="white_box">
            <ul class="ul">
                <li class="li">
                    <div class="set_tit">알림 설정</div>
                    <ul>
                        <li>
                            <div class="tit">쇼핑 혜택 및 이벤트 알림</div>
                            <div class="right">
                                <?php // 알림 체크 ?>
                                <label class="label">
                                    <input type="checkbox" name="" class="js_app_alram_set" value="">
                                    <span class="icon"></span>
                                </label>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="li">
                    <div class="set_tit">앱 정보</div>
                    <ul>
                        <li>
                            <?php // 버전 노출 ?>
                            <div class="tit ver">1.0.1</div>
                            <div class="right">
                                <?php // 최신버전이면 노출 ?>
                                <div class="tx_ver" style="display: none;">최신버전입니다</div>
                                <?php // 업데이트 있으면 노출 ?>
                                <a href="#none" target="_blank" class="tx_btn">업데이트</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
            <?php // 닫기 버튼 ?>
            <a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target="body" data-add="if_open_set">확인</a>
        </div>
        <?php // 배경 닫기 ?>
        <div onclick="return false;" class="bg_close js_onoff_event" data-target="body" data-add="if_open_set"></div>
    </div><!-- end set_open -->

</div>

<script type="text/javascript">
	// 슬라이드 열기
	var scrolltop = $(window).scrollTop();
	$(document).on('click', '.js_slide_open, .js_sliding_menu_close', function(e) {
		e.preventDefault(); e.stopPropagation();
		SlideLeft();
	});
	//$(window).on('load',function() { SlideLeft(); }); //임시
	function SlideLeft() {
		var Action;
		Action = ($('.js_sliding_menu').hasClass('js_on') === true?'hide':'show');
		if(Action == 'show') { // 열기

			// 블러효과주가
			$('.js_header_position > div').css({
				'filter':'blur(1px)',
				'-webkit-filter': 'blur(1px)',
				'-moz-filter': 'blur(1px)',
				'-o-filter': 'blur(1px)',
				'-ms-filter': 'blur(1px)'
			});
			$('.js_slide_wrap_content').css({
				'filter':'',
				'-webkit-filter': '',
				'-moz-filter': '',
				'-o-filter': '',
				'-ms-filter': ''
			});

			//$('.js_sliding_menu').show();
			$('.js_sliding_menu').addClass('js_on');
			$('.js_sliding_menu').stop().animate({'left':'0'},300, function() {});
			$('.js_sliding_menu').height('100%');//.css('overflow', 'auto');
			// $('.js_sliding_menu .slide_inner').css('height', 'auto');
			$('.js_slide_wrap_bg').stop().fadeIn(200);
			$('.js_sliding_menu_close').stop().fadeIn(200);
			//if($('.js_slide_cate.if_open').length > 0) scrolltoClass('.js_slide_cate.if_open', 0, $('.js_sliding_menu')); // 열려있는 상품 카테고리가 있다면 자동 이동
			//if($('.js_slide_comun.if_open').length > 0) scrolltoClass('.js_slide_comun.if_open', 0, $('.js_sliding_menu')); // 열려있는 전체 서비스가 있다면 자동 이동
			scrolltop = $(window).scrollTop();
			$('body, html').css({'overflow':'hidden','height':window.innerHeight,'position':'fixed','width':window.innerWidth}).scrollTop(scrolltop);
			$('.js_sliding_menu').css('top', 0);
			$('.js_ss_close_btn').show();
			$('.js_close_slide_bg').show();

			slide_footer_box('history'); // 슬라이더 동작
		}
		else {
			$('body, html').attr('style','');

			//$('.js_header_position >  div').css('filter', '');
			$(window).scrollTop(scrolltop);
			$('.js_sliding_menu').removeClass('js_on');
			$('.js_sliding_menu').stop().animate({'left':'-100%'},600, function() {
				//$('.js_sliding_menu').hide();
			});
			$('.js_sliding_menu').height('auto');//.css('overflow', 'hidden');
			$('.js_sliding_menu .slide_inner').css('height', '100%');
			$(".js_slide_wrap_bg").stop().fadeOut(500);
			$('.js_sliding_menu_close').stop().fadeOut(500);
			$('.js_sliding_menu').css('top', 0);
			$('.js_ss_close_btn').hide();
			$('.js_close_slide_bg').hide();

			// 블러효과 제거
			$('.js_header_position >  div').css({
				'filter':'',
				'-webkit-filter': '',
				'-moz-filter': '',
				'-o-filter': '',
				'-ms-filter': ''
			});
		}
	}


	// 카테고리 펼쳐보기
	$(document).on('click', '.js_slide_cate_more', function(e) {
		e.preventDefault();
		var su = $(this).closest('.js_slide_cate');
		var if_open = su.hasClass('if_open');
		var if_no2 = su.hasClass('if_no2');
		var link = su.find('.ctg1').prop('href');
		if(if_no2 === true) { // 하위가 없다면 페이지 이동
			location.href = link;
			return;
		}
		$('.js_slide_comun').removeClass('if_open');
		if(if_open === true) { // 열려있다면
			if($(this).prop('href') == link) { // 이미 열려있는 상태에서 1차 카테고리를 선택 한 경우 페이지 이동
				location.href = link;
				return;
			}
			$('.js_slide_cate').removeClass('if_open');
		}
		else { // 닫혀있다면
			$('.js_slide_cate').removeClass('if_open');
			su.addClass('if_open');
			if($('.js_slide_cate.if_open').length > 0) scrolltoClass('.js_slide_cate.if_open', 0, $('.js_sliding_menu'));
		}
	});


	// 커뮤니티 펼쳐보기
	$(document).on('click', '.js_slide_comun_more', function(e) {
		e.preventDefault();
		var su = $(this).closest('.js_slide_comun');
		var if_open = su.hasClass('if_open');
		var if_no2 = su.hasClass('if_no2');
		var link = su.find('.ctg1').prop('href');
		if(if_no2 === true) { // 하위가 없다면 페이지 이동
			location.href = link;
			return;
		}
		$('.js_slide_cate').removeClass('if_open');
		if(if_open === true) { // 열려있다면
			if($(this).prop('href') == link) { // 이미 열려있는 상태에서 1차 카테고리를 선택 한 경우 페이지 이동
				location.href = link;
				return;
			}
			$('.js_slide_comun').removeClass('if_open');
		}
		else { // 닫혀있다면
			$('.js_slide_comun').removeClass('if_open');
			su.addClass('if_open');
			if($('.js_slide_comun.if_open').length > 0) scrolltoClass('.js_slide_comun.if_open', 0, $('.js_sliding_menu'));
		}
	});


	// 슬라이드 메뉴 내부의 슬라이더 요소
	var js_slide_footer_slider = {};

	// 최근 본 상품 :: 슬라이더
	$(window).on('load',function() {
		if($('.js_slide_footer_history_box').find('ul').length > 1) { // 슬라이더가 작동 가능한 상태에서 동작
			$('.js_slide_footer_history_box').find('ul').show();
			js_slide_footer_slider['history'] = $('.js_slide_footer_history_box .slide_item_list').bxSlider({
				auto: true,
				autoHover: false,
				pagerCustom: '.js_slide_footer_history_box_pager',
				controls: false,
				maxSlides:1,
				moveSlides:1,
				slideMargin : 0,
				onSliderLoad: function() { },
				onSlideBefore: function() { js_slide_footer_slider['history'].stopAuto(); },
				onSlideAfter: function() { js_slide_footer_slider['history'].startAuto(); }
			});
			js_slide_footer_slider['history'].stopAuto();
		}
	});

	// 찜한 상품 :: 슬라이더
	$(window).on('load',function() {
		if($('.js_slide_footer_wish_box').find('ul').length > 1) { // 슬라이더가 작동 가능한 상태에서 동작
			$('.js_slide_footer_wish_box').find('ul').show();
			js_slide_footer_slider['wish'] = $('.js_slide_footer_wish_box .slide_item_list').bxSlider({
				auto: true,
				autoHover: false,
				pagerCustom: '.js_slide_footer_wish_box_pager',
				controls: false,
				maxSlides:1,
				moveSlides:1,
				slideMargin : 0,
				onSliderLoad: function() { },
				onSlideBefore: function() { js_slide_footer_slider['wish'].stopAuto(); },
				onSlideAfter: function() { js_slide_footer_slider['wish'].startAuto(); }
			});
			js_slide_footer_slider['wish'].stopAuto();
		}
	});

	// 탭 메뉴 전환
	$(document).on('click', '.js_slide_footer_tab', function(e) {
		e.preventDefault();
		scrolltoClass('.js_slide_footer', 0, $('.js_sliding_menu')); // ios에서는 위로 올라가는 버그가 존재
		var type = $(this).data('type');
		slide_footer_box(type);
	});

	// 슬라이드 메뉴 오픈 처리 및 슬라이더 제어
	function slide_footer_box(type) {

		$('.js_slide_footer_tab_li').removeClass('hit');
		$('.js_slide_footer_tab[data-type="'+type+'"]').closest('.js_slide_footer_tab_li').addClass('hit');

		$('.js_slide_footer_box').hide();
		$('.js_slide_footer_'+type+'_box').show();

		// 슬라이드 내부 슬라이더를 모두 정지
		if(Object.keys(js_slide_footer_slider).length > 0) {
			$.each(js_slide_footer_slider, function(k, v) {
				if(typeof js_slide_footer_slider[k] == 'object') js_slide_footer_slider[k].stopAuto();
			});
		}

		// 슬라이드 내부 슬라이더중 해당되는 슬라이더 플레이
		if(typeof js_slide_footer_slider[type] == 'object') {
			js_slide_footer_slider[type].reloadSlider(); // 슬라이드 새로고침(없으면 height 0버그 발생)
			js_slide_footer_slider[type].startAuto();
		}
	}
</script>


<script>
	if ($(".sld_go_cate").hasClass("hit")) {
		$(".ctg_box").addClass("hit");
		$(".slide_wrap .slide_inner").css('height', 'auto');
	}

	$(".sld_go_cate").click(function () {

      $(".sld_go_brand").removeClass("hit");
      $(".slide_brand_area").removeClass("hit");
			
      $(".sld_go_cate").addClass("hit");
      $(".ctg_box").addClass("hit");
	});

 
  $(".sld_go_brand").click(function () {
		$(".slide_wrap .slide_inner").css('height', '100%');
      $(".sld_go_cate").removeClass("hit");
			$(".ctg_box").removeClass("hit");
			
			$(".sld_go_brand").addClass("hit");
      $(".slide_brand_area").addClass("hit");
    });

</script>