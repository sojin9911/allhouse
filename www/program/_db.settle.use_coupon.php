<?php
/*
* [하이센스3.0] 정산 할인금액 패치  DB 수정
* http://{도메인}/program/_db.settle.use_coupon.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


// db항목체크
$table = 'smart_order_product';
$column = 'op_use_product_coupon';
$column_data = array('Field'=>$column , 'Type'=>'int(11)' , 'Null'=>'NO' , 'Default'=>'0' , 'Extra'=>'COMMENT \'상품쿠폰 사용액\' after op_use_discount_price');
$isField = IsField($table, $column);
if($isField === false){

	AddFeidlUpdate($table, $column_data);

	//ViewArr('['.$table.'->'.$column.'] 항목이 추가되었습니다.');
	ViewArr('정상적으로 DB가 수정되었습니다.');
}else{
	ViewArr('['.$table.'->'.$column.'] 이미 추가된 항목입니다.');
}


echo '<a href="/" style="">[홈으로]</a>';

