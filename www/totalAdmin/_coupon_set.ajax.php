<?php 
	include_once("inc.php");

	if( $ajaxMode == '' ){ echo json_encode(array('rst'=>'error','msg'=>"잘못된 접근입니다.")); exit; }

	switch($ajaxMode){
		case "issuedUseProduct": // 쿠폰 발급/사용 범위 설정  ::: 상품

			if( count($chk_pcode) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>"한개 이상 선택해 주세요.")); exit; }
			
			
			if($ctrlMode == 'add'){ // 추가일경우

			}else{ // 삭제일경우

			}
		break;

		case "issuedExceptUseProduct": // 쿠폰 발급/사용 점위/제외 설정 ::: 상품 
			if($ctrlMode){

			}else{

			}		
		break;

	}


?>