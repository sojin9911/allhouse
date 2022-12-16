<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
$table = array(
	0 => "smart_product",
	1 => "smart_product",
	2 => "smart_product",
	3 => "smart_setup",
	4 => "smart_order_product",
);
$type = array(
	0 => " ADD ",
	1 => " ADD ",
	2 => " CHANGE ",
	3 => " ADD ",
	4 => " CHANGE "
);
$column = array(
	0 => "p_shoppingPayPdPrice",
	1 => "p_shoppingPayPfPrice",
	2 => "p_shoppingPay_use  p_shoppingPay_use",
	3 => "s_del_addprice_use_product",
	4 => "op_delivery_type op_delivery_type",
	
);
$field = array(
	0 => " INT( 11 ) NOT NULL DEFAULT  0 COMMENT  '상품별 배송비 - 상품별 기본배송비' AFTER p_shoppingPayFree",
	1 => " INT( 11 ) NOT NULL DEFAULT  0 COMMENT  '상품별 배송비 - 무료배송비' AFTER  p_shoppingPayPdPrice",
	2 => " ENUM(  'Y',  'N',  'F',  'P' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '상품 배송비정책 사용여부'",
	3 => " ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N' COMMENT  '상품별배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비 적용여부' AFTER  s_del_addprice_use_normal",
	4 => " ENUM(  '입점',  '개별',  '무료',  '상품별' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '입점' COMMENT  '상품별 배송비 타입'",
	
);
$isField = IsField($table[0], $column[0]);
if($isField === false){
	for($i=0; $i<count($table); $i++){
		GetFeidlUpdate($table[$i],$type[$i],$column[$i],$field[$i]);
	}
	error_loc_msg('/', '상품DB가 수정되었습니다.');
}else{
	error_loc_msg('/', '이미 실행된 파일입니다.');
}

function GetFeidlUpdate($table,$type,$column,$field) {
	if( count($table) < 1 || count($type) < 1 || count($column) < 1 || count($field) < 1 ){ return false; }
    _MQ_noreturn("ALTER TABLE ". $table . $type . $column . $field);
	
}