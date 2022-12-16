<?php
/*
* [하이센스3.0 결제취소파일 일원화 패치]  DB 수정
* http://{도메인}/program/_db.auth.add_enckey.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


// db항목체크
$table = 'smart_setup';
$column = 's_join_auth_kcb_enckey';
$column_data = array('Field'=>$column , 'Type'=>'varchar(255)' , 'Null'=>'NO' , 'Default'=>'' , 'Extra'=>'COMMENT \'본인인증 암호화키\'');
$isField = IsField($table, $column);
if($isField === false){

	AddFeidlUpdate($table, $column_data);

	//ViewArr('['.$table.'->'.$column.'] 항목이 추가되었습니다.');
	ViewArr('정상적으로 DB가 수정되었습니다.');
}else{
	ViewArr('['.$table.'->'.$column.'] 이미 추가된 항목입니다.');
}


echo '<a href="/" style="">[홈으로]</a>';

