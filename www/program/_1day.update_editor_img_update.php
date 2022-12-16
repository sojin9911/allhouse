<?php

	# KAY :: 에디터 이미지 관리 :: 파일 생성
	// 에디터 이미지 1일에 한번 실행. (3일 이내 수정된 파일 업데이트) , 현재 기준으로 일주일동안 수정안할 시 사용여부 체크 후 사용안하면 삭제

	/*
		/program/_auto_load.php -> curl_async -> /program/_point.update.php 에서 include 1일 1회 실행
		/http://smart.gobeyond.co.kr/program/_1day.update_editor_img_update.php
	*/

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

	//actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

	// 에디터 이미지 디렉토리 경로
	$img_dir = "..".IMG_DIR_SMARTEDITOR;

	// 제외 확장자
	$ignored = array('.','..','.svn','.htaccess');

	// smarteditor 파일 스캔 ( scandir = 디렉토리 스캔 함수 )
	$scan = array_values(array_diff(scandir($img_dir),$ignored));
	$scan = array_filter($scan);

	// --- 스캔한 파일관리 DB 저장 LOOP ---
	foreach($scan as $file_name){

		// $img_dir.$file_name = 파일경로.파일명
		// 디렉토리가 아닌 파일이 맞는지 체크		
		$file_chk = $img_dir . $file_name;
		if(!is_dir( $file_chk)){

			//$file_name = iconv("EUC-KR" , "UTF-8" , $file_name) ; // 한글명 인코딩 수정

			$file_name = str_replace('%2F','/',urlencode($file_name)); // 한글명 이미지 출력을 위한 인코딩

			// $img_name[0] = 확장자 제외한 이미지명, $img_name[1] = 확장자 
			$img_name = explode(".",$file_name);
			$ext = strtolower($img_name[1]);			// 확장자

			// 정해진 확장자가 있을 경우 추가 및 업데이트
			if(preg_match("/gif|jpg|jpeg|bmp|png/i" , $ext)){
				 
				// 이미지 파일 등록일, 수정일 ( filemtime = 파일 (등록,수정)일 함수 )
				$img_rdate = date("Y-m-d H:i:s",filemtime($file_chk));

				// 수정일자를 기준으로 3일 이내 값 추출 후 업데이트
				if( DATE("Ymd" , strtotime($img_rdate) + 3600 * 24 * 3 ) >= DATE("Ymd") ){

					// --- 파일관리 DB 저장 ---
					_MQ_noreturn("
						INSERT INTO smart_editor_images_files
							(eif_img, eif_rdate)
						VALUES
							('".$file_name."', '".$img_rdate."' )
						ON DUPLICATE KEY UPDATE
							eif_rdate = '".$img_rdate."'
					");
					// --- 파일관리 DB 저장 ---
				}
			}
		}
	}
	// --- 스캔한 파일관리 DB 저장 LOOP ---
	// 현재날짜를 기준으로 일주일전 파일관리 DB정보 추출
	$res = _MQ_assoc("SELECT * FROM smart_editor_images_files WHERE 1 AND DATE_ADD(DATE(eif_rdate)  , INTERVAL + 1 WEEK ) <= CURDATE() AND eif_use_cnt <=0 ");

	if(sizeof($res)>0){
		
		// --- 파일 사용관리 LOOP ---
		foreach($res as $k => $r){

			// 업데이트 체크 변수 초기화
			$ei_update_chk = 0;

			// 이미지 사용되는 곳 변수 가져와 값 추출
			foreach(array_values($ei_group) as $v){

				// 다른테이블에서 사용하는지 like 검색
				$ei_use = _MQ_assoc("select ".$v['uid']." from ".$v['table']." where ".$v['content']." like '%{$r['eif_img']}%'");

				// 사용하는 곳이 있는 경우 사용관리DB에 값 저장
				if(sizeof($ei_use) > 0){
					foreach($ei_use as $ek => $ev){

						// --- 사용관리 DB 저장 ---
						_MQ_noreturn("
							INSERT INTO smart_editor_images_use
								(eiu_tablename, eiu_datauid, eiu_eifuid)
							VALUES
								('".$v['use_table']."', '".$ev[$v['uid']]."','".$r['eif_uid']."')
							ON DUPLICATE KEY UPDATE
								eiu_dummy = 0
						");
						// --- 사용관리 DB 저장 ---

						// 사용하는 곳 개수체크
						$ei_update_chk++;
					}
				}
			}

			// 파일관리 DB에 사용 개수 업데이트
			if( $ei_update_chk > 0 ){
				_MQ_noreturn("UPDATE smart_editor_images_files SET eif_use_cnt='{$ei_update_chk}' , eif_rdate = now() where eif_uid = '{$r['eif_uid']}'");
			}

			// 이미지 파일관리, 사용관리 DB 조인 후 정보추출
			$sres = _MQ("
				SELECT 
					eif.eif_img, eiu.eiu_eifuid
				FROM smart_editor_images_files as eif
				LEFT JOIN smart_editor_images_use as eiu on (eiu.eiu_eifuid = eif.eif_uid) 
				WHERE 
					eif.eif_uid = '{$r['eif_uid']}'
			");

			$filename = $sres['eif_img'];

			$filename = str_replace('%2F','/',urldecode($filename)); // 한글명 이미지 출력을 위한 인코딩
			
			// 이미지 경로로 파일이 있는지 체크
			$file_use_chk = @file_exists($_SERVER['DOCUMENT_ROOT'] . IMG_DIR_SMARTEDITOR.$filename);

			// 이미지 사용X 
			if(!$file_use_chk || !$sres['eiu_eifuid']){
				_MQ_noreturn("DELETE FROM smart_editor_images_files WHERE eif_uid='{$r['eif_uid']}' "); // 파일관리 DB 삭제
				_MQ_noreturn("DELETE FROM smart_editor_images_use WHERE eiu_eifuid='{$r['eif_uid']}' ");	// 사용관리 DB 삭제
			}
		}
		// --- 파일 사용관리 LOOP ---
	}

	//actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행