<?php
	if($_REQUEST['view'] == 'order_complain') {
		$app_current_link = '_order_complain.list.php';
		$app_current_link_list = '_order_complain.list.php';
	}
	else if($_REQUEST['view'] == 'cancel') {
		$app_current_link = '_cancel.list.php';
		$app_current_link_list = '_cancel.list.php';
	}
	else{
		$app_current_link = '_order_product.list.php';
		$app_current_link_list = '_order_product.list.php';
	}


	include_once('wrap.header.php');


	if( $_mode == "modify" ) {
		// 주문정보 추출
		$que = " select * from smart_order where o_ordernum='{$_ordernum}' ";
		$row = _MQ($que);

		// 주문상품 정보 추출
		$arr_product = array();
		$sres = _MQ_assoc("
			select op.* , p.p_name, p.p_img_list , p.p_img_list_square, p.p_code
			from smart_order_product as op
			left join smart_product as p on (p.p_code=op.op_pcode)
			where op_oordernum='{$_ordernum}' and op.op_partnerCode = '{$com_id}'
			order by op.op_uid
		");
		# 주문상품 추출
		$arr_pinfo = array(); // 주문상품, 옵션 정보
		$arr_status = array(); // 주문상품 진행상태 체크
		$arr_sendnum = array(); // 배송정보 체크
		foreach($sres as $sk=>$sv){
			// 상품코드
			$arr_pinfo[$sv['op_pcode']]['code'] = $sv['op_pcode'];
			// 상품명
			$arr_pinfo[$sv['op_pcode']]['name'] = stripslashes($sv['op_pname']);
			// 이미지 체크
			$_p_img = get_img_src('thumbs_s_'.$sv['p_img_list_square']);
			if($_p_img == '') $_p_img = OD_ADMIN_DIR.'/images/thumb_no.jpg';
			$arr_pinfo[$sv['op_pcode']]['img'] = $_p_img;

			// 부분취소 상태 체크 -- 결제전에는 상태없음
			$app_cancel_btn = '';
			if($row['o_canceled']=='N' && $row['o_paystatus']=='Y' && $sv['op_is_addoption'] == 'N'){
				if($sv['op_cancel'] == 'Y'){
					$app_cancel_btn = '<span class="option_btn"><span class="c_btn h22 gray line t4">취소완료</span></span>';
				}else if($sv['op_cancel'] == 'R'){
					$app_cancel_btn = '<span class="option_btn"><span class="c_btn h22 gray line t5">취소요청중</span></span>';
				}else if($sv['op_cancel'] == 'N'){
					if($sv['op_complain']){
						$app_cancel_btn = '<span class="option_btn"><span class="c_btn h22 gray line t6">'.$arr_massage_conv[$sv['op_complain']].'</span></span>';
					}
				}
			}


			// 2017-06-20 ::: 부가세율설정 ::: JJC
			$app_vat_str = ( ($siteInfo['s_vat_product'] == 'C' && $sv['op_vat'] == 'N') ? ' <span class="t_blue">(면세)</span>' : '');
			// 2017-06-20 ::: 부가세율설정 ::: JJC

			if($sv['op_pouid']){ // 옵션있음
				$arr_pinfo[$sv['op_pcode']]['has_option'] = 'Y';
				$arr_pinfo[$sv['op_pcode']]['option'][$sk] = array(
																			'op_uid'=>$sv['op_uid']
																			,'name'=>implode(' ', array_filter(array($sv['op_option1'],$sv['op_option2'],$sv['op_option3'])))
																			,'price'=>$sv['op_price']
																			,'cnt'=>$sv['op_cnt']
																			,'is_addoption'=>$sv['op_is_addoption']
																			,'app_cancel_btn'=>$app_cancel_btn
																			,'app_vat_str'=>$app_vat_str
																		);
			}else{ // 옵션없음
				$arr_pinfo[$sv['op_pcode']]['op_uid'] = $sv['op_uid'];
				$arr_pinfo[$sv['op_pcode']]['has_option'] = 'N';
				$arr_pinfo[$sv['op_pcode']]['price'] = $sv['op_price'];
				$arr_pinfo[$sv['op_pcode']]['app_cancel_btn'] = $app_cancel_btn;
				$arr_pinfo[$sv['op_pcode']]['app_vat_str'] = $app_vat_str;
			}

			$arr_pinfo[$sv['op_pcode']]['cnt'] += $sv['op_cnt'];
			$arr_pinfo[$sv['op_pcode']]['tprice'] += ($sv['op_cnt'] * $sv['op_price']);
			$arr_pinfo[$sv['op_pcode']]['point'] += $sv['op_point'];
			$arr_pinfo[$sv['op_pcode']]['delivery_type'] = $sv['op_delivery_type'];
			$arr_pinfo[$sv['op_pcode']]['delivery_price'] += $sv['op_delivery_price'];
			$arr_pinfo[$sv['op_pcode']]['add_delivery_price'] += $sv['op_add_delivery_price'];


			// 주문상품의 진행상태
			$arr_status[$sv['op_pcode']]['total']++;
			if($row['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
				$arr_status[$sv['op_pcode']]['cancel']++;
			}else if($row['o_status'] == '결제실패'){ // 결제실패일경우
				$arr_status[$sv['op_pcode']]['fail']++;
			}else{
				if($row['o_paystatus'] =='Y'){ // 주문결제를 했다면,
					if($sv['op_sendstatus'] == '배송대기') {
						$arr_status[$sv['op_pcode']]['pay']++;
					}else if($sv['op_sendstatus'] == '배송중'){
						$arr_status[$sv['op_pcode']]['delivery']++;
					}else if($sv['op_sendstatus'] == '배송완료'){
						$arr_status[$sv['op_pcode']]['complete']++;
					}else{
						$arr_status[$sv['op_pcode']]['cancel']++;
					}
				}else{ // 주문결제를 하지 않았다면
					$arr_status[$sv['op_pcode']]['ready']++;
				}
			}

			# 배송조회
			if(in_array($sv['op_sendstatus'], array('배송중','배송완료')) && $sv['op_sendcompany'] && $sv['op_sendnum']){
				if($arr_sendnum[$sv['op_sendnum']] > 0) continue; // 중복제거
				$arr_sendnum[$sv['op_sendnum']]++;
				$arr_pinfo[$sv['op_pcode']]['delivery_print'][] = '
					<div class="lineup-vertical">
						<span class="bold">'. $sv['op_sendcompany'] .'</span>
						<span class="block">'. $sv['op_sendnum'] .'</span>
						<a href="'. ($row['npay_order'] == 'Y' ? $NPayCourier[$sv['op_sendcompany']] : $arr_delivery_company[$sv['op_sendcompany']]) . rm_str($sv['op_sendnum']) .'" class="c_btn h22 green line h22 t4" target="_blank">배송조회</a>
					</div>
				';
			}

		}

		// 주문상품 진행상태 체크
		foreach($arr_status as $sk=>$sv){
			# 진행상태
			$op_status_icon = '';
			if($row['o_canceled'] == 'Y'){ // 주문자체가 취소 되었으면 주문취소 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패]
				$arr_pinfo[$sk]['status'] = '주문취소';
			}
			else if($sv['fail']>0){ // 결제실패가 하나라도 있으면 결제실패상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제실패]
				$arr_pinfo[$sk]['status'] = '결제실패';
			}
			else if($sv['ready']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소] - [결제대기]
				$arr_pinfo[$sk]['status'] = '결제대기';
			}
			else if($sv['delivery']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
				$arr_pinfo[$sk]['status'] = '배송중';
			}
			else if($sv['pay']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
				//$arr_pinfo[$sk]['status'] = '결제완료';
				$arr_pinfo[$sk]['status'] = '배송대기'; //=> 상세페이지에서는 배송대기로 표현
			}
			else if($sv['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
				$arr_pinfo[$sk]['status'] = '배송완료';
			}else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
				$arr_pinfo[$sk]['status'] = '주문취소';
			}
		}

		// 현금영수증용 상품명 생성
		$cash_product_name = (count($sres)>0)?$sres[0][p_name].'외 '.(count($sres)-1).'개':$sres[0][p_name];

	}else{ error_msg('잘못된 접근입니다.'); }



?>
<!-- ● 단락타이틀 -->
<div class="group_title"><strong>상품정보</strong></div>
<!-- ● 데이터 리스트 -->
<div class="data_list">
	<table class="table_list">
		<colgroup>
			<col width="100"><col width="*"><col width="60"><col width="100"><col width="100"><col width="60"><col width="120">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">이미지</th>
				<th scope="col">상품정보</th>
				<th scope="col">수량</th>
				<th scope="col">주문금액</th>
				<th scope="col">배송비</th>
				<th scope="col">배송상태</th>
				<th scope="col">배송정보</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $arr_pinfo as $k=>$v ){
			?>
					<tr>
						<td class="img80"><img src="<?php echo $v['img']; ?>" alt="<?php echo addslashes($v['name']); ?>"></td>
						<td>
							<!-- 상품정보 -->
							<div class="order_item">
								<!-- 상품명 -->
								<div class="title bold"><?php echo $v['name']; ?></div>
								<?php if($v['has_option']=='Y' && count($v['option'])>0){ ?>
									<!-- 옵션명, div반복 -->
									<?php foreach($v['option'] as $sk=>$sv){ ?>
										<div class="option bullet">
											<span class="option_name"><?php echo ($sv['is_addoption']=='Y'?'추가 : ':'선택 : '); ?><?php echo $sv['name']; ?></span>
											<span class="option_price"><?php echo number_format($sv['price']); ?>원 x <?php echo number_format($sv['cnt']); ?>개</span>
											<?php echo $sv['app_cancel_btn']; ?>
										</div>
									<?php } ?>
								<?php }else{ ?>
									<div class="title normal"><?php echo number_format($v['price']); ?>원 x <?php echo number_format($v['cnt']); ?>개</div>
									<?php echo $v['app_cancel_btn']; ?>
								<?php } ?>
							</div>
						</td>
						<td class="t_black"><?php echo number_format($v['cnt']); ?></td>
						<td class="t_black bold"><?php echo number_format($v['tprice']); ?>원</td>
						<td class="t_black bold">
							<?php echo number_format($v['delivery_price']); ?>원
							<?php if($v['delivery_type']<>'입점'){ ?>
								<br>(<?php echo $v['delivery_type']; ?>배송)
							<?php } ?>

							<?php if($v['add_delivery_price']>0){ ?>
								<div class="normal" style="margin-top:5px;">+<?php echo number_format($v['add_delivery_price']); ?>원<br>(추가배송비)</div>
							<?php } ?>
						</td>
						<td>
							<div class="lineup-vertical">
								<?php echo $arr_adm_button[$v['status']]; ?>
							</div>
						</td>
						<td>
							<?php
								if(count($v['delivery_print'])>0) echo implode('<br>', $v['delivery_print']);
							?>
						</td>
					</tr>
			<?php
					//상품수 , 포인트 , 상품금액
					$arr_product['cnt'] += $v['cnt'];//상품수
					$arr_product['point'] += $v['point'];//포인트
					$arr_product['sum'] += $v['tprice'];//상품금액
					$arr_product['add_delivery'] += $v['delivery_price'] + $v['add_delivery_price'];//개별배송비 포함
				}
			?>
		</tbody>
	</table>

		<!-- 결제금액정보 -->
		<div class="total_price">
			<div>
				<ul>
					<li>주문상품 수 : <strong><?php echo number_format($arr_product['cnt']); ?></strong><em>개</em></li>
					<li>배송비 : <strong><?php echo number_format($arr_product['add_delivery']); ?></strong><em>원</em></li>
					<li>주문총액 : <strong><?php echo number_format($arr_product['sum']); ?></strong><em>원</em></li>
					<li>결제금액 : <strong><?php echo number_format($arr_product['sum'] + $arr_product['add_delivery']); ?></strong><em>원</em></li>
				</ul>
			</div>
		</div>
</div>




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
				<td class="only_text"><?php echo $row['o_ordernum']; ?></td>
				<th>주문일시</th>
				<td class="only_text"><?php echo date('Y-m-d', strtotime($row['o_rdate'])); ?> <span class="t_light"><?php echo date('H:i:s', strtotime($row['o_rdate'])); ?></span></td>
			</tr>
			<?php
				// 환불요청이 있을 경우
				if( $row['o_moneyback_status'] <> 'none' ){
			?>
			<tr>
				<th>환불요청정보</th>
				<td colspan="3">

					<table>
						<colgroup>
							<col width="180"><col width="*"><col width="180"><col width="*">
						</colgroup>
						<tbody>
							<tr>
								<th>환불처리상태</th>
								<td colspan="3" class="only_text">
									<?php echo $row['o_moneyback_status'] == "complete" ? "환불완료" : "환불신청중"; ?>
								</td>
							</tr>
							<tr>
								<th>환불계좌</th>
								<td colspan="3" class="only_text">
									<?php echo $row['o_moneyback_comment']; ?>
								</td>
							</tr>
							<tr>
								<th>환불요청시간</th>
								<td colspan="3" class="only_text">
									<?php echo $row['o_moneyback_date']; ?>
								</td>
							</tr>
							<?php
								// 환불요청이 완료된 경우
								if( $row['o_moneyback_status'] == 'complete'){
							?>
							<tr>
								<th>환불처리시간</th>
								<td colspan="3" class="only_text">
									<?php echo $row['o_moneyback_comdate']; ?>
								</td>
							</tr>
							<?php } ?>
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
					<?=_InputRadio( "_memtype" , array('Y','N'), $row['o_memtype'] , ' disabled' , array('회원','비회원') , '') ?>
				</td>
				<th>주문자 아이디</th>
				<td>
					<input type="text" name="_mid" value="<?=$row[o_mid]?>" class="design" style="width:185px" disabled>
				</td>
			</tr>
			<tr>
				<th>주문자명</th>
				<td>
					<input type="text" name="_oname" value="<?=$row[o_oname]?>" class="design" style="width:100px" disabled>
				</td>
				<th>휴대폰번호</th>
				<td>
					<input type="text" name="_ohp" value="<?php echo tel_format($row['o_ohp']); ?>" class="design t_center" style="width:110px" disabled>
				</td>
			</tr>
			<tr>
				<th>주문자 이메일 주소</th>
				<td colspan="3">
					<input type="text" name="_oemail" value="<?php echo $row['o_oemail']; ?>" class="design" style="width:185px" disabled>
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
				<td><input type="text" name="_rname" value="<?php echo $row['o_rname']; ?>" class="design" style="width:100px" disabled></td>
				<th>휴대폰번호</th>
				<td>
					<input type="text" name="_rhp" value="<?php echo tel_format($row['o_rhp']); ?>" class="design t_center" style="width:110px" disabled>
				</td>
			</tr>
			<tr>
				<th>배송지 주소</th>
				<td>
					<?php
						// 배송지 우편번호
						$arr_post = explode('-', $row['o_rpost']);
					?>
					<input type="text" name="_rpost1" value="<?php echo $arr_post[0]; ?>" class="design t_center" style="width:50px" disabled>
					<span class="fr_tx">-</span>
					<input type="text" name="_rpost2" value="<?php echo $arr_post[1]; ?>" class="design t_center" style="width:50px" disabled>
					<a href="" class="c_btn h28 black">우편번호 찾기</a>
					<div class="lineup-full">
						<input type="text" name="_raddr1" class="design" style="" value="<?php echo $row['o_raddr1']; ?>" disabled>
						<input type="text" name="_raddr2" class="design" style="" value="<?php echo $row['o_raddr2']; ?>" disabled>
					</div>
				</td>
				<th>도로명 주소</th>
				<td>
					<input type="text" name="_rzonecode" value="<?php echo $row['o_rzonecode']; ?>" class="design t_center" style="width:70px" disabled>
					<div class="lineup-full">
						<input type="text" name="_raddr_doro" value="<?php echo $row['o_raddr_doro']; ?>" class="design" disabled>
					</div>
				</td>
			</tr>
			<tr>
				<th>배송시 유의사항</th>
				<td colspan="3">
					<textarea name="_content" rows="4" cols="" class="design" disabled><?php echo $row['o_content']; ?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</div>


<!-- 상세페이지 버튼 -->
<?php echo _submitBTN($app_current_link_list, null, '', true, true); ?>




<script>
$(document).ready(function(){

	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------
	$('input[name=cancel_type]').on('change',function(){
		var type = $(this).val();
		if( type=='pg' ) { $('.view_pg').show(); } else { $('.view_pg').hide(); }
	});
	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------

	$('.product_cancel').on('click',function(){
		var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_pop'), $product_form = $('form[name=product_cancel]');

		console.log({'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'product'});
		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'product'},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
			dataType: 'JSON',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_name').text(data['data']['name']);

					// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					$product_pop.find('.product_price').text(data['data']['price']);//상품금액
					$product_pop.find('.delivery_price').text(data['data']['delivery']);//배송비용
					$product_pop.find('.discount_price').text(data['data']['discount']);//할인비용
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액
					// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC

					if(data['data']['option']) {
						$product_pop.find('.product_option').text('옵션: ' + data['data']['option']);
						if(data['data']['addoption']) {
							$product_pop.find('.product_option').append('<br/>추가옵션: '+data['data']['addoption']);
						}
					} else { $product_pop.find('.product_option').text(''); }
					$product_form.find('input[name=ordernum]').val(ordernum);
					$product_form.find('input[name=op_uid]').val(op_uid);
					if(data['data']['pg_check']=='N') {
						$('input[name=cancel_type].cancel_type_pg').parent().hide();
						$('input[name=cancel_type].cancel_type_pg').prop('disabled',true);
						$('input[name=cancel_type].cancel_type_point').prop('checked',true).trigger('change');
					}
					$('#product_cancel_pop').lightbox_me({
						centered: true, closeEsc: false,
						onLoad: function() { },
						onClose: function(){
							$product_form.find('input[name=ordernum]').val('');
							$product_form.find('input[name=op_uid]').val('');
						}
					});
				} else {
					alert(data['result_text']);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	});
	$('form[name=product_cancel]').on('submit',function(e){ e.preventDefault();
		if(confirm("정말 주문을 취소하시겠습니까?")===true) {
			var data = $(this).serialize();
			$.ajax({
				data: data,
				type: 'POST',
				cache: false,
				url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
				success: function(data) {
					if(data=='OK') {
						alert('성공적으로 취소요청 되었습니다.'); location.reload(); return false;
					} else {
						alert(data);
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
<!-- / 부분취소신청 -->


<?php include_once('wrap.footer.php'); ?>