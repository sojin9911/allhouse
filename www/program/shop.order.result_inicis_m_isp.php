<?
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

    $ordernum = $_REQUEST[ordernum];

	// 주문정보 추출
	$r = _MQ("select * from smart_order where o_paystatus='Y' and o_status='결제완료' and o_ordernum='". $ordernum ."' ");

// -------------------------------- 2017-06-01 ::: ISP 결제시 다중 신호 발송시 오류 수정 ::: JJC --------------------------------

	// 결제완료 처리된 주문이 없다면 isp callback 처리에서 문제가 생긴것이다.
//	if(sizeof($r) < 1) {
//		error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.");
//		exit;
//	}



	if( $r['o_ordernum'] ) {

	// 로그인 쿠키 적용
	samesiteCookie("AuthIndividualMember", $r[o_mid] , 0 , "/" , "." . str_replace("www." , "" , reset(explode(":",$_SERVER['HTTP_HOST'])) ));
	$mem_info = _individual_info($r[o_mid]);
	$_SESSION["session_ordernum"] = $ordernum;



	// 회원정보 추출
	if(is_login()) $indr = $mem_info;

	// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
	include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

	// 결제완료페이지 이동
	error_loc("/?pn=shop.order.complete&ordernum=" . $ordernum);
	}
	else{
		//		* ISP의 경우 간혹 2번이상 신호를 발송하는데 나중에 발송되고 쿠폰일 경우
		//		* orderstatus_step = '발급완료'가 되어 결제실패 경고창이 뜸
		$sr = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");

		if($sr['o_paystatus'] == 'Y'  && $sr['o_canceled'] != 'Y'){
			error_loc("/?pn=shop.order.complete&ordernum=".$ordernum);
		}
		else {
			error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.");
		}



	}

 actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
?>