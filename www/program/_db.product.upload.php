<?php
// KAY :: 2021-07-02 :: 일괄업로드
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

$table = "smart_product_option_tmp";
$isTable = IsTable($table);
if($isTable === false){

	_MQ_noreturn("

		CREATE TABLE IF NOT EXISTS `smart_product_option_tmp` (
		  `pot_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
		  `pot_pucuid` int(11) NOT NULL COMMENT '옵션 업로드 고유번호',
		  `pot_pcode` varchar(20) NOT NULL COMMENT '적용할 상품코드',
		  `pot_info` text NOT NULL COMMENT '엑셀로 업로드한 상품 전체 옵션 줄임정보 - §(옵션구분), >(차수구분), |(항목구분)',
		  `pot_rdate` datetime NOT NULL COMMENT '등록일시',
		  PRIMARY KEY (`pot_uid`),
		  KEY `pot_pcode` (`pot_pcode`),
		  KEY `pot_pucuid` (`pot_pucuid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='엑셀 업로드 상품옵션 임시관리'

	");

	ViewArr('['.$table.'] 테이블이 추가되었습니다.');
	//ViewArr('정상적으로 DB가 수정되었습니다.');
}else{
	ViewArr('['.$table.'] 이미 추가된 테이블입니다.');
}


$table = "smart_product_upload_count";
$isTable = IsTable($table);
if($isTable === false){

	_MQ_noreturn("

		CREATE TABLE IF NOT EXISTS `smart_product_upload_count` (
		  `puc_uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호',
		  `puc_cnt` int(11) NOT NULL COMMENT '업로드상품수',
		  `puc_aid` varchar(50) NOT NULL COMMENT '관리자 아이디',
		  `puc_rdate` datetime NOT NULL COMMENT '등록일시',
		  PRIMARY KEY (`puc_uid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='엑셀 업로드 수량 관리'

	");

	ViewArr('['.$table.'] 테이블이 추가되었습니다.');
	//ViewArr('정상적으로 DB가 수정되었습니다.');
}else{
	ViewArr('['.$table.'] 이미 추가된 테이블입니다.');
}

