<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행




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

$order_no = $ORDER_ID;


// 여기에 DB 설정

$ool_type = 'I';
$r = _MQ("select * from smart_order_onlinelog where ool_ordernum='$order_no' order by ool_uid desc");

if($r[ool_amount_total]!=$AUTH_AMOUNT) { echo "AMOUNT ERROR"; exit; } // 빌게이트는 부분입금을 지원하지 않는다.

_MQ_noreturn("
    insert into smart_order_onlinelog (
        ool_ordernum,
        ool_member,
        ool_date,
        ool_tid,
        ool_type,
        ool_respdate,
        ool_amount_current,
        ool_amount_total,
        ool_account_num,
        ool_account_code,
        ool_deposit_name,
        ool_bank_name,
        ool_bank_code,
        ool_escrow,
        ool_escrow_code,
        ool_deposit_tel,
        ool_bank_owner
    ) values (
        '$order_no',
        '$r[ool_member]',
        now(),
        '$TRANSACTION_ID',
        '$ool_type',
        '$AUTH_DATE',
        '$AUTH_AMOUNT',
        '$r[ool_amount_total]',
        '$r[ool_account_num]',
        '',
        '',
        '$r[ool_bank_name]',
        '$r[ool_bank_code]',
        'Y',
        '',
        '$r[ool_deposit_tel]',
        '$r[ool_bank_owner]'
    )
");

// 빌게이트는 현금영수증 발급 관련 리턴값이 없다.
/*if(!empty($no_cshr_tid)) {
    _MQ_noreturn("update smart_order set o_get_tax='Y' where o_ordernum='$order_no'");
    _MQ_noreturn("insert into smart_order_cashlog (ocs_ordernum,ocs_member,ocs_date,ocs_tid,ocs_cashnum,ocs_respdate,ocs_amount,ocs_method,ocs_type) values ('$order_no','$r[ool_member]',now(),'$no_cshr_tid','$no_cshr_appl','$tm_cshr','$amt_input','AUTH','virtual')");
}*/

$r = _MQ("select * from smart_order_onlinelog as ol inner join smart_order as o on (o.o_ordernum=ol.ool_ordernum) where ol.ool_ordernum='$order_no' order by ol.ool_uid desc limit 1");

// - 2016-09-05 ::: JJC ::: 주문정보 추출 ::: 가상계좌 - 이미 결제가 되었다면 추가 적용을 하지 않게 처리함. ---
$iosr = get_order_info($order_no);

if($r['ool_amount_total'] == $r['ool_amount_current'] && $iosr['o_paystatus'] <> "Y" ) {

	// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
	// 공통결제
	//		넘길변수
	//			-> 주문번호 : $ordernum
	$ordernum = $order_no;
	include(OD_PROGRAM_ROOT."/shop.order.result.pro.php"); // ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
	if($pay_status == 'N') {echo "AMOUNT ERROR"; exit;}// 실패처리
	// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----

}



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
?>
RC:111