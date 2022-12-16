<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$ordernum = $_SESSION["session_ordernum"];//주문번호




$mertkey = $siteInfo[s_pg_key]; //LG유플러스에서 발급한 상점키로 변경해 주시기 바랍니다.
$hashdata2 = md5($mid.$oid.$tid.$txtype.$productid.$ssn.$ip.$mac.$resdate.$mertkey); //
$value = array( "txtype"		=> $txtype,
				"mid"    		=> $mid,
				"tid" 			=> $tid,
               	"oid"     		=> $oid,
				"ssn" 			=> $ssn,
				"ip"			=> $ip,
				"mac"			=> $mac,
				"resdate"		=> $resdate,
               	"hashdata"    	=> $hashdata,
				"productid"		=> $productid,
               	"hashdata2"  	=> $hashdata2 );

if ($hashdata2 == $hashdata) {          //해쉬값 검증이 성공하면
		$o_mid = _MQ("select o_mid from smart_order where o_ordernum='{$oid}'");
		$p_cpid = 'hyssence'; $o_mid = $o_mid[o_mid];
		if(is_login())
			$sub_que = " o.o_mid='".get_userid()."' and ";
		else
			$sub_que = " o.o_mid='".$o_mid."' and o.o_memtype='N' and ";

		$que = " select op.*, p.p_cpid from smart_order as o
							inner join smart_order_product as op on (o.o_ordernum = op.op_oordernum)
							inner join smart_product as p on (p.p_code = op.op_pcode)
							where
							o.o_ordernum='{$ordernum}' and
							o.o_canceled ='N' and
							o.o_paystatus ='Y' and
							op.op_sendstatus = '배송중' and ".$sub_que." p.p_cpid = '".$p_cpid."'";

		$r = _MQ_assoc($que);
		if(sizeof($r) ==0 ){
			error_alt("주문정보를 찾을 수 없습니다.");
		}

		foreach( $r as $k=>$v ){
			_MQ_noreturn("update smart_order_product set op_sendstatus = '배송완료', op_completedate = now() where op_uid = '".$v[op_uid]."'");
		}

		// 주문서 상태 업데이트
		order_status_update($ordernum);

		error_frame_loc_msg("/?pn=mypage.order.list&" . enc('d' , $_PVSC) , "배송완료 처리하였습니다.");

} else {                                //해쉬값 검증이 실패이면
	echo '실패';
}






actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행