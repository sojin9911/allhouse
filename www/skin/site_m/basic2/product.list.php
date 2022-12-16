<div class="section">
	<?php include_once(OD_PROGRAM_ROOT.'/product.top_nav.php'); // 상단 네비게이션 출력 ?>

	<?php
	/*
		$category_info -> 해당페이지의 카테고리 정보 => (/program/product.list.php에서 지정)
	*/
	// 카테고리 배너
	if(
		$category_info['c_img_top_mobile_banner_use'] == 'Y' &&
		!empty($category_info['c_img_top_mobile_banner']) &&
		file_exists(IMG_DIR_CATEGORY_ROOT.$category_info['c_img_top_mobile_banner'])
	) {
	?>
		<!-- ◆ 카테고리 : 상단배너 (없으면 전체 숨김) -->
		<div class="sub_visual">
			<!-- [PC]서브 : 카테고리별 상단배너 (1000 x free) -->
			<?php if($category_info['c_img_top_mobile_banner_target'] != '_none' && $category_info['c_img_top_mobile_banner_link']) { ?><a href="<?php echo $category_info['c_img_top_mobile_banner_link']; ?>" target="<?php echo $category_info['c_img_top_mobile_banner_target']; ?>"><?php } ?>
				<img src="<?php echo IMG_DIR_CATEGORY_URL.$category_info['c_img_top_mobile_banner']; ?>" alt="" />
			<?php if($category_info['c_img_top_mobile_banner_target'] != '_none' && $category_info['c_img_top_mobile_banner_link']) { ?></a><?php } ?>
		</div>
		<!-- /카테고리 : 상단배너 -->
		<!-- 모바일 배너자리 -->
	<?php } // 카테고리 배너 ?>


	<?php
	// 타입별 상단 배너
	if( !empty($_event) && $_event == 'type' && !empty($typeuid)){

		// 타입상단 배너를 가져온다.
		$product_type_info = _MQ("select *from smart_display_type_set where dts_uid = '".$typeuid."' ");
		if(
			$product_type_info['dts_img_top_mobile_banner_use'] == 'Y' &&
			!empty($product_type_info['dts_img_top_mobile_banner']) &&
			file_exists(IMG_DIR_CATEGORY_ROOT.$product_type_info['dts_img_top_banner'])
		) {

	?>
		<!-- ◆ 타입별: 상단배너 (없으면 전체 숨김) -->
		<div class="sub_visual">
			<!-- [PC]서브 : 타입별 상단배너 (1000 x free) -->
			<?php if($product_type_info['dts_img_top_mobile_banner_target'] != '_none' && $product_type_info['dts_img_top_mobile_banner_link']) { ?><a href="<?php echo $product_type_info['dts_img_top_mobile_banner_link']; ?>" target="<?php echo $product_type_info['dts_img_top_mobile_banner_target']; ?>"><?php } ?>
				<img src="<?php echo IMG_DIR_CATEGORY_URL.$product_type_info['dts_img_top_mobile_banner']; ?>" alt="" />
			<?php if($product_type_info['dts_img_top_mobile_banner_target'] != '_none' && $product_type_info['dts_img_top_mobile_banner_link']) { ?></a><?php } ?>
		</div>
		<!-- /타입별: : 상단배너 -->
	<?php } } ?>



	<?php
	// 베스트 아이템
	if($category_info['c_best_product_mobile_view'] == 'Y' && isset($cuid)) {
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
				$SkinInfoColArr = explode(',', $SkinInfo['category']['mo_best_depth']);
				if(!in_array($category_info['c_best_product_mobile_display'], $SkinInfoColArr)) $category_info['c_best_product_mobile_display'] = $SkinInfo['category']['mo_best_depth_default'];
			}else{
				// {{{스킨유형별개수설정}}}
				$ActiveColList = array();
				$tempSkinInfo = SkinInfo('category');
				if($tempSkinInfo['mo_best_depth']) $ActiveColList = explode(',', $tempSkinInfo['mo_best_depth']); // pc_best_depth or mo_best_depth
				$ActiveColListDefault = $tempSkinInfo['mo_best_depth_default']; // pc_best_depth_default or mo_best_depth_default
				if(count($ActiveColList) > 0) {
					$FindDefaultKeyArr = array_flip($ActiveColList);
					if(in_array($ActiveColListDefault, $ActiveColList)) {
						unset($FindDefaultKeyArr[$ActiveColListDefault]);
						$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
					}
				}
			}

			if(in_array($category_info['c_best_product_mobile_display'], $ActiveColList)) $item_list_class = 'if_col'.$category_info['c_best_product_mobile_display']; // 기본3단 이외 2단일 경우 클래스 변경
	?>
		<!-- ******************************************
		     카테고리 : 베스트 (없으면 전체 숨김)
		  -- ****************************************** -->
		<div class="sub_best">
			<!--타이틀 -->
			<div class="best_title">BEST ITEM</div>
			<!-- 롤링영역 -->
			<div class="rolling_box">
				<!-- ◆ 상품리스트 : 기본 2단 / 1단 if_col1  -->
				<div class="item_list<?php echo (isset($item_list_class) && $item_list_class != ''?' '.$item_list_class:null); ?> js_product_best_slide">
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<ul>
								<?php
								foreach($BestItem as $bi_k=>$bi_v) {
									if($bi_k > 0 && $bi_k%$category_info['c_best_product_mobile_display'] === 0) echo '</ul></div><div class="swiper-slide" style="display:none;"><ul>';
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
								if(count($BestItem) > 0 && count($BestItem)%$category_info['c_best_product_mobile_display'] > 0) {
									for($bf_i=0; $bf_i<$category_info['c_best_product_mobile_display']-(count($BestItem)%$category_info['c_best_product_mobile_display']); $bf_i++) {
								?>
									<li></li>
								<?php }} ?>
							</ul>
						</div>
					</div>
				</div>
				<!-- / ◆ 상품리스트 -->
			</div>


			<?php if(count($BestItem) > $category_info['c_best_product_mobile_display']) { ?>
				<!-- 롤링아이콘 (롤링이 1개일때는 숨김) (해당 롤링일때 active 추가) -->
				<div class="rolling_icon">
					<span class="lineup">
						<span class="js_product_best_slide_pager" style="display: initial;">
							<?php for($bip_i=0; $bip_i<count($BestItem)/$category_info['c_best_product_mobile_display']; $bip_i++) { ?>
								<a href="#none" class="icon<?php echo ($bip_i <= 0?' active':null); ?>"></a>
							<?php } ?>
						</span>
					</span>
				</div>
				<!-- 롤링아이콘 -->
				<script type="text/javascript">
					$(window).on('load',function(){
						$('.js_product_best_slide .swiper-slide').show();
						var product_best_slide = new Swiper('.js_product_best_slide', {
							pagination : ".js_product_best_slide_pager",
							effect: 'slide',
							slidesPerView: 1,
							paginationType : 'bullets',
							paginationClickable : true,
							autoplay : 4000,
							speed: 1000,
							parallax:true,
							autoplayDisableOnInteraction : false,
							loop : true,
							spaceBetween: 10, // 슬라이드간 간격
							bulletClass : 'icon',
							bulletActiveClass : 'active',
							paginationBulletRender: function (swiper, index, className) {
								return '<a href="#none" class="icon '+className+'"></a>';
							}
						});
						$(document).on('click', '.js_product_best_slide_prev', function(e) {
							e.preventDefault();
							product_best_slide.slidePrev();
						});
						$(document).on('click', '.js_product_best_slide_next', function(e) {
							e.preventDefault();
							product_best_slide.slideNext();
						});
					});
				</script>
			<?php } ?>
		</div>
		<!-- /카테고리 : 베스트 -->
	<?php }} // 베스트 아이템 ?>



	<?php
	// 임시보기 출력개수 보정처리
	if($_COOKIE['temp_skin']) {
		$SkinInfoColArr = explode(',', $SkinInfo['category']['mo_list_depth']);
		if(!in_array($category_info['c_list_product_mobile_display'], $SkinInfoColArr)) $category_info['c_list_product_mobile_display'] = $SkinInfo['category']['mo_list_depth_default'];
	}


	// {{{스킨유형별개수설정}}}
	$ActiveColList = array();
	$tempSkinInfo = SkinInfo('category');
	if($tempSkinInfo['mo_list_depth']) $ActiveColList = explode(',', $tempSkinInfo['mo_list_depth']); // pc_list_depth or mo_list_depth
	$ActiveColListDefault = $tempSkinInfo['mo_list_depth_default']; // pc_list_depth_default or mo_list_depth_default
	if(count($ActiveColList) > 0) {
		$FindDefaultKeyArr = array_flip($ActiveColList);
		if(in_array($ActiveColListDefault, $ActiveColList)) {
			unset($FindDefaultKeyArr[$ActiveColListDefault]);
			$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
		}
	}


	$ActiveListCol = $category_info['c_list_product_mobile_display'];
	if($list_type == 'list') $ActiveListCol = 1;
	if(in_array($ActiveListCol, $ActiveColList)) $ActiveListColClass = ' if_col'.$ActiveListCol; // 기본2단 이외 1일 경우 클래스 변경

	?>
	<!-- ******************************************
	    상품리스트
	  -- ****************************************** -->
	<div class="sub_item">
		<!-- 리스트 제어 -->
		<div class="item_list_ctrl" id="total_cnt">
			<ul>
				<li class="this_total hide"><div class="total"><strong><?php echo ($category_info['c_list_product_view'] == 'N'?'0':number_format($TotalCount)); ?></strong> Products</div></li>
				<li>
					<div class="select">
						<select onchange="location.href=this.value;">
							<option value="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'', 'listpg'=>1)); ?>#total_cnt"<?php echo (!$_order || $_order == ''?' selected':null); ?>>상품정렬</option>
							<option value="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'date', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'date'?' selected':null); ?>>등록순</option>
							<option value="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'price_asc', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'price_asc'?' selected':null); ?>>낮은 가격순</option>
							<option value="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'price_desc', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'price_desc'?' selected':null); ?>>높은 가격순</option>
							<option class="hide" value="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'sale', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'sale'?' selected':null); ?>>인기 상품순</option>
							<option class="hide" value="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>'pname', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'pname'?' selected':null); ?>>상품 이름순</option>
						</select>
					</div>
				</li>
				<li class="this_type">
					<span class="lineup">
						<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>$listmaxcount, 'list_type'=>'thumb')); ?>#total_cnt" class="btn_type ic_box<?php echo ($ActiveListCol != 1?' hit':null); ?>" title="박스형(2단)"><span class="shape"></span></a><!-- 해당타입선택 hit -->
						<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>$listmaxcount, 'list_type'=>'list')); ?>#total_cnt" class="btn_type ic_list<?php echo ($ActiveListCol == 1?' hit':null); ?>" title="리스트형(1단)"><span class="shape"></span></a>
					</span>
				</li>
			</ul>
		</div>
		<!-- / 리스트 제어 -->



		<!-- 현재위치 -->
		<div class="sub_location hide">
			<dl>
				<?php if($ActiveCate['cname'][1]) { ?>
					<dd><?php echo $ActiveCate['cname'][0]; ?><em>&gt;</em></dd>
				<?php } else { ?>
					<dt><?php echo $ActiveCate['cname'][0]; ?></dt>
				<?php } ?>
				<?php if($ActiveCate['cname'][2]) { ?>
					<dd><?php echo $ActiveCate['cname'][1]; ?><em>&gt;</em></dd>
				<?php } else { ?>
					<dt><?php echo $ActiveCate['cname'][1]; ?></dt>
				<?php } ?>
				<?php if($ActiveCate['cname'][2]) { ?>
					<dt><?php echo $ActiveCate['cname'][2]; ?></dt>
				<?php } ?>
			</dl>
		</div>



		<?php

		// 이벤트가 타입일경우 기본 진열을가져온다.
		if($_event == 'type'){
			$displayTypeInfo = _MQ(" select * from `smart_display_type_set` where (1) and dts_uid = '{$typeuid}' ");
			$ActiveListCol = $displayTypeInfo['dts_list_product_mobile_display'];

			// {{{스킨유형별개수설정}}}
			$ActiveColList = array();
			$tempSkinInfo = SkinInfo('category');
			if($tempSkinInfo['mo_list_depth']) $ActiveColList = explode(',', $tempSkinInfo['mo_list_depth']); // pc_list_depth or mo_list_depth
			$ActiveColListDefault = $tempSkinInfo['mo_list_depth_default']; // pc_list_depth_default or mo_list_depth_default
			if(count($ActiveColList) > 0) {
				$FindDefaultKeyArr = array_flip($ActiveColList);
				if(in_array($ActiveColListDefault, $ActiveColList)) {
					unset($FindDefaultKeyArr[$ActiveColListDefault]);
					$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
				}
			}

			if($list_type == 'list') $ActiveListCol = '1';
			if(in_array($ActiveListCol, $ActiveColList)) $ActiveListColClass = ' if_col'.$ActiveListCol; // 기본3단 이외 2일 경우 클래스 변경
		}


		// 상품리스트 호출
		include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');
		?>



		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi">
			<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
	</div>
	<!-- /상품리스트 -->
</div>