<?php
// SSJ 2018-06-08 $_SERVER['DOCUMENT_ROOT'] 의 마지막 / 는 삭제한다.
$_SERVER['DOCUMENT_ROOT'] = preg_replace("/\/$/", "", $_SERVER['DOCUMENT_ROOT']);

// PG 모듈 경로
define('PG_DIR', $_SERVER['DOCUMENT_ROOT'].'/pg/pc');
define('PG_M_DIR', $_SERVER['DOCUMENT_ROOT'].'/pg/m');


// 인증 모듈 경로
define('AUTH_DIR', $_SERVER['DOCUMENT_ROOT'].'/auth');


// 이미지 경로
define('IMG_DIR_NORMAL', '/upfiles/normal/'); // 기본
define('IMG_DIR_BANNER', '/upfiles/banner/'); // 배너
define('IMG_DIR_PRODUCT', '/upfiles/product/'); // 상품
define('IMG_DIR_CATEGORY', '/upfiles/category/'); // 카테고리
define('IMG_DIR_ICON', '/upfiles/icon/'); // 아이콘
define('IMG_DIR_BOARD', '/upfiles/board/'); // 게시판 첨부파일
define('IMG_DIR_POPUP', '/upfiles/popup/'); // 팝업
define('IMG_DIR_FILE', '/upfiles/files/'); // 파일업로드위치
// KAY :: 에디터 이미지 관리 :: 2021-06-07 
define('IMG_DIR_SMARTEDITOR', '/upfiles/smarteditor/'); // 에디터이미지업로드위치

# 일반 도메인과 SSL도메인 지정 2017-06-21 LDD
$system = array(
	'ssl_use' => ($siteInfo['s_ssl_check'] == 'Y'?true:false),
	'ssl_domain' => ($siteInfo['s_ssl_domain']?$siteInfo['s_ssl_domain']:''), // 관리자에서 지정한 ssl 도메인 (ex> example.com)
	'ssl_port' => ($siteInfo['s_ssl_port']?$siteInfo['s_ssl_port']:443), // 관리자에서 지정한 ssl 포트 (ex> 443)
	'ip' => $_SERVER['REMOTE_ADDR'], // 서버아이피 (123.456.789.012)
	'host' => (is_https() === true?reset(explode(':', $_SERVER['HTTP_HOST'])):$_SERVER['HTTP_HOST']), // host - 도메인 (ex> example.com)
	'__url' => '//'.$_SERVER['HTTP_HOST'], // http and https 호환 이미지&스크립트&스타일시트 링크 등에 사용 (ex> //example.com or //example.com:443)
	'url'=> (is_https() === true?'https://':'http://').$_SERVER['HTTP_HOST'], // 풀도메인 (ex> http://example.com or https://example.com)
);
$app_SSL_URL = 'https://'.$system['host'].':'.$system['ssl_port'];
$app_HTTP_URL = 'http://'.$system['host'];



# 기본 경로 정보 2017-06-26 LDD
$_skin = ($siteInfo['s_skin']?$siteInfo['s_skin']:'default'); // 환경설정 PC 사이트 스킨
$_skin_m = ($siteInfo['s_skin_m']?$siteInfo['s_skin_m']:'default'); // 환경설정 Mobile 사이트 스킨
if(isset($_GET['__pskin']) || isset($__pskin)) { // 쿠키로 지속적인 스킨 미리보기 설정
	// 지정된 스킨이 없는경우 처리
	if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/skin/site/'.$_GET['__pskin']) || !is_dir($_SERVER['DOCUMENT_ROOT'].'/skin/site_m/'.$_GET['__pskin'])) {
		samesiteCookie('temp_skin', '', time() -3600, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
		error_loc_msg('/', '스킨이 존재하지 않습니다.', 'top');
	}
	samesiteCookie('temp_skin', $_GET['__pskin'], 0, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	$_pskin = $_GET['__pskin'];
}
if(isset($_COOKIE['temp_skin']) && empty($_pskin)) {
	// 지정된 스킨이 없는경우 처리
	if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/skin/site/'.$_COOKIE['temp_skin']) || !is_dir($_SERVER['DOCUMENT_ROOT'].'/skin/site_m/'.$_COOKIE['temp_skin'])) {
		samesiteCookie('temp_skin', '', time() -3600, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
		error_loc_msg('/', '스킨이 존재하지 않습니다.', 'top');
	}
	$_pskin = $_COOKIE['temp_skin']; // 쿠키로 지속적은 스킨 보기 설정
}
if($_pskin) {
	// 지정된 스킨이 없는경우 처리
	if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/skin/site/'.$_pskin) || !is_dir($_SERVER['DOCUMENT_ROOT'].'/skin/site_m/'.$_pskin)) error_loc_msg('/', '스킨이 존재하지 않습니다.', 'top');
	$_skin = $_skin_m = $_pskin; // 스킨 미리보기
}
define('OD_PROGRAM_DIR', '/program'); // program 폴더
define('OD_PROGRAM_ROOT', $_SERVER['DOCUMENT_ROOT'].OD_PROGRAM_DIR); // program ROOT를 포함하는 경로
define('OD_PROGRAM_URL', $system['__url'].OD_PROGRAM_DIR); // program url를 포함하는 경로

define('OD_MAIL_DIR', '/mail'); // mail 폴더
define('OD_MAIL_ROOT', OD_PROGRAM_ROOT.OD_MAIL_DIR); // mail ROOT를 포함하는 경로
define('OD_MAIL_URL', $system['__url'].OD_PROGRAM_DIR.OD_MAIL_DIR); // mail url를 포함하는 경로

define('OD_ADDONS_DIR', '/addons'); // ADDONS 폴더
define('OD_ADDONS_ROOT', $_SERVER['DOCUMENT_ROOT'].OD_ADDONS_DIR); // ADDONS ROOT를 포함하는 경로
define('OD_ADDONS_URL', $system['__url'].OD_ADDONS_DIR); // ADDONS url를 포함하는 경로

define('OD_SKIN_DIR', '/skin'); // skin 폴더
define('OD_SKIN_ROOT', $_SERVER['DOCUMENT_ROOT'].OD_SKIN_DIR); // skin ROOT를 포함하는 경로
define('OD_SKIN_URL', $system['__url'].OD_SKIN_DIR); // skin url를 포함하는 경로
define('OD_BOARD_SKIN_DIR', OD_SKIN_DIR.'/board'); // PC 게시판 스킨 폴더
define('OD_BOARD_MSKIN_DIR', OD_SKIN_DIR.'/board_m'); // MOBILE 게시판 스킨 폴더
define('OD_SITE_SKIN', $_skin); // 사용자 지정 PC 스킨
define('OD_SITE_MSKIN', $_skin_m); // 사용자 지정 Mobile 스킨
define('OD_SITE_SKIN_DIR', OD_SKIN_DIR.'/site'); // PC 스킨 폴더
define('OD_SITE_MSKIN_DIR', OD_SKIN_DIR.'/site_m'); // Mobile 스킨폴더
define('OD_SITE_SKIN_PATH', OD_SITE_SKIN_DIR.'/'.OD_SITE_SKIN); // PC SITE PATH
define('OD_SITE_MSKIN_PATH', OD_SITE_MSKIN_DIR.'/'.OD_SITE_MSKIN); // MOBILE SITE PATH
define('OD_SITE_SKIN_ROOT', $_SERVER['DOCUMENT_ROOT'].OD_SITE_SKIN_PATH); // PC SITE ROOT를 포함하는 경로
define('OD_SITE_MSKIN_ROOT', $_SERVER['DOCUMENT_ROOT'].OD_SITE_MSKIN_PATH); // MOBILE SITE ROOT를 포함하는 경로
define('OD_SITE_SKIN_URL', $system['__url'].OD_SITE_SKIN_PATH); // PC SITE url를 포함하는 경로
define('OD_SITE_MSKIN_URL', $system['__url'].OD_SITE_MSKIN_PATH); // MOBILE SITE url를 포함하는 경로

define('OD_ADMIN_DIR', '/totalAdmin'); // 통합관리자 폴더
define('OD_ADMIN_ROOT', $_SERVER['DOCUMENT_ROOT'].OD_ADMIN_DIR); // 통합관리자 ROOT를 포함하는 경로
define('OD_ADMIN_URL', $system['__url'].OD_ADMIN_DIR); // 통합관리자 url를 포함하는 경로

define('OD_SUB_ADMIN_DIR', '/subAdmin'); // 입점관리자 폴더
define('OD_SUB_ADMIN_ROOT', $_SERVER['DOCUMENT_ROOT'].OD_SUB_ADMIN_DIR); // 입점관리자 ROOT를 포함하는 경로
define('OD_SUB_ADMIN_URL', $system['__url'].OD_SUB_ADMIN_DIR); // 입점관리자 url를 포함하는 경로


define('IMG_DIR_NORMAL_URL', $system['__url'].IMG_DIR_NORMAL); // 기본 URL
define('IMG_DIR_BANNER_URL', $system['__url'].IMG_DIR_BANNER); // 배너 URL
define('IMG_DIR_PRODUCT_URL', $system['__url'].IMG_DIR_PRODUCT); // 상품 URL
define('IMG_DIR_CATEGORY_URL', $system['__url'].IMG_DIR_CATEGORY); // 카테고리 URL
define('IMG_DIR_ICON_URL', $system['__url'].IMG_DIR_ICON); // 아이콘 URL
define('IMG_DIR_BOARD_URL', $system['__url'].IMG_DIR_BOARD); // 게시판 첨부파일 URL
define('IMG_DIR_POPUP_URL', $system['__url'].IMG_DIR_POPUP); // 팝업 URL


define('IMG_DIR_NORMAL_ROOT', $_SERVER['DOCUMENT_ROOT'].IMG_DIR_NORMAL); // 기본 ROOT 경로
define('IMG_DIR_BANNER_ROOT', $_SERVER['DOCUMENT_ROOT'].IMG_DIR_BANNER); // 배너 ROOT 경로
define('IMG_DIR_PRODUCT_ROOT', $_SERVER['DOCUMENT_ROOT'].IMG_DIR_PRODUCT); // 상품 ROOT 경로
define('IMG_DIR_CATEGORY_ROOT', $_SERVER['DOCUMENT_ROOT'].IMG_DIR_CATEGORY); // 카테고리 ROOT 경로
define('IMG_DIR_ICON_ROOT', $_SERVER['DOCUMENT_ROOT'].IMG_DIR_ICON); // 아이콘 ROOT 경로
define('IMG_DIR_BOARD_ROOT', $_SERVER['DOCUMENT_ROOT'].IMG_DIR_BOARD); // 게시판 첨부파일 ROOT 경로
define('IMG_DIR_POPUP_ROOT', $_SERVER['DOCUMENT_ROOT'].IMG_DIR_POPUP); // 팝업 ROOT 경로


// ========================================================================================================================== //
# 환경 설정에 의한 디바이스 고정 - (관리자 - 환경설정 - 운영 관리 설정 - PC/모바일샵 사용여부 설정)
$_pcmode = (isset($_REQUEST['_pcmode'])?$_REQUEST['_pcmode']:null);
$_mobilemode = (isset($_REQUEST['_mobilemode'])?$_REQUEST['_mobilemode']:null);
if($siteInfo['s_device_mode'] == 'M') {
	$_pcmode = '';
	$_mobilemode = 'chk';
}
else if($siteInfo['s_device_mode'] == 'P') {
	$_pcmode = 'chk';
	$_mobilemode = '';
}


# PC & Mobile 처리
$_device_mode = 'pc';
if($_pcmode == 'chk') { // PC 모드
	samesiteCookie('AuthNoMobile', 'chk', 0, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	$_device_mode = 'pc';
}
else if($_mobilemode == 'chk') { // Mobile 모드
	samesiteCookie('AuthNoMobile', '', (time()-3600), '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	$_device_mode = 'm';
}
else { // 자동
	if(is_mobile('auto') === true) $_device_mode = 'm';
	if(isset($_COOKIE['AuthNoMobile']) && $_COOKIE['AuthNoMobile'] == 'chk') $_device_mode = 'pc';
}


# device 환경 변수 설정(스킨 내부에서 사용)
$SkinData = array();
$SkinData['device'] = $_device_mode; // 스크립트에 의한 디바이스모드
$SkinData['skin'] = ($_device_mode == 'pc'?OD_SITE_SKIN:OD_SITE_MSKIN); // 모드에 맞는 스킨 폴더
$SkinData['skin_dir'] = ($_device_mode == 'pc'?OD_SITE_SKIN_DIR:OD_SITE_MSKIN_DIR); // skin 폴더상의 경로
$SkinData['skin_path'] = ($_device_mode == 'pc'?OD_SITE_SKIN_PATH:OD_SITE_MSKIN_PATH); // 스킨폴더를 포함한 skin 폴더상의 경로
$SkinData['skin_root'] = ($_device_mode == 'pc'?OD_SITE_SKIN_ROOT:OD_SITE_MSKIN_ROOT); // Root를 포함한 스킨 경로
$SkinData['skin_url'] = ($_device_mode == 'pc'?OD_SITE_SKIN_URL:OD_SITE_MSKIN_URL); // URL을 포함한 스킨 경로
$SkinData['_skin_url'] = str_replace(array('http:', 'https:'), '', $SkinData['skin_url']); // https호환용 URL경로
$SkinData['skin_pc'] = OD_SITE_SKIN;
$SkinData['skin_m'] = OD_SITE_MSKIN;
$SkinData['skin_dir_pc'] = OD_SITE_SKIN_DIR;
$SkinData['skin_dir_m'] = OD_SITE_MSKIN_DIR;
$SkinData['skin_path_pc'] = OD_SITE_SKIN_PATH;
$SkinData['skin_path_m'] = OD_SITE_MSKIN_PATH;
$SkinData['skin_root_pc'] = OD_SITE_SKIN_ROOT;
$SkinData['skin_root_m'] = OD_SITE_MSKIN_ROOT;
$SkinData['skin_url_pc'] = OD_SITE_SKIN_URL;
$SkinData['skin_url_m'] = OD_SITE_MSKIN_URL;


# pn설정
if(empty($_REQUEST['pn'])) {
	if(isset($_REQUEST['pcode'])) $pn = 'product.view';
	else if(file_exists($SkinData['skin_root'].'/intro.php') && $_COOKIE['intro_skip'] != 'Y') $pn = 'intro'; //인트로가 있는 경우 인트로로 이동
	else $pn = 'main';
}


# 스킨별 var호출
if(file_exists(OD_SITE_SKIN_ROOT.'/_var.php')) include(OD_SITE_SKIN_ROOT.'/_var.php'); // PC 스킨 _var.php 호출 (/skin/site/*/_var.php)
if(file_exists(OD_SITE_MSKIN_ROOT.'/_var.php')) include(OD_SITE_MSKIN_ROOT.'/_var.php'); // PC 스킨 _var.php 호출 (/skin/site_m/*/_var.php)


# 스킨 리스트
$_skin_list = array();
$skinTmp = dir(OD_SKIN_ROOT.'/site/');
while($entry = $skinTmp->read()) {
	if(in_array($entry, array('..', '.'))) continue;

	$SkinInfo = array();
	if(file_exists(OD_SKIN_ROOT.'/site/'.$entry.'/skin.xml')) $SkinInfo = xml2array(file_get_contents(OD_SKIN_ROOT.'/site/'.$entry.'/skin.xml'));
	if(isset($SkinInfo['skin']['title'])) $_skin_list[$entry] = $SkinInfo['skin']['title'];
	else $_skin_list[$entry] = $entry;
}
@ksort($_skin_list); // 스킨 폴더명 순으로 정렬 변경
unset($skinTmp);