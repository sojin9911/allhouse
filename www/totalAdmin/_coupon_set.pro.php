<?php
	include "./inc.php";


	if( in_array( $_mode , array("add" , "modify") ) ){
		$ocs_name = nullchk($ocs_name , '쿠폰명을 입력해 주세요.');
		if( $ocs_use_date_type == 'day'){
			$ocs_expire = nullchk($ocs_expire , '쿠폰유효기간 일 수 를입력해 주세요.');
		}

		$arrSque = array();

		if( count($ocs_issued_group) > 0){
			$group_implode = implode(",",$ocs_issued_group);
		}


		unset($ocs_per,$ocs_price);
		if($ocs_dtype == 'per'){
			$ocs_per = $temp_pricePer;
		}else{
			$ocs_price_max_use = '';
			$ocs_price = $temp_pricePer;
		}

		// 값이 없을 경우 처리
		$ocs_price_max_use = $ocs_price_max_use == '' ? 'N':'Y';



		// $arrSque[] = " ocs_pcode = '".$ocs_pcode."' ";
		$arrSque[] = " ocs_type = '".$_POST['ocs_type']."' ";
		$arrSque[] = " ocs_name = '".addslashes($ocs_name)."' ";
		$arrSque[] = " ocs_sdate = '".$ocs_sdate."' ";
		$arrSque[] = " ocs_edate = '".$ocs_edate."' ";
		$arrSque[] = " ocs_expire = '".rm_str($ocs_expire)."' ";
		$arrSque[] = " ocs_limit = '".rm_str($ocs_limit)."' ";
		$arrSque[] = " ocs_dtype = '".$ocs_dtype."' ";
		$arrSque[] = " ocs_price_max = '".rm_str($ocs_price_max)."' ";
		$arrSque[] = " ocs_price_max_use = '".$ocs_price_max_use."' ";
		$arrSque[] = " ocs_price = '".rm_str($ocs_price)."' ";
		$arrSque[] = " ocs_per = '".rm_str($ocs_per)."' ";
		$arrSque[] = " ocs_cur = '".$ocs_cur."' ";
		$arrSque[] = " ocs_issued_type = '".$ocs_issued_type."' ";
		$arrSque[] = " ocs_issued_type_auto = '".$ocs_issued_type_auto."' ";
		$arrSque[] = " ocs_use_date_type = '".$ocs_use_date_type."' ";
		$arrSque[] = " ocs_boon_type = '".$ocs_boon_type."' ";
		$arrSque[] = " ocs_issued_cnt_type = '".$ocs_issued_cnt_type."' ";
		$arrSque[] = " ocs_issued_cnt = '".rm_str($ocs_issued_cnt)."' ";
		$arrSque[] = " ocs_issued_due_type = '".$ocs_issued_due_type."' ";

		$arrSque[] = " ocs_issued_group = '".$group_implode."' ";

		$arrSque[] = " ocs_due_use = '".$ocs_due_use."' ";
		$arrSque[] = " ocs_desc = '".$ocs_desc."' ";


	}

	switch($_mode)
	{
		case "add": // 쿠폰등록

			$arrSque[] = " ocs_view = 'Y' ";
			$arrSque[] = " ocs_rdate = now() ";
			$sque = implode(",",$arrSque);


			_MQ_noreturn("insert smart_individual_coupon_set set ".$sque);
			$_uid = mysql_insert_id();

			error_loc("_coupon_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));

		break;

		case "modify": // 쿠폰 수정



			// 발급된 쿠폰이 있는지 체킹  :: 발급된 쿠폰이 있을경우 수정 불가능
			$rowChk = _MQ("select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = '".$_uid."'  ");
			if( $rowChk['cnt'] > 0){ error_loc_msg("_coupon_set.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}","발급된 쿠폰이 존재하여 수정이 불가능합니다."); }

			$sque = implode(",",$arrSque);

			_MQ_noreturn("update smart_individual_coupon_set set ".$sque." where ocs_uid = '".$_uid."' ");

			error_loc("_coupon_set.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC);

		break;


		case "modifyView": // 발급 컨트롤
			if( $ctrlMode == ''){ error_loc_msg("_coupon_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),"잘못된 접근입니다.");  }

			if( $ctrlMode == 'Y'){
				$row = _MQ("select *from smart_individual_coupon_set where ocs_uid = '".$_uid."' ");
				if( $row['ocs_use_date_type'] == 'date' && $row['ocs_edate'] < date('Y-m-d') ){
					error_loc_msg("_coupon_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),"사용기간이 지난 쿠폰은 발급시작을 할 수 없습니다.");
				}
			}

			_MQ_noreturn("update smart_individual_coupon_set set ocs_view = '".$ctrlMode."' where ocs_uid = '".$_uid."'  ");
			error_loc("_coupon_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
		break;

		case "delete": // 쿠폰삭제
			// 발급된 쿠폰이 있는지 체킹  :: 발급된 쿠폰이 있을경우 수정 불가능
			$rowChk = _MQ("select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = '".$_uid."'  ");
			if( $rowChk['cnt'] > 0){ error_loc_msg("_coupon_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),"회원에게 발급된 쿠폰이 존재하여 삭제가 불가능합니다."); }
			_MQ_noreturn(" delete from smart_individual_coupon_set where ocs_uid = '".$_uid."' ");
			error_loc("_coupon_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
		break;
	}

?>