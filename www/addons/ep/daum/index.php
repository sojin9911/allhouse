<?PHP
/*
	//  index.php 맨아래 에 아래 코드 추가
	//  다음 하우쇼핑 EP 연동
	//	include_once "./addons/ep/daum/index.php";
*/
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// 파일생성 : 상품 갱신 정보
include_once(dirname(__FILE__).'/daum.update.time.php');
// 다음 하우쇼핑 전체 적용 - 상품 전체 설정의 체크 스위치가 Y 일경우 
if($siteInfo[s_daum_switch] == "Y") { 

	/* 
		#요약 EP 업데이트 시간  
		// 현재 시간이 21 시 이상 23시 이하 이고 마지막 업데이트 년월일 이 오늘날이 아닐때
	*/ 
	if( date('H',time()) >= 21 && date('H',time()) <= 23 && date("Ymd" , $last_update_date) != date("Ymd") ) {


		// 현재 시간을 입력한다.
		$last_update_date = time();
		$fp = fopen(dirname(__FILE__)."/daum.update.time.php", "w");
		fputs($fp,"<?PHP\n\n\t\$last_update_date = '".$last_update_date."';\n\n\t\$last_update_time = '".$last_update_time."';\n\n?>");
		fclose($fp);

		$_mode = "all";
		daum_do($_mode);
		//echo "fd";
	}

	/* 
		#요약 EP 업데이트 시간  
		// 현재시간과분이8시 29분보다 크고, 20시31분보다 작으며, 현재시간초에서 마지막업데이트 시간을 뺀 시간초가 3600(한시간) 보다 클경우: 한시간마다 
	*/
	if( date('Hi',time()) > 829 && date('Hi',time()) < 2031 && (time() - $last_update_time) > 3600 ) {

		// 현재 시간을 입력한다.
		$last_update_time = time();
		$fp = fopen(dirname(__FILE__)."/daum.update.time.php", "w");
		fputs($fp,"<?PHP\n\n\t\$last_update_date = '". $last_update_date ."';\n\n\t\$last_update_time = '". $last_update_time ."';\n\n?>");
		fclose($fp);

		$_mode = "update";
		daum_do($_mode);

	}
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


// EP 생성 
function daum_do($_mode) {

	global $siteInfo ;

	switch($_mode) { 
			case 'all':
			// 전체 EP 시작
			// 상품이 노출이고,  다음 ep 적용이 Y 이고, 재고량이 0 보다 크고
			$_data = _MQ_assoc("select * from smart_product where p_view='Y' and p_daum_switch='Y' and p_stock > 0 order by p_idx asc, p_rdate desc");

			// all.txt 파일을 쓰기 모드로 연다. 
			$fp = fopen(dirname(__FILE__)."/all.txt","w");

/*			// 업체별 배송 정책을 가져온다. 
			$cp_data_arr = array(); // 초기화 
			$cp_data =_MQ_assoc("SELECT cp_delivery_price , cp_delivery_freeprice  FROM smart_company where cp_id = '".$data['p_cpid']."' AND cp_delivery_use = 'Y' ");
			
			foreach($cp_data as $ck=>$cv){
				$cp_data_arr[$cv['cp_id']]['cp_delivery_use'] = $cv['cp_delivery_use'] ; // 배송비정책 사용 여부
				$cp_data_arr[$cv['cp_id']]['cp_delivery_price'] = $cv['cp_delivery_price']; // 배송비 
				$cp_data_arr[$cv['cp_id']]['cp_delivery_freeprice'] = $cv['cp_delivery_freeprice']; // 무료배송가 
			}*/

			// 총상품의 갯수
			$content = '<<<tocnt>>>'.count($_data)."\n";  //선택
			//$content ="";
			foreach($_data as $data) {


				
				// 상품 코드에서 상품의 카테고리 정보를 가져온다. 
				$category = daum_get_pro_depth_info($data[p_code]);

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

				}	
					
				$content .= "<<<begin>>>"; //  필수 : 시작 
				$content .= "\n<<<mapid>>>".$data[p_code]; // 필수 : 상품코드
				if($lprice <> false) {
					$content .= "\n<<<lprice>>>".$lprice; // 선택적필수(있을경우 필수) : 할인전(원가) 가격 
				}
				$content .= "\n<<<price>>>".$price; // 필수 : 상품가격 
				// $content .= "\n<<<mpric>>>"; // 선택적필수 (있을경우 필수) : 모바일할인적용가 (할인후가격) 
				// $content .= "\n<<<dolar>>>";  // 면세점 필수 : 상품 달러 판매가
				// $content .= "\n<<<mdolar>>>"; // 면세점 선택 : 모바일 달러 판매가 
				$content .= "\n<<<pname>>>".$pname; // 필수 : 상품이름
				$content .= "\n<<<pgurl>>>http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$data[p_code]; //필수 : 상품의 url 
				$content .= "\n<<<igurl>>>http://".$_SERVER[HTTP_HOST].IMG_DIR_PRODUCT.$data[p_img_b1]; //필수 : 상품의 이미지 url
				// $content .= "\n<<<upimg>>>"; // 선택 : 이미지 변경 여부 
				// $content .= "\n<<<gtype>>>"; // 선택 : 상품종류  
				$content .= "\n<<<cate1>>>".preg_replace("/\s+/","",$category[depth1_catename]); //필수 : 카테고리명  (대분류)
				$content .= "\n<<<caid1>>>".$category[depth1_catecode]; // 필수 : 카테고리 아이디 (대분류)
				$content .= "\n<<<cate2>>>".preg_replace("/\s+/","",$category[depth2_catename]); 
				$content .= "\n<<<caid2>>>".$category[depth2_catecode];
				$content .= "\n<<<cate3>>>".preg_replace("/\s+/","",$category[depth3_catename]);
				$content .= "\n<<<caid3>>>".$category[depth3_catecode];
				//$content .= "\n<<<cate4>>>"; //4차 카테고리 이름, 없을 시 공백
				//$content .= "\n<<<caid4>>>"; //4차 카테고리 코드, 없을 시 공백 			
				//$content .= "\n<<<model>>>"; // 선택 : 모델명
				//$content .= "\n<<<brand>>>"; //선택 : 브랜드명
				$content .= "\n<<<maker>>>".$maker;// 제조사
				//$content .= "\n<<<coupo>>>".$coupo;  // 선택 : 쿠폰 할인율		
				// $content .= "\n<<<mcoupon>>>";  // 선택 : 모바일쿠폰 / 제휴쿠폰
				// $content .= "\n<<<pcard>>>"; //선택 : 무이자할부
				// $content .= "\n<<<point>>>"; //선택 : 적립금/포인트
				$content .= "\n<<<deliv>>>".$delivery_price; //선택 : 배송비 (무료이거나 없을경우 0)
				// $content .= "\n<<<delivterm>>>"; //선택(없을경우 필드 주석) : 평균배송일
				//$content .= "\n<<<dlvdt>>>"; //선택 : 차등배송비
				// $content .= "\n<<<rating>>>".$eval_cnt; //선택 : 상품평 평점/만점 
				$content .= "\n<<<revct>>>".$eval_cnt; //선택 : 상품평 개수 
				//$content .= "\n<<<event>>>"; // 선택 이벤트여부 
				// $content .= "\n<<<carddn>>>"; // 선택 : 할인카드 
				// $content .= "\n<<<cardp>>>"; // 선택 : 할인카드 가격 
				// $content .= "\n<<<weight>>>"; // 선택 : 가중치값	
				// $content .= "\n<<<selid>>>"; // 선택 : 셀러 아이디 
				// $content .= "\n<<<adult>>>"; //선택 : 성인상품여부 
				// $content .= "\n<<<insco>>>"; //선택 : 별도설치비 
				$content .= "\n<<<ftend>>>"; //상품의 끝 
				$content .= "\n";
				}
			
				fputs($fp,$content);
				fclose($fp);
				break;

		case 'update':
		// 요약 EP 시작
		// 상품이 노출이고,  다음 ep 적용이 Y 이고, 재고량이 0 보다 크고 수정 날짜가 오늘인거
		$_data = _MQ_assoc("select * from smart_product where p_view='Y' and p_daum_switch='Y' order by p_idx asc, p_rdate desc");		
	
		$fp = fopen(dirname(__FILE__)."/brief.txt","w");


		foreach($_data as $data) {



			// 재고량
			$stock = $data['p_stock'];		

			if($stock > 0){
				
				//  상품 저장시 오차가 있을 수 있으니, 분단위 까지 
				if(date('Ymd',time()) ==  date('Ymd',strtotime($data['p_rdate']))){
					$class = "I";  // 신규상품 
				}else{
	
						$class = "U"; // 업데이트 상품 
				}

			}else{ // 품절상품
				$class = "D"; // 품절상품
			}

			// 상품 코드에서 상품의 카테고리 정보를 가져온다. 
			$category = daum_get_pro_depth_info($data[p_code]);

			// 상품가격 - 실판매가
			$price = $data[p_price];

			// 할인전 가격 - 있을경우에만
			$lprice = $data['p_screenPrice'] > $data[p_price] ?  $data['p_screenPrice'] : false;
	

			// 상품 명 
			if($data[p_subname] <> ''){ // 서브제목이 있다면 
				$pname = cutstr($data['p_name']." - ".$data['p_subname'],95);
			}else{ // 없다면
				$pname = cutstr($data['p_name'],95);
			}
			
			// 문의갯수 
			$eval_cnt = daum_get_pro_talk_total($data['p_code']);
			
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

			}	
			

			$content .= "<<<begin>>>";  // 상품시작 
			$content .= "\n<<<mapid>>>".$data[p_code]; //상품코드 
			$content .= "\n<<<price>>>".$price; //상품가격
			if($lprice <> false) {
				$content .= "\n<<<lprice>>>".$lprice; // 선택적필수(있을경우 필수) : 할인전(원가) 가격 
			}
			$content .= "\n<<<class>>>".$class; //업데이트 상품인지 품절 상품인지 U 업데이트 D 품절 I 신상품
			$content .= "\n<<<utime>>>".date('YmdHis'); // 상품정보 갱신 시작
			$content .= "\n<<<pname>>>".$pname; //상품이름
			$content .= "\n<<<igurl>>>http://".$_SERVER[HTTP_HOST].IMG_DIR_PRODUCT.$data[p_img_b1]; //업데이트된 필드 : 상품 이미지
			$content .= "\n<<<upimg>>>Y"; // 선택 : 이미지 변경 여부 
			$content .= "\n<<<cate1>>>".preg_replace("/\s+/","",$category[depth1_catename]); //필수 : 카테고리명  (대분류)
			$content .= "\n<<<caid1>>>".$category[depth1_catecode]; // 필수 : 카테고리 아이디 (대분류)
			$content .= "\n<<<cate2>>>".preg_replace("/\s+/","",$category[depth2_catename]); 
			$content .= "\n<<<caid2>>>".$category[depth2_catecode];
			$content .= "\n<<<cate3>>>".preg_replace("/\s+/","",$category[depth3_catename]);
			$content .= "\n<<<caid3>>>".$category[depth3_catecode];
			$content .= "\n<<<deliv>>>".$delivery_price; //업데이트된 필드 : 상품 배송비
			//$content .= "\n<<<coupo>>>".$coupo;  // 선택 : 쿠폰 할인율
			$content .= "\n<<<ftend>>>"; //상품 끝 
			$content .= "\n";
		}
		
		fputs($fp,$content);
		fclose($fp);
		break;
	} // switch case end 

} // 함수 end
?>