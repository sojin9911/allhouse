<?php
	include_once("inc.php");

	if($_mode == ''){ echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

	switch($_mode){

		// -- 수정
		case "modify":
			// -- 해당 메뉴의 정보를 가져온다.
			$rowAdminMenu = _MQ("select *from smart_favmenu where fm_uid = '".$_uid."' ");
			if(count($rowAdminMenu) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'이름을 입력해 주세요.','key'=>'_name')); exit;}
			if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view')); exit;}

			$addQue = " , fm_menu = '".$pass_menu03."'  ";

			_MQ_noreturn("update smart_favmenu set fm_view = '".$_view."' , fm_menuName ='".addslashes($_name)."'  ".$addQue." where fm_uid = '".$_uid."'  ");
			echo json_encode(array('rst'=>'success','msg'=>'메뉴가 저장되었습니다.','_depth'=>$_depth,'_uid'=>$_uid)); exit;

		break;

		// -- 추가
		case "add":

			if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'이름을 입력해 주세요.','key'=>'_name')); exit;}
			if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view')); exit;}

			// -- 링크 파일이 있을경우 유효한지 체크
			if($_link != '' &&  is_file(OD_ADMIN_ROOT.'/'.$_link) == false ){
				echo json_encode(array('rst'=>'fail-link','msg'=>'파일이 존재하지 않습니다.','key'=>'_link')); exit;

			}

			$addQue = " , fm_menu = '".$pass_menu03."'  ";

		$_parent = 0;
		if($_depth == 2){
			$_parent = $locUid1;
		}else if($_depth == 3){
			$_parent = $locUid1.",".$locUid2;
		}

		$rowIdx = _MQ("select ifnull(max(fm_menuIdx),0) + 1 as max_idx from smart_favmenu where fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_depth = '".$_depth."' ".($_depth > 1 ? "and fm_parent = '".$_parent."' ":""));

		_MQ_noreturn("insert smart_favmenu set fm_appId = 'totaladmin' , fm_admin = '". $siteAdmin['a_uid'] ."' , fm_view = '".$_view."' , fm_menuName ='".addslashes($_name)."', fm_depth = '".$_depth."', fm_parent = '".$_parent."', fm_menuIdx = '".$rowIdx['max_idx']."' , fm_rdate = now()  ".$addQue." ");
		$_uid = mysql_insert_id();

		echo json_encode(array('rst'=>'success','msg'=>'메뉴가 추가되었습니다.','_uid'=>$_uid,'_depth'=>$_depth)); exit;

		break;


		// -- 삭제
		case "delete":
			// -- 해당 메뉴의 정보를 가져온다.
			$rowAdminMenu = _MQ("select *from smart_favmenu where fm_uid = '".$_uid."' ");
			if(count($rowAdminMenu) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			_MQ_noreturn("delete from smart_favmenu where fm_uid = '".$_uid."' or find_in_set('".$_uid."',fm_parent) > 0 ");

			echo json_encode(array('rst'=>'success','msg'=>'메뉴가 삭제되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 순서변경
		case "idx":
			if( $_type == 'up'){
				// 정보 불러오기
				$que  = " SELECT fm_menuIdx , fm_depth , fm_parent FROM smart_favmenu WHERE fm_uid = '". $_uid ."' ";
				$r = _MQ($que);

				$_idx = $r['fm_menuIdx'];
				$_parent = $r['fm_parent'];
				$_depth = $r['fm_depth'];

				// 같은 순위의 값이 있는지 체크///////////////////////////
				$que  = " SELECT count(*) as cnt FROM smart_favmenu WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_menuIdx = '". $_idx ."' and fm_parent='". $_parent ."' ";
				$r = _MQ($que);
				if($r['cnt'] > 1) {
					_MQ_noreturn(" UPDATE smart_favmenu SET fm_menuIdx = fm_menuIdx+1 WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_menuIdx >= '". $_idx ."' and fm_parent='". $_parent ."' ");
				}

				// 최소 순위  찾기 //////////////////////////////////////////
				$que  = " SELECT ifnull(MIN(fm_menuIdx),0) as minfm_menuIdx FROM smart_favmenu WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_parent='". $_parent ."' ";
				$r = _MQ($que);

				$minfm_menuIdx = $r['minfm_menuIdx'];

				if ($minfm_menuIdx == $_idx) {
				   echo json_encode(array('rst'=>'fail','msg'=>'더이상 상위로 이동할 수 없습니다.')); exit;
				}
				else {

					// 바로 한단계위 데이터와 fm_menuIdx 값 바꿈
					$sque = "select fm_menuIdx , fm_uid from smart_favmenu WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_parent='". $_parent ."' and fm_menuIdx < '". $_idx ."' order by fm_menuIdx desc limit 1";
					$sr = _MQ($sque);

					_MQ_noreturn(" UPDATE smart_favmenu SET fm_menuIdx = '". $_idx ."' WHERE fm_uid='". $sr['fm_uid'] ."'");

					// 순서값 제거 - 자신의 순서값
					_MQ_noreturn(" UPDATE smart_favmenu SET fm_menuIdx = '". $sr['fm_menuIdx'] ."' WHERE fm_uid = '". $_uid ."' ");

				}
			}else if($_type == 'down'){

				// 정보 불러오기
				$que  = " SELECT fm_menuIdx , fm_depth , fm_parent FROM smart_favmenu WHERE fm_uid = '". $_uid ."' ";
				$r = _MQ($que);
				$_idx = $r['fm_menuIdx'];
				$_parent = $r['fm_parent'];
				$_depth = $r['fm_depth'];

				// 같은 순위의 값이 있는지 체크///////////////////////////
				$que  = " SELECT count(*) as cnt FROM smart_favmenu WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_menuIdx = '". $_idx ."' and fm_parent='". $_parent ."' ";
					$r = _MQ($que);

					if($r['cnt'] > 1) {
						_MQ_noreturn(" UPDATE smart_favmenu SET fm_menuIdx = fm_menuIdx-1 WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_menuIdx <= '". $_idx ."' and fm_parent='". $_parent ."' ");
					}

				// 최소 순위  찾기 //////////////////////////////////////////
				$que  = " SELECT ifnull(MAX(fm_menuIdx),0) as maxfm_menuIdx FROM smart_favmenu WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_parent='". $_parent ."' ";
				$r = _MQ($que);
				$maxfm_menuIdx = $r[maxfm_menuIdx];

				if ($maxfm_menuIdx == $_idx) {
					echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
				}
				else {

					// 바로 한단계 아래 데이터와 fm_menuIdx 값 바꿈
					$sque = "select fm_menuIdx , fm_uid from smart_favmenu WHERE fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_menuIdx > '". $_idx ."' ".($_depth != 1 ? " and fm_parent='". $_parent ."'  ":"" )." order by fm_menuIdx asc limit 1";
					$sr = _MQ($sque);

					_MQ_noreturn(" UPDATE smart_favmenu SET fm_menuIdx = '". $_idx ."' WHERE fm_uid='". $sr['fm_uid'] ."'");

					// 순서값 제거 - 자신의 순서값
					_MQ_noreturn(" UPDATE smart_favmenu SET fm_menuIdx = ". $sr['fm_menuIdx'] ." WHERE fm_uid = '". $_uid ."' ");
					//	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
				}

			}else{
				echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit;
			}

			echo json_encode(array('rst'=>'success')); exit;
			break;

		case "select_admin_menu":
				// - 2단 분류 선택시 ---
				if(  $pass_menu02 && $pass_idx == 2 ){
					$que = "
						select *
						from smart_admin_menu as am
						left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
						where 1
							and am.am_depth = '3'
							and find_in_set('". $pass_menu02 ."' , am_parent) > 0
						group by am.am_uid
						order by am_idx asc
					";
				}
				// - 2단 분류 선택시 ---

				//  - 1단분류 ---
				else if( $pass_menu01 && $pass_idx == 1 ) {
					$que = "
						select *
						from smart_admin_menu as am
						left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
						where 1
							and am.am_depth = '2'
							and find_in_set('". $pass_menu01 ."' , am_parent) > 0
						group by am.am_uid
						order by am_idx asc
					";
				}
				//  - 1단분류 ---

				$res = mysql_query($que);
				$str = "";
				for( $i=0; $v = mysql_fetch_assoc($res); $i++){
					// 메뉴 유효성검사
					if($v['am_view'] <> 'Y') $v['am_name'] = $v['am_name'] . ' (미사용 메뉴)';
					else if($siteAdmin['a_uid']<>1 && $v['ams_uid']=='') $v['am_name'] = $v['am_name'] . ' (권한 없음)';
					else if($v['am_depth'] == 3 && $v['am_link']=='') $v['am_name'] = $v['am_name'] . ' (연결 페이지 없음)';
					$arr_menu03[$v['am_uid']] = $v['am_name'];

					if($i <> 0) {
						$str .= ' , ';
					}
					$str .= '{"optionValue" : "' . $v['am_uid'] . '" , "optionDisplay" : "' . $v['am_name'] . '"}';
					$cnt ++;
				}
				echo '[' . $str . ']';
			break;
	}
?>