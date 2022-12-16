<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


# 기본처리
member_chk(); // 로그인 체크
if(is_login()) $indr = $mem_info; // 개인정보 추출


# 데이터 조회
$s_query = '';
$s_query .= " and p_view = 'Y' ";
$s_query .= " and pt_type='상품문의' ";
$s_query .= " and pt_inid = '".get_userid()."' ";
$s_query .= " and pt_depth= 1 ";
if(isset($search_word) && $search_word != '') {
	if($search_type == 'search_title') $s_query .= " and `pt_title` like '%{$search_word}%' ";
	if($search_type == 'search_content') $s_query .= " and `pt_content` like '%{$search_word}%' ";
	else $s_query .= " and (`pt_title` like '%{$search_word}%' or `pt_content` like '%{$search_word}%') ";
}
$listmaxcount = 10;
if(!$listpg) $listpg = 1;
$count = $listpg*$listmaxcount-$listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_product_talk as pt inner join smart_product as p on (pt.pt_pcode = p.p_code) inner join smart_individual as ind on (ind.in_id = pt.pt_inid) where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount / $listmaxcount);
$row = _MQ_assoc(" select * from smart_product_talk as pt inner join smart_product as p on (pt.pt_pcode = p.p_code) inner join smart_individual as ind on (ind.in_id = pt.pt_inid) where (1) {$s_query} ORDER BY pt_rdate desc, pt_uid desc limit {$count}, {$listmaxcount} ");



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행