<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---

//if(!is_login()) error_loc('/');
//$_id  = get_userid(); // 회원의 아이디 
//if($_ckval <> sha1(date('H')) || $_id == '') error_loc('/');

// 기본적으로 한달의 기간을 준다.
_MQ_noreturn("update smart_individual set in_pw_rdate = '".date('Y-m-d H:i:s',strtotime("- ". ($siteInfo['member_cpw_period']-1) ." month"))."' where in_id = '".$_id."' ");



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행