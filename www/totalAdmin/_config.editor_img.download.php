<?php 
	# KAY :: 에디터 이미지 관리 :: 파일 생성
	// 에디터 이미지 다운로드 처리 파일

	@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림
	include "./inc.php";

	//이미지 다운로드
	$r = _MQ("
		SELECT 
			eif.eif_img
		FROM smart_editor_images_use as eiu
		INNER JOIN smart_editor_images_files as eif on (eiu.eiu_eifuid = eif.eif_uid )
		where 
			eif.eif_uid  = '{$uid}'
	");

	$filename = iconv("UTF-8", "EUC-KR", $r['eif_img']); // 파일이 한글명일때 변환 필요

	$edit_path = $_SERVER['DOCUMENT_ROOT'].IMG_DIR_SMARTEDITOR.$filename;

	$edit_imgsize = filesize($edit_path);

	header("Pragma: public");
	header("Expires: 0");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: $edit_imgsize");
	readfile($edit_path);