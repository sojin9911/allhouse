<?php
/*
- LDD

# 사용법
우선 후크액션을 실행 하기 위해서는 후크에 액션이 등록 되어있어야 합니다.
addHook('액션명', '실행할 함수');
처럼 액션을 추가 하고 액션이 실행될 위치에 다음과 같이 사용하면됩니다.
actionHook('액션명');

# 설명
기본적으로 /addons/hook/autoload 폴더 내부의 모든 php파일을 include하도록 되어있습니다.
조건별 파일명으로 분리 하여 관리 하시기 바랍니다.
해당 문서는 함수로만 이루어져야하며 함수 접두사 hook_을 추가 바랍니다.
또한, 후크 함수간 충돌을 피하기 위하여 function_exists() 조건도 추가 바랍니다.
후크 함수에 대한 설명은 자세히 주석에 추가 바라며 주석 접두사 (B)는 출력되지 않고 처리후 종료 되는 함수 (P)는 사용자페이지에 직접 출력됨, (S)는 (B), (P)를 위한 서브 함수를 말합니다.
*/
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/hook/hook.class.php');
define('HOOK_AUTOLOAD_PATH', $_SERVER['DOCUMENT_ROOT'].'/addons/hook/autoload');



# autoload의 php를 호출하여 include한다. {
$extend_file = array();
$tmp = dir(HOOK_AUTOLOAD_PATH);
while($entry = $tmp->read()) {
    if(preg_match("/(\.php)$/i", $entry)) $extend_file[] = $entry;
}
if(!empty($extend_file) && is_array($extend_file)) {
    natsort($extend_file);
    foreach($extend_file as $file) {
        include_once(HOOK_AUTOLOAD_PATH.'/'.$file);
    }
}
unset($extend_file);



# 후크 액션등록
addHook($HookFileName, 'hook_log_insert'); // inc.php가 호출되는 모든 페이지 후크등록

## 로그인/회원가입/SNS 로그인 시 로그인 업데이트는 - 각각의 hook 파일에서 처리하도록 하였음.



# 후크 액션실행 - 특별 케이스가 아닐경우 등록 금지
actionHook($HookFileName);
actionHook($pn);

