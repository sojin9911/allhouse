<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



# 데이터 조회 쿼리 및 페이징
echo 'FAQ 별도 프로그램';



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출 -> 디자인
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행