<?PHP
	include "./inc.php";

	$app_path = "..".IMG_DIR_PRODUCT;

	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {

		// --사전 체크 ---
		$pt_content = nullchk($pt_content , "내용을 입력해주시기 바랍니다.");
		$_img_name = _PhotoPro( $app_path , "_img" );
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = "
			pt_content	='". $pt_content ."'
			,pt_title	='". $pt_title ."'
			,pt_inid	='". $pt_inid ."'
			,pt_writer	= '".$pt_writer."'
			,pt_img		= '".$_img_name."'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---


	if( $pt_uid) {
		$que = " select *  from smart_product_talk where pt_uid='".$pt_uid."' ";
		$r = _MQ($que);
	}


	// - 모드별 처리 ---
	switch( $_mode ){

		// 추가
		case "add":
			$que = " insert smart_product_talk set " . $sque . " ,pt_pcode = '".$r['pt_pcode']."', pt_depth='2' , pt_relation='". $r['pt_uid'] ."', pt_intype='admin', pt_type = '".$r['pt_type']."', pt_rdate=now() ";
			_MQ_noreturn($que);
			error_loc("_product_talk.list.php?" . enc('d' , $_PVSC));
			break;

		// 수정
		case "modify":
			$que = " update smart_product_talk set $sque where pt_uid='{$pt_uid}' ";
			_MQ_noreturn($que);
			error_loc("_product_talk.form.php?_mode=${_mode}&pt_uid=${pt_uid}&_PVSC=${_PVSC}");
			break;


		// 삭제
		case "delete":
			_MQ_noreturn("delete from smart_product_talk where pt_uid='{$pt_uid}' || pt_relation='{$pt_uid}' ");

			if( $r['pt_img'] ) {
				_PhotoDel($app_path,$r['pt_img']);

				// 2019-02-18 SSJ :: 관리자 설정에 따라 상품을 구매한 회원만 후기 작성 가능
				// 포토 후기 등록 개수
				if($r['pt_depth'] == 1 && $r['pt_type'] == $arr_p_talk_type['eval']){
					$que = "
						select count(*) as cnt
						from smart_product_talk
						where 1
							and pt_type = '".$r['pt_type']."'
							and pt_pcode = '". $r['pt_pcode'] ."'
							and pt_inid = '".$r['pt_inid']."'
							and pt_depth = 1
							and pt_img != ''
						order by pt_uid asc
					";
					$er = _MQ($que);
					// 적립일 체크 , true - 지급완료
					$point_date = date('Y-m-d', strtotime('+'. $siteInfo['s_productevalprodate'] .' days', strtotime($r['pt_rdate'])));
					$trigger_date = date('Y-m-d') >= $point_date;
					$trigger_point = false;
					$point_days = (strtotime(date('Y-m-d')) - strtotime($point_date)) / (60*60*24);
					if($siteInfo['s_producteval_limit']<>'B'){
						if($er['cnt'] == 0) $trigger_point = true;
					}else{
						$trigger_point = true;
					}
					if($trigger_point){
						if($trigger_date) shop_pointlog_insert( $r['pt_inid'] , "포토후기 삭제 (상품코드: ".$r['pt_pcode'].")" , $siteInfo['s_productevalpoint']*-1 , "N" , 0);
						else shop_pointlog_insert( $r['pt_inid'] , "포토후기 삭제 (상품코드: ".$r['pt_pcode'].")" , $siteInfo['s_productevalpoint']*-1 , "N" , $point_days);
					}
				}
			}

			error_loc("_product_talk.list.php?".enc('d' , $_PVSC));
			break;

	}
	// - 모드별 처리 ---

	exit;
?>