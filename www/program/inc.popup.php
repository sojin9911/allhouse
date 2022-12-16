<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$res = _MQ_assoc("
	select
		*
	from
		smart_popup
	where (1)
		and p_view = 'Y'
		and (p_sdate <= curdate() and p_edate >= curdate() or p_none_limit = 'Y')
		and (p_type = 'A' or p_type = '".(is_mobile()?'M':'P')."')
	order by p_idx asc, p_uid desc
");
if(count($res) <= 0) $res = array();
include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행