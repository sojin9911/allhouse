<?php 
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림
include_once('inc.php');

// -- 모드별 처리
switch ($_mode) {

	// -- 탈퇴회원 삭제
	case "delete":

		// -- 회원 키
		$arrKey = array('in_id'=>'아이디','in_userlevel'=>'회원레벨');

		if($ctrlMode == 'select') {
			$ctrlModeName = '선택';
			if( count($arrID) < 1 ){ error_msg("회원을 1명이상 선택해 주세요."); }
			$sque = " from smart_individual where 1 and in_sleep_type = 'N' AND in_out = 'Y' and find_in_set(in_id, '".implode(",",$arrID)."' ) > 0   ";
		}else if( $ctrlMode == 'search') {
			$ctrlModeName = '검색';
			$sque = enc('d', $searchQue);
		}else if($ctrlMode == 'single') { // 개별삭제
			$sque = " from smart_individual where 1 and in_sleep_type = 'N' AND in_out = 'Y' and in_id = '".$chkVar."'   ";
		}else{
			error_msg('실행이 올바르지 않습니다.');
		}

		if($orderby == '') { $orderby = ' order by in_odate desc '; }

		$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
		if( count($res) < 1){ error_msg('회원검색에 실패하였습니다.'); }

		$successCnt = 0; // 성공카운트
		foreach($res as $k=>$v){
			$_id = $v['in_id'];
			$del_id = "del_".$v['in_id']; // 삭제처리 치환될 아이디
			if($v['in_userlevel'] >= 9){ continue; } // 관리자 아이디 제외

			/** 
				@ -- 회원관련 정보 아이디 일괄변경 del_아이디
				--수신동의 2년지난회원로그--        			'smart_2year_opt_log''
				--게시판 --        											'smart_bbs'        
				--게시판댓글--                						'smart_bbs_comment'        
				--방문로그테이블--                       'smart_cntlog_list'        
				--로그인체크--        										'smart_loginchk'        
				--주문--        													'smart_order'
				--현금영수증--        										'smart_order_cashlog' 
				--온라인로그--        										'smart_order_onlinelog'        
				--포인트로그--        										'smart_point_log'        
				--상품문의/후기--        									'smart_product_talk'
				--찜하기--        												'smart_product_wish'        
				--출석체크로그--        									'smart_promotion_attend_log' 
				--1:1문의--        											'smart_request'        
			**/
			_MQ_noreturn("update smart_2year_opt_log set ol_mid = '".$del_id."' where ol_mid = '".$_id."'  ");
			_MQ_noreturn("update smart_bbs set b_inid = '".$del_id."' where b_inid = '".$_id."' and b_writer_type = 'member' ");
			_MQ_noreturn("update smart_bbs_comment set bt_inid = '".$del_id."' where bt_inid = '".$_id."' ");
			_MQ_noreturn("update smart_cntlog_list set sc_mid = '".$del_id."' where sc_mid = '".$_id."' ");
			_MQ_noreturn("update smart_loginchk set lc_mid = '".$del_id."' where lc_mid = '".$_id."' ");
			_MQ_noreturn("update smart_order set o_mid = '".$del_id."' where o_mid = '".$_id."' and o_memtype = 'Y'  ");
			_MQ_noreturn("update smart_order_cashlog set ocs_member = '".$del_id."' where ocs_member = '".$_id."' ");
			_MQ_noreturn("update smart_order_onlinelog set ool_member = '".$del_id."' where ool_member = '".$_id."' ");
			_MQ_noreturn("update smart_point_log set pl_inid = '".$del_id."' where pl_inid = '".$_id."' ");
			_MQ_noreturn("update smart_product_talk set pt_inid = '".$del_id."' where pt_inid = '".$_id."' and pt_intype = 'normal' ");
			_MQ_noreturn("update smart_product_wish set pw_inid = '".$del_id."' where pw_inid = '".$_id."' ");
			_MQ_noreturn("update smart_promotion_attend_log set atl_member = '".$del_id."' where atl_member = '".$_id."' ");
			_MQ_noreturn("update smart_request set r_inid = '".$del_id."' where r_inid = '".$_id."' ");

			// -- 회원데이터 삭제처리 
			_MQ_noreturn("delete from smart_individual where in_id = '".$_id."' and in_out = 'Y' and in_sleep_type = 'N' ");


			$successCnt++;

		}

		if( $successCnt < 1){
			error_loc_msg("_individual_out.list.php?".enc('d' , $_PVSC ),$ctrlModeName.' 회원 삭제처리에 실패하였습니다.');
		}else{
			error_loc_msg("_individual_out.list.php?".enc('d' , $_PVSC ),$ctrlModeName.' 회원이 삭제처리 되었습니다. (총 '.number_format($successCnt).'건 처리)');
		}
}