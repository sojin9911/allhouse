<?PHP
	include "./inc.php";

	$app_path = "../upfiles";

	// 체크박스 Y/N 값
	$m_status = $m_status!='Y'?'N':'Y';
	$a_status = $a_status!='Y'?'N':'Y';
	$mk_status = ($mk_status?$mk_status:'N');
	$ak_status = ($ak_status?$ak_status:'N');


	/***** 회원 발송 설정값 입력 *****/ unset($tmp,$s_query,$_file);
	$tmp = _MQ("select * from smart_sms_set where ss_uid = '".$uid."'");
	$s_query = "

		  ss_status		= '".$m_status."'
		, ss_text		= '".$m_text."'
		, ss_title		= '".$m_title."'

		, kakao_status = '{$mk_status}'
		, kakao_templet_num = '{$mk_kakao_templet_num}'
		, kakao_add1 = '{$mk_kakao_add1}'
		, kakao_add2 = '{$mk_kakao_add2}'
		, kakao_add3 = '{$mk_kakao_add3}'
		, kakao_add4 = '{$mk_kakao_add4}'
		, kakao_add5 = '{$mk_kakao_add5}'
		, kakao_add6 = '{$mk_kakao_add6}'
		, kakao_add7 = '{$mk_kakao_add7}'
		, kakao_add8 = '{$mk_kakao_add8}'

	";
	if($tmp[ss_uid]) {
		if(!$_FILES['m_file'] && !$m_file_OLD) { _PhotoDel( $app_path , $tmp['ss_file'] ); $_file = ''; }
		else { $_file = _PhotoPro( $app_path , 'm_file' ); }
		$s_query .= " , ss_file = '".$_file."' ";
		_MQ_noreturn(" update smart_sms_set set " . $s_query . " where ss_uid = '".$uid."' ");
	} else {
		$_file = _PhotoPro( $app_path , 'm_file' );
		$s_query .= " , ss_file = '".$_file."' , ss_uid = '".$uid."' ";
		_MQ_noreturn(" insert smart_sms_set set " . $s_query);
	}



	/***** 관리자 발송 설정값 입력 *****/ unset($tmp,$s_query,$_file);
	$tmp = _MQ("select * from smart_sms_set where ss_uid = 'admin_".$uid."'");
	$s_query = "

		  ss_status		= '".$a_status."'
		, ss_text		= '".$a_text."'
		, ss_title		= '".$a_title."'

		, kakao_status = '{$ak_status}'
		, kakao_templet_num = '{$ak_kakao_templet_num}'
		, kakao_add1 = '{$ak_kakao_add1}'
		, kakao_add2 = '{$ak_kakao_add2}'
		, kakao_add3 = '{$ak_kakao_add3}'
		, kakao_add4 = '{$ak_kakao_add4}'
		, kakao_add5 = '{$ak_kakao_add5}'
		, kakao_add6 = '{$ak_kakao_add6}'
		, kakao_add7 = '{$ak_kakao_add7}'
		, kakao_add8 = '{$ak_kakao_add8}'
	";
	if($tmp[ss_uid]) {
		if(!$_FILES['a_file'] && !$a_file_OLD) { _PhotoDel( $app_path , $tmp['ss_file'] ); $_file = ''; }
		else { $_file = _PhotoPro( $app_path , 'a_file' ); }
		$s_query .= " , ss_file = '".$_file."' ";
		_MQ_noreturn(" update smart_sms_set set " . $s_query . " where ss_uid = 'admin_".$uid."' ");
	} else {
		$_file = _PhotoPro( $app_path , 'a_file' );
		$s_query .= " , ss_file = '".$_file."' , ss_uid = 'admin_".$uid."' ";
		_MQ_noreturn(" insert smart_sms_set set " . $s_query);
	}


	error_loc_msg("_config.sms.form.php?menu_idx=".$menu_idx."&_uid=".$uid , "성공적으로 저장되었습니다.");
	exit;
?>