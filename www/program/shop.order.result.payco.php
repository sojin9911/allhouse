<?php 
	require(PG_DIR."/payco/payco_config.php");
?>

<input type="hidden" id="order_num" value="" / > <!-- 주문번호-->
<input type="hidden" id="order_url" value="" / > <!-- 주문url -->
<div id="rst_msg"></div>
<script>
$(document).ready(function(){
	callPaycoUrl();
});
function callPaycoUrl(){
	var customerOrderNumber ="<?=$ordernum?>"; // 주문번호
	var cartNo = "CartNo_<?=$ordernum?>"; // 장바구니 번호 


	$.ajax({
		type: "POST",
		url: "<?php echo OD_PROGRAM_URL; ?>/shop.order.result.payco_reserve.php",
		//data: Params,		// JSON 으로 보낼때는 JSON.stringify(customerOrderNumber)
		data:{customerOrderNumber : customerOrderNumber , cartNo : cartNo},
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			if(data.code == '0') {
				//console.log(data.result.reserveOrderNo);	// 주석 해제시, 일부 웹브라우저 에서 PAYCO 결제창이 뜨지 않습니다.			
				$('#order_num').val(data.result.reserveOrderNo);
				$('#order_url').val(data.result.orderSheetUrl);		
			}else if(data.code=='fail'){
				alert('올바른 주문이 아닙니다.');
				//console.log(data.data);
			}else {
				alert("code:"+data.code+"\n"+"message:"+data.message);
			}
		},
        error: function(request,status,error) {
            //에러코드
           var error_msg = "code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error;
			$('#rst_msg').text(error_msg);
			alert('결제를 진행할 수 없습니다. 고객센터에 문의해 주세요.');
			return false;
        }
	});
}

function payco_open(){

	var order_Url = $('#order_url').val();

	if (order_Url == "")
	{
		alert("주문서 확인중입니다. 잠시만 기다려주세요.");
		return false;
	}	
	
	/*
	-----------------------------------------------------------------------------
	 USER-AGENT 구분 ( Mobile 이면 페이지 전환, Pc 이면 팝업 호출 )
	-----------------------------------------------------------------------------
	*/

	var isMobile = <?=$isMobile?>;
	
	if (isMobile == 0){  // MOBILE 
		document.location.href = order_Url;
		//location.href = order_Url;
		
	}else{               // PC              
		window.open(order_Url, 'popupPayco1', 'top=100, left=300, width=727px, height=512px, resizble=no, scrollbars=yes'); 
	}	
}

</script>