<?php
define('_OD_DIRECT_', true); // 개별 실행방지
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__)); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once(OD_PROGRAM_ROOT.'/_auto_load.php'); // 사이트 접속시 실행되는 include 파일 모음 (자동처리 파일들 호출 - 데일리 프로세스 포함)


# 스킨 호출(pc, mobile경로 처리는 /include/inc.path.php 에서 처리)
if(!file_exists(OD_PROGRAM_ROOT.'/'.$pn.'.php') && !file_exists($SkinData['skin_root'].'/'.$pn.'.php')) error_msg('잘못된 접근입니다.');
if($pn != 'intro') include_once(OD_PROGRAM_ROOT.'/wrap.header.php');
if(file_exists(OD_PROGRAM_ROOT.'/'.$pn.'.php')) include_once(OD_PROGRAM_ROOT.'/'.$pn.'.php');
else include_once(OD_PROGRAM_ROOT.'/_skin_direct.php'); // program폴더에 파일이 없으나 스킨폴더에는 파일이 있는 경우 직접 호출
if($pn != 'intro') include_once(OD_PROGRAM_ROOT.'/wrap.footer.php');