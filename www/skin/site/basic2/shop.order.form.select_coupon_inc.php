<?php if( count($arrAbailableInfo) < 1){ ?>
<li class="li_disabled_coupon_item">
	<!-- 선택전, 사용가능 쿠폰 없을때 no_coupon 클래스 추가 -->
	<span class="coupon_name no_coupon">사용 가능한 쿠폰이 없습니다.</span>
</li>
<?php 
	}else{ 

	// 선택된 쿠폰을 뿌려준다.
	$couponDiscountTotalPrice = $couponSaveTotalPrice = 0;
	$printSeleteCoupon = '';
	$arrSelectCoupon = array();
	foreach($resCouponForm as $k=>$v){

		$couponPrice = $v['coup_price'];

		// 적립일경우 와 할인,배송일 경우 처리
		if( $v['coup_type'] == 'save'){
			$couponSaveTotalPrice += $v['coup_price'];
		}else{
			$couponDiscountTotalPrice += $v['coup_price'];
		}

		// 각 할인데 따른 표기 
		if( $v['ocs_boon_type'] == 'discount'){												
			$couponBonnType = '(<strong>'.number_format($couponPrice).'원</strong> 할인)';
		}else if( $v['ocs_boon_type'] == 'save'){
			$couponBonnType = '(<strong>'.number_format($couponPrice).'원</strong> 적립)';

		}else if( $v['ocs_boon_type'] == 'delivery'){
			$couponBonnType = '(<strong>'.number_format($couponPrice).'원</strong> 배송비 할인)';
		}

		$couponTypeName = $arrCouponSet['ocs_type'][$v['ocs_type']];
		$couponName = stripslashes($v['ocs_name']);
		$arrSelectCoupon[] = $v['coup_uid']; // 이미 적용된 쿠폰 확인
		
		$printSeleteCoupon .='
			<li class="li_select_coupon_item" data-uid="'.$v['coup_uid'].'">
				<input type="hidden" name="use_coupon_member[]" value="'.$v['coup_uid'].'" />
				<span class="icon">'.$couponTypeName.'</span>
				<!-- 쿠폰유형/쿠폰명 -->
				<span class="coupon_name">
					'.$couponName.'
					<!-- 할인금액 -->
					<span class="coupon_price">'.$couponBonnType.'</span>
				</span>
				<div class="delete"><a href="#none" class="js_select_coupon_delete c_btn h22 light line" onclick="return false;" data-uid="'.$v['coup_uid'].'" title="적용취소">적용취소</a></div>
			</li>
		';		
	}
?>
<li class="li_available_coupon_item">
	<span class="txt">보유 쿠폰 <em><?php echo number_format(count($coupon_individual)); ?>장</em></span>
	<span class="txt">사용 가능 쿠폰 <span class="num js_available_coupon_totalcnt"><?php echo count($arrAbailableInfo); ?></strong>장</span></span>

	<?php if( count($resCouponForm) < count($arrAbailableInfo)) {  ?>
	<!-- 사용가능 쿠폰 없을때 셀렉트 '사용 가능한 쿠폰이 없습니다.' 문구 노출-->
	<select class='js_select_coupon_box'> <?php // 사용가능한 쿠폰이 없읅영우 disable 시키기 ?>
		<option value="">쿠폰 선택</option>
		<?php
			// $priceSum +  $priceDelivery + $priceAddDelivery 
			foreach($arrAbailableInfo as $k=>$rowCouponInfo){
				if( in_array($rowCouponInfo['coup_uid'], $arrSelectCoupon) == true){ continue; }
				$couponTypeName = $arrCouponSet['ocs_type'][$rowCouponInfo['ocs_type']];
				$couponName = stripslashes($rowCouponInfo['ocs_name']);
		?>
			<option value="<?php echo $rowCouponInfo['coup_uid']; ?>">(<?php echo $couponTypeName; ?>) <?php echo $couponName; ?></option>
		<?php }   ?>
	</select>
	<?php }else{ ?>
	<!-- 사용가능 쿠폰 없을때 셀렉트 '사용 가능한 쿠폰이 없습니다.' 문구 노출-->
	<select class='js_select_coupon_box_none' disabled=""> <?php // 사용가능한 쿠폰이 없읅영우 disable 시키기 ?>
		<option value="">사용 가능한 쿠폰이 없습니다.</option>
	</select>	
	<?php } ?>
</li>
<?php if(count($resCouponForm) < 1) { ?> 
<li class="li_none_select_coupon_item">
	<!-- 쿠폰 선택전 문구 -->
	<span class="coupon_name no_coupon">쿠폰을 선택해주세요.</span>
</li>
<?php } ?>



<?php } // 사용가능 쿠폰이 있을 시 에만 ?>

<?php echo $printSeleteCoupon; ?>


<?php // 데이터박스 ?>
<li class="coupon_form_ajax_data" style="display: none;">
	<input type="hidden" name="arrAbailableInfoCnt" value="<?php echo count($arrAbailableInfo) ?>"> <?php // 사용 가능한 쿠폰 개수 ?>
	<input type="hidden" name="arrDisableCouponUidCnt" value="<?php echo count($arrDisableCouponUid) ?>"> <?php // 사용 불가능한 쿠폰을 삭제처리한 개수  ?>
	<input type="hidden" name="couponDiscountTotalPrice" value="<?php echo $couponDiscountTotalPrice; ?>"> <?php // 할인금액 총액(주문할인+배송비할인)  ?>
	<input type="hidden" name="couponSaveTotalPrice" value="<?php echo $couponSaveTotalPrice; ?>"> <?php // 적립총액(주문적립)  ?>
	<input type="hidden" name="selectCouponAllChk" value="<?php echo count($resCouponForm) == count($arrAbailableInfo) ? 'true':'false'; ?>"> <?php // 전부 선택이 되었는지  ?>
</li>
<?php // ?>
