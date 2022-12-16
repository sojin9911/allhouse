<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행




// 로그인 체크
member_chk("Y");




// - 선택 찜상품 삭제 ---
if( $mode == "choice_delete" ) {

	if( sizeof($_chk) ==0 ) {
		error_alt("삭제할 찜상품을 선택해주세요");
	}

	// delete
	$que = " delete from smart_product_wish where pw_uid in ('". implode("','", array_filter(array_unique(array_keys($_chk)))) ."') ";

	_MQ_noreturn($que);

	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	error_frame_reload("선택하신 상품을 삭제하였습니다.") ;
}
// - 찜상품 삭제 ---
else if( $mode == "delete" ) {

	if( !$uid ) {
		error_alt("잘못된 접근입니다.");
	}

	// delete
	$que = " delete from smart_product_wish where pw_uid='". $uid ."'";
	_MQ_noreturn($que);

	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	error_frame_loc_msg("/?pn=mypage.wish.list&" . enc('d' , $_PVSC), "선택하신 상품을 삭제하였습니다.") ;
}
// - 찜상품 일반등록 삭제 ---
else if( $mode == "all" ) {

	if( !$pcode ) {
		error_alt("잘못된 접근입니다.");
	}

	echo "<script src='/include/js/jquery-1.7.1.min.js' type='text/javascript'></script>";

	// 상품 중복 체크
	$ir = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $pcode ."'");
	if( $ir[cnt] > 0 ) {
		// delete
		$que = " delete from smart_product_wish where pw_pcode='". $pcode ."' and pw_inid='". get_userid() ."' ";
		_MQ_noreturn($que);
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_alt("선택하신 상품 찜을 해제했습니다.");
	}
	else {
		// insert
		$que = " insert smart_product_wish set pw_pcode='". $pcode ."' , pw_inid='". get_userid() ."' , pw_rdate=now()";
		_MQ_noreturn($que);
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_alt("선택하신 상품을 찜했습니다.");
	}

}
// - 선택 등록 ---
else if( $mode == "select_add" ) {

	if( count($pcode_array) < 1 ) exit;

	foreach($pcode_array as $k => $pcode) {

		// 상품 중복 체크
		$ir = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $pcode ."'");
		if( $ir[cnt] > 0 ) continue;

		// insert
		$que = "
			insert smart_product_wish set
					pw_pcode='". $pcode ."'
				, pw_inid='". get_userid() ."'
				, pw_rdate=now()
		";
		_MQ_noreturn($que);

	}
	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행

// - 찜상품 추가 ---
} else {

	if( !$pcode ) {
		error_alt("잘못된 접근입니다.");
	}

	// 상품 중복 체크
	$ir = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $pcode ."'");
	if( $ir[cnt] > 0 ) {
		error_alt("이미 찜한 상품입니다.");
	}


	// insert
	$que = "
		insert smart_product_wish set
			  pw_pcode='". $pcode ."'
			, pw_inid='". get_userid() ."'
			, pw_rdate=now()
	";
	_MQ_noreturn($que);


	$r = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $pcode ."'");

	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	error_alt("선택하신 상품을 찜했습니다.");
}