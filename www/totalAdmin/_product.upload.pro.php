<?php
set_time_limit(0);
ini_set('memory_limit','-1');
# LDD014

include_once(dirname(__file__) . '/inc.php');

// -- LCY 2017-11-09 -- 입점업체 패치
$arr_customer = array_keys(arr_company());
$dfCPID = $arr_customer[0];

# 첨부파일 확인
if($_FILES['excel_file']['size'] <= 0) error_loc_msg("_product.upload.pop.php?", "첨부파일이 없습니다.");

# Excel Class Load
include_once(OD_ADDONS_ROOT.'/excelAddon/loader.php');
$Excel = ExcelLoader($_FILES['excel_file']['tmp_name']);

// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
//		basic : 기본정보
//		all : 브랜드 전체 정보
$arr_brand = brand_info('basic');
$arr_brand_trans = array_flip($arr_brand);

// th 생성
function add_table_th($title, $style='') {
	return '<th scope="col"'.(trim($style) != ''?' style="'.$style.'"':null).'>'.$title.'</th>';
}

// 항목명으로 키값 추출
$arr_key = array();
// 항목명 필수체크
$arr_required = array();

// 2019-05-02 SSJ :: 엑셀 다운로드 항목 설정
// --- _product.download.php, _product.upload.php 에서 동일하게 사용 : 배열 수정 시 2개 파일 동일하게 수정
$th = array(
	'상품코드<br>(신규등록시 생략)'=>array(
		'key'=>'p_code',
		'required'=>'Y',
		'width'=>'210'
	),
	'대표상품명'=>array(
		'key'=>'p_name',
		'required'=>'Y',
		'width'=>'195'
	),
	'상품부제목'=>array(
		'key'=>'p_subname',
		'required'=>'N',
		'width'=>'195'
	),
	'1차 분류'=>array(
		'key'=>'catename_1',
		'required'=>'Y',
		'width'=>'320',
		'title'=>'카테고리' // 업로드 시 타이틀
	),
	'2차 분류'=>array(
		'key'=>'catename_2',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'3차 분류'=>array(
		'key'=>'catename_3',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'노출여부(Y, N)'=>array(
		'key'=>'p_view',
		'required'=>'Y',
		'width'=>'90',
	),
	'정산형태<br>(공급가, 수수료)'=>array(
		'key'=>'p_commission_type',
		'required'=>'Y',
		'width'=>'90',
	),
	'공급가(원)'=>array(
		'key'=>'p_sPrice',
		'required'=>'N',
		'width'=>'90'
	),
	'수수료(%)'=>array(
		'key'=>'p_sPersent',
		'required'=>'N',
		'width'=>'90'
	),
	'기존가격'=>array(
		'key'=>'p_screenPrice',
		'required'=>'N',
		'width'=>'90'
	),
	'할인판매가'=>array(
		'key'=>'p_price',
		'required'=>'Y',
		'width'=>'90'
	),
	'브랜드'=>array(
		'key'=>'p_brand',
		'required'=>'N',
		'width'=>'140'
	),
	'과세여부(Y, N)'=>array(
		'key'=>'p_vat',
		'required'=>'Y',
		'width'=>'90'
	),
	'재고량'=>array(
		'key'=>'p_stock',
		'required'=>'Y',
		'width'=>'60'
	),
	'상품순위'=>array(
		'key'=>'p_sort_group', // SSJ : 상품순위 항목 p_idx=>p_sort_group 으로 변경 : 2021-02-17
		'required'=>'N',
		'width'=>'60'
	),
	'원산지'=>array(
		'key'=>'p_orgin',
		'required'=>'N',
		'width'=>'130'
	),
	'제조사'=>array(
		'key'=>'p_maker',
		'required'=>'N',
		'width'=>'130'
	),
	'적립율(%)'=>array(
		'key'=>'p_point_per',
		'required'=>'N',
		'width'=>'60'
	),
	'상품쿠폰명'=>array(
		'key'=>'p_coupon_title',
		'required'=>'N',
		'width'=>'195'
	),
	'상품쿠폰타입<br>(price:할인액(원), per:할인율(%))'=>array(
		'key'=>'p_coupon_type',
		'required'=>'N',
		'width'=>'90'
	),
	'상품쿠폰금액(상품쿠폰타입이 price일 경우)'=>array(
		'key'=>'p_coupon_price',
		'required'=>'N',
		'width'=>'90'
	),
	'상품쿠폰율(%)<br>(상품쿠폰타입이 per일 경우)'=>array(
		'key'=>'p_coupon_per',
		'required'=>'N',
		'width'=>'90'
	),
	'상품쿠폰최댓값<br>(상품쿠폰타입이 per일 경우)'=>array(
		'key'=>'p_coupon_max',
		'required'=>'N',
		'width'=>'90'
	),
	'배송정보'=>array(
		'key'=>'p_delivery_info',
		'required'=>'N',
		'width'=>'195'
	),
	'배송처리<br>(기본, 상품별배송, 개별배송, 무료배송)'=>array(
		'key'=>'p_shoppingPay_use',
		'required'=>'Y',
		'width'=>'210'
	),
	'개별배송 - 배송비'=>array(
		'key'=>'p_shoppingPay',
		'required'=>'N',
		'width'=>'120'
	),
	'상품별배송 - 배송비<br>(기본배송비)'=>array(
		'key'=>'p_shoppingPayPdPrice',
		'required'=>'N',
		'width'=>'120'
	),
	'상품별배송 - 배송비<br>(무료배송비)'=>array(
		'key'=>'p_shoppingPayPfPrice',
		'required'=>'N',
		'width'=>'120'
	),
	'무료배송이벤트 적용여부<br>(적용,미적용)'=>array(
		'key'=>'p_free_delivery_event_use',
		'required'=>'N',
		'width'=>'150'
	),
	'회원등급추가혜택<br>(적용,미적용)'=>array(
		'key'=>'p_groupset_use',
		'required'=>'N',
		'width'=>'150'
	),
	'관련상품 적용방식<br>(사용안함, 자동지정, 수동지정)'=>array(
		'key'=>'p_relation_type',
		'required'=>'N',
		'width'=>'180'
	),
	'관련상품 상품코드<br>(수동지정시 상품코드를|로 구분하여 기입)'=>array(
		'key'=>'p_relation',
		'required'=>'N',
		'width'=>'310'
	),
	'상품설명 - PC<br>(엔터제외)'=>array(
		'key'=>'p_content',
		'required'=>'Y',
		'width'=>'310'
	),
	'상품설명 - 모바일<br>(엔터제외)'=>array(
		'key'=>'p_content_m',
		'required'=>'N',
		'width'=>'310'
	),
	'목록이미지'=>array(
		'key'=>'p_img_list_square',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지1'=>array(
		'key'=>'p_img_b1',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지2'=>array(
		'key'=>'p_img_b2',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지3'=>array(
		'key'=>'p_img_b3',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지4'=>array(
		'key'=>'p_img_b4',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지5'=>array(
		'key'=>'p_img_b5',
		'type'=> 'N',
		'width'=>'195'
	),
	// KAY :: 일괄업로드 :: 2021-07-02 -- 옵션 컬럽추가
	'옵션<br>(1차옵션>2차옵션>3차옵션|공급가|판매가|재고)'=>array(
		'key'=>'p_option_excel',
		'required'=>'N',
		'width'=>'195'
	)
	// 1차 = 옵션명|공급가|판매가|재고 형태
	// 2차 = 1차옵션명>2차옵션명|공급가|판매가|재고
	// 3차 = 1차옵션명>2차옵션명>3차옵션명|공급가|판매가|재고
);

// -- LCY 2017-11-09 -- 입점업체 패치
if( $SubAdminMode === true){ $th['입점업체'] = array('key'=>'p_cpid','type'=>'N', 'width'=>'150'); }

// -- // KAY :: 일괄업로드 :: 2021-07-02 -- 엑셀값 추출 후 키값 변경
$idx = 0;
$tmp_th = array();
foreach($th as $kk=>$vv){
	$tmp_th[strip_tags($kk)] = $th[$kk];
	$arr_required[$vv['key']] = $vv['required'];
	$arr_key[$idx] = $vv['key'];
	$idx++;
}
$th = $tmp_th;

// KAY :: 일괄업로드 :: 2021-07-02 -- 일주일지난 기록 삭제
_MQ_noreturn("DELETE FROM smart_product_upload_count WHERE puc_rdate < '". date("Y-m-d H:i:s", strtotime("-1week")) ."' ");
_MQ_noreturn("DELETE FROM smart_product_option_tmp WHERE pot_rdate < '". date("Y-m-d H:i:s", strtotime("-1week")) ."' ");

// KAY :: 일괄업로드 :: 2021-07-02 -- 상품업로드 개수 설정 (프로그래스바에서 사용)
_MQ_noreturn(" insert into smart_product_upload_count set puc_cnt = '". (sizeof($Excel) * 1 - 2)."', puc_aid = '". ($SubAdminMode == true ? $com_id : $siteAdmin['a_id']) ."', puc_rdate = now() ");
$app_upload_uid = mysql_insert_id();

// 엑셀 파일 정보추출
foreach($Excel as $key=>$val) {
	if($key < 2) continue; // 파일정보와 헤더는 제외
	else foreach($arr_key as $kk=>$vv) $val[$vv] = $val[$kk];

	// KAY :: 일괄업로드 :: 2021-07-02 -- p_code 추출
	$product = _MQ("select p_code from smart_product where p_code = '".$val['p_code']."' ");
	if($product['p_code']){
		$p_code = $val['p_code'];
		$mode = 'modify';
	}
	else{
		$p_code = shop_productcode_create();
		$mode = 'add';
	}

	// 정상가가 판매가와 같을 경우 정상가 0적용
	if ( $_screenPrice[$v] == $_price[$v] ) {
		$_screenPrice[$v] = 0;
	}

	// KAY :: 쿠폰 정보 추출
	if(trim($val['p_coupon_type']) == '할인율') $val['p_coupon_type'] = 'per';
	else if(trim($val['p_coupon_type']) == '할인금액') $val['p_coupon_type'] = 'price';
	else $val['p_coupon_type'] = 'per';
	$p_coupon = mysql_real_escape_string($val['p_coupon_title']) . "|" . $val['p_coupon_type']. "|" . $val['p_coupon_price'] ."|". $val['p_coupon_per'] ."|". $val['p_coupon_max'];

	// ---- SSJ : 상품 일괄 업로드 개선 추가 수정 : 2021-08-23 --------
	// 관련상품
	$val['p_relation'] =  implode("|", array_unique(array_filter(explode("|", preg_replace('/\r\n|\r|\n| /','',$val['p_relation'])))));
	if(trim($val['p_relation_type']) == '자동지정'){
		$val['p_relation_type'] = 'category';
	}else if(trim($val['p_relation_type']) == '수동지정'){
		$val['p_relation_type'] = 'manual';
	}else{
		$val['p_relation_type'] = 'none';
	}

	// 무료배송 이벤트
	if(trim($val['p_free_delivery_event_use']) == '적용'){
		$val['p_free_delivery_event_use'] = 'Y';
	}else{
		$val['p_free_delivery_event_use'] = 'N';
	}

	// 회원 등급별 할인
	if(trim($val['p_groupset_use']) == '적용'){
		$val['p_groupset_use'] = 'Y';
	}else{
		$val['p_groupset_use'] = 'N';
	}

	// 배송형태
	if(trim($val['p_shoppingPay_use']) == '상품별배송'){
		$val['p_shoppingPay_use'] = 'P';
	}else if(trim($val['p_shoppingPay_use']) == '개별배송'){
		$val['p_shoppingPay_use'] = 'Y';
	}else if(trim($val['p_shoppingPay_use']) == '무료배송'){
		$val['p_shoppingPay_use'] = 'F';
	}else{
		$val['p_shoppingPay_use'] = 'N';
	}
	// ---- SSJ : 상품 일괄 업로드 개선 추가 수정 : 2021-08-23 --------

	$s_query = "
		p_name					= '{$val['p_name']}',
		p_subname				= '{$val['p_subname']}',
		p_view					= '{$val['p_view']}',
		p_commission_type	= '{$val['p_commission_type']}',
		p_sPrice					= '{$val['p_sPrice']}',
		p_sPersent				= '{$val['p_sPersent']}',
		p_screenPrice			= '{$val['p_screenPrice']}',
		p_price					= '{$val['p_price']}',
		p_stock					= '{$val['p_stock']}',
		p_sort_group			= '{$val['p_sort_group']}',
		p_orgin					= '{$val['p_orgin']}',
		p_maker					= '{$val['p_maker']}',
		p_point_per				= '{$val['p_point_per']}',
		p_coupon				= '{$p_coupon}',
		p_delivery_info			= '{$val['p_delivery_info']}',
		p_shoppingPay_use	= '{$val['p_shoppingPay_use']}',
		p_shoppingPayFree	= '{$val['p_shoppingPayFree']}',
		p_shoppingPay		= '{$val['p_shoppingPay']}',
		p_relation				= '{$val['p_relation']}',
		p_relation_type			= '{$val['p_relation_type']}',
		p_content				= '{$val['p_content']}',
		p_content_m			= '{$val['p_content_m']}',
		p_img_list_square		= '{$val['p_img_list_square']}',
		p_img_b1				= '{$val['p_img_b1']}',
		p_img_b2				= '{$val['p_img_b2']}',
		p_img_b3				= '{$val['p_img_b3']}',
		p_img_b4				= '{$val['p_img_b4']}',
		p_img_b5				= '{$val['p_img_b5']}'
	";

	// ----- LCY ::: 입점업체 패치 ::: 2017-11-09 -----
	if( $SubAdminMode == true) {
		$s_query .= " , p_cpid = '".$val['p_cpid']."' ";
	}else{
		$s_query .= " , p_cpid = '".$dfCPID."' ";
	}

	// ----- JJC ::: 부가세율설정 ::: 2017-06-16 -----
	$s_query .= " , p_vat = '" . $val['p_vat'] . "' ";

	// ----- JJC ::: 브랜드관리 ::: 2017-11-03 -----
	$s_query .= " , p_brand = '" . $arr_brand_trans[$val['p_brand']] . "' ";

	// ----- JJC ::: 상품별 배송비 ::: 2018-08-16 -----
	$s_query .= "
		, p_shoppingPayPdPrice = '" . $val['p_shoppingPayPdPrice'] . "'
		, p_shoppingPayPfPrice = '" . $val['p_shoppingPayPfPrice'] . "'
	";

	// ----- LCY ::: 무료배송이벤트 -----
	$s_query .= " , p_free_delivery_event_use = '" . $val['p_free_delivery_event_use'] . "' ";

	// ----- LCY ::: 회원등급혜택 -----
	$s_query .= " , p_groupset_use = '" . $val['p_groupset_use'] . "' ";

	//  -----  카테고리 공백제거 -----
	$val['catename_1'] = trim($val['catename_1']);
	$val['catename_2'] = trim($val['catename_2']);
	$val['catename_3'] = trim($val['catename_3']);

	//  -----  카테고리 정보추출 -----
	// 3차까지 있는 경우
	if($val['catename_1'] && $val['catename_2'] && $val['catename_3']) {
		$cateLoad = _MQ("
						select
							c3.c_uid as cc3,
							c2.c_uid as cc2,
							c1.c_uid as cc1
						from smart_category as c3
						left join smart_category as c2 on (substring_index(c3.c_parent , ',' ,-1) = c2.c_uid and c2.c_depth = 2 and c2.c_name = '{$val['catename_2']}' )
						left join smart_category as c1 on (substring_index(c3.c_parent , ',' ,1) = c1.c_uid and c1.c_depth = 1 and c1.c_name = '{$val['catename_1']}')
						where
							c1.c_uid is not null and
							c2.c_uid is not null and
							c1.c_name = '{$val['catename_1']}' and c2.c_name = '{$val['catename_2']}' and c3.c_name = '{$val['catename_3']}' and
							c3.c_depth = 3
					");
	}
	// 2차까지 있는 경우
	else if($val['catename_1'] && $val['catename_2']) {
		$cateLoad = _MQ("
						select
							c2.c_uid as cc2,
							c1.c_uid as cc1
						from smart_category as c2
						left join smart_category as c1 on ( c2.c_parent = c1.c_uid and c1.c_depth = 1 and c1.c_name = '{$val['catename_1']}')
						where
							c1.c_uid is not null and
							c1.c_name = '{$val['catename_1']}' and c2.c_name = '{$val['catename_2']}' and
							c2.c_depth = 2
					");
	}
	// 1차까지 있는 경우
	else if($val['catename_1'] ) {
		$cateLoad = _MQ("
						select
							c1.c_uid as cc1
						from smart_category as c1
						where
							c1.c_uid is not null and
							c1.c_name = '{$val['catename_1']}'  and
							c1.c_depth = 1
					");
	}
	//  -----  카테고리 정보추출 -----
	$real_catecode = ($cateLoad['cc3'] ? $cateLoad['cc3'] : ($cateLoad['cc2'] ? $cateLoad['cc2'] : $cateLoad['cc1']	));

	// 카테고리 처리
	$c_que = "
		INSERT INTO smart_product_category (pct_pcode, pct_cuid) VALUES ('". $p_code ."', '". $real_catecode ."')
		ON DUPLICATE KEY UPDATE pct_pcode='". $p_code ."' , pct_cuid='". $real_catecode ."'
	";
	_MQ_noreturn($c_que);

	// KAY :: 일괄업로드 :: 2021-07-02 -- 옵션처리를 위한 엑셀에서 받은 옵션값 임시 저장
	if(trim($val['p_option_excel'])) {
		_MQ_noreturn("insert into smart_product_option_tmp set pot_pcode = '" . $p_code . "', pot_pucuid = '". $app_upload_uid ."', pot_info = '" .trim( $val['p_option_excel']) . "', pot_rdate = now() ");
	}

	//상품추가 & 상품수정
	switch ($mode) {
		// - 상품추가 ---
		case "add":
			$que="insert into smart_product set
					p_code			= '" . $p_code . "',
					p_rdate = now() ,
					{$s_query}
					";
			_MQ_noreturn($que);
		break;
		// - 상품추가 ---

		// - 상품수정 ---
		case "modify":
			$que="update smart_product set
					{$s_query}
					where
					p_code			= '" . $p_code. "'";
			_MQ_noreturn($que);
		break;

		// - 상품수정 ---
	} // case END

	// JJC : 옵션 적합성 검사 - p_option_valid_chk 정보 업데이트 : 2018-04-16
	product_option_validate_check($p_code);

	// SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11
	product_soldout_check($p_code);

}

// 카테고리 상품 개수 업데이트
update_catagory_product_count();

// SSJ : 2017-09-18 p_idx 재정렬
product_resort();

// KAY :: 일괄업로드 :: 2021-07-02 -- 임시 옵션 백그라운드에서 처리
$url =$system['url'].'/totalAdmin/_product.upload.option_pro.php?app_upload_uid='.$app_upload_uid;
curl_async($url);

// 부모창 프로그래스바 실행 (옵션 진행률 프로그래스 바 실행)
echo "<script>parent.progress('".$app_upload_uid."');</script>";

exit;