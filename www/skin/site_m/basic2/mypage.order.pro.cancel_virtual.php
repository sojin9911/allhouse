<?php
    $bank_name_array = array(
            '04'=>'국민',
            '03'=>'기업',
            '11'=>'농협',
            '88'=>'신한',
            '05'=>'외환',
            '20'=>'우리',
            '81'=>'하나',
            '53'=>'씨티',
            '23'=>'SC은행',
            '02'=>'산업',
            '45'=>'새마을금고',
            '31'=>'대구',
            '32'=>'부산',
			'39'=>'경남',
            '34'=>'광주',
            '37'=>'전북',
            '35'=>'제주',
			'07'=>'수협',
            '48'=>'신협',
            '71'=>'우체국',
            '27'=>'한국씨티',
            '09'=>'동양증권',
            '78'=>'신한금융투자증권',
            '40'=>'삼성증권',
            '30'=>'미래에셋증권',
            '43'=>'한국투자증권',
            '69'=>'한화증권'
        );
?>
<div class="c_pop cancel_virtual" style="display:none;height:auto;">

	<form name="cancel_frm" method="post" action="<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php" autocomplete="off" style="margin:0;padding:0;" target="">
	<input type="hidden" name="_mode" value="refund"/>
	<input type="hidden" name="ordernum" value=""/>
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>"/>

		<div class="wrapping">
			<div class="inner">
				<div class="box">
					<div class="tit_box">
						<div class="tit">주문취소 신청</div>
						<a href="#none" class="btn_close close" title="닫기"></a>
					</div>
					<div class="conts_box c_order">

						<div class="c_group_tit"><span class="tit">환불 정보</span></div>
						<div class="c_form">
							<table>
								<tbody>
									<tr>
										<th class="ess"><span class="tit ">주문번호</span></th>
										<td>
											<span class="js_data_ordernum"></span>
										</td>
									</tr>
									<tr>
										<th class="ess"><span class="tit ">환불받을 금액</span></th>
										<td>
											<span class="js_data_price"></span>원
										</td>
									</tr>
										<th class=""><span class="tit ">환불계좌</span></th>
										<td>
											<div class="input_box">
												<input type="text" name="refund_nm" class="input_design" value=""  placeholder="예금주" />
											</div>

											<div class="select">
												<select name="bank_code">
													<?php foreach($bank_name_array as $k => $v) { ?><option value="<?php echo $v; ?>"><?php echo $v; ?></option><?php } ?>
												</select>
											</div>

											<div class="input_box">
												<input type="text" name="refund_account" class="input_design" value="" placeholder="계좌번호" >
											</div>
											<div class="tip_txt">취소신청하실 주문을 다시 확인하시고, 다음정보를 입력해주시면 관리자의 확인 후 처리됩니다.</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

					</div>


					<div class="c_btnbox">
						<ul>
							<li><span class="c_btn h40 dark"><input type="submit" name="" value="취소신청"></span></li>
							<li><a href="#none" class="c_btn h40 line close">닫기</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

	</form>

</div>



<script>
// 폼 유효성 검사
$(document).ready(function(){
	$("form[name=cancel_frm]").validate({
			ignore: ".ignore",
			rules: {
					ordernum: { required: true }
					,refund_nm: { required: false }
					,bank_code: { required: false }
					,refund_account: { required: false }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

			},
			messages: {
					ordernum: { required: '취소할 주문이 선택되지 않았습니다.\n\n새로고침(F5) 후 다시 시도해 주시기 바랍니다.' }
					,refund_nm: { required: '예금주를 입력해주시기 바랍니다.' }
					,bank_code: { required: '입금은행을 선택해주시기 바랍니다.' }
					,refund_account: { required: '환불받을 계좌번호를 입력해주시기 바랍니다.' }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				if(!confirm('정말 주문을 취소하시겠습니까?')) return false;
				form.submit();
			}

	});
});
</script>