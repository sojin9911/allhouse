<?php # LCY :: 2017-12-06 회원그룹 정책 저장프로세서 
	include_once('inc.php');
	/*
		// -- 필요칼럼
		- groupset_autouse
		- groupset_apply_rdate
		- groupset_auto_daily
		- groupset_check_term
	*/

	switch($_mode){

		// -- 회원등급 정책 설정 수정 시 :: 일반
		case "modify":
			_MQ_noreturn("update smart_setup set groupset_autouse = '".$groupset_autouse."', groupset_auto_daily = '".$groupset_auto_daily."', groupset_check_term = '".$groupset_check_term."' where s_uid = 1 " );
		
		error_frame_loc("_config.group.php");

		break;

		case "groupset_apply":
			define('MEMBER_GROUPSET_APPLY',true); // 실행 기호상수 선언
			include_once OD_PROGRAM_ROOT.'/inc.member_groupset_auto.php';
			if($updateResult === true){
				error_frame_loc_msg("_config.group.php","회원등급평가가 수동으로 처리되었습니다.");
			}else{
				error_frame_loc_msg("_config.group.php","회원등급평가 수동처리에 실패하였습니다.");
			}
		break;

	}
?>