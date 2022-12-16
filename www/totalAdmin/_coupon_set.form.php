<?php 
// -- 쿠폰등록
$app_current_link = '_coupon_set.'.($_REQUEST['_mode'] == 'modify' ? 'list':'form').'.php';

include_once('wrap.header.php');


if(in_array($_mode, array('modify','add'))== false) $_mode = 'add'; // 모드가 없을경우 add 로 고정


if($_mode == 'modify'){ // 수정이라면
	$row = _MQ("select *from  smart_individual_coupon_set where ocs_uid = '".$_uid."' ");

	if(count($row) < 1)error_loc_msg("_coupon_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다."); 
	// 발급된 쿠폰이 있는지 체킹  :: 발급된 쿠폰이 있을경우 수정 불가능 
	$rowChk = _MQ("select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = '".$_uid."'  ");
}else{
	$_uid = str_shuffle(time()); // 하루이상의 시간초를 준다.
}

// 발급여부
$ocs_view = $row['ocs_view'] == '' ? array_shift(array_keys($arrCouponSet['ocs_view'])):$row['ocs_view'];

// 쿠폰유형
$ocs_type = $row['ocs_type'] == '' ? array_shift(array_keys($arrCouponSet['ocs_type'])):$row['ocs_type'];

 // 발급방법 
$ocs_issued_type = $row['ocs_issued_type'] == '' ? array_shift(array_keys($arrCouponSet['ocs_issued_type'])):$row['ocs_issued_type'];


// 발급방법이 자동발급일경우
$ocs_issued_type_auto = $row['ocs_issued_type_auto'] == '' ? array_shift(array_keys($arrCouponSet['ocs_issued_type_auto'])):$row['ocs_issued_type_auto']; 

// 사용기간
$ocs_use_date_type = $row['ocs_use_date_type'] == '' ? array_shift(array_keys($arrCouponSet['ocs_use_date_type'])):$row['ocs_use_date_type'];

// 쿠폰할인유형
$ocs_boon_type = $row['ocs_boon_type'] == '' ? array_shift(array_keys($arrCouponSet['ocs_boon_type'])):$row['ocs_boon_type'];

// 쿠폰혜택
$ocs_dtype = $row['ocs_dtype'] == '' ? array_shift(array_keys($arrCouponSet['ocs_dtype'])):$row['ocs_dtype']; 

// 발급수량 타입
$ocs_issued_cnt_type = $row['ocs_issued_cnt_type'] == '' ? array_shift(array_keys($arrCouponSet['ocs_issued_cnt_type'])):$row['ocs_issued_cnt_type']; 

// 중복발급설정
$ocs_issued_due_type = $row['ocs_issued_due_type'] == '' ? array_shift(array_keys($arrCouponSet['ocs_issued_due_type'])):$row['ocs_issued_due_type']; 

// 같은 유형의 쿠폰과 중복사용 여부 
$ocs_due_use = $row['ocs_due_use'] == '' ? array_shift(array_keys($arrCouponSet['ocs_due_use'])):$row['ocs_due_use']; 

$temp_pricePer = 0; 

if($ocs_dtype == 'per'){
	$temp_pricePer = $row['ocs_per'];
}else{
	$temp_pricePer = $row['ocs_price'];
}


// -- 권한별 값을 지정 
$resGroup = _MQ_assoc("select * from smart_member_group_set where 1 order by mgs_rank asc"); // -- 그룹정보를 가져온다.  
$authGroup= $row['ocs_issued_group'] != '' ? explode(',',$row['ocs_issued_group']):array();
$printGroupChk = '';
foreach($resGroup as $gk=>$gv){ 
	$printGroupChk .='<label class="design">';
	$printGroupChk .='	<input type="checkbox" class="" name="ocs_issued_group[]" value="'.$gv['mgs_uid'].'" '.(in_array($gv['mgs_uid'],$authGroup) == true || $_mode == 'add'  ? 'checked':'' ).' />'.$gv['mgs_name'];
	$printGroupChk .='</label>';
}
?>

<form action="_coupon_set.pro.php" name="frm" id="frm" method="post">
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
<input type="hidden" name="_mode" value="<?=$_mode?>"> <?php // -- 기본모드 ---  ?>
<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
<input type="hidden" name="issuedCnt" value="<?php echo $rowChk['cnt'] < 1 ? 0 : $rowChk['cnt']; ?>">
<input type="hidden" name="saveType" value="<?php echo $_mode == 'add' ? 'temp':'real' ; ?>"><?php // 추가일시에는 저장타입을 임시로 설정 ?>

	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>기본설정</strong><!-- 메뉴얼로 링크 --> </div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">	
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr> 
					<th>쿠폰유형</th>
					<td>
						<?php echo _InputRadio( 'ocs_type' , array_keys($arrCouponSet['ocs_type']), ($ocs_type) , '' , array_values($arrCouponSet['ocs_type']) , ''); ?>
						<div class="tip_box">
							<?php echo _DescStr("<em>주문쿠폰</em>의 경우 주문금액 기준으로 할인/적립이 됩니다.(배송비도 포함됩니다.)",""); ?>
							<?php echo _DescStr("<em>배송쿠폰</em>의 경우 배송비 기준으로 할인이 됩니다.",""); ?>
						</div>
					</td>
				</tr>												

				<tr> 
					<th>발급방법</th>
					<td>
						<?php echo _InputRadio( 'ocs_issued_type' , array_keys($arrCouponSet['ocs_issued_type']), ($ocs_issued_type) , '' , array_values($arrCouponSet['ocs_issued_type']) , ''); ?>
						<div class="tip_box">
							<?php echo _DescStr("<em>수동발급</em> : 관리자가 직접 회원을 선택하여 발급할 수 있습니다.",""); ?>
							<?php echo _DescStr("<em>자동발급</em> : 회원에게 자동으로 발급이 되는 쿠폰으로 자동발급 설정에 따라 쿠폰이 발급됩니다.   ",""); ?>
						</div>
					</td>
				</tr>	

				<?php // 자동발급 일경우 노출 ?>
				<tr class="js_issued_type_auto" style="display: none;"> 
					<th>자동발급 설정</th>
					<td>
						<?php echo _InputRadio( 'ocs_issued_type_auto' , array_keys($arrCouponSet['ocs_issued_type_auto']), ($ocs_issued_type_auto) , '' , array_values($arrCouponSet['ocs_issued_type_auto']) , ''); ?>
						<div class="tip_box">
							<?php echo _DescStr("<em>".$arrCouponSet['ocs_issued_type_auto']['1']."</em> : 고객이 첫 구매후 결제완료를 할 시 자동으로 쿠폰이 발급 됩니다.",""); ?>
							<?php echo _DescStr("<em>".$arrCouponSet['ocs_issued_type_auto']['2']."</em> : 고객이 구매 후 결제완료를 할 시 자동으로 쿠폰이 발급 됩니다.",""); ?>
							<?php echo _DescStr("<em>".$arrCouponSet['ocs_issued_type_auto']['3']."</em> : 고객정보를 확인하여 생일일 경우 자동으로 쿠폰이 발급됩니다.",""); ?>							
							<?php echo _DescStr("<em>".$arrCouponSet['ocs_issued_type_auto']['4']."</em> : 고객이 회원가입을 할 시 자동으로 쿠폰이 발급됩니다.",""); ?>							
						</div>
					</td>
				</tr>	
				<?php // 자동발급 일경우 노출 ?>

				<tr>
					<th class="ess">쿠폰명</th>
					<td>
						<input type="text" name="ocs_name" class="design" style="width:280px;" value="<?php echo $row['ocs_name']; ?>">
					</td>						
				</tr>	

				<tr>
					<th>쿠폰설명</th>
					<td>
						<input type="text" name="ocs_desc" class="design" style="width:480px;" value="<?php echo $row['ocs_desc']; ?>">
						<div class="tip_box">
							<?php echo _DescStr("200자 이내로 입력해 주세요."); ?>
							<?php echo _DescStr("쿠폰 설명의 경우 관리용이며 사용자 페이지에 노출되지 않습니다."); ?>
						</div>
					</td>						
				</tr>	
				
				<tr>
					<th class="ess">사용기간</th>
					<td>
						<?php echo _InputRadio( 'ocs_use_date_type' , array_keys($arrCouponSet['ocs_use_date_type']), $ocs_use_date_type , '' , array_values($arrCouponSet['ocs_use_date_type']) , ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="js_use_date_type_date" data-desc="사용기간 지정일경우" style="display: none;">
							
							<input type="text" name="ocs_sdate" value="<?php echo rm_str($row['ocs_sdate']) < 1 ? date('Y-m-d'): $row['ocs_sdate'] ?>" class="design js_datepic_min_today" readonly style="width:95px">
							<span class="fr_tx"> ~ </span>
							<input type="text" name="ocs_edate" value="<?php echo rm_str($row['ocs_edate']) < 1 ? date('Y-m-d'): $row['ocs_edate'] ?>" class="design js_datepic_min_today" readonly style="width:95px">							
						</div>
						<div class="js_use_date_type_expire" data-desc="유효기간 지정일경우" style="display: none;">
							 <span class="fr_tx">쿠폰 발급일로 부터</span><input type="text" name="ocs_expire" class="design" style="width:80px; text-align: right;" value="<?php echo $row['ocs_expire'] == '' ? 0 : $row['ocs_expire'] ?>"><span class="fr_tx">일까지 사용가능합니다.</span>
						</div>						
					</td>						
				</tr>	

				<tr> 
					<th class="ess">쿠폰 혜택</th>
					<td>
						<?php echo _InputRadio( 'ocs_boon_type' , array_keys($arrCouponSet['ocs_boon_type']), ($ocs_boon_type) , '' , array_values($arrCouponSet['ocs_boon_type']) , ''); ?>
						<div class="tip_box">
							<?php echo _DescStr("<em>배송비 할인</em>의 경우 쿠폰유형이 <em>배송쿠폰</em>일 경우 에만 설정 가능합니다.",""); ?>
						</div>
					</td>
				</tr>	

				<tr> 
					<th class="ess">쿠폰 혜택금액설정</th>
					<td>

						<input type="text" name="temp_pricePer" class="design number_style" style="width:80px; text-align: right;" value="<?=$temp_pricePer?>" />
						<?php echo _InputSelect( "ocs_dtype" , array_keys($arrCouponSet['ocs_dtype']) ,$ocs_dtype  , "" , array_values($arrCouponSet['ocs_dtype']) , ""); ?>	
						<span class="fr_tx js_boon_type_print">할인</span>
						
						<div class="js_price_type_per" style="display: none;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<label class="design"><input type="checkbox" name="ocs_price_max_use" <?php echo $row['ocs_price_max_use'] == 'Y' ? 'checked':''  ?>>최대</label>
							<input type="text" name="ocs_price_max" class="design" style="width:80px; text-align: right;" value="<?php echo $row['ocs_price_max']?>" />
							<span class="fr_tx">원 까지</span> <span class="fr_tx js_boon_type_print">할인</span>
						</div>

						<?php echo _DescStr("할인율의 경우 소수점이 아닌 정수만 입력해 주세요."); ?>

					</td>
				</tr>	

			</tbody> 
		</table>
	</div>

	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>쿠폰 발급/사용 상세설정</strong><!-- 메뉴얼로 링크 --> </div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">	
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr class="js_issued_cnt_type">
					<th class="ess">발급수량</th>
					<td>
						<?php echo _InputRadio( "ocs_issued_cnt_type" , array_keys($arrCouponSet['ocs_issued_cnt_type']) ,$ocs_issued_cnt_type  , "" , array_values($arrCouponSet['ocs_issued_cnt_type']) , ""); ?>
						<?php echo _DescStr("발급방법이 <em>자동발급</em> 일경우 적용됩니다..", ''); ?>	
						<div class="js_issued_cnt_type_cnt" style="display:none;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<span class="fr_tx">발급받을 수 있는 쿠폰 개수를</span>
							<input type="text" name="ocs_issued_cnt" class="design number_style" style="width:80px; text-align: right;" value="<?=$row['ocs_issued_cnt']?>" />
							<span class="fr_tx">개로 제한합니다.</span>
						</div>

					</td>
				</tr>

				<tr class="js_issued_due_type" style="display: none;">
					<th class="ess">쿠폰 중복발급 설정</th>
					<td>
						<?php echo _InputRadio( "ocs_issued_due_type" , array_keys($arrCouponSet['ocs_issued_due_type']) ,$ocs_issued_due_type  , "" , array_values($arrCouponSet['ocs_issued_due_type']) , ""); ?>
						<div class="tip_box">
						<?php echo _DescStr("발급방법이 <em>자동발급</em> 일경우 적용이 됩니다.", ''); ?>	
						<?php echo _DescStr("발급방법이 <em>자동발급</em> 이고 자동발급설정이 생일축하 일경우 설정과 상관없이 1년에 한번만 발급됩니다.", ''); ?>	
						<?php echo _DescStr("발급방법이 <em>자동발급</em> 이고 자동발급설정이 첫 구매/결제완료 또는 회원가입 일경우 설정과 상관없이 한번만 발급됩니다.", ''); ?>	
						</div>

					</td>
				</tr>

				<tr class="js_issued_group" style="display: none;">
					<th>쿠폰 발급가능한 회원등급 지정</th>
					<td>
						<?php echo $printGroupChk; ?>
						<div class="tip_box">
						<?php echo _DescStr('발급가능한 회원등급을 지정할 수 있습니다.', ''); ?>	
						<?php echo _DescStr("발급방법이 <em>자동발급</em> 일경우 적용됩니다.", ''); ?>							
						</div>
					</td>						
				</tr>					

				<tr>
					<th>최소 구매금액 설정</th>
					<td>
						<span class="fr_tx">구매 금액이 최소</span>
						<input type="text" name="ocs_limit" class="design number_style" style="width:80px; text-align: right;" value="<?=$row['ocs_limit']?>" />
						<span class="fr_tx">원 이상 결제 시 사용가능합니다.</span>		
					</td>						
				</tr>	

				<tr>
					<th>쿠폰 중복사용 설정</th>
					<td>
						<?php echo _InputRadio( 'ocs_due_use' ,array_keys($arrCouponSet['ocs_due_use']), $ocs_due_use , '' , array_values($arrCouponSet['ocs_due_use']) , ''); ?>
						<div class="tip_box">
						<?php echo _DescStr('같은 쿠폰에 대해 중복사용 여부를 설정할 수 있습니다.', ''); ?>	
						<?php //echo _DescStr('쿠폰유형이 <em>상품쿠폰일</em>경우 기본상품에서 등록가능한 상품쿠폰도 포함되어 적용됩니다.', ''); ?>	
						</div>
					</td>						
				</tr>						

			</tbody> 
		</table>
	</div>

<?php echo _submitBTN('_coupon_set.list.php'); ?>


</form>


<script> 
	// -- 변순 선언
	var couponSet = {};

	// -- 현재 저장된 설정 값 초기화
	function couponSet_initVar()
	{	
		couponSet.ocs_dtype = $('select[name="ocs_dtype"]').val(); // 쿠폰혜택 금액 원% 에 따른 값 
		couponSet.temp_pricePer = $("input[name='temp_pricePer']").val().replace(/,/g,'') * 1; // 혜택금액
		couponSet.ocs_price_max = $("input[name='ocs_price_max']").val().replace(/,/g,'') * 1; // ocs_dtype 이 per 일경우 최대 원 
		couponSet.ocs_price_max_use = $("input[name='ocs_price_max_use']").prop('checked'); // ocs_dtype 이 per 일경우 최대 금액제한 사용여부
		couponSet.issuedCnt = $("input[name='issuedCnt']").val().replace(/,/g,'')*1; // 실제발급개수
		couponSet.ocs_issued_cnt_type = $('input[name="ocs_issued_cnt_type"]:checked').val(); // 발급수량 선택에 따른 값 
		couponSet.ocs_issued_cnt = $("input[name='ocs_issued_cnt']").val().replace(/,/g,'')*1; // 자동발급일경우 발급수량 개수
		couponSet.ocs_expire = $("input[name='ocs_expire']").val().replace(/,/g,'')*1; // 최대 유효기간일
		couponSet.saveType = $("input[name='saveType']").val();
		couponSet._uid = $("input[name='_uid']").val();
		couponSet._PVSC = $("input[name='_PVSC']").val();
		couponSet.ocs_type = $("input[name='ocs_type']:checked").val(); // 쿠폰유형 
		couponSet.ocs_issued_type = $('input[name="ocs_issued_type"]:checked').val();  // 발급방식 
		couponSet.ocs_issued_type_auto = $('input[name="ocs_issued_type_auto"]:checked').val(); // 자동일경우  
		couponSet.ocs_use_date_type = $('input[name="ocs_use_date_type"]:checked').val(); // 사용기간 
		couponSet.ocs_boon_type = $('input[name="ocs_boon_type"]:checked').val(); // 쿠폰혜택 
	}

	$(document).ready(function(){
		couponSet_init();
	});

	// -- 초기화 함수 
	function couponSet_init()
	{
		couponSet_initVar();
		couponSet_issued_type(); // 발급방법에 따른 처리 
		couponSet_use_date_type(); // -- 사용기간에 따른 처리 
		couponSet_boon_type(); // 쿠폰 혜택 처리	
		couponSet_price_type(); // 쿠폰 혜택금액설정에 원/% 선택시 처리
		couponSet_issued_if(); // // 발급조건에 따른 처리 -- 자동발급, 다운로드 일경우 
	}

	$(document).on('click',"input[name='ocs_type']",function(){ couponSet_init(); }); // 쿠폰유형 클릭 시
	$(document).on('click',"input[name='ocs_issued_type']",function(){ couponSet_init(); }); // 발급방법 클릭 시
	$(document).on('click',"input[name='ocs_use_date_type']",function(){ couponSet_init(); }); // 사용기간 클릭 시
	$(document).on('click',"input[name='ocs_boon_type']",function(){ couponSet_init(); }); // 혜택 선택 클릭 시
	$(document).on("change","select[name='ocs_dtype']",function(){ couponSet_init(); }); // 금액 원/% 선택 시 
	$(document).on('click',"input[name='ocs_issued_cnt_type']",function(){ couponSet_init(); }); // 발급수량 선택 시
	$(document).on('click',"input[name='ocs_issued_use_type']",function(){ couponSet_init();  }); // 쿠폰 발급/사용 범위 설정 시
	$(document).on('click',"input[name='ocs_issued_except_use_type[]']",function(){ couponSet_init();  }); // 

	// -- 발급가능한 회원등급 체크 
	$(document).on('click',"input[name='ocs_issued_group[]']",function(){
		var chkLen = $("input[name='ocs_issued_group[]']:checked").length;
		if( chkLen < 1){ alert("발급가능한 회원등급은 최소 한개이상 지정 하셔야합니다."); return false; }
	})

	// 발급방법에 따른 처리 
	function couponSet_issued_type()
	{

		couponSet_initVar(); // 변수 초기화
		if( couponSet.ocs_issued_type == 'manual'){ // 수동발급일경우
			$('.js_issued_type_auto').hide(); // 자동발급 설정 숨기기
		}else if( couponSet.ocs_issued_type == 'auto'){ // 자동발급일경우
			$('.js_issued_type_auto').show();
		}

	}

	// -- 사용기간에 따른 처리 
	function couponSet_use_date_type()
	{	
		$("input[name='ocs_use_date_type'][value='date']").prop({disabled:true});
		if( couponSet.ocs_issued_type == 'auto'){
			$("input[name='ocs_use_date_type'][value='expire']").prop({checked : true });
		}else{
			$("input[name='ocs_use_date_type'][value='date']").prop({disabled:false});
		}

		couponSet_initVar(); // 변수 초기화
		if(couponSet.ocs_use_date_type == 'date'){ // 사용기간 지정일 경우
			$('.js_use_date_type_date').show();
			$('.js_use_date_type_expire').hide();
		}else if(couponSet.ocs_use_date_type == 'expire'){ // 사용가능일 지정일 경우
			$('.js_use_date_type_date').hide();
			$('.js_use_date_type_expire').show();
		}
	
	}

	// -- 쿠폰혜택에 따른 처리
	function couponSet_boon_type()
	{	

		// ------ {쿠폰원단위패치} :: 주석처리 ------ 2019-02-28 LCY
//		$('input[name="ocs_boon_type"]').prop({disabled : true}); // 쿠폰혜택
//		$('select[name="ocs_dtype"] option').prop({disabled : true}); // 쿠폰 혜택 금액설정
		// ------ {쿠폰원단위패치} :: 주석처리 ------ 2019-02-28 LCY


		if( couponSet.ocs_type == 'delivery'){ // 쿠폰유형이 배송이라면
			$('input[name="ocs_boon_type"][value="delivery"]').prop({checked : true, disabled : false}); // 배송비 자동선택
			$('select[name="ocs_dtype"] option[value="price"]').prop({selected: true, disabled : false}); // 쿠폰 혜택금액설정 을

		}else{
			$('input[name="ocs_boon_type"]').prop('disabled',false);
			$('input[name="ocs_boon_type"][value="delivery"]').prop({disabled : true});

			// -- 쿠폰혜택이 배송일 경우
			if( couponSet.ocs_boon_type == 'delivery'){ // 쿠폰 혜택이 배송비 할인일경우 :: 첫번째 선택값으로 변경 
				$('input[name="ocs_boon_type"]').eq(0).prop('checked',true);
			}

			// 쿠폰 유형이 주문일경우
			if( couponSet.ocs_type == 'order'){ // 주문일경우

				// ------ {쿠폰원단위패치} :: 주석처리 ------ 2019-02-28 LCY
				//$('select[name="ocs_dtype"] option[value="per"]').prop({ selected: true , disabled : false}); // 퍼센트로 고정
				// ------ {쿠폰원단위패치} :: 주석처리 ------ 2019-02-28 LCY

				// ------ {쿠폰원단위패치} ------ 2019-02-28 LCY
				$('select[name="ocs_dtype"] option[value="per"]').prop({  disabled : false}); // 퍼센트로 고정
				$('select[name="ocs_dtype"] option[value="price"]').prop({ disabled : false}); // 퍼센트로 고정
				// ------ {쿠폰원단위패치} ------ 2019-02-28 LCY

			}else{
				$('select[name="ocs_dtype"] option').prop({disabled : false}); // 상품일경우 해제
			}
				
		}
		couponSet_initVar(); // 변수 초기화
		if( couponSet.ocs_boon_type == 'discount'){ // 할인
			$('.js_boon_type_print').text('할인');
		}else if(couponSet.ocs_boon_type == 'save'){ // 적립 
			$('.js_boon_type_print').text('적립');
		}else if(couponSet.ocs_boon_type == 'delivery'){ // 배송비 할인  
			$('.js_boon_type_print').text('할인');
		}
	}

	// -- 원/% 선택에 따른 이벤트
	function couponSet_price_type()
	{
		couponSet_initVar(); // 변수 초기화
		if( couponSet.ocs_dtype == 'price'){ // 원이라면
			$('.js_price_type_per').hide();
		}else if(couponSet.ocs_dtype == 'per'){ // 할인% 라면
			$('.js_price_type_per').show();
		}else{	// 선택일 경우 기본가격으로 조절
			$('.js_price_type_per').hide();
			$('select[name="ocs_dtype"]').val('price'); 
		}
	}

	// -- 발급수량 설정
	function couponSet_issued_if()
	{
		couponSet_initVar(); // 변수 초기화
		$('.js_issued_cnt_type').hide(); // 발급수량 항목 기본 숨김처리

		// -- 수동발급일 경우 숨김처리
		if( couponSet.ocs_issued_type == 'manual'){
			$('.js_issued_cnt_type').hide(); // 발급 수량설정
			$('.js_issued_due_type').hide(); // 중복발급
			$('.js_issued_group').hide(); // 발급가능 회원등급 
		}else{


			$('.js_issued_cnt_type').show(); // 발급 수량설정
			$('.js_issued_group').show(); // 발급가능 회원등급 
			
			$('.js_issued_due_type').show(); // 중복발급
			
			if( couponSet.ocs_issued_cnt_type == 'limit'){ // 제한없음 이라면
				$('.js_issued_cnt_type_cnt').hide();
			}else if(couponSet.ocs_issued_cnt_type == 'cnt'){ // 개수지정이라면
				$('.js_issued_cnt_type_cnt').show();
			}	


		}
	
	}



	$(document).ready(function() {

		// -  validate ---
		$('form[name=frm]').validate({
			ignore: '.ignore',
			rules: {
				ocs_name : {required : true  }  // 쿠폰명
			},
			messages: {
				ocs_name : {required : '쿠폰명을 입력해 주세요..'  }
			},
			submitHandler : function(form) {

				couponSet_initVar();

				// 쿠폰 혜택 금액 판별
				if( couponSet.ocs_dtype == 'per'){
					if( couponSet.temp_pricePer > 99){ 
						alert("할인률(%)의 경우 100 미만으로 입력해 주세요."); 
						$('input[name="temp_pricePer"]').focus();
						return false; 
					}

					if( floatCheck(  couponSet.temp_pricePer ) == true){
						alert("할인률(%)의 경우 0이상 정수만 입력해 주세요.");
						$('input[name="temp_pricePer"]').focus();
						return false; 						 
					}

					// --  쿠폰혜택금액설정에서 최대금액에 체크가 되어 있을 시 체크 
					if( couponSet.ocs_price_max_use == true){ 
						if(couponSet.ocs_price_max < 1){
							alert("쿠폰 혜택금액설정 최대금액을 입력해 주세요."); 
							$('input[name="ocs_price_max"]').focus();
							return false; 
							
						}    
					}
				}

				// -- 자동발급일경우 처리
				if( couponSet.ocs_issued_type == 'auto' && couponSet.ocs_issued_cnt_type == 'cnt' && couponSet.ocs_issued_cnt < 1 ){
					alert("발급받을 수 있는 쿠폰 개수를 입력해 주세요."); $('input[name="ocs_issued_cnt"]').focus();
				}

				// 사용기간에 따른 판별
				if( couponSet.ocs_use_date_type == 'expire'){ // 사용가능일 지정일경우
					if( couponSet.ocs_expire < 1){  alert("쿠폰의 사용가능일 입력해 주세요."); $('input[name="ocs_expire"]').focus(); return false;  }
				}


				if( couponSet.issuedCnt  >  0){ alert("발급된 쿠폰이 존재하여 수정이 불가능합니다."); return false; }
				
				form.submit();
			}
		});
		// - validate ---
	});


	// 소수점 체크
	function floatCheck(obj){
		 var num_check=/^([0-9]*)[\.]/;
			if(!num_check.test(obj)){
			return false;
		}
		return true;
	}

</script>

<?php 
	include_once('wrap.footer.php');
?>