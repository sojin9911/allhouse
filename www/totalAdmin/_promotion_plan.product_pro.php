<?PHP

	include "./inc.php";

	// 카테고리 추천상품 일괄삭제
	if( $smart_promotion_plan_product_setup_mode == "mass_delete" && $uid ) {

		// 상품순서 재정렬
		promotion_product_resort($uid);
		$que = " delete from smart_promotion_plan_product_setup where ppps_ppuid = '". $uid ."' and ppps_pcode in ('". implode("' , '" , array_filter(array_keys($chk_pcode))) ."') ";
		_MQ_noreturn($que);

?>


<script src="/include/js/jquery-1.11.2.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function() {
		parent.promotion_plan_product_setup_view('<?=$uid?>');
	});
</SCRIPT>

<?php

	}// 카테고리 추천상품 일괄삭제



	// 카테고리 추천상품 추가
	else {

		if(  sizeof($chk_pcode) > 0 && ISSET($uid) ) {

			// 이미 등록된 정보 있는지 확인 ::: 카테고리 - 상품
			$arr_duple = array();
			$ires = _MQ_assoc(" select ppps_pcode from smart_promotion_plan_product_setup where ppps_ppuid = '". $uid ."' and ppps_pcode in ('". implode("' , '" , array_filter(array_keys($chk_pcode))) ."') ");
			foreach($ires as $k=>$v){
				$arr_duple[$v['ppps_pcode']] = "Y";
			}

			// 이미 등록된 정보 제외한 상품 선별
			$arr_pcode = array_diff(array_filter(array_keys($chk_pcode)) , array_filter(array_keys($arr_duple)));

			// 데이터 등록
			if(sizeof($arr_pcode) > 0 ) {
				$que = " insert into smart_promotion_plan_product_setup ( ppps_ppuid , ppps_idx,ppps_sort_group,ppps_sort_idx,ppps_pcode ) values ( '". $uid ."' ,'0.5','100','0.5',  '". implode("') , ( '". $uid ."' ,'0.5','100','0.5',  '" , $arr_pcode) ."' ) ";
				//echo $que . "<hr>";
				_MQ_noreturn($que);
			}
			// 상품순서 재정렬
			promotion_product_resort($uid);

		}

?>


<script src="/include/js/jquery-1.11.2.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function() {
		parent.opener.promotion_plan_product_setup_view('<?=$uid?>');
		parent.close();
	});
</SCRIPT>

<?php

	}// 카테고리 추천상품 추가

	exit;
?>