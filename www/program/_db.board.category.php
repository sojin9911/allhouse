<?php
/*
* SSJ : 게시판 카테고리 추가 DB 변경 : 2021-04-01
* -- http://smart.gobeyond.co.kr//program/_db.board.category.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


$desc = _MQ_assoc(" desc smart_bbs_info ");
$trigger = true;
foreach($desc as $k=>$v){
	if($v['Field'] == 'bi_category_use'){
		$trigger = false;
		break;
	}
}


if($trigger){

	$query = "
		ALTER TABLE smart_bbs_info
			ADD bi_category_use ENUM( 'Y', 'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'N' COMMENT '게시판 카테고리 사용여부',
			ADD bi_category VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '게시판 카테고리';
	";
	_MQ_noreturn($query);

	$query = "
		ALTER TABLE smart_bbs
			ADD b_category VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '게시판 카테고리';
	";
	_MQ_noreturn($query);

	error_loc_msg("/", "정상적으로 DB가 추가되었습니다.");
}else{
	error_loc_msg("/", "이미 추가된 항목입니다.");
}