<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
if(!function_exists('GetFeidlUpdate')) {
	function GetFeidlUpdate($table,$type,$column,$field) {
		if( count($table) < 1 || count($type) < 1 || count($column) < 1 || count($field) < 1 ){ return falGetFeidlUpdatee; }
		_MQ_noreturn("ALTER TABLE ". $table . $type . $column . $field);
		
	}
}
$table = array(
	0 => "smart_setup",
);
$type = array(
	0 => " ADD ",
);
$column = array(
	0 => "s_none_member_buy",
);
$field = array(
	0 => " ENUM( 'Y',  'N' ) DEFAULT  'Y' COMMENT  '회원, 비회원 구매 가능 여부' ",
);

$isField = IsField($table[0], $column[0]);
if($isField === false){
	$i=0;
	GetFeidlUpdate($table[$i],$type[$i],$column[$i],$field[$i]);

	error_loc_msg('/', '상품DB가 수정되었습니다.');
}else{
	error_loc_msg('/', '이미 실행된 파일입니다.');
}

unset($i);
