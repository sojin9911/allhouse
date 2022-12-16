<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

$table = array(
	0 => "smart_order",
	1 => "smart_setup",
);
$type = array(
	0 => " ADD ",
	1 => " ADD ",
);
$column = array(
	0 => "o_paycancel_method",
	1 => "s_paycancel_method",
);
$field = array(
	0 => " ENUM( 'B',  'D' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'B' COMMENT  '결제 취소시 포인트 환불 방식'",
	1 => " ENUM( 'B',  'D' ) NOT NULL DEFAULT  'B' COMMENT  '결제 취소시 포인트 환불 방식'",
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