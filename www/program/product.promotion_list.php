<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행






	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---



	$s_query = " from smart_promotion_plan where pp_view = 'Y' ";

	if(!$listmaxcount) $listmaxcount = 10;
	$listpg = $listpg ? $listpg : 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);


	$s_orderby = "ORDER BY pp_uid DESC";
	$res = _MQ_assoc(" SELECT * $s_query $s_orderby  LIMIT $count , $listmaxcount ");






include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 스킨 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행