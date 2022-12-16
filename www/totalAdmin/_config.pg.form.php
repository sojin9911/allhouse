<?php
	// LCY 2018-02-07 -- 통합 전자결제(PG) 관리
	include_once('wrap.header.php');
	
	//$siteInfo['s_pg_type'] = 'lgpay';
	if( in_array($_pg_type, array_keys($arr_pg_type)) == false){ $_pg_type = ''; } // 기본제공되는 PG 이외것이 들어온다면...
	if($_pg_type == ''){  $_pg_type = $siteInfo['s_pg_type'] == '' ? array_shift(array_keys($arr_pg_type)) : $siteInfo['s_pg_type']; } // 없을경우
?>
<form name="frmPg" id="frmPg" method='post' action='_config.pg.pro.php' ENCTYPE='multipart/form-data'>
	<input type="hidden" name="_pg_type" value="<?php echo $_pg_type ?>"> <!-- 선택된 PG사 -->

	<!-- ● 내부탭 -->
	<div class="c_tab">
		<ul>
		<?php foreach($arr_pg_type as $k=>$v){  ?>
		<li class="select-pg <?php echo $_pg_type == $k ? 'hit':'' ?>" data-pg-type="<?php echo $k;?>">
			<a href="#none" class="btn select-pg-evt" onclick="return false;" data-pg-type="<?php echo $k;?>">
				<strong><?php echo $v; ?></strong>
				<?php if( $siteInfo['s_pg_type'] == $k ) {  ?>
				<span class="c_tag h18 yellow">사용중</span>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		</ul>
	</div>
	
	<?php // -- PG 사별로 불러온다. ?>
	<div class="pg-type-form"><?php include_once dirname(__FILE__).'/_config.pg.ajax_form.php'; ?></div>
	<?php // -- PG 사별로 불러온다. ?>


	<?php echo _submitBTNsub(); ?>
</form>

<script>

	// -- 자동실행
	$(document).ready(initPg);

	// -- 서브밋 
	function pgSubmit()
	{
		$('#frmPg').submit();
	}


	// -- 팝업 버튼 클릭 시
	$(document).on('click', '.js_pg_popup', function(e) {
		e.preventDefault();
		var _pg_type = $('#frmPg input[name="_pg_type"]').val();
		var _mode = $(this).data('mode');
		var _page = $(this).data('page');
		var defaultPopWidth = 1120; // 팝업 기본 넓이값
		var defaultPopHeight = 540; // 팝업 기본 높이값		
		var popWidth = $(this).data('width')*1;
		var popHeight = $(this).data('height')*1;

		if( _mode == undefined || _mode == '' || _page == undefined || _page == '' || _pg_type == '' || _pg_type == undefined ){ alert("잘못된 접근입니다."); return false; }

		if( popWidth == '' || popWidth == undefined){ $popWidth = defaultPopWidth; }
		if( popHeight == '' || popHeight == undefined){ $popHeight = defaultPopHeight; }

		window.open(_page+'?_mode='+_mode, _pg_type+'-'+_mode, 'width='+popWidth+',height='+popHeight+',top=100,scrollbars=yes');
	});

	// -- PG사 에스크로 코드 입력 시 체킹 :: 빈값일 시 에스크로 가입정보를 노출합니다. 체크 해제
	$(document).on('focusout','#frmPg [name="_pg_code_escrow"]',function(){
		var chk = $(this).val();
		if( chk == ''){ $('#frmPg [name="_view_escrow_join_info').prop('checked',false); return false;  }
	});

	// -- PG사 에스크로 코드 입력 값 체 킹 하여 없을 시 경고문구
	$(document).on('click','#frmPg [name="_view_escrow_join_info"]',function(){
		var chk = $(this).is(':checked');
		var escrowChk = $('#frmPg [name="_pg_code_escrow"]').val();
		if( chk == true && escrowChk == '' ){ 
			alert('PG사 에스크로 코드를 먼저 입력해 주세요'); 
			$(this).prop('checked',false); 
			$('#frmPg [name="_pg_code_escrow"]').focus();
			return false;  
		}
	});

	// -- 현금영수증 설정에 따른 노출 설정
	$(document).on('click','#frmPg [name="_cash_receipt_use"]',selectCashReceiptUse);
	function selectCashReceiptUse()
	{
		var _cash_receipt_use = $('#frmPg [name="_cash_receipt_use"]:checked').val();
		$('.cash-receipt-use').hide();
		if( _cash_receipt_use == 'Y'){$('.cash-receipt-use').show(); }
	}

	// -- PG 무이자 할부 선택에 따른 이벤트
	$(document).on('click','#frmPg [name="_pg_noinstallment"]',selectNoinstallmentPg);
	function selectNoinstallmentPg()
	{
	    var _pg_noinstallment = $('#frmPg [name="_pg_noinstallment"]:checked').val();
	    $('.pg-noinstallment-peroid').hide(); 
	    if( _pg_noinstallment == 'Y'){ $('.pg-noinstallment-peroid').show(); }
	}


	// -- PG 일반 할부 선택에 따른 이벤트
	$(document).on('click','#frmPg [name="_pg_installment"]',selectInstallmentPg);
	function selectInstallmentPg()
	{
	    var _pg_installment = $('#frmPg [name="_pg_installment"]:checked').val();
	    $('.pg-installment-peroid').hide(); 
	    if( _pg_installment == 'Y'){ $('.pg-installment-peroid').show(); }
	}


	// -- PG선택에 따른 처리
	$(document).on('click','.select-pg-evt',function(){
		var _pg_type =$(this).attr('data-pg-type');
		if( _pg_type == '' || _pg_type == undefined){ return false; }
		$('#frmPg input[name="_pg_type"]').val(_pg_type);
		selectPg();
	});
	function selectPg()
	{	
		var _pg_type = $('#frmPg input[name="_pg_type"]').val();
		if( _pg_type == '' || _pg_type == undefined){ return false; }
		$('.select-pg').removeClass('hit');
		$('.select-pg[data-pg-type="'+_pg_type+'"]').addClass('hit');
		var url = '_config.pg.ajax_form.php';
	    $.ajax({
	        url: url, cache: false,dataType : 'html', type: "get", async: false , 
	        data: { ajaxMode : 'true' , _pg_type : _pg_type }, 
	        success: function(html){
	        	$('.pg-type-form').html(html);
				selectInstallmentPg(); // -- PG 일반 할부 선택에 따른 이벤트
				selectNoinstallmentPg(); // -- PG 무이자 할부 선택에 따른 이벤트
				selectCashReceiptUse(); // -- 현금영수증 설정에 따른 노출 설정	        	
	        },error:function(request,status,error){ console.log(request.responseText); }
	    });	
	}

	// -- 초기화 이벤트 설정
	function initPg()
	{
		selectPg(); // -- PG선택에 따른 처리
		selectInstallmentPg(); // -- PG 일반 할부 선택에 따른 이벤트
		selectNoinstallmentPg(); // -- PG 무이자 할부 선택에 따른 이벤트
		selectCashReceiptUse(); // -- 현금영수증 설정에 따른 노출 설정
	}

</script>


<?php 
	
	include_once('wrap.footer.php'); 

?>