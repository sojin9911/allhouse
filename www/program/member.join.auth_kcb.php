<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 로그인중이면 메인으로.
if(is_login()) error_loc('/');

// 본인인증 사용을 하고있지 않는 경우
if($siteInfo['s_join_auth_use'] != 'Y') {
	error_msg('잘못된 접근입니다.');
}


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행