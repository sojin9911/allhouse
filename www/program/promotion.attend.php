<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 개인정보 추출
if(is_login()) $indr = $mem_info;

// 오늘 날짜 설정
$today_date = date('Y-m-d');

// 선택 날짜 설정
$selected = $selected ? $selected : date("Y-m-d");
$selected_year = date('Y', strtotime($selected));
$selected_month = date('m', strtotime($selected));
$selected_month_digit = date('n', strtotime($selected));
$selected_month_string = date('F', strtotime($selected));
$selected_day = date('d', strtotime($selected));
$selected_date = date('Y-m-d', strtotime($selected));

// 이벤트 정보 설정
$event_info = get_promotion_attend_event();
//ViewArr($event_info);
$event_uid = $event_info['atc_uid'];
$event_start = $event_info['atc_sdate'];
$event_end = $event_info['atc_edate'];

// 이벤트 진행여부
if( count($event_info) > 0 && sizeof($event_info['info']) > 0) $event_trigger = true;
else $event_trigger = false;


if($event_trigger) { 

	// 오늘 출석체크 했는지 여부
	$today_status = false;
	$today_done = _MQ_result(" select count(*) from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_date = '".$today_date."' and atl_event = '".$event_uid."' ");
	$chk_total_cnt = _MQ_result(" select count(*) from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_event = '".$event_uid."' ");
	$chk_series_cnt = _MQ_result(" select count(*) from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_status = 'Y' and atl_event = '".$event_uid."' ");
	if( $today_done > 0 ) { $today_status = true; }

	// 어제 출석했는지 체크 - 어제 출석하지 않았다면 연속출석이깨짐
	$chk_series_valid = _MQ_result(" select count(*) as cnt from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_status = 'Y' and atl_event = '".$event_uid."' and atl_date = '". date("Y-m-d", strtotime("-1day")) ."'  ");
	// 연속출석이벤트 시작일
	if($chk_series_valid>0){
		$app_first_eday = _MQ_result(" select atl_date from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_status = 'Y' and atl_event = '".$event_uid."' order by atl_date asc limit 1 ");
	}else{
		$app_first_eday = $today_date; // 연속출석이 아니면 오늘이 연속출석 시작일
	}

	// 연속출석 예상 획득일/획득포인트 추출
	$arr_next_day = array();
	$_app_mccdays = '';
	foreach($event_info['info'] as $__k=>$__v){
		$__d = date("Y-m-d", strtotime("+".($__v['mca_days']-1)."days" , strtotime($app_first_eday)));
		$arr_next_day[$__d] = $__v['mca_point'];

		// 연속출석일수 TEXT 추출
		if($_app_mccdays) $_app_mccdays .= ', ';
		$_app_mccdays .= $__v['mca_days'].'일 ';
	}


	// 선택일과 이벤트시작일/종료일 월비교
	$prev_month = $next_month = false;
	$chk_month = date('Y-m', strtotime($selected));
	$chk_month_start = date('Y-m', strtotime($event_start));
	$chk_month_end = date('Y-m', strtotime($event_end));
	if($chk_month <> $chk_month_start){ 
		$prev_month = true; 
		$prev_month_date = date("Y-m-d", strtotime("-1month", strtotime($selected) ));
	}
	if($chk_month <> $chk_month_end){ 
		$next_month = true; 
		$next_month_date = date("Y-m-d", strtotime("+1month", strtotime($selected) ));; 
	}

	// 타이틀 이미지 
	if(is_mobile()){
		$title_img = get_img_src($event_info['atc_img_mo'], IMG_DIR_BANNER);
		if($title_img == '') get_img_src($event_info['atc_img_pc'], IMG_DIR_BANNER);
	}else{
		$title_img = get_img_src($event_info['atc_img_pc'], IMG_DIR_BANNER);
	}

}else{
	// 준비중 이미지 
	if(is_mobile()){
		$ready_img = get_img_src($siteInfo['s_promotion_attend_reay_mo'], IMG_DIR_BANNER);
		if($ready_img == '') get_img_src($siteInfo['s_promotion_attend_reay_pc'], IMG_DIR_BANNER);
	}else{
		$ready_img = get_img_src($siteInfo['s_promotion_attend_reay_pc'], IMG_DIR_BANNER);
	}
}




include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행