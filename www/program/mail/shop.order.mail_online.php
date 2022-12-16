<?PHP
unset($mailing_app_content,$mailing_order_product_content); // 기본변수 초기화 <><>
$arrMailItem = array();

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

// ------------------- 주문자/기본정보  -------------------
$__first = mb_substr($or[o_oname],0,1,'utf8'); // 보안처리
$__last = mb_substr($or[o_oname],2,mb_strlen($or[o_oname], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['orderName'] = $__first."*".(mb_strlen($or[o_oname],'utf8') > 2 ? $__last : null); // 주문자명 가운데 * 처리
$arrMailItem['orderLink'] = $or['o_memtype'] == 'Y' ? $app_HTTP_URL."/?pn=mypage.order.view&ordernum=".$or['o_ordernum']:$app_HTTP_URL."/?pn=member.login.form"; // 주문버호 링크
$arrMailItem['orderDate'] = date("Y.m.d H:i",strtotime($or['o_rdate'])); // 주문일자
// ------------------- 주문자/기본정보  끝 -------------------


// ------------------- 주문  결제정보  -------------------
$arrMailItem['orderPriceTotal'] = number_format($or[o_price_total]); // 총 주문금액
$arrMailItem['orderPriceDelivery'] = number_format($or[o_price_delivery]); // 총 배송금액
$arrMailItem['orderSpriceTotal'] = number_format($or[o_price_usepoint]+$or[o_price_coupon_individual]+$or[o_price_coupon_product]+$or[o_promotion_price]); // 총 할인금액
$arrMailItem['orderPriceSupplypoint'] = number_format($or[o_price_supplypoint]); // 총 적립금
$arrMailItem['orderPaymethod'] = $arr_payment_type[$or[o_paymethod]]; // 결제수단
$arrMailItem['orderBank'] = $or[o_bank]; // 결제계좌
$arrMailItem['orderPriceReal'] = number_format($or[o_price_real]); // 최총 결제금액

// ------------------- 주문  결제정보 끝 -------------------

// ------------------- 배송지 정보  -------------------
$__first = mb_substr($or[o_rname],0,1,'utf8'); // 보안처리
$__last = mb_substr($or[o_rname],2,mb_strlen($or[o_rname], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['receiveName'] = $__first."*".(mb_strlen($or[o_rname],'utf8') > 2 ? $__last : null); // 수령인 가운데 * 처리
$__arrRhp = explode("-",tel_format($or['o_rhp']));
$arrMailItem['receiveHp'] =  count($__arrRhp) > 2 ? $__arrRhp[0].'-'.$__arrRhp[1].'-****' : null;   // 수령인 연락처
$arrMailItem['receiveZonecode'] =  $or[o_rzonecode]; // 새우편주소
$arrMailItem['receiveAddrDoro'] =  $or[o_raddr_doro]; // 도로명 주소
$arrMailItem['receiveAddr2'] =  LastCut($or[o_raddr2],2); // 나머지 주소
$arrMailItem['o_content'] =  stripslashes($or['o_content']); // 배송시 문구
// ------------------- 배송지 정보  끝 -------------------


// ------------------- 주문상품정보  -------------------
$arrOrderPrdocutOption = $group_opr = $chk_group_opr  = array();
if( count($opr) > 0) {
	foreach( $opr as $opk=>$opv ){

		if($chk_group_opr[$opv['p_code']] !== true){  $group_opr[$opk] = $opr[$opk]; $chk_group_opr[$opv['p_code']] = true;  }
		$arrOrderPrdocutOption[$opv['p_code']][$opv['op_uid']] = $opv;
	}

	foreach( $group_opr as $key=>$opv ){

		// ------------------- 주문상품옵션 정보/합산 -------------------
		$arrOptionInfo = array();
		foreach($arrOrderPrdocutOption[$opv['p_code']] as  $sk => $sv){
			if( $sv['op_pouid'] > 0){
				$arrOptionInfo['optionName'][] = ($sv['op_is_addoption'] == 'Y' ? '추가 : ':'선택 : ' ).implode(" ",array($sv['op_option1'],$sv['op_option2'],$sv['op_option3']))." ".number_format($sv['op_price'])."원 x ".$sv['op_cnt'];
			}

			$arrOptionInfo['totalCnt'] += $sv['op_cnt'];
			$arrOptionInfo['totalPrice'] +=  $sv['op_price'] * $sv['op_cnt'];
		}
		// ------------------- 주문상품옵션 정보/합산 끝  -------------------


		$mailing_order_product_content .= "
			<!-- tr반복 -->
			<tr>
				<!-- 상품이미지 -->
				<td style='padding:20px 0; border-bottom:1px solid #ddd; vertical-align:top;'>
					<!-- 상품으로 이동 -->
					<a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$opv['p_code']."' style='overflow:hidden; border:1px solid #e6e6e6; box-sizing:border-box; display:block; margin-right:30px' target='_blank'>
						<img src='".get_img_src($opv['p_img_list_square'], IMG_DIR_PRODUCT)."' alt='".$opv['p_name']."' style='width:100%; float:left; min-width:60px; border:0 !important'/>
					</a>
				</td>

				<!-- 상품 주문정보 -->
				<td style='padding:20px 0; border-bottom:1px solid #ddd; vertical-align:top; line-height:17px;'>
					<table style='width:100%;border-spacing:0; font-size:13px;'>
						<colgroup>
							<col width='20%'/><col width='*'/>
						</colgroup>
						<tbody>
							<!-- 주문정보 tr 반복 -->
							<tr>
								<td style='vertical-align:top; padding:4px 0'>상품명</td>
								<!-- 상품 링크 -->
								<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'><a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$opv['p_code']."' style='font-weight:600; text-decoration:none; color:#666' target='_blank'>".$opv['p_name']."</a></td>
							</tr>
		";


		// ------------------- 주문상품옵션명 출력/없을경우 안나옴   -------------------
		if( count($arrOptionInfo['optionName']) > 0) {
			$mailing_order_product_content .= "
								<tr>
									<td style='vertical-align:top; padding:4px 0'>옵션명</td>
									<!-- 옵션 br 구분 -->
									<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>
									".implode("<br/>",$arrOptionInfo['optionName'])."
									</td>
								</tr>
			";
		}
		// ------------------- 주문상품옵션명 출력/없을경우 안나옴 끝  -------------------

		$mailing_order_product_content .= "
							<tr>
								<td style='vertical-align:top; padding:4px 0'>주문금액</td>
								<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>".number_format($arrOptionInfo['totalPrice'])."원</td>
							</tr>
							<tr>
								<td style='vertical-align:top; padding:4px 0'>수량</td>
								<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>".number_format($arrOptionInfo['totalCnt'])."</td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
		";
	}
}
// ------------------- 주문상품정보 끝 -------------------


$mailing_app_content = "

		<!-- ● Common Box -->
		<div style='padding:30px;'>
		<table style='width:100%;border-spacing:0; font-size:12px; font-family:\"돋움\",Dotum; line-height:17px;'>
			<tbody>
				<tr>
					<td style='text-align:center; border-bottom:1px solid #ddd; padding:3px 0 36px;'>
						<!-- 메일링 타이틀 이미지 -->
						<!-- 타이틀 이미지 없을때 기본 타이틀 이미지 default_txt.jpg 사용 -->
						<div class='' style='max-width:100%; display:inline-block'><img src='".$app_HTTP_URL."/images/mailing/order_complete.jpg' alt='주문이 완료 되었습니다.' style='width:100%; border:0 !important'/></div>
					</td>
				</tr>
				<tr>
					<td>
						<!-- 주문정보 -->
						<table style='width:100%;border-spacing:0; font-size:14px; letter-spacing:-0.5px; color:#666; margin-top:45px;'>
							<colgroup>
								<col width='100'/><col width='*'/>
							</colgroup>
							<tbody>
								<!-- tr반복 -->
								<tr>
									<!-- 타이틀 -->
									<td style='padding:4px 0'>고객명</td>
									<!-- 이름 / 가운데 * 표시 -->
									<td style='padding:4px 0'>".$arrMailItem['orderName']."</td>
								</tr>
								<tr>
									<td style='padding:4px 0'>주문번호</td>
									<!-- 주문내역 상세보기 링크 -->
									<td style='padding:4px 0'><a href='".$arrMailItem['orderLink']."' style='color:#000;font-weight:600;letter-spacing:0px;text-decoration:none;' target='_blank'>".$or['o_ordernum']."</a></td>
								</tr>
								<tr>
									<td style='padding:4px 0'>주문일자</td>
									<td style='letter-spacing:0;padding:4px 0'>".$arrMailItem['orderDate']."</td>
								</tr>
							</tbody>
						</table>


						<!-- 주문정보 리스트 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:42px;'>
							<colgroup>
								<col width='23%'/><col width='*'/>
							</colgroup>
							<thead>
								<!-- 타이틀 -->
								<tr>
									<th colspan='3' style='text-align:left; border-bottom:1px solid #555; color:#333; font-size:15px; font-weight:600; padding-bottom:8px'>주문정보</th>
								</tr>
							</thead>
							<tbody>
							".$mailing_order_product_content."
							</tbody>
						</table>


						<!-- 결제정보 리스트 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:47px;'>
							<thead>
								<!-- 타이틀 -->
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #555; color:#333; font-size:15px; font-weight:600; padding-bottom:8px'>결제정보</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan='2' style='padding:14px 0 13px'>
										<table style='width:100%;border-spacing:0;'>
											<colgroup>
												<col width='100'/><col width='*'/>
											</colgroup>
											<tbody>
												<!-- 결제정보 tr 반복 -->
												<tr>
													<!-- 타이틀 -->
													<td style='vertical-align:top;padding:4px 0'>총 주문금액</td>
													<!-- 결제정보 내용 -->
													<td style='text-align:right;vertical-align:top;text-align:right; font-weight:600;padding:4px 0; letter-spacing:0px;'>".$arrMailItem['orderPriceTotal']."원</td>
												</tr>
												<tr>
													<td style='vertical-align:top;padding:4px 0'>할인금액</td>
													<td style='text-align:right;vertical-align:top;text-align:right; font-weight:600;padding:4px 0; letter-spacing:0px;'>(-) ".$arrMailItem['orderSpriceTotal']."원</td>
												</tr>
												<tr>
													<td style='vertical-align:top;padding:4px 0'>적립금</td>
													<td style='text-align:right;vertical-align:top;text-align:right; font-weight:600;padding:4px 0; letter-spacing:0px;'>(+) ".$arrMailItem['orderPriceSupplypoint']."원</td>
												</tr>
												<tr>
													<td style='vertical-align:top;padding:4px 0'>배송비</td>
													<td style='text-align:right;vertical-align:top;text-align:right; font-weight:600;padding:4px 0; letter-spacing:0px;'>".$arrMailItem['orderPriceDelivery']."원</td>
												</tr>
												<tr>
													<td style='vertical-align:top;padding:4px 0'>결제수단</td>
													<td style='text-align:right;vertical-align:top;text-align:right; font-weight:600;padding:4px 0; letter-spacing:0px;'>".$arrMailItem['orderPaymethod']."</td>
												</tr>
												<tr>
													<td style='vertical-align:top;padding:4px 0;'>결제계좌</td>
													<td style='text-align:right;vertical-align:top;text-align:right;color:#0066ff;padding:4px 0; letter-spacing:0px;'>".$arrMailItem['orderBank']."</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<!-- 총금액 -->
								<tr>
									<td style='vertical-align:top; font-size:15px; font-weight:600;border-top:1px solid #ddd; border-bottom:1px solid #ddd;padding:17px 0 15px;color:#333; letter-spacing:-0.5px'>최종결제 금액</td>
									<td style='text-align:right;vertical-align:top;text-align:right;color:#ff0000; font-size:14px; font-weight:600;letter-spacing:0.5px;border-top:1px solid #ddd; border-bottom:1px solid #ddd;padding:17px 0 15px'>".$arrMailItem['orderPriceReal']."원</td>
								</tr>
							</tbody>
						</table>

						<!-- 배송정보 -->
						<table style='width:100%;border-spacing:0; font-size:13px; letter-spacing:-0.5px; color:#666; margin-top:47px;'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #555; color:#333; font-size:15px; font-weight:600; padding-bottom:8px'>배송정보</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding:15px 0; border-bottom:1px solid #ddd;'>
										<table style='width:100%;border-spacing:0; '>
											<colgroup>
												<col width='100'/><col width='*'/>
											</colgroup>
											<tbody>
												<tr>
													<td style='vertical-align:top; padding:4px 0;'>수령인</td>
													<!-- 이름 / 가운데 * 표시 -->
													<td style='vertical-align:top;font-weight:600; padding:4px 0;'>".$arrMailItem['receiveName']."</td>
												</tr>
												<tr>
													<td style='vertical-align:top; padding:4px 0;'>연락처</td>
													<!-- 연락처 / 뒤에 4자리 * 표시 / 유선전화 없으면 | 삭제 -->
													<td style='vertical-align:top;font-weight:600; padding:4px 0; letter-spacing:0px;'>".$arrMailItem['receiveHp']."</td>
												</tr>
												<tr>
													<td style='vertical-align:top; padding:4px 0;'>배송지</td>
													<td style='vertical-align:top; font-weight:600; padding:4px 0;line-height:20px; letter-spacing:0px;'>
														<!-- 새우편주소 -->
														".$arrMailItem['receiveZonecode']."<br/>
														<!-- 도로명주소 -->
														".$arrMailItem['receiveAddrDoro']."<br/>
														<!-- 나머지주소 / 나머지주소 기준으로 뒤에 4글자 * 표시 -->
														".$arrMailItem['receiveAddr2']."
													</td>
												</tr>
												".($arrMailItem['o_content'] != '' ? "
												<tr>
													<td style='vertical-align:top; padding:4px 0;'>배송메모</td>
													<td style='vertical-align:top; padding:4px 0; word-wrap:break-word; word-break:keep-all;'>".$arrMailItem['o_content']."</td>
												</tr>
												":null)."

											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
		<!-- / Common Box -->
";

