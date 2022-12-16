<?PHP
header("Content-Type: charset=utf-8;"); // // EP 파일 업데이트 추가
/*
	//  index.php 맨아래 에 아래 코드 추가
	//  다음 하우쇼핑 EP 연동
	//	include_once "./addons/ep/daum/index.php";
*/
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
ob_end_clean();

// 다음 하우쇼핑 전체 적용 - 상품 전체 설정의 체크 스위치가 Y 일경우
if($siteInfo[s_daum_switch] == "Y") {
	// 전체 EP 시작
	// 상품이 노출이고,  다음 ep 적용이 Y 이고, 재고량이 0 보다 크고
	$_data = _MQ_assoc("select * from smart_product where p_view='Y' and p_daum_switch='Y' and p_stock > 0  order by p_idx asc, p_rdate desc");

	ob_start(); // EP 파일 업데이트 추가
	// 총상품의 갯수
	$newLine = "\n";
	echo  '<<<tocnt>>>'.count($_data)."".$newLine."";  //선택
	//$content =""; // LCY : 2022-02-15 : 다음EP 생성 개선 패치
	foreach($_data as $data) {

		// 상품 코드에서 상품의 카테고리 정보를 가져온다.
		//$category = nv_get_pro_depth_info($data[p_code]);
		$get_category = get_pro_depth_info('',$data[p_code]);
		$category[depth1_catecode] = $get_category[0][1];
		$category[depth2_catecode] = $get_category[0][2];
		$category[depth3_catecode] = $get_category[0][3];

		$category[depth1_catename] = $get_category[1][1];
		$category[depth2_catename] = $get_category[1][2];
		$category[depth3_catename] = $get_category[1][3];

		// 상품가격 - 실판매가
		$price = $data[p_price];

		// 상품의 할인가격 - 있을경우에만 출력
		$lprice = $data['p_screenPrice'] > $data[p_price] ?  $data['p_screenPrice'] : false;

		// 상품 명
		if($data[p_subname] <> ''){ // 서브제목이 있다면
			$pname = cutstr($data['p_name']." - ".$data['p_subname'],95);
		}else{ // 없다면
			$pname = cutstr($data['p_name'],95);
		}

		// 문의갯수
		$eval_cnt = daum_get_pro_talk_total($data['p_code']);

		// 재고량
		$stock = $data['p_stock'];

		// 쿠폰할인율 (하이센스 미적용)
		$coupo = '';

		// 메이커 (입력이 없을 시 공백 )
		$maker = $data['p_maker'] == ''? '' :  $data['p_maker'];

		// 원산지 (입력없을 시 공백 )
		$p_orgin = $data['p_orgin'] == ''? '' : $data['p_orgin'];

		// 배송비 정책 추출
		$pro_delivery_info = get_delivery_info($data['p_code']);

		# 배송비 정책
		switch($data[p_shoppingPay_use]){
			case 'Y': // 개별 배송
				$delivery_price = $$pro_delivery_info[price];  // 상품의 개별배송비
			break;

			case 'F': // 무료배송
				$delivery_price = 0;
			break;

			case 'N': // 업체배송

				if($pro_delivery_info[price] > 0 ){ // 업체 (입점 또는 상점) 배송비가 0보다 클 시
					if($pro_delivery_info[freePrice] > 0){ // 무료배송가가 있을 경우
						$delivery_price = $price >= $pro_delivery_info[freePrice] ? 0 : $pro_delivery_info[price]; // 상품의 가격이 무료배송비보다 크거나 같을 경우 0원
					}else{ // 무료배송가가 없을경우 업체 (입점 또는 상점)
						$delivery_price = $pro_delivery_info[price]; // 업체의 기본 배송비 적용
					}
				}else{
						$delivery_price = 0; // 배송비 무료
				}

			break;

			case 'P': // 상품별배송
				$delivery_price = $data['p_shoppingPayPdPrice'];
				if( $data['p_shoppingPayPfPrice'] > 0 && $data['p_shoppingPayPfPrice'] <= $data['p_price'] ){
					$delivery_price = 0;
				}
			
			break;


		}

		$image_link = get_img_src($data['p_img_list_square'], IMG_DIR_PRODUCT);
		

		$content =""; // LCY : 2022-02-15 : 다음EP 생성 개선 패치
		$content .= "<<<begin>>>"; //  필수 : 시작
		$content .= "".$newLine."<<<mapid>>>".$data[p_code]; // 필수 : 상품코드
		if($lprice <> false) {
			$content .= "".$newLine."<<<lprice>>>".$lprice; // 선택적필수(있을경우 필수) : 할인전(원가) 가격
		}
		$content .= "".$newLine."<<<price>>>".$price; // 필수 : 상품가격
		// $content .= "".$newLine."<<<mpric>>>"; // 선택적필수 (있을경우 필수) : 모바일할인적용가 (할인후가격)
		// $content .= "".$newLine."<<<dolar>>>";  // 면세점 필수 : 상품 달러 판매가
		// $content .= "".$newLine."<<<mdolar>>>"; // 면세점 선택 : 모바일 달러 판매가
		$content .= "".$newLine."<<<pname>>>".$pname; // 필수 : 상품이름
		$content .= "".$newLine."<<<pgurl>>>http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$data[p_code]; //필수 : 상품의 url
		$content .= "".$newLine."<<<igurl>>>".$image_link; //필수 : 상품의 이미지 url
		// $content .= "".$newLine."<<<upimg>>>"; // 선택 : 이미지 변경 여부
		// $content .= "".$newLine."<<<gtype>>>"; // 선택 : 상품종류
		$content .= "".$newLine."<<<cate1>>>".preg_replace("/\s+/","",$category[depth1_catename]); //필수 : 카테고리명  (대분류)
		$content .= "".$newLine."<<<caid1>>>".$category[depth1_catecode]; // 필수 : 카테고리 아이디 (대분류)
		$content .= "".$newLine."<<<cate2>>>".preg_replace("/\s+/","",$category[depth2_catename]);
		$content .= "".$newLine."<<<caid2>>>".$category[depth2_catecode];
		$content .= "".$newLine."<<<cate3>>>".preg_replace("/\s+/","",$category[depth3_catename]);
		$content .= "".$newLine."<<<caid3>>>".$category[depth3_catecode];
		//$content .= "".$newLine."<<<cate4>>>"; //4차 카테고리 이름, 없을 시 공백
		//$content .= "".$newLine."<<<caid4>>>"; //4차 카테고리 코드, 없을 시 공백
		//$content .= "".$newLine."<<<model>>>"; // 선택 : 모델명
		//$content .= "".$newLine."<<<brand>>>"; //선택 : 브랜드명
		$content .= "".$newLine."<<<maker>>>".$maker;// 제조사
		//$content .= "".$newLine."<<<coupo>>>".$coupo;  // 선택 : 쿠폰 할인율
		// $content .= "".$newLine."<<<mcoupon>>>";  // 선택 : 모바일쿠폰 / 제휴쿠폰
		// $content .= "".$newLine."<<<pcard>>>"; //선택 : 무이자할부
		// $content .= "".$newLine."<<<point>>>"; //선택 : 적립금/포인트
		$content .= "".$newLine."<<<deliv>>>".$delivery_price; //선택 : 배송비 (무료이거나 없을경우 0)
		// $content .= "".$newLine."<<<delivterm>>>"; //선택(없을경우 필드 주석) : 평균배송일
		//$content .= "".$newLine."<<<dlvdt>>>"; //선택 : 차등배송비
		// $content .= "".$newLine."<<<rating>>>".$eval_cnt; //선택 : 상품평 평점/만점
		$content .= "".$newLine."<<<revct>>>".$eval_cnt; //선택 : 상품평 개수
		//$content .= "".$newLine."<<<event>>>"; // 선택 이벤트여부
		// $content .= "".$newLine."<<<carddn>>>"; // 선택 : 할인카드
		// $content .= "".$newLine."<<<cardp>>>"; // 선택 : 할인카드 가격
		// $content .= "".$newLine."<<<weight>>>"; // 선택 : 가중치값
		// $content .= "".$newLine."<<<selid>>>"; // 선택 : 셀러 아이디
		// $content .= "".$newLine."<<<adult>>>"; //선택 : 성인상품여부
		// $content .= "".$newLine."<<<insco>>>"; //선택 : 별도설치비
		$content .= "".$newLine."<<<ftend>>>"; //상품의 끝
		$content .= "".$newLine."";

		echo $content;
	}

		$content = ob_get_contents(); // EP 파일 업데이트 추가
		ob_end_clean(); // EP 파일 업데이트 추가
		echo $content; // EP 파일 업데이트 추가

} // switch 가 Y일경우


// 상품문의 개수 (상품평가도 가져올 수 있음)
function daum_get_pro_talk_total($pcode) {
	$row = _MQ("select count(*) as cnt from smart_product_talk where 1 and pt_pcode = '".$pcode."' and pt_type = '상품문의' ");
	return $row[cnt];
}

// 상품 카테고리 정보를 가져온다.
// cuid 1순위, 없을 경우 상품코드 2순위
function daum_get_pro_depth_info($pcode){

	$que = "
		select
			pct.* , ct3.c_name as ct3_name , ct3.c_uid as ct3_catecode, ct2.c_name as ct2_name ,  ct2.c_uid as ct2_catecode , ct1.c_name as ct1_name , ct1.c_uid as ct1_catecode
		from smart_product_category as pct
		left join smart_category as ct3 on (ct3.c_uid = pct.pct_cuid and ct3.c_depth=3)
		left join smart_category as ct2 on (substring_index(ct3.c_parent , ',' ,-1) = ct2.c_uid and ct2.c_depth=2)
		left join smart_category as ct1 on (substring_index(ct3.c_parent , ',' ,1) = ct1.c_uid and ct1.c_depth=1)
		where
			pct.pct_pcode='". $pcode ."'
			order by pct.pct_uid asc
	";
	$r = _MQ($que);

	$total_info[depth1_catename] = $r['ct1_name'];
	$total_info[depth2_catename] = $r['ct2_name'];
	$total_info[depth3_catename] = $r['ct3_name'];

	$total_info[depth1_catecode]  = $r['ct1_catecode'];
	$total_info[depth2_catecode]  = $r['ct2_catecode'];
	$total_info[depth3_catecode]  = $r['ct3_catecode'];

	return $total_info;
}


?>