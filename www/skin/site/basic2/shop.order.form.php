<?php
	$arr_product_sum = $arr_push_product = array();  // 변수 초기화
?>

<form name="frm" method="post" action="<?php echo OD_PROGRAM_URL; ?>/shop.order.pro.php">
<input type="hidden" name="order_type" value="<?php echo $order_type; ?>"/>

	<div class="c_section c_shop">
		<div class="layout_fix order_wrap">
			<!-- ◆공통페이지 타이틀 -->
			<div class="c_page_tit">
				<div class="title">주문/결제</div>
				<!-- 단계별 페이지 -->
				<div class="c_process">
					<ul>
						<!-- 해당 페이지 hit -->
						<li><span class="num">01</span><span class="tit">장바구니</span></li>
						<li class="hit"><span class="num">02</span><span class="tit">주문/결제</span></li>
						<li><span class="num">03</span><span class="tit">주문완료</span></li>
					</ul>
				</div>
			</div>
			<!-- /공통페이지 타이틀 -->

			<!-- ◆장바구니 리스트 -->
			<div class="c_group_tit order"><span class="tit">주문 상품</span></div>
			<?php foreach($arr_cart as $crk=>$crv) {  ?>
				<!-- ◆장바구니 리스트 -->
				<div class="c_cart_list">

					<div class="table_top">
						<div class="tit_box">
							<span class="txt">업체배송</span>
							<span class="txt shop_tit"><?php echo $arr_customer[$crk]['cName']; ?></span>
						</div>
						<div class="guide_txt"><?php echo ($arr_customer[$crk]['com_delprice_free'] > 0 ? '<strong>'. number_format($arr_customer[$crk]['com_delprice_free']) .'원</strong> 이상 구매시 배송비 무료 (개별배송 제외)' : ''); ?></div>
					</div>

					<div class="cart_table">
						<table>
							<colgroup>
								<col width="57"><col width="115"><col width="*"><col width="140"><col width="120"><col width="120">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">No.</th>
									<th scope="col">이미지</th>
									<th scope="col">상품 및 옵션 정보</th>
									<th scope="col">상품 금액</th>
									<th scope="col">배송비</th>
									<th scope="col">적립금</th>
								</tr>
							</thead>
							<tbody>
								<?php
									// -- 변수 초기화
									unset($del_chk_customer, $is_vat_free, $_num); // 2017-06-16 ::: 부가세율설정 ::: JJC
									$arr_product = array(); // 업체별 상품 합계
									$arr_per_product = array(); // 상품별 합계 // ----- JJC : 상품별 배송비 : 2018-08-16 -----

									// {{{LCY무료배송이벤트}}}
									$temp_delivery_sum = 0; // 무료배송일경우 임시로 저장하기 위한 배열

									foreach($crv as $k=>$v) { // 업체별 상품 반복 구간



										// No. 설정
										$_num++;
										/* 상품 정보 */
										$pr = $arr_product_info[$k]; // 업체 상품의 정보를 담는다.
										$pro_name	= strip_tags($pr['p_name']);	// 상품명
										$thumb_img	= get_img_src('thumbs_s_'.$pr['p_img_list_square']); // 상품 이미지
										if($thumb_img=='') $thumb_img = $SkinData['skin_url']. '/images/skin/thumb.gif';
										$pro_url = "/?pn=product.view&pcode=".$k; // 상품의 주소
										/* 상품 정보 끝 */

										// {{{회원등급혜택}}}
										unset($groupSetUse);
										if( $pr['p_groupset_use'] == 'Y' && is_login() == true ){
											if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
												$groupSetUse = true;
											}
										}
										// {{{회원등급혜택}}}

										// -- 변수 초기화
										unset($option_html , $sum_price , $sum_product_cnt, $sum_point);
										foreach($v as $sk => $sv) {

											// 2017-06-16 ::: 부가세율설정 ::: JJC
											$sv['p_vat'] = $siteInfo['s_vat_product'] == 'C' ? $sv['p_vat'] : $siteInfo['s_vat_product']; // SSJ : 2018-02-10 전체설정이 복합과세일때 상품의 과세설정을 그외는 전체설정을 따른다
											//if( $sv['p_vat'] == "N" ) {
											//	$is_vat_free ++;
											//} // 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 항상 현금영수증 노출되도록 주석처리

//											$option_tmp_name = !$sv['c_option1'] ? '옵션없음' : trim(($sv['c_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . $sv['c_option1'].' '.$sv['c_option2'].' '.$sv['c_option3']);
											$option_tmp_name = !$sv['c_option1'] ? '옵션없음' : trim($sv['c_option1'].' '.$sv['c_option2'].' '.$sv['c_option3']);
											$option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
											$option_tmp_cnt			= $sv['c_cnt'];
											$option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
											$app_point				= $sv['c_point'];

											// 상품 수량 select 값
                                            $c_option_color = "블랙";
											$buy_limit_array = array();
											$buy_max = 200; // 최고 구매갯수 설정
											$buy_limit = $sv['buy_limit'] ? min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$sv['buy_limit']) : min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$buy_max); // 구매제한이 없으면 재고만큼만 선택할수 있게 하되 max는 200
											for($i=1;$i<=$buy_limit;$i++) { $buy_limit_array[] = $i; }

                                            if ($sv['c_option1'] == "색상") {
                                                $c_option_color = $sv['c_option2'];
                                                continue;
                                            }

                                            $option_html .= '
												<ul>
													<li>
														<div class="opt_tit'. (!$sv['c_option1'] ? ' opt_none' : '' ) .'"> '.$c_option_color .' '. $option_tmp_name .'</div>
													</li>
													<li class="price"><strong>'. number_format($option_tmp_price) .'</strong>원</li>
													<li class="num"><strong>'. number_format($sv['c_cnt']) .'</strong>개</li>
												</ul>
											';

											//상품수 , 포인트 , 상품금액
											$arr_product["cnt"] += $option_tmp_cnt;//상품수
											// ----- SSJ : 추가옵션은 개별배송비 미적용 : 2020-02-04 -----
											if($sv['c_is_addoption']<>'Y') $sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품갯수를 가져온다 : 해당 코드가 없을 시 추가
											$arr_product["point"] += $app_point ;//포인트
											$arr_product["sum"] += $option_tmp_sum_price;//상품금액
											$arr_per_product[$k]['sum'] += $option_tmp_sum_price;//상품금액// ----- JJC : 상품별 배송비 : 2018-08-16 -----
											$sum_price += $option_tmp_sum_price;//상품금액
											$sum_point += $app_point;//상품당 포인트 합계 // 2016-12-13 ::: 포인트 적용 수정 - JJC



										} // end foreach => $v

										// 	KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
										$ex_coupon = explode("|", $pr['p_coupon']);
										$coupon_html = '';
										if($ex_coupon[0] && $ex_coupon[1]){
											$ex_coupon['name'] = stripslashes($ex_coupon[0]);
											$ex_coupon['price'] = rm_comma($ex_coupon[2]);
											$ex_coupon['per'] = floor(rm_comma($ex_coupon[3])*10)/10;
											$ex_coupon['max'] = rm_comma($ex_coupon[4]); //쿠폰 최댓값 콤마 제거
											$ex_coupon['perprice'] = floor($sv['c_price']*$sv['c_cnt']*$ex_coupon['per']/100); //퍼센트 계산

											//per일때 최대값 비교, per이 아닌경우 원 출력
											$ex_coupon_perprice = 0;
											if($ex_coupon[1] == 'per'){
												if($ex_coupon['max'] > 0 && $ex_coupon['max'] < $ex_coupon['perprice']){
													$ex_coupon_perprice = $ex_coupon['max'];
												}else{
													$ex_coupon_perprice= $ex_coupon['perprice'];
												}
											}else{
												$ex_coupon_perprice= $ex_coupon[2];
											}

											$ex_coupon_p = ($ex_coupon[1] == 'per' ? "<strong>" . $ex_coupon['per'] ."</strong>%" : "<strong>" . number_format($ex_coupon['price']) ."</strong>원"); //per일 경우 per price일경우 price
											$ex_coupon_max = ($ex_coupon[1] == 'per' && $ex_coupon['max'] > 0 ? "</strong> ( 최대 <strong>". number_format($ex_coupon['max']) . "</strong>원 할인 )." : null); //max

											$coupon_html .= '
												<div class="c_coupon" title="'. $ex_coupon['name'] .'">
													<!-- 주문결제페이지에서 div label 로 변경 -->
													<label class="coupon_box">
														<span class="coupon_tit">
															<input type="checkbox" onclick="app_order_price()" class="product_coupon_check" style="display:none" checked="checked"  name="product_coupon['. $pr['p_code'] .']"  value="'. $ex_coupon_perprice .'"/>
															<input type="hidden" name="pc_check['. $pr['p_code'] .']" value="'. md5(sha1($_SERVER['REMOTE_ADDR'].$ex_coupon_perprice)) .'">
															상품쿠폰
														</span>

														<span class="one_coupon">
															<span class="shape ic_top"></span>
															<span class="shape ic_bottom"></span>
															<!-- 쿠폰명 -->
															<span class="txt tt">'. stripslashes($ex_coupon['name']) .'</span>
															<span class="txt"><strong>'.$ex_coupon_p.'</strong>할인 ' . $ex_coupon_max . '</span>
														</span>
													</label>

												</div>
											';

											if( $ex_coupon_perprice > $option_tmp_sum_price) { $coupon_html = ""; }
										}

										/* 추가배송비개선 - 2017-05-19::SSJ  */
										// 배송설정별 추가배송비 적용을위한 클래스지정
										$class_delivery_addprice = "";
										$class_delivery_addprice_print = "";


										// 배송비 추출
										$app_delivery = "무료배송" ; $delivery_price = 0;
										switch($pr['p_shoppingPay_use']){
											case "Y":
												$delivery_price = $pr['p_shoppingPay'] * $sum_product_cnt;// 선택 구매 2015-12-04 LDD // |개별배송패치|
												$arr_product["delivery"]+= $pr['p_shoppingPay'] * $sum_product_cnt;

												// {{{LCY무료배송이벤트}}}
												$temp_delivery_sum  += $pr['p_shoppingPay'] * $sum_product_cnt;

												$app_delivery = $delivery_price > 0 ? "<strong>" . number_format($delivery_price) . "</strong>원":"무료배송";
												if($pr['p_shoppingPay'] > 0){
														$app_delivery .= "<br>(개별배송)";
												}

											   // 입점업체의 설정체크
												if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use_unit']=="Y"){
													// 배송설정별 추가배송비 적용을위한 클래스지정
													$class_delivery_addprice = "js_delevery_addprice js_delevery_addprice_unit";
													$class_delivery_addprice_print = "js_delevery_addprice_print js_delevery_addprice_unit_print";
												}
												break;
											case "F":
												$app_delivery = "무료배송";
												$delivery_price = 0;

												// --- JJC : 무료배송 시 추가배송비 1회 적용 : 2020-04-28 ---
												// 입점업체의 설정체크 / // 배송설정별 추가배송비 적용을위한 클래스지정
												if($del_chk_customer <> $crk) {
													//$del_chk_customer = $crk;
													if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use_free']=="Y"){
														// 배송설정별 추가배송비 적용을위한 클래스지정
														$class_delivery_addprice = "js_delevery_addprice";
														$class_delivery_addprice_print = "js_delevery_addprice_print";
													}
												}
												// --- JJC : 무료배송 시 추가배송비 1회 적용 : 2020-04-28 ---

												break;
											case "N":
												$app_delivery = "무료배송";
												$delivery_price = 0;
												if($del_chk_customer <> $crk) {
													$app_delivery = ($arr_customer[$crk]['app_delivery_price'] <> 0 ? "<strong>" . number_format($arr_customer[$crk]['app_delivery_price']) . "</strong>원" : "무료배송") ;
													$arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];

													// {{{LCY무료배송이벤트}}}
													$temp_delivery_sum  += $arr_customer[$crk]['app_delivery_price'];

													$del_chk_customer = $crk;
													$delivery_price = $arr_customer[$crk]['app_delivery_price'];// 선택 구매 2015-12-04 LDD

													// 일반배송상품중 무료배송조건충족시
													if($arr_customer[$crk]['app_delivery_price']==0){
														// 입점업체의 설정체크
														if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use_normal']=="Y"){
															// 배송설정별 추가배송비 적용을위한 클래스지정
															$class_delivery_addprice = "js_delevery_addprice";
															$class_delivery_addprice_print = "js_delevery_addprice_print";
														}

													// 일반배송상품
													}else{
														// 입점업체의 설정체크
														if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y"){
															// 배송설정별 추가배송비 적용을위한 클래스지정
															$class_delivery_addprice = "js_delevery_addprice";
															$class_delivery_addprice_print = "js_delevery_addprice_print";
														}
													}
												}
												break;
											// ----- JJC : 상품별 배송비 : 2018-08-16 -----
											case "P":
												$cart_delivery_price = ($pr['p_shoppingPayPfPrice'] == 0 || $pr['p_shoppingPayPfPrice'] >  $arr_per_product[$k]['sum'] ? $pr['p_shoppingPayPdPrice'] : 0 ); // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
												$arr_product["delivery"]+= $cart_delivery_price;
												$app_delivery = ($cart_delivery_price > 0 ? "<strong>" . number_format($cart_delivery_price) . "</strong>원" : "무료배송");
												if($cart_delivery_price > 0){
													$app_delivery .= "<div class=''>상품별배송".($pr['p_shoppingPayPfPrice'] > 0 ? "<br>(".number_format($pr['p_shoppingPayPfPrice'])."원 이상 무료배송)" : null)."</div>"; // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
												}

												// {{{LCY무료배송이벤트}}}
												$temp_delivery_sum  += $cart_delivery_price;
												$delivery_price = $cart_delivery_price;// 선택 구매 2015-12-04 LDD

												// 무료일 경우 --> 추가배송비 설정 사용함 + 상품별배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비 적용
												if($siteInfo['s_del_addprice_use']=="Y" && $cart_delivery_price == 0 && $siteInfo['s_del_addprice_use_product']=="Y"){
													// 배송설정별 추가배송비 적용을위한 클래스지정
													$class_delivery_addprice = "js_delevery_addprice";
													$class_delivery_addprice_print = "js_delevery_addprice_print";
												}
												// 무료가 아닌 경우 --> 추가배송비 설정이 사용함으로 되어 있으면 진행
												else if($siteInfo['s_del_addprice_use']=="Y" && $cart_delivery_price > 0 ){
													// 배송설정별 추가배송비 적용을위한 클래스지정
													$class_delivery_addprice = "js_delevery_addprice";
													$class_delivery_addprice_print = "js_delevery_addprice_print";
												}
												break;
											// ----- JJC : 상품별 배송비 : 2018-08-16 -----
										}
										/* 추가배송비개선 - 2017-05-19::SSJ  */

										// {{{LCY무료배송이벤트}}}
										if( $freeEventChk === true &&  $pr['p_free_delivery_event_use'] == 'Y' ){
												if( $arr_product["delivery"] >= $delivery_price) $arr_product["delivery"] -= $delivery_price;
												$app_delivery = "무료배송(이벤트)";
												$delivery_price = 0;
										}
										// {{{LCY무료배송이벤트}}}

								?>
										<tr>
											<td>
												<?php echo $_num; ?>
											</td>
											<td>
												<!-- 이미지 없을때 thumb_box 유지 -->
												<a href="<?php echo $pro_url; ?>" class="thumb_box"  target="_blank"><img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($pro_name); ?>"></a>
											</td>
											<td>
												<!-- 상품정보 -->
												<div class="order_item">
													<!-- 상품명 -->
													<div class="item_name"><a href="<?php echo $pro_url; ?>" class="title"  target="_blank"><?php echo $pro_name; ?></a></div>
													<!-- 옵션 ul반복 -->
													<div class="option">
														<?php echo $option_html; ?>
													</div>

													<!-- 쿠폰 / 없으면 div 숨김 -->
													<?php echo $coupon_html; ?>

												</div>
											</td>
											<!-- 상품금액 -->
											<td class="t_price">
												<?php echo number_format($sum_price); ?>원
												<?php if($groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) {  // {{{회원등급혜택}}}?>
												<div class="member_benefit"><span>회원할인 <strong><?php echo odt_number_format($groupSetInfo['mgs_sale_price_per'],1) ?>%</strong></span></div>
												<?php } // {{{회원등급혜택}}}?>
											</td>
											<!-- 배송비 / 배송비 없을때도 무조건 '무료배송' -->
											<td class="pointbg">
												<?php echo $app_delivery; ?>
												<div class='<?php echo $class_delivery_addprice_print; ?>' data-pcnt='<?php echo $sum_product_cnt ; ?>' style="display:none;margin-top:10px;"></div>
												<input type="hidden" name="product_delivery_price[<?php echo $pr['p_code']; ?>]" value="<?php echo $delivery_price; ?>" />
												<input type="hidden" name="op_add_delivery_price[<?php echo $pr['p_code']; ?>]" class="<?php echo $class_delivery_addprice; ?>" value="0" data-pcnt="<?php echo $sum_product_cnt ; ?>" />
											</td>
											<!-- 적립금 / 없으면 0원 -->
											<td>
												<?php echo number_format(floor($sum_point)) ?>원
												<?php if($groupSetUse === true && $groupSetInfo['mgs_give_point_per'] > 0) { // {{{회원등급혜택}}} ?>
												<div class="member_benefit"><span>회원추가적립 <strong><?php echo odt_number_format($groupSetInfo['mgs_give_point_per'],1) ?>%</strong></span></div>
												<?php } // {{{회원등급혜택}}} ?>
											</td>
										</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<?php
										// 전체 총계를 $arr_prouct_sum 배열에 담는다 $ak 는 키값으로 총계의 구분 키값이다.
										foreach($arr_product as $ak=>$av){ $arr_product_sum[$ak] += $av; }
									?>
									<td><span class="total_tit">합계</span></td>
									<td colspan="2"><span class="lineup"><span class="tit">총 </span><span class="total"><strong><?php echo number_format($arr_product["cnt"]); ?></strong>개</span></span></td>
									<td><span class="lineup"><span class="tit">총 </span><span class="total"><strong id="ID_total_price_smallsum"><?php echo number_format($arr_product['sum']); ?></strong>원</span></span></td>
									<td><span class="lineup"><span class="tit">총 </span><span class="total"><strong id="delivery_price_smallsum"><?php echo number_format($arr_product['delivery']); ?></strong>원</span></span></td>
									<td><span class="lineup"><span class="tit">총 </span><span class="total"><strong><?php echo number_format($arr_product['point']); ?></strong>원</span></span></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			<?php
				}
			?>




			<?php
				// 할인혜택은 회원만 적용
				if(is_login() == true){
			?>
				<!-- ◆할인 및 추가 혜택 적용 -->
				<div class="c_group_tit"><span class="tit">할인 및 추가 혜택 적용</span></div>
				<div class="c_add_sale">
					<table>
						<colgroup>
							<col width="150"><col width="*"><col width="220">
						</colgroup>
						<tbody>

							<?php // {{{회원쿠폰}}} ?>
							<?php if( $siteInfo['s_coupon_use'] == 'Y' ) {  ?>
							<!-- 쿠폰 -->
							<tr>
								<th>쿠폰</th>
								<td>
									<ul class='ajax_view_coupon_item'>
									<?php
										$priceSum  = $arr_product_sum['sum'];
										$priceDelivery = $arr_product_sum['delivery'];
										$priceAddDelivery = $arr_product_sum['add_delivery'];
										$priceTotal = $priceSum +  $priceDelivery + $priceAddDelivery;
										include_once OD_PROGRAM_ROOT."/shop.order.form.select_coupon_inc.php";
									?>
									</ul>
								</td>
								<td>
									<div class="c_btnbox">
										<ul>
											<li><a href="#none" class="c_btn h30 color js_select_coupon_apply">적용 하기</a></li>
											<li><a href="#none" class="c_btn h30 light line js_select_coupon_delete_all">전체 취소</a></li>
										</ul>
									</div>



									<script>

										// 쿠폰금액 초기화
										function couponPriceInit()
										{
											var couponDiscountTotalPrice = $('input[name="couponDiscountTotalPrice"]').val();
											var couponSaveTotalPrice = $('input[name="couponSaveTotalPrice"]').val();
											if( couponDiscountTotalPrice == undefined){ couponDiscountTotalPrice = 0; } // 예외처리
											if( couponSaveTotalPrice == undefined){ couponSaveTotalPrice = 0;  } // 예외처리

											$('input[name="use_coupon_price_member"]').val(couponDiscountTotalPrice); // 쿠폰 할인 총액 (배송비혜택 포함)
											$('input[name="use_coupon_save_price_member"]').val(couponSaveTotalPrice); // 쿠폰 적립 총액

											if( $('[name="selectCouponAllChk"]').val() == 'true'){
												$('.js_select_coupon_apply').hide();
											}else{
												$('.js_select_coupon_apply').show();
											}

											app_order_price();
										}

										// 쿠폰 아이템 초기화
										function couponItemInit()
										{
											try{
												var ajaxData = {}
													ajaxData.priceTotal = $('input[name="price_total"]').val()*1;
													ajaxData.priceSum = $('input[name="price_sum"]').val()*1; // 총 상품주문금액
													ajaxData.priceDelivery = $('input[name="price_delivery"]').val()*1; // 총 배송비 금액
													ajaxData.priceAddDelivery = $('input[name="add_delivery"]').val()*1; // 총 배송비 금액
												$.ajax({
													data: ajaxData,
													type: 'get', dataType: 'html', cache: false,
													url: '<?php echo OD_PROGRAM_URL; ?>/shop.order.form.select_coupon_inc.php',
													success: function(html) {
														$('.ajax_view_coupon_item').html(html);
														couponPriceInit();
													}
												});
											}catch(e){
												couponPriceInit(); // 항상 처리 이후 실행
											}
										}

										// 쿠폰선택에 따른 처리
										$(document).on('click','.js_select_coupon_apply',function(){
											var $this = $('.js_select_coupon_box');// this 의 경우 ajax 안으로 들어가면 처리되지 않으니 별도 변수 처리


											if( $('[name="selectCouponAllChk"]').val() == 'true'){
												alert('사용 가능한 쿠폰이 없습니다.'); return false;
											}

											var chk = $('input[name="arrAbailableInfoCnt"]').val()*1;
											if( chk < 1){ return false; }


											try{
												var ajaxData = {}
												ajaxData.ajaxMode = 'couponSelete'; // ajaxMode 추가
												ajaxData.couponUid = $this.val();
												ajaxData.priceTotal = $('input[name="price_total"]').val()*1;
												ajaxData.priceSum = $('input[name="price_sum"]').val()*1; // 총 상품주문금액
												ajaxData.priceDelivery = $('input[name="price_delivery"]').val()*1; // 총 배송비 금액
												ajaxData.priceAddDelivery = $('input[name="add_delivery"]').val()*1; // 총 배송비 금액
												if( ajaxData.couponUid == '') { alert("적용하실 쿠폰을 선택해 주세요."); return false; }
												$.ajax({
													data: ajaxData,
													type: 'POST', dataType: 'JSON', cache: false,
													url: '<?php echo OD_PROGRAM_URL; ?>/shop.order.form_ajax.php',
													success: function(data) {
														if( data.rst != 'success'){
															alert(data.msg);
														}

														couponItemInit(); // 항상 처리 이후 실행
													},
													error:function(request,status,error){
														couponItemInit(); // 항상 처리 이후 실행
														console.log(request.responseText);
													}
												});
											}catch(e){
												couponItemInit(); // 항상 처리 이후 실행
											}
										})

										// 쿠폰 적용취소에 따른 처리
										$(document).on('click','.js_select_coupon_delete',function(){

											var $this = $(this);// this 의 경우 ajax 안으로 들어가면 처리되지 않으니 별도 변수 처리
											var couponUid = $this.attr('data-uid');
											if( couponUid == '') { return false; }
											try{
												var ajaxData = {}
												ajaxData.ajaxMode = 'couponDelete'; // ajaxMode 추가
												ajaxData.couponUid = couponUid; // 쿠폰고유번호
												$.ajax({
													data: ajaxData,
													type: 'POST', dataType: 'JSON', cache: false,
													url: '<?php echo OD_PROGRAM_URL; ?>/shop.order.form_ajax.php',
													success: function(data) {
														if( data.rst != 'success'){
															alert(data.msg);
														}
														couponItemInit(); // 항상 처리 이후 실행
													},
													error:function(request,status,error){
														couponItemInit(); // 항상 처리 이후 실행
														console.log(request.responseText);
													}
												});
											}catch(e){
												couponItemInit(); // 항상 처리 이후 실행
											}
										});

										// 쿠폰 적용 전체 취소처리
										$(document).on('click','.js_select_coupon_delete_all',function(){
											var chk = $('input[name="arrAbailableInfoCnt"]').val()*1;
											if( chk < 1){  return false; }

											try{
												var ajaxData = {}
												ajaxData.ajaxMode = 'couponDeleteAll'; // ajaxMode 추가
												$.ajax({
													data: ajaxData,
													type: 'POST', dataType: 'JSON', cache: false,
													url: '<?php echo OD_PROGRAM_URL; ?>/shop.order.form_ajax.php',
													success: function(data) {
														if( data.rst != 'success'){
															alert(data.msg);
														}
														couponItemInit(); // 항상 처리 이후 실행
													},
													error:function(request,status,error){
														couponItemInit(); // 항상 처리 이후 실행
														console.log(request.responseText);
													}
												});
											}catch(e){
												couponItemInit(); // 항상 처리 이후 실행
											}
										});

									</script>
								</td>
							</tr>

							<?php } // {{{회원쿠폰}}} ?>

							<!-- 적립금 -->
							<tr>
								<th>적립금</th>
								<!-- 적립금 없을때, 프로모션 없을때 if_no_sale 클래스 추가 -->
								<td class="<?php echo ($able_point<1 || $siteInfo['s_pointusevalue'] ? 'if_no_sale' : null); ?>">
									<ul>
										<li>
											<span class="txt">현재 적립금 <em><?php echo number_format($mem_info['in_point']); ?>원</em></span>
											<span class="txt">사용 가능 적립금 <span class="num"><strong><?php echo number_format($able_point); ?></strong>원</span></span>
										</li>
										<li>
											<div class="input_box">
												<input type="text" name="_use_point" class="input_design number_style" style="width:110px" <?php echo ($able_point < $siteInfo['s_pointusevalue'] ? ' disabled="disabled"' : null); ?>>
												<a href="#none" onclick="return false;" class="c_btn h30 color <?php echo ($able_point < $siteInfo['s_pointusevalue'] ? ' error_point_low' : ' do_point_apply'); ?>">적립금 적용하기</a>
												<?php
													$point_info_msg = array();
													if( $able_point < $siteInfo['s_pointusevalue'] ) { // 적립금 사용 금액이 작을 시
														$point_info_msg[] = '<span class="num">'. number_format($siteInfo['s_pointusevalue']) .'원</span>이상 보유 시 사용가능';
													}else{
														if($siteInfo['s_pointuselimit']>0){ // 적립금을 사용할 시 최대 적립금
															$point_info_msg[] = '한번 주문 시 최대 <span class="num">'. number_format($siteInfo['s_pointuselimit']) .'원</span>까지 사용가능';
														}
													}
													// -- LCY160410 2016-04-10 -- 사용가능 적립금 안내 문구 추가
													//$point_info_msg[] = "다른 주문으로 사용된 적림금을 제외한 금액을 사용하실 수 있습니다.";
													// -- LCY160410 2016-04-10 -- 사용가능 적립금 안내 문구 추가
												?>
												<?php if(count($point_info_msg) > 0){ ?>
												<div class="tip_txt<?php echo (count($point_info_msg)<2?' if_beside':''); ?>">
													<?php echo implode('</div><div class="tip_txt'. (count($point_info_msg)<2?' if_beside':'') .'">', $point_info_msg); ?>
												</div>
												<?php } ?>
											</div>
										</li>
									</ul>
								</td>
								<td>
									<div class="c_btnbox">
										<ul>
											<li><a href="/?pn=mypage.point.list" class="c_btn h30 light ">적립금 내역</a></li>
											<li><a href="#none" onclick="return false;" class="c_btn h30 light line do_point_reset">적용 취소</a></li>
										</ul>
									</div>
								</td>
							</tr>

							<!-- 프로모션코드 -->
							<tr>
								<th>프로모션 코드</th>
								<td class="<?php echo ($_promotion_cnt<1 ? 'if_no_sale' : null); ?>">
									<ul>
										<li>
											<span class="txt">프로모션 코드를 입력하시고 혜택을 적용하실 수 있습니다.</span>
										</li>
										<li>
											<div class="input_box">
												<input type="text" name="promotion_code" class="input_design" style="width:270px">
												<input type="hidden" name="use_promotion_price" value="0"/>
												<a href="#none" onclick="return false;" class="c_btn h30 light do_promotion_valid">프로모션 코드 확인</a>
												<!-- 프로모션 코드 확인 버튼 클릭시 노출 -->
												<span class="promo_sale promotion_text"></span>
											</div>
										</li>
									</ul>
								</td>
								<td>
									<div class="c_btnbox">
										<ul>
											<li><a href="#none" onclick="return false;" class="c_btn h30 color do_promotion_apply">적용 하기</a></li>
											<li><a href="#none" onclick="return false;" class="c_btn h30 light line do_promotion_reset">적용 취소</a></li>
										</ul>
									</div>
								</td>
							</tr>
							<script>
								$(document).ready(function(){
									$('input[name=promotion_code]').on('keypress',function(e){ if( e.which == 13 ){ e.preventDefault(); alert('우측 프로모션 코드 확인 버튼을 눌러주세요.'); } });
									$('.do_promotion_apply').on('click',function(){
										if( $('input[name=promotion_code]').val() == '' ) { alert('프로모션코드를 입력하세요.'); }
										else {
											$.ajax({
												data: {'mode':'promotion_code','promotion_code':$('input[name=promotion_code]').val()},
												type: 'POST', dataType: 'JSON', cache: false,
												url: '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
												success: function(data) {
													if(data['code']=='OK') {
														var use_promotion_price = data['result']['type']=='P' ? Math.floor(<?=$arr_product_sum["sum"]?>*data['result']['amount']/100) : data['result']['amount'];
														$('.promotion_text').html('<strong>'+String(data['result']['amount']).comma()+(data['result']['type']=='P'?'%':'원')+'</strong> 할인');
														$('input[name=use_promotion_price]').val( use_promotion_price*1 );
														app_order_price();
													} else {
														alert(data['text']);
														$('.promotion_text').text('');
														$('input[name=use_promotion_price]').val(0);
														app_order_price();
													}
												},
												error:function(request,status,error){
													alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
												}
											});
										}
									});
									$('.do_promotion_reset').on('click',function(){
										$('input[name=use_promotion_price]').val(0); $('input[name=promotion_code]').val(''); app_order_price();
										$('.promotion_text').text('');
									});
									$('.do_promotion_valid').on('click',function(){
										if( $('input[name=promotion_code]').val() == '' ) { alert('프로모션코드를 입력하세요.'); }
										else {
											$.ajax({
												data: {'mode':'promotion_code','promotion_code':$('input[name=promotion_code]').val()},
												type: 'POST', dataType: 'JSON', cache: false,
												url: '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
												success: function(data) {
													// 조회 시 적용된 프로모션코드 적용취소
													$('input[name=use_promotion_price]').val(0); app_order_price();
													if(data['code']=='OK') {
														alert('사용가능한 프로모션코드 입니다.\n\n우측 적용하기 버튼을 눌러 할인 혜택을 적용하실 수 있습니다.');
														$('.promotion_text').html('<strong>'+String(data['result']['amount']).comma()+(data['result']['type']=='P'?'%':'원')+'</strong> 할인');
													} else {
														alert(data['text']); $('.promotion_text').text('');
													}
												},
												error:function(request,status,error){
													alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
												}
											});
										}
									});
								});
							</script>
						</tbody>
					</table>
				</div>
			<?php }else{ ?>
				<!-- 비회원일경우 포인트 미사용 -->
				<input type="hidden" name="_use_point" value="0">
				<input type="hidden" name="use_promotion_price" value="0">
			<?php } ?>

			<!-- ◆총 결제 금액 -->
			<div class="c_total_price">
				<div class="lineup">
					<div class="price">총 상품금액<span class="price_num"><strong><?php echo number_format($arr_product_sum['sum']); ?></strong>원</span></div>
					<!-- + 아이콘 -->
					<div class="ic_price ic_plus for_css"></div>
					<!-- 배송비 없을때 0원 -->
					<div class="price">총 배송비<span class="price_num"><strong class="ID_total_delivery_price"><?php echo number_format($arr_product_sum['delivery']+$arr_product_sum['add_delivery']); ?></strong>원</span></div>
					<!-- - 아이콘 -->
					<div class="ic_price ic_minus"></div>
					<!-- 할인금액 없을때 0원 -->
					<div class="price">총 할인금액<span class="price_num"><strong class="ID_sale_point">0</strong>원</span></div>
					<!-- = 아이콘 -->
					<div class="ic_price ic_equal for_css1"></div>
					<div class="price total_num">총 주문금액<span class="price_num"><strong class="ID_total_price"><?php echo number_format($arr_product_sum['sum'] + $arr_product_sum['delivery'] + $arr_product_sum['add_delivery']); ?></strong>원</span></div>
				</div>

			</div>

			<!-- ◆주문자 정보 -->
			<?php // 주문자 INPUT HIDDEN : 티켓몰 과 다름 ?>
			<input type="hidden" name="_opost1" class="input"  value="<?php echo $mem_info['in_zip1']; ?>" data-info='주문자 우편번호 1'/>
			<input type="hidden" name="_opost2" class="input"  value="<?php echo $mem_info['in_zip2']; ?>" data-info='주문자 우편번호 2'/>
			<input type="hidden" name="_oaddr1" class="input" value="<?php echo $mem_info['in_address1']; ?>" data-info='주문자 주소1'/>
			<input type="hidden" name="_oaddr2" class="input" value="<?php echo $mem_info['in_address2']; ?>" data-info='주문자 주소2' />
			<input type="hidden" name="_oaddr_doro" class="input"  value="<?php echo $mem_info['in_address_doro']; ?>" data-info='주문자 도로명주소' />
			<input type="hidden" name="_ozonecode" class="input" value="<?php echo $mem_info['in_zonecode']; ?>" data-info='주문자 도로명주소 코드' />
			<?php // 주문자 INPUT HIDDEN : 티켓몰과 다름?>
			<div class="c_group_tit"><span class="tit">주문자 정보</span><span class="sub_txt">체크된 항목은 필수 항목입니다. 꼭 입력해주시기 바랍니다.</span></div>
			<div class="c_form">
				<table>
					<colgroup>
						<col width="150"><col width="*"><col width="150"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th class="ess"><span class="tit ">주문자 이름</span></th>
							<td>
								<div class="input_box">
									<input type="text" name="_oname" class="input_design" value="<?php echo $mem_info['in_name']; ?>" placeholder="주문자 이름" style="width:120px" >
									<div class="tip_txt if_beside">실명을 입력해주세요.</div>
								</div>

							</td>
							<th class="ess"><span class="tit ">주문자 휴대폰</span></th>
							<td>
								<input type="text" name="_ohp" class="input_design" value="<?php echo $mem_info['in_tel2']; ?>" placeholder="휴대폰 번호" style="width:180px">
								<!-- <div class="tip_txt black">주문 등과 관련된 중요한 문자가 발송됩니다.</div> -->
							</td>
						</tr>
						<tr>
							<th class="ess"><span class="tit ">주문자 이메일</span></th>
							<td colspan="3">
								<input type="hidden" name="_oemail" class="js_join_email" value="<?php echo $mem_info['in_email']; ?>">
								<?php
									$_email_prefix = $_email_suffix = '';
									if($mem_info['in_email']) {
										$_email_arr = explode('@', $mem_info['in_email']);
										$_email_prefix = $_email_arr[0];
										$_email_suffix = $_email_arr[1];
									}
								?>
								<div class="input_box mail">
									<input type="text" name="_email_prefix" class="input_design js_email_prefix" value="<?php echo $_email_prefix; ?>" style="width:150px"/>
									<span class="mail_icon">＠</span>
									<select name="_email_suffix_select" class="js_email_suffix_select">
										<option value="">선택해주세요</option>
										<?php foreach($email_suffix as $ek=>$ev) { ?>
											<option value="<?php echo $ev; ?>"<?php echo ($_email_suffix == $ev?' selected':(!in_array($_email_suffix, $email_suffix) && $ev == 'direct'?' selected':null)); ?>><?php echo ($ev == 'direct'?'직접입력':str_replace('@', '', $ev)); ?></option>
										<?php } ?>
									</select>
									<!-- 직접입력 선택시 노출 / 그 전에는 숨김 -->
									<input type="text" name="_email_suffix_input" class="input_design js_email_suffix_input" value="<?php echo $_email_suffix; ?>" style="width:150px; display: none;">
								</div>

							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- ◆배송지 정보 -->
			<div class="c_group_tit"><span class="tit">배송지 정보</span><span class="sub_txt">체크된 항목은 필수 항목입니다. 꼭 입력해주시기 바랍니다.</span></div>
			<div class="c_form">
				<table>
					<colgroup>
						<col width="150"><col width="*"><col width="150"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th class="ess"><span class="tit ">배송지 선택</span></th>
							<td colspan="3">
								<label class="label_design"><input type="radio" name="_rtype" value="equal"><span class="txt">기본주소 (주문자 정보와 동일)</span></label>
								<label class="label_design"><input type="radio" name="_rtype" value="new"><span class="txt">새로운 주소</span></label>
								<?php if($old_use_val=='Y'){ ?>
								<!-- 이전주소 클릭시 이전주소 선택 tr 노출 -->
								<label class="label_design"><input type="radio" name="_rtype" value="old" id="_rtype_old"><span class="txt">이전주소</span></label>
								<?php } ?>
							</td>
						</tr>
						<?php if($old_use_val=='Y'){ ?>
						<!-- 이전주소 선택 / 이전주소 클릭시 노출 / 선택전에는 tr 숨김 -->
						<tr class="before_address_pop" style="display:none; ">
							<th class="ess"><span class="tit ">이전주소 선택</span></th>
							<td colspan="3">
								<div class="address_box">
									<?php
										foreach($arr_old_order as $srk=>$srv){
											$o_rpost_exp = explode('-',$srv['o_rpost']); // 우편번호
									?>
										<div class="address">
											<div class="txt_box">
												<a href="#none" class="upper_link before_address_apply"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""></a>
												<!-- 이름 -->
												<span class="txt name"><?php echo $srv['o_rname']; ?></span>
												<!-- 핸드폰번호 -->
												<span class="txt"><?php echo $srv['o_rhp']; ?></span>
												<!-- 주소 -->
												<!-- <span class="txt"><?php echo $srv['o_raddr1']; ?> <?php echo $srv['o_raddr2']; ?></span> -->
												<span class="txt"><?php echo $srv['o_raddr_doro']; ?> <?php echo $srv['o_raddr2']; ?></span>
											</div>
											<!-- 선택전 -->
											<a href="#none" class="c_btn h22 light line before_address_apply">선택하기</a>
											<!-- 선택후 -->
											<!-- <a href="" class="c_btn h22 light ">선택주소</a> -->
											<span class="before_address_data"
													data-rname="<?php echo $srv['o_rname']; ?>"
													data-rhtel="<?php echo $srv['o_rhp']; ?>"
													data-remail="<?php echo $srv['o_remail']; ?>"
													data-rzip1="<?php echo $o_rpost_exp[0]; ?>"
													data-rzip2="<?php echo $o_rpost_exp[1]; ?>"
													data-raddress="<?php echo $srv['o_raddr1']; ?>"
													data-raddress1="<?php echo $srv['o_raddr2']; ?>"
													data-raddress_doro="<?php echo $srv['o_raddr_doro']; ?>"
													data-rzonecode="<?php echo $srv['o_rzonecode']; ?>"
													style="display:none;"
													></span>
										</div>
									<?php } ?>
									</div>
								</div>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<th class="ess"><span class="tit ">받는분 이름</span></th>
							<td>
								<div class="input_box">
									<input type="text" name="_rname" class="input_design" placeholder="" style="width:120px">
									<div class="tip_txt if_beside">실명을 입력해주세요.</div>
								</div>
							</td>
							<th class="ess"><span class="tit ">받는분 휴대폰</span></th>
							<td>
								<input type="text" name="_rhp" class="input_design" placeholder="휴대폰 번호" style="width:180px">
								<!-- <div class="tip_txt black">주문 등과 관련된 중요한 문자가 발송됩니다.</div> -->
							</td>
						</tr>
						<?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>
						<tr>
							<th class="ess"><span class="tit ">받는분 주소</span></th>
							<td>
								<div class="input_box">
									<input type="text" name="_zonecode" id="_zonecode" class="input_design" value="61492" style="width:70px" readonly="readonly">
									<!-- <div class="tip_txt if_beside">새 우편번호</div> -->
									<a href="#none" onclick="new_post_view(); return false;" class="c_btn h30 light">주소검색</a>
								</div>

								<div class="input_full">
									<input type="text" name="_addr_doro" id="_addr_doro" class="input_design" placeholder="도로명 주소" readonly="readonly">
									<input type="text" name="_addr2" id="_addr2" class="input_design" placeholder="나머지 주소">
									<div id="add_delivery_string"><!-- 도서산간 추가배송비 메세지 출력 위치 --></div>
								</div>
							</td>
							<th class=""><span class="tit ">지번주소</span></th>
							<td>
								<div class="input_box" style="display:none;">
									<input type="text" name="_post1" id="_post1" class="input_design" value="501" style="width:50px" readonly="readonly">
									<span class="dash">-</span>
									<input type="text" name="_post2" id="_post2" class="input_design" value="833" style="width:50px" readonly="readonly">
								</div>
								<div class="input_full">
									<input type="text" name="_addr1" id="_addr1" class="input_design" placeholder="지번주소" readonly="readonly">
								</div>
								<div class="tip_txt ">주소검색을 통해 자동으로 입력됩니다.</div>
							</td>
						</tr>
						<?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>

						<tr>
							<th class=""><span class="tit ">배송메세지</span></th>
							<td colspan="3">
								<select name="_content_select" class="order_select">
									<option value="">배송 메세지를 선택해주세요.</option>
									<option value="배송전에 미리 연락바랍니다.">배송전에 미리 연락바랍니다.</option>
									<option value="부재시 경비실에 맡겨주세요.">부재시 경비실에 맡겨주세요.</option>
									<option value="부재시 전화주시거나 문자 남겨주세요.">부재시 전화주시거나 문자 남겨주세요.</option>
									<option value="4">직접 입력</option>
								</select>
								<!-- 직접입력 선택시 노출 / 안나올땐 div 숨김 -->
								<div class="textarea_box" style="display:none;"><textarea name="_content" rows="2" style="" class="textarea_design" placeholder="위 배송 메세지 중 선택하거나 직접 입력할 수 있습니다."></textarea></div>
								<div class="tip_txt ">배송 시 요청사항을 선택하거나 직접 입력해주세요.(200자 이내)</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- ◆결제정보 -->
			<div class="c_group_tit"><span class="tit">결제 정보</span><span class="sub_txt">체크된 항목은 필수 항목입니다. 꼭 입력해주시기 바랍니다.</span></div>

			<!-- 결제정보폼에서는 if_last_order 클래스 추가 -->
			<div class="c_form if_last_order">
				<dl>
					<dt>
						<table>
							<colgroup>
								<col width="150"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th class="ess"><span class="tit ">결제수단 선택</span></th>
									<td>
										<ul class="pay_select">
											<?php if($siteInfo['s_pg_paymethod_C'] == 'Y') {  // 결제 수단 설정에 따른 노출 - 카드결제 ?>
												<li class="use_card"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_card" value="card"><span class="lineup">신용카드</span></label></li>
											<?php } ?>
											<?php if($siteInfo['s_pg_paymethod_H'] == 'Y' && $siteInfo['s_pg_mobile_use'] == 'Y') {  // 결제 수단 설정에 따른 노출 - 휴대폰 결제 ?>
												<li class="use_phone"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_hpp" value="hpp"><span class="lineup">휴대폰 소액결제</span></label></li>
											<?php } ?>
											<?php if($siteInfo['s_pg_paymethod_L'] == 'Y') {  // 결제 수단 설정에 따른 노출 - 실시간계좌이체 ?>
												<li class="use_real"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_iche" value="iche"><span class="lineup">실시간계좌이체</span></label></li>
											<?php } ?>
											<?php if($siteInfo['s_pg_paymethod_V'] == 'Y') {  // 결제 수단 설정에 따른 노출 - 가상계좌 ?>
												<li class="use_vert"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_virtual" value="virtual"><span class="lineup">가상계좌</span></label></li>
											<?php } ?>
											<?php if($siteInfo['s_pg_paymethod_B'] == 'Y') {  // 결제 수단 설정에 따른 노출 - 무통장입금 ?>
												<li class="use_bank"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_online" value="online"><span class="lineup">무통장</span></label></li>
											<?php } ?>

                                            <?php 
                                                // LCY : 2021-07-04 : 신용카드 간편결제 추가
                                                if( $siteInfo['s_pg_paymethod_easypay'] != '' && count($arr_available_easypay_pg[$siteInfo['s_pg_type']]) > 0 ){ 
                                                    $select_pg_paymethod_easypay = explode(",",$siteInfo['s_pg_paymethod_easypay']);
                                                    foreach($select_pg_paymethod_easypay as $v){
                                            ?>
                                                    <li class="use_<?php echo str_replace("easypay_","",$v) ?>"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_<?php echo $v; ?>" value="<?php echo $v; ?>"><span class="lineup"><?php echo $arr_available_easypay_pg[$siteInfo['s_pg_type']][$v];  ?></span></label></li>
                                            <?php }} ?>

											<?php if($siteInfo['payco_use'] == 'Y') {  // 결제 수단 설정에 따른 노출 - 페이코 ?>
												<li class="use_payco"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_payco" value="payco"><span class="lineup">페이코</span></label></li>
											<?php } ?>

                                            <?php if($siteInfo['s_payple_use'] == 'Y') {  // JJC : 간편결제 - 페이플 : 2021-06-05 - 결제 수단 설정에 따른 노출 - 간편결제(페이플) ?>
                                                <li class="use_payple"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_payple" value="payple"><span class="lineup">페이플 간편결제</span></label></li>
                                            <?php } ?>

											<li class="use_point" style="display:none;"><label><input type="radio" name="_paymethod" class="pay_input" id="_paymethod_point" value="point"><span class="lineup">전액 적립금</span></label></li>
										</ul>
									</td>
								</tr>

								<!-- 무통장 선택시 노출 -->
								<tr class="ID_paymethod_online" style="display:none;">
									<th class="ess"><span class="tit ">입금은행 선택</span></th>
									<td>
										<?php echo _InputSelect('_bank',array_values($arr_bank),'','','','- 계좌 선택 -'); ?>
									</td>
								</tr>
								<tr class="ID_paymethod_online" style="display:none;">
									<th class="ess"><span class="tit ">입금자명</span></th>
									<td>
										<div class="input_box">
											<input type="text" name="_deposit" class="input_design" value="<?php echo $mem_info['in_name']; ?>" placeholder="" style="width:120px">
											<?php
												// 2017-06-16 ::: 부가세율설정 - 면세일 경우 현금영수증 발행하지 않음 ::: JJC
												if($is_vat_free == 0 )  {
											?>
												<label class="if_beside"><input type="checkbox" name="_get_tax" id="js_get_tax" value="Y">현금영수증 발행신청</label>
											<?php
												} // 2017-06-16 ::: 부가세율설정 - 면세일 경우 현금영수증 발행하지 않음 ::: JJC
											?>
										</div>
									</td>
								</tr>
								<!-- / 무통장 선택시 노출 -->

								<!-- 현금영수증 신청 폼 -->
								<tr class="js_get_tax_form" style="display:none;">
									<th class="ess"><span class="tit ">거래용도</span></th>
									<td>
										<div class="input_box">
											<label class="label_design"><input type="radio" name="_tax_TradeUsage" value="1" checked /><span class="txt">소득공제(휴대폰/카드번호)</span></label>
											<label class="label_design"><input type="radio" name="_tax_TradeUsage" value="2" /><span class="txt">지출증빙(사업자번호)</span></label>
										</div>
									</td>
								</tr>
								<tr class="js_get_tax_form" style="display:none;">
									<th class="ess"><span class="tit ">신분확인번호 구분</span></th>
									<td>
										<div class="input_box">
											<label class="label_design"><input type="radio" name="_tax_TradeMethod" value="1" id="js_tradeMethod1" /><span class="txt">카드번호(국세청에 등록된 카드번호만 가능)</span></label>
											<!-- <label class="label_design"><input type="radio" name="_tax_TradeMethod" value="3" id="js_tradeMethod3" />주민등록번호</label> -->
											<label class="label_design"><input type="radio" name="_tax_TradeMethod" value="5" id="js_tradeMethod5" checked /><span class="txt">휴대폰번호</span></label>
											<label class="label_design"><input type="radio" name="_tax_TradeMethod" value="4" id="js_tradeMethod4" disabled/><span class="txt">사업자번호</span></label>
										</div>
									</td>
								</tr>
								<tr class="js_get_tax_form" style="display:none;">
									<th class="ess"><span class="tit ">신분확인번호</span></th>
									<td>
										<input type="text" name="_tax_IdentityNum" class="input_design phone_style js_number_valid" value="<?php echo $mem_info['in_tel2']; ?>" placeholder="" style="width:120px">
										<input type="hidden" name="_identitynum_valid" value="" /><!-- 신분확인번호 유효성체크 -->

										<div class="tip_txt ">주민번호/휴대폰/카드번호/사업자번호 중 하나를 입력하세요.</div>
										<div class="tip_txt ">사업자번호를 입력한 경우 거래용도를 지출증빙용으로만 선택할 수 있습니다.</div>
										<div class="tip_txt black">번호 입력 오류로 인한 영수증 미발행은 책임지지 않습니다.</div>
									</td>
								</tr>
								<!-- /현금영수증 신청 폼 -->

								<tr class="ID_paymethod_payco">
									<th class="ess"><span class="tit ">페이코 안내사항</span></th>
									<td>
										<div class="tip_txt ">PAYCO는 온/오프라인 쇼핑은 물론 송금, 멤버십 적립까지 가능한 통합 서비스입니다.</div>
										<div class="tip_txt ">휴대폰과 카드 명의자가 동일해야 결제 가능하며, 결제금액 제한은 없습니다.</div>
										<div class="tip_txt black">- 지원카드 : 모든 국내 신용/체크카드</div>
									</td>
								</tr>

								<tr>
									<th class="ess"><span class="tit ">구매확인</span></th>
									<td>
										<label class="label_design">
											<input type="checkbox" name="order_confirm" value="Y">구매하실 상품의 상품명, 발행일등의 상품정보 및 가격을 확인하였으며, 이에 동의합니다.<br>
											<span class="sub_txt">전자상거래법8조 2항<br>사업자와 전자결제업자등은 전자적 대금지급이 이루어지는 경우 소비자가 입력한 정보가 소비자의 진정 의사 표시에 의한 것인지를 확인함에 있어 주의를 다하여야 한다.</span>
										</label>
									</td>
								</tr>
							</tbody>
						</table>
					</dt>
					<dd class="total_order">
						<ul>
							<li><span class="price_tt">총 상품 금액</span><span class="price"><strong><?php echo number_format($arr_product_sum['sum']); ?></strong>원</span></li>
							<li><span class="price_tt">총 배송비</span><span class="price"><span class="icon plus"></span><strong class="ID_total_delivery_price"><?php echo number_format($arr_product_sum['delivery']+$arr_product_sum['add_delivery']); ?></strong>원</span></li>
							<li><span class="price_tt">총 할인 금액</span><span class="price"><span class="icon minus"></span><strong class="ID_sale_point">0</strong>원</span></li>
						</ul>
						<div class="order_price"><span class="price_tt">총 결제 금액</span><span class="price"><strong class="ID_total_price"><?php echo number_format($arr_product_sum['sum'] + $arr_product_sum['delivery'] + $arr_product_sum['add_delivery']); ?></strong>원</span></div>
					</dd>
				</dl>
			</div>

			<?php
				// 비회원 주문 시 이용약관 , 개인정보 수집동의 항목
				if(!is_login()){
			?>
				<!-- ◆비회원 주문에 대한 개인정보 이용 동의 -->
				<div class="c_agree">
					<div class="agree_form">
						<div class="c_group_tit">
							<span class="tit">비회원 주문에 대한 이용약관 동의</span>

						</div>
						<div class="form">
							<div class="text_box">
								<textarea cols="" rows="12" class="textarea_design" readonly="readonly"><?php echo stripslashes($arr_policy['agree']['po_content']);?></textarea>
							</div>
							<div class="agree_check"><label><input type="checkbox" name="order_agree" id="order_agree" value="Y">위의 내용을 읽고 이에 동의합니다.</label></div>
						</div>
					</div>

					<div class="agree_form">
						<div class="c_group_tit">
							<span class="tit">비회원 주문에 대한 개인정보 수집 및 이용 동의</span>

						</div>
						<div class="form">
							<div class="text_box">
								<textarea cols="" rows="12" class="textarea_design" readonly="readonly"><?php echo stripslashes($arr_policy['guest_order']['po_content']);?></textarea>
							</div>
							<div class="agree_check"><label><input type="checkbox" name="order_privacy" id="order_privacy" value="Y">위의 내용을 읽고 이에 동의합니다.</label></div>
						</div>
					</div>
				</div>
			<?php } ?>




			<!-- ◆페이지 이용도움말 -->
			<div class="c_user_guide">
				<div class="guide_box">
					<dl>
						<dt>주문시 유의사항을 알려드립니다.</dt>
						<dd>메인상품중에 주문하실 상품과 수량을 확인하시고 상품 금액과 배송비를 확인해주세요.</dd>
						<dd>서브상품(옵션상품)이 있는 경우, 메인상품 주문자에 한해 주문 가능하며 서브상품만 따로 주문하실 수 없습니다.</dd>
						<dd>주문고객정보와 배송정보를 정확히 기입해주십시오. (회원정보를 수정해 놓으면 편리하게 이용하실 수 있습니다.)</dd>
						<dd>품절된 상품은 주문하실 수 없습니다.</dd>
					</dl>
				</div>
			</div>


			<div class="c_btnbox ">
				<ul>
					<li><a href="#none" onclick="if(confirm('작성중인 주문정보가 있습니다.\n이전페이지로 이동하시겠습니까?')){location.href=('/?pn=shop.cart.list');}return false;" class="c_btn h55 black line">이전 단계</a></li>
					<li><a href="#none" onclick="order_submit();return false;" class="c_btn h55 color ">결제하기</a></li>
				</ul>
			</div>

		</div>
	</div>


	<?php //  HIDDEN INPUT  ?>
	<?php // -- LCY 2016-04-10 -- 암호화 방식 적용
		 $encode_type_arr = array(time(),mt_rand(0,1000000),chr(mt_rand(65,90)).chr(mt_rand(65,90)).chr(mt_rand(65,90)));
		 $encode_type = md5($encode_type_arr[mt_rand(0,count($encode_type_arr)-1)]);
		 // 배송비 적용
		 $temp_delivery_price_sum = $arr_product_sum['delivery']+$arr_product_sum['add_delivery'];
	?>
	<input type="hidden" name="_ecode_type" value="<?=$encode_type?>"/>
	<input type="hidden" name="_ecode_type_delivery" value="<?=md5($encode_type.md5($temp_delivery_price_sum))?>"/>

	<input type="hidden" name="price_sum" value="<?php echo $arr_product_sum['sum']; ?>"/><!-- 구매총액 -->
	<input type="hidden" name="price_total" value="<?php echo ($arr_product_sum['sum'] + $arr_product_sum['delivery']+$arr_product_sum['add_delivery']); ?>"/><!-- 총결제액 -->
	<input type="hidden" name="price_delivery" value="<?php echo ($arr_product_sum['delivery']+$arr_product_sum['add_delivery']); ?>"/><!-- 배송비(추가배송비 포함) -->
	<input type="hidden" name="price_add_delivery" value="<?php echo $arr_product_sum['add_delivery']; ?>"/><!-- 추가배송비 -->
	<input type="hidden" name="app_point" value="<?php echo ceil($arr_product_sum['point']); ?>"/><!-- 제공해야할 포인트 -->
	<input type="hidden" name="able_point" value="<?php echo $able_point; ?>"/><!-- 사용가능포인트 -->

	<?php //{{{회원쿠폰}}} ?>
	<input type="hidden" name="use_coupon_price_member"/><!-- 사용한 사용자쿠폰할인금액-->
	<input type="hidden" name="use_coupon_save_price_member"/><!-- 사용한 사용자쿠폰적립금액-->
	<?php //{{{회원쿠폰}}} ?>

	<input type="hidden" name="use_coupon_price_product"/><!-- 사용한 상품쿠폰금액-->
	<input type="hidden" name="price_total_backup" value="<?php echo ($arr_product_sum['sum'] + $arr_product_sum['delivery']+$arr_product_sum['add_delivery']); ?>"/><!-- 총결제액 - 백업용(도서산간-배송비제외) -->
	<input type="hidden" name="price_delivery_backup" value="<?php echo ($arr_product_sum['delivery']+$arr_product_sum['add_delivery']); ?>"/><!-- 배송비(추가배송비 포함) - 백업용(도서산간-배송비제외) -->
	<?php// {{{LCY무료배송이벤트}}} -- 무료배송이벤트에 대한 처리 ?>
	<input type="hidden" name="temp_delivery_sum" value="<?=$temp_delivery_sum?>">


	<?php //  HIDDEN INPUT  ?>



</form>



<script src="/include/js/jquery/jquery.formatCurrency-1.4.0.min.js"></script>
<script language="javascript">

// - 결제를 위한 폼 전송 ---
function order_submit() {

	// 메세지 체크
	var _del_msg = $('select[name="_content_select"]');
	if(_del_msg.val() == '' || _del_msg.val() == undefined){
		/* 배송 메세지 선택 유무 사용 시 주석 해제 */
		// alert('배송메세지를 선택해 주세요');
		// _del_msg.focus();
		// return false;
	}

	// 실결제금액 1000원 이상 체크
	var app_price_total = $("input[name='price_total']").val()*1;
	if( app_price_total < 1000 && app_price_total != 0 ){ alert("실제 결제금액은 1,000원 이상이어야 합니다."); }
	else{ $("form[name=frm]").submit();	}
}
// - 결제를 위한 폼 전송 ---

// -- LCY 2016-04-10 -- 배송 메세지 적용
$(document).on('change', 'select[name="_content_select"]', function(){

	var _sel_delmsg = $(this).val();

	if(_sel_delmsg != '' && _sel_delmsg == '4'){
		$('textarea[name="_content"]').closest('.textarea_box').show();
		$('textarea[name="_content"]').prop('disabled',false).val('');

	}else{
		$('textarea[name="_content"]').closest('.textarea_box').hide();
		$('textarea[name="_content"]').prop('disabled',true).val('');
	}

});
// -- LCY 2016-04-10 -- 배송 메세지 적용

// -- LCY 2016-04-10 -- 이전주소 적용
$(document).ready(function(){
	$('.before_address_apply').on('click',function(){
		$data = $(this).parent().find('.before_address_data');
		//$('.before_address_pop').hide();
		$('input[name=_rname]').val($data.data('rname'));
		$('input[name=_rhp]').val($data.data('rhtel'));
		$('input[name=_post1]').val($data.data('rzip1'));
		$('input[name=_post2]').val($data.data('rzip2'));
		$('input[name=_addr1]').val($data.data('raddress'));
		$('input[name=_addr2]').val($data.data('raddress1'));
		$('input[name=_addr_doro]').val($data.data('raddress_doro'));
		$('input[name=_zonecode]').val($data.data('rzonecode'));
		add_delivery();
	});
});
// -- LCY 2016-04-10 -- 이전주소 적용

<?php if(is_login()) { ?>
// -- LCY 2016-04-10 -- 적립금 적용
$(document).on('click', '.do_point_apply', function(){
	if( $('input[name=_use_point]').val() == '' || $('input[name=_use_point]').val().replace(/,/g,'')*1 == 0 ) { alert('적립을을 입력하세요.'); }
	else { sale_submit(); }
});
$(document).on('click', '.error_point_low', function(){
	alert('적립금은 <?php echo number_format($siteInfo['s_pointusevalue']); ?>포인트 이상 보유 시 사용 가능합니다.');
	return false;
});
$(document).on('click', '.do_point_reset', function(){ $('input[name=_use_point]').val(0); $('input[name=_use_point]').removeAttr('readonly'); sale_submit(); });
// -- LCY 2016-04-10 -- 적립금 적용
<?php } ?>

// -- LCY 2016-04-10 -- 배송지정보 적용
$(document).ready(function(){
	app_order_price();
	$('input[name=_addr2]').on('focus',function(){ add_delivery(); });

	// - 배송지정보 radio 클릭 적용 ---
	$('input[name=_rtype]').on('click',function(e) {
		$('.before_address_pop').hide(); // 이전주소 닫기
		var _app_rtype = $('input[name=_rtype]').filter(function() {if (this.checked) return this;}).val();//체크값 확인
		switch(_app_rtype){
			// -- 주문정보와 동일 ---
			case 'equal':
				$('input[name=_rname]').val($('input[name=_oname]').val());//주문자명->수령인명
				$('input[name=_rhp]').val($('input[name=_ohp]').val());//주문자휴대폰->수령인휴대폰
				$('input[name=_post1]').val($('input[name=_opost1]').val());//주문자휴대폰->우편번호
				$('input[name=_post2]').val($('input[name=_opost2]').val());//주문자휴대폰->우편번호
				$('input[name=_addr1]').val($('input[name=_oaddr1]').val());//주문자휴대폰->주소1
				$('input[name=_addr2]').val($('input[name=_oaddr2]').val());//주문자휴대폰->주소2
				$('input[name=_addr_doro]').val($('input[name=_oaddr_doro]').val());//주문자휴대폰->주소2
				$('input[name=_zonecode]').val($('input[name=_ozonecode]').val());//주문자주소->국가기초구역번호
				break;
			// -- 새로운 주소 ---
			case 'new':
				$('input[name=_rname]').val('');
				$('input[name=_rhp]').val('');
				$('input[name=_post1]').val('');
				$('input[name=_post2]').val('');
				$('input[name=_addr1]').val('');
				$('input[name=_addr2]').val('');
				$('input[name=_addr_doro]').val('');
				$('input[name=_zonecode]').val('');
				break;
			// -- 과거배송지 ---
			case 'old':
				$('.before_address_pop').show();
				break;
		}
		add_delivery(); // 추가배송비 적용
	});
	$('input[name=_rtype][value=equal]').click().trigger('click'); // 기본선택 -- 기본주소(주문자 정보와 동일)
	// - 배송지정보 클릭시 적용 ---
});
// -- LCY 2016-04-10 -- 배송지정보 적용


// - 결제금액 확인 및 적용 ---
function app_order_price(){

	// 적용할 쿠폰금액 체크
	var _app_coupon_uid = _app_coupon_member_price = _app_coupon_product_price = coupon_product_price = 0;

	// 프로모션코드 체크 LMH005
	var _app_promotion_price = $('input[name=use_promotion_price]').val()*1;



	//{{{회원쿠폰}}}
	_app_coupon_member_price = $("input[name=use_coupon_price_member]").val()*1;//사용자 할인 쿠폰 합산금액
	_app_coupon_member_save_price = $("input[name=use_coupon_save_price_member]").val()*1;//사용자 적립 쿠폰 합산금액
	//{{{회원쿠폰}}}

	// 상품쿠폰
	product_coupon_cnt = $(".product_coupon_check").length;
	for(i=0;i<product_coupon_cnt;i++) {
		if( $(".product_coupon_check").eq(i).attr("checked") == "checked" ) {
			coupon_product_price += $(".product_coupon_check").eq(i).val()*1;
		}
	}
	_app_coupon_product_price = coupon_product_price; //상품쿠폰

	// 쿠폰 할인 총액
	_app_coupon_total_price = _app_coupon_product_price*1 + _app_coupon_member_price*1;
	// 쿠폰 할인 총액 (프로모션코드 추가) LMH005
	_app_coupon_total_price = _app_coupon_total_price*1 + _app_promotion_price;

	// 총 결제액 = 구매총액 + 배송비 - 사용포인트
	var _price_total = $("input[name=price_sum]").val()*1 + $("input[name=price_delivery]").val()*1 - $("input[name=_use_point]").val().replace(/,/g,'')*1 - _app_coupon_member_price*1 - _app_coupon_product_price*1;
	// 총 결제액 (프로모션코드 추가) LMH005
	_price_total = _price_total - _app_promotion_price;

	// 총 결제액이 0보다 작을경우....
	if(_price_total < 0) {
		alert("할인금액이 총 결제 금액을 초과하였습니다.");

		// 사용한 포인트가 있으면 포인트를 초기화.
		$("input[name=_use_point]").val(0);

		// 사용쿠폰이 있다면 클릭 해제
		$(".use_coupon_member").attr("checked",false);

		// 프로모션코드 초기화 LMH005
		$('input[name=promotion_code]').val(''); $('input[name=use_promotion_price]').val(0); $('.promotion_text').text('');

		// 함수 재실행.
		app_order_price();
		return;
	}
	// 총할인금액.
	// .ID_total_price_smallsum 미사용
	_total_sale_point = $("input[name=_use_point]").val().replace(/,/g,'')*1 + _app_coupon_total_price*1;
	$("#ID_use_point").html($("input[name=_use_point]").val().replace(/,/g,'')*1).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 포인트 사용금액 - 합계표
	$("#ID_use_coupon").html(_app_coupon_total_price*1).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 쿠폰 총 할인금액 - 합계표
	$(".ID_total_price , .ID_total_price2").html(_price_total*1).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 총결제액적용 - 합계표
	$(".ID_sale_point").html(_total_sale_point).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 총할인금액.
	$("input[name=price_total]").val(_price_total); // 총결제액적용 - input

	//{{{회원쿠폰}}} -- 쿠폰선택처리시 작동으로 업데이트
	// $("input[name=use_coupon_price_member]").val(_app_coupon_member_price); // 사용한 보너스쿠폰금액 - input
	//{{{회원쿠폰}}} -- 쿠폰선택처리시 작동으로 업데이트

	$("input[name=use_coupon_price_product]").val(_app_coupon_product_price); // 사용한 상품쿠폰금액 - input

	app_order_all_point();	// 전액 적립금 결제인지 체크하여 처리.

	// SSJ : 현금영수증 필수발행 패치 : 2021-02-01
	if(typeof forceCashbillCheck == 'function'){
		var force_cashbill_trigger = forceCashbillCheck();
		forceCashbillInput(force_cashbill_trigger);
	}

}
// - 결제금액 확인 및 적용 ---


// - 전액 적립금 결제... (굳이 적립금 결제가 아니더라도, 할인 쿠폰통해 총결제금액이 0이 된 주문도 함께 체크하여 처리한다)
function app_order_all_point(){

	// 총결제금액이 0원이고, 사용한 포인트가 존재 한다면, 전액 적립금결제로 보고 처리한다.
	// ----- JJC : 전액 적립금 - 비교 기준 변경 : 2020-05-29 : 총상품액 + 총배송비 == 총 할인액
	var app_total_price = <?php echo $arr_product_sum['sum']?> * 1 + $(".ID_total_delivery_price").html().replace(/,/g,'') * 1;// 총 상품금액 + 총 배송비
	var app_total_discount = $(".ID_sale_point").html().replace(/,/g,'')*1; // 총 할인액
	if(($("input[name=price_total]").val()*1 == 0) && app_total_price == app_total_discount  ) {//총상품액 + 총배송비 == 총 할인액
		$(".use_card,.use_real,.use_vert,.use_bank,.use_phone,.use_payco").hide();
		$(".use_point").show();
		$("#_paymethod_point").attr("checked","checked");
	} else {
		$(".use_card,.use_real,.use_vert,.use_bank,.use_phone,.use_payco").show();
		$(".use_point").hide();
		// -- 2016-12-05 무통장입금체크후 배송지선택버튼을 누르면 무통장입금 폼이 남은상태로 카드결제가 체크되는것 방지 SSJ ----
		if($("input[name=_paymethod]:checked").val() == "point" || $("input[name=_paymethod]:checked").val() == undefined){
			//$("#_paymethod_card").attr("checked","checked");
			var ele = $("input[name=_paymethod]")[0];
			$(ele).attr("checked","checked");
			change_paymethod();
		}
	}

}
// - 결제방식 radio 클릭 적용 ---
$(document).on('change', 'input[name=_paymethod]', change_paymethod);
function change_paymethod(){
	// SSJ : 현금영수증 필수발행 패치 : 2021-02-01
	var force_cashbill_trigger = false;
	if(typeof forceCashbillCheck == 'function'){
		force_cashbill_trigger = forceCashbillCheck();
	}
	if(force_cashbill_trigger == false){ $('input[name=_get_tax]').prop('checked',false); }
	var _app_paymethod = $("input[name=_paymethod]:checked").val();//체크값 확인
	if( _app_paymethod == "online" ) {
		$(".ID_paymethod_online").show();// 무통장입금테이블 보임
	}
	else {
		$(".ID_paymethod_online").hide();// 무통장입금테이블 숨김
		<?php if(in_array($siteInfo[s_pg_type],array('daupay'))) { ?>
			if(_app_paymethod == "virtual")
				$(".ID_paymethod_virtual").show();// 현금영수증 발행 테이블 보임
		<?php } ?>
	}

	if( _app_paymethod == 'payco'){
		$(".ID_paymethod_payco").show();// 페이코 안내사항 보임
	}else{
		$(".ID_paymethod_payco").hide();// 페이코 안내사항 숨김
	}

}

$(document).ready(function($) {
	add_delivery();


	// - 휴대폰 검증
	jQuery.validator.addMethod("htel_check", function(value, element) {
		var pattern = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/;
		return this.optional(element) || pattern.test(value);
	}, "휴대폰번호 형식이 유효하지않습니다.");

	// - 주문서 validate ---
	$("form[name=frm]").validate({
		ignore: "input[type=text]:hidden",
		rules:{
<?php
			if(!is_login()){ // 비회원 일 시
?>
			// --사전 체크 :: 이용약관, 개인정보 취급방침 동의 ---
			order_agree:{ required : true},
			order_privacy:{ required : true},
<?php
			}
?>
			// -- 구매확인 ---
			order_confirm:  { required: true},
			// --사전 체크 :: 가격정보 ---
			price_cnt: { required: true },
			// --사전 체크 :: 주문자정보 ---
			_oname: { required: true },
			_oemail: { required: true , email:true},
			_otel: { required: false },
			_ohp: { required: true , htel_check : true },
			// --사전 체크 :: 배송지정보 ---
			_rname: { required: true },
			_rtel: { required: false },
			_rhp : { required: true, htel_check : true },
			_post1: { required: false },
			_post2: { required: false },
			_addr1: { required: true },
			_addr2: { required: true },
			// --사전 체크 :: 결제입력정보 ---
			_paymethod: { required: true },
			_bank:{ required: function() { return ($("input[name=_paymethod]:checked").val() == "online" ? true : false); } },
			_deposit:{ required: function() { return ($("input[name=_paymethod]:checked").val() == "online" ? true : false); } },
			_tax_IdentityNum:{ required: function() { return ($("input[name=_get_tax]:checked").val() == "Y" ? true : false); } },
			_identitynum_valid:{ required: function() { return ($("input[name=_get_tax]:checked").val() == "Y" ? true : false); } }
		},
		messages: {
<?php
			if(!is_login()) { // 비회원 일 시
?>
			// --사전 체크 :: 이용약관, 개인정보 취급방침 동의 ---
			order_agree:  { required: "비회원 주문에 대한 이용약관에 동의하셔야 구매가 가능합니다."},
			order_privacy:  { required: "비회원 주문에 대한 개인정보 수집 및 이용에 동의하셔야 구매가 가능합니다."},
<?php
			}
?>
			// -- 구매확인 ---
			order_confirm:  { required: "구매하실 상품의 상품명, 발행일등의 상품정보 및 가격을 확인하고,\n\n구매확인에 동의해 주시기 바랍니다."},
			// --사전 체크 :: 가격정보 ---
			price_cnt: { required: "상품이 선택되지 않았습니다."},
			// --사전 체크 :: 주문자정보 ---
			_oname: { required: "주문자명을 입력해주시기 바랍니다."},
			_oemail: { required: "이메일을 입력해주시기 바랍니다." , email:"이메일이 바르지 않습니다."},
			_otel: { required: "전화번호을 입력해주시기 바랍니다." },
			_ohp: { required: "휴대폰번호을 입력해주시기 바랍니다." , htel_check: '휴대폰번호 형식이 유효하지않습니다' },
			// --사전 체크 :: 배송지정보 ---
			_rname: { required: "수령인명을 입력해주시기 바랍니다."},
			_rtel: { required: "전화번호를 입력해주시기 바랍니다." },
			_rhp: { required: "휴대폰번호를 입력해주시기 바랍니다." , htel_check : '휴대폰번호 형식이 유효하지않습니다'  },
			_post1: { required: "우편번호를 입력해주시기 바랍니다."},
			_post2: { required: "우편번호를 입력해주시기 바랍니다."},
			_addr1: { required: "주소를 입력해주시기 바랍니다."},
			_addr2: { required: "상세주소를 입력해주시기 바랍니다."},
			// --사전 체크 :: 결제입력정보 ---
			_paymethod: { required: "결제방식을 선택해주시기 바랍니다." },
			_bank:{ required: "무통장 계좌정보를 선택해주시기 바랍니다." },
			_deposit:{ required: "무통장 입금자명을 입력해주시기 바랍니다." },
			_tax_IdentityNum:{ required: "신분확인번호를 입력해주시기 바랍니다." },
			_identitynum_valid:{ required: "잘못된 신분확인번호 입니다." }
		}
	});
	// - 주문서 validate ---
});


/* 추가배송비개선 - 2017-05-19::SSJ  */
// - 추가 배송비 적용비 체크 ---
function add_delivery(){
    var app_addr = $("#_addr1").val(); // 지번주소
    if(app_addr == undefined) app_addr = "";
    var app_addr2 = $("#_addr2").val(); // 상세주소
    if(app_addr2 == undefined) app_addr2 = "";
    var app_addr_doro = $("#_addr_doro").val(); // 도로명주소
    if(app_addr_doro == undefined) app_addr_doro = "";

    // - 초기화 ---
    $("input[name=price_delivery]").val( $("input[name=price_delivery_backup]").val() );// 총배송비 초기화
    $("input[name=price_total]").val( $("input[name=price_total_backup]").val() );// 총결제액 초기화
    // 미사용
    $.ajax({
            url: "<?php echo OD_PROGRAM_URL; ?>/ajax.delivery.addprice.php",
            cache: false,
            type: "POST",
			async: false, // {{{2018-10-23:::ajax데이터동기화}}}
            data: "app_addr=" + app_addr + "&app_addr2=" + app_addr2 + "&app_addr_doro="+app_addr_doro,
            success: function(data){

                // 추가배송비 적용
                $(".js_delevery_addprice").val(data);
                // 개별배송 추가배송비 수정
                $(".js_delevery_addprice_unit").each(function(){
                    var _pcnt = $(this).data("pcnt");
                    $(this).val(data*_pcnt);
                });

                // 추가배송비 합계적용
                var app_add_delivery_price = 0; // 합계 추가배송비
                $(".js_delevery_addprice").each(function(){
                    app_add_delivery_price += $(this).val()*1;
                });

                if(app_add_delivery_price > 0 ) {
                    $(".js_delevery_addprice_print").show(); // 추가배송 안내 부분 보임
                    $("#add_delivery_string").html("<div class='tip_txt black'>도서 산간 지역에 대한 추가 배송비 " + app_add_delivery_price.toString().comma() + "원이 적용되었습니다.</div>" ); // 문구추가
                    $(".js_delevery_addprice_print").html("<strong>+" + data.toString().comma() + "</strong>원<br>(추가배송비)");
                    // 개별배송 추가배송비 수정
                    $(".js_delevery_addprice_unit_print").each(function(){
                        var _pcnt = $(this).data("pcnt");
                        $(this).html("<strong>+" + (data * _pcnt).toString().comma() + "원</strong><br>(추가배송비)");
                    });
                } else {
                    $(".js_delevery_addprice_print").hide(); // 추가배송 안내 부분 숨김
                    $("#add_delivery_string").html(""); // 문구초기화
                    $(".js_delevery_addprice_print").html(""); // 문구초기화
                }

                // 추가배송비 노출 적용
                $("input[name=price_delivery]").val( parseInt($("input[name=price_delivery]").val()) + parseInt(app_add_delivery_price) );// 총배송비 추가
                $("input[name=price_total]").val( parseInt($("input[name=price_total]").val()) + parseInt(app_add_delivery_price) );// 총결제액 추가
                $("input[name=price_add_delivery]").val( parseInt(app_add_delivery_price) );// 추가 배송비 추가
                app_order_price();
                $(".ID_total_delivery_price").html($("input[name=price_delivery]").val().toString().comma()); // 배송비 가격 적용
                var smallsum = $("input[name=price_sum]").val()*1 + $("input[name=price_delivery]").val()*1;
                $(".ID_total_price_smallsum").html(smallsum.toString().comma());
                $("#add_delivery_appprice_smallsum").html(app_add_delivery_price.toString().comma()); // 추가배송비 총합 노출

            }
    });

}
// - 추가 배송비 적용비 체크 ---
/* 추가배송비개선 - 2017-05-19::SSJ  */



// - 포인트 입력 시 사전 체크 및 적용 ---
function sale_submit() {

	if($("input[name=_use_point]").val().replace(/,/g,'')*1 > $("input[name=able_point]").val() * 1 ) {
		alert("보유 적립금 보다 큰 적립금을 입력하실 수 없습니다.");
		$("input[name=_use_point]").val(0);
	}


	<?php if($siteInfo[s_pointuselimit] > 0) { ?>
	if($("input[name=_use_point]").val().replace(/,/g,'')*1 > <?php echo $siteInfo['s_pointuselimit']; ?> * 1 ) {
		alert("적립금은 한번 주문 시 최대 <?php echo number_format($siteInfo['s_pointuselimit']); ?>포인트까지 사용가능합니다.");
		$("input[name=_use_point]").val(0);
	}
	<?php } ?>



	app_order_price();
};
// - 포인트 입력 시 사전 체크 및 적용 ---



// 이메일 항목제어
$(document).ready(join_email_form_view);
$(document).on('change', '.js_email_suffix_select', join_email_form_view);
function join_email_form_view() {
	var i_value = $('.js_email_prefix').val();
	var s_value = $('.js_email_suffix_select option:selected').val();
	var save_value = $('.js_join_email').val();
	var r_val = '';
	if(save_value != i_value.replace('@', '')+'@'+$('.js_email_suffix_input').val().replace('@', '')) $('.js_join_email_check').val('');
	if(s_value == 'direct') {
		$('.js_email_suffix_input').val('<?php echo $_email_suffix; ?>');
		$('.js_email_suffix_input').show();
	}
	else {
		$('.js_email_suffix_input').val(s_value);
		$('.js_email_suffix_input').hide();
		r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
		$('.js_join_email').val(r_val);
	}
}
$(document).on('keyup', '.js_email_prefix', function(e) {
	var i_value = $(this).val();
	var s_value = $('.js_email_suffix_input').val();
	var r_val = '';
	$('.js_join_email_check').val('');
	if(i_value.split('@').length > 1) {
		$(this).val($(this).val().replace('@', ''));
		$('.js_email_suffix_input').val('');
		$('.js_email_suffix_select').val('direct');
		$('.js_email_suffix_input').show();
		$('.js_email_suffix_input').focus();
	}
	r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
	$('.js_join_email').val(r_val);
});
$(document).on('keyup', '.js_email_suffix_input', function(e) {
	var su = $(this);
	var i_value = $('.js_email_prefix').val();
	var s_value = $(this).val().replace('@', '');
	var r_val = '';
	$('.js_join_email_check').val('');
	if(s_value) {
		$.each($('.js_email_suffix_select option'), function(k, v){
			if($(v).val() == s_value.replace('@', '')) {
				su.hide();
				$('.js_email_suffix_select').val($(v).val());
			}
		});
	}
	r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
	$('.js_join_email').val(r_val);
});




// - 현금영수증 발행신청시 신청항목 입력폼 노출 ----
$("#js_get_tax, input[name=_paymethod]").on("click",function(){
		// SSJ : 현금영수증 필수발행 패치 : 2021-02-01
		var force_cashbill_trigger = false;
		if(typeof forceCashbillCheck == 'function'){
			force_cashbill_trigger = forceCashbillCheck();
			forceCashbillInput(force_cashbill_trigger);
		}

		var _trigger = (($("#js_get_tax").prop("checked") || force_cashbill_trigger) && $("#_paymethod_online").prop("checked")); // 현금영수증 신청체크 && 무통장체크 모두 만족할때
		if(_trigger){
			$(".js_get_tax_form").show();// 현금영수증 신청폼 보임
		}else{
			$(".js_get_tax_form").hide();// 현금영수증 신청폼 숨김
		}
});
// - 현금영수증 지출증빙일때는 사업자번호만 선택가능 ---
$("input[name=_tax_TradeUsage]").on("change", function(){
	var _val = $(this).val();

	// 소득공제일때
	if(_val == "1"){
		$("input[name=_tax_TradeMethod]").prop("disabled", false);

		$("#js_tradeMethod5").prop("checked", true); // 기본선택 휴대폰번호
		$("#js_tradeMethod4").prop("disabled", true); // 사업자번호 선택불가
	}
	// 지출증빙일때
	else if(_val=="2"){
		$("input[name=_tax_TradeMethod]").prop("disabled", true);

		$("#js_tradeMethod4").prop("disabled", false); // 사업자번호 선택가능
		$("#js_tradeMethod4").prop("checked", true); // 기본선택 사압자번호
	}
	$(".js_number_valid").trigger("change");
});

$("input[name=_tax_TradeMethod]").on("change", function(){
	$("input[name=_tax_IdentityNum]").val("");
	$("input[name=_identitynum_valid]").val("");
});

// 신분확인번호 유효성체크----
$(document).delegate(".js_number_valid", "change", function(){
	var _type = $("input[name=_tax_TradeMethod]:checked").val() + '';
	var _val = $(this).val();
	//alert(_type);
	if(_type != undefined && _val.replace(' ','') != ""){
		var result = validate_number(_type,_val);
		if(result === false){
			var msg = "";
			if(_type == "1"){
				//카드 번호가 유효한지 검사
				msg = "잘못된 카드번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
			}
			else if(_type == "3"){
				//주민등록 번호가 유효한지 검사
				msg = "잘못된 주민등록번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
			}
			else if(_type == "4"){
				//사업자등록 번호가 유효한지 검사
				msg = "잘못된 사업자번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
			}
			else if(_type == "5"){
				//휴대폰 번호가 유효한지 검사
				msg = "잘못된 휴대폰번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
			}
			$("input[name=_identitynum_valid]").val("");
			//alert(msg);
		}else{
			$("input[name=_identitynum_valid]").val("1");
		}
	}else{
		$("input[name=_identitynum_valid]").val("");
	}
});
$(".js_number_valid").trigger("change");// 최초실행시 한번실행시킨다


function validate_number(_type, number) {

	//빈칸과 대시 제거
	number = number.replace(/[ -]/g,'');

	var match;
	if(_type == "1"){
		//카드 번호가 유효한지 검사
		match = /^(?:(94[0-9]{14})|(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/.exec(number);
	}
	else if(_type == "3"){
		//주민등록 번호가 유효한지 검사
		match = /^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))[1-4][0-9]{6}$/.exec(number);
	}
	else if(_type == "4"){
		//사업자등록 번호가 유효한지 검사
		match = checkBizID(number);
	}
	else if(_type == "5"){
		//휴대폰 번호가 유효한지 검사
		match = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/.exec(number);
	}

	if(match) {
		return true;
	} else {
		return false;
	}
}

function checkBizID(bizID)  //사업자등록번호 체크
{
	// bizID는 숫자만 10자리로 해서 문자열로 넘긴다.
	var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1);
	var tmpBizID, i, chkSum=0, c2, remander;
	 bizID = bizID.replace(/-/gi,'');

	 for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i);
	 c2 = "0" + (checkID[8] * bizID.charAt(8));
	 c2 = c2.substring(c2.length - 2, c2.length);
	 chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1));
	 remander = (10 - (chkSum % 10)) % 10 ;

	if (Math.floor(bizID.charAt(9)) == remander) return true ; // OK!
	  return false;
}

</script>

<?php
	// -- SSJ : 현금영수증 필수발행 패치 : 2021-02-01  ----
	if($siteInfo['s_force_cashbill_use'] == 'Y'){
?>
	<input type="hidden" class="js_force_cashbill_price" value="<?php echo ($siteInfo['s_force_cashbill_price']*1); ?>">
	<script>
		// 현금영수증 제한금액 체크
		function forceCashbillCheck(){

			// 결제수단 체크
			var _paymethod = $('input[name=_paymethod]:checked').val();
			if(_paymethod != 'online'){ return false; }

			// 결제금액 체크
			var price = $("input[name=price_total]").val()*1;
			var force_price = $('.js_force_cashbill_price').val()*1;
			// 제한금액 보다 많으면 현금영수증 강제 발행
			if(price >= force_price){
				return true;
			}else{
				return false;
			}
		}
		// 현금영수증 강제발행
		function forceCashbillInput(trigger){
			// 제한금액 보다 많으면 현금영수증 강제 발행
			if(trigger){
				$('#js_get_tax').attr('onclick','return false;');
				$('#js_get_tax').attr('checked', true);

				var _trigger = $("#_paymethod_online").prop("checked");
				if(_trigger){
					$(".js_get_tax_form").show();// 현금영수증 신청폼 보임
				}else{
					$(".js_get_tax_form").hide();// 현금영수증 신청폼 숨김
				}

			}else{
				$('#js_get_tax').attr('onclick','');
			}
		}
	</script>
<?php
	} // -- SSJ : 현금영수증 필수발행 패치 : 2021-02-01  ----
?>