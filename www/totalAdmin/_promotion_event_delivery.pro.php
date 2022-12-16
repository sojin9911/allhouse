<?php // -- LCY :: 2017-09-20 -- 프로모션 > 이벤트 > 무료배송이벤트 처리
	
	include_once('inc.php');
	// -- 이벤트기간 체크 
	$chkSdate = preg_replace('/[-]/', '', $sdate);
	$chkEdate = preg_replace('/[-]/', '', $edate);
	if( $chkSdate > $chkEdate ) { error_alt("종료일은 시작일보다 작을 수 없습니다."); } 

	// -- 회원등급 체크
	if( $setMember == 'group') { 
		if( count($setGroupUid) < 1 ) { error_alt("적용할 회원등급을 선택해 주세요."); } 
		$setGroupUidSave = implode(',',$setGroupUid); 
	}else{ 
		$setGroupUidSave = "";
	}

	// -- 무료배송이벤트 설정값 호출 -- 덮의씌우기
	$arrItem = getPromotionEventDelivery();

	// -- 정보저장
	$arrItem['use'] = $use; // 사용상태 Y, N
	$arrItem['sdate'] = $sdate ; // 이벤트기간 :: 시작일
	$arrItem['edate'] = $edate ; // 이벤트기간 :: 종료일
	$arrItem['minPrice'] = $minPrice; // 최소결제금액 
	$arrItem['setMember'] = $setMember; // 적용대상 all, group 
	$arrItem['setGroupUid'] = $setGroupUidSave; // 그룹
	$arrItem['mdate'] = date('Y-m-d H:i:s'); // 최근수정일


	$item = serialize($arrItem);
	_MQ_noreturn("update smart_setup set promotion_event_delivery_config = '".addslashes($item)."' where s_uid = 1; "); // 저장

	error_frame_loc("_promotion_event_delivery.php");

?>