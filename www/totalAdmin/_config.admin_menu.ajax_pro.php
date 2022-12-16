<?php
	include_once("inc.php");

	if($_mode == ''){ echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

	switch($_mode){

		// -- 수정
		case "modify":
			// -- 해당 메뉴의 정보를 가져온다.
			$rowAdminMenu = _MQ("select *from smart_admin_menu where am_uid = '".$_uid."' ");
			if(count($rowAdminMenu) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'이름을 입력해 주세요.','key'=>'_name')); exit;}
			if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view')); exit;}

			// -- 링크 파일이 있을경우 유효한지 체크

			if( $_link != '' ) $chkLink = explode("?",$_link);

			if($_link != '' &&  is_file(OD_ADMIN_ROOT.'/'.$chkLink[0]) == false ){
				echo json_encode(array('rst'=>'fail-link','msg'=>'파일이 존재하지 않습니다.','key'=>'_link')); exit;
			}

			$addQue = " , am_link = '".$_link."'  ";

			// -- 수정불가능처리
			if( in_array($rowAdminMenu['am_link'],$arrAdminMenuNoneModify) == true){

				if($_link != $rowAdminMenu['am_link']){
					echo json_encode(array('rst'=>'fail-modify','msg'=>'해당 메뉴는 파일명 수정이 불가능합니다.')); exit;
				}

				if( $_view != $rowAdminMenu['am_view'] && $_view == 'N'){
					echo json_encode(array('rst'=>'fail-modify','msg'=>'해당 메뉴는 숨김처리가 불가능합니다.')); exit;
				}

			}

			_MQ_noreturn("update smart_admin_menu set am_view = '".$_view."' , am_name ='".addslashes($_name)."'  ".$addQue." where am_uid = '".$_uid."'  ");
			echo json_encode(array('rst'=>'success','msg'=>'메뉴가 저장되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 추가
		case "add":

			if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'이름을 입력해 주세요.','key'=>'_name')); exit;}
			if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view')); exit;}

			if( $_link != '' ) $chkLink = explode("?",$_link);

			if($_link != '' &&  is_file(OD_ADMIN_ROOT.'/'.$chkLink[0]) == false ){
			//if($_link != '' &&  is_file(OD_ADMIN_ROOT.'/'.$_link) == false ){
				echo json_encode(array('rst'=>'fail-link','msg'=>'파일이 존재하지 않습니다.','key'=>'_link')); exit;

			}

			$addQue = " , am_link = '".$_link."'  ";

		//$_parent = 0;
		$_parent = ''; // 2019-01-14 SSJ :: 초기 셋팅이 ''으로 되어있음 , 0으로 추가하면 순위변경 오류 발생
		if($_depth == 2){
			$_parent = $locUid1;
		}else if($_depth == 3){
			$_parent = $locUid1.",".$locUid2;
		}

		$rowIdx = _MQ("select ifnull(max(am_idx),0) + 1 as max_idx from smart_admin_menu where am_depth = '".$_depth."' ".($_depth > 1 ? "and am_parent = '".$_parent."' ":""));

		_MQ_noreturn("insert smart_admin_menu set am_view = '".$_view."' , am_name ='".addslashes($_name)."', am_depth = '".$_depth."', am_parent = '".$_parent."', am_idx = '".$rowIdx['max_idx']."'  ".$addQue." ");

		echo json_encode(array('rst'=>'success','msg'=>'메뉴가 추가되었습니다.','_depth'=>$_depth)); exit;

		break;


		// -- 삭제
		case "delete":
			// -- 해당 메뉴의 정보를 가져온다.
			$rowAdminMenu = _MQ("select *from smart_admin_menu where am_uid = '".$_uid."' ");
			if(count($rowAdminMenu) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			// -- 삭제불가능처리
			if( in_array($rowAdminMenu['am_link'],$arrAdminMenuNoneDelete) == true){
				echo json_encode(array('rst'=>'fail-delete','msg'=>'해당 메뉴는 삭제가 불가능합니다.')); exit;
			}

			_MQ_noreturn("delete from smart_admin_menu where am_uid = '".$_uid."' or find_in_set('".$_uid."',am_parent) > 0 ");

			echo json_encode(array('rst'=>'success','msg'=>'메뉴가 삭제되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 순서변경
		case "idx":

			if( $_type == 'up'){
		    // 정보 불러오기
		    $que  = " SELECT am_idx , am_depth , am_parent FROM smart_admin_menu WHERE am_uid = '$_uid' ";
		    $r = _MQ($que);

		    $_idx = $r[am_idx];
		    $_parent = $r[am_parent];
		    $_depth = $r[am_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_admin_menu WHERE am_idx = '$_idx' and am_parent='$_parent' ";
		        $r = _MQ($que);
		        if($r[cnt] > 1) {
		      _MQ_noreturn(" UPDATE smart_admin_menu SET am_idx = am_idx+1 WHERE am_idx >= '$_idx' and am_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MIN(am_idx),0) as minam_idx FROM smart_admin_menu WHERE am_parent='$_parent' ";
		    $r = _MQ($que);
		    $minam_idx = $r[minam_idx];

		    if ($minam_idx == $_idx) {
		       echo json_encode(array('rst'=>'fail','msg'=>'더이상 상위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계위 데이터와 am_idx 값 바꿈
		        $sque = "select am_idx , am_uid from smart_admin_menu WHERE am_parent='$_parent' and am_idx < '$_idx' order by am_idx desc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_admin_menu SET am_idx = $_idx WHERE am_uid='$sr[am_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_admin_menu SET am_idx = $sr[am_idx] WHERE am_uid = '$_uid' ");

		    }
		  }else if($_type == 'down'){

		    // 정보 불러오기
		    $que  = " SELECT am_idx , am_depth , am_parent FROM smart_admin_menu WHERE am_uid = '$_uid' ";
		    $r = _MQ($que);
		    $_idx = $r[am_idx];
		    $_parent = $r[am_parent];
		    $_depth = $r[am_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_admin_menu WHERE am_idx = '$_idx' and am_parent='$_parent' ";
		        $r = _MQ($que);

		        if($r[cnt] > 1) {
		      		_MQ_noreturn(" UPDATE smart_admin_menu SET am_idx = am_idx-1 WHERE am_idx <= '$_idx' and am_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MAX(am_idx),0) as maxam_idx FROM smart_admin_menu WHERE am_parent='$_parent' ";
		    $r = _MQ($que);
		    $maxam_idx = $r[maxam_idx];

		    if ($maxam_idx == $_idx) {
		    	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계 아래 데이터와 am_idx 값 바꿈
		        $sque = "select am_idx , am_uid from smart_admin_menu WHERE 1 and am_idx > '$_idx' ".($_depth != 1 ? " and am_parent='$_parent'  ":"" )." order by am_idx asc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_admin_menu SET am_idx = $_idx WHERE am_uid='$sr[am_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_admin_menu SET am_idx = $sr[am_idx] WHERE am_uid = '$_uid' ");
		        //	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }

		  }else{
		  	echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit;
		  }

			echo json_encode(array('rst'=>'success')); exit;
		break;

	}
?>