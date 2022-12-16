<?php 
	include_once $_SERVER['DOCUMENT_ROOT']."/include/inc.php";
	if( IsField('smart_company','cp_snumber') == true){ echo "이미 `DB 데이터`가 추가되었습니다.";exit; }
	// 추가완료
	_MQ_noreturn(" ALTER TABLE  smart_company ADD  cp_snumber VARCHAR( 50 ) NOT NULL COMMENT  '입점업체 통신판매업번호' AFTER  cp_number  ");
	
	echo " `DB 데이터` 추가가 완료되었습니다. ";
?>