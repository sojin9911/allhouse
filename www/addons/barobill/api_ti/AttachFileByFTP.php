<? include '../include/top.php'; ?>
<p>AttachFileByFTP - 파일 첨부</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호
	$FileName = '';			//첨부할 파일명
	$DisplayFileName = '';	//다운로드시 보여질 파일명

	//----------------------------------------------------
	//이곳에 FTP 파일 업로드를 구현하세요.

	$BAROSERVICE_FTP = 'ftp://testftp.barobill.co.kr:9031/';	//테스트베드용
	//$BAROSERVICE_FTP = 'ftp://ftp.barobill.co.kr:9030/';		//실서비스용

	$FTP_URL = $BAROSERVICE_FTP.$CorpNum.'/';
	$FTP_ID = '';			//FTP 접속 계정 : 연계사업자 아이디
	$FTP_PWD = '';			//FTP 접속 비밀번호 : 연계사업자 비밀번호

	//----------------------------------------------------
	$Result = $BaroService_TI->AttachFileByFTP(array(
				'CERTKEY'			=> $CERTKEY,
				'CorpNum'			=> $CorpNum,
				'MgtKey'			=> $MgtKey,
				'FileName'			=> $FileName,
				'DisplayFileName'	=> $DisplayFileName
				))->AttachFileByFTPResult;

	//----------------------------------------------------
	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
