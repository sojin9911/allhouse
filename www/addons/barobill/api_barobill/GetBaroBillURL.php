<? include '../include/top.php'; ?>
<p>GetBaroBillURL - 바로빌 URL</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';				//연계사업자 아이디
	$PWD = '';				//연계사업자 비밀번호
	$arrTOGO = array(		//URL코드
				array('INTEROPBOX',	'임시저장함'),
				array('SALESBOX',	'매출 보관함'),
				array('PURCHASEBOX','매입 보관함'),
				array('CLIENTANAL',	'거래처별 통계'),
				array('WRITE',		'세금계산서 작성'),
				array('NTSOPT',		'국세청 전송설정'),
				array('LOGIN',		'SSO로그인'),			//GetLoginURL 함수를 대체함
				array('CHRG',		'포인트 충전'),			//GetCashChargeURL 함수를 대체함
				array('CERT',		'공인인증서 등록'),		//GetCertificateRegistURL 함수를 대체함
				array('JICIN',		'인감 등록'),			//GetJicInRegistURL 함수를 대체함
				array('BLICENSE',	'사업자등록증 등록'),
				array('BANKBOOK',	'통장사본 등록')
			);

	foreach($arrTOGO as $TOGO){

		echo "<b>$TOGO[1] :</b><br />";

		$Result = $BaroService_TI->GetBaroBillURL(array(
					'CERTKEY'		=> $CERTKEY,
					'CorpNum'		=> $CorpNum,
					'ID'			=> $ID,
					'PWD'			=> $PWD,
					'TOGO'			=> $TOGO[0]
					))->GetBaroBillURLResult;

		if ($Result < 0){
			echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
		}else{
			echo "<a href=\"$Result\" target=\"_blank\">$Result</a>";
		}

		echo '<hr />';
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
