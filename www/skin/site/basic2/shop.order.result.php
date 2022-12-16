<div class="c_section c_shop">
	<div class="layout_fix">
		<!-- ◆공통타이틀 -->
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
		<!-- /공통타이틀 -->


		<!-- ◆장바구니 리스트 -->
		<div class="c_group_tit"><span class="tit">주문 상품</span></div>
		<div class="c_cart_list complete_list">

			<div class="cart_table">
				<table>
					<colgroup>
						<col width="50"><col width="115"><col width="*"><col width="120"><col width="100"><col width="80">
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
						unset($op_price_delivery,$op_price_sum_total, $_num);
						foreach( $sres as $k=>$v ){
							// No. 설정
							$_num++;

							$res = _MQ_assoc("
								select *
								from smart_order_product as op
								inner join smart_product as p on ( p.p_code=op.op_pcode )
								where op_pcode = '".$v['op_pcode']."' AND op_oordernum='{$ordernum}'
								order by op_uid asc
							");
							//ViewArr($res);
							unset($op_option_print,$option_name,$option_cnt,$op_total_price, $add_delivery_print,$op_total_point,$op_status_class,$op_delivery_price, $op_add_delivery_price);
							foreach($res as $sk=>$sv) {

								/*------- 상품명 (결제시 상품명으로 사용됨) ------*/
								if(!$app_product_name)  {
									$app_product_name_tmp = $sv['op_pname'];
									$app_product_name = $sv['op_pname'];
								} else {
									$app_product_cnt++;
									$app_product_name = $app_product_name_tmp ." 외 ".$app_product_cnt."건";
								}
								/*------- // 상품명 (결제시 상품명으로 사용됨) ------*/

								# 상품 가격 및, 배송비 정보
								$op_total_price += $sv['op_price'] * $sv['op_cnt'];
								# 상품의 갯수
								$p_total_cnt += $sv['op_cnt'];
								# 적립금
								$op_total_point += $sv['op_point'];

								// 2017-10-13 ::: 배송비 오류 수정 ::: JJC
								$op_delivery_price += $sv['op_delivery_price'];
								$op_add_delivery_price += $sv['op_add_delivery_price'];

								# 옵션처리
								$option_name = !$sv['op_option1'] ? '옵션없음' : trim(($sv['op_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . $sv['op_option1'].' '.$sv['op_option2'].' '.$sv['op_option3']);
								$option_cnt			= $sv['op_cnt'];
								$op_option_print .= '
									<ul>
										<li>
											<div class="opt_tit'. (!$sv['op_option1'] ? ' opt_none' : '' ) .'">'. $option_name .'</div>
										</li>
										<li class="price"><strong>'. number_format($sv['op_price']) .'</strong>원</li>
										<li class="num"><strong>'. number_format($sv['op_cnt']) .'</strong>개</li>
									</ul>
								';

							}
							// 옵션처리끝

							// {{{LCY무료배송이벤트}}}
							if( $v['op_free_delivery_event_use'] == 'Y' ){
								$op_delivery_price = 0;
							}
							// {{{LCY무료배송이벤트}}}

							# 배송처리
							$delivery_price = $op_delivery_price;; // |개별배송패치| - $sum_product_cnt : 상품갯수를 곱해준다. -- 계산이 되어서 들어감
							$deliver_price_print = '무료배송';
							if($delivery_price > 0 ){
								$deliver_price_print = ($delivery_price > 0 ? '<strong>'.number_format($delivery_price).'</strong>원':'' );
							}
							if($v['op_delivery_type'] == '무료'){
								$deliver_price_print = '무료배송';
							}else if($v['op_delivery_type'] == '개별'){
								$deliver_price_print .= '<br>(개별배송)';
							}
							// ----- JJC : 상품별 배송비 : 2018-08-16 -----
							else if($v['op_delivery_type'] == '상품별'){
								$deliver_price_print .= '<br>(상품별)';
							}

							// {{{LCY무료배송이벤트}}}
							if( $v['op_free_delivery_event_use'] == 'Y' ){
								$deliver_price_print = "무료배송(이벤트)";
							}
							// {{{LCY무료배송이벤트}}}


							/* 추가배송비개선 - 2017-05-19::SSJ  */
							$add_delivery_price = $op_add_delivery_price;;
							$deliver_price_print .= ($add_delivery_price > 0 ? '<div style="margin-top: 10px;"><strong>+ '.number_format($add_delivery_price).'</strong>원<br>(추가배송비)</div>':'' );
							$delivery_price += $add_delivery_price;


							# 상품 쿠폰 처리
							$product_coupon_normal_use = ''; // 변수 초기화 : 필수
							$product_coupon_normal_check = _MQ(" select * from smart_order_coupon_log where cl_oordernum = '".$v['o_ordernum']."' and cl_pcode = '".$v['p_code']."' and cl_type = 'product' ");
							if( count($product_coupon_normal_check) > 0){
								$product_coupon_normal_use = "Y"; // 상품 쿠폰 있는지 처리
								$product_coupon_normal_title = $product_coupon_normal_check['cl_title'];
								$product_coupon_normal_price = number_format($product_coupon_normal_check['cl_price']);
								$product_coupon_normal_per = number_format($product_coupon_normal_check['cl_price']/$op_total_price*100);
							}

							# 상품의 url
							$p_name	= strip_tags($v['p_name']);	// 상품명
							$p_url = "/?pn=product.view&pcode=".$v['p_code']; // 상품의 주소
							# 상품의 썸네일
							$p_thumb	= get_img_src('thumbs_s_'.$v['p_img_list_square']); // 상품 이미지
							if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/skin/thumb.gif';
							# 총 배송비 합계
							$op_price_delivery += $delivery_price;
							# 총 합계
							$op_price_sum_total +=  $op_total_price;
					?>
							<tr>
								<td><?php echo $_num; ?></td>
								<td>
									<!-- 이미지 없을때 thumb_box 유지 -->
									<a href="<?php echo $p_url; ?>" class="thumb_box" target="_blank"><img src="<?php echo $p_thumb; ?>" alt="<?php echo addslashes($p_name); ?>"></a>
								</td>
								<td>
									<!-- 상품정보 -->
									<div class="order_item">
										<!-- 상품명 -->
										<div class="item_name"><a href="<?php echo $p_url; ?>" class="title"  target="_blank"><?php echo $p_name; ?></a></div>
										<!-- 옵션 ul반복 -->
										<div class="option">
											<?php echo $op_option_print; ?>
										</div>

										<!-- 쿠폰 / 없으면 div 숨김 -->
										<?php if($product_coupon_normal_use == 'Y') { // 상품 쿠폰을 사용한다면?>
											<div class="c_coupon" title="쿠폰명" style="">
												<!-- 주문결제페이지에서 div label 로 변경 -->
												<div class="coupon_box">
													<span class="coupon_tit">상품쿠폰</span>
													<span class="one_coupon">
														<span class="shape ic_top"></span>
														<span class="shape ic_bottom"></span>
														<span class="txt tt"><?php echo $product_coupon_normal_title; ?></span>
														<span class="txt"><strong><?php echo $product_coupon_normal_price; ?>원</strong>할인</span>
													</span>
												</div>
											</div>
										<?php } ?>

									</div>
								</td>
								<!-- 상품금액 -->
								<td class="t_price"><?php echo number_format($op_total_price); ?>원</td>
								<!-- 배송비 / 배송비 없을때도 무조건 '무료배송' -->
								<td class="pointbg">
									<?php echo $deliver_price_print;?>
								</td>
								<!-- 적립금 / 없으면 0원 -->
								<td><?php echo number_format($op_total_point); ?>원</td>
							</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>

		</div>







		<!-- ◆주문자 정보 -->
		<div class="c_group_tit"><span class="tit">주문자 정보</span></div>
		<div class="c_form">
			<table>
				<colgroup>
					<col width="150"><col width="*"><col width="150"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">주문자 이름</span></th>
						<td>
							<?php echo $row['o_oname']; ?>
						</td>
						<th class="ess"><span class="tit ">주문자 휴대폰</span></th>
						<td>
							<?php echo $row['o_ohp']; ?>
						</td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">주문자 이메일</span></th>
						<td colspan="3">
							<?php echo $row['o_oemail']; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>




		<!-- ◆배송지 정보 -->
		<div class="c_group_tit"><span class="tit">배송지 정보</span></div>
		<div class="c_form">
			<table>
				<colgroup>
					<col width="150"><col width="*"><col width="150"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">받는분 이름</span></th>
						<td>
							<?php echo $row['o_rname']; ?>
						</td>
						<th class="ess"><span class="tit ">받는분 휴대폰</span></th>
						<td>
							<?php echo $row['o_rhp']; ?>
						</td>
					</tr>
					<?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>
					<tr>
						<th class="ess"><span class="tit ">받는분 주소</span></th>
						<td>
							<span>
								<?php echo $row['o_rzonecode']; ?><br>
								<span><?php echo $row['o_raddr_doro']; ?></span>
							</span>
							<span><?php echo $row['o_raddr2']; ?></span>
						</td>
						<th class="ess"><span class="tit ">지번주소</span></th>
						<td>
							<?php echo (rm_str($row['o_rpost'])>0?'('.$row['o_rpost'].') ' : ''); ?>
							<?php echo $row['o_raddr1']; ?>
							<span><?php echo $row['o_raddr2']; ?></span>
						</td>
					</tr>
					<?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>

					<tr>
						<th class=""><span class="tit ">배송메세지</span></th>
						<td colspan="3">
							<?php echo ($row['o_content']?nl2br($row['o_content']):''); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>




		<!-- ◆결제정보 -->
		<div class="c_group_tit"><span class="tit">결제 정보</span></div>

		<div class="c_form ">
			<table>
				<colgroup>
					<col width="150"><col width="*"><col width="150"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">최종 결제금액</span></th>
						<td>
							<strong><?php echo number_format($row['o_price_real']); ?></strong>원
						</td>
						<th class="ess"><span class="tit ">결제수단 선택</span></th>
						<td>
							<?php echo $arr_payment_type[$row['o_easypay_paymethod_type'] != '' ? $row['o_easypay_paymethod_type'] : $row['o_paymethod']]; // LCY : 2021-07-04 : 신용카드 간편결제 추가 -- 결제수단 표기 -- ?>
						</td>
					</tr>
					<?php if($row['o_paymethod'] == 'online') { ?>
					<tr>
						<th class="ess"><span class="tit ">입금은행</span></th>
						<td>
							<?php echo $row['o_bank']; ?>
						</td>
						<th class="ess"><span class="tit ">입금자명</span></th>
						<td>
							<?php echo $row['o_deposit']; ?> <?php echo ($row['o_get_tax']=='Y'?'(현금영수증 발행을 신청하였습니다)':''); ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>


		<!-- ◆총 결제 금액 -->
		<div class="c_total_price complete_total">
			<div class="lineup">
				<div class="price">총 상품금액<span class="price_num"><strong><?php echo number_format($op_price_sum_total); ?></strong>원</span></div>
				<!-- + 아이콘 -->
				<div class="ic_price ic_plus"></div>
				<!-- 배송비 없을때 0원 -->
				<div class="price">총 배송비<span class="price_num"><strong><?php echo number_format($op_price_delivery); ?></strong>원</span></div>
				<!-- - 아이콘 -->
				<div class="ic_price ic_minus"></div>
				<!-- 할인금액 없을때 0원 -->
				<div class="price">총 할인금액<span class="price_num"><strong><?php echo number_format($row['o_price_total'] + $row['o_price_delivery'] - $row['o_price_real']); ?></strong>원</span></div>
				<!-- = 아이콘 -->
				<div class="ic_price ic_equal"></div>
				<div class="price total_num">총 주문금액<span class="price_num"><strong><?php echo number_format($row['o_price_real']); ?></strong>원</span></div>
			</div>
		</div>


		<?php
			// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC
			//$ordernum = $ordernum; // --> 주문번호
			$order_row = $row; // --> 주문배열정보

			include(OD_PROGRAM_ROOT."/shop.order.result.vat_calc.php");
			// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC

			//{{{페이코}}}
			if($row['o_paymethod'] == 'payco'){
				require_once(OD_PROGRAM_ROOT."/shop.order.result.payco.php");
				$submit_onclick = 'payco_open();';
			}

            // JJC : 간편결제 - 페이플 : 2021-06-05
            else if($row['o_paymethod'] == 'payple'){
                require_once(OD_PROGRAM_ROOT."/shop.order.result.payple.php");
                $submit_id = 'payAction';
            }
            // JJC : 간편결제 - 페이플 : 2021-06-05
			
			else{

				// PG사 결제 인증요청 페이지
				switch($siteInfo[s_pg_type]) {
					case "inicis" :
						require_once(OD_PROGRAM_ROOT."/shop.order.result_inicis_std.php");  // PC는 STD, MOBILE 은 m, 공통처리는 아무것도 안붙었음, 가상계좌 입금확인처리..
						$submit_onclick = "ini_submit();";
						break;
					case "lgpay" :
						//require_once(OD_PROGRAM_ROOT."/shop.order.result_lgpay_new.php");  // lg u+ 은 PC,MOBILE 모두 다르게 쓴다.
						//$submit_onclick = "launchCrossPlatform();";
						// SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
						require_once(OD_PROGRAM_ROOT."/shop.order.result_toss.php");
						$submit_onclick = "requestPayment();";
						break;
					case "kcp" :
						require_once(OD_PROGRAM_ROOT."/shop.order.result_kcp.php"); // KCP 는 결제창, 결제처리 만 따로쓴다.
						$submit_onclick = "onload_pay(document.order_info);";
						break;
					case "billgate" :
						require_once(OD_PROGRAM_ROOT."/shop.order.result_billgate.php"); // 빌게이트는 결제창만 따로 쓴다.
						$submit_onclick = "checkSubmit();";
						break;
					case "daupay" :
						require_once(OD_PROGRAM_ROOT."/shop.order.result_daupay.php");
						$submit_onclick = "fnSubmit();";
						break;
					case "":

					break;
				}
			}
			//{{{페이코}}}
		?>
        <div class="c_btnbox ">
            <ul>
                <li><a href="#none" onclick="if(confirm('작성중인 주문정보가 있습니다.\n이전페이지로 이동하시겠습니까?')){location.href=('/?pn=shop.order.form');}return false;" class="c_btn h55 black line">이전 단계</a></li>

                <?php if($row['o_paymethod'] == 'payple'){// JJC : 간편결제 - 페이플 : 2021-06-05?>
                    <li><a href="#none" onclick="return false;" class="c_btn h55 color" ID="<?php echo $submit_id; ?>">결제하기</a></li>
                <?php } else { ?>
                    <li><a href="#none" onclick="<?php echo $submit_onclick; ?> return false;" class="c_btn h55 color">결제하기</a></li>
                <?php } ?>

            </ul>
        </div>

	</div>
</div>