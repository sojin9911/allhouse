<?php
include_once(dirname(__FILE__).'/inc.php');
# 파일다운로드 프로그램
if( $_SESSION['filedownAuth'] !== true){ echo '<script>alert("파일다운로드에 대한 권한이 없습니다.");</script>'; exit; } // /include/inc.php 에서 초기화 :: 사이트에 접속한 유저만
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// -- 무분별한 파일다운로드 제어 할 시 이곳에 추가
// if( in_array($_SERVER['REMOTE_ADDR'],array()) == true){	error_msg("파일다운로드에 대한 권한이 없습니다."); exit; }


$row_file = _MQ("SELECT * FROM smart_files WHERE f_uid = '".($_uid)."'  ");
if($row_file['f_realname'] == '') { error_msg("파일이 존재하지 않습니다."); exit; }


// SSJ : 2018-02-05 1:1 문의 게시판 파일 다운로드 권한 체크
if(!is_admin() && $row_file['f_table'] == 'smart_request'){
	if(is_login()){
		// 작성자와 관리자만 다운로드 가능
		$_chk = _MQ(" select count(*) as cnt from smart_request where r_uid = '". $row_file['f_table_uid'] ."' and r_inid = '". $mem_info['in_id'] ."' ");
		if($_chk['cnt']<1){ error_msg("파일다운로드에 대한 권한이 없습니다."); exit; }
	}else{ error_msg("파일다운로드에 대한 권한이 없습니다."); exit; }
}


$filepath = $_SERVER['DOCUMENT_ROOT'].IMG_DIR_FILE.$row_file['f_realname'];
$filesize = filesize($filepath);
$path_parts = pathinfo($filepath);
$filename = $row_file['f_oldname'];
$extension = $path_parts['extension'];
_MQ_noreturn("UPDATE smart_files SET f_download = f_download+1  WHERE 1 and f_uid = '".($_uid)."'   "); // 다운로드 카운트 증가

// @ 2017-03-20 LCY :: 익스에서 한글파일 깨짐현상
if( getChkIE() ) $filename = utf2euc($filename);

header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");
readfile($filepath);

//unset($_SESSION['filedownAuth']);
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행