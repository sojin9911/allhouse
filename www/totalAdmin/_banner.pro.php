<?php
include_once('inc.php');

// 기본처리 ------------------------------------------------------
if(in_array($_mode, array('add', 'modify'))) {

	// --사전 체크 ---
	$b_loc = nullchk($b_loc, '배너구분을 선택해주세요.');
	if($b_none_limit == 'N') {
		$b_sdate = nullchk($b_sdate, '시작일을 선택해주세요.');
		$b_edate = nullchk($b_edate, '종료일을 선택해주세요.');
	}

	if($b_target == '_none') $b_link = ''; // 링크없음 타입이라면 링크 지움
	if(!$b_link) $b_target = '_none'; // 링크가 없다면 타겟 타입을 링크 없음으로 변경

	// --이미지 처리 ---
	$_imgname = _PhotoPro('../upfiles/banner', 'b_img') ; // 배너이미지

	// --query 사전 준비 ---
	$sque = "
		  b_site_skin = '{$b_site_skin}'
		, b_loc ='{$b_loc}'
		, b_img = '{$_imgname}'
		, b_link ='{$b_link}'
		, b_target ='{$b_target}'
		, b_view ='{$b_view}'
		, b_title ='{$b_title}'
		, b_info ='{$b_info}'
		, b_sub_info ='{$b_sub_info}'
		, b_color = '{$b_color}'
		, b_idx = '{$b_idx}'
		, b_none_limit = '{$b_none_limit}'
		, b_sdate = '{$b_sdate}'
		, b_edate = '{$b_edate}'
	";
}

// 쿼리처리 ------------------------------------------------------
if($_mode == 'skin_banner_loc') { // 스킨에 따른 배너리스트

	// 초기화 및 호출
	unset($skin_pc_banner_loc, $skin_mobile_banner_loc); // inc.path.php 에서 include_once되었기 때문에 unset후 변수를 include하여 호출
	$result = array();
	if(file_exists(OD_SKIN_ROOT.'/site/'.$s_skin.'/_var.php')) include(OD_SKIN_ROOT.'/site/'.$s_skin.'/_var.php'); // PC 스킨 _var.php 호출 (/skin/site/*/_var.php)
	if(file_exists(OD_SKIN_ROOT.'/site_m/'.$s_skin.'/_var.php')) include(OD_SKIN_ROOT.'/site_m/'.$s_skin.'/_var.php'); // PC 스킨 _var.php 호출 (/skin/site_m/*/_var.php)


	// 공통배너 /include/var.php
	if(count($arr_banner_loc_common)) $result = array_merge($result, $arr_banner_loc_common);


	// 스킨 PC 배너 /skin/site/*/_var.php
	if(count($skin_pc_banner_loc) > 0) {
		foreach($skin_pc_banner_loc as $k=>$v) {
			$skin_pc_banner_loc[$s_skin.','.$k] = $v;
			unset($skin_pc_banner_loc[$k]);
		}
		$result = array_merge($result, $skin_pc_banner_loc);
	}


	// 스킨 Mobile 배너 /skin/site_m/*/_var.php
	if(count($skin_mobile_banner_loc) > 0) {
		foreach($skin_mobile_banner_loc as $k=>$v) {
			$skin_mobile_banner_loc[$s_skin.','.$k] = $v;
			unset($skin_mobile_banner_loc[$k]);
		}
		$result = array_merge($result, $skin_mobile_banner_loc);
	}

	die(json_encode($result));
}
else if($_mode == 'add') { // 배너추가

	_MQ_noreturn(" insert smart_banner set {$sque}, b_rdate = now() ");
	$_uid = mysql_insert_id();
	error_loc("_banner.form.php?_mode=modify&s_skin={$s_skin}&s_loc={$s_loc}&_uid={$_uid}&_PVSC={$_PVSC}");
}
else if($_mode == 'modify') { // 배너수정

	_MQ_noreturn(" update smart_banner set {$sque} where b_uid = '{$_uid}' ");
	error_loc("_banner.form.php?_mode=modify&s_skin={$s_skin}&s_loc={$s_loc}&_uid={$_uid}&_PVSC={$_PVSC}");
}
else if($_mode == 'delete') { // 배너삭제

	// -- 이미지 삭제 ---
	$r = _MQ(" select b_img from smart_banner where b_uid = '{$_uid}' ");
	if($r['b_img']) _PhotoDel('../upfiles/banner', $r['b_img']);
	// -- 이미지 삭제 ---

	_MQ_noreturn(" delete from smart_banner where b_uid = '{$_uid}' ");
	error_loc('_banner.list.php'.URI_Rebuild('?'.enc('d', $_PVSC)));
}