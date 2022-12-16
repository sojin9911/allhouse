<!-- ◆ 상품목록 레이아웃 -->
<div class="sub_section"><!-- 2차메뉴가 없을때 if_nonext -->
	<div class="layout_fix">
		<dl class="section_wrap">
			<dt class="left_side">
				<div class="left_menu" >
					<ul class="left-side_Tshirts">
						<li><h2>티셔츠</h2></li>
						<li>
							<input type="checkbox" class="left_checkbox_style" id="left_sd1">
							<label for="left_sd1">T 소단</label>
						</li>
						<li>
							<input type="checkbox" class="left_checkbox_style" id="left_dd1">
							<label for="left_dd1">T 대단</label>
						</li>
					</ul>
					<ul class="left-side_brand">
						<li><h3 class="left-side_tit">브랜드</h3><button class="left_plus-btn"></button></li>
						<li>
							<input type="checkbox" class="left_checkbox_style" id="left_pinkberry">
							<label for="left_pinkberry">핑크베리</label>
						</li>
						<li>
							<input type="checkbox" class="left_checkbox_style" id="left_hamaro">
							<label for="left_hamaro">하마로</label>
						</li>
						<li>
							<input type="checkbox" class="left_checkbox_style" id="left_hiChu">
							<label for="left_hiChu">하이츄</label>
						</li>
						<li>
							<input type="checkbox" class="left_checkbox_style" id="left_heartBaby">
							<label for="left_heartBaby">하트베이비</label>
						</li>
						<li>
							<input type="checkbox" class="left_checkbox_style" id="left_hyvaa">
							<label for="left_hyvaa">휘바</label></li>
					</ul>
					<div class="left-side_price">
						<h3 class="left-side_tit">금액</h3>
						<div class="middle">
							<div class="multi-range-slider">
								<!-- 진짜 슬라이더 -->
								<div class="multi-slider_value">
									<p><span id="value-left"></span></p>
									<p><span id="value-right"></span></p>
								</div>


								<input type="range" id="input-left" min="0" max="100000" value="0" />
								<input type="range" id="input-right" min="0" max="100000" value="100000" />

									<!-- 커스텀 슬라이더 -->
									<div class="slider">
										<div class="track"></div>
										<div class="range"></div>
										<div class="thumb left"></div>
										<div class="thumb right"></div>
									</div>
								</div>
								<script>
										const inputLeft = document.getElementById("input-left");
										const inputRight = document.getElementById("input-right");

										const thumbLeft = document.querySelector(".slider > .thumb.left");
										const thumbRight = document.querySelector(".slider > .thumb.right");
										const range = document.querySelector(".slider > .range");

										const setLeftValue = () => {
											const _this = inputLeft;
											const [min, max] = [parseInt(_this.min), parseInt(_this.max)];
											
											// 교차되지 않게, 1을 빼준 건 완전히 겹치기보다는 어느 정도 간격을 남겨두기 위해.
											_this.value = Math.min(parseInt(_this.value), parseInt(inputRight.value) - 1);
											
											// input, thumb 같이 움직이도록
											const percent = ((_this.value - min) / (max - min)) * 100;
											thumbLeft.style.left = percent + "%";
											range.style.left = percent + "%";
										};

										const setRightValue = () => {
											const _this = inputRight;
											const [min, max] = [parseInt(_this.min), parseInt(_this.max)];
											
											// 교차되지 않게, 1을 더해준 건 완전히 겹치기보다는 어느 정도 간격을 남겨두기 위해.
											_this.value = Math.max(parseInt(_this.value), parseInt(inputLeft.value) + 1);
											
											// input, thumb 같이 움직이도록
											const percent = ((_this.value - min) / (max - min)) * 100;
											thumbRight.style.right = 100 - percent + "%";
											range.style.right = 100 - percent + "%";
										};

										inputLeft.addEventListener("input", setLeftValue);
										inputRight.addEventListener("input", setRightValue);

										//최소금액 뜨게 하는 스크립트
										var slider_min = document.getElementById("input-left");
										var output_min = document.getElementById("value-left");
										output_min.innerHTML = slider_min.value;

										slider_min.oninput = function() {
												output_min.innerHTML = this.value;
											}
										//최대금액 뜨게 하는 스크립트
										var slider_max = document.getElementById("input-right");
										var output_max = document.getElementById("value-right");
										output_max.innerHTML = slider_max.value;

										slider_max.oninput = function() {
											output_max.innerHTML = this.value;
											}
								</script>
						</div>
						<div class="left-side_search">
							<div class="left_search-flex">
								<input type="text" placeholder="결과 내 검색">
								<a href="#" class="left_search-btn">검색</a>
							</div>

							<button class="left_search-reset">검색초기화</button>
						</div>
						
					</div>
				</div>
			</dt>
			<dd class="right_side">
				<?php
				/*
					$category_info -> 해당페이지의 카테고리 정보 => (/program/product.list.php에서 지정)
				*/
				// 카테고리 배너
				if(
					$category_info['c_img_top_banner_use'] == 'Y' &&
					isset($category_info['c_img_top_banner']) &&
					file_exists(IMG_DIR_CATEGORY_ROOT.$category_info['c_img_top_banner'])
				) {
				?>
					<!-- ◆ 카테고리 : 상단배너 (없으면 전체 숨김) -->
					<div class="sub_visual">
						<div class="hide">
							<!-- [PC]서브 : 카테고리별 상단배너 (1590 x free) -->
							<?php if($category_info['c_img_top_banner_target'] != '_none' && $category_info['c_img_top_banner_link']) { ?><a href="<?php echo $category_info['c_img_top_banner_link']; ?>" target="<?php echo $category_info['c_img_top_banner_target']; ?>"><?php } ?>
								<img src="<?php echo IMG_DIR_CATEGORY_URL.$category_info['c_img_top_banner']; ?>" alt="" />
							<?php if($category_info['c_img_top_banner_target'] != '_none' && $category_info['c_img_top_banner_link']) { ?></a><?php } ?>
						</div>
					</div>
					<!-- /카테고리 : 상단배너 -->
				<?php } ?>



				<?php
				// 타입별 상단 배너

				if( !empty($_event) && $_event == 'type' && !empty($typeuid)){

					// 타입상단 배너를 가져온다.
					$product_type_info = _MQ("select *from smart_display_type_set where dts_uid = '".$typeuid."' ");

					if(
						$product_type_info['dts_img_top_banner_use'] == 'Y' &&
						!empty($product_type_info['dts_img_top_banner']) &&
						file_exists(IMG_DIR_CATEGORY_ROOT.$product_type_info['dts_img_top_banner'])
					) {
				?>
					<!-- ◆ 타입별: 상단배너 (없으면 전체 숨김) -->
					<div class="sub_visual">
						<div class="">
							<!-- [PC]서브 : 타입별 상단배너 (1590 x free) -->
							<?php if($product_type_info['dts_img_top_banner_target'] != '_none' && $product_type_info['dts_img_top_banner_link']) { ?><a href="<?php echo $product_type_info['dts_img_top_banner_link']; ?>" target="<?php echo $product_type_info['dts_img_top_banner_target']; ?>"><?php } ?>
								<img src="<?php echo IMG_DIR_CATEGORY_URL.$product_type_info['dts_img_top_banner']; ?>" alt="" />
							<?php if($product_type_info['dts_img_top_banner_target'] != '_none' && $product_type_info['dts_img_top_banner_link']) { ?></a><?php } ?>
						</div>
					</div>
					<!-- /타입별: : 상단배너 -->
				<?php } } ?>



				<?php
				// 베스트 아이템
				if($category_info['c_best_product_view'] == 'Y' && isset($cuid)) {
					/* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
					$BestItem = _MQ_assoc("
						select
							*
							,if(p_soldout_chk='N',p_stock,0) as p_stock
						from
							`smart_product` as p left join
							`smart_product_category_best` as pctb on(p.p_code = pctb.pctb_pcode)
						where (1) and
							p_view = 'Y' and p_option_valid_chk = 'Y' and
							pctb_cuid = '{$cuid}'
						order by
							pctb_idx asc
					");
					if(count($BestItem) > 0) {

						// 임시보기 출력개수 보정처리
						if($_COOKIE['temp_skin']) {
							$SkinInfoColArr = explode(',', $SkinInfo['category']['pc_best_depth']);
							if(!in_array($category_info['c_best_product_display'], $SkinInfoColArr)) $category_info['c_best_product_display'] = $SkinInfo['category']['pc_best_depth_default'];
						}else{
							// {{{스킨유형별개수설정}}}
							$ActiveColList = array();
							$tempSkinInfo = SkinInfo('category');
							if($tempSkinInfo['pc_best_depth']) $ActiveColList = explode(',', $tempSkinInfo['pc_best_depth']); // pc_best_depth or mo_best_depth
							$ActiveColListDefault = $tempSkinInfo['pc_best_depth_default']; // pc_best_depth_default or mo_best_depth_default
							if(count($ActiveColList) > 0) {
								$FindDefaultKeyArr = array_flip($ActiveColList);
								if(in_array($ActiveColListDefault, $ActiveColList)) {
									unset($FindDefaultKeyArr[$ActiveColListDefault]);
									$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
								}
							}

						}

						if(in_array($category_info['c_best_product_display'], $ActiveColList)) $item_list_class = ' if_col'.$category_info['c_best_product_display']; // 기본4단 이외 3단, 5단일 경우 클래스 변경
				?>
					<!-- ◆ 카테고리 : 베스트 (없으면 전체 숨김) -->
					<div class="sub_best">
						<div class="hide">
							<!--타이틀 -->
							<div class="best_title"><strong>BEST</strong></div>
							<!-- 롤링영역 -->
							<div class="rolling_box">
								<!-- ◆ 상품리스트 : 기본 6단 / 5단 if_col5  -->
								<div class="item_list<?php echo (isset($item_list_class) && $item_list_class != ''?$item_list_class:null); ?>">
									<ul class="js_list_best_slide_tmp">
										<?php
										foreach($BestItem as $bi_k=>$bi_v) {
											if($bi_k >= $category_info['c_best_product_display']) continue;
										?>
											<li>
												<?php 
													$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
													$locationFile = basename(__FILE__); // 파일설정
													$k = $bi_k; $v = $bi_v;
													include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
												?>
											</li>
										<?php } ?>
										<?php
										if($category_info['c_best_product_display'] > count($BestItem)) {
											for($i=0; $i<$category_info['c_best_product_display']-count($BestItem); $i++) {
										?>
											<li><div class="item_box"><div class="thumb"><div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="" /></div></div></div></li>
										<?php }} ?>
									</ul>
									<div class="js_list_best_slide" style="display: none;">
										<ul>
											<?php
											$BestNum = 1;
											foreach($BestItem as $bi_k=>$bi_v) {
												$BestNum++;
												if($bi_k > 0 && ($bi_k%$category_info['c_best_product_display']) === 0) {
													echo '</ul><ul>';
													$BestNum = 1;
												}
											?>
												<li>
													<?php 
														$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
														$locationFile = basename(__FILE__); // 파일설정
														$k = $bi_k; $v = $bi_v;
														include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
													?>
												</li>
											<?php } ?>
											<?php
											if($category_info['c_best_product_display'] > $BestNum && $BestNum > 0) {
												for($i=0; $i<$category_info['c_best_product_display']-$BestNum; $i++) {
											?>
												<li><div class="item_box"><div class="thumb"><div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="" /></div></div></div></li>
											<?php }} ?>
										</ul>
									</div>
								</div>
								<!-- / ◆ 상품리스트 -->

								<?php if(count($BestItem) > $category_info['c_best_product_display']) { ?>
									<!-- 이전다음 버튼 (롤링 없으면 숨김) -->
									<span class="prevnext prev"><a href="#none" class="js_list_best_slide_prev" title="이전"><span class="icon off"></span><span class="icon over"></span></a></span>
									<span class="prevnext next"><a href="#none" class="js_list_best_slide_next" title="다음"><span class="icon off"></span><span class="icon over"></span></a></span>

									<script type="text/javascript">
										var BestItemSlide = $('.js_list_best_slide');
										function BestItemSlideOption() {
											$('.js_list_best_slide_tmp').show();
											$('.js_list_best_slide').hide();

											var BestItemSlideMargin = $('.js_list_best_slide_tmp').find('.item_box').css('margin-left').replace('px', '')*1;
											var BestItemSlideWidth = $('.js_list_best_slide_tmp').outerWidth();
											$('.js_list_best_slide').css('width', BestItemSlideWidth+BestItemSlideMargin);

											$('.js_list_best_slide_tmp').hide();
											$('.js_list_best_slide').show();
											return {
												auto: false,
												autoHover: false,
												controls: false,
												useCSS: false,
												minSlides: 1,
												moveSlides: 1,
												slideMargin: BestItemSlideMargin,
												slideWidth: BestItemSlideWidth,
												holdWidth: BestItemSlideWidth, // LDD: 2018-01-09 새롭게 추가된 옵션(자동 크기 변경을 차단하고 지정값으로 강제로 맞춘다)
												onSliderLoad: function() { },
												onSlideBefore: function() { BestItemSlide.stopAuto(); },
												onSlideAfter: function() { BestItemSlide.startAuto(); }
											};
										}
										$(function() {
											BestItemSlide.bxSlider(BestItemSlideOption());
										});

										$(document).on('click', '.js_list_best_slide_prev', function(e) {
											e.preventDefault();
											if(typeof BestItemSlide == 'object') BestItemSlide.goToPrevSlide();
										});
										$(document).on('click', '.js_list_best_slide_next', function(e) {
											e.preventDefault();
											if(typeof BestItemSlide == 'object') BestItemSlide.goToNextSlide();
										});

										$(window).resize(function() {
											if(typeof BestItemSlide == 'object') {
												BestItemSlide.destroySlider();
												BestItemSlide.bxSlider(BestItemSlideOption());
												//BestItemSlide.startAuto();
											}
										});
									</script>
								<?php } ?>
							</div>
						</div>
					</div>
					<!-- /카테고리 : 베스트 -->
				<?php }} ?>



				<?php
				// 임시보기 출력개수 보정처리
				if($_COOKIE['temp_skin']) {
					$SkinInfoColArr = explode(',', $SkinInfo['category']['pc_list_depth']);
					if(!in_array($category_info['c_list_product_display'], $SkinInfoColArr)) $category_info['c_list_product_display'] = $SkinInfo['category']['pc_list_depth_default'];
				}
				$ActiveListCol = $category_info['c_list_product_display'];
				if(!$ActiveListCol) $ActiveListCol = 6;

				// {{{스킨유형별개수설정}}}
				$ActiveColList = array();
				$tempSkinInfo = SkinInfo('category');
				if($tempSkinInfo['pc_list_depth']) $ActiveColList = explode(',', $tempSkinInfo['pc_list_depth']); // pc_list_depth or mo_list_depth
				$ActiveColListDefault = $tempSkinInfo['pc_list_depth_default']; // pc_list_depth_default or mo_list_depth_default
				if(count($ActiveColList) > 0) {
					$FindDefaultKeyArr = array_flip($ActiveColList);
					if(in_array($ActiveColListDefault, $ActiveColList)) {
						unset($FindDefaultKeyArr[$ActiveColListDefault]);
						$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
					}
				}

				$ActiveListColClass = '';
				if($list_type == 'list') $ActiveListCol = '1';
				if(in_array($ActiveListCol, $ActiveColList)) $ActiveListColClass = ' if_col'.$ActiveListCol; // 기본6단 이외 5일 경우 클래스 변경
				?>
				<!-- ◆ 상품리스트 -->
				<div class="sub_item" id="total_cnt">
					<div class="">
						<!-- 리스트 제어 -->
						<div class="item_list_ctrl">
							<div class="total">전체 상품 <strong><?php echo ($category_info['c_list_product_view'] == 'N'?'0':number_format($TotalCount)); ?></strong>개</div>
							<div class="ctrl_right">
								<!-- 리스트 정렬 -->
								<div class="range">
									<ul>
										<!-- 활성화시 hit클래스 추가 -->
										<li<?php echo (!$_order || $_order == ''?' class="hit"':null); ?>#total_cnt><a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'', 'listpg'=>1)); ?>" class="btn">기본순</a></li>
										<li<?php echo ($_order == 'sale'?' class="hit"':null); ?>#total_cnt><a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'sale', 'listpg'=>1)); ?>" class="btn">인기순</a></li>
										<li<?php echo ($_order == 'date'?' class="hit"':null); ?>#total_cnt><a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'date', 'listpg'=>1)); ?>" class="btn">등록일순</a></li>
										<li<?php echo ($_order == 'price_desc'?' class="hit"':null); ?>#total_cnt><a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'price_desc', 'listpg'=>1)); ?>" class="btn">높은 가격순</a></li>
										<li<?php echo ($_order == 'price_asc'?' class="hit"':null); ?>#total_cnt><a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'price_asc', 'listpg'=>1)); ?>" class="btn">낮은 가격순</a></li>
										<li<?php echo ($_order == 'pname'?' class="hit"':null); ?>#total_cnt><a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'pname', 'listpg'=>1)); ?>" class="btn">상품명순</a></li>
									</ul>
								</div>
								<?php if(empty($_event) || $_event == '') { // 이벤트 리스트가 아닌경우 ?>
									<div class="select">
										<div class="this_ctg ">
											<!-- 여기에 선택한 값이 나타남 -->
											<div class="btn" onclick="return false;"><?php echo $listmaxcount; ?>개씩 보기</div>
											<!-- <a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>$listmaxcount, 'listpg'=>1)); ?>#total_cnt" class="btn"><?php echo $listmaxcount; ?>개씩 보기</a> -->
											<div class="open_ctg">
												<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>20, 'listpg'=>1)); ?>#total_cnt" class="option">20개씩 보기</a>
												<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>40, 'listpg'=>1)); ?>#total_cnt" class="option">40개씩 보기</a>
												<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>60, 'listpg'=>1)); ?>#total_cnt" class="option">60개씩 보기</a>
												<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>80, 'listpg'=>1)); ?>#total_cnt" class="option">80개씩 보기</a>
											</div>
										</div>
										<script>
											$(document).on('click','.this_ctg .btn',function(){
												var targetClass = '.this_ctg'; // 클릭 시 타겟이 되는 클래스 (css 선택자 지정할때처럼 선택 지정자)
												var addClassName = 'if_open'; // 클릭 시 추가되는 클래스 (명만 써주시면됩니다.)
												var chk = $(targetClass).hasClass(addClassName);
												if( chk == false){ $(targetClass).addClass(addClassName); }
												else {  $(targetClass).removeClass(addClassName);  }
											});
										</script>
									</div>
								<?php } ?>
							</div>
						</div>
						<!-- / 리스트 제어 -->



						<?php

						// 이벤트가 타입일경우 기본 진열을가져온다.
						if($_event == 'type'){
							$displayTypeInfo = _MQ(" select * from `smart_display_type_set` where (1) and dts_uid = '{$typeuid}' ");
							$ActiveListCol = $displayTypeInfo['dts_list_product_display'];
							if(!$ActiveListCol) $ActiveListCol = 6;

							// {{{스킨유형별개수설정}}}
							$ActiveColList = array();
							$tempSkinInfo = SkinInfo('category');
							if($tempSkinInfo['pc_list_depth']) $ActiveColList = explode(',', $tempSkinInfo['pc_list_depth']); // pc_list_depth or mo_list_depth
							$ActiveColListDefault = $tempSkinInfo['pc_list_depth_default']; // pc_list_depth_default or mo_list_depth_default
							if(count($ActiveColList) > 0) {
								$FindDefaultKeyArr = array_flip($ActiveColList);
								if(in_array($ActiveColListDefault, $ActiveColList)) {
									unset($FindDefaultKeyArr[$ActiveColListDefault]);
									$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
								}
							}

							$ActiveListColClass = '';
							if($list_type == 'list') $ActiveListCol = '1';
							if(in_array($ActiveListCol, $ActiveColList)) $ActiveListColClass = ' if_col'.$ActiveListCol; // 기본4단 이외 5, 2, 1일 경우 클래스 변경
						}


						// 상품리스트 호출
						include(OD_SITE_SKIN_ROOT.'/ajax.product.list.php');
						?>



						<?php if(empty($_event) || $_event == '') { // 이벤트 리스트가 아닌경우 ?>
							<!-- 페이지네이트 (상품목록 형) -->
							<div class="c_pagi">
								<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
							</div>
							<!-- / 페이지네이트 (상품목록 형) -->
						<?php } ?>
					</div>
				</div>
				<!-- /상품리스트 -->
			</dd>
		</dl>
	</div>
</div>
<!-- / 상품목록 레이아웃 -->