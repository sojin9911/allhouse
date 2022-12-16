<?php
define('_OD_DIRECT_', true); // 개별 실행허용
@ini_set("precision", "20"); // 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
if(empty($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
if($SubAdminMode === false) error_loc(OD_ADMIN_URL);

# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
$AdminPathData = explode('/', $AdminPathData['path']);
$AdminPath = $AdminPathData[1]; unset($AdminPathData);


// -- 관리자체크 ==> 처음로그인시 저장된 암호화 세션(라이선스+...+db계정uid) 와 쿠키에 저장도니 값을 비교하여 조작이 있을 시 튕겨내기
if(isset($app_mode) && $app_mode === 'popup' && SubAdminLoginCheck('value') === false) error_msgPopup('권한이 없습니다.'); // 팝업모드에서는 권한이 없는 경우 팝업을 닫고 부모창을 새로고침 한다.
SubAdminLoginCheck();


# 입점업체 메뉴 설정
$subAdminArr = array(
	array(
		'name'=>'환경설정',
		'link'=>'_config.delivery.form.php',
		'sub'=>array(
			array(
				'name'=>'기본설정',
				'link'=>'_config.delivery.form.php',
				'sub'=>array(
					array(
						'name'=>'배송 기본 정보',
						'link'=>'_config.delivery.form.php'
					),
					array(
						'name'=>'상품 상세 이용안내 관리',
						'link'=>'_product.guide.list.php'
					)
				)
			)
		)
	),
	array(
		'name'=>'상품관리',
		'link'=>'_product.list.php',
		'sub'=>array(
			array(
				'name'=>'상품관리',
				'link'=>'_product.list.php',
				'sub'=>array(
					array(
						'name'=>'상품목록',
						'link'=>'_product.list.php'
					),
					array(
						'name'=>'상품등록',
						'link'=>'_product.form.php'
					)
				)
			),
			array(
				'name'=>'상품 일괄 관리',
				'link'=>'_product_mass.price.php',
				'sub'=>array(
					array(
						'name'=>'상품일괄 가격 관리',
						'link'=>'_product_mass.price.php'
					),
					array(
						'name'=>'상품일괄 재고 관리',
						'link'=>'_product_mass.view.php'
					),
					array(
						'name'=>'상품일괄 이동/삭제 관리',
						'link'=>'_product_mass.move.php'
					),
					array(
						'name'=>'상품 옵션 일괄 관리',
						'link'=>'_product_mass.option.php'
					)
				)
			),
			array(
				'name'=>'상품 참여 관리',
				'link'=>'_product_wish.list.php',
				'sub'=>array(
					array(
						'name'=>'상품 찜 관리',
						'link'=>'_product_wish.list.php'
					),
					array(
						'name'=>'상품후기 관리',
						'link'=>'_product_talk.list.php?pt_type='.urlencode($arr_p_talk_type['eval'])
					),
					array(
						'name'=>'상품문의 관리',
						'link'=>'_product_talk.list.php?pt_type='.urlencode($arr_p_talk_type['qna'])
					)
				)
			)
		)
	),
	array(
		'name'=>'주문/배송 관리',
		'link'=>'_order_product.list.php',
		'sub'=>array(
			array(
				'name'=>'주문 관리',
				'link'=>'_order_product.list.php',
				'sub'=>array(
					array(
						'name'=>'배송주문상품관리',
						'link'=>'_order_product.list.php'
					),
					array(
						'name'=>'네이버페이 주문 목록',
						'link'=>'_npay_order.list.php'
					),
					array(
						'name'=>'교환/반품 관리',
						'link'=>'_order_complain.list.php'
					),
					array(
						'name'=>'부분 취소 관리',
						'link'=>'_cancel.list.php'
					),
					array(
						'name'=>'취소 주문상품 관리',
						'link'=>'_order_product.cancel_list.php'
					)
				)
			),
			array(
				'name'=>'정산 관리',
				'link'=>'_ordercalc.view.php',
				'sub'=>array(
					array(
						'name'=>'정산 현황',
						'link'=>'_ordercalc.view.php'
					),
					array(
						'name'=>'정산 대기 관리',
						'link'=>'_order3.list.php'
					),
					array(
						'name'=>'정산 완료 목록',
						'link'=>'_order4.list.php'
					)
				)
			)
		)
	)
);
$FindUrl = str_replace('/subAdmin/', '', $_SERVER['SCRIPT_NAME']);
$FindUrlReq = str_replace('/subAdmin/', '', $_SERVER['REQUEST_URI']);
if($app_current_link) $FindUrl = $FindUrlReq = $app_current_link;
$FindMenu = recursiveFind($subAdminArr, (isset($FindUrl)?$FindUrl:null));
if(count($FindMenu) <= 0) $FindMenu = recursiveFind($subAdminArr, (isset($FindUrlReq)?$FindUrlReq:null));
$FindMenuArr = $FindMenu;
$Find1Depth = $FindMenuArr[0];
$Find2Depth = $FindMenuArr[1];
$Find3Depth = $FindMenuArr[2];
if(!$Find2Depth['name']) {
	$Find2Depth = $FindMenuArr[2];
	$Find3Depth = $FindMenuArr[3];
	if(!$Find3Depth['name']) $Find3Depth = $FindMenuArr[4];
}


# 입점관리자 정보 추출
$com = $subAdmin = _company_info($_COOKIE['AuthCompany']);
$com_id = $pass_com = $_COOKIE['AuthCompany']; // 입점 검색어가 있는경우 해당 입점업체로 고정


// 문의/후기 답변등에 사용되는 이름
$seller_name = '판매자';