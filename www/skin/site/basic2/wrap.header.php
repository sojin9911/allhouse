<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// 기본파일 include
include_once(OD_PROGRAM_ROOT.'/inc.header.php'); // 스킨 내부파일로 직접 include 하지 마세요.
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>

<!-- ◆ 탑 (공통) -->
<div class="top">
	<div class="layout_fix">
		<div class="top_wrap clearfix">
			<ul class="left_menu">
				<li><a href="#" id="bookmark"><i class="xi-star"></i>즐겨찾기</a></li>
			</ul>
			<ul class="right_menu">
				<?php if(is_login()) { ?>
					<!-- 로그인후 -->
					<li>
						<?php
							// == 등급전체 정보를 가져온다.
							$getGroupInfo = getGroupInfo();
						?>
						<p class="btn"><?php echo $mem_info['in_name']; ?> 님 환영합니다!</p>
					</li>
					<li>
						<a href="/?pn=mypage.main" class="btn"><span class="tx">마이페이지</span></a>
						<div class="open_box if_my">


							<div class="inner">

								<?php // {{{회원등급추가}}}   ?>
								<div class="about_level">
									<a href="/?pn=mypage.main" class="upper_link" title=""><!--img src="/skin/site/basic/images/c_img/blank.gif" alt="" /--></a>
									<div class="level_img">
										<img src="<?php echo get_img_src($getGroupInfo[$mem_info['in_mgsuid']]['icon'],IMG_DIR_ICON); ?>" alt="" />
									</div>
									<div class="level_name"><?php echo $getGroupInfo[$mem_info['in_mgsuid']]['name'] ?></div>
									<div class="name"><?php echo $mem_info['in_name']; ?>님</div>
								</div>
								<?php // {{{회원등급추가}}} ?>

								<div class="my_data">
									<a href="/?pn=mypage.order.list" class="data">
										<span class="tx">주문</span>
										<span class="num"><?php echo number_format(get_order_ing_cnt(array('결제대기', '결제완료', '배송대기', '배송준비', '배송중'))); ?></span>
									</a>
									<a href="/?pn=mypage.point.list" class="data">
										<span class="tx">적립금</span>
										<span class="num"><?php echo number_format($mem_info['in_point']); ?></span>
									</a>
								</div>
								<div class="sub_menu">
									<a href="/?pn=mypage.wish.list" class="menu"><span class="tx">찜한 상품</span></a>
									<?php // 내부패치 68번줄 kms 2019-11-05 ?>
									<a href="/?pn=mypage.inquiry.list" class="menu"><span class="tx">1:1 온라인 문의</span></a>
									<a href="/?pn=mypage.eval.list" class="menu"><span class="tx">상품후기</span></a>
									<a href="/?pn=mypage.qna.list" class="menu"><span class="tx">상품문의</span></a>
									<a href="/?pn=mypage.modify.form" class="menu"><span class="tx">정보수정</span></a>
								</div>
							</div>
						</div>
					</li>
					<li>
						<a href="/?pn=message_list" class="btn"><span class="tx">쪽지함</span></a>
					</li>
					<li>
						<a href="/?pn=shop.cart.list" class="btn cart"><span class="tx">장바구니<strong class="glb_cart_cnt js_cart_cnt">(<?php echo $cart_cnt; ?>/<?php echo $cart_manual_cnt; ?>)</strong></span></a>
					</li>
					<li>
						<a href="/?pn=mypage.wish.list" class="btn"><span class="tx">찜한 상품</span></a>
					</li>
					<li>
					<a href="/?pn=board.list&_menu=notice" class="btn"><span class="tx">공지사항</span></a>
					</li>
					<li><a href="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php?_mode=logout" class="btn"><span class="tx">로그아웃</span></a></li>
					<?php } else { ?>
						<!-- 로그인전 -->
					<li><a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="btn log"><span class="tx">로그인</span></a></li>
					<li><a href="/?pn=join_method" class="btn"><span class="tx">회원가입</span></a></li>
				<?php } ?>
				<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
				<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
			</ul>
		</div>
	</div>
</div>
<!-- /탑 (공통) -->



<!-- ◆헤더 (공통) -->
<div class="header">
	<ul class="ul">
		<li class="li this_logo">
			<div class="layout_fix">
					<div class="logo_banner">
						<div class="swiper-container">
							<div class="swiper-wrapper">
								<div class="swiper-slide"><img src="<?php echo $SkinData['skin_url'] ?>/images/skin/logo_banner.jpg"></div>
								<div class="swiper-slide"><img src="<?php echo $SkinData['skin_url'] ?>/images/skin/logo_banner.jpg"></div>
								<div class="swiper-slide"><img src="<?php echo $SkinData['skin_url'] ?>/images/skin/logo_banner.jpg"></div>
							</div>
							<!-- 네비게이션 지정 -->
							<div class="lb_btn_np clearfix">
								<div class="sb-prev">
									<img src="<?php echo $SkinData['skin_url'] ?>/images/skin/cate_img/arrow_right_gray.png" alt="이전">
								</div>
								<div class="sb-next">
									<img src="<?php echo $SkinData['skin_url'] ?>/images/skin/cate_img/arrow_right_gray.png"  alt="다음">
								</div>
							</div>
						</div>
					</div>
				<!-- [PC]공통 : 상단 로고 (가로 200 이하 * 세로 25 이하 ) -->
				<?php
				$TopLogo = info_banner($_skin.',site_top_logo', 1, 'data'); // [PC]공통 : 상단 로고 (가로 265 이하 x 세로 80 이하, 1개)
				if(count($TopLogo) > 0) {
				?>
					<div class="logo_box"><a href="<?php echo ($TopLogo[0]['b_link']?$TopLogo[0]['b_link']:'/'); ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/main_logo.png"/></a></div>
				<?php } else { ?>
					<div class="logo_box"><a href="/"><img src="<?php echo $SkinData['skin_url']; ?>/images/sample/logo.png" alt="<?php echo addslashes($siteInfo['s_glbtlt']); ?>" /></a></div>
				<?php } ?>
			</div>
		</li>
		<!-- !!!! 임의로 적어둔거에요 !!!! -->
		<li class="li this_nav">
			<div class="layout_fix">
				<!-- ◆카테고리네비 -->
				<div class="nav">
					<ul>
						<!-- 해당메뉴 hit -->
							<li>
								<a href="/?pn=product.list&amp;cuid=393" class="ctg1">
									<span class="tx">낱장Premium<span class="shape"></span></span>
									<div class="next_box">
										<div class="layout_fix">
											<div class="inner">
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=409" class="ctg2">T대단 (쥬니어)</a></dt>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=410" class="ctg2">T 소단 (토들러)</a></dt>
												</dl>
											</div>
										</div>
										<div class="bg"></div>
									</div>
								</li>
								<li>
									<a href="/?pn=product.list&amp;cuid=398" class="ctg1">
										<span class="tx">TODAY<span class="shape"></span></span>
									</a>
									<div class="next_box">
										<div class="layout_fix">
											<div class="inner">
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=402" class="ctg2">J대단 (쥬니어)</a></dt>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=403" class="ctg2">J소단 (토들러)</a></dt>
												</dl>
											</div>
										</div>
										<div class="bg"></div>
									</div>
								</li>
								<li>
									<a href="/?pn=product.list&amp;cuid=407" class="ctg1">
										<span class="tx">Weekly Best<span class="shape"></span></span>
									</a>
									<div class="next_box">
										<div class="layout_fix">
											<div class="inner">
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=411" class="ctg2">스커트</a></dt>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=412" class="ctg2">원피스</a></dt>
												</dl>
											</div>
										</div>
										<div class="bg"></div>
									</div>
								</li>
								<li>
									<a href="/?pn=product.list&amp;cuid=147" class="ctg1">
										<span class="tx">실시간 신상<span class="shape"></span></span>
									</a>
									<div class="next_box">
										<div class="layout_fix">
											<div class="inner">
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=206" class="ctg2">P대단 (쥬니어)</a></dt>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=203" class="ctg2">P소단 (토들러)</a></dt>
														<dd><a href="/?pn=product.list&amp;cuid=208" class="ctg3">STILETTO HEEL</a></dd>
														<dd><a href="/?pn=product.list&amp;cuid=299" class="ctg3">PUMPS</a></dd>
														<dd><a href="/?pn=product.list&amp;cuid=300" class="ctg3">SLINGBACK</a></dd>
													</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=217" class="ctg2">레깅스/쫄바지</a></dt>
													<dd><a href="/?pn=product.list&amp;cuid=231" class="ctg3">WEDGE HEEL</a></dd>
													<dd><a href="/?pn=product.list&amp;cuid=292" class="ctg3">STILETTO HEEL</a></dd>
													<dd><a href="/?pn=product.list&amp;cuid=293" class="ctg3">OPEN TOE</a></dd>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=205" class="ctg2">청바지</a></dt>
												</dl>
											</div>
										</div>
										<div class="bg"></div>
									</div>
								</li>
								<li>
									<a href="/?pn=product.list&amp;cuid=1" class="ctg1">
										<span class="tx">50%이월특가<span class="shape"></span></span>
									</a>
									<div class="next_box">
										<div class="layout_fix">
											<div class="inner">
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=155" class="ctg2">S대단 (쥬니어)</a></dt>
													<dd><a href="/?pn=product.list&amp;cuid=404" class="ctg3">5~7CM</a></dd>
													<dd><a href="/?pn=product.list&amp;cuid=405" class="ctg3">8~10CM</a></dd>
													<dd><a href="/?pn=product.list&amp;cuid=406" class="ctg3">11CM ~</a></dd>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=219" class="ctg2">S소단 (토들러)</a></dt>
													<dd><a href="/?pn=product.list&amp;cuid=232" class="ctg3">SLEEPER 01</a></dd>
													<dd><a href="/?pn=product.list&amp;cuid=322" class="ctg3">SLEEPER 02</a></dd>
													<dd><a href="/?pn=product.list&amp;cuid=323" class="ctg3">SLEEPER 03</a></dd>
													<dd><a href="/?pn=product.list&amp;cuid=324" class="ctg3">SLEEPER 04</a></dd>
												</dl>
											</div>
										</div>
										<div class="bg"></div>
									</div>
								</li>
								<li>
									<a href="/?pn=product.list&amp;cuid=2" class="ctg1">
										<span class="tx">추천모음<span class="shape"></span></span>
									</a>
									<div class="next_box">
										<div class="layout_fix">
											<div class="inner">
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=145" class="ctg2">가방(지갑)</a></dt>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=195" class="ctg2">케이프</a></dt>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=197" class="ctg2">모자(두건)</a></dt>
												</dl>
												<dl>
													<dt><a href="/?pn=product.list&amp;cuid=198" class="ctg2">악세사리</a></dt>
												</dl>
																									
											</div>
										</div>
										<div class="bg"></div>
									</div>
								</li>
								<li>
									<a href="/?pn=product.list&amp;cuid=72" class="ctg1">
										<span class="tx">HOT SALE<span class="shape"></span></span>
									</a>
								</li>
								<li>
									<a href="/?pn=product.list&amp;cuid=168" class="ctg1">
										<span class="tx">주니어<span class="shape"></span></span>
									</a>
								</li>
													
						</ul>
				</div>
				<!-- !!!! 임의로 적어둔거에요 !!!! -->
					<ul class="hide">
						<!-- 해당메뉴 hit -->
						<?php foreach($AllCate as $k=>$v) { ?>
							<li<?php echo (isset($ActiveCate['cuid'][0]) && $ActiveCate['cuid'][0] == $v['c_uid']?' class="hit"':null); ?>>
								<a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="ctg1">
									<span class="tx"><?php echo $v['c_name']; ?><span class="shape"></span></span>
								</a>
								<?php if(count($v['sub']) > 0) { ?>
									<!-- 2차+3차 하나도없으면 노출금지 -->
									<div class="next_box">
										<div class="layout_fix">
											<div class="inner">
												<!-- 2차 dl반복 -->
												<?php
												foreach($v['sub'] as $kk=>$vv) {
													if($kk > 0 && $kk%6 == 0) echo '</div><div class="inner">';
												?>
													<dl>
														<dt><a href="/?pn=product.list&cuid=<?php echo $vv['c_uid']; ?>" class="ctg2"><?php echo $vv['c_name']; ?></a></dt><!-- 2차 dt -->
														<?php
														if(count($vv['sub']) > 0) {
															foreach($vv['sub'] as $kkk=>$vvv) {
														?>
															<dd><a href="/?pn=product.list&cuid=<?php echo $vvv['c_uid']; ?>" class="ctg3"><?php echo $vvv['c_name']; ?></a></dd><!-- 3차 dd반복/없으면 dd숨김 -->
														<?php }} ?>
													</dl>
												<?php } ?>
											</div>
										</div>
										<div class="bg"></div>
									</div>
								<?php } ?>
							</li>
						<?php } ?>
					</ul>
				</div>
				<!-- / 카테고리네비 -->
			</div>
		</li>
		<li class="li this_side hide">
			<div class="layout_fix">
				<?php
				$TopEventList = _MQ_assoc(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_list_product_view = 'Y' order by dts_idx asc ");
				if(count($TopEventList) > 0) {
				?>
				<!-- 바로가기메뉴 -->
				<ul class="nav_box">
					<?php foreach($TopEventList as $k=>$v) { ?>
						<li><a href="/?pn=product.list&_event=type&typeuid=<?php echo $v['dts_uid']; ?>" class="btn"><?php echo $v['dts_name']; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>

				<!-- 통합검색 -->
				<div class="search">
					<form action="/" method="GET" onsubmit="return searchFunction(this);">
						<input type="hidden" name="pn" value="product.search.list">
						<!-- 나타날때는 마우스 오버로 나오고, 나오고 나면 if_open 클래스를 주어 계속열려있도록 하고, 닫기 버튼으로 닫힐 수 있도록 -->
						<div class="search_form">
							<input type="text" name="search_word" value="<?php echo (isset($search_word)?$search_word:null); ?>" class="input_search search_word" placeholder="Search products" /><!-- 키보드 엔터키로 검색되도록 -->
							<a href="#none" class="btn_close js_header_search_close" title="검색창 닫기"></a>
						</div>
						<input type="submit" value="Search" class="btn_search" title="검색" />
					</form>
					<script type="text/javascript">
						function searchFunction(target) {
							if($(target).find('.search_word').val() == '' || $(target).find('.search_word').val() == '상품을 검색하세요') {
								alert('검색할 단어를 입력하세요');
								$(target).find('.search_word').focus();
								return false;
							}
							return true;
						}
						$(document).on('focusin focusout', '.search_word', function(e) {
							e.preventDefault();
							if(e.type == 'focusin') $(this).closest('.search').addClass('if_open');
							else if(e.type == 'focusout') $(this).closest('.search').removeClass('if_open');
						});
						$(document).on('click', '.js_header_search_close', function(e) {
							e.preventDefault();
							var su = $(this);
							su.closest('.search').removeClass('if_open');
							su.closest('.search_form').hide();
							setTimeout(function() {
								su.closest('.search_form').removeAttr('style');
							}, 400);
						});
					</script>
				</div>
			</div>
		</li>
	</ul>
</div>
<!-- /헤더 (공통) -->

<!-- 카테고리 (공통) -->
<div class="hd_cate_wrap">
	<div class="layout_fix">
		<div class="hd_cate_box">
			<ul class="clearfix">
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_01.png" alt="티셔츠"><p>티셔츠</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_02.png" alt="바지류"><p>바지류</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_03.png" alt="상하 SET"><p>상하 SET</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_04.png" alt="원피스 치마"><p>원피스 치마</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_05.png" alt="점퍼 자켓"><p>점퍼 자켓</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_06.png" alt="코트 망토"><p>코트 망토</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_07.png" alt="남방 Blouse"><p>남방 Blouse</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_08.png" alt="조끼"><p>조끼</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_09.png" alt="가디건"><p>가디건</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_10.png" alt="아동내의"><p>아동내의</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_11.png" alt="아동신발"><p>아동신발</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_12.png" alt="매장용품"><p>매장용품</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_13.png" alt="한복"><p>한복</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_14.png" alt="우비 우산"><p>우비 우산</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_15.png" alt="유아복"><p>유아복</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_16.png" alt="잡화"><p>잡화</p></a></li>
				<li><a href=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cate_img/ico_cate_17.png" alt="엄마아빠용"><p>엄마아빠용</p></a></li>
			</ul>
		</div>
	</div>
</div>
<!-- /카테고리 (공통) -->

<!-- 왼쪽 퀵 메뉴 (공통) -->
<div class="left_quick">
	<div class="quick_s_cont">
		<h3>퀵검색</h3>
		<form action="">
			<div class="qsc_cont_box">
				<table>
					<th>브랜드</th>
					<td>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
					</td>
				</table>
			</div>
			<div class="qsc_cont_box">
				<table>
					<th>카테고리</th>
					<td>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
					</td>
				</table>
			</div>
			<div class="qsc_cont_box">
				<table>
					<th>가격</th>
					<td>
						<div class="qsc_cb_wrap for_bottom_margin">
							<input type="text"> 원 ~
						</div>
						<div class="qsc_cb_wrap">
							<input type="text"> 원
						</div>
					</td>
				</table>
			</div>
			<div class="qsc_cont_box">
				<table>
					<th>색상</th>
					<td>
						<div class="qsc_cb_wrap">
							<select name="" id="">
								<option value="">브랜드1</option>
								<option value="">브랜드2</option>
								<option value="">브랜드3</option>
							</select>
						</div>
					</td>
				</table>
			</div>
			<div class="qsc_cont_box">
				<table>
					<th>검색어</th>
					<td>
						<div class="qsc_cb_wrap">
							<input type="text">
						</div>
					</td>
				</table>
			</div>
			<div class="qsc_cont_box">
				<table>
					<th>혜택/조건</th>
					<td>
						<div class="qsc_cb_wrap lq_chk_wrap">
							<span>
								<input type="checkbox" name="" id="free_deliv">
								<label for="free_deliv">무료배송</label>
							</span>
							<span>
								<input type="checkbox" name="" id="new_reg_prod">
								<label for="new_reg_prod">최근등록상품</label>	
							</span>
						</div>
					</td>
				</table>
			</div>
			<div class="lq_btn_wrap">
				<button>검색</button>
			</div>
		</form>
	</div>
	<div class="lq_onoff_btn"></div>
</div>
<!-- 왼쪽 퀵 메뉴 (공통) -->

<!-- 오른쪽 fix 메뉴 (공통) -->
<div class="right_quick">
	<ul>
		<li>
			<a href="/?pn=product.brand_list">
				<span>BRAND</span>
				<p>전체보기 <img src="<?php echo $SkinData['skin_url'] ?>/images/skin/cate_img/arrow_right_gray.png" alt="전체보기"></p>
			</a>
		</li>
		<li>
			<p>TODAY</p>
			<p>VIEW</p>
		</li>
		<li>
			<div class="go_top_wrap">
				<img src="<?php echo $SkinData['skin_url'] ?>/images/skin/cate_img/arrow_top_white.png" alt="상단이동">
				<p>TOP</p>
			</div>
		</li>
	</ul>
</div>
<!-- /오른쪽 fix 메뉴 (공통) -->

<script>
	$(document).ready(function(){


    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
					$('.right_quick').addClass('change_fixed');
            $('.scrollToTop').fadeIn();
        } else {
            $('.scrollToTop').fadeOut();
						$('.right_quick').removeClass('change_fixed');
        }
    });

    //Click event to scroll to top
    $('.go_top_wrap').click(function(){
        $('html, body').animate({scrollTop : 0},800);
        return false;
    });

		// $(document).ready(function(){
		// 		var currentPosition = parseInt($(".right_quick").css("top"));
		// 		$(window).scroll(function() {
		// 			var position = $(window).scrollTop(); 
		// 			var realposition = position - 200;
		// 			if ($(window).scrollTop() > 300) {
		// 				$(".right_quick").stop().animate({"top":realposition+currentPosition+"px"},300);
		// 			}else{
		// 				$(".right_quick").stop().animate({"top":position+currentPosition+"px"},300);
		// 			}
		// 		});
		// 	});



		new Swiper('.swiper-container', {
			autoplay: {
				delay: 3000,
				disableOnInteraction: true // 쓸어 넘기거나 버튼 클릭 시 자동 슬라이드 정지.
			},
  		loop: true,
			speed: 2000,
			navigation : {
				nextEl : '.sb-next', // 다음 버튼 클래스명
				prevEl : '.sb-prev', // 이번 버튼 클래스명
				},
			});

});
</script>

<script>
		
		$(".lq_onoff_btn").click(function () {
    if ($(".left_quick").hasClass("on")) {
      $(".left_quick").removeClass("on");
    } else {
      $(".left_quick").addClass("on");
    }
  });

</script>

<script>
	$(document).ready(function() {
    $('#bookmark').on('click', function(e) {
        var bookmarkURL = window.location.href;
        var bookmarkTitle = document.title;
        var triggerDefault = false;

        if (window.sidebar && window.sidebar.addPanel) {
            // Firefox version < 23
            window.sidebar.addPanel(bookmarkTitle, bookmarkURL, '');
        } else if ((window.sidebar && (navigator.userAgent.toLowerCase().indexOf('firefox') > -1)) || (window.opera && window.print)) {
            // Firefox version >= 23 and Opera Hotlist
            var $this = $(this);
            $this.attr('href', bookmarkURL);
            $this.attr('title', bookmarkTitle);
            $this.attr('rel', 'sidebar');
            $this.off(e);
            triggerDefault = true;
        } else if (window.external && ('AddFavorite' in window.external)) {
            // IE Favorite
            window.external.AddFavorite(bookmarkURL, bookmarkTitle);
        } else {
            // WebKit - Safari/Chrome
            alert((navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Cmd' : 'Ctrl') + '+D 키를 눌러 즐겨찾기에 등록하실 수 있습니다.');
        }

        return triggerDefault;
    });
});
</script>