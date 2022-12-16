<!-- 팝업 / 팝업 사이즈는 컨텐츠 마다 별도 -->
<div class="c_pop" id="product_cancel_pop" style="display:none;width:auto;height:auto;">

	<form name="product_cancel">
	<input type="hidden" name="mode" value="cancel"/><input type="hidden" name="ordernum" value=""/><input type="hidden" name="op_uid" value=""/><input type="hidden" name="cancel_mem_type" value="member"/>

		<!-- 크기에 따라 자동으로 가운데 정렬되도록 -->
		<div class="pop_wrap" style="width:600px; margin-left:-300px; margin-top:-400px">
			<!-- 팝업창 기본타이틀 -->
			<div class="pop_title">
				부분취소/환불신청
				<a href="#none" onclick="return false;" class="btn_close close" title="닫기"></a>
			</div>

			<!-- 팝업창 내용 -->
			<div class="conts_box">
				<div class="inner_box">
					<!-- 여기에 필요한 내용 들어감 -->
					<div class="c_group_tit"><span class="tit">상품정보</span></div>
					<div class="c_cart_list">
						<div class="cart_table">
							<table>
								<colgroup><col width="115"><col width="*"><col width="120"><!-- <col width="85"><col width="80"> -->
								</colgroup>
								<thead>
									<tr>
										<th scope="col">이미지</th>
										<th scope="col">상품 및 옵션 정보</th>
										<th scope="col">금액 / 배송비</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<!-- 이미지 없을때 thumb_box 유지 -->
											<div class="thumb_box">
												<a href="#none" class="product_link" target="_blank"><img class="product_thumb" src="" alt="상품명"></a>
											</div>
										</td>
										<td>
											<!-- 상품정보 -->
											<div class="order_item">
												<!-- 상품명 -->
												<div class="item_name"><a href="#none" class="title product_name product_link" target="_blank"><!-- 상품명 --></a></div>
												<!-- 옵션 ul반복 -->
												<div class="option product_option"><!-- 옵션명 --></div>
											</div>
										</td>
										<!-- 상품금액 -->
										<td class="">
											<div class="t_price product_price">0</div><!-- 상품금액 -->
											<div class="pointbg"><strong class="delivery_price">0</strong>원(배송비)</div><!-- 배송비 --><?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>
											<div class="pointbg discount_price" style="margin-top:10px">0원</div><!-- 할인금액 -->
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<div class="c_group_tit"><span class="tit">환불정보</span></div>
					<div class="c_form">
						<?php if(!$row['o_paycancel_method'] =='D' ){ // 환불 방식이 분배이면 설명 (부분취소 kms 2019-03-20)
							echo '
								<div class="tip_txt">상품 금액에서 할인금액이 상품 금액 비율로 분배되어 환불 됩니다</div>
								<div class="tip_txt">할인금액이 있으면 상품 금액에서 제외되고 환불됩니다</div>
								<div class="tip_txt">적립금 사용 내역은 마이페이지에 적립금 탭에서 확인하실 수 있습니다.</div>
								';
						}else {
							echo '
								<div class="tip_txt">마지막 상품을 취소할 때 상품 금액에서 할인금액이 제외되고 환불됩니다.</div>
								<div class="tip_txt">할인금액이 있으면 상품 금액에서 제외되고 환불됩니다.</div>
								<div class="tip_txt">적립금 사용 내역은 마이페이지에 적립금 탭에서 확인하실 수 있습니다.</div>
								';
						}
						?>
						<table style="margin-top:5px;">
							<colgroup>
								<col width="150"><col width="*"><col width="150"><col width="*">
							</colgroup>
							<tbody>

								<tr style="border-top:1px solid #ddd">
									<th class="ess"><span class="tit ">환불금액</span></th>
									<td colspan="3">
										<?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>
										<strong class="return_price">0</strong>원 <br /><br />
										직접 환불 가능액 : <span class="cancel_return_price">0</span>원(배송비 포함)</span><br />
										적립금 환불 가능액 : <span class="cancel_return_point">0</span>원</span><br />
										환불불가 할인액 : <span class="cancel_return_discount">0</span>원</span>
										<?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>
									</td>
								</tr>

								<tr>
									<th class="ess"><span class="tit ">환불수단</span></th>
									<td colspan="3">
										<?php if(in_array($siteInfo['s_pg_type'],array_keys($arr_pg_type))) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
											<?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
												<label class="label_design"><input type="radio" name="cancel_type" class="cancel_type_pg" value="pg"><span class="txt">직접 환불</span></label>
											<?php }else{ ?>
												<label class="label_design"><input type="radio" name="cancel_type" class="cancel_type_pg" value="pg"><span class="txt">PG사 결제 취소</span></label>
											<?php } ?>
										<?php } ?>
										<?php if(is_login()){ // SSJ : 비회원 주문 취소 요청 시 적립금 환불 막기 : 2021-06-04 ?>
											<label class="label_design"><input type="radio" name="cancel_type" class="cancel_type_point" value="point"><span class="txt">적립금 환불</span></label>
										<?php } ?>
									</td>
								</tr>
								<?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
								<tr class="view_pg" style="display:none;">
									<th class="ess"><span class="tit ">환불계좌</span></th>
									<td colspan="3">
										<span class="input_box" style="width:375px;">
											<input type="text" name="cancel_bank_name" class="input_design" value="<?php echo $mem_info['in_cancel_bank_name']; ?>"  placeholder="예금주" style="width:175px;"/>
											<select name="cancel_bank" class="select_design" style="width:170px; margin-left:5px;">
												<?php foreach($ksnet_bank as $kk => $vv) { ?><option value="<?php echo $kk; ?>" <?php echo ($mem_info['in_cancel_bank']==$kk?' selected ':''); ?>><?php echo $vv; ?></option><?php } ?>
											</select>
										</span>
										<div class="input_box">
											<input type="text" name="cancel_bank_account" class="input_design" value="<?php echo $mem_info['in_cancel_bank_account']; ?>" placeholder="계좌번호" style="width:350px">
										</div>
										<label class="label_design"><input type="checkbox" name="save_myinfo" value="Y"><span class="txt">나의 정보에 함께 저장하기</span></label>
									</td>
								</tr>
								<? } ?>
								<tr>
									<th class=""><span class="tit ">전달내용</span></th>
									<td colspan="3">
										<div class="textarea_box"><textarea name="cancel_msg" rows="3" style="" class="textarea_design" placeholder="관리자에게 전달하실 내용이 있다면 입력해주세요."></textarea></div>
										<div class="tip_txt">위 정보를 다시한번 정확하게 확인 후 신청해주시면, 관리자 확인 후 처리됩니다.</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>


			<!-- 버튼 컬러 변경 불가 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h30 black"><input type="submit" value="취소신청"></span></li>
					<li><a href="#none" onclick="return false;" class="c_btn h30 light line close">닫기</a></li>
				</ul>
			</div>
		</div>

	</form>

	<!-- 팝업창 배경 -->
	<div class="bg"></div>
</div>









<!-- 팝업 / 팝업 사이즈는 컨텐츠 마다 별도 -->
<div class="c_pop" id="product_cancel_view_pop" style="display:none;width:auto;height:auto;">

	<!-- 크기에 따라 자동으로 가운데 정렬되도록 -->
	<div class="pop_wrap" style="width:600px; margin-left:-300px; margin-top:-400px">
		<!-- 팝업창 기본타이틀 -->
		<div class="pop_title">
			부분취소/환불 신청 내역
			<a href="#none" onclick="return false;" class="btn_close close" title="닫기"></a>
		</div>

		<!-- 설명글 -->
		<div class="pop_guide"><span class="cancel_date"></span>에 부분취소 요청하신 내역입니다.</div>

		<!-- 팝업창 내용 -->
		<div class="conts_box">
			<div class="inner_box">
				<!-- 여기에 필요한 내용 들어감 -->
				<div class="c_group_tit"><span class="tit">부분취소 신청하신 상품정보</span></div>
				<div class="c_cart_list">
					<div class="cart_table">
						<table>
							<colgroup><col width="115"><col width="*"><col width="120"><!-- <col width="85"><col width="80"> -->
							</colgroup>
							<thead>
								<tr>
									<th scope="col">이미지</th>
									<th scope="col">상품 및 옵션 정보</th>
									<th scope="col">금액 / 배송비</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<!-- 이미지 없을때 thumb_box 유지 -->
										<div class="thumb_box">
											<a href="#none" class="product_link" target="_blank"><img class="product_thumb" src="" alt="상품명"></a>
										</div>
									</td>
									<td>
										<!-- 상품정보 -->
										<div class="order_item">
											<!-- 상품명 -->
											<div class="item_name"><a href="#none" class="title product_name product_link" target="_blank"><!-- 상품명 --></a></div>
											<!-- 옵션 ul반복 -->
											<div class="option product_option"><!-- 옵션명 --></div>
										</div>
									</td>
									<!-- 상품금액 -->
									<td class="">
										<div class="t_price product_price">0</div><!-- 상품금액 -->
										<div class="pointbg"><strong class="delivery_price">0</strong>원(배송비)</div><!-- 배송비 --><?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>
										<div class="pointbg discount_price" style="margin-top:10px">0원</div><!-- 할인금액 -->
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="c_group_tit"><span class="tit">환불정보</span></div>
				<div class="c_form">
					<table>
						<colgroup>
							<col width="150"><col width="*"><col width="150"><col width="*">
						</colgroup>
						<tbody>

							<tr style="border-top:1px solid #ddd">
								<th class="ess"><span class="tit ">환불금액</span></th>
								<td colspan="3">
									<?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>
									<strong class="return_price">0</strong>원 <br /><br />
									직접 환불 가능액 : <span class="cancel_return_price">0</span>원(배송비 포함)</span><br />
									적립금 환불 가능액 : <span class="cancel_return_point">0</span>원</span><br />
									환불불가 할인액 : <span class="cancel_return_discount">0</span>원</span>
									<?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>
								</td>
							</tr>

							<tr>
								<th class="ess"><span class="tit ">환불수단</span></th>
								<td colspan="3">
									<?php if(in_array($siteInfo['s_pg_type'],array_keys($arr_pg_type))) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
										<?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
											<span class="cancel_type_val cancel_type_pg" style="display:none;">직접 환불</span>
										<?php }else{ ?>
											<span class="cancel_type_val cancel_type_pg" style="display:none;">PG사 결제 취소</span>
										<?php } ?>
									<?php } ?>
									<?php if(is_login()){ // SSJ : 비회원 주문 취소 요청 시 적립금 환불 막기 : 2021-06-04 ?>
										<span class="cancel_type_val cancel_type_point">적립금 환불</span>
									<?php } ?>
								</td>
							</tr>
							<?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
							<tr class="cancel_bank_wrap" style="display:none;">
								<th class="ess"><span class="tit ">환불계좌</span></th>
								<td colspan="3">
									<span class="cancel_bank"></span>
									<span class="cancel_bank_account"></span>
									<span class="cancel_bank_name"></span>
								</td>
							</tr>
							<? } ?>
							<tr>
								<th class=""><span class="tit ">전달내용</span></th>
								<td colspan="3">
									<span class="cancel_msg" style="white-space: pre-wrap;"></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>


		<!-- 버튼 컬러 변경 불가 -->
		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h30 light line close">닫기</a></li>
			</ul>
		</div>
	</div>


	<!-- 팝업창 배경 -->
	<div class="bg"></div>
</div>




<script>
$(document).ready(function(){
	$('input[name=cancel_type]').on('change',function(){
		var type = $(this).val();
		if( type=='pg' ) { $('.view_pg').show(); } else { $('.view_pg').hide(); }
	});
	$('.product_cancel').on('click',function(){

		<?php
			// ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치  --
			if( $row['npay_order'] == 'Y'){
				echo 'alert("네이버페이 주문취소는 고객센터에 문의해 주세요."); return false;';
			}
		?>

		var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_pop'), $product_form = $('form[name=product_cancel]');
		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'product'},
			type: 'POST', dataType: 'JSON', cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_name').html(data['data']['name']);
					$product_pop.find('.product_link').attr('href', '/?pn=product.view&pcode='+ data['data']['pcode']);
					$product_pop.find('.product_price').text(data['data']['price']+'원');//상품금액
					$product_pop.find('.delivery_price').text(data['data']['delivery']);//배송비용

					// --- JJC : 부분취소 개선 : 2021-02-10  ---
					$product_pop.find('.cancel_return_price').text(data['data']['return_price']);//직접 환불 가능액
					$product_pop.find('.cancel_return_point').text(data['data']['return_point']);//적립금 환불 가능액
					$product_pop.find('.cancel_return_discount').text(data['data']['return_discount']);//할인금액
					// --- JJC : 부분취소 개선 : 2021-02-10  ---

					//할인비용 // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					if(parseInt(data['data']['discount']) > 0){
						$product_pop.find('.discount_price').html('<strong>' + data['data']['discount'] + '</strong>원<br>(할인금액)');
					}else{
						$product_pop.find('.discount_price').html('');
					}
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액

					if(data['data']['option']) {
						$product_pop.find('.product_option').html('<ul><li><div class="opt_tit"><span class="icon">필수</span>'+ data['data']['option'] +'</div></li></ul>');
						if(data['data']['addoption']) {
							$product_pop.find('.product_option').append('<ul><li><div class="opt_tit"><span class="icon add">추가</span>'+data['data']['addoption'] +'</div></li></ul>');
						}
					} else { $product_pop.find('.product_option').html('<ul><li><div class="opt_tit opt_none">옵션없음</div></li></ul>'); }
					$product_form.find('input[name=ordernum]').val(ordernum);
					$product_form.find('input[name=op_uid]').val(op_uid);
					if(data['data']['pg_check']=='N') {
						$('input[name=cancel_type].cancel_type_pg').parent().hide();
						$('input[name=cancel_type].cancel_type_pg').prop('disabled',true);
						$('input[name=cancel_type].cancel_type_point').prop('checked',true).trigger('change');
					}
					$('#product_cancel_pop').lightbox_me({
						centered: true, closeEsc: false, overlaySpeed: 0, lightboxSpeed: 0,
						overlayCSS:{background:'#000', opacity: 0.7},
						onLoad: function() { },
						onClose: function(){
							$product_form.find('input[name=ordernum]').val('');
							$product_form.find('input[name=op_uid]').val('');
						}
					});
				}
				else {alert(data['result_text']);}
			},
			error:function(request,status,error){alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);}
		});
	});

	$('form[name=product_cancel]').on('submit',function(e){ e.preventDefault();

		<?// 2016-11-30 ::: 사전체크 ::: JJC ?>
		var app_cancel_type = $("form[name=product_cancel] input[name=cancel_type]").filter(function() {if (this.checked) return this;}).val(); // 선택한 환불수단
		app_cancel_type = app_cancel_type == undefined ? '' : app_cancel_type;// - undefined 초기화
		if( app_cancel_type == '' ){ alert('환불수단을 선택해주시기 바랍니다.'); return false; }

		<? if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
			if( $('form[name=product_cancel] input[name=cancel_bank_name]').val() == '' && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' )){ alert('예금주를 입력해주시기 바랍니다.'); return false; }
			if( $('form[name=product_cancel]  select[name=cancel_bank]').val() == ''  && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' ) ){ alert('은행을 선택해주시기 바랍니다.'); return false; }
			if( $('form[name=product_cancel]  input[name=cancel_bank_account]').val() == ''  && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' ) ){ alert('계좌번호를 입력해주시기 바랍니다.'); return false; }
		<? } ?>
		<?// 2016-11-30 ::: 사전체크 ::: JJC ?>

		// --- JJC : 부분취소 개선 : 2021-02-10  ---
		if(confirm("정말 주문을 취소하시겠습니까?")===true) {
			var data = $(this).serialize();
			$.ajax({
				data: data, type: 'POST', dataType: 'JSON', cache: false,
				url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
				success: function(data) {
					if(data['result']=='OK'){alert('부분취소/환불을 신청하셨습니다.\n\n신청내용을 확인한 후 빠르게 처리하도록 하겠습니다.'); location.reload(); return false;}
					else {alert(data['result_text']);}
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
		// --- JJC : 부분취소 개선 : 2021-02-10  ---
	});
});
</script>
<script>
$(document).ready(function(){
	$('.product_cancel_view').on('click',function(){
		var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_view_pop');
		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'view'},
			type: 'POST', dataType: 'JSON', cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_name').html(data['data']['name']);
					$product_pop.find('.product_price').text(data['data']['price']);//상품금액
					$product_pop.find('.delivery_price').text(data['data']['delivery']);//배송비용

					// --- JJC : 부분취소 개선 : 2021-02-10  ---
					$product_pop.find('.cancel_return_price').text(data['data']['return_price']);//직접 환불 가능액
					$product_pop.find('.cancel_return_point').text(data['data']['return_point']);//적립금 환불 가능액
					$product_pop.find('.cancel_return_discount').text(data['data']['return_discount']);//할인금액
					// --- JJC : 부분취소 개선 : 2021-02-10  ---

					//할인비용 // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					if(parseInt(data['data']['discount']) > 0){
						$product_pop.find('.discount_price').html('<strong>' + data['data']['discount'] + '</strong>원<br>(할인금액)');
					}else{
						$product_pop.find('.discount_price').html('');
					}
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액
					if(data['data']['option']) {
						$product_pop.find('.product_option').html('<ul><li><div class="opt_tit"><span class="icon">필수</span>'+ data['data']['option'] +'</div></li></ul>');
						if(data['data']['addoption']) {
							$product_pop.find('.product_option').append('<ul><li><div class="opt_tit"><span class="icon add">추가</span>'+data['data']['addoption'] +'</div></li></ul>');
						}
					} else { $product_pop.find('.product_option').html('<ul><li><div class="opt_tit opt_none">옵션없음</div></li></ul>'); }
					$product_pop.find('.cancel_date').text(data['data']['date']);
					$product_pop.find('.cancel_bank').text('[' + data['data']['bank'] + ']');
					$product_pop.find('.cancel_bank_account').text(data['data']['bank_account']);
					$product_pop.find('.cancel_bank_name').text(data['data']['bank_name']);
					$product_pop.find('.cancel_msg').text(data['data']['msg']);
					if(data['data']['msg'] == '') $product_pop.find('.cancel_msg').closest('tr').hide();
					else $product_pop.find('.cancel_msg').closest('tr').show();
					$product_pop.find('.cancel_type_val').hide();
					$product_pop.find('.cancel_type_val.cancel_type_'+data['data']['cancel_type']).show();
					if(data['data']['cancel_type']!='pg') {
						$product_pop.find('.cancel_bank_wrap').hide();
					}else{ $product_pop.find('.cancel_bank_wrap').show(); }
					$('#product_cancel_view_pop').lightbox_me({
						centered: true, closeEsc: false,
						onLoad: function() { },
						onClose: function(){ }
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

});
</script>
<!-- / 부분취소신청 -->