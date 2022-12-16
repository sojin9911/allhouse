<?php
# 스킨의 파일을 바로 부를 경우 사용
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
/*
	$AllCate -> 전체 카테고리 => (/program/wrap.header.php에서 지정)
	$ActiveCate -> 해당페이지 카테고리 => (/program/wrap.header.php에서 지정)
	$dp2cate -> 1차 기준 2차 카테고리 리스트 => (/program/wrap.header.php에서 지정)
	$dp3cate -> 2차 기준 3차 카테고리 리스트 => (/program/wrap.header.php에서 지정)
*/
// 현재 카테고리의 동일 위치 카테고리 => array(array('cuid'=>'cname'))
$SameCategory = (count($ActiveCate['cuid']) >= 2?$dp2cateName[$ActiveCate['cuid'][0]]:$dp1cateName); // 3차는 2차가 출력됨

// 현재 카태고리의 하위 카테고리 => array(array('cuid'=>'cname'))
$DownCategory = (count($ActiveCate['cuid']) == 3?$dp3cateName[$ActiveCate['cuid'][0]][$ActiveCate['cuid'][1]]:(count($ActiveCate['cuid']) == 2?$dp3cateName[$ActiveCate['cuid'][0]][$ActiveCate['cuid'][1]]:$dp2cateName[$ActiveCate['cuid'][0]]));

// 해당 카테고리의 2차카테고리 리스트
$Category2Depth = $dp2cate[$ActiveCate['cuid'][0]];

// 해당 카테고리의 3차 카테고리 리스트
$Category3Depth = array();
if(isset($ActiveCate['cuid'][1])) {
	$Category3Depth = $dp3cate[$ActiveCate['cuid'][1]];
}

include_once($SkinData['skin_root'].'/product.top_nav.php'); // 스킨폴더에서 해당 스킨 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행