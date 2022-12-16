<?PHP
include "./inc.php";

// 콤마제거
$_sPrice		= delComma($_sPrice);
$_screenPrice	= delComma($_screenPrice);
$_price			= delComma($_price);
$_stock			= delComma($_stock);
$_idx			= delComma($_idx);
$_salecnt		= delComma($_salecnt);
$_shoppingPay	= delComma($_shoppingPay);

// - 입력수정 사전처리 ---
if( in_array( $_mode , array("add" , "modify") ) ) {

	// --사전 체크 ---
	$_code = nullchk($_code , '상품코드를 입력해주시기 바랍니다.');
	$_cpid = nullchk($_cpid , '입점업체를 선택 해주시기 바랍니다.');
	$_name = nullchk($_name , '상품명을 입력해주시기 바랍니다.');
	$_price = nullchk($_price , '판매가를 입력해주시기 바랍니다.');
	// --사전 체크 ---

	$_content	= mysql_real_escape_string($_content);
	$_content_m	= mysql_real_escape_string($_content_m);
	$_name		= mysql_real_escape_string($_name);
	$_subname	= mysql_real_escape_string($_subname);
	$_orgin		= mysql_real_escape_string($_orgin);
	$_maker		= mysql_real_escape_string($_maker);
	$_coupon	= mysql_real_escape_string($_coupon_title) . '|' . $_coupon_price ;


	// 상품 판매일
	$_salePeriod = $_salePeriod_use == "Y" ? implode("|",$_salePeriod) : NULL;

	// 최대구매수량
	$_capablePerBuy = $_capableYN == "Y" ? $_capablePerBuy : NULL;

	// 최신상품평노출
	$_newEvalViewYN = $_newEvalViewYN == "N" ? $_newEvalViewYN : "Y";



	// --query 사전 준비 ---
	$sque = "
		  p_cpid					= '" . $_cpid . "'
		, p_stock					= '" . rm_str($_stock)  . "'
		, p_salecnt					= '" . rm_str($_salecnt)  . "'
		, p_screenPrice				= '" . rm_str($_screenPrice)  . "'
		, p_commission_type			= '" . $_commission_type  . "'
		, p_sPrice					= '" . rm_str($_sPrice)  . "'
		, p_sPersent				= '" . rm_str($_sPersent)  . "'
		, p_icon					= '". @implode(",",$_icon)."'
		, p_price					= '" . rm_str($_price)  . "'
		, p_point_per				= '" . preg_replace("/[^0-9.]/","",$_point_per)  . "'
		, p_name					= '" . $_name . "'
		, p_subname					= '" . $_subname . "'
		, p_view					= '" . $_view  . "'
		, p_coupon					= '" . $_coupon  . "'
		, p_shoppingPay_use			= '" . $_shoppingPay_use . "'
		, p_shoppingPay				= '" . $_shoppingPay . "'
		, p_shoppingPayFree			= '" . $_shoppingPayFree . "'
		, p_delivery_info			= '" . $_delivery_info . "'
		, p_orgin					= '" . $_orgin  . "'
		, p_maker					= '" . $_maker  . "'
		, p_sort_group				= '" . delComma($_sort_group) . "'
		, p_sort_idx				= '" . delComma($_sort_idx) . "'
		, p_idx						= '" . delComma($_idx) . "'
		, p_naver_switch			= '".$p_naver_switch."'
		, p_daum_switch				= '".$p_daum_switch."'
		, p_groupset_use			= '".($_groupset_use=='Y'?'Y':'N')."'
		, p_free_delivery_event_use	= '".($_free_delivery_event_use=='Y'?'Y':'N')."'
	";
	// --query 사전 준비 ---

	// 2017-06-16 ::: 부가세율설정 ::: JJC -- SSJ : 2018-02-08 복합과세일때만 변경 되도록
	if($p_vat <> '') $sque .= " , p_vat = '" . $p_vat . "' ";

	// JJC ::: 브랜드관리 ::: 2017-11-03
	$sque .= " , p_brand = '" . $_brand . "' ";
}
// - 입력수정 사전처리 ---



// - 모드별 처리 ---
switch( $_mode ){
	case "modify":
		$que = " update smart_product set $sque ,  p_mdate = now() where p_code='{$_code}' ";
		_MQ_noreturn($que);

		// 카테고리 상품 갯수 업데이트
		update_catagory_product_count();

		// SSJ : 2017-09-18 p_idx 재정렬
		product_resort();


		// JJC : 옵션 적합성 검사 - p_option_valid_chk 정보 업데이트 : 2018-04-16
		product_option_validate_check($_code);


		error_loc("_product.form.php?_mode=${_mode}&_code=${_code}&_PVSC=${_PVSC}");
	break;



	case "delete":
		// -- 상품정보 추출 ---
		$r = _MQ("select * from smart_product where p_code='${_code}' ");

		$arr_imgname = array('p_img_list_square', 'p_img_b1', 'p_img_b2', 'p_img_b3', 'p_img_b4', 'p_img_b5', 'p_img_list', 'p_img_list2');
		foreach($arr_imgname as $k=>$v){
			if($r[$v] <> '' && strpos($r[$v], '//') === false){
				// -- 이미지 삭제 ---
				_PhotoDel( $app_path , $r[$v] );
				_PhotoDel( $app_path , "thumbs_s_".$r[$v] );
				_PhotoDel( $app_path , "thumbs_b_".$r[$v] );
			}
		}
		// -- 이미지 삭제 ---

		// -- 옵션 삭제 ---
		_MQ_noreturn("delete from smart_product_option where po_pcode='{$_code}' ");

		// -- 추가옵션 삭제 ---
		_MQ_noreturn("delete from smart_product_addoption where pao_pcode='{$_code}' ");

		// -- 상품 적용 카테고리 삭제 ---
		_MQ_noreturn("delete from smart_product_category where pct_pcode='{$_code}' ");

		// -- 상품 정보제공고시 삭제 ---
		_MQ_noreturn("delete from smart_product_req_info where pri_pcode='{$_code}' ");

		// 이용정보 삭제
		_MQ_noreturn("delete from smart_table_text where ttt_tablename = 'smart_product' and ttt_datauid = '{$_code}' ");

		// 기획전 상품 삭제
		_MQ_noreturn("delete from smart_promotion_plan_product_setup where ppps_pcode = '{$_code}' ");

		// 상품정보 삭제
		_MQ_noreturn("delete from smart_product where p_code='{$_code}' ");

		// 카테고리 상품 갯수 업데이트
		update_catagory_product_count();

		// SSJ : 2017-09-18 p_idx 재정렬
		product_resort();

		error_loc("_product.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
	break;

	// - 선택순위수정 ---
	case "mass_sort":
		if(sizeof($chk_pcode) > 0){
			foreach($chk_pcode as $pcode=>$val){
				if($val == 'Y'){

					// 현재 상품 정보 추출
					$now = _MQ("select p_code, p_sort_group, p_sort_idx, p_idx from smart_product where p_code = '". $pcode ."' ");

					// 변경할 상품그룹 정보 추출
					$group = _MQ(" select max(p_sort_idx) max from smart_product where p_sort_group = '". $sort_group[$pcode] ."' ");

					// 상위그룹으로 변경시
					if($now['p_sort_group']>$sort_group[$pcode]){
						_MQ_noreturn("update smart_product set p_sort_group = '". $sort_group[$pcode] ."', p_sort_idx = '". ($group['max']+0.5) ."' where p_code = '".$now['p_code']."' ");
					}
					// 하위그룹으로 변경시
					else if($now['p_sort_group']<$sort_group[$pcode]){
						_MQ_noreturn("update smart_product set p_sort_group = '". $sort_group[$pcode] ."', p_sort_idx = '0.5' where p_code = '".$now['p_code']."' ");
					}

				}
			}
		}
		error_loc("_product.list.php?".enc('d' , $_PVSC));
	break;
}
// - 모드별 처리 ---
