<?PHP
	include "./inc.php";


	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {

		// --사전 체크 ---
		$_title = nullchk($_title , "메일링 제목을 입력해주시기 바랍니다.");
		$_content = nullchk($_content , "메일링 내용을 입력해주시기 바랍니다.");
		// --사전 체크 ---

		// {{{URL경로변경}}}
		if( trim($_content) != ''){
			$_content = preg_replace("/(http:\/\/".$_SERVER['HTTP_HOST']."\/upfiles\/smarteditor\/)/","/upfiles/smarteditor/",$_content);
			$_content = preg_replace("/(\/upfiles\/smarteditor\/)/","http://".$_SERVER['HTTP_HOST']."/upfiles/smarteditor/",$_content);
		}



		$_title = mysql_real_escape_string($_title);
		$_content = mysql_real_escape_string($_content);


		// --query 사전 준비 ---
		$sque = "
			 md_title = '". $_title ."'
			,md_content = '". $_content ."'
            ,md_adchk = '".$_adchk."'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){
		case "add":
			$que = " insert smart_mailing_data set $sque , md_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();

			// KAY :: 에디터 이미지관리 :: 에디터 이미지 등록함수 :: 2021-06-02 ---------
			editor_img_ex($_content , 'mailing' , $_uid);
			error_loc_msg("_mailing_data.form.php?_mode=modify&_uid=". $_uid . "&_PVSC=${_PVSC}", "정상적으로 등록되었습니다.");
			break;

		case "modify":
			$que = " update smart_mailing_data set $sque where md_uid='{$_uid}' ";
			_MQ_noreturn($que);

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 등록함수 :: 2021-06-02 --------
			editor_img_ex($_content , 'mailing' , $_uid);
			error_loc_msg("_mailing_data.form.php?_mode=${_mode}&_uid=". $_uid . "&_PVSC=${_PVSC}", "정상적으로 수정되었습니다.");
			break;

		case "delete":

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del($_uid,'mailing');

			_MQ_noreturn("delete from smart_mailing_data where md_uid='{$_uid}' ");
			error_loc_msg("_mailing_data.list.php?".enc('d' , $_PVSC ), "정상적으로 삭제되었습니다.");
			break;
	}
	// - 모드별 처리 ---

?>