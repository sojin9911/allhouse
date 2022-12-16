<?php
# 게시판 댓글등록
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 글 번호나 비밀번호 없을시
if(!$_uid || !$passwd) error_alt("잘못된 접근입니다. 다시 시도하세요");

$passwd = _MQ_result(" select password('".$passwd."') ");

$r = _MQ("select * from smart_bbs where b_uid = '".$_uid."'");

// -- 답글일경우 부모글을 가져온다.
if( $r['b_depth'] > 1 && $v['b_relation'] > 0){ $re = _MQ("select * from smart_bbs where b_uid='".$r[b_relation]."'"); } 
$is_auth = $r[b_passwd] == $passwd && $passwd ? true : false;
if(!$is_auth) { $is_auth = $re['b_passwd'] == $passwd && $passwd ? true : false; } // 2뎁스이고 1뎁스의 글의 권한이 있을경우

if($is_auth !== true){
	error_alt("비밀번호가 맞지 않습니다.");
}else {
	// -- 보안상 세션으로 굽는다.
	$authCode = onedaynet_encode($siteInfo['s_license'].$_uid);
	$_SESSION['authPostItem'][$_uid] = $authCode; 
}
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
switch($_mode) {
	case "view" :
		error_frame_loc("/?pn=board.view&_menu=".$r['b_menu']."&_uid=".$_uid."&_PVSC=".$_PVSC);
	break;
	case "modify" :
		error_frame_loc("/?pn=board.form&_mode=modify&_menu=".$r['b_menu']."&_uid=".$_uid."&_PVSC=".$_PVSC);
	break;
	case "delete" :
		error_loc(OD_PROGRAM_URL."/board.pro.php?_mode=delete&_uid=".$_uid."&_PVSC=".$_PVSC);
	break;
}