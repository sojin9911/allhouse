<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


// -- 은행명 추출 ---
$arr_bank = array();
$ex = _MQ_assoc("select * from smart_bank_set order by bs_idx asc");
foreach( $ex as $k=>$v ){ 
	$arr_bank[rm_str($v['bs_bank_num'])] = '['. $v['bs_bank_name'] .'] ' . $v['bs_bank_num'] . ' ' . $v['bs_user_name'];
}


# 데이터 조회
$s_query = '';
$s_query .= " and on_view = 'Y' ";
if( $search_name !="" ) { $s_query .= " and on_name like '%". $search_name ."%' "; }
if( $search_date !="" ) { $s_query .= " and on_date = '". $search_date ."' "; }
else{ $s_query .= " and on_date between '". date('Y-m-d', strtotime('-'. ($siteInfo['s_online_notice_view']?$siteInfo['s_online_notice_view']:'3') .'days')) ."' and '". date('Y-m-d') ."' "; }

$listmaxcount = 20;
if(!$listpg) $listpg = 1;
$count = $listpg*$listmaxcount-$listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_online_notice where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount / $listmaxcount);
$res = _MQ_assoc(" select * from smart_online_notice where (1) {$s_query} order by on_uid desc limit {$count}, {$listmaxcount} ");




include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출 -> 디자인
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행