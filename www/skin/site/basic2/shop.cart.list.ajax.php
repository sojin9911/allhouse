<?php
	$arr_product_sum = $arr_push_product = array();  // 변수 초기화
?>

<?php if(count($arr_cart) > 0){ ?>
	<form name="frm" method="post">
	<input type="hidden" name="mode" value=""/>
	<input type="hidden" name="cuid" value=""/>
	<input type="hidden" name="code" value=""/>
	<input type="hidden" name="allcheck" value="Y"/>
    <input type="hidden" name="buy_type" value="cart"/>

		<?php foreach($arr_cart as $crk=>$crv) {  ?>

			<!-- ◆장바구니 리스트 -->
			<div class="c_cart_list">

				<div class="table_top">
					<div class="tit_box">
						<span class="txt hide">업체배송</span>
						<span class="txt shop_tit hide"><?php echo $arr_customer[$crk]['cName']; ?></span>
					</div>
					<div class="guide_txt hide"><?php echo ($arr_customer[$crk]['com_delprice_free'] > 0 ? '<strong>'. number_format($arr_customer[$crk]['com_delprice_free']) .'원</strong> 이상 구매시 배송비 무료 (개별배송 제외)' : ''); ?></div>
				</div>

				<div class="cart_table">
					<table>
						<colgroup>
							<col width="30"><col width="250"><col width="70"><col width="*"><col width="100"><col width="120">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">
									<!-- 전체선택 / 처음엔 checked 되어있는 상태 -->
									<label class="design" title="전체선택"><input type="checkbox" name="allcheck" class="js_allcheck" value="Y" <?php echo ($app_cart_init?' checked="checked"':null); ?>></label>
								</th>
								<th scope="col">상품</th><!--이미지-->
								<th scope="col">색상</th><!--상품 및 옵션 정보-->
								<th scope="col">옵션정보</th><!--상품금액-->
								<th scope="col">비고</th><!--배송비-->
								<th scope="col">합계금액</th><!--적립금-->
							</tr>
						</thead>
						<tbody>
							<?php
								// -- 변수 초기화
								unset($del_chk_customer);
								$arr_product = array(); // 업체별 상품 합계
								$arr_per_product = array(); // 상품별 합계 // ----- JJC : 상품별 배송비 : 2018-08-16 -----

								foreach($crv as $k=>$v) { // 업체별 상품 반복 구간



									/* 상품 정보 */
									$pr = $arr_product_info[$k]; // 업체 상품의 정보를 담는다.
									$pro_name	= strip_tags($pr['p_name']);	// 상품명
									$thumb_img	= get_img_src('thumbs_s_'.$pr['p_img_list_square']); // 상품 이미지
									if($thumb_img=='') $thumb_img = $SkinData['skin_url']. '/images/skin/thumb.gif';
									$pro_url = "/?pn=product.view&pcode=".$pr['p_code']; // 상품의 주소
									/* 상품 정보 끝 */

									// {{{회원등급혜택}}}
									unset($groupSetUse);
									if( $pr['p_groupset_use'] == 'Y' && is_login() == true ){
										if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
											$groupSetUse = true;
										}
									}
									// {{{회원등급혜택}}}

									// 체크박스 체크 여부 체크 - 총합계는 체크된 상품만 합산
									$_is_checked = false;
									if($app_cart_init){
										$_is_checked = true;
									}
									else if(is_array($_code) && in_array($pr['p_code'],$_code)){
										$_is_checked = true;
									}

									// -- 변수 초기화
									unset($option_html , $sum_price , $sum_product_cnt , $sum_point); // 2016-12-13 ::: 포인트 적용 수정 - JJC --> sum_point 추가
									foreach($v as $sk => $sv) {

//										$option_tmp_name = !$sv['c_option1'] ? '옵션없음' : trim(($sv['c_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . $sv['c_option1'].' '.$sv['c_option2'].' '.$sv['c_option3']);
										$option_tmp_name = !$sv['c_option1'] ? '옵션없음' : trim($sv['c_option2'].' '.$sv['c_option3']);
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
												<li class="counter">
													<div class="opt_tit'. (!$sv['c_option1'] ? ' opt_none' : '' ) .'">'. $option_tmp_name .'</div>
													<div class="c_counter_design">
														<a href="#none" onclick="cart_modify('. $sv['c_uid'] .', \'down\'); return false;" class="btn btn_minus" ><span class="shape"></span></a>
														<input type="text" name="_ccnt['.$sv['c_uid'].']" id="cart_cnt_'. $sv['c_uid'] .'" value="'. $sv['c_cnt'] .'" class="counter_input" readonly="readonly"/>
														<a href="#none" onclick="cart_modify('. $sv['c_uid'] .', \'up\'); return false;" class="btn btn_plus" ><span class="shape"></span></a>
													</div>
												</li>
												<li class="price"><strong>'. number_format($option_tmp_price) .'</strong>원</li>

                                                <!--<li class="delete">
													<a href="#none" onclick="cart_delete('. $sv['c_uid'] .');return false;" class="btn_delete" title="옵션삭제"></a>
												</li>-->

                                            </ul>
										';

										//상품수 , 포인트 , 상품금액
										if($_is_checked) $arr_product["cnt"] += $option_tmp_cnt;//상품수
										// ----- SSJ : 추가옵션은 배송비 미적용 : 2020-02-04 -----
										if($sv['c_is_addoption']<>'Y') $sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품갯수를 가져온다 : 해당 코드가 없을 시 추가
										if($_is_checked) $arr_product["point"] += $app_point ;//포인트
										if($_is_checked) {
											$arr_product["sum"] += $option_tmp_sum_price;//상품금액
											$arr_per_product[$k]['sum'] += $option_tmp_sum_price;//상품금액// ----- JJC : 상품별 배송비 : 2018-08-16 -----
										}
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
										$ex_coupon['max'] = rm_comma($ex_coupon[4]);

										$ex_coupon_p = ($ex_coupon[1] == 'per' ? "<strong>" . $ex_coupon['per'] ."</strong>%" : "<strong>" . number_format($ex_coupon['price']) ."</strong>원");
										$ex_coupon_max = ($ex_coupon[1] == 'per' && $ex_coupon[4] >0 ? "</strong> ( 최대 <strong>". number_format($ex_coupon['max']) . "</strong>원 할인 )" : null);

										$coupon_html .= '
											<div class="c_coupon" title="'. addslashes($ex_coupon['name']) .'">
												<!-- 주문결제페이지에서 div label 로 변경 -->
												<div class="coupon_box">
													<span class="coupon_tit">
														상품쿠폰
													</span>
													<span class="one_coupon">
														<span class="shape ic_top"></span>
														<span class="shape ic_bottom"></span>
														<!-- 쿠폰명 -->
														<span class="txt tt">'. stripslashes($ex_coupon['name']) .'</span>
														<span class="txt "><strong>'. $ex_coupon_p.'</strong>할인 ' . $ex_coupon_max . '</span>
													</span>
												</div>
											</div>
										';
									}

									// 배송비 추출
									//<strong>5,000</strong>원<br>(개별배송)
									$app_delivery = "무료배송";
									switch($pr['p_shoppingPay_use']){
										case "Y":
											$cart_delivery_price = $pr['p_shoppingPay'] * $sum_product_cnt;// 선택 구매 2015-12-04 LDD // |개별배송패치|
											if($_is_checked) $arr_product["delivery"]+= $pr['p_shoppingPay'] * $sum_product_cnt;
											$app_delivery = $cart_delivery_price > 0 ? "<strong>" . number_format(1 * $cart_delivery_price) . "</strong>원":"무료배송";
											if($pr['p_shoppingPay'] > 0){ /* 추가배송비개선 - 2017-05-19::SSJ  */
													$app_delivery .= "<br>(개별배송)";
													//$app_delivery .= "<div class='guide_txt'>개별배송<br>(개당 ".number_format($pr['p_shoppingPay'] )."원)</div>";
											}
											break;
										case "F": $app_delivery = "무료배송"; $cart_delivery_price = 0; break;
										case "N":
											$app_delivery = "무료배송";
											$cart_delivery_price = 0;
											if($del_chk_customer <> $crk) {
												$app_delivery = ($arr_customer[$crk]['app_delivery_price'] <> 0 ? "<strong>" . number_format($arr_customer[$crk]['app_delivery_price']) . "<strong>원" : "무료배송") ;
												if($_is_checked) $arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];
												$del_chk_customer = $crk;
												$cart_delivery_price = $arr_customer[$crk]['app_delivery_price'];// 선택 구매 2015-12-04 LDD
											}
											break;
										// ----- JJC : 상품별 배송비 : 2018-08-16 -----
										case "P":
											$cart_delivery_price = ($pr['p_shoppingPayPfPrice'] == 0 || $pr['p_shoppingPayPfPrice'] >  $arr_per_product[$k]['sum'] ? $pr['p_shoppingPayPdPrice'] : 0 ); // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
											if($_is_checked) {
												$arr_product["delivery"]+= $cart_delivery_price;
											}
											$app_delivery = ($cart_delivery_price > 0 ? "<strong>" . number_format($cart_delivery_price) . "</strong>원" : "무료배송");
											if($cart_delivery_price > 0){
													$app_delivery .= "<div class=''>상품별배송". ($pr['p_shoppingPayPfPrice'] > 0 ? "<br>(".number_format($pr['p_shoppingPayPfPrice'] )."원 이상 무료배송)" : null) ."</div>"; // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
											}
											break;
										// ----- JJC : 상품별 배송비 : 2018-08-16 -----
									}

							?>
									<tr>
										<td>
											<label class="design"><input type="checkbox" name="_code[]" class="cls_code" value="<?php echo $pr['p_code']; ?>" <?php echo ($_is_checked?' checked="checked"':null); ?>></label>
											<input type="hidden" name="cart_price_<?php echo $pr['p_code']; ?>" value="<?php echo $sum_price; ?>">
											<input type="hidden" name="cart_delivery_<?php echo $pr['p_code']; ?>" value="<?php echo $cart_delivery_price; ?>">
										</td>
										<td class="cart_td_flex">
											<!-- 이미지 없을때 thumb_box 유지 -->
											<a href="<?php echo $pro_url; ?>" class="thumb_box"><img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($pro_name); ?>"></a>
											<div class="order_item">
												<!-- 상품명 -->
												<div class="item_name"><a href="<?php echo $pro_url; ?>" class="title"><?php echo $pro_name; ?></a></div>

												<!-- 쿠폰 / 없으면 div 숨김 -->
												<?php echo $coupon_html; ?>
											</div>
										</td>
										<td>
											<!-- 상품정보 -->
											<p><?php echo $c_option_color?></p>
										</td>
										<!-- 상품금액 -->
										<td><!--옵션-->
                                            <div class="option option___">
                                                <?php echo $option_html; ?>
                                            </div>
										</td>
										<!-- 배송비 / 배송비 없을때도 무조건 '무료배송' -->
										<!--수량변경버튼으로 변경했습니다-->
										<td class="pointbg"><? //php echo $app_delivery; ?><button class="pointbg_btn">수량변경</button></td>
										<!-- 적립금 / 없으면 0원 -->
										<td class="t_price">
											<?php echo number_format($sum_price); ?>원
											<?php if($groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) {  // {{{회원등급혜택}}}?>
											<div class="member_benefit"><span>회원할인 <strong><?php echo odt_number_format($groupSetInfo['mgs_sale_price_per'],1) ?>%</strong></span></div>
											<?php } // {{{회원등급혜택}}}?>
											<div class="hide">
												<?php echo number_format(floor($sum_point)); ?>원
												<?php if($groupSetUse === true && $groupSetInfo['mgs_give_point_per'] > 0) { // {{{회원등급혜택}}} ?>
												<div class="member_benefit"><span>회원추가적립 <strong><?php echo odt_number_format($groupSetInfo['mgs_give_point_per'],1) ?>%</strong></span></div>
												<?php } // {{{회원등급혜택}}} ?>
											</div>
										</td>
									</tr>
							<?php
								}
								// 전체 총계를 $arr_prouct_sum 배열에 담는다 $ak 는 키값으로 총계의 구분 키값이다.
								foreach($arr_product as $ak=>$av){ $arr_product_sum[$ak] += $av; }
							?>
						</tbody>
					</table>
				</div>

			</div>

		<?php } ?>




		<div class="c_cart_ctrl">
			<div class="left_box">
				<a href="#none" onclick="cart_select_delete(); return false;" class="c_btn h30 light line">선택상품 삭제</a>
				<a href="#none" onclick="cart_remove_all(); return false;" class="c_btn h30 light">장바구니 비우기</a>
			</div>
			<div class="select_num hide">
				선택 상품 <span class="num">( <strong class="js_cart_selected">1</strong> / <?php echo count($arr_product_info); ?> )</span>
			</div>
		</div>

		<!-- 총 결제 금액 -->
		<div class="c_total_price">
			<div class="lineup">
				<div class="price"><p class="price_label">총 개의 상품금액</p><span class="price_num"><strong id="cart_price"><?php echo number_format($arr_product_sum['sum']); ?></strong>원</span></div>
				<!-- + 아이콘 -->
				<div class="ic_price ic_plus"></div>
				<!-- 배송비 없을때 0원 -->
				<div class="price"><p class="price_label">배송비</p><span class="price_num"><strong id="cart_delivery"><?php echo number_format($arr_product_sum['delivery']); ?></strong>원</span></div>
				<!-- = 아이콘 -->
				<div class="ic_price ic_equal"></div>
				<div class="price"><p class="price_label">합계</p><span class="price_num"><strong id="cart_total"><?php echo number_format($arr_product_sum['sum'] + $arr_product_sum['delivery']); ?></strong>원</span></div>
			</div>
		</div>

		<div class="c_btnbox ">
			<ul>
				<li><a href="/" class="c_btn h55 black line hide">쇼핑 계속하기</a></li>
				<!-- 장바구니 상품 없을때 구매하기 버튼 숨김 -->
				<?php if(is_login() ){ ?>
					<!-- 로그인 후 -->
					<li><a href="#none" onclick="cart_submit();return false;" class="c_btn h55  color">전체 상품 주문</a></li>
				 <?php }else { ?>
					<!-- 로그인 전 -->
						<?php // === 비회원 구매 설정 kms 2019-06-24 ==== ?>
						<?php if (  $none_member_buy === true ) { ?>
							<li><a href="#none" onclick="cart_confirm_submit();return false;" class="c_btn h55 color ">전체 상품 주문</a></li>
						<?php } else { ?>
							<li><a href="#none" onclick="cart_submit();return false;" class="c_btn h55 color "><?php echo ($siteInfo['s_none_member_login_skip'] == 'Y' ? '비회원 ' : null); ?>전체 상품 주문</a></li>
						<?php } ?>
						<?php // === 비회원 구매 설정 kms 2019-06-24 ==== ?>
				 <?php } ?>
			</ul>
		</div>

	</form>



<?php }else{ ?>
	<!-- 장바구니 없을때 / 리스트, 총결제금액 div 숨김 -->
	<div class="none">
		<div class="gtxt">장바구니에 담겨있는 상품이 없습니다.</div>
	</div>


	<div class="c_btnbox ">
		<ul>
			<li><a href="#none" class="c_btn h55  color">전체 상품 주문</a></li>
			<!-- 장바구니 상품 없을때 구매하기 버튼 숨김 -->
		</ul>
	</div>

	<!-- <div class="c_btnbox ">
		<ul>
			<li><a href="/" class="c_btn h55 black line">쇼핑 계속하기</a></li>
			/*장바구니 상품 없을때 구매하기 버튼 숨김*/
		</ul>
	</div> -->
<?php } ?>