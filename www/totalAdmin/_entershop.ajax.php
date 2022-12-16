<?php // -- LCY -- 입점업체관리 AJAX 처리
	include_once('inc.php');

	switch($ajaxMode){

		// -- 입점업체 탈퇴처리
		case "delete":
			$rowChk = _MQ("select *from smart_company where cp_id = '".$cpID."' ");
			if($rowChk['cp_id'] == ''){ echo json_encode(array('rst'=>'fail','msg'=>'입점업체 정보가 없습니다.')); exit; }
			if($rowChk['cp_id'] == 'hyssence' ){ echo json_encode(array('rst'=>'fail','msg'=>'기본 입점업체 계정은 삭제 할 수 없습니다.')); exit;  } // 관리자 계정 차단

			_MQ_noreturn("delete from smart_company where cp_id='{$cpID}' ");
			echo json_encode(array('rst'=>'success', 'msg'=>'선택하신 입점업체가 삭제 되었습니다.'));
			exit;

		break;

		// -- 입력값 체크
		case "inputChk":


			if($_mode == 'add'){ // 추가라면
				// -- 아이디 체크 
				$rowChk = _MQ("select count(*) as cnt from smart_company where 1 and cp_id = '".$_id."' ");
				if( $rowChk['cnt'] > 0){ echo json_encode(array('rst'=>'fail','key'=>'_id','msg'=>"이미 등록된 아이디 입니다.")); exit;  }
			}

			// -- 패스워드 입력이 있을경우 처리
			if( $_pw && $_repw ){
				if($_pw != $_repw){  echo json_encode(array('rst'=>'fail','key'=>'_pw','msg'=>"입력된 비밀번호가 서로 다릅니다.")); exit;  }
				if(mb_strlen($_pw) < 6){echo json_encode(array('rst'=>'fail','key'=>'_pw','msg'=>"비밀번호는 6자리이상 입력해 주세요.")); exit;  }
			} 

			if( empty($_tel) ){ echo json_encode(array('rst'=>'fail','key'=>'_tel','msg'=>"전화를 입력해 주세요.")); exit; }
			if( empty($_name) ){ echo json_encode(array('rst'=>'fail','key'=>'_name','msg'=>"업체명을 입력해 주세요.")); exit; }
			if( empty($_address) ){ echo json_encode(array('rst'=>'fail','key'=>'_address','msg'=>"주소를 입력해 주세요.")); exit; }
			if( empty($_email) ){ echo json_encode(array('rst'=>'fail','key'=>'_email','msg'=>"담당자 이메일을 입력해주세요.")); exit; }
			if( checkInputValue($_email,'email') !== true){ echo json_encode(array('rst'=>'fail','key'=>'_email','msg'=>'담당자 이메일의 형식이 올바르지 않습니다.')); exit;  }
			
			if($_delivery_use == 'Y' && $_del_addprice_use == 'Y'){}


			echo json_encode(array('rst'=>'success')); exit;

		break;

	}



?>