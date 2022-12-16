<?PHP
	include "./inc.php";

	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {


	}
	// - 입력수정 사전처리 ---


	if( $_uid ) {
		$que = " select *  from smart_point_log where pl_uid='".$_uid."' ";
		$r = _MQ($que);
	}


	// - 모드별 처리 ---
	switch( $_mode ){


		// 추가
		case "add":
			// --사전 체크 ---
			$_title = nullchk($_title , "제목을 입력해주시기 바랍니다.");
			$_point = nullchk($_point , "적립금을 입력해주시기 바랍니다.");
			$_appdate = nullchk($_appdate , "지급예정일을 입력해주시기 바랍니다.");
			$_inid = nullchk($_inid , "적용유저을 입력해주시기 바랍니다.");
			// --사전 체크 ---

			$_pro_date = 0;
			if($_appdate){
				$diff = (strtotime($_appdate) - strtotime(date('Y-m-d'))) / (60*60*24);
				$_pro_date = $diff > 0 ? $diff : 0;
			}
			
			$ex = array_filter(array_unique(explode("," , $_inid)));
			if( sizeof($ex) > 0 ){
				foreach( $ex as $k=>$v ){
					shop_pointlog_insert( $v , $_title , $_point , 'N' , $_pro_date);
				}
			}
			error_loc("_point.list.php?" . enc('d' , $_PVSC));
			break;



		// 수정
		case "modify":
			
			// 적립완료, 적립취소, 삭제된 적립금은 타이틀만 수정가능
			if($r['pl_status'] == 'Y' || $r['pl_status'] == 'C' || $r['pl_delete'] == 'Y'){
				// --사전 체크 ---
				$_title = nullchk($_title , "제목을 입력해주시기 바랍니다.");

				$sque = "
					 pl_title='". addslashes($_title) ."'
				";
			}else{
				// --사전 체크 ---
				$_title = nullchk($_title , "제목을 입력해주시기 바랍니다.");
				$_point = nullchk($_point , "적립금을 입력해주시기 바랍니다.");
				$_appdate = nullchk($_appdate , "지급예정일을 입력해주시기 바랍니다.");

				$sque = "
					 pl_title='". addslashes($_title) ."'
					,pl_point='". rm_comma($_point) ."'
					,pl_appdate='". $_appdate ."'
				";
			}
			$que = " update smart_point_log set $sque where pl_uid='{$_uid}' ";
			_MQ_noreturn($que);
			point_update(); // 지급예정일 변경에따른 포인트 지급처리

			error_loc("_point.form.php?_mode=${_mode}&_uid=${_uid}&_PVSC=${_PVSC}");
			break;



		// 삭제
		case "delete":
			_MQ_noreturn("update smart_point_log set pl_delete = 'Y' where pl_uid='{$_uid}' ");
			error_loc_msg("_point.list.php?".enc('d' , $_PVSC), '정상적으로 삭제되었습니다.');
			break;




		// 삭제
		case "mass_delete":
			if(count($chk_uids) < 1) error_msg('삭제할 적립금 내역이 선택되지 않았습니다.');
			
			// 적립금 내역 삭제
			_MQ_noreturn("update smart_point_log set pl_delete = 'Y' where pl_uid in ('". implode("','", $chk_uids) ."') ");
			error_loc_msg("_point.list.php?".enc('d' , $_PVSC), '정상적으로 삭제되었습니다.');
			break;



		// 취소 -- 지급전이면 취소처리 , 지급후면 포인트 차감 로그 추가
		case "cancel":
			
			if($r['pl_status'] == 'C') error_msg('이미 취소된 적립금 내역입니다.');
			if($r['pl_delete'] == 'Y') error_msg('이미 삭제된 적립금 내역입니다.');
			// 포인트 취소
			shop_pointlog_delete( $_uid , '' );

			error_loc_msg("_point.list.php?".enc('d' , $_PVSC), '정상적으로 취소 되었습니다.');
			break;

	}
	// - 모드별 처리 ---

	exit;
?>