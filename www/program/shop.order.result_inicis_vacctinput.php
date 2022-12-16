<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$ool_bank_name_array = array(
    '39'=>'경남',
    '34'=>'광주',
    '04'=>'국민',
    '03'=>'기업',
    '11'=>'농협',
    '31'=>'대구',
    '32'=>'부산',
    '02'=>'산업',
    '45'=>'새마을금고',
    '07'=>'수협',
    '88'=>'신한',
    '26'=>'신한',
    '48'=>'신협',
    '05'=>'외환',
    '20'=>'우리',
    '71'=>'우체국',
    '37'=>'전북',
    '35'=>'제주',
    '81'=>'하나',
    '27'=>'한국씨티',
    '53'=>'씨티',
    '23'=>'SC은행',
    '09'=>'동양증권',
    '78'=>'신한금융투자증권',
    '40'=>'삼성증권',
    '30'=>'미래에셋증권',
    '43'=>'한국투자증권',
    '69'=>'한화증권'
);



if(is_login()) $indr = $mem_info;
    $ool_member = $indr[in_id];

//**********************************************************************************
//  이부분에 로그파일 경로를 수정해주세요.

$INIpayHome = PG_DIR."/inicis"; // 이니페이 홈디렉터리(상점수정 필요)


//**********************************************************************************


$TEMP_IP = getenv("REMOTE_ADDR");
$PG_IP  = substr($TEMP_IP,0, 10);

//if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138" || $TEMP_IP == "39.115.212.9" || $TEMP_IP == "39.115.212.10" )  //PG에서 보냈는지 IP로 체크
if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138" || $TEMP_IP == "39.115.212.9" || $TEMP_IP == "39.115.212.10" || $TEMP_IP == "183.109.71.50" || $TEMP_IP == "183.109.71.30" || $TEMP_IP == "183.109.71.153" )  //PG에서 보냈는지 IP로 체크

{
        $msg_id = $msg_id;             //메세지 타입
        $no_tid = $no_tid;             //거래번호
        $no_oid = $no_oid;             //상점 주문번호
        $order_no = $no_oid;
        $id_merchant = $id_merchant;   //상점 아이디
        $cd_bank = $cd_bank;           //거래 발생 기관 코드
        $cd_deal = $cd_deal;           //취급 기관 코드
        $dt_trans = $dt_trans;         //거래 일자
        $tm_trans = $tm_trans;         //거래 시간
        $no_msgseq = $no_msgseq;       //전문 일련 번호
        $cd_joinorg = $cd_joinorg;     //제휴 기관 코드

        $dt_transbase = $dt_transbase; //거래 기준 일자
        $no_transeq = $no_transeq;     //거래 일련 번호
        $type_msg = $type_msg;         //거래 구분 코드 // 0200 정상 , 0400 취소
        $cl_close = $cl_close;         //마감 구분코드
        $cl_kor = $cl_kor;             //한글 구분 코드
        $no_msgmanage = $no_msgmanage; //전문 관리 번호
        $no_vacct = $no_vacct;         //가상계좌번호
        $amt_input = $amt_input;       //입금금액
        $amt_check = $amt_check;       //미결제 타점권 금액
        $nm_inputbank = iconv('euckr','utf8',$nm_inputbank); //입금 금융기관명
        $nm_input = iconv('euckr','utf8',$nm_input);         //입금 의뢰인
        $dt_inputstd = $dt_inputstd;   //입금 기준 일자
        $dt_calculstd = $dt_calculstd; //정산 기준 일자
        $flg_close = $flg_close;       //마감 전화

        //가상계좌채번시 현금영수증 자동발급신청시에만 전달
        $dt_cshr      = $dt_cshr;       //현금영수증 발급일자
        $tm_cshr      = $tm_cshr;       //현금영수증 발급시간
        $no_cshr_appl = $no_cshr_appl;  //현금영수증 발급번호
        $no_cshr_tid  = $no_cshr_tid;   //현금영수증 발급TID


        // 여기에 DB 설정

        if($type_msg == '0200') {

        $ool_type = 'I';
        $r = _MQ("select * from smart_order_onlinelog where ool_ordernum='$order_no' order by ool_uid desc");
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
                '$no_tid',
                '$ool_type',
                '$dt_trans',
                '$amt_input',
                '$r[ool_amount_total]',
                '$no_vacct',
                '',
                '$nm_input',
                '$nm_inputbank',
                '$cd_bank',
                'Y',
                '',
                '$r[ool_deposit_tel]',
                '$r[ool_bank_owner]'
            )
        ");

        if(!empty($no_cshr_tid)) {
            _MQ_noreturn("update smart_order set o_get_tax='Y' where o_ordernum='$order_no'");
            _MQ_noreturn("insert into smart_order_cashlog (ocs_ordernum,ocs_member,ocs_date,ocs_tid,ocs_cashnum,ocs_respdate,ocs_amount,ocs_method,ocs_type) values ('$order_no','$r[ool_member]',now(),'$no_cshr_tid','$no_cshr_appl','$tm_cshr','$amt_input','AUTH','virtual')");

			$op_name = _MQ("
				select p.p_name, count(*) as cnt
				from smart_order_product as op
				inner join smart_product as p on (p.p_code=op.op_pcode)
				where op_oordernum='{$order_no}'
				group by op_oordernum
			");
			// 현금영수증용 상품명 생성
			$cash_product_name = ($op_name['cnt']>0)?$op_name['p_name'].'외 '.($op_name['cnt']-1).'개':$op_name['p_name'];
			_MQ_noreturn("insert into smart_baro_cashbill (bc_type, bc_ordernum,TradeUsage,IdentityNum,Amount,TradeDate,RegistDT,IssueDT,BarobillState,ItemName,NTSConfirmNum) values
			('pg','$order_no','1','','".$amt_input."',curdate(),now(),now(),'3000','".addslashes($cash_product_name)."','')");
        }

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
			//if($pay_status == 'N') {echo "FAIL";}// 실패처리
			// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----

        }





}













        // 여기까지 DB 설정

        $logfile = fopen( $INIpayHome . "/log/result.log", "a+" );


        fwrite( $logfile,"************************************************");
        fwrite( $logfile,"ID_MERCHANT : ".$id_merchant."\r\n");
        fwrite( $logfile,"NO_TID : ".$no_tid."\r\n");
        fwrite( $logfile,"NO_OID : ".$no_oid."\r\n");
        fwrite( $logfile,"NO_VACCT : ".$no_vacct."\r\n");
        fwrite( $logfile,"AMT_INPUT : ".$amt_input."\r\n");
        fwrite( $logfile,"NM_INPUTBANK : ".$nm_inputbank."\r\n");
        fwrite( $logfile,"NM_INPUT : ".$nm_input."\r\n");
        fwrite( $logfile,"************************************************");

        /*
        fwrite( $logfile,"전체 결과값"."\r\n");
        fwrite( $logfile, $msg_id."\r\n");
        fwrite( $logfile, $no_tid."\r\n");
        fwrite( $logfile, $no_oid."\r\n");
        fwrite( $logfile, $id_merchant."\r\n");
        fwrite( $logfile, $cd_bank."\r\n");
        fwrite( $logfile, $dt_trans."\r\n");
        fwrite( $logfile, $tm_trans."\r\n");
        fwrite( $logfile, $no_msgseq."\r\n");
        fwrite( $logfile, $type_msg."\r\n");
        fwrite( $logfile, $cl_close."\r\n");
        fwrite( $logfile, $cl_kor."\r\n");
        fwrite( $logfile, $no_msgmanage."\r\n");
        fwrite( $logfile, $no_vacct."\r\n");
        fwrite( $logfile, $amt_input."\r\n");
        fwrite( $logfile, $amt_check."\r\n");
        fwrite( $logfile, $nm_inputbank."\r\n");
        fwrite( $logfile, $nm_input."\r\n");
        fwrite( $logfile, $dt_inputstd."\r\n");
        fwrite( $logfile, $dt_calculstd."\r\n");
        fwrite( $logfile, $flg_close."\r\n");
        fwrite( $logfile, "\r\n");
        */

        fclose( $logfile );


//************************************************************************************

        //위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로
        //리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
        //(주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
        //기타 다른 형태의 PRINT( echo )는 하지 않으시기 바랍니다

//      if (데이터베이스 등록 성공 유무 조건변수 = true)
//      {

                echo "OK";                        // 절대로 지우지마세요

//      }

//*************************************************************************************

}

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행