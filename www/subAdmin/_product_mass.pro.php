<?PHP

	include "./inc.php";


	// 카테고리 변수 받기
	$_cuid = $pass_parent03_real ;



	// --- 2017-07-20 ::: _product.mass_modify.php 일괄수정을 위한 --- 선택 카테고리 배열화 ::: JJC ---
	function massModifyCategoryData(){
		$_tmpcode = "TMP01234"; // 임시 상품번호
		$arr_cuid = array();
		$que = "select pct_cuid from smart_product_category where pct_pcode='". $_tmpcode ."'";
		$r = _MQ_assoc($que);
		foreach( $r as $k=>$v ){
			$arr_cuid[] = $v['pct_cuid'];
		}
		return $arr_cuid;
	}
	// --- 2017-07-20 ::: _product.mass_modify.php 일괄수정을 위한 --- 선택 카테고리 배열화 ::: JJC ---




	// - 모드별 처리 ---
	switch( $_mode ){

		// ---------------- 상품가격 일괄수정 ----------------
		case "mass_price" :

			foreach($chk_pcode as $k => $v) {
				// query 생성
				$que = "
					update smart_product set
						p_screenPrice = '". $_screenPrice[$k] ."' ,
						p_price = '". $_price[$k] ."'
					where p_code = '". $k ."'
				";
				_MQ_noreturn($que);
				//echo $que . "<br>";

				// SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11
				product_soldout_check($k);
			}

			error_loc("_product_mass.price.php?".enc('d' , $_PVSC));

			break;
		// ---------------- 상품가격 일괄수정 ----------------


		// ---------------- 상품적립/쿠폰 일괄수정 ----------------
		case "mass_point" :

			foreach($chk_pcode as $k => $v) {

				$_coupon	= mysql_real_escape_string($_coupon_title[$k]) . "|" . $_coupon_price[$k] ;

				// query 생성
				$que = "
					update smart_product set
						p_point_per = '". $_point_per[$k] ."' ,
						p_coupon = '" . $_coupon  . "'
					where p_code = '". $k ."'
				";
				_MQ_noreturn($que);
				//echo $que . "<br>";
			}

			error_loc("_product_mass.point.php?".enc('d' , $_PVSC));

			break;
		// ---------------- 상품적립/쿠폰 일괄수정 ----------------


		// ---------------- 상품노출/재고관리 ----------------
		case "mass_view" :

			foreach($chk_pcode as $k => $v) {

				// query 생성
				$que = "
					update smart_product set
						p_stock = '". $_stock[$k] ."'
					where p_code = '". $k ."'
				";
				_MQ_noreturn($que);
				//echo $que . "<br>";
			}

			error_loc("_product_mass.view.php?".enc('d' , $_PVSC));

			break;
		// ---------------- 상품노출/재고관리----------------


		// ---------------- 상품옵션관리 ----------------
		case "mass_option" :

			foreach($chk_pcode as $k => $v) {

				// query 생성
				$que = "
					update smart_product_option set
						po_poptionprice = '". $_poptionprice[$k] ."' ,
						po_poption_supplyprice = '". $_poption_supplyprice[$k] ."' ,
						po_cnt = '". $_cnt[$k] ."' ,
						po_view = '". $_view[$k] ."'
					where po_uid = '". $k ."'
				";
				_MQ_noreturn($que);

			}

			error_loc("_product_mass.option.php?".enc('d' , $_PVSC));

			break;
		// ---------------- 상품옵션관리 ----------------






		// ---------------- 일괄수정 - 선택 상품의 전체 카테고리 제외 ----------------
		case "mass_modify_category_delete" :

			foreach($chk_pcode as $k => $v) {
				if(trim($k)) {
					_MQ_noreturn("delete from smart_product_category where pct_pcode='". $k ."' ");
				}
			}

			error_loc("_product_mass.move.php?".enc('d' , $_PVSC));

			break;
			// ---------------- 일괄수정 - 선택 상품의 전체 카테고리 제외 ----------------




		// ---------------- 일괄수정 - 선택 상품에 선택 카테고리 추가 ----------------
		case "mass_modify_category_add" :

			// --- 선택 카테고리 배열화 ---
			$arr_cuid = massModifyCategoryData();
			// --- 카테고리 갯수 체크 ---
			if( sizeof($arr_cuid) == 0 ) { error_msg("추가할 카테고리를 먼저 선택해주시기 바랍니다."); }

			// --- 선택 상품에 선택 카테고리 추가 ---
			foreach($chk_pcode as $k => $v) {
				if(trim($k)) {

					foreach($arr_cuid as $sk => $sv) {
						// 중복 배제 추가
						$que = "
							INSERT INTO smart_product_category (pct_pcode, pct_cuid) VALUES ('". $k ."', '". $sv ."')
							ON DUPLICATE KEY UPDATE pct_pcode='". $k ."' , pct_cuid='". $sv ."'
						";
						_MQ_noreturn($que);
					}
				}
			}
			// --- 선택 상품에 선택 카테고리 추가 ---

			error_loc("_product_mass.move.php?".enc('d' , $_PVSC));

			break;
			// ---------------- 일괄수정 - 선택 상품의 전체 카테고리 제외 ----------------




		// ---------------- 일괄수정 - 선택 상품의 선택 카테고리 삭제 ----------------
		case "mass_modify_category_seldel" :

			// --- 선택 카테고리 배열화 ---
			$arr_cuid = massModifyCategoryData();
			// --- 카테고리 갯수 체크 ---
			if( sizeof($arr_cuid) == 0 ) { error_msg("추가할 카테고리를 먼저 선택해주시기 바랍니다."); }

			// --- 선택 상품에 선택 카테고리 추가 ---
			foreach($chk_pcode as $k => $v) {
				if(trim($k)) {
					foreach($arr_cuid as $sk => $sv) {
						// 삭제
						$que = "delete from smart_product_category where pct_pcode='". $k ."' and pct_cuid='". $sv ."' ";
						_MQ_noreturn($que);
					}
				}
			}
			// --- 선택 상품에 선택 카테고리 추가 ---

			error_loc("_product_mass.move.php?".enc('d' , $_PVSC));

			break;
			// ---------------- 일괄수정 - 선택 상품의 선택 카테고리 삭제 ----------------


	}
	// - 모드별 처리 ---

	exit;
?>