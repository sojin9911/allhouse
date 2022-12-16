<?PHP
	include "./inc.php";

	if( in_array($_mode,array('add','modify')) == true){

		$_condition_totprice = 	delComma($_condition_totprice); // 평가기준 : 주문합계
		$_condition_totcnt = 	delComma($_condition_totcnt); // 평가기준 : 주문횟수
		$_give_point_per = 	delComma($_give_point_per); // 혜택 적립률
		$_sale_price_per = 	delComma($_sale_price_per); // 혜택 할인율

		$_name = nullchk($_name , "회원등급 이름을 입력해 주세요.");
		if( $_condition_totprice < 0){ error_alt("구매금액은 0이상 입력해 주세요. "); }
		if( $_condition_totcnt < 0){ error_alt("구매횟수는 0이상 입력해 주세요. "); }
		if( $_give_point_per < 0 || $_give_point_per > 100 ){ error_alt(" 적립률은 0이상~100이하로 입력해 주세요. "); }
		if( $_sale_price_per < 0 || $_sale_price_per > 100 ){ error_alt("할인율은  0이상~100이하로 입력해 주세요. "); }

		// {{{회원등급추가}}}
		// --이미지 처리 ---
		$_icon_name = _PhotoPro('../upfiles/icon/', '_icon') ; // 아이콘
		$_mobile_icon_name = _PhotoPro('../upfiles/icon/', '_mobile_icon') ; // 아이콘
		// {{{회원등급추가}}}

	}

	switch($_mode){

		case "add":
			$rowRank = _MQ_result("select mgs_rank from smart_member_group_set order by mgs_rank desc limit 0, 1");
			$_rank = $rowRank == '' ? 1 : ($rowRank+1);

			$rowChk = _MQ("select count(*) as cnt from smart_member_group_set where mgs_name = '".$_name."' ");
			if( $rowChk['cnt'] > 0 ){ error_alt("이미 등록된 회원등급 이름입니다. 다른 이름을 입력해 주세요."); }

			_MQ_noreturn("insert smart_member_group_set set mgs_rank = '".$_rank."', mgs_name ='".$_name."', mgs_condition_totprice = '".$_condition_totprice."', mgs_condition_totcnt = '".$_condition_totcnt."', mgs_give_point_per = '".$_give_point_per."', mgs_sale_price_per = '".$_sale_price_per."',  mgs_rdate = now()

			".
				( ", mgs_icon = '".$_icon_name."' , mgs_mobile_icon = '".$_mobile_icon_name."' , mgs_idx = '".$_idx."' " )// {{{ 회원등급추가}}}
			."


			");
			$_uid = mysql_insert_id();
			error_frame_loc("_member_group_set.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
		break;


		case "modify":

			$rowChk = _MQ("select count(*) as cnt from smart_member_group_set where mgs_name = '".$_name."' and mgs_uid != '".$_uid."'  ");
			if( $rowChk['cnt'] > 0 ){ error_alt("이미 등록된 회원등급 이름입니다. 다른 이름을 입력해 주세요. "); }

			_MQ_noreturn("update smart_member_group_set set  mgs_name ='".$_name."', mgs_condition_totprice = '".$_condition_totprice."', mgs_condition_totcnt = '".$_condition_totcnt."', mgs_give_point_per = '".$_give_point_per."', mgs_sale_price_per = '".$_sale_price_per."'

			".
				( ", mgs_icon = '".$_icon_name."' , mgs_mobile_icon = '".$_mobile_icon_name."' , mgs_idx = '".$_idx."' " )// {{{ 회원등급추가}}}
			."


			where mgs_uid = '".$_uid."'  ");

			error_frame_loc("_member_group_set.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");

		break;

		// -- 등급삭제
		case "delete":
			$row = _MQ("select *from smart_member_group_set where mgs_uid = '".$_uid."' ");
			if( count($row) < 1){
					error_alt("해당 등급정보가 존재하지 않습니다.");
			}

			// -- 기본등급일경우 처리
			if($row['mgs_rank'] == 1){ error_alt("기본등급은 삭제가 불가능합니다."); }


			// {{{회원등급추가}}}
			_PhotoDel( "../upfiles/icon/" , $row['mgs_icon'] );
			_PhotoDel( "../upfiles/icon/" , $row['mgs_mobile_icon'] );
			// {{{회원등급추가}}}

			_MQ_noreturn("delete from smart_member_group_set where mgs_uid = '".$_uid."' ");
			error_frame_loc("_member_group_set.list.php");

		break;

		// -- 선택등급삭제
		case "selectDelete":

			if(count($arrUid) < 1){ error_alt("한개 이상 선택해 주세요."); }

			// {{{회원등급추가}}}
			$res = _MQ_assoc("select *from smart_member_group_set where find_in_set(mgs_uid , '".implode(",",$arrUid)."' ) and (mgs_icon != '' or mgs_mobile_icon != '' )  ");
			foreach($res as $k=>$row) {
				_PhotoDel( "../upfiles/icon/" , $row['mgs_icon'] );
				_PhotoDel( "../upfiles/icon/" , $row['mgs_mobile_icon'] );
			}
			// {{{회원등급추가}}}


			_MQ_noreturn("delete from smart_member_group_set where find_in_set(mgs_uid, '".implode(",",$arrUid)."') ");
			error_frame_loc("_member_group_set.list.php");
		break;


		// == 등급순서 일괄적용
		case "execIdx":
			if( count($_idx) < 1){  error_alt("순서를 변경할 등급이 존재하지 않습니다."); }

			foreach( $_idx as $_uid => $val){
				_MQ_noreturn("update smart_member_group_set set mgs_idx = '".$val."'  where mgs_uid = '".$_uid."'  ");
			}

			error_frame_loc("_member_group_set.list.php");

		break;
	}