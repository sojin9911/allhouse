<?PHP
	include_once(dirname(__file__)."/inc.php");

	// 필수 변수 : $rowFavMenu 선택된 메뉴 정보
	// -- 메뉴 정보 
	if(!$rowFavMenu && $_uid){
		$rowFavMenu = _MQ(" select *from smart_favmenu where fm_uid = '".$_uid."' ");
	}

	// 이미 선택된 메뉴가 있다면 메뉴정보 추출
	$selectedMenu = array();
	if($rowFavMenu['fm_menu']){
		$que = "
			select *
			from smart_admin_menu as am
			left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
			where am_uid = '". $rowFavMenu['fm_menu'] ."'
			group by am.am_uid
			order by am_idx asc
		";
		$selectedMenu = _MQ($que);
		if($v['am_view'] <> 'Y') $v['am_name'] = $v['am_name'] . ' (미사용 메뉴)';
		else if($siteAdmin['a_uid']<>1 && $v['ams_uid']=='') $v['am_name'] = $v['am_name'] . ' (권한 없음)';
		else if($v['am_depth'] == 3 && $v['am_link']=='') $v['am_name'] = $v['am_name'] . ' (연결 페이지 없음)';
		if(!$selectedMenu){ // 메뉴가 삭제되었다면
			$selectedMenu = array('am_uid' => $rowFavMenu['fm_menu'], 'am_name' => '삭제된 메뉴');
		}

		// 1차메뉴, 2차메뉴 선택값 추출
		$ex = explode("," , $selectedMenu['am_parent']);
		$app_menu01 = $ex[0];
		$app_menu02 = $ex[1];
		$app_menu03 = $rowFavMenu['fm_menu'];
	}

	// -- 1차 메뉴 추출 ---
	$arr_menu01 = array();
	$que = "
		select *
		from smart_admin_menu as am
		left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
		where 1
			and am.am_depth = '1' 
		group by am.am_uid
		order by am_idx asc
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		if($v['am_view'] <> 'Y') $v['am_name'] = $v['am_name'] . ' (미사용 메뉴)';
		else if($siteAdmin['a_uid']<>1 && $v['ams_uid']=='') $v['am_name'] = $v['am_name'] . ' (권한 없음)';
		$arr_menu01[$v['am_uid']] = $v['am_name'];
	}
	// -- 1차 메뉴 추출 ---

	// -- 2차 메뉴 추출 ---
	$arr_menu02 = array();
	if($app_menu01){
		$que = "
			select *
			from smart_admin_menu as am
			left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
			where 1
				and am.am_depth = '2' 
				and find_in_set('". $app_menu01 ."' , am_parent) > 0
			group by am.am_uid
			order by am_idx asc
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){
			if($v['am_view'] <> 'Y') $v['am_name'] = $v['am_name'] . ' (미사용 메뉴)';
			else if($siteAdmin['a_uid']<>1 && $v['ams_uid']=='') $v['am_name'] = $v['am_name'] . ' (권한 없음)';
			$arr_menu02[$v['am_uid']] = $v['am_name'];
		}
	}
	// -- 2차 메뉴 추출 ---

	// -- 3차 메뉴 추출 ---
	$arr_menu03 = array();
	if($app_menu02){
		$que = "
			select *
			from smart_admin_menu as am
			left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
			where 1
				and am.am_depth = '3' 
				and find_in_set('". $app_menu02 ."' , am_parent) > 0
			group by am.am_uid
			order by am_idx asc
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){
			if($v['am_view'] <> 'Y') $v['am_name'] = $v['am_name'] . ' (미사용 메뉴)';
			else if($siteAdmin['a_uid']<>1 && $v['ams_uid']=='') $v['am_name'] = $v['am_name'] . ' (권한 없음)';
			else if($v['am_depth'] == 3 && $v['am_link']=='') $v['am_name'] = $v['am_name'] . ' (연결 페이지 없음)';
			$arr_menu03[$v['am_uid']] = $v['am_name'];

			// DB에저장된 메뉴가 추가되었는지 체크
			if($selectedMenu['am_uid'] == $v['am_uid']) $selectedMenu = array();
		}
	}
	// -- 3차 메뉴 추출 ---
	
	// 메뉴가 삭제되었다면 추가
	if($selectedMenu) $arr_menu03[$selectedMenu['am_uid']] = $selectedMenu['am_name'];
?>
<span class="fr_tx">1차 메뉴</span>
<?php echo _InputSelect( 'pass_menu01' , array_keys($arr_menu01) , $app_menu01 , 'id="pass_menu01" onchange="menu_select(1);" ' , array_values($arr_menu01) , '-선택-'); ?>
<span class="fr_tx">2차 메뉴</span>
<?php echo _InputSelect( 'pass_menu02' , array_keys($arr_menu02) , $app_menu02 , 'id="pass_menu02" onchange="menu_select(2);" ' , array_values($arr_menu02) , '-선택-'); ?>
<span class="fr_tx">3차 메뉴</span>
<?php echo _InputSelect( 'pass_menu03' , array_keys($arr_menu03) , $app_menu03, 'id="pass_menu03" ' , array_values($arr_menu03) , '-선택-'); ?>

<div class="tip_box">
	<div class="dash_line"><!-- 점선라인 --></div>
	<div class="c_tip">메뉴는 반드시 3차 메뉴까지 모두 선택하셔야 합니다.</div>
	<div class="c_tip">선택된 메뉴는 메뉴의 노출여부, 접속권한, 메뉴설정에따라 관리자 페이지 상단에 노출됩니다.</div>
	<div class="c_tip"><em>권한 없음</em> : [운영자별 메뉴관리]메뉴에서 관리자별 노출여부가 비노출로 설정된 메뉴입니다.</div>
	<div class="c_tip"><em>미사용 메뉴</em> : [Admin 메뉴관리]메뉴에서 노출여부가 비노출로 설정된 메뉴입니다.</div>
	<div class="c_tip"><em>삭제된 메뉴</em> : [Admin 메뉴관리]메뉴에서 삭제된 메뉴 입니다.</div>
	<div class="c_tip"><em>연결 페이지 없음</em> : [Admin 메뉴관리]메뉴 설정중 "파일명"설정이 되지 않아 연결할 페이지가 없는 메뉴 입니다.</div>
</div>