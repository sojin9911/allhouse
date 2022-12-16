<?php 
include_once('inc.php');

// -- 모드별 처리
switch ($_mode) {

	// -- 탈퇴회원 삭제
	case "delete":
		// -- 회원 키
		$arrKey = array('md_uid'=>'고유번호');

		if($ctrlMode == 'select') {
			if( count($arrUid) < 1 ){ error_msg("삭제하실 데이터를 1개이상 선택해 주세요."); }
			$sque = " from smart_member_080_deny where 1 and find_in_set(md_uid, '".implode(",",$arrUid)."' ) > 0   ";
		}else if( $ctrlMode == 'search') {
			$sque = enc('d', $searchQue);
		}else if($ctrlMode == 'single') { // 개별삭제
			$sque = " from smart_member_080_deny where 1 and md_uid = '".$chkVar."'   ";
		}else{
			error_msg('실행이 올바르지 않습니다.');
		}

		if($orderby == '') { $orderby = ' order by md_uid desc '; }

		$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
		$delUid = array();
		foreach($res as $k=>$v){ $delUid[] = $v['md_uid']; }
		if( count($delUid) < 1){ error_msg('데이터 조회에 실패하였습니다.'); }
		_MQ_noreturn("delete from smart_member_080_deny where find_in_set(md_uid, '".implode(",",$delUid)."' ) > 0  ");
		error_loc_msg("/totalAdmin/_addons.php?".enc('d' , $_PVSC),"정상적으로 삭제처리 되었습니다.");
	break;
}

exit;