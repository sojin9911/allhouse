<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// SNS 로그인 후 이동할 페이지를 위한 처리
if(in_array($pn,array('member.login.form')) == true) {
	if(!$_COOKIE['_rurl']) samesiteCookie('_rurl', $_rurl , 0 , '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
}else{
	samesiteCookie("_rurl", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
}

// ------ .onedaynet.co.kr 이 붙어서 원데이넷 모든 서브도메인이 먹통되는 현상 수정 ------ 2019-03-13 LCY
@samesiteCookie("AuthShopCOOKIEID", "" , 0, "/" , ".onedaynet.co.kr");
// ------ .onedaynet.co.kr 이 붙어서 원데이넷 모든 서브도메인이 먹통되는 현상 수정 ------ 2019-03-13 LCY


# 옵션/장바구니/비회원 구매를 위한 쿠키 적용
if(!$_COOKIE['AuthShopCOOKIEID']) samesiteCookie('AuthShopCOOKIEID', md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));


# 세션생성 위치 변경
if($_GET[pn] == 'shop.order.form' && $_COOKIE["AuthShopCOOKIEID"]) {
	if(substr(phpversion(),0,3) < 5.4) { session_register("order_start"); }
	$_SESSION["order_start"] = $_COOKIE["AuthShopCOOKIEID"];
}



# 최근 본 상품 처리
if(isset($_GET['pcode']) && $_GET['pn'] == 'product.view') {

	// - 최근 본 상품 업데이트(쿠키생성)  ---
	if(!$_COOKIE['AuthProductLatest']){
		// -- AuthSDProductLatest 없을 경우 적용 ---
		$appAuthSDProductLatest = '';
		for( $i=0; $i<9 ; $i++ ){
			if( rand(1,2) == 1 ) $appAuthSDProductLatest .= rand(0,9); // 숫자
			else $appAuthSDProductLatest .= chr(rand(97,122)); // 영문
		}
		samesiteCookie('AuthProductLatest', $appAuthSDProductLatest, time()+3600*24*30, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
		// -- AuthSDProductLatest 없을 경우 적용 ---
	}
	else {
		$appAuthSDProductLatest = $_COOKIE['AuthProductLatest'];
	}
	// 최근 본 상품 업데이트 (쿠키생성)
	// - 최근 본 상품 업데이트 ---
	$plr = _MQ(" select count(*) as cnt from smart_product_latest where pl_pcode='{$pcode}' and pl_uniqkey='{$appAuthSDProductLatest}' ");
	if($plr['cnt'] == 0) _MQ_noreturn(" insert smart_product_latest set pl_pcode='{$pcode}', pl_uniqkey='{$appAuthSDProductLatest}', pl_rdate=now()  ");
	// - 최근 본 상품 업데이트 ---
}



# 파비콘
if(is_mobile() === true) $Favicon = $siteInfo['s_home_icon'];
else $Favicon = $siteInfo['s_favicon'];

// -- 공유시 파비콘 설정 추가 -- 2019-05-22 LCY
$FaviconShare = $siteInfo['s_share_favicon'];
// -- 공유시 파비콘 설정 추가 -- 2019-05-22 LCY

// 2018-11-12 SSJ :: 페이지별 타이틀 설정 패치 ---{
$site_glbtlt = $siteInfo['s_glbtlt']; // 공통 타이틀 저장
$site_glbdsc = $siteInfo['s_glbdsc']; // 2019-12-02 SSJ :: 공통 desc 추가
$title_setup = array();

// 기본 페이지 처리
if($pn == 'product.brand_list' && $uid){ // 브랜드상품(브랜드 선택 시)
    $app_pn_title = '/?pn='.$pn.'&uid=';
}else if($pn){
    $app_pn_title = '/?pn='.$pn;
}
$title_setup = _MQ(" select * from smart_site_title where sst_page like '%§§". $app_pn_title ."§§%' ");

// 추가적용 페이지 처리
if($title_setup['sst_uid'] == ''){
    $title_setup = _MQ(" select * from smart_site_title where sst_page like '%§§". $_SERVER['REQUEST_URI'] ."§§%' ");
}

// 타이틀 문구 설정
if($title_setup['sst_title'] <> ''){
    $siteInfo['s_glbtlt'] = trim(stripslashes($title_setup['sst_title']));
    $siteInfo['s_glbdsc'] = trim(stripslashes($title_setup['sst_desc'])); // 2019-12-02 SSJ :: 공통 desc 추가
}else{
    $siteInfo['s_glbtlt'] = $site_glbtlt;
    $siteInfo['s_glbdsc'] = $site_glbdsc; // 2019-12-02 SSJ :: 공통 desc 추가
}

// 기본 치환자
$arrTitleReplace = array();
$arrTitleReplace['{공통타이틀}'] = $site_glbtlt;
$arrTitleReplace['{사이트명}'] = $siteInfo['s_adshop'];
$arrTitleReplace['{검색어}'] = trim($search_word);
$arrTitleReplace['{Description}'] = $site_glbdsc; // 2019-12-02 SSJ :: 공통 desc 추가
// 치환자 초기화
$arrTitleReplace['{카테고리명}'] = ''; $arrTitleReplace['{상품명}'] = ''; $arrTitleReplace['{게시판명}'] = ''; $arrTitleReplace['{게시물제목}'] = ''; $arrTitleReplace['{기획전명}'] = ''; $arrTitleReplace['{브랜드명}'] = '';
// 2019-12-02 SSJ :: 공통 desc 추가
$arrTitleReplace['{부가상품명}'] = ''; $arrTitleReplace['{상품판매가}'] = ''; $arrTitleReplace['{게시물내용}'] = '';
// 치환자 추출
if($cuid && preg_match("/{카테고리명}/i" , $siteInfo['s_glbtlt'].$siteInfo['s_glbdsc'])) $arrTitleReplace['{카테고리명}'] = trim(stripslashes(_MQ_result(" select c_name from smart_category where c_uid = '". $cuid ."' ")));
if($_event == 'type' && $typeuid && preg_match("/{카테고리명}/i" , $siteInfo['s_glbtlt'].$siteInfo['s_glbdsc'])) $arrTitleReplace['{카테고리명}'] = trim(stripslashes(_MQ_result(" select dts_name from smart_display_type_set where dts_uid = '". $typeuid ."' ")));
if($pcode && preg_match("/{상품명}/i" , $siteInfo['s_glbtlt'].$siteInfo['s_glbdsc'])) $arrTitleReplace['{상품명}'] = trim(stripslashes(_MQ_result(" select p_name from smart_product where p_code = '". $pcode ."' ")));
if($pcode && preg_match("/{부가상품명}/i" , $siteInfo['s_glbdsc'])) $arrTitleReplace['{부가상품명}'] = trim(stripslashes(_MQ_result(" select p_subname from smart_product where p_code = '". $pcode ."' ")));
if($pcode && preg_match("/{상품판매가}/i" , $siteInfo['s_glbdsc'])) $arrTitleReplace['{상품판매가}'] = number_format(trim(stripslashes(_MQ_result(" select p_price from smart_product where p_code = '". $pcode ."' "))));
// 게시판명 추출
if(in_array($pn, array('board.list','board.view','board.form'))){
    if($_menu && preg_match("/{게시판명}/i" , $siteInfo['s_glbtlt'].$siteInfo['s_glbdsc'])) $arrTitleReplace['{게시판명}'] = trim(stripslashes(_MQ_result(" select bi_name from smart_bbs_info where bi_uid = '". $_menu ."' ")));
    if($_uid && preg_match("/{게시물제목}/i" , $siteInfo['s_glbtlt'].$siteInfo['s_glbdsc'])) $arrTitleReplace['{게시물제목}'] = trim(stripslashes(_MQ_result(" select b_title from smart_bbs where b_uid = '". $_uid ."' ")));
    if($_uid && preg_match("/{게시물내용}/i" , $siteInfo['s_glbdsc'])){ // 2019-12-02 SSJ :: 공통 desc 추가
		$app_bbs_content = _MQ_result(" select b_content from smart_bbs where b_uid = '". $_uid ."' ");
		$app_bbs_content = addslashes(htmlspecialchars(trim(str_replace(array("\r", "\n", "\n\r", "\r\n", "\t"), '', strip_tags($app_bbs_content)))));
		$arrTitleReplace['{게시물내용}'] = $app_bbs_content;
	}
}
// 기획전명 추출
if($pn == 'product.promotion_view' && $uid && preg_match("/{기획전명}/i" , $siteInfo['s_glbtlt'])){
    $arrTitleReplace['{기획전명}'] = trim(stripslashes(_MQ_result(" select pp_title from smart_promotion_plan where pp_uid = '". $uid ."' ")));
}
// 브랜드명 추출
if($pn == 'product.brand_list' && $uid && preg_match("/{브랜드명}/i" , $siteInfo['s_glbtlt'])){
    $arrTitleReplace['{브랜드명}'] = trim(stripslashes(_MQ_result(" select c_name from smart_brand  where c_uid = '". $uid ."' ")));
}

// 치환자 적용
$siteInfo['s_glbtlt'] = str_replace(array_keys($arrTitleReplace),array_values($arrTitleReplace), $siteInfo['s_glbtlt']);
$siteInfo['s_glbtlt'] = $siteInfo['s_glbtlt'] ? $siteInfo['s_glbtlt'] : $site_glbtlt;
// 치환자 적용 - // 2019-12-02 SSJ :: 공통 desc 추가
$siteInfo['s_glbdsc'] = str_replace(array_keys($arrTitleReplace),array_values($arrTitleReplace), $siteInfo['s_glbdsc']);
$siteInfo['s_glbdsc'] = $siteInfo['s_glbdsc'] ? $siteInfo['s_glbdsc'] : $site_glbdsc;
// }--- 2018-11-12 SSJ :: 페이지별 타이틀 설정 패치

// -- {canonical} 적용 :: inc.header.php PC/모바일 개별적용  -- 2019-11-28 LCY
if(is_https() === true) $site_domain = 'https://'.$_SERVER['HTTP_HOST'];
else $site_domain = 'http://'.($siteInfo['s_ssl_domain']?$siteInfo['s_ssl_domain']:$_SERVER['HTTP_HOST']);
$canonical_url = $site_domain.$_SERVER['REQUEST_URI'];
//$canonical_url = preg_replace("/(\/\/)/","/",$canonical_url); // LCY :: 연속 슬래시 제거 2019-11-28

# Open Graph
$og_type = 'website'; // 사이트 분류 - 페이스북
$og_type2 = 'summary'; // 사이트 분류 - 트위터카드
$og_title = htmlspecialchars($siteInfo['s_glbtlt']);  // 사이트명
$og_description = htmlspecialchars(str_replace(array('/', '\\', '"', "'"), '', $siteInfo['s_glbdsc'])); // 사이트 설명
$og_url = $canonical_url;  // 사이트 주소
$og_site_name = $siteInfo['s_glbtlt']; // 사이트명
$og_image = ($Favicon && file_exists($_SERVER['DOCUMENT_ROOT'].IMG_DIR_BANNER.$Favicon)?$site_domain.IMG_DIR_BANNER.$Favicon:null);  // 이미지
$og_app_id = $siteInfo['s_facebook_key'];

// -- 공유시 파비콘 설정 추가 -- 2019-05-22 LCY
$ogFavicon = $FaviconShare && file_exists($_SERVER['DOCUMENT_ROOT'].IMG_DIR_BANNER.$FaviconShare) ? $FaviconShare: $Favicon;
$og_image = ($ogFavicon && file_exists($_SERVER['DOCUMENT_ROOT'].IMG_DIR_BANNER.$ogFavicon)?$system['url'].IMG_DIR_BANNER.$ogFavicon:null);  // 이미지
// -- 공유시 파비콘 설정 추가 -- 2019-05-22 LCY

# 게시판에서 og 변경
if(in_array($pn, array('board.list', 'board.view')) && $_menu) {
	$og_url = $site_domain.'/?pn='.$pn.'&_menu='.$_menu;  // 사이트 주소
	if($_uid){
		$og_post = _MQ(" select b_img1 from smart_bbs where b_uid = '{$_uid}' "); // 게시물 정보 추출
		$og_url = $og_url.'&_uid='.$_uid;  // 사이트 주소
		$og_image = ($og_post['b_img1']?$site_domain.IMG_DIR_BOARD.$og_post['b_img1']:$og_image);  // 이미지
	}
}

# 상품에서 og변경
if(in_array($pn, array('product.list', 'product.view'))) {
	if(isset($_event) && isset($typeuid)) {
		$og_url = $site_domain.'/?pn='.$pn.'&_event='.$_event.'&typeuid='.$typeuid;
	}
	if(isset($cuid)) {
		$og_url = $site_domain.'/?pn='.$pn.'&cuid='.$cuid;
	}
	if(isset($pcode)) {
		$_ogp = _MQ(" select p_img_b1 from smart_product where p_code = '{$pcode}' ");
		$og_url = $site_domain.'/?pn='.$pn.'&pcode='.$pcode;
		$og_image = ($_ogp['p_img_b1'] && file_exists($_SERVER['DOCUMENT_ROOT'].IMG_DIR_PRODUCT.$_ogp['p_img_b1'])?$site_domain.IMG_DIR_PRODUCT.$_ogp['p_img_b1']:$og_image);
	}
}

# 기획전/브랜드에서 uid 허용
if(in_array($pn, array('product.brand_list', 'product.promotion_view')) && $uid) {
	$og_url = $site_domain.'/?pn='.$pn.'&uid='.$uid;
}

# 일반페이지
if($pn == 'pages.view' && $type && $data) {
	$og_url = $site_domain.'/?pn='.$pn.'&type='.$type.'&data='.$data;
}


// 최근게시물 호출 - 범용적으로 사용 하기 위하여 이곳에 위치
$LatestList = get_latest_list();
if(count($LatestList) <= 0) $LatestList = array();


// 찜한 상품 리스트 - 범용적으로 사용 하기 위하여 이곳에 위치
$WishList = array();
if(is_login()) { // 회원일 경우만 조회
	$WishList = _MQ_assoc(" select p.* from smart_product_wish as pw inner join smart_product as p on(pw.pw_pcode = p.p_code) where pw_inid = '".get_userid()."' ");
	if(count($WishList) <= 0) $WishList = array();
}


// 네이버 검색 연관채널
$NWChanel = array();
if(trim($siteInfo['sns_link_instagram']) != '') $NWChanel[] = trim($siteInfo['sns_link_instagram']);
if(trim($siteInfo['sns_link_facebook']) != '') $NWChanel[] = trim($siteInfo['sns_link_facebook']);
if(trim($siteInfo['sns_link_twitter']) != '') $NWChanel[] = trim($siteInfo['sns_link_twitter']);
if(trim($siteInfo['sns_link_blog']) != '') $NWChanel[] = trim($siteInfo['sns_link_blog']);
if(trim($siteInfo['sns_link_cafe']) != '') $NWChanel[] = trim($siteInfo['sns_link_cafe']);
if(trim($siteInfo['sns_link_youtube']) != '') $NWChanel[] = trim($siteInfo['sns_link_youtube']);
if(trim($siteInfo['sns_link_kkp']) != '') $NWChanel[] = trim($siteInfo['sns_link_kkp']);
if(trim($siteInfo['sns_link_kks']) != '') $NWChanel[] = trim($siteInfo['sns_link_kks']);



// JJC : 2020-12-16 : 회사소개(About us) 및 일반페이지 노출여부 확인
//			사용법 : if($normalpage_view['aboutus'] == 1) { } 노출처리
//			wrap.header.php, wrap.footer.php  메뉴의 노출여부 사용
$normalpage_view = array();
$normal_res = _MQ_assoc(" select * from smart_normal_page where np_view = 'Y' order by null ");
foreach($normal_res as $pk=>$pv) {
	$normalpage_view[$pv['np_id']]++;
}


# 스킨폴더에서 해당 파일 호출
if($none_load_skin !== true) include_once($SkinData['skin_root'].'/'.basename(__FILE__));
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행