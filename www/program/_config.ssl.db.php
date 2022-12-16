<?php
/*
* 보안서버 설정 메뉴 관리자, PC, 모바일 삭제
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// db항목체크
$res = _MQ_noreturn("
	delete from smart_admin_menu
	where
		(
			am_link like '%_config.ssl.admin_form.php'
			or
			am_link like '%_config.ssl.pc_form.php'
			or
			am_link like '%_config.ssl.m_form.php'
		)
	");

// db항목체크
_MQ_noreturn("
	ALTER TABLE `smart_site_title` ADD `sst_desc` TEXT CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL COMMENT '페이지 Desc'
");


error_loc_msg('/', 'DB가 수정되었습니다.');
