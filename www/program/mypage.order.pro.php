<?PHP
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


switch( $_mode ){

	// - 주문취소 ---
	case "cancel":

		$r = _MQ("
			select o.* , oc.oc_tid
			from smart_order as o
			left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum AND oc.oc_tid !='')
			where
				o.o_ordernum='".$ordernum."'
				and o.o_canceled ='N'
				". (is_login() ? " and o.o_mid='".get_userid()."' and o_memtype = 'Y' " : " and o_memtype = 'N' ") ."   /* ----- JJC : 비회원 주문취소 추가 : 2020-07-09 ----- */
		");

		if(sizeof($r) ==0 ){
			echo '<script> parent.window.cancel_trigger = true; </script>'; // SSJ : 중복취소 방지 : 2021-12-31
			error_alt("주문정보를 찾을 수 없습니다.");
		}

		if( !in_array($r[o_status] , array('접수대기','접수완료','구매발주')) ){
			echo '<script> parent.window.cancel_trigger = true; </script>'; // SSJ : 중복취소 방지 : 2021-12-31
			error_alt("상품이 배송된 주문의 취소는 고객센터에 문의해주세요.");
		}

        // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치
        if( $r['npay_order'] == 'Y' ){
			echo '<script> parent.window.cancel_trigger = true; </script>'; // SSJ : 중복취소 방지 : 2021-12-31
            error_alt("네이버페이 주문의 취소는 고객센터에 문의해주세요.");
        }

		// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----
		// 공통취소
		//		넘길변수
		//			-> 취소위치 : _loc (관리자일 경우 - admin / 사용자일 경우 - user)
		//			-> 주문번호 : _ordernum
		//			-> 주문정보 : $osr
		//		return 정보
		//			-> 성공여부 : cancel_status = Y/N
		//			-> 메시지 : cancel_msg
		$_loc = "";
		$_ordernum = $ordernum;
		$osr = $r ;
		include_once(OD_PROGRAM_ROOT."/pg.cancel.inc.php");

		if($cancel_status && $cancel_msg){
			error_frame_loc_msg( (is_login() ? "/?pn=mypage.order.list&" . enc('d' , $_PVSC) : "/?pn=service.guest.order.list&_onum=" . $ordernum . "&_oname=" . urlencode($r['o_oname']))  , $cancel_msg);// ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----
		}
		// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----

		break;
	// - 주문취소 ---

	// - 배송완료 ---
	case "complete":

		//if(!$pouid) error_msg("잘못된 접근입니다.");

		if(is_login())
			$sub_que = " and o.o_mid='".get_userid()."' ";
		else
			$sub_que = " and o.o_memtype='N' ";

		$que = " select op.*, p.p_cpid from smart_order as o
							inner join smart_order_product as op on (o.o_ordernum = op.op_oordernum)
							inner join smart_product as p on (p.p_code = op.op_pcode)
							where (1)
								and o.o_ordernum='{$ordernum}'
								and o.o_canceled ='N'
								and o.o_paystatus ='Y'
								and op.op_sendstatus = '배송중'
								and ( op.op_pouid = '".$pouid."' or (op.op_is_addoption = 'Y' and op.op_addoption_parent = '".$pouid."') )
								".$sub_que."
		";

		$r = _MQ_assoc($que);
		if(sizeof($r) ==0 ){
			error_alt("주문정보를 찾을 수 없습니다.");
		}

		foreach( $r as $k=>$v ){
			_MQ_noreturn("update smart_order_product set op_sendstatus = '배송완료', op_completedate = now() where op_uid = '".$v['op_uid']."'");
		}

		// 주문서 상태 업데이트
		order_status_update($ordernum);

		//error_frame_loc_msg("/?pn=mypage.order.list&" . enc('d' , $_PVSC) , "배송완료 처리하였습니다.");
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_reload("배송완료 처리하였습니다.");
		break;
	// - 구매완료 ---

	// - 컴플레인 ---
	case "complain":

		if(!$opuid) error_msg("잘못된 접근입니다.");

		$que = "update smart_order_product set op_complain = '교환/반품신청' , op_complain_date = now() , op_complain_comment = '".$complain_content."' where op_uid = '".$opuid."'";

		_MQ_noreturn($que);

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_reload("접수되었습니다.");
		break;
	// - 컴플레인 ---


	// 환불신청
	case "refund":
		$r = _MQ("
			select o.* , oc.oc_tid
			from smart_order as o
			left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum AND oc.oc_tid !='')
			where
				o.o_ordernum='".$ordernum."'
				and o.o_canceled ='N'
				". (is_login() ? " and o.o_mid='".get_userid()."' and o_memtype = 'Y' " : " and o_memtype = 'N' ") ."   /* ----- JJC : 비회원 주문취소 추가 : 2020-07-09 ----- */
		");
		if(sizeof($r) ==0 ){
			error_alt("주문정보를 찾을 수 없습니다.");
		}
		if( !in_array($r[o_status] , array('접수대기','접수완료','구매발주')) ){
			error_alt("상품이 배송된 주문의 취소는 고객센터에 문의해주세요.");
		}

        // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치
        if( $r['npay_order'] == 'Y' ){
            error_alt("네이버페이의 환불신청은 고객센터에 문의해주세요.");
        }

		$_ordernum = $ordernum;

		if($r[o_paystatus]=='Y') { // 환불요청관리 메뉴에 표시될 내용
			$moneyback_content = '환불계좌: ['.$bank_code.'] '.$refund_account.' '.$refund_nm;
			$que = "update smart_order set o_moneyback_status = 'request' ,o_moneyback = '환불요청' , o_moneyback_date = now() , o_moneyback_comment = '".$moneyback_content."' where o_ordernum = '".$_ordernum."'";
			_MQ_noreturn($que);
		}

		_MQ_noreturn("update smart_order set o_canceled='R' where o_ordernum='{$_ordernum}' ". (is_login() ? " and o_mid='".get_userid()."' and o_memtype = 'Y' " : " and o_memtype = 'N' ") ."   /* ----- JJC : 비회원 주문취소 추가 : 2020-07-09 ----- */ ");

		// 주문서 상태 업데이트
		order_status_update($_ordernum);
		error_frame_loc_msg( (is_login() ? "/?pn=mypage.order.list&" . enc('d' , $_PVSC) : "/?pn=service.guest.order.list&_onum=" . $ordernum . "&_oname=" . urlencode($r['o_oname']))  , "환불요청 하였습니다.");// ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----

		break;


	default :
		error_msg('오류');
}