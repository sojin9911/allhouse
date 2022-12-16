<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---



# 데이터 조회 쿼리 및 페이징
if( $_menu == '' && rm_str($_uid) > 0){ $_menu = _MQ_result("select b_menu from smart_bbs where b_uid = '".$_uid."'  ");  } // -- 게시판 아이디가 없을 시 처리
$boardInfo = get_board_info($_menu); // 게시판정보 추출
$b_menu = $boardInfo['bi_uid']; // 게시판 아이디
if($boardInfo['bi_view'] == "N") error_msg('사용할 수 없는 게시판입니다.');


# 경로
$boardInfo_tmp['_board_skin'] = ($_device_mode == 'pc'?$boardInfo['bi_skin']:$boardInfo['bi_skin_m']); // 스킨명
$boardInfo_tmp['_board_dir'] = ($_device_mode == 'pc'?OD_BOARD_SKIN_DIR:OD_BOARD_MSKIN_DIR).'/'.$boardInfo_tmp['_board_skin'];
$boardInfo_tmp['_board_root'] = $_SERVER['DOCUMENT_ROOT'].$boardInfo_tmp['_board_dir'];
$boardInfo_tmp['_board_url'] = $system['url'].$boardInfo_tmp['_board_dir'];
$boardInfo_tmp = array_merge($boardInfo_tmp, $boardInfo); // 배열 순서를 위하여 임시정보와 merge
$boardInfo = $boardInfo_tmp;
unset($boardInfo_tmp);

// -- 스킨정보 호출
$boardSkinInfo = getBoardSkinInfo($boardInfo['_board_skin']);

// 권한체크
$boardAuthChk = boardAuthChkAll($_menu,'list');
if( $boardAuthChk['list'] !== true){
	switch($boardAuthChk['list']['code']) {
		case ""  : 	error_msg("본 게시판에 대한 권한이 없습니다."); // 아무것도 없을 시
		case "9995" :	error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']), $boardAuthChk['list']['msg']);
		default  :	error_msg($boardAuthChk['list']['msg']);
	}
}

$boardAuthChk = boardAuthChkAll($_menu,'write'); // 게시판 쓰기권한을 가져온다.

# 데이터 호출
$s_query = " where b_menu='".$b_menu."' ";
if($_GET['searchWord']) {
	$s_query_array = array();

	if( $_GET['searchMode'] == 't' || $_GET['searchMode'] == 'tc'){
		$search_tmp = explode(' ',$_GET['searchWord']);
		foreach($search_tmp as $skk=>$skv) { $s_query_array[] = " replace(b_title,' ','') like '%".trim($skv)."%' "; }
	}
	if( $_GET['searchMode'] == 'c' || $_GET['searchMode'] == 'tc'){
		$search_tmp = explode(' ',$_GET['searchWord']);
		foreach($search_tmp as $skk=>$skv) {
			$s_query_array[] = " replace(b_content,' ','') like '%".trim($skv)."%' ";
			//$s_query_array[] = "   ( b_secret = 'N' or (b_inid = '".get_userid()."' and b_writer_type = 'member' and b_secret = 'Y'  ) )  "; // 비밀글은 내용 검색제외
		}
	}
	$s_query .= (sizeof($s_query_array) > 0 ? " and (".implode(' or ',$s_query_array).")" : "");
	$s_query .= " and ( IF(b_secret = 'Y' and b_writer_type = 'guest', 1 , 0) = 0 ) ";
	if( is_login() ) { $s_query .= " and ( IF(b_secret = 'Y' and b_writer_type = 'member', b_inid, '".get_userid()."') = '".get_userid()."' ) "; }
}

// KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정
if($b_category!=""){
	$s_query .= " and b_category= '".$b_category."' ";
}
$_categoryload =array_filter(explode(",",$boardInfo['bi_category']));

// -- qna 게시판일경우 답글은 제외
if( in_array($boardInfo['bi_list_type'], array('qna')) == true){
	$s_query .=" and b_depth = 1 and b_relation < 1 ";
}

$listmaxcount = $boardInfo[bi_listmaxcnt] ? $boardInfo[bi_listmaxcnt] : 20;	// 미입력시 20개 출력.
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*)  as cnt from smart_bbs {$s_query} ");
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);
$res = _MQ_assoc("
	select
		* ,
		CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid
	from smart_bbs {$s_query}
	ORDER BY b_notice='Y' desc , b_orderuid desc , b_depth asc
	limit $count , $listmaxcount
");

// -- 공통으로 사용하기 위해 데이터 가공 {{{
$listPost = $boardListData =  array();
foreach($res as $k=>$v){

	$listPost[$k]['row'] = $v; // 기본데이터를 담는다. 특수한 경우가 아닌이상 공통 변수 적용을 위해 사용하지 않는다.
	$listPost[$k]['uid'] = $v['b_uid']; // 고유번호
	$arrIcon = $arrTrClass = array(); //  배열 초기화

	// -- KAY :: 게시판 카테고리설정 -- 카테고리 설정 변수
	$listPost[$k]['category']=$v['b_category'];

	// -- 공지일경우 별도 출력 처리
	if( $v['b_notice'] == 'Y'){  $listPost[$k]['num'] = '[공지]'; $arrTrClass[] = 'if_notice'; }
	else{ $listPost[$k]['num'] = $TotalCount - $count - $k;}

	$listPost[$k]['title'] = htmlspecialchars(stripslashes($v['b_title'])); // 게시물 제목
	$listPost[$k]['content'] = strip_tags($v['b_content']); // 게시물 제목
	$listPost[$k]['rdate'] = date('Y-m-d H:i:s',strtotime($v['b_rdate'])); // 작성일
	$listPost[$k]['hit'] = number_format($v['b_hit']);
	$listPost[$k]['thumb'] = number_format($v['b_thumb']);

	// -- 글쓴이 :: 관리자일경우 작성자명은 모두 노출
	$listPost[$k]['writer'] = $boardInfo['bi_writer_view_use'] == 'Y' ? $v['b_writer'] : LastCut($v['b_writer'],1); // 글쓴이
	if( $v['b_writer_type'] == 'admin') { $listPost[$k]['writer'] = $v['b_writer']; }

	// -- 새글일 시
	if(time() - strtotime($v['b_rdate'])< (60*60*24*$boardInfo['bi_newicon_view'])){
		$listPost[$k]['iconNew'] = true;
	}

	// -- 댓글이 0보다 클경우
	if($v['b_talkcnt'] > 0){
		$listPost[$k]['iconReply'] = true;
		$listPost[$k]['talkCnt'] = number_format($v['b_talkcnt']);
	}

	// -- 비밀글일 시
	if($v['b_secret']  == 'Y' ){
		$listPost[$k]['iconSecret'] = true;
	}

	// -- 첨부된 이미지가 있을 시 :: 사진첨부
	if($v['b_img1'] != '' && @is_file($_SERVER['DOCUMENT_ROOT'].IMG_DIR_BOARD.$v['b_img1']) == true){

		if(  in_array($boardInfo['bi_list_type'],array('news','event','gallery')) == false){
			$listPost[$k]['iconPhoto'] = true;
		}
		$listPost[$k]['thumb'] = '<img src="'.IMG_DIR_BOARD.$v['b_img1'].'" alt="'.$listPost[$k]['title'].'" />'; // 리스트 썸네일

	}

	// -- 첨부된 파일이 있을 시
	if( getFilesCount('smart_bbs',$v['b_uid']) > 0){
		$listPost[$k]['iconFile'] = true;
	}

	// -- 답글일경우 판별 :: 디자인 요청 상태
	if($v['b_depth'] > 1 && $v['b_relation'] > 0){ $arrTrClass[] = 'if_reply'; }

	// -- 아이콘 출력
	//$listPost[$k]['icon'] = count($arrIcon) > 0 ? implode($arrIcon) : null;  // 아이콘 출력용
	$listPost[$k]['trClass'] = count($arrTrClass) > 0 ? implode(" ",$arrTrClass) : null;  // 클래스 출력용

	$listPost[$k]['postUrl'] = '/?pn=board.view&_menu='.$v['b_menu'].'&_uid='.$v['b_uid'].($_PVSC<>''?'&_PVSC='.$_PVSC:null);

	// -- 비밀글 판별
	$_secretChk = postSecretChk($v['b_uid']);
	$listPost[$k]['secretEvtClass'] = '';
	if( $_secretChk !== true){
		if(  $v['b_inid'] != ''){
			$listPost[$k]['secretEvtClass'] = ' js_auth_fail'; // 무조건 실패 (권한이 없고 회원등록글이라면)
		}else{
			$listPost[$k]['secretEvtClass'] = ' js_open_auth_pop'; // 비회원글이라면 비밀번호 팝업 이벤트
		}
	}

	// -- event
	$listPost[$k]['eventDate'] = $v['b_sdate'].' ~ '.$v['b_edate'];
	if( $v['b_edate'] < date('Y-m-d') ){
		$listPost[$k]['eventClose'] = true;
		$listPost[$k]['eventDay'] = '마감';
		$listPost[$k]['eventStatusName'] = '종료';
		$listPost[$k]['eventStatusVal'] = 'end';
	}else{
		$listPost[$k]['eventClose'] = false;
		if($v['b_sdate'] <= date('Y-m-d') && $v['b_edate'] >= date('Y-m-d')){ // 진행중
			//$listPost[$k]['eventDay'] = 'D-DAY';
			$listPost[$k]['eventDay'] = '진행';
			$listPost[$k]['eventStatusName'] = '진행';
			$listPost[$k]['eventStatusVal'] = 'ing';
		}else{
			$listPost[$k]['eventStatusName'] = '진행';
			$listPost[$k]['eventStatusVal'] = 'wait';
			// $tempTime = strtotime($v['b_sdate'])-time('Y-m-d');
			// $listPost[$k]['eventDay'] = 'D-'.ceil($tempTime > 86400 ? $tempTime/86400 : 1);
			$listPost[$k]['eventDay'] = '진행전';
		}
	}

	// -- qna
	if( in_array($boardInfo['bi_list_type'], array('qna')) == true){
		$replyCnt = _MQ_result("select count(*) as cnt from smart_bbs where b_relation = '".$v['b_uid']."' and b_depth = '2' ");
		if($replyCnt > 0){ $listPost[$k]['replayStatus'] = 'Y'; }
		else{ $listPost[$k]['replayStatus'] = 'R';  }
		if( $v['b_notice'] == 'Y'){ $listPost[$k]['replayStatus'] = 'N'; } // 공지일경우
	}

}
// -- 공통으로 사용하기 위해 데이터 가공 }}}

// -- 고객센터 /커뮤니티를 위한 변수정의 ///?pn=service.main
$boardHeaderData['viewTypeName'] = $arrBoardViewType[$boardInfo['bi_view_type']];
if( $boardInfo['bi_view_type'] == 'community'){
	$boardHeaderData['viewTypeLink'] = '/?pn=service.eval.list';
}else{
	$boardHeaderData['viewTypeLink'] = '/?pn=service.main';
}


ob_start();
# 스킨호출

include_once($boardInfo['_board_root'].'/'.basename(__FILE__)); // 게시판 스킨폴더에서 해당 파일 호출
$BoardSkinData = ob_get_contents();
ob_end_clean();


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 사이트 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행