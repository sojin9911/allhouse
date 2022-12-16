<?PHP
header("Content-Type: charset=utf-8;"); // // EP 파일 업데이트 추가
/*
	//  index.php 맨아래 에 아래 코드 추가
	// 네이버 지식쇼핑 EP 연동
	// 	include_once "./addons/ep/naver/index.php";
*/
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
ob_end_clean();

	// 지식쇼핑 전체 적용 - 상품 전체 설정의 체크 스위치가 Y 일경우
	if($siteInfo[s_naver_switch] == "Y") {

		// 전체 EP 시작
		// 상품이 노출이고, 네이버 ep 적용이 Y 이고, 재고량이 0 보다 크고
		$_data = _MQ_assoc("select * from smart_product where p_view='Y' and p_naver_switch='Y' and p_stock > 0 order by p_idx asc, p_rdate desc");

		// 헤더생성 (전체ep는 구분타입및 업데이트 시간 제외)
		$header='id,title,price_pc,price_mobile,normal_price,link,mobile_link,image_link,add_image_link,category_name1,category_name2,category_name3,category_name4,naver_category,naver_product_id,condition,import_flag,parallel_import,order_made,product_flag,adult,goods_type,barcode,manufacture_define_number,model_number,brand,maker,origin,card_event,event_words,coupon,partner_coupon_download,interest_free_event,point,installation_costs,search_tag,group_id,vendor_id,coordi_id,minimum_purchase_quantity,review_count,shipping,delivery_grade,delivery_detail,attribute,option_detail,seller_id,age_group,gender';
		$arr_header = explode(",",$header);

		ob_start(); // EP 파일 업데이트 추가
		echo nv_create_tsv($arr_header)."\n";

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

			// -- 포인트율
			$point = ($price * $data[p_point_per] / 100);
			$point = $point > 0 ? '^'.$point : '';

			$normal_price = $data['p_screenPrice'] > $data['p_price'] ? $data['p_screenPrice'] : '';  // -- 할인전 가격

			// 상품 명
			if($data[p_subname] <> ''){ // 서브제목이 있다면
				$pname = cutstr($data['p_name']." - ".$data['p_subname'],95);
			}else{ // 없다면
				$pname = cutstr($data['p_name'],95);
			}

			// 문의갯수
//			$review_count = nv_get_pro_talk_total($data['p_code']);
			$review_count = get_talk_total($data['p_code'],'eval'); // LCY : 문의개수->후기개수로 변경 : 2020-10-15 

			// 쿠폰할인율 (하이센스 미적용)
			$coupo = '';

			// 메이커 (입력이 없을 시 공백 )
			$maker = $data['p_maker'] == ''? '' :  $data['p_maker'];

			// 원산지 (입력없을 시 공백 )
			$origin = $data['p_orgin'] == ''? '' : $data['p_orgin'];

			// 배송비 정책 추출
			$pro_delivery_info = get_delivery_info($data['p_code']);

			# 배송비 정책
			switch($data[p_shoppingPay_use]){
				case 'Y': // 개별 배송
					$delivery_price = $pro_delivery_info[price];  // 상품의 개별배송비
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

			// -- 업체아이디
			$p_cpid = $data['p_cpid'];

			$image_link = get_img_src($data['p_img_list_square'], IMG_DIR_PRODUCT);

			$arr_tsv_content = array(
			'id'=>$data[p_code], // @ 상품코드 Y
			'title'=>$pname, // @ 상품명 Y
			'price_pc'=>$price, // @ 상품가격PC Y
			'price_mobile'=>$price, // @ 상품가격MOBILE
			'normal_price'=>$normal_price, // @ 할인전 가격
			'link'=>"http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$data[p_code], // @ 상품링크  Y
			'mobile_link'=>"http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$data[p_code], // @ 상품링크  Y
			'image_link'=>$image_link, // @ 이미지링크   Y
			'add_image_link'=>'', // @ 추가 이미지
			'category_name1'=>$category[depth1_catename], // @ 캍테고리명 1차 Y
			'category_name2'=>$category[depth2_catename], // @ 캍테고리명 2차
			'category_name3'=>$category[depth3_catename], // @ 캍테고리명 3차
			'category_name4'=>$category[depth4_catename], // @ 캍테고리명 4차
			'naver_category'=>'', // @ 네이버 카테고리 매칭코드
			'naver_product_id'=>'', // @ 네이버 가격비교상품코드
			'condition'=>'', // @ 상품상태  R
			'import_flag'=>'', // @ 해외구매대행 여부 R
			'parallel_import'=>'', // @ 병생수입여부 R
			'order_made'=>'', // @ 주문제작상품여부 R
			'product_flag'=>'', // @ 판매방식 R
			'adult'=>'', // @ 미성년자 구매불가 상품 여부
			'goods_type'=>'', // @ 상품구분
			'barcode'=>'', // @ 바코드
			'manufacture_define_number'=>'', // @ 제품코드
			'model_number'=>'', // @ 모델명
			'brand'=>'', // @ 브랜드
			'maker'=>$maker, // @ 제조사
			'origin'=>$origin, // @ 원산지
			'card_event'=>'', // @ 카드명/카드할인가격
			'event_words'=>'',  // @ 이벤트
			'coupon'=>'',  // @ 일반/제휴쿠폰
			'partner_coupon_download'=>'', // @ 쿠폰다운로드필요 여부 R
			'interest_free_event'=>'', // @ 카드 무이자 할부정보
			'point'=>$point, // @ 포인트
			'installation_costs'=>'', // @ 별도 설치비 유무 R
			'search_tag'=>'', // @ 검색태그
			'group_id'=>'',  // @ 그룹아이디
			'vendor_id'=>'', // @제휴사 상품 아이디
			'coordi_id'=>'', // @ 코디상품 아이디
			'minimum_purchase_quantity'=>'', // @ 최소구매수량
			'review_count'=>$review_count, // @ 상품평 카운트
			'shipping'=>$delivery_price, // @ 배송비
			'delivery_grade'=>'', // @ 차등배송비여부
			'delivery_detail'=>'', // @ 차등배송비 내용
			'attribute'=>'', // @ 상품속성
			'option_detail'=>'',  // @ 구매옵션
			'seller_id'=>$p_cpid, // @ 업체아이디
			'age_group'=>'', // @ 주 이용 고객층
			'gender'=>'' // @ 성별
			);
			echo nv_create_tsv($arr_tsv_content)."\n"; // EP 파일 업데이트 추가
		}

		$content = ob_get_contents(); // EP 파일 업데이트 추가
		ob_end_clean(); // EP 파일 업데이트 추가
		echo $content; // EP 파일 업데이트 추가

	} // switch 가 Y일경우

	// 상품문의 개수 (상품평가도 가져올 수 있음)
	function nv_get_pro_talk_total($pcode) {
		$row = _MQ("select count(*) as cnt from smart_product_talk where 1 and pt_pcode = '".$pcode."' and pt_type = '상품문의' ");
		return $row[cnt];
	}

	// 상품 카테고리 정보를 가져온다.
	// cuid 1순위, 없을 경우 상품코드 2순위
	function nv_get_pro_depth_info($pcode){

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


	// @ tsv 형식변환
	function nv_create_tsv($arr)
	{
		if( count($arr) < 1) { return false; }
		$tsv = implode("\t",$arr);
		return $tsv;
	}

?>

