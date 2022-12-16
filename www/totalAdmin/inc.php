<?php
define('_OD_DIRECT_', true); // 개별 실행허용
@ini_set("precision", "20"); // 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
if(empty($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


// - 넘길 변수 설정하기 ---
if(preg_match("/.list.php/i" , $CURR_FILENAME)){
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {if(is_array($val)) {foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }}else {$_PVS .= "&$key=$val";}}
	$_PVSC = enc('e' , $_PVS);
}
// - 넘길 변수 설정하기 ---


# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
$AdminPathData = explode('/', $AdminPathData['path']);
$AdminPath = $AdminPathData[1]; unset($AdminPathData);

// -- 관리자체크 ==> 처음로그인시 저장된 암호화 세션(라이선스+...+db계정uid) 와 쿠키에 저장도니 값을 비교하여 조작이 있을 시 튕겨내기
if(isset($app_mode) && $app_mode === 'popup' && AdminLoginCheck('value') === false) {
	if($AdminPath == 'subAdmin' && SubAdminLoginCheck('value') === false) error_msgPopup('권한이 없습니다.'); // 팝업모드에서는 권한이 없는 경우 팝업을 닫고 부모창을 새로고침 한다.
	else error_msgPopup('권한이 없습니다.'); // 팝업모드에서는 권한이 없는 경우 팝업을 닫고 부모창을 새로고침 한다.
}
if($AdminPath == 'subAdmin') SubAdminLoginCheck();
else AdminLoginCheck();

// -- 현재 페이지 체크  :: 페이지가 파일명 + 파라미터로 되어있다면
$app_current_link = (isset($app_current_link)?$app_current_link:$CURR_FILENAME);
if(isset($tempUid) && $tempUid != '') $menuUid = $tempUid;
if(empty($menuUid)) $menuUid = '';


if($AdminPath == 'totalAdmin') {


	// -- 현재 페이지로 부터 고유번호 추출 -- 2017-07-25 LCY
	$temp_key = array('uid','idx','depth','view','parent','name','link');
	$temp_select = array(); // or am1.am_uid = '".$uid."'  or am2.am_uid = '".$uid."' or am3.am_uid = '".$uid."'
	for( $i=1; $i <= 3; $i++) { foreach($temp_key as $k=>$v){ $temp_select[] = 'am'.$i.'.am_'.$v.' as am'.$i.'_'.$v;   } }
	$current_page_info = _MQ(" select ".( count($temp_select) > 0 ? implode(",",$temp_select):'*' )."
	from smart_admin_menu as am3
	inner join smart_admin_menu as am2 on (substring_index(am3.am_parent , ',' ,-1) = am2.am_uid and am2.am_depth=2)
	inner join smart_admin_menu as am1 on (substring_index(am3.am_parent , ',' ,1) = am1.am_uid and am1.am_depth=1)
	where am3.am_link = '".$app_current_link."' or am1.am_uid = '".$menuUid."'  or am2.am_uid = '".$menuUid."' or am3.am_uid = '".$menuUid."'
	");

	// -- 접근 가능한 운영자 메뉴정보를 배열로 받는다
	$siteAdminMenuChk = adminMenuChk($app_current_link , $menuUid); // 메뉴체크
	$siteAdminMenuSet = adminMenuSet(); // 메뉴권한 배열로 받기
}



# 페이지별 메뉴얼
$MLink = parse_url($_SERVER['REQUEST_URI']);
$MLink = end(explode('/', $MLink['path']));
if($_menuType) $MLink = $MLink.'?_menuType='.$_menuType;
if($pass_menu && !in_array($MLink, array('_cntlog.php', '_cntlog_route.php'))) $MLink = $MLink.'?pass_menu='.$pass_menu;
if($MLink == '_mailing_premium.view.php' && $_view) $MLink = $MLink.'?_view='.$_view;
if($MLink == '_order.form.php' && $view) $MLink = $MLink.'?view='.$view;
if($MLink == '_order.list.php' && $view) $MLink = $MLink.'?view='.$view;
$ManualBaseLink = 'http://www.onedaynet.co.kr/manual/hyssence3/pages';
$ManualLink = array(
	// =============================================================== //
	// 환경설정 -> 기본설정
		  '_config.default.form.php'=>$ManualBaseLink.'/01_1.html#1_1' // 쇼핑몰 기본정보
		, '_config.agree.form.php'=>$ManualBaseLink.'/01_1.html#1_2' // 약관 및 정책 설정
		, '_config.vat.form.php'=>$ManualBaseLink.'/01_1.html#1_3' // 부가세율 설정
		, '_config.usage.php'=>$ManualBaseLink.'/01_1.html#1_4' // 이용안내 설정
		, '_addons.php?_menuType=smsEmail'=>$ManualBaseLink.'/01_1.html#1_5' // 문자/이메일 수신관련설정
		, '_addons.php?_menuType=080'=>$ManualBaseLink.'/01_1.html#1_6' // 080 수신거부 관련설정

	// 환경설정 -> 운영 관리 설정
		, '_config.admin_menu.list.php'=>$ManualBaseLink.'/01_2.html#1_1' // Admin 메뉴관리
		, '_config.admin.list.php'=>$ManualBaseLink.'/01_2.html#1_2' // 운영자관리
		, '_config.admin.form.php'=>$ManualBaseLink.'/01_2.html#1_2' // 운영자관리
		, '_config.admin_menuset.form.php'=>$ManualBaseLink.'/01_2.html#1_3' // 운영자별 메뉴관리
		, '_config.password.config.php'=>$ManualBaseLink.'/01_2.html#1_4' // 비밀번호 변경안내 설정
		, '_config.password_find.config.php'=>$ManualBaseLink.'/01_2.html#1_5' // 비밀번호 찾기 설정
		, '_solution.info.php'=>$ManualBaseLink.'/01_2.html#1_6' // 솔루션 이용현황
		, '_config.cntlog.form.php'=>$ManualBaseLink.'/01_2.html#1_7' // 접속 로그 설정
		, '_config.sns.form.php'=>$ManualBaseLink.'/01_2.html#1_8' // SNS 로그인/API 설정
		, '_config.device.form.php'=>$ManualBaseLink.'/01_2.html#1_9' // PC/모바일샵 사용여부 설정
		, '_config.favmenu.list.php'=>$ManualBaseLink.'/01_2.html#1_10' // 자주쓰는 메뉴 설정

	// 환경설정 -> SMS/알림톡 관리
		, '_config.sms.form.php'=>$ManualBaseLink.'/01_3.html#1_1' // SMS/알림톡 정보설정
		, '_sms.form.php'=>$ManualBaseLink.'/01_3.html#1_2' // 개별/전체 SMS 발송
		, '_config.sms.out_send_list.php'=>$ManualBaseLink.'/01_3.html#1_3' // 발송내역
		, '_config.sms.out_list.php'=>$ManualBaseLink.'/01_3.html#1_4' // 충전관리

	// 환경설정 -> 결제 관련 설정
		, '_config.paymethod.php'=>$ManualBaseLink.'/01_4.html#1_1' // 결제 수단 설정
		, '_config.none_bank.php'=>$ManualBaseLink.'/01_4.html#1_2' // 무통장입금 은행 관리
		, '_config.pg.form.php'=>$ManualBaseLink.'/01_4.html#1_3' // 통합 전자결제(PG) 관리
		, '_config.pg_mobile.form.php'=>$ManualBaseLink.'/01_4.html#1_4' // 휴대폰 결제 서비스 설정
		, '_config.pg_naver.form.php'=>$ManualBaseLink.'/01_4.html#1_5' // 네이버페이 설정
		, '_config.pg_payco.form.php'=>$ManualBaseLink.'/01_4.html#1_6' // 페이코 설정
		, '_config.orderbank.form.php'=>$ManualBaseLink.'/01_4.html#1_7' // 실시간입금 확인 설정
		, '_config.tax.form.php'=>$ManualBaseLink.'/01_4.html#1_8' // 바로빌 설정

	// 환경설정 -> 상품/배송 설정
		, '_config.delivery.form.php'=>$ManualBaseLink.'/01_5.html#1_1' // 상품/배송 기본 정보
		, '_product.guide.list.php'=>$ManualBaseLink.'/01_5.html#1_2' // 상품 상세 이용안내 관리
		, '_product.guide.form.php'=>$ManualBaseLink.'/01_5.html#1_2' // 상품 상세 이용안내 관리
		, '_config.delivery_addprice.list.php'=>$ManualBaseLink.'/01_5.html#1_3' // 도서산간 추가배송비 설정
		, '_config.today_view.form.php'=>$ManualBaseLink.'/01_5.html#1_4' // 최근 본 상품 설정

	// 환경설정 -> 본인확인 서비스 설정
		, '_config.member.form.php'=>$ManualBaseLink.'/01_6.html' // 휴대폰 본인확인 서비스

	// 환경설정 -> 키워드 관리
		, '_config.keyword.php'=>$ManualBaseLink.'/01_7.html' // 키워드 관리

	// 환경설정 -> 보안서버 설정
		, '_config.ssl.default_form.php'=>$ManualBaseLink.'/01_8.html#1_1' // 보안서버 설정
		, '_config.ssl.admin_form.php'=>$ManualBaseLink.'/01_8.html#1_2' // 관리자 보안서버 관리
		, '_config.ssl.pc_form.php'=>$ManualBaseLink.'/01_8.html#1_3' // PC쇼핑몰 보안서버 관리
		, '_config.ssl.m_form.php'=>$ManualBaseLink.'/01_8.html#1_4' // 모바일쇼핑몰 보안서버 관리



	// =============================================================== //
	// 회원관리 -> 회원관리
		, '_individual.list.php'=>$ManualBaseLink.'/02_1.html#1_1' // 회원관리
		, '_individual.form.php'=>$ManualBaseLink.'/02_1.html#1_1' // 회원관리
		, '_config.join.php'=>$ManualBaseLink.'/02_1.html#1_2' // 회원가입 정책/항목
		, '_member_group_set.list.php'=>$ManualBaseLink.'/02_1.html#1_3' // 회원등급 관리
		, '_member_group_set.form.php'=>$ManualBaseLink.'/02_1.html#1_3' // 회원등급 관리
		, '_config.group.php'=>$ManualBaseLink.'/02_1.html#1_4' // 회원 등급 정책
		, '_config.sleep.php'=>$ManualBaseLink.'/02_1.html#1_5' // 휴면 회원 정책
		, '_individual_sleep.list.php'=>$ManualBaseLink.'/02_1.html#1_6' // 휴면 회원 관리
		, '_individual_out.list.php'=>$ManualBaseLink.'/02_1.html#1_7' // 회원탈퇴/삭제 관리
		, '_entershop.list.php'=>$ManualBaseLink.'/02_1.html#1_8' // 입점업체관리

	// 회원관리 -> 적립금 관리
		, '_config.point.form.php'=>$ManualBaseLink.'/02_2.html#1_1' // 적립금 설정
		, '_point.list.php'=>$ManualBaseLink.'/02_2.html#1_2' // 적립금 관리

	// 회원관리 -> 상품후기/문의
		, '_product_talk.list.php'=>$ManualBaseLink.'/02_3.html#1_1' // 상품후기 관리
		, '_product_talk.list.php'=>$ManualBaseLink.'/02_3.html#1_2' // 상품문의 관리
		, '_request.list.php?pass_menu=inquiry'=>$ManualBaseLink.'/02_3.html#1_3' // 1:1문의 관리
		, '_request.list.php?pass_menu=partner'=>$ManualBaseLink.'/02_3.html#1_4' // 제휴문의

	// 회원관리 -> 메일 관리
		, '_config.mail.form.php'=>$ManualBaseLink.'/02_4.html#1_1' // 프리미엄 메일 설정
		, '_mailing_premium.view.php'=>$ManualBaseLink.'/02_4.html#1_2' // 프리미엄 메일 발송
		, '_mailing_data.list.php'=>$ManualBaseLink.'/02_4.html#1_3' //



	// =============================================================== //
	// 상품 -> 상품 관리
		, '_product.list.php'=>$ManualBaseLink.'/03_1.html#1_1' // 상품 목록
		, '_product.form.php'=>$ManualBaseLink.'/03_1.html#1_2' // 상품 등록, 상품 수정
		, '_product_icon.list.php'=>$ManualBaseLink.'/03_1.html#1_3' // 상품 아이콘 관리
		, '_product_icon.form.php'=>$ManualBaseLink.'/03_1.html#1_3' // 상품 아이콘 관리
		, '_product.common_option_set.list.php'=>$ManualBaseLink.'/03_1.html#1_4' // 자주쓰는 옵션 관리
		, '_product.common_option_set.form.php'=>$ManualBaseLink.'/03_1.html#1_4' // 자주쓰는 옵션 관리
		, '_product_wish.list.php'=>$ManualBaseLink.'/03_1.html#1_5' // 상품 찜 관리

	// 상품 -> 상품 일괄 관리
		, '_product_mass.price.php'=>$ManualBaseLink.'/03_2.html#1_1' // 상품일괄 가격 관리
		, '_product_mass.view.php'=>$ManualBaseLink.'/03_2.html#1_2' // 상품일괄 노출/재고 관리
		, '_product_mass.point.php'=>$ManualBaseLink.'/03_2.html#1_3' // 상품일괄 적립/쿠폰 관리
		, '_product_mass.move.php'=>$ManualBaseLink.'/03_2.html#1_4' // 상품일괄 이동/복사/삭제 관리
		, '_product_mass.option.php'=>$ManualBaseLink.'/03_2.html#1_5' // 상품옵션일괄관리

	// 상품 -> 상품 진열 관리
		, '_config.display.main.php'=>$ManualBaseLink.'/03_3.html#1_1' // 메인 상품관리
		, '_config.display.review.php'=>$ManualBaseLink.'/03_3.html#1_2' // 메인 리뷰관리
		, '_config.display.search.php'=>$ManualBaseLink.'/03_3.html#1_3' // 검색 페이지
		, '_config.display.type.php'=>$ManualBaseLink.'/03_3.html#1_4' // 타입별 상품관리
		, '_config.display.pinfo.php'=>$ManualBaseLink.'/03_3.html#1_5' // 상품 상세페이지 노출 설정
		, '_hash_view.php'=>$ManualBaseLink.'/03_3.html#1_6' // 해시태그 노출 설정

	// 상품 -> 카테고리 관리
		, '_category.list.php'=>$ManualBaseLink.'/03_4.html#1_1' // 카테고리 관리
		, '_brand.list.php'=>$ManualBaseLink.'/03_4.html#1_2' // 브랜드 관리



	// =============================================================== //
	// 주문/배송 -> 주문 관리
		, '_order.list.php'=>$ManualBaseLink.'/04_1.html#1_1' // 전체 주문 리스트
		, '_order.form.php'=>$ManualBaseLink.'/04_1.html#1_1' // 전체 주문 리스트
		, '_npay_order.list.php'=>$ManualBaseLink.'/04_1.html#1_2' // 네이버페이 주문 목록
		, '_npay_order.form.php'=>$ManualBaseLink.'/04_1.html#1_2' // 네이버페이 주문 목록
		, '_order.list.php?view=online'=>$ManualBaseLink.'/04_1.html#1_3' // 입금대기 주문 목록
		, '_order.form.php?view=online'=>$ManualBaseLink.'/04_1.html#1_3' // 입금대기 주문 목록

	// 주문/배송 -> 배송/정산 관리
		, '_order_delivery.list.php'=>$ManualBaseLink.'/04_2.html#1_1' // 배송주문관리
		, '_order.form.php?view=order_delivery'=>$ManualBaseLink.'/04_2.html#1_1' // 배송주문관리
		, '_order_delivery.excel_form.php'=>$ManualBaseLink.'/04_2.html#1_1' // 배송주문관리
		, '_order_product.list.php'=>$ManualBaseLink.'/04_2.html#1_2' // 배송주문상품관리
		, '_order.form.php?view=order_product'=>$ManualBaseLink.'/04_2.html#1_2' // 배송주문상품관리
		, '_order_product.excel_form.php'=>$ManualBaseLink.'/04_2.html#1_2' // 배송주문상품관리
		, '_order3.list.php'=>$ManualBaseLink.'/04_2.html#1_3' // 정산 대기 관리
		, '_order4.list.php'=>$ManualBaseLink.'/04_2.html#1_4' // 정산 완료 목록
		, '_order4.view.php'=>$ManualBaseLink.'/04_2.html#1_4' // 정산 완료 목록
		, '_ordercalc.view.php'=>$ManualBaseLink.'/04_2.html#1_5' // 정산 현황

	// 주문/배송 -> 취소/교환/반품/환불
		, '_order.cancel_list.php'=>$ManualBaseLink.'/04_3.html#1_1' // 주문 취소 관리
		, '_order.form.php?view=cancel'=>$ManualBaseLink.'/04_3.html#1_1' // 주문 취소 관리
		, '_cancel.list.php'=>$ManualBaseLink.'/04_3.html#1_2' // 부분 취소 관리
		, '_cancel.form.php'=>$ManualBaseLink.'/04_3.html#1_2' // 부분 취소 관리
		, '_order_complain.list.php'=>$ManualBaseLink.'/04_3.html#1_3' // 교환/반품 관리
		, '_cancel_order.list.php'=>$ManualBaseLink.'/04_3.html#1_4' // 환불 요청 관리

	// 주문/배송 -> 자동입금확인
		, '_orderbanklog.list.php'=>$ManualBaseLink.'/04_3.html#1_1' // 실시간입금 확인
		, '_online_notice.list.php'=>$ManualBaseLink.'/04_3.html#1_2' // 미확인 입금자 관리

	// 주문/배송 -> 현금영수증 관리
		, '_cashbill.list.php'=>$ManualBaseLink.'/04_5.html#1_1' // 현금영수증 발급/조회
		, '_cashbill.form.php'=>$ManualBaseLink.'/04_5.html#1_2' // 현금영수증 개별발급

	// 주문/배송 -> 전자세금계산서 관리
		, '_tax.list.php'=>$ManualBaseLink.'/04_6.html#1_1' // 세금계산서 발급/조회
		, '_tax.form.php'=>$ManualBaseLink.'/04_6.html#1_2' // 세금계산서 개별발급



	// =============================================================== //
	// 게시판 -> 게시판 관리
		, '_bbs.board.list.php'=>$ManualBaseLink.'/05_1.html#1_1' // 게시판 목록
		, '_bbs.board.form.php'=>$ManualBaseLink.'/05_1.html#1_2' // 게시판 등록
		, '_bbs.post_mng.list.php'=>$ManualBaseLink.'/05_1.html#1_3' // 게시글 관리
		, '_bbs.post_mng.form.php'=>$ManualBaseLink.'/05_1.html#1_3' // 게시글 관리
		, '_bbs.forbidden_word.form.php'=>$ManualBaseLink.'/05_1.html#1_4' // 게시판 금지어 관리
		, '_bbs.post_template.list.php'=>$ManualBaseLink.'/05_1.html#1_5' // 게시글 양식 관리
		, '_bbs.post_template.form.php'=>$ManualBaseLink.'/05_1.html#1_5' // 게시글 양식 관리
		, '_bbs.post_faq.list.php'=>$ManualBaseLink.'/05_1.html#1_6' // FAQ 관리
		, '_bbs.post_faq.form.php'=>$ManualBaseLink.'/05_1.html#1_6' // FAQ 관리



	// =============================================================== //
	// 디자인 -> 디자인관리
		, '_skin.php'=>$ManualBaseLink.'/06_1.html#1_1' // 스킨관리
		, '_normalpage.list.php'=>$ManualBaseLink.'/06_1.html#1_2' // 일반페이지 관리
		, '_normalpage.form.php'=>$ManualBaseLink.'/06_1.html#1_2' // 일반페이지 관리

	// 디자인 -> 배너/팝업
		, '_popup.list.php'=>$ManualBaseLink.'/06_2.html#1_1' // 팝업관리
		, '_popup.form.php'=>$ManualBaseLink.'/06_2.html#1_1' // 팝업관리
		, '_banner.list.php'=>$ManualBaseLink.'/06_2.html#1_2' // 배너관리
		, '_banner.form.php'=>$ManualBaseLink.'/06_2.html#1_2' // 배너관리



	// =============================================================== //
	// 프로모션 -> 쿠폰관리
		, '_coupon_config.php'=>$ManualBaseLink.'/07_1.html#1_1' // 쿠폰 설정
		, '_coupon_set.list.php'=>$ManualBaseLink.'/07_1.html#1_2' // 쿠폰 목록
		, '_coupon_set.form.php'=>$ManualBaseLink.'/07_1.html#1_3' // 쿠폰 등록

	// 프로모션 -> 프로모션 코드
		, '_promotion.list.php'=>$ManualBaseLink.'/07_2.html' // 프로모션 코드 관리
		, '_promotion.form.php'=>$ManualBaseLink.'/07_2.html' // 프로모션 코드 관리

	// 프로모션 -> 이벤트
		, '_promotion_event_delivery.php'=>$ManualBaseLink.'/07_3.html#1_1' // 무료배송이벤트
		, '_promotion_attend.list.php'=>$ManualBaseLink.'/07_3.html#1_2' // 출석체크 관리
		, '_promotion_attend.form.php'=>$ManualBaseLink.'/07_3.html#1_3' // 출석체크 설정
		, '_promotion_plan.list.php'=>$ManualBaseLink.'/07_3.html#1_4' // 기획전 관리
		, '_promotion_plan.form.php'=>$ManualBaseLink.'/07_3.html#1_4' // 기획전 관리



	// =============================================================== //
	// 로그분석 -> 방문자 분석
		, '_cntlog.php'=>$ManualBaseLink.'/10_1.html#1_1' // 방문자 분석
		, '_cntlog_route.php'=>$ManualBaseLink.'/10_1.html#1_2' // 방문자 접속분석
		, '_cntlog_env.php'=>$ManualBaseLink.'/10_1.html#1_3' // 방문자 환경분석
		, '_cntlog_detail.php'=>$ManualBaseLink.'/10_1.html#1_4' // 방문자 상세분석

	// 로그분석 -> 회원분석
		, '_static_mem.method.php'=>$ManualBaseLink.'/10_2.html#1_1' // 회원 가입형태 분석
		, '_static_mem.type.php'=>$ManualBaseLink.'/10_2.html#1_2' // 회원 상태 분석
		, '_static_mem.point.php'=>$ManualBaseLink.'/10_2.html#1_3' // 회원 적립금 분석

	// 로그분석 -> 상품분석
		, '_static_product.category.php'=>$ManualBaseLink.'/10_3.html#1_1' // 카테고리 판매 순위 분석
		, '_static_product.order.php'=>$ManualBaseLink.'/10_3.html#1_2' // 판매 상품 순위 분석
		, '_static_product.cart.php'=>$ManualBaseLink.'/10_3.html#1_3' // 장바구니 상품 순위 분석
		, '_static_product.wish.php'=>$ManualBaseLink.'/10_3.html#1_4' // 찜 상품 순위 분석

	// 로그분석 -> 매출분석
		, '_static_sale.all.php'=>$ManualBaseLink.'/10_4.html#1_1' // 매출통계
		, '_static_sale.method.php'=>$ManualBaseLink.'/10_4.html#1_2' // 결제수단별 매출통계
		, '_static_sale.age.php'=>$ManualBaseLink.'/10_4.html#1_3' // 연령별 매출통계
		, '_static_sale.area.php'=>$ManualBaseLink.'/10_4.html#1_4' // 지역별 매출통계

	// 로그분석 -> 주문분석
		, '_static_order.all.php'=>$ManualBaseLink.'/10_5.html#1_1' // 주문통계
		, '_static_order.age.php'=>$ManualBaseLink.'/10_5.html#1_2' // 연령별 주문통계
		, '_static_order.area.php'=>$ManualBaseLink.'/10_5.html#1_3' // 지역별 주문통계
		, '_static_order.sex.php'=>$ManualBaseLink.'/10_5.html#1_4' // 성별 주문통계

	// 로그분석 -> SMS분석
		, '_sms.log.php'=>$ManualBaseLink.'/10_6.html' // SMS 에러로그
);