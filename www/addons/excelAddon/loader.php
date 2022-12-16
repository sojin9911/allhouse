<?php
# 자동 excel 로더 변수
function ExcelLoader($File) {
	// KAY :: 일괄업로드 :: 2021-07-02 -- xlsx 파일 업로드
	$fileKey =array_keys($_FILES);
	$exFilename = end(@explode(".",$_FILES[$fileKey[0]]['name']));

	// 수정 전 소스코드
	/*if(preg_match("`.xlsx`", $File)) {
		include_once(OD_ADDONS_ROOT.'/excelAddon/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($File);
		$Edata = $xlsx->rows();
		$Name = $xlsx->sheetNames();
		$Edata = @array_merge(array(array("File"=>$File, "Name"=>$Name[0])), $Edata);
	}*/

	if(preg_match("/(xlsx)/i", $exFilename)) { // 파일명 xlsx찾는 부분에서 오류있던거 수정
		include_once(OD_ADDONS_ROOT.'/excelAddon/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($File);
		$Edata = $xlsx->rows();
		$Name = $xlsx->sheetNames();
		$Edata = @array_merge(array(array("File"=>$File, "Name"=>$Name[0])), $Edata);
	}

	// 수정 전 소스코드
	/*
	else {
		include_once(OD_ADDONS_ROOT.'/excelAddon/xls_reader.php');
		# 클래스 생성
		$Excel = new Spreadsheet_Excel_Reader();

		# 출력 인코딩 설정
		$Excel->setOutputEncoding('UTF-8');

		# 엑셀파일 호출
		$Excel->read($File);

		# 변수 함축
		$Edata = $Excel->sheets['0']['cells'];
		$Edata = array_merge(array(array("File"=>$File, "Name"=>$Excel->boundsheets[0]['name'])), $Edata);
	}
	return $Edata;*/

	else {
		include_once(OD_ADDONS_ROOT.'/excelAddon/xls_reader.php');

		//html 변환을 위해  PHPExcel-1.8 다운 :: http://www.codeplex.com/PHPExcel
		include_once (OD_ADDONS_ROOT.'/PHPExcel-1.8/Classes/PHPExcel.php');
		include_once ($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

		$Excel = new Spreadsheet_Excel_Reader(); // 클래스생성
		$Excel->setOutputEncoding('UTF-8'); // 출력 인코딩 설정

		// 엑셀파일 호출
		$excel_res = $Excel->read($File);

		// KAY :: 일괄업로드 :: 2021-07-02 -- 엑셀 다운로드(웹페이지로 다운) 한 후 바로 업로드시 html로 변환 후 업로드
		if($excel_res == 1){
			// 읽어드린 엑셀파일 -> $_FILES 일경우 해당 /tmp 파일의 경로
			$Edata = file_get_contents($File);
			$File  = '/tmp/'.uniqid().'.html';

			file_put_contents($File , $Edata);

			// 임시파일 저장 후 로드
			$reader = new PHPExcel_Reader_HTML;
			$content = $reader->load($File);

			unlink( $File );

			// 엑셀 출력
			PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
			$writer = PHPExcel_IOFactory::createWriter($content, 'Excel5');
			ob_end_clean();

			ob_start();
			$writer->save('php://output');
			$Edata = ob_get_clean();
			$File  = '/tmp/'.uniqid().'.html';
			file_put_contents($File , $Edata);

			unset($Excel);
			$Excel = new Spreadsheet_Excel_Reader(); // 클래스생성
			$Excel->setOutputEncoding('UTF-8'); // 출력 인코딩 설정
			$Excel->read($File);
		}

		// 변수 함축
		$Edata = $Excel->sheets['0']['cells'];
		$Edata = array_merge(array(array("File"=>$File, "Name"=>$Excel->boundsheets[0]['name'])), $Edata);

		// KAY :: 일괄업로드 :: 2021-07-02 -- 다운로드 후 웹 페이지로 저장시 열리지 않게 하기위한 작업
		if( $Edata[2][0] =='이 페이지에는 프레임이 있지만 사용 중인 브라우저에서 프레임을 지원하지 않습니다.'){
			echo "<script>if(typeof parent.progress != 'undefined'){ parent.progress(); }</script>";
			error_msg("파일 포맷이 올바르지 않습니다. 다른이름으로 저장(xls, xlsx)후 다시 시도하여 주십시오.");
		}
	}
	return $Edata;
}