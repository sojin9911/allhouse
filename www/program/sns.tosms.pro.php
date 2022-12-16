<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$_POST[textarea] = $_POST[fromName]."님의추천: ". $url . " " .$_POST[textarea];
$que = "insert into smart_sns_log set
				sl_pcode			=	'".$_POST[pcode]."',
				sl_type				=	'".$_POST[type]."',
				sl_ip					=	'".$_SERVER[REMOTE_ADDR]."',
				sl_rdate			=	now()";
$res = mysql_query($que);
$text_array = mb_cut_str($_POST[textarea],80,100);
for($i=1;$i<$text_array[0];$i++) {
	onedaynet_sms_send($_POST[toHp] , $_POST[fromHp] ,$text_array[$i]);
}





actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
error_msgPopup_s('친구에게 추천문자를 발송하였습니다.');