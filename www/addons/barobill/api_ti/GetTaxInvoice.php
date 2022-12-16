<? include '../include/top.php'; ?>
<p>GetTaxInvoice - 문서 정보 (자체문서관리번호)</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호

	$Result = $BaroService_TI->GetTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->GetTaxInvoiceResult;

	if ($Result->InvoiceKey == ''){
		echo "문서 정보를 불러오지 못했습니다.";
	}else{
		echo "바로빌문서관리번호 : ".$Result->InvoiceKey."<br>
				작성일자 : ".$Result->WriteDate."<br>
				공급가액 : ".$Result->AmountTotal."<br>
				세액 : ".$Result->TaxTotal."<br>
				합계금액 : ".$Result->TotalAmount."<br>
				--------------------------------------------------------<br>
				-- 공급자 --<br>
				관리번호 :".$Result->InvoicerParty->MgtNum."<br>
				사업자번호 :".$Result->InvoicerParty->CorpNum."<br>
				회사명 : ".$Result->InvoicerParty->CorpName."<br>
				대표자명 : ".$Result->InvoicerParty->CEOName."<br>
				주소 : ".$Result->InvoicerParty->Addr."<br>
				업태 : ".$Result->InvoicerParty->BizType."<br>
				업종 : ".$Result->InvoicerParty->BizClass."<br>
				아이디 : ".$Result->InvoicerParty->ContactID."<br>
				담당자 : ".$Result->InvoicerParty->ContactName."<br>
				전화 : ".$Result->InvoicerParty->TEL."<br>
				휴대폰 : ".$Result->InvoicerParty->HP."<br>
				이메일 : ".$Result->InvoicerParty->Email."<br>
				--------------------------------------------------------<br>
				-- 공급받는자 --<br>
				관리번호 :".$Result->InvoiceeParty->MgtNum."<br>
				사업자번호 : ".$Result->InvoiceeParty->CorpNum."<br>
				회사명 : ".$Result->InvoiceeParty->CorpName."<br>
				대표자명 : ".$Result->InvoiceeParty->CEOName."<br>
				주소 : ".$Result->InvoiceeParty->Addr."<br>
				업태 : ".$Result->InvoiceeParty->BizType."<br>
				업종 : ".$Result->InvoiceeParty->BizClass."<br>
				아이디 : ".$Result->InvoiceeParty->ContactID."<br>
				담당자 : ".$Result->InvoiceeParty->ContactName."<br>
				전화 : ".$Result->InvoiceeParty->TEL."<br>
				휴대폰 : ".$Result->InvoiceeParty->HP."<br>
				이메일 : ".$Result->InvoiceeParty->Email."<br>
				--------------------------------------------------------<br>
				-- 수탁자 --<br>
				관리번호 :".$Result->BrokerParty->MgtNum."<br>
				사업자번호 : ".$Result->BrokerParty->CorpNum."<br>
				회사명 : ".$Result->BrokerParty->CorpName."<br>
				대표자명 : ".$Result->BrokerParty->CEOName."<br>
				주소 : ".$Result->BrokerParty->Addr."<br>
				업태 : ".$Result->BrokerParty->BizType."<br>
				업종 : ".$Result->BrokerParty->BizClass."<br>
				아이디 : ".$Result->BrokerParty->ContactID."<br>
				담당자 : ".$Result->BrokerParty->ContactName."<br>
				전화 : ".$Result->BrokerParty->TEL."<br>
				휴대폰 : ".$Result->BrokerParty->HP."<br>
				이메일 : ".$Result->BrokerParty->Email."<br>";

		if (!is_null($Result->TaxInvoiceTradeLineItems->TaxInvoiceTradeLineItem)){
			$Item = $Result->TaxInvoiceTradeLineItems->TaxInvoiceTradeLineItem;

			echo '--------------------------------------------------------<br>
				-- 품목 --<br>';

			if (sizeof($Item) == 1){
				echo "$Item->PurchaseExpiry,
					$Item->Name,
					$Item->Information,
					$Item->ChargeableUnit,
					$Item->UnitPrice,
					$Item->Amount,
					$Item->Tax,
					$Item->Description<br>";
			}else{
				foreach($Item as $tl){
					echo "$tl->PurchaseExpiry,
						$tl->Name,
						$tl->Information,
						$tl->ChargeableUnit,
						$tl->UnitPrice,
						$tl->Amount,
						$tl->Tax,
						$tl->Description<br>";
				}
			}
		}
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
