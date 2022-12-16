<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "주문내역";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력
?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_order">



	<!-- ◆ 주문완료 -->
	<div class="c_complete if_mypage">
		<div class="complete_box">
			<span class="order_number">주문번호 : <strong><?php echo $row['o_ordernum']; ?></strong></span>
		</div>
	</div>
	<!-- / 주문완료 -->



	<!-- 주문결제단계에서 묶음 -->
	<div class="c_order_box"><!-- 처음에 열려있고 버튼클릭하면 열리면서 클래스값 if_closed -->
		<div class="c_group_tit order"><span class="tit">주문 상품 (<?php echo number_format(count($sres)); ?>)</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"></a><!-- 내용을 열고접을 수 있음 --></div>

		<!-- ◆장바구니 리스트 -->
		<div class="c_cart_list">
			<!-- 마이페이지미사용 -->
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
						$NpayDcPrice = 0; // LDD: 2018-07-21 네이버 페이 할인 (N포인트+N적립금)
						unset($op_price_delivery,$op_price_sum_total, $_num);
						foreach( $sres as $k=>$v ){
							// No. 설정
							$_num++;

							$res = _MQ_assoc("
								select *
								from smart_order_product as op
								left join smart_product as p on ( p.p_code=op.op_pcode )
								where op_pcode = '".$v['op_pcode']."' AND op_oordernum='{$ordernum}'
								order by op_uid asc
							");
							//ViewArr($res);
							unset($op_option_print,$option_name,$option_cnt,$op_total_price, $add_delivery_print,$op_total_point,$op_status_class,$op_delivery_price, $op_add_delivery_price);
							$op_status = array(); // 주문상품별 주문/배송 진행상태 체크 SSJ : 2018-02-14
							foreach($res as $sk=>$sv) {

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
                                unset($app_btn_cancel);  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
                                if($v['o_paystatus']=='Y' && $sv['op_is_addoption']!='Y' && $sv['op_settlementstatus']=='none' && $v['npay_order'] != 'Y'  ) {
									switch($sv['op_cancel']) {
										case 'Y': // 취소완료
											$app_btn_cancel = "<a href='#none' onclick=\"return false;\" data-ordernum='".$row['o_ordernum']."' data-opuid='".$sv['op_uid']."' class='c_btn h22 light product_cancel_view'>취소내역</a>";
											break;
										case 'R': // 취소진행
											$app_btn_cancel = "<a href='#none' onclick=\"return false;\" data-ordernum='".$row['o_ordernum']."' data-opuid='".$sv['op_uid']."' class='c_btn h22 light product_cancel_view'>취소진행중</a>";
											break;
										default:
											if($v['o_canceled']=='N' && ($sv['op_sendstatus'] == '' || $sv['op_sendstatus'] == '배송대기' || $sv['op_sendstatus'] == '배송준비')) {
												// SSJ : 주문/결제 통합 패치 : 2021-02-24
												if(in_array($v['o_paymethod'], $arr_cancel_part_payment_type) || in_array($v['o_paymethod'], $arr_refund_payment_type)){
													$app_btn_cancel = "<a href='#none' onclick=\"return false;\" data-ordernum='".$row['o_ordernum']."' data-opuid='".$sv['op_uid']."' class='c_btn h22 light line product_cancel'>주문취소</a>";
												}
											}
											break;
									}
								}

                                // JJC : 간편결제 - 페이플 : 2021-06-05 - 부분취소불가
                                if($row['o_paymethod'] == "payple") {$app_btn_cancel = "";}

								# 옵션처리
								$option_name = !$sv['op_option1'] ? '옵션없음' : trim(($sv['op_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . $sv['op_option1'].' '.$sv['op_option2'].' '.$sv['op_option3']);
								$option_cnt			= $sv['op_cnt'];

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
                                            $delivery_search = "<a href='".($row['npay_order'] == 'Y'?($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]):$arr_delivery_company[$sv[op_sendcompany]]). rm_str($sv['op_sendnum']) . "' class='c_btn h22 black' title='배송조회' target='_blank'>배송조회</a>";
                                            if(!$sv['op_complain']  && $row['npay_order'] != 'Y'  ){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 ::  && $row['npay_order'] != 'Y'  --
                                                $complete_button = "<a href='#none' onclick=\"order_complete('".$ordernum."','".$sv['op_pouid']."');return false;\" class='c_btn h22 light line' title='구매확인' >구매확인</a>";
                                                $complain_button = "<a href='#none' onclick=\"complain_view('".str_replace("'","`",strip_tags($sv['op_pname'].$option_name))."','".$sv['op_uid']."');return false;\" class='c_btn h22 light line' title='교환/반품' >교환/반품</a>";
                                            }else{
                                                $complete_button = "";
                                                $complain_button = "<span class='c_btn h22 light'>".$arr_massage_conv[$sv['op_complain']]."</span>";
                                            }
                                            break;

                                        case "배송완료" :
                                            $delivery_search = "<a href='".($row['npay_order'] == 'Y'?($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]):$arr_delivery_company[$sv[op_sendcompany]]). rm_str($sv['op_sendnum']) . "' class='c_btn h22 black' target='_blank' title='배송조회' >배송조회</a>";
                                            $complete_button = "";
                                            if(!$sv['op_complain']  && $row['npay_order'] != 'Y'  ){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 ::  && $row['npay_order'] != 'Y'  --
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
							$p_name	= strip_tags($v['op_pname']);	// 상품명
							$p_url = ($v['p_code'] ? "/?pn=product.view&pcode=".$v['p_code'] : "#none"); // 상품의 주소
							# 상품의 썸네일
							$p_thumb	= get_img_src('thumbs_s_'.$v['p_img_list_square']); // 상품 이미지
							if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/skin/thumb.gif';
							# 총 배송비 합계
							$op_price_delivery += $delivery_price;
							# 총 합계
							$op_price_sum_total +=  $op_total_price;


							/*
								LCY -- 장바구니 다시 담기
								$option_select_type  => 넘겨준다. 옵션값을
							*/
							$cnt_tmp = _MQ("select count(*) as cnt from smart_cart  where c_pcode = '". $v['p_code'] ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '".$v['op_pouid']."'");
							$re_cart_html = '';
							if($cnt_tmp['cnt'] > 0) { //이미 담긴 상품
								$re_cart_html = '<span class="c_tag h22 light line">이미 담긴상품</span>';
							}else{
								if($v['p_view'] == 'N'){
									$re_cart_html = '<span class="c_tag h22 light line">판매종료된 상품</span>';
								}else if($v['p_stock'] <= 0 ){
									$re_cart_html = '<span class="c_tag h22 light line">품절된 상품</span>';
								}else{
									$re_cart_html = '<a href="#none" onclick="_re_cart_pro(\''. $v['op_oordernum'] .'\', \''. $v['p_code'] .'\', \'\', \''. ($v['p_stock']	< $v['op_cnt'] ? 'stock' : 'none') .'\')" class="c_btn h22 dark line">장바구니 담기</a>';
								}
							}
					?>
							<li class="li">

								<div class="cart_item_box">
									<ul>
										<li class="thumb">
											<!-- 이미지 없을때 thumb_box 유지 -->
											<a href="<?php echo $p_url; ?>" class="thumb_box" target="_blank"><img src="<?php echo $p_thumb; ?>" alt="<?php echo addslashes($p_name); ?>"></a>
										</li>
										<li class="item_name">
											<!-- 상품명 --><a href="<?php echo $p_url; ?>" class="title" target="_blank"><?php echo $p_name; ?></a>
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
						<td><?php echo $row['o_deposit']; ?> <?php echo ($row['o_get_tax']=='Y'?'(현금영수증 발행을 신청하였습니다)':''); ?></td>
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
			<li><a href="/?<?php echo ($_PVSC ? enc('d',$_PVSC) : 'pn=mypage.order.list'); ?>" class="c_btn h55 black line">목록으로</a></li>
		</ul>
	</div>


</div>
<!-- /공통페이지 섹션 -->




<?php
	# 교환/반품 팝업
	include($SkinData['skin_root'].'/mypage.order.view.complain.php');
	# 부분취소 팝업
	include($SkinData['skin_root'].'/mypage.order.view.cancel_pop.php');
?>


<script>

// 항목별로 열고/닫기 버튼
$(document).on('click', '.js_box_ctl', function(){
	$box = $(this).closest('.c_order_box');
	var is_open = $box.hasClass('if_closed');
	if(is_open) $box.removeClass('if_closed');
	else $box.addClass('if_closed');
});

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
	<? // 에스크로 처리 부분 -- 아직 사용하지 않음
/*		$c = _MQ("select ool_escrow from smart_order_onlinelog where ool_ordernum = '{$row[o_ordernum]}' order by ool_uid desc limit 1");
		if($c[ool_escrow]=='Y') { echo $complete_print; }
		else {
*/	?>
			common_frame.location.href='<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php?_mode=complete&ordernum='+ordernum+'&pouid='+pouid;
	<? //} ?>

}

// -- LCY 카트 다시 담기 기능
// cart_stats : 카트상태 공백일 경우만 가능 , if_stats : 카트상태가 공백이고, 재고량이 있을 시
function _re_cart_pro(ordernum,opcode,cart_stats,if_stats)
{

	if(opcode == '' || opcode == undefined || ordernum == '' || ordernum == undefined ){
		alert('해당정보가 누락되었습니다.');
		return false;
	}

	if(cart_stats == ''){ // 카트에 담을 수 있다면
		if(if_stats == 'stock'){
			if(confirm('선택하신 상품의 재고량이 부족하여, 남은 재고량으로 장바구니에 담을 수 있습니다.\n장바구니에 담으시겠습니까?') === false){
				return false;
			}
		}else{
			if(confirm('선택하신 상품을 장바구니에 다시 담으시겠습니까?') === false){
				return false;
			}
		}
	}else{

		return false;
	}

		$.ajax({
			data: {'ordernum':ordernum,'opcode':opcode,'if_stats':if_stats},
			type: 'POST',
			cache: false,
			dataType:'json',
			url: '<?php echo OD_PROGRAM_URL; ?>/ajax.re_cart.pro.php',
			success: function(data) {
				//console.log(data['result']+'\n'+data['console']);
				if(data['result'] == 'success'){ // 장바구니에 다시 담았을 시
						if(confirm('해당 상품을 장바구니에 다시 담았습니다. 장바구니로 이동하시겠습니까?') === true){
							window.location.href='/?pn=shop.cart.list';
						}else{
							window.location.reload();
						}
				}else{ // 장바구니 다시 담기에 실패 하였을 시 상세 페이지 이동을 물어본다.
					if(confirm('해당 상품을 장바구니에 담지 못하였습니다. 상품 상세페이지로 이동하시겠습니까?') === true){
						window.location.href='/?pn=product.view&pcode='+opcode;
					}else{
						window.location.reload();
					}
				}
			}
		});

}
</script>