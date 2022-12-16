<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$none_load_skin = true; // 스킨의 inc.header.php 호출을 차단
include_once(OD_PROGRAM_ROOT.'/inc.header.php');

actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


# 스킨폴더에서 해당 파일 호출
include_once($SkinData['skin_root'].'/'.basename(__FILE__));
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행