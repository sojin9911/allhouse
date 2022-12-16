<div class="section">


<?php include_once(OD_PROGRAM_ROOT.'/product.top_nav.php'); // 상단 네비게이션 출력 ?>


<!-- ◆ 상품상세 : 사진,기본정보 -->
<div class="view_top">
	<ul class="ul">
		<li class="li view_photo">

			<!-- 상품 사진 -->
			<div class="photo_box<?php echo (count($pro_img)<=1?' if_onlyone':null); ?>"><!-- 이미지가 한장만 있으면 if_onlyone -->
				<!-- 큰사진 롤링박스 -->
				<div class="rolling_box js_photo_large_slider">
					<div class="<?php echo (count($pro_img)>1?' swiper-wrapper':null); ?>">
						<!-- 이 div 롤링 / 470 * 470 -->
						<?php
							if(count($pro_img)>0){
								foreach($pro_img as $k=>$v){
									$_pimg = get_img_src($v);
						?>
									<div class="thumb<?php echo (count($pro_img)>1?' swiper-slide':null); ?>" style="<?php echo ($k>0?'display:none;':null); ?>">
										<?php if($_pimg){ ?><div class="real_img"><img src="<?php echo $_pimg; ?>" alt="<?php echo addslashes($pro_name); ?>"></div><?php } ?>
										<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($pro_name); ?>"></div>
									</div>
						<?php
								}
							}else{
						?>
							<div class="thumb<?php echo (count($pro_img)>1?' swiper-slide':null); ?>" style="<?php echo ($k>0?'display:none;':null); ?>">
								<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($pro_name); ?>"></div>
							</div>
						<?php } ?>
					</div>
				</div>

				<!-- 롤링사진 1개일 경우 숨김 -->
				<?php if(count($pro_img)>1){ ?>
				<div class="rolling_thumb">
					<ul class="js_photo_large_pager">
						<!-- li 최대 5개 노출 / 활성화시 li에 hit클래스 추가 -->
						<!-- 이미지 5개 안채워지면 이미지와 a만 없고 li, div는 그대로 유지 -->
						<?php
							foreach($pro_img as $k=>$v){
								$_pimg = get_img_src('thumbs_s_' . $v);
								if($_pimg == '') $_pimg = $SkinData['skin_url'] . '/images/none_photo.png';
						?>
							<li class="<?php echo ($k === 0?'hit':''); ?>">
								<a href="#none;" onclick="return false;" data-slide-index="<?php echo $k; ?>" class="upper_link<?php echo ($k === 0?' active':''); ?>" title=""></a>
								<div class="box"><span><img src="<?php echo $_pimg; ?>" alt=""></span></div>
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>

				<?php if(sizeof($pro_img)>1) { ?>
					<script type="text/javascript">
						$(document).ready(function(){
							$('.js_photo_large_slider .swiper-slide').show();
							var photo_large = new Swiper('.js_photo_large_slider', {
								effect: 'slide',
								slidesPerView: 1,
								autoplay : 4000,
								speed: 1000,
								parallax:true,
								autoHeight:true,
								autoplayDisableOnInteraction : false,
								loop : true,
								spaceBetween: 1,
								onSlideChangeStart: function(swiper){
									$('.js_photo_large_pager li').removeClass('hit');
									$('.js_photo_large_pager li a[data-slide-index='+swiper.realIndex+']').parent().addClass('hit');
								}
							});
							// 썸네일 클릭
							$('.js_photo_large_pager li a').on('click', function(){
								var idx = $(this).attr('data-slide-index')*1 + 1;
								photo_large.slideTo(idx);
							});
						});

					</script>
				<?php } ?>

			</div>

		</li>
		<li class="li view_info">

			<!-- 상품이름/설명/아이콘 -->
			<div class="view_name">
				<?php if($pro_subname){ ?>
					<div class="sub_name"><?php echo $pro_subname; ?></div>
				<?php } ?>
				<div class="title"><?php echo $pro_name; ?></div>
				<div class="price">
					<ul>
						<?php if($pro_price){ ?><li><span class="after"><strong><?php echo $pro_price; ?></strong><em>원</em></span></li><?php } ?>
						<?php if($pro_screenprice){ ?><li><span class="before"><?php echo $pro_screenprice; ?>원</span></li><?php } ?>
						<?php if($pro_point){ ?><li><span class="point" style="display:none"><span class="icon">P</span><strong><?php echo $pro_point; ?> 포인트</strong> 적립</span></li><?php } ?>
					</ul>
				</div>
			</div>

			<?php
				// 2018-07-16 SSJ :: 배송정보에서 노출되도록 위치이동
				// 배송비 <!-- 기본 <em>2,500</em>원 (<em>30,000</em>원 이상 무료) --><!-- 무료배송일경우, 아이콘없으면 텍스트로 노출 -->
				switch($p_info['p_shoppingPay_use']){
					case 'Y': $pro_delivery = '개별배송 <em>' . number_format($pro_delivery_info['price']) . '</em>원'; break;
					case 'N':
						$pro_delivery = '기본 <em>' . number_format($pro_delivery_info['price']) . '</em>원';
						if($pro_delivery_info['freePrice'] > 0) $pro_delivery .= ' (<em>'.number_format($pro_delivery_info['freePrice']) . '</em>원 이상 무료)';
						break;
					case 'F': $pro_delivery = '무료배송'; break; //무료배송 // SSJ :: 무료배송 아이콘대신 문구로 노출 ---- 2020-02-05
					case 'P': $pro_delivery = '상품별배송 <em>' . number_format($pro_delivery_info['price']) . '</em>원'.($pro_delivery_info['freePrice'] > 0 ? ' (<em>'.number_format($pro_delivery_info['freePrice']) . '</em>원 이상 무료)' : null); break; // 상품별 // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
				}
			?>

</div>
			<!-- 상품기본정보 -->
			<div class="view_default">
				<?php if(count($ex_display_mo) > 0){ ?>

					<?php foreach($ex_display_mo as $k=>$v){ ?>

						<?php if($pro_screenprice && $v == 'screenPrice'){ ?>
						<dl>
							<dt>소비자가</dt>
							<dd><span class="before_price"><strong><?php echo $pro_screenprice; ?></strong>원</span></dd>
						</dl>
						<?php } ?>

						<?php if($v == 'price'){ ?>
						<dl>
							<dt>판매가</dt>
							<dd><span class="after_price"><strong><?php echo $pro_price; ?></strong>원</span>
								<?php // {{{회원등급혜택 ?>
								<?php if( $groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) { ?>
								<span class="point_plus">
									<span class="txt">회원할인</span> <strong><?=odt_number_format($groupSetInfo['mgs_sale_price_per'],1)?>%</strong>
								</span>
								<?php } ?>
								<?php // {{{회원등급혜택 ?>
							</dd>
						</dl>
						<?php } ?>

						<?php if($pro_point && $v == 'point'){ ?>
						<dl>
							<dt><span class="tit">적립금</span></dt>
							<dd><span class="point"><strong><?php echo $pro_point; ?></strong>원</span>
								<?php // {{{회원등급혜택 ?>
								<?php if( $groupSetUse === true && $groupSetInfo['mgs_give_point_per'] > 0 ) { ?>
								<span class="point_plus">
									<span class="txt">회원추가적립</span> <strong><?=odt_number_format($groupSetInfo['mgs_give_point_per'],1)?>%</strong>
								</span>
								<?php } ?>
								<?php // {{{회원등급혜택 ?>
							</dd>
						</dl>
						<?php } ?>

						<?php if(($pro_maker || $pro_orgin) && $v == 'maker/orgin'){ ?>
						<dl>
							<dt>제조사/원산지</dt>
							<dd><?php echo implode(' / ', array_filter(array($pro_maker, $pro_orgin))); //<!-- 슬래시 사이 간격 유지 / 제조사,원산지 중 한개만 있을경우 슬래시 삭제--> ?></dd>
						</dl>
						<?php } ?>

						<?php if($pro_brand_name && $v == 'brand'){ ?>
						<dl>
							<dt>브랜드</dt>
							<dd>
								<span class="brand_tx"><?php echo $pro_brand_name; ?></span>
								<a href="/?pn=product.brand_list&uid=<?php echo $pro_brand_uid; ?>" target="_blank"  class="btn_brand">브랜드 다른 상품보기</a>
							</dd>
						</dl>
						<?php } ?>

						<?php if($v == 'deliveryInfo'){ ?>
						<dl>
							<dt>배송정보</dt>
							<dd><?php echo $pro_del_info; //<!-- 슬래시 사이 간격 유지 --> ?></dd>
						</dl>
						<?php } ?>

						<?php if($v == 'deliveryPrice'){ ?>
						<dl>
							<dt>배송비</dt>
							<dd>
								<?php
									echo '<span class="delivery">'.$pro_delivery.'</span>';
								?>

								<?php // {{{무료배송이벤트}}} ?>
								<?php if( $freeEventChk === true && $p_info['p_free_delivery_event_use'] == 'Y' ) { ?>
								<span class="point_plus delivery_free">
									<strong><?=number_format($freeEventInfo['minPrice'])?>원</strong>이상 주문 시 무료배송 이벤트 진행
								</span>
								<?php } ?>
								<?php // {{{무료배송이벤트}}} ?>


							</dd>
						</dl>
						<?php } ?>

						<?php if($ex_coupon['name'] && $ex_coupon[1] && $v == 'coupon'){ ?>
							<!-- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22-->
							<dl>
								<dt><?php echo $arrDisplayPinfo['coupon'] ?></dt>
								<dd>
									<!-- 상품쿠폰 (주문에서도 동일하게 사용할 예정) -->
									<div class="view_coupon">
										<span class="coupon_name">
											<?php echo stripslashes($ex_coupon['name']); ?>
										</span>
										<span class="coupon_about">
										<?php if($ex_coupon[1]=="price"){
											echo number_format($ex_coupon['price']); ?>원
										<?php }else{?>
											<?php echo floor($ex_coupon['per']*10)/10; ?>%
											<?php if($ex_coupon['max']>0){?>
												<span class="txt">( 최대: <?php echo number_format($ex_coupon['max']); ?>원 할인 )</span>
											<?php }?>
										<?php }?>
									</div>
								</dd>
							</dl>
						<?php } ?>

						<?php if( $groupSetUse === true && $v== 'groupSet') { // {{{회원등급혜택}}} ?>
						<dl>
							<dt><?php echo $arrDisplayPinfo['groupSet'] ?></dt>
							<dd>
							<?php
								$printGroupSetPer = array();
								if( $groupSetInfo['mgs_sale_price_per'] > 0)
								$printGroupSetPer[] =  "<strong>할인 : <em>".number_format($groupSetInfo['mgs_sale_price_per'],1)."</em>%</strong>";
								if( $groupSetInfo['mgs_give_point_per'] > 0)
								$printGroupSetPer[] = " <strong>추가적립 : <em>".number_format($groupSetInfo['mgs_give_point_per'],1)."</em>%</strong>";

								echo ''.implode(" / ",$printGroupSetPer).'';


							?>
							</dd>
						</dl>
						<?php } // {{{회원등급혜택}}} ?>

					<?php } ?>

				<?php } ?>

				<?php
					// -- 옵션 없을 경우 ----
					if($p_info['p_option_type_chk'] == 'nooption' && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){
				?>
						<!-- 상품옵션없을 경우 수량표기는 여기에/없으면 dl 숨김 -->
						<dl>
							<dt>수량</dt>
							<dd>
								<div class="view_counter">
									<?php if($p_info['p_stock'] > 0){ ?>
										<a href="#none" onclick="pro_cnt_down(); return false;" class="btn_down" title="빼기"><span class="shape"></span></a>
										<input type="text" name="option_select_cnt" id="option_select_cnt" class="updown_input" value="1" readonly>
										<a href="#none" onclick="pro_cnt_up(); return false;" class="btn_up" title="더하기"><span class="shape"></span></a>
									<?php }else{ ?>
										품절<input type="hidden" name="option_select_cnt" class="input_num" id="option_select_cnt" value="0" />
									<?php } ?>
									<input type="hidden" name="option_select_expricesum" ID="option_select_expricesum" value="<?php echo ($p_info['p_price']-getGroupSetPer($p_info['p_price'],'price',$pcode)); ?>">
									<input type="hidden" name="option_select_type" id="option_select_type" value="<?php echo $p_info['p_option_type_chk']; ?>">
								</div>
							</dd>
						</dl>
				<?php
					// -- 옵션 있을 경우 ----
					}else if(count($options) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){
				?>

				<dl class="view_option" style="display:none;">
					<dt>필수 옵션</dt>
					<dd>

						<?php if($p_info['p_stock'] > 0){ ?>

							<?php
								// ------------------------- 1차 옵션 설정 -------------------------
								// 1차 옵션이 normal 형일 경우 처리
								//			옵션형태 : normal , color , size
								if($p_info['p_option1_type'] == 'normal') {
							?>
								<!-- 선택하면 클래스값 if_selected -->
								<div class="select">
									<select name="_option_select1"  onchange="option_select_tmp('1', '<?php echo $p_info['p_code']; ?>')"  ID="option_select1_id">
										<option value="">옵션을 선택해주세요.(필수)</option>
										<?php foreach( $options as $k=>$sr){ ?>
											<option value="<?php echo $sr['po_uid']; ?>">
												<?php echo $sr['po_poptionname']; ?>
												<?php
													if($p_info['p_option_type_chk'] == '1depth'){
														echo ($sr['po_cnt'] > 0 ? ($isOptionStock ? ' (잔여:' . number_format($sr['po_cnt']) . ')' : null) . ' / ' . number_format($sr['po_poptionprice']) . '원' : ' (품절)');
													}
												?>
											</option>
										<?php } ?>
									</select>
								</div>
							<?php
								} // 1차 옵션이 normal 형일 경우 처리


								// 1차 옵션이 color 형일 경우 처리
								else if($p_info['p_option1_type'] == 'color') {
							?>
								<!-- 컬러는 #컬러값을 입력하거나 이미지를 등록할 수 있도록 / 이미지: [모바일]150 * 150, [PC]35 * 35  / 품절일 때 label에 none 추가 / 선택안되게 -->
								<div class="view_option_color">
									
									<ul>
										<?php
											foreach( $options as $k=>$sr){

												// 품절여부
												$app_soldout_class = ($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ? 'none' : '');

												//색상 or 이미지
												$app_color_name = (
													$sr['po_color_type'] == 'img' ?
														'background-image:url(\'/upfiles/option/'.$sr['po_color_name'].'\');' :
														'background:' . $sr['po_color_name']
												);
										?>
											<li>
												<!-- 옵션설명값 & 품절시 none 클래스 처리 -->
												<label title="<?=$sr['po_poptionname']?>" class="<?=$app_soldout_class?>">
													<input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')" /><span class="tx"><span class="shape"  style="<?=$app_color_name?>"></span></span>
												</label>
											</li>
										<?php } ?>
									</ul>
									<input type="hidden" name="_option_select1" ID="option_select1_id" value="">
								</div>
							<?php
								} // 1차 옵션이 color 형일 경우 처리


								// 1차 옵션이 size 형일 경우 처리
								else if($p_info['p_option1_type'] == 'size') {
							?>
								<!-- 품절일 때 label에 none 추가 / 선택안되게 -->
								<div class="view_option_size">
									<ul>
										<?php
											foreach( $options as $k=>$sr){

												// 품절여부
												$app_soldout_class = ($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ? 'none' : '');

										?>
											<li>
												<label class="<?=$app_soldout_class?>">
													<input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')"  <?=($app_soldout_class == 'none' ? 'disabled' : '')?> /><span class="tx"><?=$sr['po_poptionname']?></span>
												</label>
											</li>
										<?php } ?>
									</ul>
									<input type="hidden" name="_option_select1" ID="option_select1_id" value="">
								</div>
							<?php
								} // 1차 옵션이 size 형일 경우 처리
								// ------------------------- 1차 옵션 설정 -------------------------
							?>


						<?php }else{ ?>
							<div class="select">
								<select name="">
									<option value="">품절</option>
								</select>
							</div>
						<?php } ?>


						<?php
							if($p_info['p_stock'] > 0){

								//일반형일 경우 2차 옵션 클래스
								$app_depth2_class="select";
								switch($p_info['p_option2_type']){
									case "color": $app_depth2_class="view_option_color"; break;//컬러형일 경우 옵션 클래스
									case "size": $app_depth2_class="view_option_size"; break;//사이즈형일 경우 옵션 클래스
								}

								//일반형일 경우 3차 옵션 클래스
								$app_depth3_class="select";
								switch($p_info['p_option3_type']){
									case "color": $app_depth3_class="view_option_color"; break;//컬러형일 경우 옵션 클래스
									case "size": $app_depth3_class="view_option_size"; break;//사이즈형일 경우 옵션 클래스
								}

						?>

							<?php if( in_array($p_info['p_option_type_chk'], array('2depth','3depth')) ){  ?>
								<div class="<?=$app_depth2_class?> before" id="" data-idx="2"><!--id = "span_option2"-->
									<?=($p_info['p_option2_type'] == 'normal' ? '<select name=""><option value="0">상위옵션을 먼저 선택해주세요.(필수)</option></select>' : '<div class="this">상위옵션을 먼저 선택해주세요.(필수)</div>')?>
								</div>
							<?php } ?>

							<?php if($p_info['p_option_type_chk'] == '3depth'){ ?>
								<div class="<?=$app_depth3_class?> before" id="span_option3" data-idx="3">
									<?=($p_info['p_option3_type'] == 'normal' ? '<select name=""><option value="0">상위옵션을 먼저 선택해주세요.(필수)</option></select>' : '<div class="this">상위옵션을 먼저 선택해주세요.(필수)</div>')?>
								</div>
							<?php } ?>

						<?php } ?>

						<!-- <input type="hidden" name="_option_select1" ID="option_select1_id" value=""> // 2019-01-10 SSJ :: 중복으로 제거 -->

					</dd>
				</dl>



				<?php if(count($add_options)>0 && $p_info['p_stock'] > 0){ ?>
					<dl class="view_option">
						<dt>추가 옵션</dt>
						<dd>
							<?php foreach($add_options as $k=>$v) { ?>
								<div class="select "><!-- 선택하면 클래스값 if_selected -->
									<select name='_add_option_select_<?php echo ($k+1); ?>' id="add_option_select_<?php echo ($k+1); ?>_id" class='add_option add_option_chk' onchange="add_option_select_add('<?php echo $pcode; ?>' , this.value); this.value=''; return false;">
										<option value=""><?php echo trim($v['pao_poptionname']); ?></option>
										<?php foreach($v['add_sub_options'] as $key=>$value){ ?>
											<option value="<?php echo $value['pao_uid']; ?>" data-uid="<?php echo $value['pao_uid']; ?>">
												<?php echo $value['pao_poptionname'].($value['pao_cnt']>0 ? ($isOptionStock ? ' (잔여:'.number_format($value['pao_cnt']).')' : null) . ' / '. number_format($value["pao_poptionprice"]) . '원' : ' (품절)'); ?>
											</option>
										<?php } ?>
									</select>
								</div>
							<?php } ?>
						</dd>
					</dl>
				<?php } ?>


				<?php } ?>


			</div>


			<?php if(count($options) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){ ?>
			<!-- 선택한 옵션 -->
			<div style="display:none;"><!-- class="view_option result" id="span_seleced_list"-->
				<dl>
					<dt class="if_before">구매하실 상품 옵션을 선택해 주시기 바랍니다.</dt>
				</dl>
			</div>
			<?php } ?>






			<!-- 결제금액계산 -->
			<?php
				// 상품 옵션 설정이 등록되었을때만 노출
				if(($p_info['p_option_type_chk'] == 'nooption' || count($options)) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){
			?>
			<div class="view_total" style="display:none;">
				<span class="total_tt">총 합계금액</span>
				<div class="after_price"><strong id="option_select_expricesum_display">0</strong>원</div>
			</div>
			<?php } ?>

			<?php if($p_info['p_option_valid_chk']<>'Y'){ ?>
				<span class="view_total_error">현재 상품판매를 준비중입니다.</span>
			<?php } ?>


			<?php
				// 상품 옵션 설정이 등록되었을때만 노출
				if(($p_info['p_option_type_chk'] == 'nooption' || count($options) > 0) && $p_info['p_option_valid_chk']=='Y'){
			?>
			<!-- 구매,장바구니,찜하기 버튼 -->
			<div class="view_btn view_cart_ask" style ="display:none;">
				<?php if($isSoldOut === false){  ?>
				<!-- 장바구니 담고 묻는창 나오도록 클래스값 추가 (모션을위함) if_cart_save -->
				<!-- 장바구니 눌렀을때 선택버튼 -->
				<div class="how">
					<div class="box">
						<div class="tip">상품을 장바구니에 담았습니다! <br>장바구니로 이동할까요?</div>
						<ul>
							<li><a href="#none" onclick="return false;" class="btn2 no_cart">계속 쇼핑</a></li>
							<li><a href="/?pn=shop.cart.list" class="btn2 go_cart">바로가기</a></li>
						</ul>
					</div>
					<script type="text/javascript">
						$(document).ready(function(){ $('.view_cart_ask .how .no_cart').click(function(){ $('.view_cart_ask').removeClass('if_cart_save'); }); });
					</script>
				</div>
				<?php }?>
				<!-- 찜하기버튼 / 활성화 시 hit 클래스 추가 -->
				<a href="#none" onclick="return false;" class="btn btn_wish js_wish<?php echo (is_wish($p_info['p_code'])?' hit':null); ?>" data-pcode="<?php echo $p_info['p_code']; ?>" title="찜하기"></a>
				<ul>
					<?php if($isSoldOut){  ?>
						<li><a href="#none" onclick="return false;" class="btn btn_soldout">품절된 상품입니다.</a></li>
					<?php }else{ ?>
						<li><a href="#none" onclick="<?php echo ($p_info['p_stock'] < 1 ? "app_soldout();" : "app_submit('".$pcode."','cart');"); ?>return false;" class="btn btn_cart">장바구니</a></li>
						<li><a href="#none" onclick="<?php echo ($p_info['p_stock'] < 1 ? "app_soldout();" : "app_submit('".$pcode."','order');"); ?>return false;" class="btn btn_order">바로구매</a></li>
					<?php }?>
				</ul>

			</div>
			<?php } ?>

			<!-- ★★★★★ 장바구니 버튼 확인(하이센스 공통기능) -->
			<div class="view_cart_ask" style="display:none;">
				이 상품을 장바구니에 담았습니다.<br/>지금 장바구니를 확인하시겠습니까?
				<ul class="ask_btn">
					<li><span class="button_pack"><a href="/?pn=shop.cart.list" class="btn_sm_black">장바구니 이동</a></span></li>
					<li><span class="button_pack"><a href="#none" onclick="return false;" class="btn_sm_white shopping">쇼핑 계속하기</a></span></li>
				</ul>
			</div>
			<script type="text/javascript">
				$(document).ready(function(){ $(".view_cart_ask .ask_btn .shopping").click(function(){ $(".view_cart_ask").hide(); }); });
			</script>
			<!-- / ★★★★★장바구니 버튼 확인 -->


			<?php // LDD NPAY { ?>
				<?php
				$NPayTrigger = 'N';
				if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $p_info['npay_use'] == 'Y') $NPayTrigger = 'Y';
				if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'test' && $nt == 'test') $NPayTrigger = 'Y';
				if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'test' && $nt != 'test') $NPayTrigger = 'N'; // 버튼+주문연동 작업
				if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'real' && $p_info['npay_use'] == 'Y') $NPayTrigger = 'Y'; // 버튼+주문연동 작업

				// LCY : 네이버페이 사용유무 추가 : 2020-10-20 - 어떠한 경우라도 상품의 네이버페이 사용유무가 Y가 아니라면 노출하지 않는다.
				if( $p_info['npay_use'] != 'Y'){ $NPayTrigger = 'N'; }

				if($NPayTrigger == 'Y') {
				?>
				<div style="padding-top:20px; text-align:center;">
					<script type="text/javascript" src="//<?php echo ($siteInfo['npay_mode'] == 'test'?'test-':null); ?>pay.naver.com/customer/js/mobile/naverPayButton.js" charset="UTF-8"></script>
					<script type="text/javascript">
					//<![CDATA[
						function NPayBuy() {

							var pcode = '<?php echo $pcode; ?>';
							var _type = 'view';
							if( !( $("#option_select_cnt").val() * 1 > 0 ) ) {
								alert("옵션을 하나 이상 선택해주시기 바랍니다.")
							}
							else if( !( $("#option_select_expricesum").val() * 1 > 0 ) ) {
								alert("옵션 합계금액이 0원을 초과해야 합니다.")
							}
							else {
								location.href = ('/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val());
							}
						}
						function NPayWish() {

							var pcode = '<?php echo $pcode; ?>';
							var _type = 'wish';
							var LocationUrl = '/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val();
							location.href = LocationUrl;
							return false;
						}
						naver.NaverPayButton.apply({
							BUTTON_KEY: "<?php echo $siteInfo['npay_bt_key']; ?>", // 페이에서 제공받은 버튼 인증 키 입력
							TYPE: "MA", // 버튼 모음 종류 설정
							COLOR: 1, // 버튼 모음의 색 설정
							COUNT: 2, // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
							ENABLE: "Y", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
							BUY_BUTTON_HANDLER: NPayBuy, // 구매하기 버튼
							WISHLIST_BUTTON_HANDLER: NPayWish, // 찜하기 버튼
							"":"",
						});
					//]]>
					</script>
				</div>
				<?php } ?>
			<?php // } LDD NPAY ?>

		</li>
	</ul>

<!-- / ◆ 상품상세 : 사진,기본정보 -->
<div class="new-wish-btn"><!--찜버튼의 자리-->
	<a href="#none" onclick="return false;" class="btn btn_wish js_wish newWish<?php echo (is_wish($p_info['p_code'])?' hit':null); ?>" data-pcode="<?php echo $p_info['p_code']; ?>" title="찜하기"><p>찜하기</p></a>
</div>


<div class="new-viewcont-wrap">
	<div class="new-tab">
		<ul class="new-tab_ul">
			<li class="tabLi_btn onTab">상세정보</li>
			<li class="tabLi_btn">기본정보</li>
			<li class="tabLi_btn">상품후기 <em class="num eval_cnt"><?php echo $eval_cnt; ?></em></li>
			<li class="tabLi_btn">상품문의</li>
		</ul>
	</div>
	<!--상세정보-->
	<div class="Nview-div display-block">
			<!-- 에디터 : 상품상세안에 들어가는 이미지 가로최대 모바일전용 (1000px) -->
		<div class="view_detail editor"><?php echo stripcslashes($p_info['p_content']); ?></div>
		<?php if(count($notify_info) > 0 ) { ?>
		<!-- 상품정보제공고시 -->
		<div class="view_notify">
			<div class="group_title">상품 정보 제공고시</div>
			<div class="table_box">
				<table>
					<tbody>
						<tr>
							<?php
							foreach($notify_info as $nik=>$niv) {
								if($nik>0) echo '</tr><tr>';
							?>
								<th><?=stripslashes($niv['pri_key'])?></th>
								<td><?=stripslashes($niv['pri_value'])?></td>
							<?php } ?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php } ?>
	</div>
	<!--기본정보-->
	<div class="Nview-div tab_inner_info">
		<dl>
			기본정보
		</dl>
		<dl>
			<dt>브랜드</dt>
			<dd>
				<span class="brand_tx"><?php echo $pro_brand_name; ?></span>
			</dd>
		</dl>
		<dl>
			<dt>소비자가</dt>
			<dd><span class="before_price"><strong><?php echo $pro_screenprice; ?></strong>원</span></dd>
		</dl>
		<dl>
			<dt>판매가</dt>
			<dd><span class="after_price"><strong><?php echo $pro_price; ?></strong>원</span>
				<?php // {{{회원등급혜택 ?>
				<?php if( $groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) { ?>
				<span class="point_plus">
					<span class="txt">회원할인</span> <strong><?=odt_number_format($groupSetInfo['mgs_sale_price_per'],1)?>%</strong>
				</span>
				<?php } ?>
				<?php // {{{회원등급혜택 ?>
			</dd>
		</dl>
	</div>
	<div class="Nview-div">
		<div class="c_view_board" id="eval_ajax">
			<?php include OD_PROGRAM_ROOT."/product.eval.form.php"; ?>
		</div>
	</div>
	<div class="Nview-div">
		<div class="c_view_board" id="qna_ajax">
			<?php include OD_PROGRAM_ROOT."/product.qna.form.php"; ?>
		</div>
	</div>
	<script>
		for(let i = 0; i < $('.tabLi_btn').length; i++){
    tabOpen(i); 
		}

			//함수에 보관
			function tabOpen(e){
					$('.tabLi_btn').eq(e).click(function(){
							$('.tabLi_btn').removeClass('onTab');
							$('.Nview-div').removeClass('display-block');
							$('.tabLi_btn').eq(e).addClass('onTab');
							$('.Nview-div').eq(e).addClass('display-block');
					});
			}
	</script>
</div>

<div class="goCart-tab">
	<div class="arrow-tab">
			<p></p>
	</div>
	<div class="hide-show-tab">
			<ul>
				<li class="cart_select_list">
				<dl class="view_option">
					<dt>필수 옵션</dt>
					<dd>

						<?php if($p_info['p_stock'] > 0){ ?>

							<?php
								// ------------------------- 1차 옵션 설정 -------------------------
								// 1차 옵션이 normal 형일 경우 처리
								//			옵션형태 : normal , color , size
								if($p_info['p_option1_type'] == 'normal') {
							?>
								<!-- 선택하면 클래스값 if_selected -->
								<div class="select">
									<select name="_option_select1"  onchange="option_select_tmp('1', '<?php echo $p_info['p_code']; ?>')"  ID="option_select1_id">
										<option value="">옵션을 선택해주세요.(필수)</option>
										<?php foreach( $options as $k=>$sr){ ?>
											<option value="<?php echo $sr['po_uid']; ?>">
												<?php echo $sr['po_poptionname']; ?>
												<?php
													if($p_info['p_option_type_chk'] == '1depth'){
														echo ($sr['po_cnt'] > 0 ? ($isOptionStock ? ' (잔여:' . number_format($sr['po_cnt']) . ')' : null) . ' / ' . number_format($sr['po_poptionprice']) . '원' : ' (품절)');
													}
												?>
											</option>
										<?php } ?>
									</select>
								</div>
							<?php
								} // 1차 옵션이 normal 형일 경우 처리


								// 1차 옵션이 color 형일 경우 처리
								else if($p_info['p_option1_type'] == 'color') {
							?>
								<!-- 컬러는 #컬러값을 입력하거나 이미지를 등록할 수 있도록 / 이미지: [모바일]150 * 150, [PC]35 * 35  / 품절일 때 label에 none 추가 / 선택안되게 -->
								<div class="view_option_color">
									<p class="view_option_tit">색상</p>
									<ul>
										<?php
											foreach( $options as $k=>$sr){
												
												// 품절여부
												$app_soldout_class = ($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ? 'none' : '');

												//색상 or 이미지
												$app_color_name = (
													$sr['po_color_type'] == 'img' ?
														'background-image:url(\'/upfiles/option/'.$sr['po_color_name'].'\');' :
														'background:' . $sr['po_color_name']
												);
										?>
											<li>
												<!-- 옵션설명값 & 품절시 none 클래스 처리 -->
												<label title="<?=$sr['po_poptionname']?>" class="<?=$app_soldout_class?>">
													<input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')" /><span class="tx"><span class="shape"  style="<?=$app_color_name?>"></span></span>
												</label>
											</li>
										<?php } ?>
									</ul>
									<input type="hidden" name="_option_select1" ID="option_select1_id" value="">
								</div>
							<?php
								} // 1차 옵션이 color 형일 경우 처리


								// 1차 옵션이 size 형일 경우 처리
								else if($p_info['p_option1_type'] == 'size') {
							?>
								<!-- 품절일 때 label에 none 추가 / 선택안되게 -->
								<div class="view_option_size">
									<ul>
										<?php
											foreach( $options as $k=>$sr){

												// 품절여부
												$app_soldout_class = ($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ? 'none' : '');

										?>
											<li>
												<label class="<?=$app_soldout_class?>">
													<input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')"  <?=($app_soldout_class == 'none' ? 'disabled' : '')?> /><span class="tx"><?=$sr['po_poptionname']?></span>
												</label>
											</li>
										<?php } ?>
									</ul>
									<input type="hidden" name="_option_select1" ID="option_select1_id" value="">
								</div>
							<?php
								} // 1차 옵션이 size 형일 경우 처리
								// ------------------------- 1차 옵션 설정 -------------------------
							?>


						<?php }else{ ?>
							<div class="select">
								<select name="">
									<option value="">품절</option>
								</select>
							</div>
						<?php } ?>


						<?php
							if($p_info['p_stock'] > 0){

								//일반형일 경우 2차 옵션 클래스
								$app_depth2_class="select";
								switch($p_info['p_option2_type']){
									case "color": $app_depth2_class="view_option_color"; break;//컬러형일 경우 옵션 클래스
									case "size": $app_depth2_class="view_option_size"; break;//사이즈형일 경우 옵션 클래스
								}

								//일반형일 경우 3차 옵션 클래스
								$app_depth3_class="select";
								switch($p_info['p_option3_type']){
									case "color": $app_depth3_class="view_option_color"; break;//컬러형일 경우 옵션 클래스
									case "size": $app_depth3_class="view_option_size"; break;//사이즈형일 경우 옵션 클래스
								}

						?>

							<?php if( in_array($p_info['p_option_type_chk'], array('2depth','3depth')) ){  ?>
								<div class="<?=$app_depth2_class?> before" id="span_option2" data-idx="2">
									<?=($p_info['p_option2_type'] == 'normal' ? '<select name=""><option value="0">상위옵션을 먼저 선택해주세요.(필수)</option></select>' : '<div class="this">상위옵션을 먼저 선택해주세요.(필수)</div>')?>
								</div>
							<?php } ?>

							<?php if($p_info['p_option_type_chk'] == '3depth'){ ?>
								<div class="<?=$app_depth3_class?> before" id="span_option3" data-idx="3">
									<?=($p_info['p_option3_type'] == 'normal' ? '<select name=""><option value="0">상위옵션을 먼저 선택해주세요.(필수)</option></select>' : '<div class="this">상위옵션을 먼저 선택해주세요.(필수)</div>')?>
								</div>
							<?php } ?>

						<?php } ?>

						<!-- <input type="hidden" name="_option_select1" ID="option_select1_id" value=""> // 2019-01-10 SSJ :: 중복으로 제거 -->

					</dd>
				</dl>



				<?php if(count($add_options)>0 && $p_info['p_stock'] > 0){ ?>
					<dl class="view_option">
						<dt>추가 옵션</dt>
						<dd>
							<?php foreach($add_options as $k=>$v) { ?>
								<div class="select "><!-- 선택하면 클래스값 if_selected -->
									<select name='_add_option_select_<?php echo ($k+1); ?>' id="add_option_select_<?php echo ($k+1); ?>_id" class='add_option add_option_chk' onchange="add_option_select_add('<?php echo $pcode; ?>' , this.value); this.value=''; return false;">
										<option value=""><?php echo trim($v['pao_poptionname']); ?></option>
										<?php foreach($v['add_sub_options'] as $key=>$value){ ?>
											<option value="<?php echo $value['pao_uid']; ?>" data-uid="<?php echo $value['pao_uid']; ?>">
												<?php echo $value['pao_poptionname'].($value['pao_cnt']>0 ? ($isOptionStock ? ' (잔여:'.number_format($value['pao_cnt']).')' : null) . ' / '. number_format($value["pao_poptionprice"]) . '원' : ' (품절)'); ?>
											</option>
										<?php } ?>
									</select>
								</div>
							<?php } ?>
						</dd>
					</dl>
				<?php } ?>




				</li>
				<li>
					<?php if(count($options) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){ ?>
					<!-- 선택한 옵션 -->
					<div class="view_option result" id="span_seleced_list">
						<dl>
							<dt class="if_before">구매하실 상품 옵션을 선택해 주시기 바랍니다.</dt>
						</dl>
					</div>
					<?php } ?>
				</li>
				
			</ul>
			<ul class="view_tab_total">
				<li>총 상품금액</li>
				<li>총 할인금액</li>
				<li>총 합계금액</li>
			</ul>
	</div>
	<div class="goCart_btnlist">
		<ul>
			<li class="goCart_btnskin toggleBtn">장바구니에 담기</li>
			<li><a href="#none" class="goCart_btnskin toggleBtn_none" onclick="<?php echo ($p_info['p_stock'] < 1 ? "app_soldout();" : "app_submit('".$pcode."','cart');"); ?>return false;" class="btn btn_cart">구매하기</a></li><!--장바구니-->
		</ul>
	</div>
</div>
<script>
	let div = document.querySelector('.hide-show-tab');
	let buyBtn = document.querySelector('.toggleBtn_none')
	let cart_toggle_btn = document.querySelector('.toggleBtn');
	cart_toggle_btn.addEventListener('click',function(){
		div.classList.toggle('click_block');
		buyBtn.classList.toggle('click_block');
		cart_toggle_btn.classList.toggle('toggleBtn_none');
	});
	let arrowBtn = document.querySelector('.arrow-tab p');
	arrowBtn.addEventListener('click',function(){
		div.classList.toggle('click_block');
		cart_toggle_btn.classList.toggle('toggleBtn_none');
		buyBtn.classList.toggle('click_block');
		arrowBtn.classLst.toggle('changeimg');
	});



</script>



<?php
$SNSSendUse = array($siteInfo['facebook_share_use'], $siteInfo['kakao_share_use'], $siteInfo['twitter_share_use'], $siteInfo['pinter_share_use']);
if(in_array('Y', $SNSSendUse)) {
?>
	<!-- 상품평점/sns공유 -->
	<div class="view_summery" style="display:none;">
		<div class="tit">Share &amp; Tag</div>
		<div class="sns">
			<ul>
				<?php if($siteInfo['kakao_share_use'] == 'Y' && $siteInfo['kakao_js_api'] != '' ) { ?>
					<li><a href="#none" onclick="sendSNS('kakao'); return false;" class="btn" title="카카오톡 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_kakao.png" alt=""></a></li>
				<?php } ?>
				<?php if($siteInfo['kakao_share_use'] == 'Y' && $siteInfo['kakao_js_api'] != '') { ?>
					<li><a href="#none" onclick="sendSNS('kakao-story'); return false;" class="btn" title="카카오스토리 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_story.png" alt=""></a></li>
				<?php } ?>
				<?php if($siteInfo['facebook_share_use'] == 'Y' && $siteInfo['s_facebook_key'] != '' ) { ?>
					<li><a href="#none" onclick="sendSNS('facebook'); return false;" class="btn" title="페이스북 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_face.png" alt=""></a></li>
				<?php } ?>
				<?php if($siteInfo['twitter_share_use'] == 'Y') { ?>
					<li><a href="#none" onclick="sendSNS('twitter'); return false;" class="btn" title="트위터 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_twitt.png" alt=""></a></li>
				<?php } ?>
				<?php if($siteInfo['pinter_share_use'] == 'Y') { ?>
					<li><a href="#none" onclick="sendSNS('pinterest'); return false;" class="btn" title="핀터레스트 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_pin.png" alt=""></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
<?php } ?>

<?php if(count($pro_hashtag)>0){ ?>
<!-- 해시태그 -->
<div class="sub_hash">
	<div class="hash_box">
		<ul>
			<?php foreach($pro_hashtag as $k=>$v){ ?>
				<li><a href="/?pn=product.search.list&search_word=%23<?php echo urlencode(trim($v)); ?>" class="btn" title="#<?php echo addslashes(trim($v)); ?>">#<?php echo trim($v); ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php } ?>



<?php
	if(count($ProductMiddle)>0){
?>
	<!-- ◆ 상세배너 (없으면 전체 숨김)  -->
	<div class="view_banner">
		<div class="layout_fix">
		<?php
			foreach($ProductMiddle as $k=>$v){
				$_img = get_img_src($v['b_img'], IMG_DIR_BANNER);
				if($_img == '') continue;
		?>
				<!-- [MOBILE]공통 : 상품상세 중간 배너 (940 x free) -->
				<?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?><a href="<?php echo $v['b_link']; ?>" target="<?php echo $v['b_target']; ?>"><?php } ?>
				<img src="<?php echo $_img; ?>" alt="<?php echo addslashes($v['b_title']); ?>">
				<?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?></a><?php } ?>
		<?php
			}
		?>
		</div>
	</div>
	<!-- / ◆ 상세배너 -->
<?php
	}
?>







<?php if(count($relation) > 0){ ?>
	<!-- ◆ 다른관련상품 (없으면 전체 숨김)  -->
	<div class="view_relative" style="display:none;">

		<div class="relative_top">
			<div class="tt">다른 연관상품</div>

			<?php if(count($relation)>$SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]){ ?>
				<!-- 롤링아이콘 (롤링이 1개일때는 숨김) (해당 롤링일때 active 추가) -->
				<div class="rolling_icon">
					<span class="lineup js_list_relation_slide_pager">
						<?php for($i=0; $i<ceil(count($relation)/$SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]); $i++) {?>
							<a href="#none" onclick="return false;" class="icon<?php echo ($i==0?' active':null); ?>"></a>
						<?php } ?>
					</span>
				</div>
				<!-- 롤링아이콘 -->
			<?php } ?>

		</div>

		<div class="rolling_box">
			<!-- ◆ 상품리스트 : 기본 3단 / 2단 if_col2 -->
			<div class="item_list if_col<?php echo $SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]; ?> js_list_relation_slide">
				<ul class="js_list_relation_slide_tmp">
					<?php
					foreach($relation as $bi_k=>$bi_v) {

						//	더미슬라이드는 한줄만
						if($SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']] - $bi_k < 1) break;


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
					if(count($relation) < $SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]) {
						for($i=0; $i<$SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]-count($relation); $i++) {
					?>
						<li></li>
					<?php }} ?>
				</ul>
				<ul class="swiper-wrapper" style="display:none;overflow:visible;">
					<?php
					foreach($relation as $bi_k=>$bi_v) {
					?>
						<li class="swiper-slide">
						<?php 
							$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
							$locationFile = basename(__FILE__); // 파일설정
							$k = $bi_k; $v = $bi_v;
							include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
						?>
						</li>
					<?php
						}
						// 2018-12-31 SSJ :: 빈칸 채우기
						if(
							count($relation) > $SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]
							&&
							count($relation)%$SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']] > 0
						){
							for($i=0; $i<($SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']] - count($relation)%$SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]); $i++){
								echo '<li class="swiper-slide"></li>';
							}
						}
					?>
				</ul>
			</div>
			<!-- / ◆ 상품리스트 -->
		</div>

	</div>
	<!-- / ◆ 다른연관상품 -->

	<?php if(count($relation)>$SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]){ ?>


		<script type="text/javascript">
			$(window).load(function() {
				var RelationSlideMargin = $('.js_list_relation_slide .js_list_relation_slide_tmp').find('.item_box').css('margin-left').replace('px', '')*1;
				var RelationSlideWrap = $('.js_list_relation_slide .js_list_relation_slide_tmp').outerWidth();

				$('.js_list_relation_slide .swiper-wrapper').css('width', RelationSlideWrap);
				$('.js_list_relation_slide .swiper-wrapper').css('margin-left', 0);
				$('.js_list_relation_slide .swiper-wrapper .item_box').css('margin-left', 0);

				$('.js_list_relation_slide .js_list_relation_slide_tmp').remove();
				$('.js_list_relation_slide .swiper-wrapper').show();
				var relation_slide = new Swiper('.js_list_relation_slide', {
					pagination : ".js_list_relation_slide_pager",
					effect: 'slide',
					slidesPerView: <?php echo $SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]; ?>,
					slidesPerGroup: <?php echo $SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']]; ?>,
					paginationType : 'bullets',
					paginationClickable : true,
					autoplay : 4000,
					speed: 1000,
					parallax:false,
					autoHeight:false,
					autoplayDisableOnInteraction : false,
					loop : true,
					spaceBetween: RelationSlideMargin,
					bulletClass : 'icon',
					bulletActiveClass : 'active',
					paginationBulletRender: function (swiper, index, className) {
						return '<a href="#none" onclick="return false;" class="icon '+className+'"></a>';
					}
				});
			});

		</script>
	<?php } ?>

<?php } ?>


<!-- ◆ 상세탭 -->
<div class="view_tab js_info_position" style="display:none;">
	<div class="tab_box">
		<ul>
			<!-- 활성화시 hit클래스  -->
			<li class="hit"><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab"><span class="tx">상세정보</span></a></li><!--상품정보-->
			<li class=""><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab"><span class="tx">기본정보</span></a></li><!--상품정보-->
			<li class=""><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab"><span class="tx">상품후기<br><em class="num eval_cnt">(<?php echo $eval_cnt; ?>)</em></span></a></li>
			<li class=""><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab"><span class="tx">상품문의<br><em class="num qna_cnt" style="display:none;">(<?php echo $qna_cnt; ?>)</em></span></a></li>
			<li class="" style="display:none;"><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab"><span class="tx">배송/교환/<br>반품안내</span></a></li>
		</ul>
	</div>
</div>
	<!-- / ◆ 상세탭 -->







<!-- ◆상품정보 -->
<div class="view_conts"  style="display:none;">

	<!-- 에디터 : 상품상세안에 들어가는 이미지 가로최대 모바일전용 (1000px) -->
	<div class="view_detail editor"><?php echo stripcslashes($p_info['p_content']); ?></div>


	<?php if(count($notify_info) > 0 ) { ?>
	<!-- 상품정보제공고시 -->
	<div class="view_notify">
		<div class="group_title">상품 정보 제공고시</div>
		<div class="table_box">
			<table>
				<tbody>
					<tr>
						<?php
						foreach($notify_info as $nik=>$niv) {
							if($nik>0) echo '</tr><tr>';
						?>
							<th><?=stripslashes($niv['pri_key'])?></th>
							<td><?=stripslashes($niv['pri_value'])?></td>
						<?php } ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php } ?>

</div>
<!-- / ◆상품정보 -->








<!-- ◆ 상세탭 -->
<div class="view_tab js_eval_position" style="display:none;">
	<div class="tab_box">
		<ul>
			<!-- 활성화시 hit클래스  -->
			<li class=""><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab"><span class="tx">상품정보</span></a></li>
			<li class="hit"><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab"><span class="tx">상품후기<br><em class="num eval_cnt">(<?php echo $eval_cnt; ?>)</em></span></a></li>
			<li class=""><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab"><span class="tx">상품문의<br><em class="num qna_cnt">(<?php echo $qna_cnt; ?>)</em></span></a></li>
			<li class=""><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab"><span class="tx">배송/교환/<br>반품안내</span></a></li>
		</ul>
	</div>
</div>
<!-- / ◆ 상세탭 -->








<!-- ◆상품후기 -->
<div class="view_conts" style="display:none;">
	<!--기본정보로 변경-->
<div class="view_conts">
	<div class="view_default">
					<?php if(count($ex_display_mo) > 0){ ?>

						<?php foreach($ex_display_mo as $k=>$v){ ?>

							<?php if($pro_screenprice && $v == 'screenPrice'){ ?>
							<dl>
								<dt>소비자가</dt>
								<dd><span class="before_price"><strong><?php echo $pro_screenprice; ?></strong>원</span></dd>
							</dl>
							<?php } ?>

							<?php if($v == 'price'){ ?>
							<dl>
								<dt>판매가</dt>
								<dd><span class="after_price"><strong><?php echo $pro_price; ?></strong>원</span>
									<?php // {{{회원등급혜택 ?>
									<?php if( $groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) { ?>
									<span class="point_plus">
										<span class="txt">회원할인</span> <strong><?=odt_number_format($groupSetInfo['mgs_sale_price_per'],1)?>%</strong>
									</span>
									<?php } ?>
									<?php // {{{회원등급혜택 ?>
								</dd>
							</dl>
							<?php } ?>

							<?php if($pro_point && $v == 'point'){ ?>
							<dl>
								<dt><span class="tit">적립금</span></dt>
								<dd><span class="point"><strong><?php echo $pro_point; ?></strong>원</span>
									<?php // {{{회원등급혜택 ?>
									<?php if( $groupSetUse === true && $groupSetInfo['mgs_give_point_per'] > 0 ) { ?>
									<span class="point_plus">
										<span class="txt">회원추가적립</span> <strong><?=odt_number_format($groupSetInfo['mgs_give_point_per'],1)?>%</strong>
									</span>
									<?php } ?>
									<?php // {{{회원등급혜택 ?>
								</dd>
							</dl>
							<?php } ?>

							<?php if(($pro_maker || $pro_orgin) && $v == 'maker/orgin'){ ?>
							<dl>
								<dt>제조사/원산지</dt>
								<dd><?php echo implode(' / ', array_filter(array($pro_maker, $pro_orgin))); //<!-- 슬래시 사이 간격 유지 / 제조사,원산지 중 한개만 있을경우 슬래시 삭제--> ?></dd>
							</dl>
							<?php } ?>

							<?php if($pro_brand_name && $v == 'brand'){ ?>
							<dl>
								<dt>브랜드</dt>
								<dd>
									<span class="brand_tx"><?php echo $pro_brand_name; ?></span>
									<a href="/?pn=product.brand_list&uid=<?php echo $pro_brand_uid; ?>" target="_blank"  class="btn_brand">브랜드 다른 상품보기</a>
								</dd>
							</dl>
							<?php } ?>

							<?php if($v == 'deliveryInfo'){ ?>
							<dl>
								<dt>배송정보</dt>
								<dd><?php echo $pro_del_info; //<!-- 슬래시 사이 간격 유지 --> ?></dd>
							</dl>
							<?php } ?>

							<?php if($v == 'deliveryPrice'){ ?>
							<dl>
								<dt>배송비</dt>
								<dd>
									<?php
										echo '<span class="delivery">'.$pro_delivery.'</span>';
									?>

									<?php // {{{무료배송이벤트}}} ?>
									<?php if( $freeEventChk === true && $p_info['p_free_delivery_event_use'] == 'Y' ) { ?>
									<span class="point_plus delivery_free">
										<strong><?=number_format($freeEventInfo['minPrice'])?>원</strong>이상 주문 시 무료배송 이벤트 진행
									</span>
									<?php } ?>
									<?php // {{{무료배송이벤트}}} ?>


								</dd>
							</dl>
							<?php } ?>

							<?php if($ex_coupon['name'] && $ex_coupon[1] && $v == 'coupon'){ ?>
								<!-- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22-->
								<dl>
									<dt><?php echo $arrDisplayPinfo['coupon'] ?></dt>
									<dd>
										<!-- 상품쿠폰 (주문에서도 동일하게 사용할 예정) -->
										<div class="view_coupon">
											<span class="coupon_name">
												<?php echo stripslashes($ex_coupon['name']); ?>
											</span>
											<span class="coupon_about">
											<?php if($ex_coupon[1]=="price"){
												echo number_format($ex_coupon['price']); ?>원
											<?php }else{?>
												<?php echo floor($ex_coupon['per']*10)/10; ?>%
												<?php if($ex_coupon['max']>0){?>
													<span class="txt">( 최대: <?php echo number_format($ex_coupon['max']); ?>원 할인 )</span>
												<?php }?>
											<?php }?>
										</div>
									</dd>
								</dl>
							<?php } ?>

							<?php if( $groupSetUse === true && $v== 'groupSet') { // {{{회원등급혜택}}} ?>
							<dl>
								<dt><?php echo $arrDisplayPinfo['groupSet'] ?></dt>
								<dd>
								<?php
									$printGroupSetPer = array();
									if( $groupSetInfo['mgs_sale_price_per'] > 0)
									$printGroupSetPer[] =  "<strong>할인 : <em>".number_format($groupSetInfo['mgs_sale_price_per'],1)."</em>%</strong>";
									if( $groupSetInfo['mgs_give_point_per'] > 0)
									$printGroupSetPer[] = " <strong>추가적립 : <em>".number_format($groupSetInfo['mgs_give_point_per'],1)."</em>%</strong>";

									echo ''.implode(" / ",$printGroupSetPer).'';


								?>
								</dd>
							</dl>
							<?php } // {{{회원등급혜택}}} ?>

						<?php } ?>

					<?php } ?>

					<?php
						// -- 옵션 없을 경우 ----
						if($p_info['p_option_type_chk'] == 'nooption' && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){
					?>
							<!-- 상품옵션없을 경우 수량표기는 여기에/없으면 dl 숨김 -->
							<dl>
								<dt>수량</dt>
								<dd>
									<div class="view_counter">
										<?php if($p_info['p_stock'] > 0){ ?>
											<a href="#none" onclick="pro_cnt_down(); return false;" class="btn_down" title="빼기"><span class="shape"></span></a>
											<input type="text" name="option_select_cnt" id="option_select_cnt" class="updown_input" value="1" readonly>
											<a href="#none" onclick="pro_cnt_up(); return false;" class="btn_up" title="더하기"><span class="shape"></span></a>
										<?php }else{ ?>
											품절<input type="hidden" name="option_select_cnt" class="input_num" id="option_select_cnt" value="0" />
										<?php } ?>
										<input type="hidden" name="option_select_expricesum" ID="option_select_expricesum" value="<?php echo ($p_info['p_price']-getGroupSetPer($p_info['p_price'],'price',$pcode)); ?>">
										<input type="hidden" name="option_select_type" id="option_select_type" value="<?php echo $p_info['p_option_type_chk']; ?>">
									</div>
								</dd>
							</dl>
					<?php
						// -- 옵션 있을 경우 ----
						}else if(count($options) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){
					?>

					<dl class="view_option" style="display:none;">
						<dt>필수 옵션</dt>
						<dd>

							<?php if($p_info['p_stock'] > 0){ ?>

								<?php
									// ------------------------- 1차 옵션 설정 -------------------------
									// 1차 옵션이 normal 형일 경우 처리
									//			옵션형태 : normal , color , size
									if($p_info['p_option1_type'] == 'normal') {
								?>
									<!-- 선택하면 클래스값 if_selected -->
									<div class="select">
										<select name="_option_select1"  onchange="option_select_tmp('1', '<?php echo $p_info['p_code']; ?>')"  ID="option_select1_id">
											<option value="">옵션을 선택해주세요.(필수)</option>
											<?php foreach( $options as $k=>$sr){ ?>
												<option value="<?php echo $sr['po_uid']; ?>">
													<?php echo $sr['po_poptionname']; ?>
													<?php
														if($p_info['p_option_type_chk'] == '1depth'){
															echo ($sr['po_cnt'] > 0 ? ($isOptionStock ? ' (잔여:' . number_format($sr['po_cnt']) . ')' : null) . ' / ' . number_format($sr['po_poptionprice']) . '원' : ' (품절)');
														}
													?>
												</option>
											<?php } ?>
										</select>
									</div>
								<?php
									} // 1차 옵션이 normal 형일 경우 처리


									// 1차 옵션이 color 형일 경우 처리
									else if($p_info['p_option1_type'] == 'color') {
								?>
									<!-- 컬러는 #컬러값을 입력하거나 이미지를 등록할 수 있도록 / 이미지: [모바일]150 * 150, [PC]35 * 35  / 품절일 때 label에 none 추가 / 선택안되게 -->
									<div class="view_option_color">
										<ul>
											<?php
												foreach( $options as $k=>$sr){

													// 품절여부
													$app_soldout_class = ($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ? 'none' : '');

													//색상 or 이미지
													$app_color_name = (
														$sr['po_color_type'] == 'img' ?
															'background-image:url(\'/upfiles/option/'.$sr['po_color_name'].'\');' :
															'background:' . $sr['po_color_name']
													);
											?>
												<li>
													<!-- 옵션설명값 & 품절시 none 클래스 처리 -->
													<label title="<?=$sr['po_poptionname']?>" class="<?=$app_soldout_class?>">
														<input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')" /><span class="tx"><span class="shape"  style="<?=$app_color_name?>"></span></span>
													</label>
												</li>
											<?php } ?>
										</ul>
										<input type="hidden" name="_option_select1" ID="option_select1_id" value="">
									</div>
								<?php
									} // 1차 옵션이 color 형일 경우 처리


									// 1차 옵션이 size 형일 경우 처리
									else if($p_info['p_option1_type'] == 'size') {
								?>
									<!-- 품절일 때 label에 none 추가 / 선택안되게 -->
									<div class="view_option_size">
										<ul>
											<?php
												foreach( $options as $k=>$sr){

													// 품절여부
													$app_soldout_class = ($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ? 'none' : '');

											?>
												<li>
													<label class="<?=$app_soldout_class?>">
														<input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')"  <?=($app_soldout_class == 'none' ? 'disabled' : '')?> /><span class="tx"><?=$sr['po_poptionname']?></span>
													</label>
												</li>
											<?php } ?>
										</ul>
										<input type="hidden" name="_option_select1" ID="option_select1_id" value="">
									</div>
								<?php
									} // 1차 옵션이 size 형일 경우 처리
									// ------------------------- 1차 옵션 설정 -------------------------
								?>


							<?php }else{ ?>
								<div class="select">
									<select name="">
										<option value="">품절</option>
									</select>
								</div>
							<?php } ?>


							<?php
								if($p_info['p_stock'] > 0){

									//일반형일 경우 2차 옵션 클래스
									$app_depth2_class="select";
									switch($p_info['p_option2_type']){
										case "color": $app_depth2_class="view_option_color"; break;//컬러형일 경우 옵션 클래스
										case "size": $app_depth2_class="view_option_size"; break;//사이즈형일 경우 옵션 클래스
									}

									//일반형일 경우 3차 옵션 클래스
									$app_depth3_class="select";
									switch($p_info['p_option3_type']){
										case "color": $app_depth3_class="view_option_color"; break;//컬러형일 경우 옵션 클래스
										case "size": $app_depth3_class="view_option_size"; break;//사이즈형일 경우 옵션 클래스
									}

							?>

								<?php if( in_array($p_info['p_option_type_chk'], array('2depth','3depth')) ){  ?>
									<div class="<?=$app_depth2_class?> before" id="span_option2" data-idx="2">
										<?=($p_info['p_option2_type'] == 'normal' ? '<select name=""><option value="0">상위옵션을 먼저 선택해주세요.(필수)</option></select>' : '<div class="this">상위옵션을 먼저 선택해주세요.(필수)</div>')?>
									</div>
								<?php } ?>

								<?php if($p_info['p_option_type_chk'] == '3depth'){ ?>
									<div class="<?=$app_depth3_class?> before" id="span_option3" data-idx="3">
										<?=($p_info['p_option3_type'] == 'normal' ? '<select name=""><option value="0">상위옵션을 먼저 선택해주세요.(필수)</option></select>' : '<div class="this">상위옵션을 먼저 선택해주세요.(필수)</div>')?>
									</div>
								<?php } ?>

							<?php } ?>

							<!-- <input type="hidden" name="_option_select1" ID="option_select1_id" value=""> // 2019-01-10 SSJ :: 중복으로 제거 -->

						</dd>
					</dl>



					<?php if(count($add_options)>0 && $p_info['p_stock'] > 0){ ?>
						<dl class="view_option">
							<dt>추가 옵션</dt>
							<dd>
								<?php foreach($add_options as $k=>$v) { ?>
									<div class="select "><!-- 선택하면 클래스값 if_selected -->
										<select name='_add_option_select_<?php echo ($k+1); ?>' id="add_option_select_<?php echo ($k+1); ?>_id" class='add_option add_option_chk' onchange="add_option_select_add('<?php echo $pcode; ?>' , this.value); this.value=''; return false;">
											<option value=""><?php echo trim($v['pao_poptionname']); ?></option>
											<?php foreach($v['add_sub_options'] as $key=>$value){ ?>
												<option value="<?php echo $value['pao_uid']; ?>" data-uid="<?php echo $value['pao_uid']; ?>">
													<?php echo $value['pao_poptionname'].($value['pao_cnt']>0 ? ($isOptionStock ? ' (잔여:'.number_format($value['pao_cnt']).')' : null) . ' / '. number_format($value["pao_poptionprice"]) . '원' : ' (품절)'); ?>
												</option>
											<?php } ?>
										</select>
									</div>
								<?php } ?>
							</dd>
						</dl>
					<?php } ?>


					<?php } ?>


				</div>


				<?php if(count($options) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false){ ?>
				<!-- 선택한 옵션 -->
				<div style="display:none;" ><!--class="view_option result" id="span_seleced_list"-->
					<dl>
						<dt class="if_before">구매하실 상품 옵션을 선택해 주시기 바랍니다.</dt>
					</dl>
				</div>
				<?php } ?>

			<!-- ◆배송/교환/반품 안내 -->
			<?php // JJC : 2019-05-15 : 판매자 정보 ?>
			<div class="view_notify" style="display:none;">
					<div class="sub_tit">판매자 정보</div>
					<div class="table_box">
							<table>
									<tbody>
											<tr>
													<th>상호명</th>
													<td><?php echo $app_adshop; ?></td>
											</tr>
											<tr>
													<th>대표전화</th>
													<td><?php echo $app_glbtel; ?></td>
											</tr>
											<tr>
													<th>대표자</th>
													<td><?php echo $app_ceo_name; ?></td>
											</tr>
											<tr>
													<th>팩스전화</th>
													<td><?php echo $app_fax; ?></td>
											</tr>
											<tr>
													<th>사업자등록번호</th>
													<td><?php echo $app_company_num; ?></td>
											</tr>
											<tr>
													<th>대표 이메일</th>
													<td><?php echo $app_ademail; ?></td>
											</tr>
											<tr>
													<th>통신판매업번호</th>
													<td><?php echo $app_company_snum; ?></td>
											</tr>
											<tr>
													<th>사업장소재지</th>
													<td><?php echo $app_company_addr; ?></td>
											</tr>
									</tbody>
							</table>
					</div>
			</div>
			<?php // JJC : 2019-05-15 : 판매자 정보 ?>


		<!-- 배송 기본정보 -->
		<div class="view_notify" style="display:none;">
			<div class="sub_tit">배송 기본정보</div>
			<div class="table_box">
				<table>
					<tbody>
						<tr>
							<th>지정택배사</th>
							<td><?php echo $del_company; ?></td>
						</tr>
						<tr>
							<th>평균배송기간</th>
							<td><?php echo $del_date; ?></td>
						</tr>
						<tr>
							<th>기본배송비</th>
							<td><?php echo $pro_delivery; ?></td>
						</tr>
						<tr>
							<th>반송주소</th>
							<td><?php echo $del_return_addr; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<?php
			if(count($arrProGuideType)>0){
				foreach($arrProGuideType as $_guide_key=>$_guide_title){
					// 내용을 저장할 변수 초기화
					$_guide_text = '';

					// 내용 추출 - 직접입력
					if($p_info['p_guide_type_'.$_guide_key] == 'manual'){
						$_guide_text = $p_info['p_guide_'.$_guide_key];
					}
					// 내용 추출 - 선택입력
					else if($p_info['p_guide_type_'.$_guide_key] == 'list'){
						$_guide_text = _MQ_result(" select g_content  from smart_product_guide where g_uid = '". $p_info['p_guide_uid_'.$_guide_key] ."' and g_user in ('_MASTER_', '". $p_info['p_cpid'] ."') ");
					}
					// 사용안함 체크
					else{
						continue;
					}

					// 내용이 없으면 노출하지 않음
					if(trim($_guide_text) == ''){ continue; }
		?>

					<!-- 배송 구매/배송안내 / 제목과 내용 모두 관리자에서 설정가능 -->
					<div class="view_guide">
						<div class="sub_tit"><?php echo stripslashes($_guide_title); ?><span class="add">※ 상품정보에 별도 기재된 경우 ,아래의 내용보다 우선하여 적용됩니다.</span></div>
						<div class="txt_box editor"><?php echo stripslashes($_guide_text); ?></div>
					</div>

		<?php
				}
			}
		?>

	</div>
	<!-- / ◆배송/교환/반품 안내 -->
	
	<div class="c_view_board" id="eval_ajax" style="display:none;">
		<?php include OD_PROGRAM_ROOT."/product.eval.form.php"; ?>
	</div>

</div>
<!-- / ◆상품후기 -->







<!-- ◆ 상세탭 -->
<div class="view_tab js_qna_position" style="display:none;">
	<div class="tab_box">
		<ul>
			<!-- 활성화시 hit클래스  -->
			<li class=""><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab"><span class="tx">상품정보</span></a></li>
			<li class=""><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab"><span class="tx">상품후기<br><em class="num eval_cnt">(<?php echo $eval_cnt; ?>)</em></span></a></li>
			<li class="hit"><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab"><span class="tx">상품문의<br><em class="num qna_cnt">(<?php echo $qna_cnt; ?>)</em></span></a></li>
			<li class=""><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab"><span class="tx">배송/교환/<br>반품안내</span></a></li>
		</ul>
	</div>
</div>
<!-- / ◆ 상세탭 -->







<!-- ◆상품문의 -->
<div class="view_conts" style="display:none;">

	<div class="c_view_board" id="qna_ajax">
		<?php include OD_PROGRAM_ROOT."/product.qna.form.php"; ?>
	</div>

</div>
<!-- / ◆상품문의 -->




<!-- ◆ 상세탭 -->
<div class="view_tab js_guide_position" style="display:none;">
	<div class="tab_box">
		<ul>
			<!-- 활성화시 hit클래스  -->
			<li class=""><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab"><span class="tx">상품정보</span></a></li>
			<li class=""><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab"><span class="tx">상품후기<br><em class="num eval_cnt">(<?php echo $eval_cnt; ?>)</em></span></a></li>
			<li class=""><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab"><span class="tx">상품문의<br><em class="num qna_cnt">(<?php echo $qna_cnt; ?>)</em></span></a></li>
			<li class="hit"><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab"><span class="tx">배송/교환/<br>반품안내</span></a></li>
		</ul>
	</div>
</div>
<!-- / ◆ 상세탭 -->












</div>






<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript">
	var old_idx = "0";
	var now_idx = "1";
	var max_idx = $(".photo_thumb img").length;
	var auto_change = true;
	function view_thumb_img(idx,mode) {
		if(!auto_change && mode=="auto") return;

		img_src = $("#thumb_"+idx).attr("src");
		img_src = img_src.replace("thumbs_s_","");
		$("#main_img").attr("src",img_src);

		// 셈네일 이미지 class on/off
		$("#thumb_"+idx).removeClass("off");
		$("#thumb_"+idx).addClass("on");
		$("#thumb_"+old_idx).removeClass("on");
		$("#thumb_"+old_idx).addClass("off");

		old_idx = idx;
		now_idx = idx*1+1 > max_idx ? 1 :idx*1+1;
	}
	$(".photo_thumb .fix").hover(
		function() {
			auto_change = false;
		},
		function() {
			auto_change = true;
		}
	);

	function view_thumb_img_auto() {
		view_thumb_img(now_idx,"auto");
		setTimeout(view_thumb_img_auto,2000);
	}

	function sale_info(mode) {
		if(mode == "show") $(".ly_notice").show();
		else $(".ly_notice").hide();
	}

	function pro_cnt_up() {
        cnt = $("#option_select_cnt").val()*1;
        // 2019-07-24 SSJ :: 옵션이 없는 상품의 재고체크 추가
        $.ajax({
            url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
            data: {'_mode':'get_pstock' , 'pcode':'<?php echo $pcode; ?>'},
            type: 'post',
            dataType: 'text',
            success: function(data){
                if(data == 0){
                    alert('해당 상품은 품절된 상품입니다.');
                    location.reload();
                }else if(cnt+1 > data){
                    alert('해당 상품의 재고량이 부족합니다.');
                    $("#option_select_cnt").val(data);
                }else{
                    $("#option_select_cnt").val(cnt+1);
                }
                update_sum_price();
            }
        });
    }
    function pro_cnt_down() {
        cnt = $("#option_select_cnt").val()*1;
        // 2019-07-24 SSJ :: 옵션이 없는 상품의 재고체크 추가
        $.ajax({
            url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
            data: {'_mode':'get_pstock' , 'pcode':'<?php echo $pcode; ?>'},
            type: 'post',
            dataType: 'text',
            success: function(data){
                if(data == 0){
                    alert('해당 상품은 품절된 상품입니다.');
                    location.reload();
                }else if(cnt-1 > data){
                    alert('해당 상품의 재고량이 부족합니다.');
                    $("#option_select_cnt").val(data);
                }else{
                    if(cnt > 1) $("#option_select_cnt").val(cnt-1);
                }
                update_sum_price();
            }
        });
    }
	function update_sum_price() {
		var sumprice = 0;
		sumprice = String($("#option_select_expricesum").val()*$("#option_select_cnt").val());
		if(sumprice == "NaN") sumprice = "0";
		$("#option_select_expricesum_display").html(sumprice.comma());
	}


	$(document).ready(function() {
		// 섬네일 이미지 자동 변경
		//view_thumb_img_auto();
		update_sum_price();
	});

	function cate_change(obj) {
		location.href="/?pn=product.list&cuid="+obj.value;
	}

	// SNS공유하기 버튼
	function sendSNS(type) {
		var url = 'http://<?=$system['host']?>/?pn=product.view&pcode=<?=$pcode?>';
		var title = '<?=$pro_name?>';
		var image = '<?=$main_img?>';
		var desc = '<?=cutstr(trim(str_replace("  "," ",str_replace(":","-",str_replace("\t"," ",str_replace("\r"," ",str_replace("\n"," ",str_replace("'","`",stripslashes(($p_info['p_subname']?$p_info['p_subname']:$siteInfo['s_glbtlt']))))))))) , 24 , "..")?>';
		if(type == 'kakao') {
			try {
				Kakao.cleanup();
				Kakao.init('<?php echo $siteInfo['kakao_js_api']; ?>');
				Kakao.Link.sendDefault({
					objectType: 'feed',
					content: {
						title: title,
						description: desc,
						imageUrl: image,
						imageWidth: 470, // 없으면 이미지가 찌그러짐
						imageHeight: 470, // 없으면 이미지가 찌그러짐
						link: {
							mobileWebUrl: url,
							webUrl: url
						}
					},
					buttons: [
						{
							title: og_site_name,
							link: {
								mobileWebUrl: url,
								webUrl: url
							}
						}
					],
					installTalk: true,
					fail: function(err) {
						alert(JSON.stringify(err));
					}
				});
			} catch(e) {
				alert('카카오톡으로 공유 할 수 없는 상태 입니다.');
			};
		}
		else if(type=='kakao-story') {
			try {

				Kakao.Story.open({
					url: url,
					text: title
				});

			} catch(e) {
				alert('카카오스토리로 공유 할 수 없는 상태 입니다.');
			};
		}
		else if(type=='facebook') {
			postToFeed(title, desc, url, image);
		}
		else if(type=='twitter') {
			var wp = window.open("http://twitter.com/intent/tweet?text=" + encodeURIComponent(title) + " " + encodeURIComponent(url), 'twitter', 'width=550,height=256');
			if(wp) { wp.focus(); }
		}
		else if(type=='pinterest') {
			var href = "http://www.pinterest.com/pin/create/button/?url="+encodeURIComponent(url)+"&media="+encodeURIComponent(image)+"&description="+encodeURIComponent(title);
			var a = window.open(href, 'pinterest', 'width=734, height=734');
			if ( a ) {
				a.focus();
			}
		}
		$.ajax({
			data: {'pcode':'<?=$pcode?>','type':type},
			type: 'GET', cache: false, url: '<?php echo OD_PROGRAM_URL; ?>/ajax.sns.update.php',
			success: function(data) { return true; },
			error:function(request,status,error){ alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error); }
		});
	}


	function option_select_tmp(idx,pcode) {

		if(idx+'depth' == '<?php echo $p_info['p_option_type_chk']; ?>'){
			option_select_add(pcode);
		}else{
			option_select(idx,pcode);
		}

	}





	function option_select_tmp2(idx,pro_depth,pouid,pcode) {

		$('#option_select'+idx+'_id').val(pouid);
		if(idx+'depth' == pro_depth){
			option_select_add(pcode);
		}else{
			option_select(idx,pcode);
		}

	}


	// 해시이동(주소해시에 상응하는 클래스 객체가 있다면 스크롤 자동 이동)
	$(function() {
		var UrlHash = window.location.hash;
		if(UrlHash) {
			UrlHash = UrlHash.replace('#', '');
			if($('.'+UrlHash).length > 0) {
				scrolltoClass('.'+UrlHash, -100);
			}
		}
	});
</script>
<script src="<?php echo $SkinData['skin_url']; ?>/js/option_select.js" type="text/javascript"></script>