<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$que = "insert into smart_sns_log set
				sl_pcode			=	'".$_GET[pcode]."',
				sl_type				=	'".$_GET[type]."',
				sl_ip				=	'".$_SERVER[REMOTE_ADDR]."',
				sl_rdate			=	now()";

$res = mysql_query($que);

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행