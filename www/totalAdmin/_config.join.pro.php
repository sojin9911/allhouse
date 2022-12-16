<?php
include_once('inc.php');

// --  이메일 항목에 @ 또는 입력항목 공백을 제거
$join_email_list = preg_replace("/[@|\s]/","",$join_email_list );
$join_ban_id = preg_replace("/[\s]/","",$join_ban_id );

$sque = " join_approve = '".$join_approve."' "; // 가입승인
$sque .= " , join_ban_id = '".$join_ban_id."' ";  // 가입제한 아이디

$sque .= " , join_id_limit_min = '".$join_id_limit_min."' "; // 아이디 최소 길이
$sque .= " , join_id_limit_max = '".$join_id_limit_max."' "; //아이디 최대 길이

$sque .= " , join_pw_limit_min = '".$join_pw_limit_min."' "; // 비밀번호 최소 길이
$sque .= " , join_pw_limit_max = '".$join_pw_limit_max."' "; // 비밀번호 최대 길이


$sque .= " , join_pw_sp_use = '".$join_pw_sp_use."' "; // 특수문자 혼용 필수여부
$sque .= " , join_pw_sp_length = '".$join_pw_sp_length."' "; // 특수문자 혼용 필수여부가 사용일 시 몇개 포함인지

$sque .= " , join_pw_up_use = '".$join_pw_up_use."' "; // 대문자 혼용 필수여부
$sque .= " , join_pw_up_length = '".$join_pw_up_length."' "; // 대문자 혼용 필수여부가 사용일시 몇개이상 포함인지

$sque .= " , join_email_list = '".$join_email_list."' "; // 이메일 리스트 

$sque .= " , join_tel = '".$join_tel."' "; 
$sque .= " , join_tel_required = '".$join_tel_required."' ";

$sque .= " , join_addr = '".$join_addr."' ";
$sque .= " , join_addr_required = '".$join_addr_required."' ";

$sque .= " , join_birth = '".$join_birth."' ";
$sque .= " , join_birth_required = '".$join_birth_required."' ";

$sque .= " , join_sex = '".$join_sex."' ";
$sque .= " , join_sex_required = '".$join_sex_required."' ";

$sque .= " , join_spam = '".$join_spam."' ";

_MQ_noreturn("update smart_setup set ".$sque." where s_uid = '1' ");

// 설정페이지 이동
error_loc('_config.join.php');
