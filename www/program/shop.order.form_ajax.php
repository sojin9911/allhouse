<?php
/*	
	- 해당파일은 shop.order.form 에서 기존 처리이외 별도의 ajax 처리가 필요할 경우 처리 
	- 모드는 ajaxMode 로 처리,.. _mode 는 shop.order.form 에 있는 모드와 겹칠 수 있어 ajaxMode 로 변경 
*/
include_once(dirname(__FILE__).'/inc.php');
$varPayMinPrice = 0; // 결제가능한 최대 금액
// ajaxMode 처리
switch ($ajaxMode) {
	case 'couponSelete': // return => JSON ::  사용자 쿠폰 선택 시
		// LCY : 2021-11-10 : 쿠폰시작일/만료일 강화체크 추가 -- and if( ocs_use_date_type = 'date' , ocs_sdate <= curdate() , 1  ) and coup_expdate >= curdate()
		$rowCouponInfo = _MQ(" select * from smart_individual_coupon as coup inner join smart_individual_coupon_set as ocs on(ocs.ocs_uid = coup.coup_ocs_uid) where coup_inid='".get_userid()."' and coup_use='N' and coup_uid = '".$couponUid."' and if( ocs_use_date_type = 'date' , ocs_sdate <= curdate() , 1  ) and coup_expdate >= curdate() ");

		// 쿠폰검색이 안된다면
		if(count($rowCouponInfo)  <  1){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit; }

		// 중복사용여부 체크 
		$rowChkDue = _MQ(" select count(*) as cnt from `smart_individual_coupon_form` as ocf 
		inner join smart_individual_coupon as coup on(coup.coup_uid = ocf.ocf_coupuid)
		inner join smart_individual_coupon_set as ocs on(ocs.ocs_uid = coup.coup_ocs_uid) 
		where ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and coup_use='N' and ocs_uid = '".$rowCouponInfo['ocs_uid']."'  order by ocf_rdate desc  ");
		if( $rowChkDue['cnt'] > 0 && $rowCouponInfo['ocs_due_use'] != 'Y'){  echo json_encode(array('rst'=>'fail','msg'=>'해당 쿠폰은 중복으로 사용이 불가능합니다.')); exit; }

		// 다중창에서 실행 시 중복 검사 
		$rowChkDue2 = _MQ("select count(*) as cnt from smart_individual_coupon_form where ocf_coupuid = '".$couponUid."' and ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'  ");
		if( $rowChkDue2['cnt'] > 0){ echo json_encode(array('rst'=>'fail','msg'=>'이미 선택된 쿠폰입니다.')); exit;  }

		//  회원이 아니라면 
		if( is_login() == false){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit;  }

		// 쿠폰이 사용가능한지 체크 step1. 주문상품의 총금액 계산 ; $priceTotal 로하면 할인받은 금액 기준으로 체크를 하기때문에 안된다.
		if( ( $priceSum +  $priceDelivery + $priceAddDelivery ) < $rowCouponInfo['ocs_limit']){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit;  }	

		// 쿠폰 혜택
		if( in_array($rowCouponInfo['ocs_dtype'], array('price','per')) == false){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit; }

		// 쿠폰혜택에 따른 금액처리, $couponPrice 쿠폰금액, 쿠폰할인금액이 기준금액을 초과했을 시 1, 그렇지 않다면 기본 0
		$couponPrice = 0; $couponChkDrice = 0;
		if( $rowCouponInfo['ocs_dtype'] == 'per'){
			if( $rowCouponInfo['ocs_per'] == 0){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit; } // 0 일경우 사용 불가능

			if( $rowCouponInfo['ocs_boon_type'] == 'delivery'){	 // 배송비 혜택이 있을경우
				if( $priceDelivery < 0){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit; }  // 배송비가 없다면 사용할 수 없다.

				// 쿠폰할인금액 처리
				$couponPrice = ($priceDelivery + $priceAddDelivery)*$rowCouponInfo['ocs_per']/100;
				if( $couponPrice > $priceDelivery + $priceAddDelivery){ // 할인  배송비가 배송비 총합보다 크다면
					$couponPrice = $priceDelivery + $priceAddDelivery;
					$couponChkDrice ++;
				}

			 }else{ // discount => 주문할인금액 처리
			 	$couponPrice = ($priceSum +  $priceDelivery + $priceAddDelivery)*$rowCouponInfo['ocs_per']/100; // 배송비에 대한 금액처리(사용하지는 않는다.)
			 }

			if( $rowCouponInfo['ocs_price_max_use'] == 'Y'){ // 할인률 최대 설정 금액이 있을경우 처리
				$couponPrice = $couponPrice > $rowCouponInfo['ocs_price_max'] ? $rowCouponInfo['ocs_price_max'] : $couponPrice; // 최대 ~원까지 할인있을경우 처리 
			}

		}else if( $rowCouponInfo['ocs_dtype'] == 'price'){ // 금액일경우
			if( $rowCouponInfo['ocs_price'] == 0){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit; }

			if( $rowCouponInfo['ocs_boon_type'] == 'delivery'){	 // 배송비 혜택이 있을경우
				if( $priceDelivery < 0){ echo json_encode(array('rst'=>'fail','msg'=>'사용불가능한 쿠폰입니다.')); exit; }  // 배송비가 없다면 사용할 수 없다.

				$couponPrice = $rowCouponInfo['ocs_price']; // 쿠폰금액
				if( $couponPrice > $priceDelivery + $priceAddDelivery){ // 할인률 받은 배송비가 배송비 총합보다 크다면
					$couponPrice = $priceDelivery + $priceAddDelivery;
					$couponChkDrice ++;
				}
			 }

			// ------ {쿠폰원단위패치} ------ 2019-02-28 LCY
			else{
				if( ($priceSum +  $priceDelivery + $priceAddDelivery) < $rowCouponInfo['ocs_price']){
					$couponPrice = ($priceSum +  $priceDelivery + $priceAddDelivery); // 쿠폰금액
				}else{
					$couponPrice = $rowCouponInfo['ocs_price']; // 쿠폰금액
				}
			}
			// ------ {쿠폰원단위패치} ------ 2019-02-28 LCY

		}	

		// 적용할 쿠폰금액이 현재 결제해야할 주문금액보다 클경우 
		if( $couponPrice >  $priceTotal || $couponPrice > ($priceSum +  $priceDelivery + $priceAddDelivery) ){
			echo json_encode(array('rst'=>'fail','msg'=>'결제해야할 주문금액을 초과하여 적용이 불가능합니다.')); exit; 
		}

		// 쿠폰의 금액을 업데이트 시켜준다. 
		_MQ_noreturn(" update smart_individual_coupon set coup_price = '".$couponPrice."'  where coup_inid = '".get_userid()."' and coup_uid = '".$couponUid."'   ");

		// DB에 현재 정보 갱신
		couponFormInsert($couponUid);
		echo json_encode(array('rst'=>'success'));exit;

	break;

	case 'couponDelete': // return => JSON ::  사용자 쿠폰 삭제 시
		couponFormDelete($couponUid);
		echo json_encode(array('rst'=>'success'));exit;
	break;

	case 'couponDeleteAll': // return => JSON ::  사용자 쿠폰 삭제 시
		couponFormInit();
		echo json_encode(array('rst'=>'success'));exit;
	break;
	
	default:
		
	break;
}
