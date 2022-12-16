<?php

	$app_current_link = '_cancel.list.php';// 메뉴지정
	include_once('wrap.header.php');


	$r = _MQ("
		select
			op.* , o.* , p.*
		from smart_order_product as op
		inner join smart_order as o on (o.o_ordernum = op.op_oordernum)
		inner join smart_product as p on (p.p_code = op.op_pcode)
		where op.op_oordernum='".$_ordernum."' and op.op_uid = '".$uid."' and op.op_is_addoption = 'N'
	");
	//ViewArr($r);
	if($r['op_uid']<1) error_msg('잘못된 접근입니다.');


	// 주문 상품정보 추출
	//$totalPrice = 0 ;//총상품가격
	$arr_product = array();

	// 이미지 체크
	$_p_img = get_img_src('thumbs_s_'.$r['p_img_list_square']);
	if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

	//$pro_view_link = get_pro_view_link($sv[p_code]);
	//$p_img_list = get_img_src($sv[p_img_list]);


	//{{{혜택표기}}} -- 혜택에 대한 처리
	$arrBoonInfo =  array();
	// 혜택에 대한 처리 :: list1 -  회원 할인/추가적립
	if( $r['op_groupset_use'] == 'Y' && $r['op_groupset_price_per'] > 0 ){ $arrBoonInfo[] = '회원할인 '.odt_number_format($r['op_groupset_price_per'],1).'%'; }
	if( $r['op_groupset_use'] == 'Y' && $r['op_groupset_point_per'] > 0 ){ $arrBoonInfo[] = '회원추가적립'.odt_number_format($r['op_groupset_price_per'],1).'%'; }

	// 혜택에 대한 처리 :: list2 -  쿠폰적용여부
	$rowClChk = _MQ("select count(*) as cnt from smart_order_coupon_log where cl_oordernum = '".$r['op_oordernum']."' and cl_pcode = '".$r['op_pcode']."'   ");
	if( $rowClChk['cnt'] > 0){ $arrBoonInfo[] = '상품쿠폰사용 '.number_format($rowClChk['cnt']).'개';  }

	// 혜택에 대한 처리 :: list3 -  무료배송
	if( $r['op_free_delivery_event_use'] == 'Y'){ $arrBoonInfo[] = '무료배송 이벤트'; }

	$r['boonInfo'] = count($arrBoonInfo) > 0 ? implode("<br>",$arrBoonInfo) : '-';
	//{{{혜택표기}}}


	# 배송조회
	if(in_array($r['op_sendstatus'], array('배송중','배송완료')) && $r['op_sendcompany'] && $r['op_sendnum']){
		$r['delivery_print'] = '
			<div class="lineup-vertical">
				<span class="bold">'. $r['op_sendcompany'] .'</span>
				<span class="block">'. $r['op_sendnum'] .'</span>
				<a href="'. ($r['npay_order'] == 'Y' ? ($NPayCourier[$r[op_sendcompany]]?$NPayCourier[$r[op_sendcompany]]:$arr_delivery_company[$r[op_sendcompany]]) : $arr_delivery_company[$r['op_sendcompany']]) . rm_str($r['op_sendnum']) .'" class="c_btn h22 green line h22 t4" target="_blank">배송조회</a>
			</div>
		';
	}



	//상품수 , 포인트 , 상품금액
	$arr_product['cnt'] += $r['op_cnt'];//상품수
	$arr_product['point'] += $r['op_point'];//포인트
	$arr_product['sum'] += $r['op_price'] * $r['op_cnt'];//상품금액
	$arr_product['add_delivery'] += $r['op_delivery_price'] + $r['op_add_delivery_price'];//개별배송비 포장

	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
	$arr_product["discount"] += $r['op_cancel_discount_price'];
	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------

?>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>취소요청상품정보</strong></div>



	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<table class="table_list">
			<colgroup>
				<col width="100"><col width="*"><col width="60"><col width="90"><col width="100"><col width="100"><col width="100"><col width="<?php echo ($r['boonInfo'] <> '' ? '140':'100'); ?>"><col width="60"><col width="120">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">이미지</th>
					<th scope="col">상품명</th>
					<th scope="col">수량</th>
					<th scope="col">상품가격</th>
					<th scope="col">적립금</th>
					<th scope="col">주문금액</th>
					<th scope="col">배송비</th>
					<th scope="col">할인혜택</th>
					<th scope="col">배송상태</th>
					<th scope="col">배송정보</th>
				</tr>
			</thead>
			<tbody>
					<tr>
						<td class="img80"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes($r['p_name']); ?>"></td>
						<td>
							<!-- 상품정보 -->
							<div class="order_item">
								<!-- 상품명 -->
								<div class="title bold"><?php echo $r['p_name']; ?></div>
								<?php if($r['op_pouid']>0){ ?>
									<!-- 옵션명, div반복 -->
									<div class="option bullet">
										<span class="option_name"><?php echo ($r['op_is_addoption']=='Y'?'추가 : ':'선택 : '); ?><?php echo implode(' ', array_filter(array($r['op_option1'],$r['op_option2'],$r['op_option3']))); ?></span>
									</div>
								<?php } ?>
							</div>
						</td>
						<td class="t_black"><?php echo number_format($r['op_cnt']); ?></td>
						<td class="t_black"><?php echo number_format($r['op_price']); ?>원</td>
						<td class="t_black"><?php echo number_format($r['op_point']); ?></td>
						<td class="t_black bold"><?php echo number_format($r['op_price']*$r['op_cnt']); ?>원</td>
						<td class="t_black bold">
							<?php echo number_format($r['op_delivery_price']); ?>원
							<?php if($r['op_delivery_type']<>'입점'){ ?>
								<br>(<?php echo $r['op_delivery_type']; ?>배송)
							<?php } ?>

							<?php if($r['op_add_delivery_price']>0){ ?>
								<div class="normal" style="margin-top:5px;">+<?php echo number_format($r['op_add_delivery_price']); ?>원<br>(추가배송비)</div>
							<?php } ?>
						</td>
						<td class="t_orange bold">
							<?php echo $r['boonInfo']; ?>
						</td>
						<td>
							<div class="lineup-vertical">
								<?php echo $arr_adm_button[$r['op_sendstatus']]; ?>
							</div>
						</td>
						<td>
							<?php
								if($r['delivery_print']) echo $r['delivery_print'];
							?>
						</td>
					</tr>
			</tbody>
		</table>

		<!-- 결제금액정보 -->
		<div class="total_price">
			<div>
				<ul>
					<li>총상품가격 : <strong><?php echo number_format($arr_product['sum']); ?></strong><em>원</em></li>
					<li>총배송비 : <strong><?php echo number_format($arr_product['add_delivery']); ?></strong><em>원</em></li>
					<li>총할인액 : <strong><?php echo number_format($arr_product['discount']); ?></strong><em>원</em></li>
					<li>
						총환불금액 : <strong><?php echo number_format($arr_product['sum']+$arr_product['add_delivery']-$arr_product['discount']); ?></strong><em>원</em>
						<input type="hidden" name="cancel_total" value="<?php echo ($arr_product['sum']+$arr_product['add_delivery']-$arr_product['discount']); ?>"/>
					</li>
				</ul>
			</div>
		</div>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>고객 요청내용</strong></div>

	<form name="frm" method="post" action="_cancel.pro.php" >
	<input type="hidden" name="_mode" value="modify">
	<input type="hidden" name="ordernum" value='<?php echo $_ordernum; ?>'>
	<input type="hidden" name="op_uid" value="<?php echo $uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="statusUpdate" value="yes">
	<input type="hidden" name="cancel_type" value="<?php echo $r["op_cancel_type"]; ?>">
	<input type="hidden" name="cancel_bank" class="js_change_val" value="<?php echo $r['op_cancel_bank']; ?>">
	<input type="hidden" name="cancel_bank_account" class= "js_cancel_bank_account" value="<?php echo $r['op_cancel_bank_account']; ?>" />
	<input type="hidden" name="cancel_bank_name" class="js_cancel_bank_name" value="<?php echo $r['op_cancel_bank_name']; ?>" />

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>고객 요청내용</th>
						<td>
							<textarea name="cancel_msg" rows="4" cols="" class="design"><?php echo $r['op_cancel_msg']; ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>



			<!-- 상세페이지 버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h46 red"><input type="submit" name="" value="정보수정" accesskey="s"></span></li>
					<li><a href="_cancel.list.php<?php echo ($_PVSC ? '?'.enc('d' , $_PVSC) : null); ?>" class="c_btn h46 black line" accesskey="l">목록으로</a></li>
				</ul>
			</div>


		</div>

	</form>




	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>주문 및 결제 정보</strong></div>

	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>주문번호</th>
					<td class="only_text"><?php echo $r['o_ordernum']; ?></td>
					<th>주문일시</th>
					<td class="only_text"><?php echo date('Y-m-d', strtotime($r['o_rdate'])); ?> <span class="t_light"><?php echo date('H:i:s', strtotime($r['o_rdate'])); ?></span></td>
				</tr>
				<tr>
					<th>총환불금액</th>
					<td colspan="3" class="only_text">
						<?php
							// 2016-11-30 ::: 환불 비용 계산 ::: JJC ---
							//		return $__cancelTotal = array('pg'=>PG비용 , 'point'=>포인트비용);
							//		reutnr $__console = 타입; // 적립금환불 요청 시

							//	넘길 변수
							//		$opr <== 부분취소 상품의 주문상품 / 주문 / 상품배열정보
							//		$ordernum <== 주분번호
							//		$totalPrice <== 부분취소 상품의 상품가격
							//		$totadlPrice <== 부분취소 상품의  배송비
							//		$totalAprice
							//		$totalDiscount <== 부분취소 상품의 할인비용
							$opr = $r;
							$totalPrice = $r['op_price'] * $r['op_cnt'] ;//총상품가격
							$totadlPrice = $r['op_delivery_price'] + $r['op_add_delivery_price'] ;//총배송비
							$totalDiscount = $r['op_cancel_discount_price'];// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
							include("_cancel.inc_calc.php");// *** 파일생성 ***
							// 2016-11-30 ::: 환불 비용 계산 ::: JJC ---
						?>
						<span class="t_black bold"><?php echo number_format($__cancelTotal['pg']); ?>원</span>(PG환불) +
						<span class="t_black bold"><?php echo number_format($__cancelTotal['point']); ?>원</span>(적립금환불) =
						<span class="t_black bold"><?php echo number_format($__cancelTotal['pg']+$__cancelTotal['point']); ?>원</span>
						<?php if($__refundToBe > $__cancelTotal['pg']+$__cancelTotal['point']) { echo _DescStr('상품금액이 환불가능한 최대 금액을 초과하여 일부만 환불합니다.','orange'); } ?>
					</td>
				</tr>
				<tr>
					<th>결제정보</th>
					<td colspan="3">
						<!-- 내부테이블 -->
						<table>
							<colgroup>
								<col width="130"><col width="*"><col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th>결제수단</th>
									<td>
                                        <?php 
                                            // LCY : 2021-07-04 : 신용카드 간편결제 추가
                                            if( $r['o_easypay_paymethod_type'] != ''){ 
                                                echo $arr_adm_button["E".$arr_available_easypay_pg_list[$r['o_easypay_paymethod_type']]];
                                            }else{
                                                echo $arr_adm_button[$arr_payment_type[$r['o_paymethod']]]; 
                                            }
                                        ?>
									</td>
									<th>결제상태</th>
									<td>
										<?php echo ($r['o_paystatus'] == 'Y' ? $arr_adm_button['결제완료'] : $arr_adm_button['결제대기']); ?>
									</td>
								</tr>
								<tr>
									<th>주문상태</th>
									<td>
										<?php echo str_replace(array('h22','t4'), array('h27', ''), ($r['o_status']?$arr_adm_button[$r['o_status']]:$arr_adm_button['결제실패'])); ?>
									</td>
									<?php if($r['op_cancel'] == 'Y'){?>
										<th>취소일시</th>
										<td>
											<?php echo date('Y년 m월 d일 H시 i분 s초',strtotime($r['op_cancel_cdate'])); ?>
										</td>
									<?php }else{ ?>
										<th>취소요청일시</th>
										<td>
											<?php echo date('Y년 m월 d일 H시 i분 s초',strtotime($r['op_cancel_rdate'])); ?>
										</td>
									<?php } ?>
								</tr>
							</tbody>
						</table>
						<!-- / 내부테이블 -->
					</td>
				</tr>
				<?php
					// 환불요청이 있을 경우
					if(in_array($r['o_paymethod'],$arr_refund_payment_type)) { // SSJ : 주문/결제 통합 패치 : 2021-02-24
				?>
				<tr>
					<th>환불계좌 정보</th>
					<td colspan="3">

						<table>
							<colgroup>
								<col width="180"><col width="*"><col width="180"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th>은행명</th>
									<td colspan="3" class="only_text">
										<?php if($r['op_cancel']=='Y') { ?>
											<strong><?php echo $ksnet_bank[$r['op_cancel_bank']]; ?></strong>
										<?php } else { ?>
											<select name="cancel_bank" class="js_bank_change">
												<option value="">- 은행 선택 -</option>
												<?php foreach($ksnet_bank as $k=>$v) { ?>
												<option value="<?php echo $k; ?>" <?php echo ($k==$r['op_cancel_bank']?'selected':'');?>><?=$v?></option>
												<?php } ?>
											</select>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th>계좌번호</th>
									<td colspan="3" class="only_text">
										<?php if($r['op_cancel']=='Y') { ?>
											<strong><?php echo $r['op_cancel_bank_account'];?></strong>
										<?php } else { ?>
											<input type="text" name="cancel_bank_account" value="<?php echo $r['op_cancel_bank_account']; ?>" class="design js_bank_keyup"/>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th>예금주명</th>
									<td colspan="3" class="only_text">
										<?php if($r['op_cancel']=='Y') { ?>
											<strong><?php echo $r['op_cancel_bank_name']; ?></strong>
										<?php } else { ?>
											<input type="text" name="cancel_bank_name" value="<?php echo $r['op_cancel_bank_name']; ?>" class="design js_bank_keyup"/>
										<?php } ?>
									</td>
								</tr>
							</tbody>
						</table>

					</td>
				</tr>
				<?php
					}
				?>
			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>주문자 정보</strong></div>



	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>회원타입</th>
					<td>
						<?php echo ($r['o_memtype']=='Y' ? '회원' : '비회원'); ?>
					</td>
					<th>주문자 아이디</th>
					<td>
						<?php echo $r['o_mid']; ?>
					</td>
				</tr>
				<tr>
					<th>주문자명</th>
					<td>
						<?php echo $r['o_oname']; ?>
					</td>
					<th>휴대폰번호</th>
					<td>
						<?php echo tel_format($r['o_ohp']); ?>
					</td>
				</tr>
				<tr>
					<th>주문자 이메일 주소</th>
					<td colspan="3">
						<?php echo $r['o_oemail']; ?>
					</td>
				</tr>

				<tr>
					<th>주문자 기기정보</th>
					<td colspan="3">
						<?php echo nl2br($r['device_info']); ?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>받는 분 정보</strong></div>




	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>받는 분 이름</th>
					<td><?php echo $r['o_rname']; ?></td>
					<th>휴대폰번호</th>
					<td>
						<?php echo tel_format($r['o_rhp']); ?>
					</td>
				</tr>
				<tr>
					<th>배송지 주소</th>
					<td>
						<?php echo '[' . $r['o_rpost'] . '] ' . $r['o_raddr1'] . ' ' . $r['o_raddr2']; ?>
					</td>
					<th>도로명 주소</th>
					<td>
						<?php echo '[' . $r['o_rzonecode'] . '] ' . $r['o_raddr_doro'] . ' ' . $r['o_raddr2']; ?>
					</td>
				</tr>
				<tr>
					<th>배송시 유의사항</th>
					<td colspan="3">
						<?php echo nl2br(htmlspecialchars($row['o_content'])); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<script>
	$(document).ready(function(){
		$(".js_bank_change").on("change", function(){
			$(".js_change_val").val($(this).val());
		});
		$(".js_bank_keyup").on("keyup", function(){
			var value = $(this).val();
			var name = $(this).attr("name");
			$(".js_"+name).val(value);
			console.log( $(".js_"+name).val() );
		});
	});
</script>

<?php include_once('wrap.footer.php'); ?>