<?php
	include_once('inc.php');

	if( in_array( $_mode , array("add" , "modify") ) ) {

		// 필수 입력폼 공백검사
		$_id = nullchk($_id , '아이디를 입력해주세요.');
		$_name = nullchk($_name , '이름을 입력해 주세요.');
		$_htel = nullchk($_htel , '핸드폰번호를 입력해 주세요.');
		$_email = nullchk($_email , '이메일을 입력해 주세요.');

		// 이메일 체크
		if(filter_var($_email, FILTER_VALIDATE_EMAIL) == ''){error_msg("올바른 이메일을 입력해 주세요."); }

		// 공통쿼리문
		$sque = "
			a_id = '".$_id."'
			, a_name = '".$_name."'
			, a_htel = '".tel_format($_htel)."'
			, a_email = '".$_email."'
			, a_job_title = '".$_job_title."'
			, a_corrosion_name = '".$_corrosion_name."'
			, a_use = '".$_use."'
			, a_mdate = now()
		 ";
	}
	switch($_mode){


		// -- 운영자 추가
		case "add":

			$arrUpdateQue = array();

			// -- 아이디 변경이 있었는지 체크
			if( $rowAdmin['a_id'] != $_id) {
				$rowAdminChk = _MQ("select count(*) as cnt from smart_admin where a_id = '".$_id."' ");
				if( $rowAdminChk['cnt'] > 0){ error_msg("이미 등록된 운영자 아이디 입니다.");  }
			}

			// -- 패스워드 변경에 체크가 되었을 시
			if(trim($_pw) == '') {  $_pw = nullchk($_pw , '비밀번호를 입력해 주세요.'); }
			if(trim($_rpw) == '') {   $_rpw = nullchk($_rpw , '비밀번호 확인을 입력해 주세요.'); }

			// -- 패스워드 규칙 체크
			if( $_pw != $_rpw){  error_msg("입력하신 비밀번호와 확인 비밀번호가 일치하지 않습니다."); }
			$arrUpdateQue[] = " a_pw = password('".$_pw."') ";
			if(count($arrUpdateQue) > 0){
				foreach($arrUpdateQue as $k=>$v){
					$sque .= ' , ' . $v;
				}
			}

			_MQ_noreturn("insert smart_admin set   ".$sque." , a_type = 'admin'  ,  a_rdate =  now() ");
			$adminUid = mysql_insert_id();

			error_loc_msg("_config.admin.form.php?_mode=modify&_uid=".$adminUid."&_PVSC=".$_PVSC,"운영자가 추가되었습니다.");
			exit;

		break;


		// -- 운영자 정보 수정
		case "modify":

			$rowAdmin = _MQ("select *from smart_admin where a_type = 'admin' and a_uid = '".$_uid."' ");
			if(count($rowAdmin) < 1){   }

			// -- 아이디 변경이 있었는지 체크
			if( $rowAdmin['a_id'] != $_id) {
				$rowAdminChk = _MQ("select count(*) as cnt from smart_admin where a_id = '".$_id."' and a_uid != '".$_uid."' ");
				if( $rowAdminChk['cnt'] > 0){ error_msg("이미 등록된 아이디 입니다."); }
			}

			// -- 패스워드 변경에 체크가 되었을 시
			if( $_changePw == 'Y'){
				if(trim($_pw) == '') { $_pw = nullchk($_pw , '비밀번호를 입력해 주세요.');  }
				if(trim($_rpw) == '') {  $_rpw = nullchk($_rpw , '비밀번호 확인을 입력해 주세요.'); }

				// -- 패스워드 규칙 체크
				if( $_pw != $_rpw){ error_msg("입력하신 비밀번호와 확인 비밀번호가 일치하지 않습니다."); }
				$arrUpdateQue[] = " a_pw = password('".$_pw."') ";
				if(count($arrUpdateQue) > 0){
					foreach($arrUpdateQue as $k=>$v){
						$sque .= ' , ' . $v;
					}
				}
			}

			_MQ_noreturn("update smart_admin set ".$sque." where a_type = 'admin' and a_uid = '".$_uid."'  ");
			error_loc("_config.admin.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC);
			exit;

		break;

	}

?>