<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


# 기본처리
if(is_login()) error_loc("/");


# 데이터 조회
$msr =_MQ("SELECT * FROM smart_individual_sleep where in_id='{$_id}' ");
if(!$msr['in_id']) error_loc_msg("/" , "잘못된 접근입니다.");

$sleepData = array();

$sleepData['type'] = $siteInfo['member_return_type']; // auth : 이메일인증후 휴면해제, login : 이메일 인증없이 휴면해제
$sleepData['mode'] = $siteInfo['member_return_type'] == 'auth' ? 'send':'request';

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행