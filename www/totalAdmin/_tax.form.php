<?PHP
	include_once("wrap.header.php");

	// 바로빌변수
	include_once(OD_ADDONS_ROOT . '/barobill/include/var.php');
	include_once(OD_ADDONS_ROOT . '/barobill/include/BaroService_TI.php');

	// - 수정 ---
	if( $_mode == "modify" ) {
		// 발행정보 추출
		$r = _MQ(" select * from smart_baro_tax where bt_uid = '{$_uid}' ");

		// 발행로그 추출
		$log = _MQ_assoc(" select * from smart_baro_tax_log where tl_btuid = '{$_uid}' ");

		//공급자 정보 - 정발행시 세금계산서 작성자
		$InvoicerParty = getInvoicerParty($r['MgtKey']);
	}
	// - 수정 ---

	// - 등록 ---
	else {
		$_mode = "add";

		//공급자 정보 - 정발행시 세금계산서 작성자
		$InvoicerParty = getInvoicerParty();

		if($suid > 0){
			// 정산정보
			$oscr = _MQ(" select * from `smart_order_settle_complete` where `s_uid` = '{$suid}' ");
			$oscr = array_merge($oscr , _text_info_extraction( "smart_order_settle_complete" , $oscr['s_uid'] ));
			$op_code = explode(',', $oscr['s_opuid']);

			// 입점업체정보
			$partner = _MQ(" select * from `smart_company` where `cp_id` = '{$oscr['s_partnerCode']}' ");

			// 주문정보 호출
			$pr = _MQ_assoc("
				select
					op.*, p.* , o.* ,
					IF(
						op.op_comSaleType='공급가' ,
						(op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price) ,
						(op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price)
					) as comPrice
				from `smart_order_product` as op
				left join `smart_product` as p on (p.p_code=op.op_pcode)
				left join `smart_order` as o on (op.op_oordernum = o.o_ordernum )
				where
					op.op_uid in ('". implode("' , '" , $op_code) ."') and
					op_partnerCode = '" . $oscr['s_partnerCode'] . "'
			");

			// 2017-06-22 ::: 부가세율설정 ::: JJC
			$arr_sum = array();
			foreach($pr as $sk=>$sv) {
				// 2017-06-22 ::: 부가세율설정 ::: JJC
				$partner['cp_vat_delivery'] = ($siteInfo['s_vat_delivery'] == 'C' ? $partner['cp_vat_delivery'] : $siteInfo['s_vat_delivery']);
				$arr_sum['delivery_price'][$partner['cp_vat_delivery']] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
				$arr_sum['product_cnt'][$sv['op_vat']] += $sv['op_cnt'];
				$arr_sum['product_price'][$sv['op_vat']] += $sv['op_price'] * $sv['op_cnt'];
				$arr_sum['product_usepoint'][$sv['op_vat']] += $sv['op_usepoint'];
				$arr_sum['comPrice'][$sv['op_vat']] += $sv['comPrice'];
				// 2017-06-22 ::: 부가세율설정 ::: JJC

				// 2018-04-10 ::: 품목명칭 추출 ::: SSJ
				if($arr_sum['product_name'][$sv['op_vat']] == '') $arr_sum['product_name'][$sv['op_vat']] = trim(stripslashes($sv['op_pname'] . " " . $sv['op_option1'] . " " . $sv['op_option2'] . " " . $sv['op_option3']));
				$arr_sum['product_count'][$sv['op_vat']]++; // 품목의 수
			}
			$arr_sum['total']['Y'] = $arr_sum['product_price']['Y'] + $arr_sum['delivery_price']['Y']; // 과세 합계
			$arr_sum['total']['N'] = $arr_sum['product_price']['N'] + $arr_sum['delivery_price']['N']; // 면세 합계

			// ------ 2017-06-22 ::: 부가세율설정 ::: JJC --------------------
			$app_vat_Y_discount = $arr_sum['product_price']['Y'] + $arr_sum['delivery_price']['Y'] - $arr_sum['comPrice']['Y'] - $arr_sum['product_usepoint']['Y']; // 과세 수수료
			$app_vat_N_discount = $arr_sum['product_price']['N'] + $arr_sum['delivery_price']['N'] - $arr_sum['comPrice']['N'] - $arr_sum['product_usepoint']['N']; // 면세 수수료

			// 품목명칭 추출
			$app_vat_Y_pname = $arr_sum['product_name']['Y'] . ($arr_sum['product_count']['Y'] > 1 ? ' 외 ' . ($arr_sum['product_count']['Y']-1) . '건' : null) . ' 정산수수료';
			$app_vat_N_pname = $arr_sum['product_name']['N'] . ($arr_sum['product_count']['N'] > 1 ? ' 외 ' . ($arr_sum['product_count']['N']-1) . '건' : null) . ' 정산수수료';

		}else if($cpid){
			// 입점업체정보
			$partner = _MQ(" select * from `smart_company` where `cp_id` = '{$cpid}' ");
		}else{
			// 입점업체 정보 불러오기 노출
			if($SubAdminMode === true && $AdminPath == 'totalAdmin') $trigger_partner_form = true;
		}

		// 입점업체정보 - 대입
		$r['CorpName'] = $partner['cp_name']; //상호명(법인명)
		$r['CEOName'] = $partner['cp_ceoname']; //대표자명
		$r['CorpNum'] = $partner['cp_number']; //사업자등록번호
		$r['BizType'] = $partner['cp_item1']; //업태
		$r['BizClass'] = $partner['cp_item2']; //종목
		$r['ContactName'] = ($partner['cp_charge'] ? $partner['cp_charge'] : $partner['cp_ceoname']); //담당자명
		$r['HP'] = $partner['cp_tel2']; //담당자 휴대폰
		$r['Email'] = $partner['cp_email']; //담당자 E-mail
		$r['TEL'] = $partner['cp_tel1']; //담당자 전화번호
		$r['Addr'] = $partner['cp_address']; //사업장소재지

		// 정산금액 - 대입
		$r['TaxInvoiceType'] = ($vat <> 'N' ? 1 : 2); // 1: 과세, 2: 면세
		$r['Name'] = ($vat <> 'N' ? $app_vat_Y_pname : $app_vat_N_pname); // 품목명칭
		$r['bt_total_price'] = ($vat <> 'N' ? $app_vat_Y_discount : $app_vat_N_discount); // 합계금액
		$r['Tax'] = ($vat <> 'N' ? ceil($r['bt_total_price']/11) : 0); // 세액
		$r['Amount'] = $r['bt_total_price'] - $r['Tax']; // 공급가

		// 연동uid - 대입
		$r['bt_suid'] = $suid;

	}
	// - 등록 ---


?>




<form id="frm" name="frm" method="post" ENCTYPE="multipart/form-data" action="_tax.pro.php" >
<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
<input type="hidden" name="_submode" value="<?php echo $_mode; ?>">
<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
<input type="hidden" name="suid" value="<?php echo $r['bt_suid']; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>공급자 정보</strong></div>

	<!-- 검색영역 -->
	<div class="data_form">
		<table class="table_form" summary="검색항목">
			<colgroup>
				<col width="180"><col width="*"/><col width="180"><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>공급자정보 설정</th>
					<td colspan="3">
						<a href="_config.tax.form.php" class="c_btn h27 blue" target="_blank" title="공급자 정보 설정">공급자 정보 수정</a>
					</td>
				</tr>
				<tr>
					<th class="ess">상호명(법인명)</th>
					<td>
						<?php echo $InvoicerParty['CorpName']; ?>
					</td>
					<th class="ess">대표자명</th>
					<td>
						<?php echo $InvoicerParty['CEOName']; ?>
					</td>
				</tr>
				<tr>
					<th class="ess">사업자등록번호</th>
					<td>
						<?php echo $InvoicerParty['CorpNum']; ?>
					</td>
					<th>업태 / 종목</th>
					<td>
						<?php echo $InvoicerParty['BizType']; ?> / <?php echo $InvoicerParty['BizClass']; ?>
					</td>
				</tr>
				<tr>
					<th class="ess">담당자명</th>
					<td>
						<?php echo $InvoicerParty['ContactName']; ?>
					</td>
					<th>담당자 휴대폰</th>
					<td>
						<?php echo $InvoicerParty['HP']; ?>
					</td>
				</tr>
				<tr>
					<th class="ess">담당자 E-mail</th>
					<td>
						<?php echo $InvoicerParty['Email']; ?>
					</td>
					<th>담당자 전화번호</th>
					<td>
						<?php echo $InvoicerParty['TEL']; ?>
					</td>
				</tr>
				<tr>
					<th class="ess">사업장소재지</th>
					<td colspan="3">
						<?php echo $InvoicerParty['Addr']; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- // 검색영역 -->



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>공급받는자 정보</strong></div>

	<!-- 검색영역 -->
	<div class="data_form">
		<table class="table_form" summary="검색항목">
			<colgroup>
				<col width="180"><col width="*"/><col width="180"><col width="*"/>
			</colgroup>
			<tbody>
				<?php if($trigger_partner_form){ ?>
				<tr>
					<th class="ess">입점업체 아이디</th>
					<td colspan="3">
						<input type="text" name=""  id="js_cpid" value="" class="design" style="" placeholder="입점업체 아이디를 입력하세요.">
						<a href="#none" onclick="document.location.href='/totalAdmin/_tax.form.php?cpid='+$('#js_cpid').val(); return false;" class="c_btn h27 line gray" target="_blank">입점업체정보 불러오기</a>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th class="ess">상호명(법인명)</th>
					<td>
						<input type="text" name="CorpName" class="design" style="width:300px" value="<?php echo $r['CorpName']; ?>" />
					</td>
					<th class="ess">대표자명</th>
					<td>
						<input type="text" name="CEOName" class="design" style="width:300px" value="<?php echo $r['CEOName']; ?>" />
					</td>
				</tr>
				<tr>
					<th class="ess">사업자등록번호</th>
					<td>
						<input type="text" name="CorpNum" class="design" style="width:300px" value="<?php echo $r['CorpNum']; ?>" />
					</td>
					<th>업태 / 종목</th>
					<td>
						<input type="text" name="BizType" class="design" style="width:143px" value="<?php echo $r['BizType']; ?>" />
						<span class="fr_tx">/</span>
						<input type="text" name="BizClass" class="design" style="width:143px;margin-left:0;" value="<?php echo $r['BizClass']; ?>" />
					</td>
				</tr>
				<tr>
					<th class="ess">담당자명</th>
					<td>
						<input type="text" name="ContactName" class="design" style="width:300px" value="<?php echo $r['ContactName']; ?>" />
					</td>
					<th>담당자 휴대폰</th>
					<td>
						<input type="text" name="HP" class="design" style="width:300px" value="<?php echo $r['HP']; ?>" />
					</td>
				</tr>
				<tr>
					<th class="ess">담당자 E-mail</th>
					<td>
						<input type="text" name="Email" class="design" style="width:300px" value="<?php echo $r['Email']; ?>" />
					</td>
					<th>담당자 전화번호</th>
					<td>
						<input type="text" name="TEL" class="design" style="width:300px" value="<?php echo $r['TEL']; ?>" />
					</td>
				</tr>
				<tr>
					<th class="ess">사업장소재지</th>
					<td colspan="3">
						<input type="text" name="Addr" class="design" style="width:520px" value="<?php echo $r['Addr']; ?>" />
					</td>
				</tr>
				<tr>
					<th class="ess">발행상세</th>
					<td colspan="3">
						<span class="fr_bullet">과세구분 : </span>
						<?php echo _InputRadio( 'TaxInvoiceType' , array('1','2') , ($r['TaxInvoiceType']?$r['TaxInvoiceType']:1) , ' class="js_calc_auto js_calc_type" ' , array('과세(세금계산서)','면세(계산서)') , ""); ?>
						<div class="dash_line"><!-- 점선라인 --></div>

						<span class="fr_bullet">품목명칭 : </span>
						<input type="text" name="Name" class="design" style="width:455px" value="<?php echo $r['Name']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>

						<span class="fr_bullet">합계금액 : </span>
						<input type="text" name="bt_total_price" class="design number_style js_calc_auto js_calc_total" value="<?php echo number_format($r['bt_total_price']); ?>" placeholder="" style="width:100px">
						<div class="bar"></div>
						<!-- 공급가와 세액은 입력받지않고 합계금액에서 계산하여 등록한다 -->
						<span class="fr_bullet">공급가 : </span>
						<input type="text" name="" class="design disabled t_right js_calc_amount" value="<?php echo number_format($r['Amount']); ?>" placeholder="" style="width:100px" readonly>
						<div class="bar"></div>
						<span class="fr_bullet">세액 : </span>
						<input type="text" name="" class="design disabled t_right js_calc_tax" value="<?php echo number_format($r['Tax']); ?>" placeholder="" style="width:100px" readonly>
					</td>
				</tr>
				<?php if(count($log)>0){ ?>
				<tr>
					<th>발행기록</th>
					<td colspan="3">

						<table class="table_list" summary="리스트기본">
							<colgroup>
								<col width="70"><col width="80"><col width="80"><col width="80"><col width="*"><col width="100">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">NO</th>
									<th scope="col">구분</th>
									<th scope="col">결과</th>
									<th scope="col">결과코드</th>
									<th scope="col">상세로그</th>
									<th scope="col">연동일시</th>
								</tr>
							</thead>
							<tbody>
							<?PHP
								foreach($log as $k=>$v) {

									$_num = $k+1 ;
							?>
									<tr>
										<td><?php echo $_num; ?></td>
										<td><?php echo $arr_tax_method[$v['tl_mode']]; ?></td>
										<td>
											<div class="lineup-vertical"><?php echo $arr_barobill_button[($v['tl_code']<0?'실패':'성공')]; ?></div>
										</td>
										<td><?php echo  $v['tl_code']; ?></td>
										<td class="t_left"><?php echo $v['tl_remark']; ?></td>
										<td><?php echo date('Y-m-d', strtotime($v['tl_rdate'])); ?></td>
									</tr>
							<?php
								}
							?>
							</tbody>
						</table>

					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<!-- // 검색영역 -->


	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="submit_btn('quick')" class="c_btn h46 red">세금계산서 발행</a></li>
			<li><span class="c_btn h46 dark line"><input type="submit" name="" value="임시저장" accesskey="s"></span></li>
			<li><a href="_tax.list.php<?php echo URI_Rebuild('?' . enc('d' , $_PVSC)); ?>" class="c_btn h46 black line">목록으로</a></li>
		</ul>
	</div>
	<div class="fixed_save js_fixed_save" style="display: block;">
		<div class="wrapping">
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><a href="#none" onclick="submit_btn('quick')" class="c_btn h46 red">세금계산서 발행</a></li>
					<li><span class="c_btn h46 dark line"><input type="submit" name="" value="임시저장" accesskey="s"></span></li>
					<li><a href="_tax.list.php<?php echo URI_Rebuild('?' . enc('d' , $_PVSC)); ?>" class="c_btn h46 black line">목록으로</a></li>
				</ul>
			</div>
		</div>
	</div>




</form>


<script>
	// 합계금액으로 공급가/세액 계산
	$(document).on('change', '.js_calc_auto', function(){
		// 과세형태
		var type = $('.js_calc_type:checked').val()*1;
		// 합계금액
		var total = $('.js_calc_total').val().replace(',','')*1;
		// 세액
		var tax = 0;
		// 공급가
		var amt = 0;

		// 과세일때만 세액계산
		if(type == 1) tax = Math.ceil(total/11);
		amt = total - tax;

		$('.js_calc_amount').val(amt.toString().comma());
		$('.js_calc_tax').val(tax.toString().comma());
	});


	// submit 버튼
	function submit_btn(_type){
		$('#frm input[name=_submode]').val(_type);

		if($('#frm').valid()){
			$('#frm').submit();
		}
	}


	// 폼 유효성 검사
	$(document).ready(function(){
		$('form[name=frm]').validate({
				ignore: '.ignore',
				rules: {
						CorpName: { required: true }
						,CEOName: { required: true }
						,CorpNum: { required: true }
						,BizType: { required: false }
						,BizClass:{ required: false }
						,ContactName:{ required: true }
						,Email:{ required: true }
						,Addr: { required: true }
						,bt_total_price: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						CorpName : { required: '상호명(법인명)을 입력해주시기 바랍니다.' }
						,CEOName : { required: '대표자명을 입력해주시기 바랍니다.' }
						,CorpNum : { required: '사업자 등록번호를 입력해주시기 바랍니다.' }
						,BizType : { required: '업태를 입력해주시기 바랍니다.' }
						,BizClass : { required: '종목을 입력해 주시기 바랍니다.' }
						,ContactName:{ required: '담당자명을 입력해주시기 바랍니다.' }
						,Email:{ required: '담당자 E-mail을 입력해주시기 바랍니다.' }
						,Addr : { required: '사업장 소재지 주소를 입력해주시기 바랍니다.' }
						,bt_total_price : { required: '발행 합계금액을 입력해주시기 바랍니다.' }
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});
</script>



<?PHP
	include_once("wrap.footer.php");
?>