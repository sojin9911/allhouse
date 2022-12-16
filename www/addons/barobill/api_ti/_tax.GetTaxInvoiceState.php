<?php

	// 세금계산서 발행정보 추출
	$taxInfo = _MQ(" select * from smart_baro_tax where MgtKey = '{$MgtNum}' ");

	//$CERTKEY = '';			//인증키
	$CorpNum = rm_str($siteInfo['s_company_num']);			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtNum = $taxInfo['MgtKey'];			//자체문서관리번호

	$Result2 = $BaroService_TI->GetTaxInvoiceState(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtNum
				))->GetTaxInvoiceStateResult;


	if (is_null($Result2)){
		//echo '문서 상태를 불러오지 못했습니다.';
		//_MQ_noreturn(" update smart_baro_tax set Status ='-9999' where bt_uid='". $taxInfo['bt_uid'] ."' ");
	}else if ($Result2->BarobillState < 0){
		//echo "오류코드 : $Result2->BarobillState<br><br>".getErrStr($CERTKEY, $Result2->BarobillState);
		//_MQ_noreturn(" update smart_baro_tax set Status ='". $Result2->BarobillState ."' where bt_uid='". $taxInfo['bt_uid'] ."' ");
	}else{
		_MQ_noreturn(" update smart_baro_tax set Status ='". $Result2->BarobillState ."' where bt_uid='". $taxInfo['bt_uid'] ."' ");

		//  정산완료 데이터 상태 변환
		if($taxInfo['TaxInvoiceType'] <> 2){ // 과세
			_MQ_noreturn(" update smart_order_settle_complete set s_tax_status = '". $Result2->BarobillState ."' where s_uid = '". $taxInfo['bt_suid'] ."' ");
		}else{ // 면세
			_MQ_noreturn(" update smart_order_settle_complete set s_tax_status_vat_n = '". $Result2->BarobillState ."' where s_uid = '". $taxInfo['bt_suid'] ."' ");
		}

		//echo "자체문서관리번호 : $Result2->MgtKey<br>
		//	바로빌문서관리번호 : $Result2->InvoiceKey<br>
		//	바로빌상태코드 : $Result2->BarobillState : ". $arr_inner_state_table[$Result2->BarobillState] ."<br>
		//	개봉여부 : $Result2->IsOpened<br>
		//	메모1 : $Result2->Remark1<br>
		//	메모2 : $Result2->Remark2<br>
		//	국세청전송상태 : $Result2->NTSSendState<br>
		//	국세청승인번호 : $Result2->NTSSendKey<br>
		//	국세청전송결과 : $Result2->NTSSendResult<br>
		//	국세청전송일시 : $Result2->NTSSendDT<br>
		//	전송결과수신일시 : $Result2->NTSResultDT";
	}
