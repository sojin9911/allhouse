<?php // -- LCY :: ADMIN -- 운영자관리 AJAX 처리
	include "./inc.php";

	switch($ajaxMode){
		
		// -- 운영자삭제
		case "DeleteAdmin":

			$rowChk = _MQ("select *from smart_admin where a_uid = '".$adminUid."' and a_id = '".$adminId."'  ");
			
			// -- 운영자 검색 실패 시
			if(count($rowChk) < 1){ echo json_encode(array('rst'=>'rail', 'msg'=>'해당 운영자 계정이 존재하지 않습니다.' )); exit; }

			// -- 최고관리자일경우 삭제 불가능 
			if($rowChk['a_type'] == 'master'){ echo json_encode(array('rst'=>'fail', 'msg'=>'마스터 등급의 운영자 계정은 삭제가 불가능합니다.')); exit; }

			_MQ_noreturn("delete from smart_admin where a_uid = '".$adminUid."' and a_id = '".$adminId."' and a_type != 'master'  ");

			echo json_encode(array('rst'=>'success',"msg"=>"해당 운영자 계정이 삭제되었습니다.","returnUrl"=>"_config.admin.list.php?".enc('d' , $_PVSC)));
			exit;

	break;
		// -- 선택 운영자삭제
		case "selectDeleteAdmin":

			if( count($adminUid) < 1){ echo json_encode(array('rst'=>'fail','msg'=>'삭제하실 운영자를 선택해 주세요.')); exit; }

			_MQ_noreturn("delete from smart_admin where find_in_set(a_uid, '".implode(",",$adminUid)."') > 0  and a_type != 'master'  ");
			echo json_encode(array('rst'=>'success',"msg"=>"해당 운영자 계정이 삭제되었습니다.","returnUrl"=>"_config.admin.list.php?".enc('d' , $_PVSC)));
			exit;

	break;

		
	}



?>