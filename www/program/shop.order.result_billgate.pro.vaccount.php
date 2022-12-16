<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$ordernum = $_SESSION["session_ordernum"];//주문번호




$ool_bank_name_array = array(
    '039'=>'경남',
    '034'=>'광주',
    '004'=>'국민',
    '003'=>'기업',
    '011'=>'농협',
    '031'=>'대구',
    '032'=>'부산',
    '002'=>'산업',
    '045'=>'새마을금고',
    '007'=>'수협',
    '088'=>'신한',
    '026'=>'신한',
    '048'=>'신협',
    '005'=>'외환',
    '020'=>'우리',
    '071'=>'우체국',
    '037'=>'전북',
    '035'=>'제주',
    '081'=>'하나',
    '027'=>'한국씨티',
    '053'=>'씨티',
    '023'=>'SC은행',
    '009'=>'동양증권',
    '078'=>'신한금융투자증권',
    '040'=>'삼성증권',
    '030'=>'미래에셋증권',
    '043'=>'한국투자증권',
    '069'=>'한화증권'
);

cookie_chk();

$ordernum = $ORDER_ID;

// - 결제 성공 기록정보 저장 ---
$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
if($RESPONSE_CODE) { $app_oc_content = $RESPONSE_CODE."||".$RESPONSE_MESSAGE. "§§"; }
if($DETAIL_RESPONSE_CODE) { $app_oc_content .= $DETAIL_RESPONSE_CODE."||".$DETAIL_RESPONSE_MESSAGE. "§§"; }

// 회원정보 추출
if(is_login()) $indr = $mem_info;

// 주문정보 추출
$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");

// - 주문결제기록 저장 ---
$que = "
	insert smart_order_cardlog set
		 oc_oordernum = '".$ordernum."'
		,oc_tid = '". $TRANSACTION_ID ."'
		,oc_content = '". $app_oc_content ."'
		,oc_rdate = now();
";
_MQ_noreturn($que);
// - 주문결제기록 저장 ---
// - 결제 성공 기록정보 저장 ---

// 현금영수증을 신청했으면 주문정보 업데이트
if(isset($AUTH_DATEIDENTIFIER)) {
	_MQ_noreturn("update smart_order set o_get_tax = 'Y' where o_ordernum = '$ordernum'");

	$op_name = _MQ("
		select p.p_name, count(*) as cnt
		from smart_order_product as op
		inner join smart_product as p on (p.p_code=op.op_pcode)
		where op_oordernum='{$ordernum}'
		group by op_oordernum
	");
	// 현금영수증용 상품명 생성
	$cash_product_name = ($op_name['cnt']>0)?$op_name['p_name'].'외 '.($op_name['cnt']-1).'개':$op_name['p_name'];
	_MQ_noreturn("insert into smart_baro_cashbill (bc_type, bc_ordernum,TradeUsage,IdentityNum,Amount,TradeDate,RegistDT,IssueDT,BarobillState,ItemName,NTSConfirmNum) values
	('pg','$ordernum','1','','".$AUTH_AMOUNT."',curdate(),now(),now(),'3000','".addslashes($cash_product_name)."','$AUTH_DATEIDENTIFIER')");
}

if(!strcmp($RESPONSE_CODE, "0000")) { // 인증 성공인 경우

	$order = _MQ("select * from smart_order as o left join smart_order_cardlog as oc on (o.o_ordernum = oc.oc_oordernum) where o.o_ordernum = '$ordernum'");
	$ool_type = 'R';
	$tno = $TRANSACTION_ID;
	$app_time = $ORDER_DATE;
	$amount = trim($AMOUNT);
	$account = $ACCOUNT_NUMBER;
	$bankcode = $BANK_CODE;
	$depositor = $order[o_oname];
	_MQ_noreturn("
		insert into smart_order_onlinelog (
		ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
		) values (
		'$ordernum', '$order[o_mid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$depositor', '$ool_bank_name_array[$bankcode]', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$bank_owner'
		)
	");

	// 장바구니 정보 삭제
	_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");

	// 가상계좌 결제 이메일 및 SMS 발송
	include_once OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";

	echo "<script language='javascript'>opener.location.href=('/?pn=shop.order.complete');window.close();</script>";

}else{

	_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
	echo "<script language='javascript'>alert('결제에 실패하였습니다. 다시 한번 확인 바랍니다.');opener.location.href=('/?pn=shop.order.result');window.close();</script>";

}







actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행