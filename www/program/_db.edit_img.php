<?php
// KAY :: 2021-07-14 :: 에디터 이미지 관리
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

$table = "smart_editor_images_files";
$isTable = IsTable($table);
if($isTable === false){

	_MQ_noreturn("

		CREATE TABLE IF NOT EXISTS `smart_editor_images_files` (
			`eif_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '에디터 이미지 파일 고유번호',
			`eif_img` varchar(200) NOT NULL COMMENT '에디터 이미지 파일명',
			`eif_rdate` datetime NOT NULL COMMENT '에디터 이미지 파일 등록일',
			`eif_use_cnt` int(10) NOT NULL COMMENT '에디터 이미지 사용처 개수',
			PRIMARY KEY (`eif_uid`),
			UNIQUE KEY `eif_img` (`eif_img`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='에디터 이미지 파일 관리' 

	");

	ViewArr('['.$table.'] 테이블이 추가되었습니다.');
	//ViewArr('정상적으로 DB가 수정되었습니다.');
}else{
	ViewArr('['.$table.'] 이미 추가된 테이블입니다.');
}


$table = "smart_editor_images_use";
$isTable = IsTable($table);
if($isTable === false){

	_MQ_noreturn("

		CREATE TABLE IF NOT EXISTS `smart_editor_images_use` (
			`eiu_uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '에디터 이미지 사용관리 고유번호',
			`eiu_tablename` varchar(50) NOT NULL DEFAULT '' COMMENT '대상 테이블명',
			`eiu_datauid` varchar(50) DEFAULT NULL COMMENT '대상 데이터 고유번호',
			`eiu_eifuid` int(20) DEFAULT NULL COMMENT '에디터이미지 파일 uid',
			`eiu_dummy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '더미',
			PRIMARY KEY (`eiu_uid`),
			UNIQUE KEY `eiu_unique` (`eiu_tablename`,`eiu_datauid`,`eiu_eifuid`),
			KEY `eiu_eifuid` (`eiu_eifuid`),
			KEY `eiu_tablename` (`eiu_tablename`),
			KEY `eiu_datauid` (`eiu_datauid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='에디터 이미지 사용처 관리' 

	");

	ViewArr('['.$table.'] 테이블이 추가되었습니다.');
	//ViewArr('정상적으로 DB가 수정되었습니다.');
}else{
	ViewArr('['.$table.'] 이미 추가된 테이블입니다.');
}

