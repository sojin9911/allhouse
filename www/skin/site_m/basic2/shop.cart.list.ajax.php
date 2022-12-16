<?php
	$arr_product_sum = $arr_push_product = array();  // 변수 초기화
	$arr_open_pcode = array_filter(explode('|', $open_pcode)); // 열리 옵션 정보 체크
?>

<?php if(count($arr_cart) > 0){ ?>
	<form name="frm" method="post">
	<input type="hidden" name="mode" value=""/>
	<input type="hidden" name="cuid" value=""/>
	<input type="hidden" name="code" value=""/>
	<input type="hidden" name="allcheck" value="Y"/>

		<!-- ◆장바구니 리스트 -->
		<div class="c_cart_list">

			<?php foreach($arr_cart as $crk=>$crv) {  ?>

				<div class="table_top">
					<div class="tit_box">
						<span class="txt">업체배송</span>
						<span class="txt shop_tit"><?php echo $arr_customer[$crk]['cName']; ?></span>
					</div>
					<div class="guide_txt"><?php echo ($arr_customer[$crk]['com_delprice_free'] > 0 ? '<strong>'. number_format($arr_customer[$crk]['com_delprice_free']) .'원</strong> 이상 구매시 배송비 무료 (개별배송 제외)' : ''); ?></div>
				</div>

				<div class="cart_table">
					<ul class="ul">
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
								$arr_options = array(); // 옵션저장배열
								foreach($v as $sk => $sv) {

									$option_tmp_name = !$sv['c_option1'] ? '옵션없음' : trim(($sv['c_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . $sv['c_option1'].' '.$sv['c_option2'].' '.$sv['c_option3']);
									$option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
									$option_tmp_cnt			= $sv['c_cnt'];
									$option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
									$app_point				= $sv['c_point'];

									// 상품 수량 select 값
									$buy_limit_array = array();
									$buy_max = 200; // 최고 구매갯수 설정
									$buy_limit = $sv['buy_limit'] ? min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$sv['buy_limit']) : min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$buy_max); // 구매제한이 없으면 재고만큼만 선택할수 있게 하되 max는 200
									for($i=1;$i<=$buy_limit;$i++) { $buy_limit_array[] = $i; }

									$arr_options[] = '
										<li>
											<div class="opt_tit">
												'. $option_tmp_name .'
											</div>
											<a href="#none" onclick="cart_delete('. $sv['c_uid'] .');return false;" class="btn_delete" title="옵션삭제"></a>
											<div class="c_counter_design">
												<a href="#none" onclick="cart_modify('. $sv['c_uid'] .', \'down\'); return false;" class="btn btn_minus"><span class="shape"></span></a>
												<input type="text" name="_ccnt['.$sv['c_uid'].']" id="cart_cnt_'. $sv['c_uid'] .'" value="'. $sv['c_cnt'] .'" class="counter_input" readonly="readonly">
												<a href="#none" onclick="cart_modify('. $sv['c_uid'] .', \'up\'); return false;" class="btn btn_plus"><span class="shape"></span></a>
											</div>
											<div class="price">'. number_format($option_tmp_price) .'원</div>
										</li>
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

									$ex_coupon_p=($ex_coupon[1]=='per' ? "<strong>" . $ex_coupon['per'] ."</strong>%" : "<strong>" . number_format($ex_coupon['price']) ."</strong>원");
									$ex_coupon_max=($ex_coupon[1]=='per' && $ex_coupon[4] >0 ?"</strong> ( 최대 <strong>". number_format($ex_coupon[4]) . "</strong>원 할인 )" : null);

									$coupon_html .= '
										<div class="c_coupon" title="'. addslashes($ex_coupon['name']) .'">
											<!-- 주문결제페이지에서 div label 로 변경 -->
											<div class="coupon_box">
												<span class="coupon_tit">
													<!-- 주문결제페이지에서 노출 -->
													<!-- <input type="checkbox" name="" /> -->
													상품<br>쿠폰
												</span>
												<span class="one_coupon">
													<span class="txt tt">'. stripslashes($ex_coupon['name']) .'</span>
													<span class="txt"><strong>'. $ex_coupon_p .'</strong>할인 ' . $ex_coupon_max . '</span>
												</span>
											</div>
											<span class="shape ic_top"></span><span class="shape ic_bottom"></span>
										</div>
									';
								}

								// 배송비 추출
								//<strong>5,000</strong>원<br>(개별배송)
								$app_delivery = '무료배송';
								$app_delivery_type = '';
								switch($pr['p_shoppingPay_use']){
									case 'Y':
										$cart_delivery_price = $pr['p_shoppingPay'] * $sum_product_cnt;// 선택 구매 2015-12-04 LDD // |개별배송패치|
										if($_is_checked) $arr_product['delivery']+= $pr['p_shoppingPay'] * $sum_product_cnt;
										$app_delivery = $cart_delivery_price > 0 ? "<strong>" . number_format(1 * $cart_delivery_price) . "</strong>원":"무료배송";
										if($pr['p_shoppingPay'] > 0){ /* 추가배송비개선 - 2017-05-19::SSJ  */
												$app_delivery_type = ' (개별배송)';
										}
										break;
									case 'F': $app_delivery = '무료배송'; $cart_delivery_price = 0; break;
									case 'N':
										$app_delivery = '무료배송';
										$cart_delivery_price = 0;
										if($del_chk_customer <> $crk) {
											$app_delivery = ($arr_customer[$crk]['app_delivery_price'] <> 0 ? '<strong>' . number_format($arr_customer[$crk]['app_delivery_price']) . '</strong>원' : '무료배송') ;
											if($_is_checked) $arr_product['delivery']+=$arr_customer[$crk]['app_delivery_price'];
											$del_chk_customer = $crk;
											$cart_delivery_price = $arr_customer[$crk]['app_delivery_price'];// 선택 구매 2015-12-04 LDD
										}
										break;
									// ----- JJC : 상품별 배송비 : 2018-08-16 -----
									case "P":
										$cart_delivery_price = ($pr['p_shoppingPayPfPrice'] == 0 || $pr['p_shoppingPayPfPrice'] >  $arr_per_product[$k]['sum'] ? $pr['p_shoppingPayPdPrice'] : 0 );  // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
										if($_is_checked) {
											$arr_product["delivery"]+= $cart_delivery_price;
										}
										$app_delivery = ($cart_delivery_price > 0 ? "<strong>" . number_format($cart_delivery_price) . "</strong>원" : "무료배송");

										break;
									// ----- JJC : 상품별 배송비 : 2018-08-16 -----
								}

						?>
								<!-- 상품하나당 li반복 -->
								<li class="li">

									<div class="cart_item_box">
										<ul>
											<li class="check">
												<label class="design"><input type="checkbox" name="_code[]" class="cls_code" value="<?php echo $pr['p_code']; ?>" <?php echo ($_is_checked?' checked="checked"':null); ?>></label>
												<input type="hidden" name="cart_price_<?php echo $pr['p_code']; ?>" value="<?php echo $sum_price; ?>">
												<input type="hidden" name="cart_delivery_<?php echo $pr['p_code']; ?>" value="<?php echo $cart_delivery_price; ?>">
												<input type="hidden" name="cart_point_<?php echo $pr['p_code']; ?>" value="<?php echo floor($sum_point); ?>">
											</li>
											<li class="thumb">
												<!-- 이미지 없을때 thumb_box 유지 -->
												<a href="<?php echo $pro_url; ?>" class="thumb_box"><img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($pro_name); ?>"></a>
											</li>
											<li class="item_name">
												<!-- 상품명 -->
												<a href="<?php echo $pro_url; ?>" class="title"><?php echo $pro_name; ?></a>

												<!-- 쿠폰 / 없으면 div 숨김 -->
												<?php echo $coupon_html; ?>
											</li>
										</ul>

									</div>

									<!-- 옵션노출 -->
									<div class="option js_pinfo<?php echo (count($arr_options)<2 ? ' if_only':null); ?><?php echo (in_array($pr['p_code'], $arr_open_pcode) ? ' if_open':null); ?>" data-pcode="<?php echo $pr['p_code']; ?>"><!-- 옵션이 1개일때는 if_only / 옵션이 2개 이상일때 열고닫기 버튼 클릭하면 if_open -->
										<!-- 옵션 ul반복 -->
										<ul>
											<?php echo implode('', $arr_options); ?>
										</ul>


										<!-- 총 결제 금액 -->
										<div class="c_total_price">
											<dl>
												<dt>상품 합계 <strong style="display:none;">(옵션 <?php echo number_format(count($arr_options)); ?>개)</strong></dt>
												<dd>
													<span class="price_num"><strong><?php echo number_format($sum_price); ?></strong>원</span>
													<?php if($groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) {  // {{{회원등급혜택}}}?>
													<div class="member_benefit"><span>회원할인 <strong><?php echo odt_number_format($groupSetInfo['mgs_sale_price_per'],1) ?>%</strong></span></div>
													<?php } // {{{회원등급혜택}}}?>
												</dd>
											</dl>
											<dl>
												<dt>적립금</dt>
												<dd>
													<span class="price_num"><strong><?php echo number_format(floor($sum_point)) ?></strong>원</span>
													<?php if($groupSetUse === true && $groupSetInfo['mgs_give_point_per'] > 0) { // {{{회원등급혜택}}} ?>
													<div class="member_benefit"><span>회원추가적립 <strong><?php echo odt_number_format($groupSetInfo['mgs_give_point_per'],1) ?>%</strong></span></div>
													<?php } // {{{회원등급혜택}}} ?>
												</dd>
											</dl>
											<dl>
												<dt>배송비<?php echo $app_delivery_type; ?></dt>
												<dd><span class="price_num"><?php echo $app_delivery; ?></span></dd>
											</dl>
										</div>

										<?php if(count($arr_options)>1){ ?>
											<!-- 옵션이 2개 이상일때 열고닫기 버튼/ 부모 div 클래스제어  / 열리면 "옵션정보 닫기"로 -->
											<div class="ctrl">
												<a href="#none" class="btn_ctrl js_pinfo_ctl" data-ocnt="<?php echo count($arr_options); ?>">
													<?php if(in_array($pr['p_code'], $arr_open_pcode)){ ?>
														<span class="tx">옵션정보 닫기</span>
													<?php }else{ ?>
														<span class="tx">선택한 옵션<strong>(<?php echo number_format(count($arr_options)); ?>개)</strong> 모두보기</span>
													<?php } ?>
												</a>
											</div>
										<?php } ?>

									</div>

								</li>

						<?php
							}
							// 전체 총계를 $arr_prouct_sum 배열에 담는다 $ak 는 키값으로 총계의 구분 키값이다.
							foreach($arr_product as $ak=>$av){ $arr_product_sum[$ak] += $av; }
						?>
					</ul>
				</div>
			<?php } ?>
		</div>


		<div class="c_cart_ctrl">
			<div class="c_btnbox">
				<ul>
					<li><a href="#none" onclick="cart_all_select(); return false;" class="c_btn h30 light line">전체 선택</a></li>
					<li><a href="#none" onclick="cart_select_delete(); return false;" class="c_btn h30 light line">선택 삭제</a></li>
					<li><a href="#none" onclick="cart_remove_all(); return false;" class="c_btn h30 light">장바구니 비우기</a></li>
				</ul>
			</div>
		</div>


		<!-- 총 결제 금액 -->
		<div class="c_total_price">
			<div class="point">
				<span class="lineup"><span class="icon">P</span><strong id="cart_point"><?php echo number_format($arr_product_sum['point']); ?></strong> 포인트 적립</span>
			</div>
			<dl>
				<dt>총 상품금액</dt>
				<dd><span class="price_num"><strong id="cart_price"><?php echo number_format($arr_product_sum['sum']); ?></strong>원</span></dd>
			</dl>
			<dl>
				<dt>총 배송비</dt>
				<dd><span class="price_num"><strong id="cart_delivery"><?php echo number_format($arr_product_sum['delivery']); ?></strong>원</span><div class="ic_price ic_plus"></div></dd>
			</dl>
			<dl class="total_num">
				<dt>총 주문금액</dt>
				<dd><span class="price_num"><strong id="cart_total"><?php echo number_format($arr_product_sum['sum'] + $arr_product_sum['delivery']); ?></strong>원</span><div class="ic_price ic_equal"></div></dd>
			</dl>
		</div>


		<div class="c_btnbox ">
			<ul>
				<li><a href="/" class="c_btn h55 black line">쇼핑 계속하기</a></li>
				<!-- 장바구니 상품 없을때 구매하기 버튼 숨김 -->
				<?php if(is_login() ){ ?>
					<!-- 로그인 후 -->
					<li><a href="#none" onclick="cart_submit();return false;" class="c_btn h55 color ">구매하기</a></li>
				 <?php }else { ?>
					<!-- 로그인 전 -->
						<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
						<?php if (  $none_member_buy === true ) { ?>
							<li><a href="#none" onclick="cart_confirm_submit();return false;" class="c_btn h55 black ">구매하기</a></li>
						<?php } else { ?>
							<li><a href="#none" onclick="cart_submit();return false;" class="c_btn h55 black ">구매하기</a></li>
						<?php } ?>
						<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
				 <?php } ?>
			</ul>
		</div>


<?php }else{ ?>
	<!-- 장바구니 없을때 -->
	<div class="none">
		<div class="gtxt">장바구니에 담긴 상품이 없습니다.</div>
	</div>


	<div class="c_btnbox">
		<ul>
			<li><a href="/" class="c_btn h55 black line">쇼핑 계속하기</a></li>
		</ul>
	</div>
<?php } ?>