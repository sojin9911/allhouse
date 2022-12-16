<?php
// 프로세스 파일은 /program/ajax.quick_view.php 에서 하세요.
?>
<!-- ◆ 상품 간편보기 레이어팝업창, 바탕클릭 시 닫힘 ㅣ 상품상세 소스를 그대로 사용하여 이 부분에서만 감출것만 감추는 방식으로 구현, 협의바람 -->
<div class="item_quick_view js_quick_view_box" style="display: none">
	<a href="#none" class="btn_close js_quick_view_box_close" title="간편보기 닫기"></a>
	<!-- ◆ 상품상세 : 사진,기본정보 -->
	<div class="view_top">
		<div class="layout_fix">
			<ul class="ul">
				<li class="li view_photo">
					<!-- 상품 사진 -->
					<div class="photo_box">
						<!-- 큰사진 롤링박스 -->
						<div class="rolling_box js_qphoto_large_slider">
							<!-- 이 div 롤링 / 470 * 470 -->
							<?php
								if(count($pro_img)>0){
									foreach($pro_img as $k=>$v){
										$_pimg = get_img_src($v);
							?>
								<div class="thumb" style="<?php echo ($k>0?'display:none;':null); ?>">
									<?php if($_pimg){ ?><div class="real_img"><img src="<?php echo $_pimg; ?>" alt="<?php echo addslashes($pro_name); ?>"></div><?php } ?>
									<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($pro_name); ?>"></div>
								</div>
							<?php
									}
								}else{
							?>
								<div class="thumb">
									<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($pro_name); ?>"></div>
								</div>
							<?php } ?>
						</div>
						<?php if(count($pro_img) > 1){ ?>
							<!-- 롤링사진 1개일 경우 숨김 -->
							<div class="rolling_thumb">
								<ul class="js_qphoto_large_pager">
									<!-- li 최대 5개 노출 / 활성화시 li에 hit클래스 추가 -->
									<?php
										foreach($pro_img as $k=>$v){
											$_pimg = get_img_src('thumbs_s_' . $v);
											if($_pimg == '') $_pimg = $SkinData['skin_url'] . '/images/skin/thumb.gif';
									?>
											<li class="<?php echo ($k === 0?'hit':''); ?>" ><a href="#none;" onclick="return false;" data-slide-index="<?php echo $k; ?>" class="<?php echo ($k === 0?'active':''); ?>"><img src="<?php echo $_pimg; ?>" alt=""></a></li>
									<?php } ?>
								</ul>
							</div>
							<script type="text/javascript">
								$(function() {
									setTimeout(function() {
										$('.js_qphoto_large_slider').find('.thumb').show();
										var qphoto_large = $('.js_qphoto_large_slider').bxSlider({
											auto: true,
											autoHover: false,
											pagerCustom: '.js_qphoto_large_pager',
											controls: false,
											maxSlides:1,
											moveSlides:1,
											slideMargin : 0,
											slideWidth: 472,
											onSliderLoad: function() { },
											onSlideBefore: function($slideElement, oldIndex, newIndex) {
												$('.js_qphoto_large_pager li').removeClass('hit');
												$('.js_qphoto_large_pager li a[data-slide-index='+newIndex+']').parent().addClass('hit');
												qphoto_large.stopAuto();
											},
											onSlideAfter: function($slideElement, oldIndex, newIndex) { qphoto_large.startAuto(); }
										});
									}, 500);
								});
							</script>
						<?php } ?>
					</div>


					<!-- 상품평점/sns공유 -->
					<div class="view_summery">
						<div class="score">
							<!-- 상품후기 탭으로 이동 -->
							<a href="/?pn=product.view&pcode=<?php echo $qpcode; ?>#js_eval_position" class="upper_link" title=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif" alt="" /></a>
							<span class="mark"><span class="star" style="width:<?php echo $star_persent; ?>%"></span></span>
							<!-- 0과 10 제외하고 소수점 한자리까지 노출 -->
							<span class="num">
								<?php
									// <!-- 0과 10 제외하고 소수점 한자리까지 노출 -->
									if($star_persent == 0 || $star_persent == 100)
										echo number_format($star_persent/10,0);
									else
										echo number_format($star_persent/10,1);
								?>
							</span>
							<span class="total">(<?php echo $eval_cnt; ?>건)</span>
						</div>
						<?php
						$SNSSendUse = array($siteInfo['facebook_share_use'], $siteInfo['kakao_share_use'], $siteInfo['twitter_share_use'], $siteInfo['pinter_share_use']);
						if(in_array('Y', $SNSSendUse)) {
						?>
							<div class="sns">
								<ul>
									<?php if($siteInfo['kakao_share_use'] == 'Y') { ?>
										<li>
											<a href="#none" onclick="QsendSNS('kakao'); return false;" class="btn" title="카카오톡 공유하기">
												<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_kakao.png" class="on" alt="카카오톡 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_kakao_ov.png" class="ov" alt="카카오톡 공유하기">
											</a>
										</li>
									<?php } ?>
									<?php if($siteInfo['facebook_share_use'] == 'Y') { ?>
										<li>
											<a href="#none" onclick="QsendSNS('facebook'); return false;" class="btn" title="페이스북 공유하기">
												<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_face.png" class="on" alt="페이스북 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_face_ov.png" class="ov" alt="페이스북 공유하기">
											</a>
										</li>
									<?php } ?>
									<?php if($siteInfo['twitter_share_use'] == 'Y') { ?>
										<li>
											<a href="#none" onclick="QsendSNS('twitter'); return false;" class="btn" title="트위터 공유하기">
												<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_twitt.png" class="on" alt="트위터 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_twitt_ov.png" class="ov" alt="트위터 공유하기">
											</a>
										</li>
									<?php } ?>
									<?php if($siteInfo['pinter_share_use'] == 'Y') { ?>
										<li>
											<a href="#none" onclick="QsendSNS('pinterest'); return false;" class="btn" title="핀터레스트 공유하기">
												<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_pin.png" class="on" alt="핀터레스트 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_pin_ov.png" class="ov" alt="핀터레스트 공유하기">
											</a>
										</li>
									<?php } ?>
								</ul>
							</div>
						<?php } ?>
					</div>


					<?php if(count($pro_hashtag)>0){ ?>
						<!-- 해시태그 -->
						<div class="view_hash">
							<ul>
								<li class="title">이 상품의<br>관련 태그</li>
								<li>
									<?php foreach($pro_hashtag as $k=>$v){ ?>
										<a href="/?pn=product.search.list&search_hashtag=<?php echo urlencode(trim($v)); ?>" class="btn" target="_blank">#<?php echo trim($v); ?></a>
									<?php } ?>
								</li>
							</ul>
						</div>
					<?php } ?>
				</li>
				<li class="li view_info">
					<!-- 상품이름/설명/아이콘 -->
					<div class="view_name">
						<?php echo $pro_icon; ?>
						<div class="title"><?php echo $pro_name; ?></div>
						<?php if($pro_subname){ ?>
							<div class="sub_name"><?php echo $pro_subname; ?></div>
						<?php } ?>
					</div>


					<?php if(count($ex_display_pc) > 0){ ?>
						<!-- 상품기본정보 -->
						<div class="view_default">
							<?php foreach($ex_display_pc as $k=>$v){ ?>

								<?php if($pro_screenprice && $v == 'screenPrice'){ ?>
								<dl>
									<dt>소비자가</dt>
									<dd><span class="before_price"><strong><?php echo $pro_screenprice; ?></strong>원</span></dd>
								</dl>
								<?php } ?>

								<?php if($v == 'price'){ ?>
								<dl>
									<dt>판매가</dt>
									<dd>
										<span class="after_price"><strong><?php echo $pro_price; ?></strong>원</span>
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
									<dd>
										<span class="point"><strong><?php echo $pro_point; ?></strong>원</span>
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
										<a href="/?pn=product.brand_list&uid=<?php echo $pro_brand_uid; ?>" target="_blank" class="btn_brand">브랜드 다른 상품보기</a>
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
										// 배송비 <!-- 기본 <em>2,500</em>원 (<em>30,000</em>원 이상 무료) --><!-- 무료배송일경우, 아이콘없으면 텍스트로 노출 -->
										switch($p_info['p_shoppingPay_use']){
											case 'Y': $pro_delivery = '개별배송 <em>' . number_format($pro_delivery_info['price']) . '</em>원'; break;
											case 'N': $pro_delivery = '기본 <em>' . number_format($pro_delivery_info['price']) . '</em>원 (<em>'.number_format($pro_delivery_info['freePrice']) . '</em>원 이상 무료)'; break;
											case 'F': $pro_delivery = '무료배송'; break; //무료배송 // SSJ :: 무료배송 아이콘대신 문구로 노출 ---- 2020-02-05
											// 상품별 배송비 추가 kms 2019-07-22
											case 'P': $pro_delivery = '상품별배송 <em>' . number_format($pro_delivery_info['price']) . '</em>원'.($pro_delivery_info['freePrice'] > 0 ? ' (<em>'.number_format($pro_delivery_info['freePrice']) . '</em>원 이상 무료)' : null); break; // 상품별 // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
										}
										echo '<span class="delivery">'.$pro_delivery.'</span>';
									?>

									<?php // {{{무료배송이벤트}}} ?>
									<?php if( $freeEventChk === true && $p_info['p_free_delivery_event_use'] == 'Y' ) { ?>
									<span class="point_plus delivery_free">
										<strong><?=number_format($freeEventInfo['minPrice'])?>원</strong><span class="txt">이상 주문 시 무료배송 이벤트 진행</span>
									</span>
									<?php } ?>
									<?php // {{{무료배송이벤트}}} ?>


									</dd>
								</dl>
								<?php } ?>

								<?php if($ex_coupon['name'] && $ex_coupon['price'] && $v == 'coupon'){ ?>
								<dl>
									<dt>할인혜택</dt>
									<dd>
										<!-- 상품쿠폰 (주문에서도 동일하게 사용할 예정) -->
										<div class="view_coupon">
											<span class="coupon_name"><?php echo stripslashes($ex_coupon['name']); ?></span><span class="coupon_about"><?php echo $ex_coupon['price']; ?>원</span>
											<div class="guide">
												<div class="open_box">
													<div class="tt">상품 쿠폰 사용하는 방법</div>
													<span class="txt">상품 쿠폰이 있는 상품을 구매하실 경우 주문서 작성 시 쿠폰을 선택하시면 해당 상품 가격에 할인이 적용되어 최종금액에 반영됩니다.</span>
												</div>
											</div>
										</div>
									</dd>
								</dl>
								<?php } ?>
							<?php } ?>

						</div>
					<?php } ?>


					<!-- 상품 상세보기, 찜하기 버튼 -->
					<div class="view_btn">
						<ul>
							<li><a href="/?pn=product.view&pcode=<?php echo $p_info['p_code']; ?>" class="btn btn_cart">상품 상세보기</a></li>
						</ul>
						<!-- 찜하기버튼 / 활성화 시 hit 클래스 추가 -->
						<a href="#none" class="btn btn_wish js_wish<?php echo (is_wish($p_info['p_code'])?' hit':null); ?>" data-pcode="<?php echo $p_info['p_code']; ?>" title="찜하기"></a>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<!-- / ◆상품상세 : 사진,기본정보 -->
</div>
<!-- / 상품 간편보기 레이어팝업창 -->

<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript">
	$(document).on('click', '.js_quick_view_box_close', function(e) {
		e.preventDefault();
		$(this).closest('.js_quick_view_box').fadeOut(function() {
			$(this).closest('.js_quick_view_box').remove();
		});
	});

	// SNS공유하기 버튼
	function QsendSNS(type) {
		var url = '<?=$system['url']?>/?pn=product.view&pcode=<?=$pcode?>';
		var title = '<?=$pro_name?>';
		var image = '<?=$main_img?>';
		var desc = '<?=cutstr(trim(str_replace("  "," ",str_replace(":","-",str_replace("\t"," ",str_replace("\r"," ",str_replace("\n"," ",str_replace("'","`",stripslashes(($p_info['p_subname']?$p_info['p_subname']:$siteInfo['s_glbtlt']))))))))) , 24 , "..")?>';
		if(type == 'kakao') {
			try {
				if(Kakao) {
					Kakao.init('<?php echo $siteInfo['kakao_js_api']; ?>');
				}
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
				console.log(e);
				alert('카카오톡으로 공유 할 수 없는 상태 입니다.');
			};
		}
		else if(type=='facebook') {
			postToFeedLayer(title, desc, url, image);
		}
		else if(type=='twitter') {
			var wp = window.open("http://twitter.com/intent/tweet?text=" + encodeURIComponent(title) + " " + encodeURIComponent(url), 'twitter', 'width=550,height=256');
			if(wp) { wp.focus(); }
		}
		else if(type=='pinterest') {
			var href = "http://www.pinterest.com/pin/create/button/?url="+encodeURIComponent(url)+"&amp;media="+encodeURIComponent(image)+"&amp;description="+encodeURIComponent(title);
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
</script>