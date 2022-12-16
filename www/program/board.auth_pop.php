<?php 
# 게시글 처리 프로세스
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 스킨 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
?>