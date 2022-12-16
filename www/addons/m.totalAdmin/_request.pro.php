<?PHP

	include_once( dirname(__FILE__)."/inc.php");


	if( in_array( $_mode , array('add' , 'modify') ) ){

		// --사전 체크 ---
		$_menu = nullchk($_menu , "메뉴를 선택해주시기 바랍니다.");
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = "
			 r_menu = '". $_menu ."'
			,r_comname = '". $_comname ."'
			,r_email = '". $_email ."'
			,r_tel = '". $_tel ."'
			,r_hp = '". $_hp ."'
			,r_status = '". $_status ."'
			,r_title = '". $_title ."'
			,r_content = '". $_content ."'
			,r_admcontent = '". $_admcontent ."'
		";
		if($_menu == "normal_pt" && $_title ) {
			$sque .= " ,r_title = '". $_title ."' ";
		}
		// --query 사전 준비 ---

	}

	// 문의하기 정보 추출
	$r = _MQ(" select * from smart_request where r_uid='{$_uid}' ");


	// - 모드별 처리 ---
	switch( $_mode ){


		case "add":
			$que = " insert smart_request set $sque , r_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();
			error_loc("_request.form.php?pass_menu={$pass_menu}&_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;



		case "modify":
			$r = _MQ(" select * from smart_request where r_uid='{$_uid}' ");

			$que = " update smart_request set $sque ". ($_status == "답변완료" ? " , r_admdate = now()" : "")." where r_uid='{$_uid}' ";
			_MQ_noreturn($que);

			// 제휴문의일 경우 답변을 메일로 발송
			if($_status=='답변완료' && $_status != $r[r_status] && in_array($_menu,array('partner'))) {
				// - 메일발송 ---
				$_oemail = $_email;
				if( mailCheck($_oemail) ){
		            include_once(OD_MAIL_ROOT."/service.request.mail.php"); // 메일 내용 불러오기 ($mailing_content)
					$_title = "[".$siteInfo[s_adshop]."] 제휴문의에 관해 답변드립니다.";
					$_content = get_mail_content($mailling_content);
					// -- 메일 발송
					mailer( $_oemail, $_title , $_content );
				}
				// - 메일발송 ---
			}

			error_loc("_request.form.php?pass_menu={$pass_menu}&_mode=${_mode}&_uid=${_uid}&_PVSC=${_PVSC}");
			break;


		case "delete":

			// -- 파일삭제(데이터,첨부파일)
			$resBoardFiles = _MQ_assoc("select f_realname from smart_files where f_table_uid = {$_uid} and f_table = 'smart_request'   ");
			foreach($resBoardFiles as $k=>$v){
				deleteFiles($v['f_realname']);
			}
			_MQ_noreturn("delete  from smart_files where  f_table_uid = {$_uid} and f_table = 'smart_request'  "); //파일, 과부하가 발생할 수 있으니 한번에 삭제

			_MQ_noreturn("delete from smart_request where r_uid='{$_uid}' ");
			error_loc("_request.list.php?pass_menu={$pass_menu}&".enc('d' , $_PVSC ));
			break;

	}
	// - 모드별 처리 ---

	exit;
?>