<?php
include_once('inc.php');


// 설정값 업데이트
$que = "
	update smart_setup set
		 s_display_pinfo_pc = '". (count($s_display_pinfo_pc)>1 ? implode('|', array_filter($s_display_pinfo_pc)) : $s_display_pinfo_pc) ."'
		,s_display_pinfo_mo = '". (count($s_display_pinfo_mo)>1 ? implode('|', array_filter($s_display_pinfo_mo)) : $s_display_pinfo_mo) ."'
		,s_display_pinfo_mo_use_pc = '". $s_display_pinfo_mo_use_pc ."'
		,s_display_pinfo_add = '". (is_array($s_display_pinfo_add) ? implode('|', array_filter($s_display_pinfo_add)) : $s_display_pinfo_add) ."'
		,s_display_relation_pc_use = '". $s_display_relation_pc_use ."'
		,s_display_relation_pc_col = '". $s_display_relation_pc_col ."'
		,s_display_relation_pc_row = '". $s_display_relation_pc_row ."'
		,s_display_relation_mo_use = '". $s_display_relation_mo_use ."'
		,s_display_relation_mo_col = '". $s_display_relation_mo_col ."'
		,s_display_relation_mo_row = '". $s_display_relation_mo_row ."'
	where s_uid = '1'
";
_MQ_noreturn($que);


// 설정페이지 이동
error_loc_msg('_config.display.pinfo.php', '정상적으로 저장되었습니다.');