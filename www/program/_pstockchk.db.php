<?php
/*
	포인트 & 휴면 & 자동정산 처리 :: 하루 한번 실행
	/program/_auto_load.php 에서 1일 1회 실행
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// db항목체크
$table = 'smart_product';
$column = 'p_soldout_chk';
$isField = IsField($table, $column);
if($isField === false){
	// db항목 추가
	$column_data = array('Field'=>$column,'Type'=>'enum(\'Y\',\'N\') CHARACTER SET utf8 COLLATE utf8_general_ci','Default'=>'N','Null'=>'NO','Extra'=>'COMMENT  \'상품품절체크\', ADD INDEX (  `'.$column.'` )');
	AddFeidlUpdate($table,$column_data);

	// 모든상품 추출
	$res = _MQ_assoc(" select p_code from smart_product where 1 ");
	foreach($res as $k=>$v){ // 전체상품 일괄 업데이트
		product_soldout_check($v['p_code']);
	}

	error_loc_msg('/', '상품DB가 수정되었습니다.');
}else{
	error_loc_msg('/', '이미 실행된 파일입니다.');
}
