<?php
/*
* 2019-11-11 SSJ :: 출석체크 DB 수정
* 달성조건 진행도 표시 항목 추가 -- ex) 2/5
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// db항목체크
$res = _MQ_assoc(" desc smart_promotion_attend_log ");
$arrField = array();
if(count($res) > 0){
	foreach($res as $k=>$v){
		$arrField[] = $v['Field'];
	}
}
if(in_array('atl_addinfo_days_count', $arrField)){
	error_loc_msg('/', '이미 실행된 파일입니다.');
}else{
	_MQ_noreturn(" ALTER TABLE `smart_promotion_attend_log` ADD `atl_addinfo_days_count` INT( 11 ) NOT NULL COMMENT '달성조건 - 출석일수중 몇번째인지' ");
	error_loc_msg('/', 'DB가 수정되었습니다.');
}
