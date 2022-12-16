<?php //{{{회원쿠폰}}}
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 사용가능 쿠폰 목록을 가져오기 위한 처리 // LCY : 2021-11-10 : 쿠폰시작일/만료일 강화체크 추가 -- and if( ocs_use_date_type = 'date' , ocs_sdate <= curdate() , 1  ) and coup_expdate >= curdate()
$coupon_individual = _MQ_assoc(" select * from smart_individual_coupon as coup
	inner join smart_individual_coupon_set as ocs on(ocs.ocs_uid = coup.coup_ocs_uid)
	where coup_inid='".get_userid()."' and coup_use='N' and if( ocs_use_date_type = 'date' , ocs_sdate <= curdate() , 1  ) and coup_expdate >= curdate()  order by coup_rdate desc ");

$arrAbailableInfo = array(); // 사용가능한 쿠폰을 담을 변수
$arrDisableCouponUid = array(); // 사용불가능한 쿠폰을 담을 변수 ;; 삭제처리 하기위한 변수
foreach($coupon_individual as $k=>$rowCouponInfo){

	// 쿠폰이 사용가능한지 체크 step1. 주문상품의 총금액 계산 ; $priceTotal 로하면 할인받은 금액 기준으로 체크를 하기때문에 안된다.
	if( ( $priceSum +  $priceDelivery + $priceAddDelivery ) < $rowCouponInfo['ocs_limit']){ $arrDisableCouponUid[]= $rowCouponInfo['coup_uid']; continue; }

	// 쿠폰 혜택
	if( in_array($rowCouponInfo['ocs_dtype'], array('price','per')) == false){ $arrDisableCouponUid[]= $rowCouponInfo['coup_uid']; continue; }

	// 쿠폰혜택에 따른 금액처리, $couponPrice 쿠폰금액, 쿠폰할인금액이 기준금액을 초과했을 시 1, 그렇지 않다면 기본 0
	$couponPrice = 0; $couponChkDrice = 0;
	if( $rowCouponInfo['ocs_dtype'] == 'per'){
		if( $rowCouponInfo['ocs_per'] == 0){  $arrDisableCouponUid[]= $rowCouponInfo['coup_uid']; continue; } // 0 일경우 사용 불가능

		if( $rowCouponInfo['ocs_boon_type'] == 'delivery'){	 // 배송비 혜택이 있을경우
			if( $priceDelivery < 0){  $arrDisableCouponUid[]= $rowCouponInfo['coup_uid']; continue;  }  // 배송비가 없다면 사용할 수 없다.

			// 쿠폰할인금액 처리
			$couponPrice = ($priceDelivery + $priceAddDelivery)*$rowCouponInfo['ocs_per']/100;
			if( $couponPrice > $priceDelivery + $priceAddDelivery){ // 할인률 받은 배송비가 배송비 총합보다 크다면
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
		if( $rowCouponInfo['ocs_price'] == 0){ $arrDisableCouponUid[]= $rowCouponInfo['coup_uid']; continue; }

		if( $rowCouponInfo['ocs_boon_type'] == 'delivery'){	 // 배송비 혜택이 있을경우
			if( $priceDelivery < 0){  $arrDisableCouponUid[]= $rowCouponInfo['coup_uid']; continue;  }  // 배송비가 없다면 사용할 수 없다.

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
//		 $arrDisableCouponUid[]= $rowCouponInfo['coup_uid']; continue;
	}

	// 현재 쿠폰테이블에서 금액을 저장해준다.
	$rowCouponInfo['coup_price'] = $couponPrice;
	_MQ_noreturn(" update smart_individual_coupon set coup_price = '".$couponPrice."'  where coup_inid = '".get_userid()."' and coup_uid = '".$rowCouponInfo['coup_uid']."'   ");
	$arrAbailableInfo[] = $rowCouponInfo;
}

/*// 사용 불가능한 쿠폰은 자동 삭제처리
if( count($arrDisableCouponUid)  > 0){ _MQ_noreturn(" delete from smart_individual_coupon_form where find_in_set(ocf_coupuid,'".implode(",",$arrDisableCouponUid)."') > 0 and ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'   "); }*/

// 최종적으로 적용한 쿠폰 목록을 가져온다.
$resCouponForm = _MQ_assoc(" select * from `smart_individual_coupon_form` as ocf
	inner join smart_individual_coupon as coup on(coup.coup_uid = ocf.ocf_coupuid)
	inner join smart_individual_coupon_set as ocs on(ocs.ocs_uid = coup.coup_ocs_uid)
	where ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and coup_use='N' order by ocf_rdate desc  ");


@include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
//{{{회원쿠폰}}}