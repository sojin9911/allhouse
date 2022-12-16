<?php
$page_title = "주문완료";
include_once($SkinData['skin_root'].'/shop.header.php'); // 상단 헤더 출력
?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_order">



	<!-- ◆ 주문완료 -->
	<div class="c_complete">
		<div class="complete_box">
			<span class="order_number">주문번호 : <strong><?php echo $row['o_ordernum']; ?></strong></span>
			<div class="tit">
				<?php if($row['o_paystatus'] == 'Y') { ?>
					<strong><?php echo $row['o_oname']; ?></strong>님의 주문 및 결제가 안전하게 완료되었습니다.
				<?php } else { ?>
					<strong><?php echo $row['o_oname']; ?></strong>님의 주문이 안전하게 완료되었습니다.<br />지정된 계좌로 입금해주시면 결제확인 후 배송이 진행됩니다.
				<?php } ?>
			</div>
			<div class="sub_txt">
				본 화면에서는 새로고침(F5) 또는 뒤로가기 버튼을 클릭하지 않는 것이 좋습니다. 위와 같은 동작으로 인하여 중복 결제가 발생할 수 있습니다.<br>
				회원의 경우 마이페이지에서 주문 진행 상황을 확인할 수 있습니다.
			</div>
		</div>
	</div>
	<!-- / 주문완료 -->



	<!-- 주문결제단계에서 묶음 -->
	<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
		<div class="c_group_tit order"><span class="tit">주문 상품 (<?php echo number_format(count($sres)); ?>)</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"></a><!-- 내용을 열고접을 수 있음 --></div>

		<!-- ◆장바구니 리스트 -->
		<div class="c_cart_list">
			<!-- <div class="table_top">
				<div class="tit_box">
					<span class="txt">업체배송</span>
					<span class="txt shop_tit">(주)쇼핑몰명</span>
				</div>
			</div> -->

			<div class="cart_table">
				<ul class="ul">
					<!-- 상품하나당 li반복 -->
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
									<li>
										<div class="tit_box">
											<div class="opt_tit">'. $option_name .' <strong>('. number_format($sv['op_cnt']) .'개)</strong></div>
											<div class="price">'. number_format($sv['op_price']) .'원</div>
										</div>
									</li>
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
							<li class="li">

								<div class="cart_item_box">
									<ul>
										<li class="thumb">
											<!-- 이미지 없을때 thumb_box 유지 -->
											<a href="<?php echo $p_url; ?>" class="thumb_box"><img src="<?php echo $p_thumb; ?>" alt="<?php echo addslashes($p_name); ?>"></a>
										</li>
										<li class="item_name">
											<!-- 상품명 --><a href="<?php echo $p_url; ?>" class="title"><?php echo $p_name; ?></a>
											<!-- 쿠폰 / 없으면 div 숨김 -->
											<?php if($product_coupon_normal_use == 'Y') { // 상품 쿠폰을 사용한다면?>
												<div class="c_coupon" title="쿠폰명">
													<!-- 주문결제페이지에서 div label 로 변경 -->
													<div class="coupon_box">
														<span class="coupon_tit">
															상품<br>쿠폰
														</span>
														<span class="one_coupon">
															<span class="txt tt"><?php echo $product_coupon_normal_title; ?> </span>
															<span class="txt"><strong><?php echo $product_coupon_normal_price; ?>원</strong>할인</span>
														</span>
													</div>
													<span class="shape ic_top"></span><span class="shape ic_bottom"></span>
												</div>
											<?php } ?>
										</li>
									</ul>

								</div>

								<div class="option if_only"><!-- 옵션이 1개일때는 if_only / 옵션이 2개 이상일때 열고닫기 버튼 클릭하면 if_open -->
									<!-- 옵션 ul반복 -->
									<ul>
										<?php echo $op_option_print; ?>
									</ul>
								</div>


							</li>
					<?php } ?>
				</ul>
			</div>

		</div>
	</div>





	<!-- 주문결제단계에서 묶음 -->
	<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
		<div class="c_group_tit"><span class="tit">주문자 정보</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"></a></div>

		<div class="c_form">
			<table>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">주문자 이름</span></th>
						<td><?php echo $row['o_oname']; ?></td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">주문자 휴대폰</span></th>
						<td><?php echo $row['o_ohp']; ?></td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">주문자 이메일</span></th>
						<td><?php echo $row['o_oemail']; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>





	<!-- 주문결제단계에서 묶음 -->
	<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
		<div class="c_group_tit"><span class="tit">배송지 정보</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"></a></div>

		<div class="c_form">
			<table>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">받는분 이름</span></th>
						<td><?php echo $row['o_rname']; ?></td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">받는분 휴대폰</span></th>
						<td><?php echo $row['o_rhp']; ?></td>
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
					</tr>
					<tr>
						<th class="ess"><span class="tit ">지번주소</span></th>
						<td>
							<?php echo (rm_str($row['o_rpost'])>0?'('.$row['o_rpost'].') ' : ''); ?>
							<?php echo $row['o_raddr1']; ?>
							<span><?php echo $row['o_raddr2']; ?></span>
						</td>
					</tr>
					<?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>
					<tr>
						<th class=""><span class="tit ">배송 메세지</span></th>
						<td><?php echo ($row['o_content']?nl2br($row['o_content']):''); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>



	<?php
		// 가상계좌 입금 은행 추가 kms 2019-05-20
		if ( $row['o_paymethod'] == "virtual" ) {
			$ol = _MQ("select * from smart_order_onlinelog where ool_ordernum = '".$row['o_ordernum']."' and ool_type='R' order by ool_uid desc limit 1");
			$row['o_price_real'] = $row['o_price_real'] + $ol['ool_escrow_fee']; // 총액 수수료더하기
		}
		// 가상 계좌 입금자명 체크
		$deposit_name_exist = true;
		if ( $ol['ool_deposit_name'] == "" ) {
			$deposit_name_exist = false;
		}
	?>
	<!-- 주문결제단계에서 묶음 -->
	<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
		<div class="c_group_tit"><span class="tit">결제 정보</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"></a></div>

		<div class="c_form ">
			<table>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">결제수단</span></th>
						<td>
							<?php echo $arr_payment_type[$row['o_easypay_paymethod_type'] != '' ? $row['o_easypay_paymethod_type'] : $row['o_paymethod']]; // LCY : 2021-07-04 : 신용카드 간편결제 추가 -- 결제수단 표기 -- ?>
							<?php
								$arr_occontent = array();
								$ex = explode("§§" , $row['oc_content']);
								foreach( $ex as $sk=>$sv ){
									$ex2 = explode("||" , $sv);
									$arr_occontent[$ex2[0]] = $ex2[1];
								}

								//- 카드 영수증 출력 ---
								if($row['oc_tid']) echo link_credit_receipt($row['o_ordernum'],'[영수증출력]');
							?>
						</td>
					</tr>
					<?php if($row['o_paymethod'] == 'online') { ?>
					<tr>
						<th class="ess"><span class="tit ">입금은행</span></th>
						<td><?php echo $row['o_bank']; ?></td>
					</tr>
					<tr>
						<th class="ess"><span class="tit">입금자명</span></th>
						<td><?php echo $row['o_deposit']; ?> <?php echo ($row['o_get_tax']=='Y'?'(현금영수증 발행을 신청하였습니다)':''); ?></td>
					</tr>
					<?php } ?>
					<?php // 가상계좌 추가 kms 2019-05-21 ?>
					<?php if($row['o_paymethod'] == 'virtual') { ?>
						<tr>
							<th class="ess"><span class="tit ">입금은행</span></th>
							<td><?=$ol['ool_account_num']?> (<?=$ol['ool_bank_name']?>)</td>
						</tr>
						<?php if ($deposit_name_exist) { ?>
							<tr>
								<th class="ess"><span class="tit">입금자명</span></th>
								<td>	<?=$ol['ool_deposit_name']?> <?=$row['o_get_tax']=="Y"?"(현금영수증 발행을 신청하였습니다)":""?></td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>




	<!-- 주문결제단계에서 묶음 -->
	<div class="c_order_box">
		<div class="c_group_tit"><span class="tit">최종 결제금액</span><!-- <a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"></a> --></div>
		<!-- 총 결제 금액 -->
		<div class="c_total_price">
			<div class="point">
				<span class="lineup"><strong><?php echo number_format($op_total_point); ?></strong> 포인트 적립</span>
			</div>
			<dl>
				<dt>총 상품금액</dt>
				<dd><span class="price_num"><strong><?php echo number_format($op_price_sum_total); ?></strong>원</span></dd>
			</dl>
			<dl>
				<dt>총 배송비</dt>
				<dd><span class="price_num"><strong><?php echo number_format($op_price_delivery); ?></strong>원</span><div class="ic_price ic_plus"></div></dd>
			</dl>
			<dl>
				<dt>총 할인금액</dt>
				<dd><span class="price_num"><strong><?php echo number_format($row['o_price_total'] + $row['o_price_delivery'] - $row['o_price_real']); ?></strong>원</span><div class="ic_price ic_minus"></div></dd>
			</dl>
			<dl class="total_num">
				<dt>총 주문금액</dt>
				<dd><span class="price_num"><strong><?php echo number_format($row['o_price_real']); ?></strong>원</span><div class="ic_price ic_equal"></div></dd>
			</dl>
		</div>
	</div>



	<div class="c_btnbox">
		<ul>
			<li><a href="/" class="c_btn h55 black line">홈으로</a></li>
			<?php if(is_login()) { ?>
				<li><a href="/?pn=mypage.main" class="c_btn h55 color">마이페이지</a></li>
			<?php } else { ?>
				<li><a href="/?pn=service.guest.order.list" class="c_btn h55 color">비회원 주문조회</a></li>
			<?php } ?>
		</ul>
	</div>


</div>
<!-- ◆공통페이지 섹션 -->


<script>
// 항목별로 열고/닫기 버튼
$(document).on('click', '.js_box_ctl', function(){
	$box = $(this).closest('.c_order_box');
	var is_open = $box.hasClass('if_closed');
	if(is_open) $box.removeClass('if_closed');
	else $box.addClass('if_closed');
});
</script>