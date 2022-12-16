<?PHP
	$app_current_link = '_cashbill.form.php';
	include_once('wrap.header.php');

	include_once(OD_ADDONS_ROOT . '/barobill/include/var.php');


	if($_mode == "modify" && $_uid){
		$row = _MQ(" select * from smart_baro_cashbill where bc_uid = '${_uid}' ");
		if(!$row) error_msg("잘못된 접근입니다.");
	}
	else if($_ordernum){

		// 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 주문금액 과세/면세 구분
		// 입점업체정보 배열 추출
		$partner = array();
		$cp_row = _MQ_assoc("
			select
				op.op_partnerCode,
				cp.cp_vat_delivery
			from
				smart_company as cp left join
				smart_order_product as op on (op.op_partnerCode = cp.cp_id)
			where
				op.op_oordernum='{$_ordernum}' and op.op_cancel = 'N'
		");
		foreach($cp_row as $sk=>$sv) {
			if($siteInfo['s_vat_delivery'] <> 'C' || $SubAdminMode === false) $sv['cp_vat_delivery'] = $siteInfo['s_vat_delivery'];
			$partner[$sv['op_partnerCode']] = $sv['cp_vat_delivery'];
		}
		// 입점업체정보 배열 추출

		// 주문정보 호출
		$pr = _MQ_assoc("
			select
				op.*
			from
				smart_order_product as op
			where (1) and
				op.op_oordernum='{$_ordernum}' and op.op_cancel = 'N'
		");
		$data2 = array();
		foreach($pr as $sk=>$sv) {

			// 과세
			if($sv['op_vat'] == 'Y') {
				$data2['vatY'] += $sv['op_price'] * $sv['op_cnt'] - $sv['op_usepoint'];
			}
			// 면세
			else if($sv['op_vat'] == 'N') {
				$data2['vatN'] += $sv['op_price'] * $sv['op_cnt'] - $sv['op_usepoint'];
			}

			// 배송비 과세
			if($partner[$sv['op_partnerCode']] == 'Y') {
				$data2['vatY'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
			}
			// 배송비 면세
			else {
				$data2['vatN'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
			}
		}
		// 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 주문금액 과세/면세 구분

		// JJC : 주문 취소항목 추출 : 2018-01-04
		$add_que = implode(", " , array_keys($arr_order_cancel_field));

		// 주문정보로 기본정보 입력
		$oque = "
		select o_ohp, o_price_real, o_oemail, ". $add_que ." ,
					o.o_tax_TradeUsage,o.o_tax_TradeMethod,o.o_tax_IdentityNum,
					(select count(*) from smart_order_product as op where op.op_oordernum=o.o_ordernum) as op_cnt,
					(
						select
							op.op_pname
						from smart_order_product as op
						left join smart_product  as p on ( p.p_code=op.op_pcode )
						where op.op_oordernum=o.o_ordernum order by op.op_uid asc limit 1
					) as op_pname
		from smart_order as o where o_ordernum='{$_ordernum}' ";
		$or = _MQ($oque);
		//ViewArr($or);
		$row["bc_ordernum"] = $_ordernum;  // 주문번호
		$row['TradeUsage'] = $or['o_tax_TradeUsage'] ? $or['o_tax_TradeUsage'] : 1; // 1:소득공제
		$row['TradeMethod'] = $or['o_tax_TradeMethod'] ? $or['o_tax_TradeMethod'] : 5; // 4:휴대폰번호
		$row["IdentityNum"] = rm_str(($or['o_tax_IdentityNum'] ? onedaynet_decode($or['o_tax_IdentityNum']) : $or['o_ohp']));  // 신분확인번호

		// JJC : 주문 취소항목 추가 : 2018-01-04
		$add_cancel_price = 0;
		foreach($arr_order_cancel_field as $cfk=>$cfv){ $add_cancel_price += $or[$cfk]; }
		$totalPrice = $or["o_price_real"] - $add_cancel_price; // 판매금액
		// 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 과세 부분만 부가세 계산
		$row["Tax"] = $data2['vatY'] - ceil($data2['vatY']/1.1);
		$row["Amount"] = $totalPrice - $row["Tax"];  // 공급가액
		$row["ServiceCharge"] = 0;  // 봉사료 , 기본 0원
		$row["ItemName"] = $or["op_pname"] . ($or["op_cnt"] > 1 ? " 외 " . number_format($or["op_cnt"]-1) . "개" : null);  // 품목명
		$row["Email"] = $or["o_oemail"];  // 소비자이메일
	}

	// 발행실패 총 수량 추출
	$app_autofail_cnt = _MQ_result(" select count(*) as cnt from smart_baro_cashbill where 1 and bc_isdelete = 'N' and bc_iscancel = 'N' and BarobillState = '0000' ");
	// 전송실패함 총 수량 추출
	$app_fail_cnt = _MQ_result(" select count(*) as cnt from smart_baro_cashbill where 1 and bc_isdelete = 'N' and BarobillState = '4000' ");

?>

	<div class="group_title">
		<strong>현금영수증 발행정보</strong>
	</div>




<form id="frm" name="frm" method="post" ENCTYPE='multipart/form-data' action="_cashbill.pro.php" >
<input type="hidden" name="_mode" value=''>
<input type="hidden" name="_uid" value='<?php echo $_uid; ?>'>
<input type="hidden" name="_state" value='<?php echo $_state; ?>'>
<input type="hidden" name="_ordernum" value="<?php echo $row["bc_ordernum"]; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

	<!-- <div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">현금영수증 작성</div> -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
				<?php if($row["bc_ordernum"]){ ?>
				<tr>
					<th>주문번호</th>
					<td>
						<a href="_order.form.php?_mode=modify&_ordernum=<?php echo $row["bc_ordernum"];?>" alt="주문번호" target="infoOrder"><strong><?php echo $row["bc_ordernum"];?></strong></a>
					</td>
				</tr>
				<?php }else if($_state == 'issue'){ ?>
				<tr>
					<th>주문번호</th>
					<td>
						<input type="text" name=""  id="js_ordernum" value="" class="design" style="" placeholder="주문번호를 입력하세요.">
						<a href="#none" onclick="document.location.href='/totalAdmin/_cashbill.form.php?_state=issue&_ordernum='+$('#js_ordernum').val(); return false;" class="c_btn h27 line gray" target="_blank">주문정보 불러오기</a>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th class="ess">거래용도</th>
					<td><?php echo _InputRadio( "TradeUsage" , array_keys($arr_tradeUsage), ($row["TradeUsage"] ? $row["TradeUsage"] : "1") , "" , array_values($arr_tradeUsage) , ''); ?></td>
				</tr>
				<tr>
					<th class="ess">신분확인번호 구분</th>
					<td><?php echo _InputRadio( "TradeMethod" , array_keys($arr_TradeMethod), ($row["TradeMethod"] ? $row["TradeMethod"] : "5") , "" , array_values($arr_TradeMethod) , ''); ?><input type="hidden" name="_identitynum_valid" value="" /><!-- 신분확인번호 유효성체크 --></td>
				</tr>
				<tr>
					<th class="ess">신분확인번호</th>
					<td>
						<input type="text" name="IdentityNum" value="<?php echo $row["IdentityNum"]; ?>" class="design js_number_valid" style='width:150px' placeholder="">
						<div class="tip_box">
							<?php //echo _DescStr("주민번호/휴대폰/카드번호/사업자번호 중 하나를 입력하세요."); ?>
							<?php echo _DescStr("사업자번호를 입력한 경우 거래용도를 지출증빙용으로만 선택할 수 있습니다."); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="ess">공급가액</th>
					<td><input type="text" name="Amount" value="<?php echo $row["Amount"]; ?>" class="design number_style calc_total calc_amount" style='width:150px' placeholder=""><span class="fr_tx">원</span></td>
				</tr>
				<tr>
					<th>부가세</th>
					<td>
						<input type="text" name="Tax" value="<?php echo $row["Tax"]; ?>" class="design number_style calc_total calc_tax" style='width:150px' placeholder=""><span class="fr_tx">원</span>
						<?php
							 // 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 복합과세 일경우 부가세 직접입력
							 if($siteInfo['s_vat_product'] == 'Y'){
								 echo _DescStr("부가세 미입력(또는 0입력) 시 면세로 적용됩니다.");
							 }else if($siteInfo['s_vat_product'] == 'N'){
								 echo _DescStr("부가세 미입력(또는 0입력) 시 면세로 적용됩니다.");
							 }else{
								 echo _DescStr("복합과세일 경우 부가세는 직접 입력해 주시기 바랍니다..");
							 }
						?>
					</td>
				</tr>
				<tr>
					<th>봉사료</th>
					<td><input type="text" name="ServiceCharge" value="<?php echo $row["ServiceCharge"]; ?>" class="design number_style calc_total" style='width:150px' placeholder=""><span class="fr_tx">원</span></td>
				</tr>
				<tr>
					<th>판매금액</th>
					<td><input type="text" name="" value="<?php echo number_format($row["Amount"] + $row["Tax"] + $row["ServiceCharge"]); ;?>" class="design number_style calc_price_sum" style='width:150px' placeholder="" disabled="disabled"><span class="fr_tx">원</span></td>
				</tr>
				<tr>
					<th class="ess">품목명</th>
					<td><input type="text" name="ItemName" value="<?php echo $row["ItemName"]; ?>" class="design" style='width:350px' placeholder=""></td>
				</tr>
				<tr>
					<th>소비자이메일</th>
					<td>
						<input type="text" name="Email" value="<?php echo $row["Email"]; ?>" class="design" style='width:350px' placeholder="">
						<div class="tip_box">
							<?php echo _DescStr("주소를 입력하지 않으면 이메일이 발송되지 않습니다."); ?>
							<?php echo _DescStr("<a href='//www.barobill.co.kr' target='_blank' ><em>바로빌사이트</em></a> [현금영수증 > 환경설정 > 이메일환경설정] 메뉴에 메일발송 설정시 이메일이 발송됩니다."); ?>
						</div>
					</td>
				</tr>
<!--
				<tr>
					<th>관리자메모</th>
					<td>
						<textarea name="Memo" class="design" style="width:98%;height:80px;"><?php echo stripslashes($row['Memo']); ?></textarea>
						<?php echo _DescStr("소비자에게는 보이지 않습니다."); ?>
					</td>
				</tr>
				<?if($row["NTSConfirmMessage"]){?>
				<tr>
					<th>참고사항</th>
					<td class="conts" style="color:red"><?php echo $row["NTSConfirmMessage"]; ?></td>
				</tr>
				<?}?>
 -->
			</tbody>
		</table>
	</div>


	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="submit_btn('issue')" class="c_btn h46 red">즉시발행</a></li>
			<li><a href="#none" onclick="submit_btn('save')" class="c_btn h46 dark line">임시저장</a></li>
			<li><a href="_cashbill.list.php<?php echo URI_Rebuild('?' . enc('d' , $_PVSC)); ?>" class="c_btn h46 black line">목록으로</a></li>
		</ul>
	</div>
	<div class="fixed_save js_fixed_save" style="display: block;">
		<div class="wrapping">
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><a href="#none" onclick="submit_btn('issue')" class="c_btn h46 red">즉시발행</a></li>
					<li><a href="#none" onclick="submit_btn('save')" class="c_btn h46 dark line">임시저장</a></li>
					<li><a href="_cashbill.list.php<?php echo URI_Rebuild('?' . enc('d' , $_PVSC)); ?>" class="c_btn h46 black line">목록으로</a></li>
				</ul>
			</div>
		</div>
	</div>

</form>


<script language='javascript' src='../../include/js/lib.validate.js'></script>
<script type="text/javascript">
	$(document).ready(function(){

		// - 회원가입 박스 validate ---
		$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					IdentityNum:{ required : true},
					_identitynum_valid:{ required : true},
					Amount:{ required : true, min : 1},
					ItemName:{ required : true},
					Email:{ email : true}
			},
			messages: {
					IdentityNum : { required: "신분확인번호 입력하세요." },
					_identitynum_valid : { required: "잘못된 신분확인번호 입니다." },
					Amount: { required: "공급가액을 입력하세요." , min:"공급가액을 입력하세요"},
					ItemName: { required: "품목명을 입력하세요." },
					Email: { email: "이메일형식이 잘못되었습니다." }
			}
		});

		// 공급가액 입력시 부가세 자동입력
		$(".calc_amount").on("blur",function(){
			var amount = ($(this).val()).replace(/,/g,"")*1;
			<?php if($siteInfo['s_vat_product'] == 'Y'){ // 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 과세 일경우 부가세 계산 ?>
				var tax = Math.ceil(amount*0.1);
				$('.calc_tax').val((tax+'').comma());
			<?php }else if($siteInfo['s_vat_product'] == 'N'){ // 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 면세 일경우 부가세 0 ?>
				var tax = 0;
				$('.calc_tax').val((tax+'').comma());
			<?php } // 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 복합과세 일경우 부가세 직접입력 ?>
		});


		// 판매금액 계산
		$(".calc_total").on("blur",function(){
			var sum = 0;
			$(".calc_total").each(function(){
				var _price = ($(this).val()).replace(/,/g,"")*1;
				sum += _price;
			});
			$(".calc_price_sum").val((sum+"").comma());
		});

	});


	// submit 버튼
	function submit_btn(_type){
		$("#frm input[name=_mode]").val(_type);

		if($("#frm").valid()){
			$("#frm").submit();
		}
	}


	// - 현금영수증 지출증빙일때는 사업자번호만 선택가능 ---
	function tax_tradeUsage_check(){
		var _val = $("input[name=TradeUsage]:checked").val();

		// 소득공제일때
		if(_val == "1"){
			$("input[name=TradeMethod]").prop("disabled", false);

			$("input[name=TradeMethod][value=5]").prop("checked", true); // 기본선택 휴대폰번호
			$("input[name=TradeMethod][value=4]").prop("disabled", true); // 사업자번호 선택불가
		}
		// 지출증빙일때
		else if(_val=="2"){
			$("input[name=TradeMethod]").prop("disabled", true);

			$("input[name=TradeMethod][value=4]").prop("disabled", false); // 사업자번호 선택가능
			$("input[name=TradeMethod][value=4]").prop("checked", true); // 기본선택 사압자번호
		}
	}
	$(document).ready(function(){
		// 소등공제일때 값이 변경되는것을 방지 --
		//tax_tradeUsage_check();
		var _val = $("input[name=TradeUsage]:checked").val();

		// 소득공제일때
		if(_val == "1"){
			//$("input[name=TradeMethod]").prop("disabled", false);

			//$("input[name=TradeMethod][value=5]").prop("checked", true); // 기본선택 휴대폰번호
			$("input[name=TradeMethod][value=4]").prop("disabled", true); // 사업자번호 선택불가
		}
		// 지출증빙일때
		else if(_val=="2"){
			$("input[name=TradeMethod]").prop("disabled", true);

			$("input[name=TradeMethod][value=4]").prop("disabled", false); // 사업자번호 선택가능
			$("input[name=TradeMethod][value=4]").prop("checked", true); // 기본선택 사압자번호
		}
		// 소등공제일때 값이 변경되는것을 방지 --
	});
	$("input[name=TradeUsage]").on("change", tax_tradeUsage_check);

	$("input[name=TradeMethod]").on("change", function(){
		$("input[name=IdentityNum]").val("");
		$("input[name=_identitynum_valid]").val("");
	});

	// 신분확인번호 유효성체크----
	$(document).delegate(".js_number_valid", "change", function(){
		var _type = $("input[name=TradeMethod]:checked").val() + '';
		var _val = $(this).val();
		//alert(_type);
		if(_type != undefined && _val.replace(' ','') != ""){
			var result = validate_number(_type,_val);
			if(result === false){
				var msg = "";
				if(_type == "1"){
					//카드 번호가 유효한지 검사
					msg = "잘못된 카드번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
				}
				else if(_type == "3"){
					//주민등록 번호가 유효한지 검사
					msg = "잘못된 주민등록번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
				}
				else if(_type == "4"){
					//사업자등록 번호가 유효한지 검사
					msg = "잘못된 사업자번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
				}
				else if(_type == "5"){
					//휴대폰 번호가 유효한지 검사
					msg = "잘못된 휴대폰번호 입니다. 확인 후 다시 입력해주시기 바랍니다.";
				}
				$("input[name=_identitynum_valid]").val("");
				//alert(msg);
			}else{
				$("input[name=_identitynum_valid]").val("1");
			}
		}else{
			$("input[name=_identitynum_valid]").val("");
		}
	});
	$(".js_number_valid").trigger("change");// 최초실행시 한번실행시킨다

	function validate_number(_type, number) {

        //빈칸과 대시 제거
        number = number.replace(/[ -]/g,'');

        var match;
		if(_type == "1"){
			//카드 번호가 유효한지 검사
			match = /^(?:(94[0-9]{14})|(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/.exec(number);
		}
		else if(_type == "3"){
			//주민등록 번호가 유효한지 검사
			match = /^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))[1-4][0-9]{6}$/.exec(number);
		}
		else if(_type == "4"){
			//사업자등록 번호가 유효한지 검사
			match = checkBizID(number);
		}
		else if(_type == "5"){
			//휴대폰 번호가 유효한지 검사
			match = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/.exec(number);
		}

        if(match) {
			return true;
        } else {
            return false;
        }
    }

	function checkBizID(bizID)  //사업자등록번호 체크
	{
		// bizID는 숫자만 10자리로 해서 문자열로 넘긴다.
		var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1);
		var tmpBizID, i, chkSum=0, c2, remander;
		 bizID = bizID.replace(/-/gi,'');

		 for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i);
		 c2 = "0" + (checkID[8] * bizID.charAt(8));
		 c2 = c2.substring(c2.length - 2, c2.length);
		 chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1));
		 remander = (10 - (chkSum % 10)) % 10 ;

		if (Math.floor(bizID.charAt(9)) == remander) return true ; // OK!
		  return false;
	}





</script>


<?PHP
	include_once('wrap.footer.php');
?>