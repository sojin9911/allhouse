<?PHP

	// LMH005

	include "inc.php";


	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" , "modify"))) {
		// --사전 체크 ---
		$pr_amount = nullchk(rm_str($pr_amount) , "쿠폰를 입력하시기 바랍니다.");
		$pr_code = nullchk($pr_code , "프로모션코드를 입력하시기 바랍니다.");
		$pr_expire_date = nullchk($pr_expire_date , "만료일을 선택하시기 바랍니다.");
		// --사전 체크 ---

		// 프로모션 코드 중복 체크
		$chk_org = _MQ(" select pr_code from smart_promotion_code where pr_uid = '".$pr_uid."' ");
		if( $chk_org['pr_code']<>$pr_code ) {
			$chk = _MQ_result(" select count(*) from smart_promotion_code where pr_code = '".$pr_code."' ");
			if( $chk>0 ) { error_msg("이미 등록된 프로모션 코드 입니다."); }
		}
	}
	// - 입력수정 사전처리 ---




	// - 모드별 처리 ---
	switch( $_mode ){


		// -- 추가 ---
		case "add":
			
			_MQ_noreturn("
				insert smart_promotion_code set
					pr_code			= '".$pr_code."',
					pr_name			= '".$pr_name."',
					pr_amount		= '".rm_str($pr_amount)."',
					pr_expire_date	= '".$pr_expire_date."',
					pr_expire		= 'N',
					pr_use			= '".$pr_use."',
					pr_type			= '".$pr_type."',
					pr_rdate		= now()
				");

			error_loc( "_promotion.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 추가 ---


		// -- 수정 ---
		case "modify":
			$sque = "
				update smart_promotion_code set
					pr_code			= '".$pr_code."',
					pr_name			= '".$pr_name."',
					pr_amount		= '".rm_str($pr_amount)."',
					pr_expire_date	= '".$pr_expire_date."',
					pr_use			= '".$pr_use."',
					pr_type			= '".$pr_type."',
					pr_edate		= now()
				where 
					pr_uid='${pr_uid}'
			";
			_MQ_noreturn( $sque );
			error_loc("_promotion.form.php" . URI_Rebuild('?', array('_mode'=>'modify', 'pr_uid'=>$pr_uid, '_PVSC'=>$_PVSC)));
			break;
		// -- 수정 ---


		// -- 삭제 ---
		case "delete":
			_MQ_noreturn("delete from smart_promotion_code where pr_uid='$pr_uid' ");
			error_loc( "_promotion.list.php" . URI_Rebuild('?'.enc('d' , $_PVSC)) );
			break;
		// -- 삭제 ---


		// 선택삭제
		case "select_delete":
			if(sizeof($chk_id) == 0 ) {
				error_msg("선택된 코드가 없습니다.");
			}
			_MQ_noreturn("delete from smart_promotion_code where pr_uid in ('".implode("','" , $chk_id)."') ");

			error_loc_msg("_promotion.list.php".URI_Rebuild('?'.enc('d' , $_PVSC)) , "정상적으로 삭제하였습니다.");
			break;

	}
	// - 모드별 처리 ---

?>
