<?php

include_once('inc.php');


# 이미지 저장 위치
$dir = '../upfiles/product/';


# 이미지 복사 함수
if( !function_exists('_PhotoCopy') ) {
	function _PhotoCopy($name , $type = null){
		global $dir;

		if(strpos($name, '//') !== false) return $name; // # LDD015
		$ex_image_name = explode(".",$name); $app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); // 확장자
		$img_name = sprintf("%u" , crc32($name . time())) . "." . $app_ext ;
		@copy($dir.'/'.$name , $dir.'/'.$img_name);

		if($type == 's') {
			@copy($dir.'/thumbs_s_'.$name , $dir.'/thumbs_s_'.$img_name);
		}
		else if($type == 'b') {
			@copy($dir.'/thumbs_b_'.$name , $dir.'/thumbs_b_'.$img_name);
		}
		return $img_name;
	}
}


# 새로운정보 생성
$_code = shop_productcode_create(); // 상품 코드


# 기존정보 호출
$Product					= _MQ(" select `old`.*, '{$_code}' as `p_code` from `smart_product` as `old` where `p_code` = '{$pcode}' "); // 원 상품 정보
$ProductOption		= _MQ_assoc(" select `old`.*, '{$_code}' as `po_pcode` , 0 as `po_salecnt` from `smart_product_option` as `old` where `po_pcode` = '{$pcode}' and `po_depth` = '1' "); // 상품옵션 // JJC : 복사 시 옵션 판매량 0 적용 : 2020-07-02
$ProductAddOption	= _MQ_assoc(" select `old`.*, '{$_code}' as `pao_pcode` , 0 as `pao_salecnt` from `smart_product_addoption` as `old` where `pao_pcode` = '{$pcode}' and `pao_depth` = '1' "); // 1차 추가옵션 // JJC : 복사 시 추가옵션 판매량 0 적용 : 2020-07-02
$ProductCategory	= _MQ_assoc(" select `old`.*, '{$_code}' as `pct_pcode` from `smart_product_category` as `old` where `pct_pcode` = '{$pcode}' "); // 카테고리
$ProductReqInfo		= _MQ_assoc("select `old`.*, '{$_code}' as `pri_pcode` from `smart_product_req_info` as `old` where `pri_pcode` = '{$pcode}' "); // 상품정보제공고시
$ProductTableText	= _MQ_assoc("select `old`.*, '{$_code}' as `ttt_datauid` from `smart_table_text` as `old` where `ttt_tablename` = 'smart_product' and `ttt_datauid` = '{$pcode}' "); // 이용안내




# 상품이미지 복사 & 썸네일 생성
//$arr_imgname = array('p_img_list_square', 'p_img_b1', 'p_img_b2', 'p_img_b3', 'p_img_b4', 'p_img_b5', 'p_img_list', 'p_img_list2');
$arr_imgname = array('p_img_list_square', 'p_img_b1', 'p_img_b2', 'p_img_b3', 'p_img_b4', 'p_img_b5');
foreach($arr_imgname as $k=>$v){
	if(trim($Product[$v]) != '') {
		$Product[$v] = _PhotoCopy($Product[$v] , 's');
	}
}
if($Product['p_img_list_square']) {
	$Product['p_img_list'] = $Product['p_img_list_square'];
	$Product['p_img_list2'] = $Product['p_img_list_square'];
}


# 상품 추가
$Product['p_name'] = addslashes('[복사] '.$Product['p_name']); // 이름에 복사항목 문구 추가
$Product['p_subname'] = addslashes($Product['p_subname']); // 부제목 addslashes
$Product['p_content'] = addslashes($Product['p_content']); // 내용 addslashes
$Product['p_rdate'] = date('Y-m-d H:i:s', time()); // 등록일을 오늘로 변경
_MQ_noreturn(" insert into `smart_product` (`".implode('`, `', array_keys($Product))."`) values ('".implode("', '", $Product)."')"); // 원 상품 정보


# 상품옵션 추가
if(sizeof($ProductOption)>0){
	foreach($ProductOption as $k=>$v) {
		$pouid = $v['po_uid']; unset($v['po_uid']);

		_MQ_noreturn(" insert into `smart_product_option` (`".implode('`, `', array_keys($v))."`) values ('".implode("', '", $v)."')");
		$_uid = mysql_insert_id();

		// 2차 옵션
		$option2	= _MQ_assoc(" select `old`.*, '{$_code}' as `po_pcode` from `smart_product_option` as `old` where `po_pcode` = '{$pcode}' and `po_parent` = '". $pouid ."' and `po_depth` = '2' "); // 상품옵션
		if(sizeof($option2)>0){
			foreach($option2 as $sk=>$sv) {
				$pouid2 = $sv['po_uid']; unset($sv['po_uid']);
				$sv['po_parent'] = $_uid;

				_MQ_noreturn(" insert into `smart_product_option` (`".implode('`, `', array_keys($sv))."`) values ('".implode("', '", $sv)."')");
				$_uid2 = mysql_insert_id();

				// 3차 옵션
				$option3	= _MQ_assoc(" select `old`.*, '{$_code}' as `po_pcode` from `smart_product_option` as `old` where `po_pcode` = '{$pcode}' and `po_parent` = '". $pouid .",". $pouid2 ."' and `po_depth` = '3' "); // 상품옵션
				if(sizeof($option3)>0){
					foreach($option3 as $ssk=>$ssv) {
						unset($ssv['po_uid']);
						$ssv['po_parent'] = $_uid.','.$_uid2;

						_MQ_noreturn(" insert into `smart_product_option` (`".implode('`, `', array_keys($ssv))."`) values ('".implode("', '", $ssv)."')");
					}
				} // end if 3차 옵션
			}
		} // end if 2차 옵션
	}
}// end if 1차 옵션


# 추가옵션 추가
if(sizeof($ProductAddOption)>0){
	foreach($ProductAddOption as $k=>$v) {
		// 2차 추가옵션
		$addOption	= _MQ_assoc(" select `old`.*, '{$_code}' as `pao_pcode` from `smart_product_addoption` as `old` where `pao_pcode` = '{$pcode}' and `pao_parent` = '". $v['pao_uid'] ."' and `pao_depth` = '2' ");

		unset($v['pao_uid']);
		_MQ_noreturn(" insert into `smart_product_addoption` (`".implode('`, `', array_keys($v))."`) values ('".implode("', '", $v)."')");
		$_uid = mysql_insert_id();

		if(sizeof($addOption)>0){
			foreach($addOption as $sk=>$sv) {
				unset($sv['pao_uid']);
				$sv['pao_parent'] = $_uid;
				_MQ_noreturn(" insert into `smart_product_addoption` (`".implode('`, `', array_keys($sv))."`) values ('".implode("', '", $sv)."')");
			}
		}// end if 2차 추가옵션
	}
}// end if 1차 추가옵션


# 카테고리 추가
if(sizeof($ProductCategory)>0){
	foreach($ProductCategory as $k=>$v) {
		unset($v['pct_uid']);
		_MQ_noreturn(" insert into `smart_product_category` (`".implode('`, `', array_keys($v))."`) values ('".implode("', '", $v)."')");
	}
}


# 상품정보제공고시 추가
if(sizeof($ProductReqInfo)>0){
	foreach($ProductReqInfo as $k=>$v) {
		unset($v['pri_uid']);
		_MQ_noreturn(" insert into `smart_product_req_info` (`".implode('`, `', array_keys($v))."`) values ('".implode("', '", $v)."')");
	}
}


# 이용정보 추가
if(sizeof($ProductTableText)>0){
	foreach($ProductTableText as $k=>$v) {
		unset($v['ttt_uid']);
		_MQ_noreturn(" insert into `smart_table_text` (`".implode('`, `', array_keys($v))."`) values ('".implode("', '", $v)."')");
	}
}


// 카테고리 상품 갯수 업데이트
update_catagory_product_count();

// SSJ : 2017-09-18 p_idx 재정렬
product_resort();


// JJC : 옵션 적합성 검사 - p_option_valid_chk 정보 업데이트 : 2018-04-16
product_option_validate_check($_code);


// --- 2017-07-20 ::: 상품일괄수정관리 일 경우 사용을 위하여 이동 막기 ::: JJC ---
if( !($_mode == "mass_modify_category_copy" && $_submode == "mass_move") ){

	$_mode = 'modify';

	# 새로운 상품 수정 페이지로 이동
	error_loc("_product.form.php?_mode={$_mode}&_code={$_code}&_PVSC=${_PVSC}");

}