<?php
	# KAY :: 에디터 이미지 관리 :: 파일 생성
	/*
		기존(패치 전) 에디터 이미지 최초 패치 실행 파일 == 최초 1회 실행 파일
		-  insert, update만 존재 삭제는 하루한번 실행에서 하도록 함
		- 에디터 이미지 파일 DB화 하는 파일
		/http://smart.gobeyond.co.kr/program/_1day.update_editor_img.php
	*/

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


	//actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


	//	에디터 이미지 파일(디렉토리) 경로
	$img_dir = "..".IMG_DIR_SMARTEDITOR;

	// 제외 확장자
	$ignored = array('.','..','.svn','.htaccess');

	// 스마트 에디터 파일 스캔 ( scandir = 디렉토리 스캔 함수 )
	$scan = array_values(array_diff(scandir($img_dir),$ignored));

	// --- 스캔한 파일DB저장 LOOP ---
	foreach($scan as $file_name){

		// 디렉토리체크로 디렉토리가 아닌 파일이 맞는지 체크		
		$file_chk = $img_dir . $file_name;

		if(!is_dir( $file_chk)){
			$file_name = str_replace('%2F','/',urlencode($file_name)); // 한글명 이미지 출력을 위한 인코딩

			// $img_name[0] = 확장자 제외한 이미지명, $img_name[1] = 확장자 
			$img_name = explode(".",$file_name);
			$ext = strtolower($img_name[1]);			// 확장자

			// 정한 확장자가 있을 경우 추가 및 업데이트
			if(preg_match("/gif|jpg|jpeg|bmp|png/i" , $ext)){
				 
				// 이미지 파일 등록일, 수정일 ( filemtime = 파일 (등록,수정)일 함수 )
				$img_rdate = date("Y-m-d H:i:s",filemtime($file_chk));

				// --- 파일관리 DB 저장 ---
				_MQ_noreturn("
					INSERT INTO smart_editor_images_files
					(eif_img, eif_rdate)
					VALUES
						('".$file_name."', '".$img_rdate."')
					ON DUPLICATE KEY UPDATE
						eif_rdate = '".$img_rdate."'
				");
				// --- 파일관리 DB 저장 ---
			}
		}
	}
	// --- 스캔한 파일DB저장 LOOP ---


	// 최초 실행 = 전체 파일 DB 정보추출
	$res = _MQ_assoc("SELECT * FROM smart_editor_images_files WHERE 1 ");
	if(sizeof($res)>0){
		
		// --- 파일 사용관리 LOOP ---
		foreach($res as $k => $r){

			// 업데이트 체크 변수 초기화
			$ei_update_chk = 0;

			// 에디터 사용되는 곳 변수 가져와 값 추출
			foreach(array_values($ei_group) as $v){

				// 다른테이블에서 사용하는지 like 검색
				$ei_use = _MQ_assoc("SELECT ".$v['uid']." FROM ".$v['table']." WHERE ".$v['content']." LIKE '%{$r['eif_img']}%'");

				// 사용할 경우 사용관리DB에 값 저장, 변경할 값이 있는 지 없는 지 체크
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

						$ei_update_chk++;	//사용하는 곳 개수체크
					}
				}
			}

			// 사용 개수 업데이트
			if( $ei_update_chk > 0 ){
				_MQ_noreturn("UPDATE smart_editor_images_files SET eif_use_cnt='{$ei_update_chk}' , eif_rdate = now() where eif_uid = '{$r['eif_uid']}'");
			}
		}
		// --- 파일 사용관리 LOOP ---
	}
	ViewArr('에디터 이미지 파일 업로드가 완료되었습니다.');

	//actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행