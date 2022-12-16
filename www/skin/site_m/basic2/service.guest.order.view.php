<!-- ◆공통페이지 섹션 -->
<div class="c_section c_order js_guest_order">
	<?php if($row['o_ordernum'] == '' || count($sres) < 1) { ?>
		<div class="c_none"><div class="gtxt">주문내역이 없습니다.</div></div>
	<?php } else { ?>
		<!-- ◆ 주문완료 -->
		<div class="c_complete if_mypage">
			<div class="complete_box">
				<span class="order_number">주문번호 : <strong><?php echo $row['o_ordernum']; ?></strong></span>
			</div>
		</div>
		<!-- / 주문완료 -->



		<!-- 주문결제단계에서 묶음 -->
		<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
			<div class="c_group_tit order"><span class="tit">주문 상품 (<?php echo count($sres); ?>)</span><a href="#none" onclick="$(this).closest('.c_order_box').toggleClass('if_closed'); return false;" class="btn_ctrl" title="열고닫기"></a><!-- 내용을 열고접을 수 있음 --></div>

			<!-- ◆장바구니 리스트 -->
			<div class="c_cart_list">
				<div class="cart_table">
					<ul class="ul">
						<!-- 상품하나당 li반복 -->
						<?php
							$NpayDcPrice = 0; // LDD: 2018-07-21 네이버 페이 할인 (N포인트+N적립금)
							unset($op_price_delivery,$op_price_sum_total, $_num);
							foreach($sres as $k=>$v) {
								$sr = _MQ_assoc("
									select * from smart_order_product as op left join smart_product as p on (p.p_code=op.op_pcode)
									where op_pcode = '{$v['op_pcode']}' AND op_oordernum='{$row['o_ordernum']}'
									order by op_uid
								");
								unset($op_option_print, $option_name, $option_cnt, $op_total_price, $add_delivery_print, $op_total_point, $op_status_class);
								// 2017-10-13 ::: 배송비 오류 수정 ::: JJC
								unset($op_delivery_price, $op_add_delivery_price);
								foreach($sr as $sk=>$sv) {

									/* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */
									$NpayDcPrice += ($sv['npay_point']+$sv['npay_point2']);
									/* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */

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

									# 진행상태
									$op_status['total']++;
									if($v['o_canceled'] <> 'N' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
										$op_status['cancel']++;
									}else if($v['o_status'] == '결제실패'){ // 결제실패일경우
										$op_status['fail']++;
									}else{
										if($v['o_paystatus'] =='Y'){ // 주문결제를 했다면,
											if($sv['op_sendstatus'] == '배송대기') {
												$op_status['pay']++;
											}else if($sv['op_sendstatus'] == '배송준비'){
											$op_status['del_ready']++;
											}else if($sv['op_sendstatus'] == '배송중'){
												$op_status['delivery']++;
											}else if($sv['op_sendstatus'] == '배송완료'){
												$op_status['complete']++;
											}else{
												$op_status['cancel']++;
											}
										}else{ // 주문결제를 하지 않았다면
											$op_status['ready']++;
										}
									}

                                    # 부분 취소
                                    unset($app_btn_cancel); // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: $row['npay_order'] != 'Y'  --
                                    if($v['o_paystatus']=='Y' && $sv['op_is_addoption']!='Y' && $sv['op_settlementstatus']=='none' && $row['npay_order'] != 'Y' ) {
										switch($sv['op_cancel']) {
											case 'Y': // 취소완료
												$app_btn_cancel = '<a href="#none" onclick="return false;" data-ordernum="'.$row['o_ordernum'].'" data-opuid="'.$sv['op_uid'].'" class="c_btn h22 light product_cancel_view">취소내역</a>';
											break;
											case 'R': // 취소진행
												$app_btn_cancel = '<a href="#none" onclick="return false;" data-ordernum="'.$row['o_ordernum'].'" data-opuid="'.$sv['op_uid'].'" class="c_btn h22 light product_cancel_view">취소진행중</a>';
											break;
											default:
												if($v['o_canceled']=='N' && ($sv['op_sendstatus'] == '' || $sv['op_sendstatus'] == '배송대기' || $sv['op_sendstatus'] == '배송준비')) {
													$app_btn_cancel = '<a href="#none" onclick="return false;" data-ordernum="'.$row['o_ordernum'].'" data-opuid="'.$sv['op_uid'].'" class="c_btn h22 light line product_cancel">주문취소</a>';
												}
											break;
										}
									}

                                    // JJC : 간편결제 - 페이플 : 2021-06-05 - 부분취소불가
                                    if($row['o_paymethod'] == "payple") {$app_btn_cancel = "";}

									# 배송상태에 따른 버튼 및 상태값 출력
									unset($delivery_search, $complete_button, $complain_button);
									if($v['o_paystatus']=='Y' && $sv['op_is_addoption']!='Y' && $sv['op_cancel'] == 'N' && $row['o_canceled'] == 'N'){ // SSJ : 취소된 주문은 버튼이 노출되지 않도록 수정 : 2021-04-14
										switch($sv['op_sendstatus']) {
											case "":
											case "배송대기":
											case "배송준비":
												$delivery_search = "";
												$complete_button = "";
												$complain_button = "";
												break;
                                            case "배송중":
                                                $delivery_search = "<a href='".($row['npay_order'] == 'Y'?($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]):$arr_delivery_company[$sv['op_sendcompany']]). rm_str($sv['op_sendnum']) . "' class='c_btn h22 black' title='배송조회' target='_blank'>배송조회</a>";
                                                if(!$sv['op_complain']  && $v['npay_order'] != 'Y' ){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
                                                    $complete_button = "<a href='#none' onclick=\"order_complete('".$ordernum."','".$sv['op_pouid']."');return false;\" class='c_btn h22 light line' title='구매확인' >구매확인</a>";
                                                    $complain_button = "<a href='#none' onclick=\"complain_view('".str_replace("'","`",strip_tags($sv['op_pname'].$option_name))."','".$sv['op_uid']."');return false;\" class='c_btn h22 light line' title='교환/반품' >교환/반품</a>";
                                                }else{
                                                    $complete_button = "";
                                                    $complain_button = "<span class='c_btn h22 light'>".$arr_massage_conv[$sv['op_complain']]."</span>";
                                                }

                                            break;

                                            case "배송완료" :
                                                $delivery_search = "<a href='".($row['npay_order'] == 'Y'?($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]):$arr_delivery_company[$sv['op_sendcompany']]). rm_str($sv['op_sendnum']) . "' class='c_btn h22 black' target='_blank' title='배송조회' >배송조회</a>";
                                                $complete_button = "";
                                                if(!$sv['op_complain']  && $v['npay_order'] != 'Y'  ){ // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
                                                    $complain_button = "<a href='#none' onclick=\"complain_view('".str_replace("'","`",strip_tags($sv['op_pname'].$option_name))."','".$sv['op_uid']."');return false;\" class='c_btn h22 light line' title='교환/반품' >교환/반품</a>";
                                                }else{
                                                    $complain_button = "<span class='c_btn h22 light'>".$arr_massage_conv[$sv['op_complain']]."</span>";
                                                }
                                                break;

											default :
												echo "잘못된 배송단계 : ".$sv['op_sendstatus'];
												break;
										}
									}

									# 옵션처리
									$option_name = !$sv['op_option1'] ? '옵션없음' : trim(($sv['op_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . $sv['op_option1'].' '.$sv['op_option2'].' '.$sv['op_option3']);
									$option_cnt = $sv['op_cnt'];


									# 진행상태
									$op_status_icon = '';
									if($row['o_canceled'] == 'Y'){ // 주문자체가 취소 되었으면 주문취소 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패]
										$op_status_icon = "<span class='c_btn h22 black'>".$arr_massage_conv['주문취소']."</span>";
									}
									else if($op_status['fail']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제대기]
										$op_status_icon = "<span class='c_btn h22 black'>".$arr_massage_conv['주문취소']."</span>";
									}
									else if($op_status['ready']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소] - [결제대기]
										$op_status_icon = "<span class='c_btn h22 light'>".$arr_massage_conv['결제대기']."</span>";
									}
									else if($op_status['delivery']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
										$op_status_icon = "<span class='c_btn h22 color'>".$arr_massage_conv['배송중']."</span>";
									}
									else if($op_status['del_ready']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
										$op_status_icon = "<span class='c_btn h22 color'>".$arr_massage_conv['배송준비']."</span>";
									}
									else if($op_status['pay']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
										$op_status_icon = "<span class='c_btn h22 color'>".$arr_massage_conv['결제완료']."</span>";
									}
									else if($op_status['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
										$op_status_icon = "<span class='c_btn h22 color'>".$arr_massage_conv['배송완료']."</span>";
									}else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
										$op_status_icon = "<span class='c_btn h22 black'>".$arr_massage_conv['주문취소']."</span>";
									}


									# 주문취소 / 배송조회 / 교환반품 / 구매확인 버튼
									$arr_btn = array_filter(array($app_btn_cancel, $delivery_search, $complete_button, $complain_button, $op_status_icon));
									$btn_box_html = (count($arr_btn) > 0 ? '<div class="btn_box">' . implode($arr_btn) . '</div>' : null);

									$op_option_print .= '
										<li>
											<div class="tit_box">
												<div class="opt_tit">'. $option_name .' <strong>('. number_format($sv['op_cnt']) .'개)</strong></div>
												<div class="price">'. number_format($sv['op_price']) .'원</div>
											</div>
											'. $btn_box_html .'
										</li>
									';
								}


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


								# 총 배송비 합계
								$op_price_delivery += $delivery_price;
								# 총 합계
								$op_price_sum_total +=  $op_total_price;

								// 상품 기본정보
								$p_name = $v['op_pname'];
								$p_url = ($v['p_code'] ? "/?pn=product.view&pcode=".$v['p_code'] : "#none"); // 상품의 주소
								$p_thumb = get_img_src('thumbs_s_'.$v['p_img_list_square']); // 상품 이미지
						?>
							<li class="li">
								<div class="cart_item_box">
									<ul>
										<li class="thumb">
											<!-- 이미지 없을때 thumb_box 유지 -->
											<a href="<?php echo $p_url; ?>" target="_blank" class="thumb_box">
												<?php if($p_thumb) { ?><img src="<?php echo $p_thumb; ?>" alt="<?php echo addslashes(htmlspecialchars($p_name)); ?>"/><?php } ?>
											</a>
										</li>
										<li class="item_name">
											<!-- 상품명 -->
											<a href="<?php echo $p_url; ?>" target="_blank" class="title"><?php echo $p_name; ?></a>
										</li>
									</ul>
								</div>

								<div class="option if_only"><!-- 옵션이 1개일때는 if_only / 옵션이 2개 이상일때 열고닫기 버튼 클릭하면 if_open -->
									<!-- 옵션 ul반복 -->
									<ul>
										<?php echo $op_option_print; ?>
									</ul>
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
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>





		<!-- 주문결제단계에서 묶음 -->
		<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
			<div class="c_group_tit"><span class="tit">주문자 정보</span><a href="#none" onclick="$(this).closest('.c_order_box').toggleClass('if_closed'); return false;" class="btn_ctrl" title="열고닫기"></a></div>

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
			<div class="c_group_tit"><span class="tit">배송지 정보</span><a href="#none" onclick="$(this).closest('.c_order_box').toggleClass('if_closed'); return false;" class="btn_ctrl" title="열고닫기"></a></div>

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
							<th><span class="tit ">배송 메세지</span></th>
							<td><?php echo ($row['o_content']?htmlspecialchars(nl2br($row['o_content'])):''); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>




		<!-- 주문결제단계에서 묶음 -->
		<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
			<div class="c_group_tit"><span class="tit">결제 정보</span><a href="#none" onclick="$(this).closest('.c_order_box').toggleClass('if_closed'); return false;" class="btn_ctrl" title="열고닫기"></a></div>

			<div class="c_form ">
				<table>
					<tbody>
						<tr>
							<th class="ess"><span class="tit ">결제수단</span></th>
							<td>
								<?php echo $arr_payment_type[$row['o_easypay_paymethod_type'] != '' ? $row['o_easypay_paymethod_type'] : $row['o_paymethod']]; // LCY : 2021-07-04 : 신용카드 간편결제 추가 -- 결제수단 표기 -- ?>
								<?php echo $row['npay_order'] == 'Y' ? ' (네이버페이)':null; // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: 결제수단에 네이버페이 표기  --  ?>
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
								<td>
									<?php echo $row['o_deposit']; ?>
									<?php echo ($row['o_get_tax']=='Y'?'(현금영수증 발행을 신청하였습니다)':''); ?>
								</td>
							</tr>
						<?php } ?>

						<?php
							//![LCY] 2020-07-16 가상계좌 입금정보 추가
							if( $row['o_paymethod'] == "virtual" ) {
							$ol = _MQ("select * from smart_order_onlinelog where ool_ordernum = '".$row['o_ordernum']."' and ool_type='R' order by ool_uid desc limit 1");
							$row['o_price_real'] = $row['o_price_real'] + $ol['ool_escrow_fee']; // 총액 수수료더하기

							// 가상 계좌 입금자명 체크
							$deposit_name_exist = true;
							if ( $ol['ool_deposit_name'] == "" ) {
								$deposit_name_exist = false;
							}
						?>
						<tr>
							<th class="ess"><span class="tit ">입금은행</span></th>
							<td>
							<?=$ol['ool_account_num']?> (<?=$ol['ool_bank_name']?>)
							</td>
						</tr>
						<tr>
							<?php if ($deposit_name_exist) { ?>
								<th class="ess"><span class="tit ">입금자명</span></th>
								<td>
									<?=$ol['ool_deposit_name']?> <?=$row['o_get_tax']=="Y"?"(현금영수증 발행을 신청하였습니다)":""?>
								</td>
							<?php  } ?>
						</tr>
						<?php } ?>

					</tbody>
				</table>
			</div>
		</div>




		<!-- 주문결제단계에서 묶음 -->
		<div class="c_order_box">
			<div class="c_group_tit"><span class="tit">최종 결제금액</span><!-- <a href="" class="btn_ctrl" title="열고닫기"></a> --></div>
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
					<dd><span class="price_num"><strong><?php echo number_format($row['o_price_total'] + $row['o_price_delivery'] - $row['o_price_real'] + $NpayDcPrice); ?></strong>원</span><div class="ic_price ic_minus"></div></dd>
				</dl>
				<dl class="total_num">
					<dt>총 주문금액</dt>
					<dd><span class="price_num"><strong><?php echo number_format($row['o_price_real']); ?></strong>원</span><div class="ic_price ic_equal"></div></dd>
				</dl>
			</div>
		</div>
		<div class="c_btnbox">
			<ul>
				<li><a href="/?pn=service.guest.order.list" class="c_btn h55 black line">초기화</a></li>
				<?php
					// ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----
					if($row['o_canceled'] == "N" ) {
						if( in_array($row['o_status'] , array('결제대기','결제완료','배송대기')) && $row['npay_order'] <> 'Y' ){

							if($row['o_status']!='결제대기'&&in_array($row['o_paymethod'], $arr_refund_payment_type)) { // SSJ : 주문/결제 통합 패치 : 2021-02-24
								$cancel_function = 'order_cancel_virtual(\''.$row['o_ordernum'].'\', \''.$row['o_price_real'].'\');'; // 가상계좌
							}else {
								$cancel_function = 'order_cancel(\''.$row['o_ordernum'].'\');'; // 일반
							}

							// 주문취소 생성
							$app_btn_cancel = '<li><a href="#none" onclick="'. $cancel_function .' return false;" class="c_btn h55 light ">주문취소</a></li>';

							// 상품이 /취소/반품/교환 요청중인 상품 검사
							$chk_part_cancel = _MQ_result(" select count(*) from smart_order_product where op_oordernum = '".$row['o_ordernum']."' and op_is_addoption = 'N' and op_cancel != 'N' ");
							if( $chk_part_cancel > 0){
								$app_btn_cancel = '<li><a href="#none" onclick="alert(\"취소/반품/교환 요청중인 상품이 있습니다. 고객센터 '.$siteInfo['s_glbtel'] .'로 문의하세요.\") return false;" class="c_btn h55 light ">주문취소</a></li>';
							}
						}
						else {
							$app_btn_cancel = '<li><a href="#none" onclick="alert(\"주문취소가 불가능한 상태입니다. 고객센터 '.$siteInfo['s_glbtel'] .'로 문의하세요.\") return false;" class="c_btn h55 light ">주문취소</a></li>';
						}
						echo $app_btn_cancel;
					}
					// ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----
				?>
			</ul>
		</div>

	<?php } ?>
</div>
<!-- /공통페이지 섹션 -->

<?php
	# 내용 있을때만
	if($row['o_ordernum'] <> '' && count($sres) > 0) {
		# 교환/반품 팝업
		include($SkinData['skin_root'].'/mypage.order.view.complain.php');
		# 부분취소 팝업
		include($SkinData['skin_root'].'/mypage.order.view.cancel_pop.php');
?>
	<script type="text/javascript">
		$('#cash_issue').on('click',function(e){ // 현금영수증 신청 버튼
			e.preventDefault();
			if (confirm('<?=$row[o_oname]?>님 <?=$row[o_ohp]?> 번호로 현금영수증 발행을 신청합니다.')) {
				$.ajax({
					data: {'ordernum':'<?=$ordernum?>'},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/totalCashReceipt.ajax.php',
					success: function(data) {
						if(data=='AUTH'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
							$('#cash_status').text('현금영수증이 발행되었습니다');
						} else if(data=='OK') {
							$('#cash_status').text('현금영수증 발행이 신청되었습니다');
						} else { // 아니라면 오류 메세지
							alert('현금영수증 발행 신청에 실패했습니다.');
						}
					}
				});
			} else {
				return false;
			}
		});
		function order_complete(ordernum,pouid) {
			if(!confirm('구매확인 처리 하겠습니까?')) return;
			common_frame.location.href='<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php?_mode=complete&ordernum='+ordernum+'&pouid='+pouid;
		}
	</script>
<?php } ?>
<script>
	// 비회원 주문조회 시 스크롤 이동
	$(document).ready(function(){
		setTimeout(function(){
			scrolltoClass(".js_guest_order");
		}, 300);
	});
</script>





<?php // ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----?>
<?php
	# 가상계좌 주문취소일 경우 환불계정 레이아웃 미리 생성
	include_once($SkinData['skin_root'].'/mypage.order.pro.cancel_virtual.php');
?>

<script id="mypage_order_list">

	// 주문취소
	var cancel_trigger = true; // SSJ : 중복취소 방지 : 2021-12-31
	function order_cancel(ordernum){
		if(ordernum == '' || ordernum == undefined){
			alert('잘못된 접근입니다.');
			return false;
		}

		// SSJ : 중복취소 방지 : 2021-12-31
		if(cancel_trigger === false){
			alert('주문 취소를 진행중입니다. 잠시만 기다려 주시기 바랍니다.');
			return false;
		}

		if( confirm('정말 주문을 취소하시겠습니까?') == true ) {
			cancel_trigger = false; // SSJ : 중복취소 방지 : 2021-12-31
			common_frame.location.href=("<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php?_mode=cancel&ordernum=" + ordernum );
		}

	}

	// 가상계좌/무통장 주문취소
	function order_cancel_virtual(ordernum, price){
		// 콤마추가
		price = (price + '').comma();

		// 데이터 입력
		$('.cancel_virtual').find('input[name=ordernum]').val(ordernum);
		$('.cancel_virtual').find('.js_data_ordernum').text(ordernum);
		$('.cancel_virtual').find('.js_data_price').text(price);

		$('.cancel_virtual').lightbox_me({
			centered: true,
			closeEsc: false,
			onLoad: function() {
				$('.cancel_virtual').find('input:first').focus();
			},
			onClose: function() {
				// 데이터 삭제
				$('.cancel_virtual').find('input[name=ordernum]').val('');
				$('.cancel_virtual').find('.js_data_ordernum').text('');
				$('.cancel_virtual').find('.js_data_price').text('');
			}
		});
	}

</script>
<?php // ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----?>