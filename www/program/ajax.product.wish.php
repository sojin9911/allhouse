<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

switch($mode) {

	case "add":

		if(!$code) {
			die("잘못된 접근입니다.");
		}

		// 상품 중복 체크
		$ir = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $code ."'");
		if( $ir['cnt'] > 0 ) {
			//echo "이미 찜한 상품입니다."; exit;
			$que = " delete from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $code ."' ";
			_MQ_noreturn($que);
			actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
			die(0);
		}

		$que = "
			insert smart_product_wish set
				  pw_pcode='". $code ."'
				, pw_inid='". get_userid() ."'
				, pw_rdate=now()
		";
		_MQ_noreturn($que);

		$r = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "'");
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		die($r['cnt']);
	break;

	case "delete":

		if( !$code ) {
			echo "잘못된 접근입니다."; exit;
		}

		$que = " delete from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $code ."' ";
		_MQ_noreturn($que);

		$r = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "'");
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		die($r['cnt']);
	break;
}