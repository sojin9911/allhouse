<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---

# 데이터 호출
$s_query = " where 1 ";
if($_GET['searchWord']) {
	$s_query_array = array();

	if( $_GET['searchMode'] == 't' || $_GET['searchMode'] == 'tc'){
		$search_tmp = explode(' ',$_GET['searchWord']);
		foreach($search_tmp as $skk=>$skv) { $s_query_array[] = " replace(bf_title,' ','') like '%".trim($skv)."%' "; }
	} 
	if( $_GET['searchMode'] == 'c' || $_GET['searchMode'] == 'tc'){
		$search_tmp = explode(' ',$_GET['searchWord']);
		foreach($search_tmp as $skk=>$skv) { $s_query_array[] = " replace(bf_content,' ','') like '%".trim($skv)."%' "; }	
	} 
	$s_query .= (sizeof($s_query_array) > 0 ? " and (".implode(' or ',$s_query_array).")" : ""); 
}


if( $_type != ''){
	$s_query .= " and bf_type = '".$_type."' ";	
}


$listmaxcount = 20;
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*)  as cnt from smart_bbs_faq {$s_query} ");
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);
$res = _MQ_assoc("
	select 
		*
	from smart_bbs_faq {$s_query}
	ORDER BY bf_uid desc
	limit $count , $listmaxcount 
");

$faqData = array();

// -- 공통으로 사용하기 위해 데이터 가공 {{{
$listFaq = array();
foreach($res as $k=>$v){

	$listFaq[$k]['row'] = $v; // 기본데이터를 담는다. 특수한 경우가 아닌이상 공통 변수 적용을 위해 사용하지 않는다.
	$listFaq[$k]['uid'] = $v['bf_uid']; // 고유번호
	$arrIcon = $arrTrClass = array(); //  배열 초기화 

	// 넘버링
	$listFaq[$k]['num'] = $TotalCount - ($TotalCount - ($count+($k+1)));

	$listFaq[$k]['title'] = htmlspecialchars(stripslashes($v['bf_title'])); // 게시물 제목
	$listFaq[$k]['content'] = stripslashes($v['bf_content']); // 게시물 제목
	$listFaq[$k]['type'] = $arrFaqBoardConfig['faqType'][$v['bf_type']];

	// -- 새글일 시
	if(time() - strtotime($v['bf_rdate'])< (60*60*24*$arrFaqBoardConfig['newIcon'])) {
		$arrIcon[] = '<img src="'.$SkinData['skin_url'].'/images/c_img/board_new.gif" alt="새글"/>'; 
		$listFaq[$k]['iconNew'] = true;
	}

	// -- 아이콘 출력
	$listFaq[$k]['icon'] = count($arrIcon) > 0 ? implode($arrIcon) : null;  // 아이콘 출력용 
} 
// -- 공통으로 사용하기 위해 데이터 가공 }}}


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 사이트 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행