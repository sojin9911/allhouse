<?php
 	$sFileInfo = '';
	$headers = array();

	foreach($_SERVER as $k => $v) {
		if(substr($k, 0, 9) == "HTTP_FILE") {
			$k = substr(strtolower($k), 5);
			$headers[$k] = $v;
		}
	}

	$filename = rawurldecode($headers['file_name']);
	$filename_ext = strtolower(array_pop(explode('.',$filename)));
	$allow_file = array("jpg", "png", "bmp", "gif");

	$app_ext = end(explode('.', $filename));
	$misec = explode(' ', microtime());
	$filename = sprintf("%u" , crc32($filename  . time() . rand())).'_'.$misec[1].'.'.$app_ext;
	$file->name = $filename;

	if(!in_array($filename_ext, $allow_file)) {
		echo "NOTALLOW_".$filename;
	} else {



		$file = new stdClass;
		$file->name = date("YmdHis").mt_rand().".".$filename_ext;
		$file->content = file_get_contents("php://input");

		// LCY : 2021-08-11 : 1차 이미지 파일 보안 처리 { 
		$s = $file->content;
		if( preg_match("/(\<\?php)/", $s) > 0){ echo "NOTALLOW_FAIL_".rawurldecode($headers['file_name']); exit; }
		// LCY : 2021-08-11 : 1차 이미지 파일 보안 처리 }

		$uploadDir = '../../../../upfiles/smarteditor/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777);
		}

		$newPath = $uploadDir.$file->name;

		if(file_put_contents($newPath, $file->content)) {
			$sFileInfo .= "&bNewLine=true";
			$sFileInfo .= "&sFileName=".$filename;
			$sFileInfo .= "&sFileURL=/upfiles/smarteditor/".$file->name;
			$sFileInfo .= "&sUploadFile=".$file->name;
		}

		echo $sFileInfo;
	}
?>