<?PHP
	include_once('inc.php');

	// 달성조건 DB 입력
	function insert_attend_addinfo($_uid, $addinfo, $isEditable='N'){
		if(!$_uid) return false;
		if($isEditable == 'N') return false;

		// 기존자료 삭제
		_MQ_noreturn(" delete from smart_promotion_attend_addinfo where ata_event = '". $_uid ."' ");

		if(sizeof($addinfo['_days']) < 1) return false;

		$arr_addinfo_que = array();
		foreach($addinfo['_days'] as $k=>$v){
			$_addque = " ('". $_uid ."', '". $addinfo['_days'][$k] ."', '". $addinfo['_coupon'][$k] ."', '". rm_str($addinfo['_point'][$k]) ."', '". rm_str($addinfo['_coupon_delay'][$k]) ."', '". rm_str($addinfo['_point_delay'][$k]) ."', now()) ";
			$arr_addinfo_que[] = $_addque;
		}

		// 신규자료 등록
		$que = " insert into smart_promotion_attend_addinfo (ata_event, ata_days, ata_coupon, ata_point, ata_coupon_delay, ata_point_delay, ata_rdate) values " . implode(" , " , $arr_addinfo_que);
		_MQ_noreturn($que);

		return true;
	}

	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array('add' , 'modify') ) ) {

		// --사전 체크 ---
		if($isEditable == 'Y'){ // 수정가능일때만 세부정보 필수 입력체크
			$_title = nullchk($_title , '이벤트명을 입력해주시기 바랍니다.');
			if($_limit == 'Y'){
				$_sdate = nullchk($_sdate , '이벤트 시작일을 입력해주시기 바랍니다.');
				$_edate = nullchk($_edate , '이벤트 종료일을 입력해주시기 바랍니다.');
			}
		}
		// --사전 체크 ---


		// --이미지 처리 ---
		$_img_pc_name = _PhotoPro( "..".IMG_DIR_BANNER , "_img_pc" ) ; // 이미지
		$_img_mo_name = _PhotoPro( "..".IMG_DIR_BANNER , "_img_mo" ) ; // 이미지
		// --이미지 처리 ---

		// --query 사전 준비 ---
		$sque = "
			atc_use = '". ($_use == 'Y' ? 'Y' : 'N') ."'
		";
        // 수정가능일때만 세부정보 수정가능
        if($isEditable == 'Y'){
            $sque .= "
                , atc_title = '". addslashes(trim($_title)) ."'
                , atc_limit = '". ($_limit == 'Y' ? 'Y' : 'N') ."'
                , atc_sdate = '". addslashes(trim($_sdate)) ."'
                , atc_edate = '". addslashes(trim($_edate)) ."'
                , atc_type = '". ($_type == 'T' ? 'T' : 'C') ."'
                , atc_duplicate = '". ($_duplicate == 'Y' ? 'Y' : 'N') ."'
            ";
        }
        // 2019-03-11 SSJ :: 이벤트 타이틀 이미지는 수정가능하도록 수정
        $sque .= "
            , atc_img_pc = '". $_img_pc_name ."'
            , atc_img_mo = '". $_img_mo_name ."'
        ";
        // --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			$que = " insert smart_promotion_attend_config set $sque , atc_mdate = now() , atc_rdate = now() ";
			_MQ_noreturn($que);

			$_uid = mysql_insert_id();

			// 달성조건 DB 입력
			insert_attend_addinfo($_uid, $addinfo, $isEditable);

			// 사용상태가 사용이면 다른 이벤트는 중지
			if($_use == 'Y'){
				_MQ_noreturn(" update smart_promotion_attend_config set atc_use = 'N' where atc_uid != '". $_uid ."' ");
			}

			error_loc_msg("_promotion_attend.form.php?_mode=modify&_uid=" . $_uid . ($_PVSC ? '&_PVSC='.$_PVSC : null) , '출석체크 이벤트가 정상적으로 등록되었습니다. ');
			break;



		case "modify":
			$que = " update smart_promotion_attend_config set $sque ,  atc_mdate = now() where atc_uid='". $_uid ."' ";
			_MQ_noreturn($que);

			// 달성조건 DB 입력
			insert_attend_addinfo($_uid, $addinfo, $isEditable);

			// 사용상태가 사용이면 다른 이벤트는 중지
			if($_use == 'Y'){
				_MQ_noreturn(" update smart_promotion_attend_config set atc_use = 'N' where atc_uid != '". $_uid ."' ");
			}

			error_loc_msg("_promotion_attend.form.php?_mode=modify&_uid=" . $_uid . ($_PVSC ? '&_PVSC='.$_PVSC : null) , '정상적으로 수정되었습니다.');
			break;



		case "delete":
			// 삭제 가능한지 체크 - 출석내역이 있으면 삭제 불가
			$log_cnt = _MQ_result(" select count(*) as cnt from smart_promotion_attend_log where atl_event = '". $_uid ."' ");
			if($log_cnt > 0){
				error_loc_msg("_promotion_attend.list.php?".enc('d' , $_PVSC) , '출석 내역이 있는 이벤트는 삭제할 수 없습니다.');
				break;
			}

			// -- 이미지 삭제 ---
			$r = _MQ("select atc_img_pc,atc_img_mo from smart_promotion_attend_config where atc_uid='". $_uid ."' ");
			if($r['atc_img_pc']) _PhotoDel( "..".IMG_DIR_BANNER , $r['atc_img_pc'] );
			if($r['atc_img_mo']) _PhotoDel( "..".IMG_DIR_BANNER , $r['atc_img_mo'] );
			// -- 이미지 삭제 ---

			// 출석체크 이벤트 달성조건 삭제
			_MQ_noreturn("delete from smart_promotion_attend_addinfo where ata_event='". $_uid ."' ");

			// 출석체크 이벤트 삭제
			_MQ_noreturn("delete from smart_promotion_attend_config where atc_uid='". $_uid ."' ");

			error_loc_msg("_promotion_attend.list.php?".enc('d' , $_PVSC) , '정상적으로 삭제되었습니다. ');
			break;



		case "delete_log":
			if(!$_uid) error_msg("잘못된 접근입니다.");

			// 출석체크 내역 삭제
			_MQ_noreturn("delete from smart_promotion_attend_log where atl_uid='". $_uid ."' ");

			error_loc_msg("_promotion_attend.log.php?".enc('d' , $_PVSC2) . "&_PVSC=" . $_PVSC , '정상적으로 삭제되었습니다. ');
			break;



		case "mass_delete_log":
			if(sizeof($chk_uid) < 1) error_msg("잘못된 접근입니다.");

			// 출석체크 내역 삭제
			_MQ_noreturn("delete from smart_promotion_attend_log where atl_uid in ('". implode("','" , array_keys($chk_uid)) ."') ");

			error_loc_msg("_promotion_attend.log.php?".enc('d' , $_PVSC2) . "&_PVSC=" . $_PVSC , '정상적으로 삭제되었습니다. ');
			break;

	}
	// - 모드별 처리 ---

	exit;