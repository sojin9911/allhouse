<?php
/*
	포인트 & 휴면 & 자동정산 처리 :: 하루 한번 실행
	/program/_auto_load.php 에서 1일 1회 실행
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// db항목체크
$table = 'smart_setup';
$column = 's_producteval_limit';
$isField = IsField($table, $column);
if($isField === false){
	// db항목 추가
	$column_data = array('Field'=>$column,'Type'=>'enum(\'Y\',\'N\',\'B\') CHARACTER SET utf8 COLLATE utf8_general_ci','Default'=>'N','Null'=>'NO','Extra'=>'COMMENT  \'상품후기 작성조건(Y:상품을 구매한 회원만, N: 모든회원 작성가능, B: 상품을 구매한횟수만큼)\'');
	AddFeidlUpdate($table,$column_data);

	error_loc_msg('/', '상품DB가 수정되었습니다.');
}else{
	error_loc_msg('/', '이미 실행된 파일입니다.');
}
