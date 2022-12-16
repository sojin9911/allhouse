<?php
// KAY :: 일괄업로드 옵션 처리를 위한 파일 :: 2021-06-01

//------- 임시 옵션 처리를 위해 smart_product_option_tmp 'DB생성' ----기록

set_time_limit(0);
ini_set('memory_limit','-1');
include_once("../include/inc.php");

// KAY :: 2021-05-31 :: 옵션 500개씩 루프
$que = " SELECT * FROM smart_product_option_tmp where pot_pucuid = '". $app_upload_uid ."' ORDER BY pot_uid asc limit 0, 500 ";
$res = _MQ_assoc($que);

// KAY :: 2021-05-31 :: 옵션 정보 저장
foreach( $res as $k=>$v) {
	$arr_max_sort = array();	// 옵션 순서지정 배열화
	$arr_op_uid = array();		// 옵션 고유번호 배열화
	$arr_option = array();		// 최종옵션 가격정보 배열화

	$p_code = $v['pot_pcode'];	// 상품코드
	$op_ex = explode("§" , $v['pot_info']);	//옵션 §로 구분

	foreach( $op_ex as $opk=>$opv ){
		$arr_op_ex = array($opv); // 옵션 구분 후 배열 (옵션명 지정을 위한 처리)

		foreach( $arr_op_ex as $opk1=>$opv1){
			$op_type_ex = explode(">" , $opv1); // 옵션 > 로 구분
			$op_type_chk = (sizeof(array_filter($op_type_ex)) > 0 ) > 0 ? sizeof(array_filter($op_type_ex)) . "depth" : "nooption";		// 옵션 차수 뽑기 - 첫번째 옵션 구분을 통한 차수 뽑기

			$op1_uid = $op2_uid = $op3_uid = ""; // 고유번호 초기화

			switch(rm_str($op_type_chk)){
				// ------------------ 3차 옵션 처리 ------------------
				case "3":
					// 옵션명 지정
					$_option1 = trim($op_type_ex[0]); // 1차 옵션명
					$_option2 = trim($op_type_ex[1]); // 2차 옵션명
					$op_ex_3 = explode("|" , trim($op_type_ex[2])); // |로 구분한 값 (3차 옵션명,판매가,공급가,재고 값)
					$_option3 = trim($op_ex_3[0]); // 3차 옵션명

					// 배열 키에 지정할 full 옵션명 지정
					$_full_op_name1 = $_option1;  // 1차 , 2차 옵션명 (2차일때 옵션명으로 구분)
					$_full_op_name2 = $_option1 .",". $_option2 ;  // 1차 , 2차 옵션명 (2차일때 옵션명으로 구분)
					$_full_op_name3 = $_option1 .",". $_option2 .",". $_option3 ; // 1차 , 2차 , 3차 옵션명 (3차 추가,수정시 옵션명으로 구분)

					// 옵션순서 체크를 위한 full 옵션고유번호 지정
					$_sort_op1_uid = $p_code .",". $op1_uid;						 // 상품코드 , 1차 옵션 uid
					$_sort_op2_uid = $p_code .",". $op1_uid .",". $op2_uid; // 상품코드 1차,2차 uid
					$_sort_op3_uid = $p_code .",". $op1_uid .",". $op2_uid .",". $op3_uid; //1차,2차,3차 uid

					// 3차 옵션 정보
					$op_info = array(
						"option1" => $_option1, // 1차옵션명
						"option2" => $_option2, // 2차옵션명
						"option3" => $_option3, // 3차옵션명
						"supplyprice" =>trim($op_ex_3[1]) ,  // 공급가
						"price" => trim($op_ex_3[2]) ,  // 판매가
						"cnt" => trim($op_ex_3[3]) // 재고
					);

					// ------- 1차 옵션 -------
					// 옵션명 구분 후 1차 옵션고유번호(uid)추출
					if(!$arr_op_uid[$_full_op_name1] ){
						$op1_uid = _MQ_result(" select po_uid from smart_product_option where po_pcode='" . $p_code . "' and po_depth='1' and po_poptionname='". addslashes($_option1) . "' ");
						$arr_op_uid[$_full_op_name1] = $op1_uid;
					}
					// 추출한 1차옵션 uid 변수 저장
					$op1_uid = $arr_op_uid[$_full_op_name1];

					//1차 uid가 없을 경우 추가 , 1차 uid가 있을 경우 2차옵션으로.
					if( !$op1_uid ) {
						if( !$arr_max_sort[$_sort_op1_uid]) {	//1차 uid 추출
							$arr_max_sort[$_sort_op1_uid] = _MQ_result(" select ifnull(max(po_sort),0) + 1 as max_sort from smart_product_option where po_pcode='" . $p_code . "' and po_depth='1' and po_parent='' ");
						}
						else {
							$arr_max_sort[$_sort_op1_uid] ++;
						}
						// ------- 1차 옵션 추가 -------
						_MQ_noreturn(" INSERT smart_product_option SET po_poptionname='". addslashes($_option1) . "', po_poptionprice='0', po_poption_supplyprice='0', po_cnt='0' , po_depth='1', po_pcode='". $p_code ."', po_sort='". $arr_max_sort[$_sort_op1_uid] ."' ");

						$op1_uid = mysql_insert_id();
						$arr_op_uid[$_full_op_name1] = $op1_uid;
					}

					// ------- 2차 옵션 -------
					// 1차,2차 옵션명으로 구분하여 2차 옵션고유번호(uid)추출
					if(!$arr_op_uid[$_full_op_name2] ){
						$op2_uid = _MQ_result(" select po_uid from smart_product_option where po_pcode='" . $p_code . "' and po_depth='2' and po_parent='". $op1_uid ."' and po_poptionname='". addslashes($_option2) . "' ");
						$arr_op_uid[$_full_op_name2] = $op2_uid;
					}
					// 추출한 2차옵션 uid 변수 저장
					$op2_uid = $arr_op_uid[$_full_op_name2];

					//2차 uid가 없을 경우 추가 , 2차 uid가 있을 경우 3차옵션으로.
					if( !$op2_uid) {
						if( !$arr_max_sort[$_sort_op2_uid]) {
							$arr_max_sort[$_sort_op2_uid] = _MQ_result(" select ifnull(max(po_sort),0) + 1 as max_sort from smart_product_option where po_pcode='" . $p_code . "' and po_depth='2' and po_parent='". $op1_uid ."' ");
						}
						else {
							$arr_max_sort[$_sort_op2_uid] ++;
						}
						// ------- 2차 옵션 추가 -------
						_MQ_noreturn(" INSERT smart_product_option SET po_poptionname='". addslashes($_option2) . "', po_poptionprice='0', po_poption_supplyprice='0', po_cnt='0' , po_depth='2', po_pcode='". $p_code ."', po_parent='". $op1_uid ."' , po_sort='". $arr_max_sort[$_sort_op2_uid] ."' ");

						$op2_uid = mysql_insert_id();
						$arr_op_uid[$_full_op_name2] = $op2_uid;
					}

					// ------- 3차 옵션 -------
					// 1차,2차,3차 옵션명으로 구분하여 3차옵션 고유번호,판매가,공급가,재고 추출
					if(!$arr_op_uid[$_full_op_name3] ){
						$op3_row = _MQ("
							select
								po_uid , po_poptionprice , po_poption_supplyprice , po_cnt
							from smart_product_option
							where
								po_pcode='" . $p_code . "' and
								po_depth='3' and
								po_parent='". $op1_uid .",". $op2_uid ."' and
								po_poptionname='". addslashes($_option3) . "'
						");
						$arr_op_uid[$_full_op_name3] = $op3_row['po_uid'];	// 3차 옵션 고유번호
						$arr_option[$_full_op_name3] = $op3_row ;				// 3차 옵션 정보 변수
					}

					// 추출한 3차옵션 uid 변수 저장
					$op3_uid = $arr_op_uid[$_full_op_name3];

					//	3차 uid가 없을 경우 추가
					if( !$op3_uid ) {
						if( !$arr_max_sort[$_sort_op3_uid] ) {
							$arr_max_sort[$_sort_op3_uid] = _MQ_result(" select ifnull(max(po_sort),0) + 1 as max_sort from smart_product_option where po_pcode='" . $p_code. "' and po_depth='3' and po_parent='". $op1_uid .",". $op2_uid ."' ");
						}
						else {		$arr_max_sort[$_sort_op3_uid] ++;	}
						_MQ_noreturn("
							INSERT smart_product_option SET
								po_poptionname='". addslashes($_option3) . "',
								po_poptionprice='". $op_info['price'] ."',
								po_poption_supplyprice='". $op_info['supplyprice'] ."',
								po_cnt='". $op_info['cnt'] ."' ,
								po_depth='3', po_pcode='". $p_code ."', po_parent='". $op1_uid .",". $op2_uid ."' , po_sort='". $arr_max_sort[$_sort_op3_uid] ."'
						");
						$op3_uid = mysql_insert_id();
						$arr_op_uid[$_full_op_name3] = $op3_uid;
					}

					//	3차 uid가 있을 경우 공급가,판매가,재고 비교 후 수정
					else if( $arr_option['po_poptionprice'] <> $op_info['price'] || $arr_option['po_poption_supplyprice'] <> $op_info['supplyprice'] || 	$arr_option['po_cnt'] <> $op_info['cnt'] ){
						_MQ_noreturn( "
							UPDATE smart_product_option SET
								po_poptionprice='". $op_info['price'] ."' ,
								po_poption_supplyprice='". $op_info['supplyprice'] ."' ,
								po_cnt='". $op_info['cnt'] ."'
							WHERE
								po_uid = '". $op3_uid ."' AND
								po_poptionname='".$op_info['option3']. "'
						");
					}
					break;
				// ------------------ 3차 옵션 처리 ------------------


				// ------------------ 2차 옵션 처리 ------------------
				case "2":
					$_option1 = trim($op_type_ex[0]); // 1차 옵션명
					$op_ex_2 = explode("|" , trim($op_type_ex[1])); // |로 구분
					$_option2 = trim($op_ex_2[0]); // 2차 옵션명

					// 2차 옵션 정보
					$op_info= array("option1" => $_option1,"option2" => $_option2,"supplyprice" => trim($op_ex_2[1]) , "price" => trim($op_ex_2[2]) , "cnt" => trim($op_ex_2[3]));

					// 배열 키에 지정할 full 옵션명 지정
					$_full_op_name1 = $_option1;  // 1차 , 2차 옵션명 (2차일때 옵션명으로 구분)
					$_full_op_name2 = $_option1 .','. $_option2 ;  // 1차 , 2차 옵션명 (2차일때 옵션명으로 구분)

					// 옵션순서 체크를 위한 full 옵션고유번호 지정
					$_sort_op1_uid = $p_code .','. $op1_uid; //상품코드 , 1차 옵션 uid
					$_sort_op2_uid = $p_code .','. $op1_uid .",". $op2_uid; //1차,2차 uid

					// ------- 1차 옵션 -------
					// 옵션명 구분 후 1차 옵션고유번호(uid)추출
					if(!$arr_op_uid[$_full_op_name1] ){
						$op1_uid = _MQ_result(" select po_uid from smart_product_option where po_pcode='" . $p_code . "' and po_depth='1' and po_poptionname='". addslashes($_option1) . "' ");
						$arr_op_uid[$_full_op_name1] = $op1_uid;
					}

					// 추출한 1차옵션 uid 변수 저장
					$op1_uid = $arr_op_uid[$_full_op_name1];

					//1차 uid가 없을 경우 추가 , 1차 uid가 있을 경우 2차옵션으로.
					if(!$op1_uid){
						if( !$arr_max_sort[$_sort_op1_uid] ) {
							$arr_max_sort[$_sort_op1_uid] = _MQ_result(" select ifnull(max(po_sort),0) + 1 as max_sort from smart_product_option where po_pcode='" . $p_code . "' and po_depth='1' and po_parent='' ");
						}
						else {
							$arr_max_sort[$_sort_op1_uid] ++;
						}
						_MQ_noreturn(" INSERT smart_product_option SET po_poptionname='". addslashes($_option1) . "', po_poptionprice='0', po_poption_supplyprice='0', po_cnt='0' , po_depth='1', po_pcode='". $p_code ."', po_sort='". $arr_max_sort[$_sort_op1_uid] ."' ");
						$op1_uid = mysql_insert_id();
						$arr_op_uid[$_full_op_name1] = $op1_uid;
					}

					// ------- 2차 옵션 -------
					// 1차,2차 옵션명으로 구분하여 2차 옵션고유번호(uid)추출
					if(!$arr_op_uid[$_full_op_name2] ){
						$op2_row = _MQ("
							select
								po_uid , po_poptionprice,po_poption_supplyprice,po_cnt
							from smart_product_option
							where
								po_pcode='" . $p_code . "' and
								po_depth='2' and po_parent='". $op1_uid ."' and
								po_poptionname='". addslashes($_option2) . "'
							");
						$arr_op_uid[$_full_op_name2] = $op2_row['po_uid'];
						$arr_option[$_full_op_name2] = $op2_row;
					}

					// 추출한 2차옵션 uid 변수 저장
					$op2_uid = $arr_op_uid[$_full_op_name2];

					//	2차 uid가 없을 경우 추가
					if(!$op2_uid ){
						if( !$arr_max_sort[$_sort_op2_uid] ) {
							$arr_max_sort[$_sort_op2_uid] = _MQ_result(" select ifnull(max(po_sort),0) + 1 as max_sort from smart_product_option where po_pcode='" . $p_code . "' and po_depth='2' and po_parent='". $op1_uid ."' ");
						}
						else {
							$arr_max_sort[$_sort_op2_uid] ++;
						}
						_MQ_noreturn("
							INSERT smart_product_option SET
							po_poptionname='". addslashes($_option2) . "',
							po_poptionprice='". rm_str($op_info['price']) ."',
							po_poption_supplyprice='". rm_str($op_info['supplyprice']) ."',
							po_cnt='". rm_str($op_info['cnt']) ."' , po_depth='2', po_pcode='". $p_code ."', po_parent='". $op1_uid ."' , po_sort='". $arr_max_sort[$_sort_op2_uid] ."'
						");

						$op2_uid = mysql_insert_id();
						$arr_op_uid[$_full_op_name2] = $op2_uid;
					}
					//	2차 uid가 있을 경우 공급가,판매가,재고 비교 후 수정
					else if( $arr_option['po_poptionprice'] <> $op_info['price'] || $arr_option['po_poption_supplyprice'] <> $op_info['supplyprice'] || 	$arr_option['po_cnt'] <> $op_info['cnt'] ){
						_MQ_noreturn( "
							UPDATE smart_product_option SET
								po_poptionprice='".$op_info['price']."',
								po_poption_supplyprice='". $op_info['supplyprice']."',
								po_cnt='". $op_info['cnt']."'
							WHERE
								po_uid = '". $op2_uid ."' AND
								po_poptionname='". $op_info['option2']."'
						");
					}
					break;
				// ------------------ 2차 옵션 처리 ------------------


				// ------------------ 1차 옵션 처리 ------------------
				case "1":
					$op_ex_1 = explode("|" , trim($opv1)); // | 로 구분 (1차 옵션명,공급가,판매가,재고)
					$_option1 = trim($op_ex_1[0]); // 1차 옵션명
					$op_info = array("option1" => $_option1 ,"supplyprice" =>trim($op_ex_1[1]), "price" => trim($op_ex_1[2]) , "cnt" => trim($op_ex_1[3])); // 1차 옵션 정보

					// 배열 키에 지정할 full 옵션명 지정
					$_full_op_name1 = $_option1;  // 1차 옵션명

					// 옵션순서 체크를 위한 full 옵션고유번호 지정
					$_sort_op1_uid = $p_code .','. $op1_uid; //상품코드 , 1차 옵션 uid

					// 옵션명 구분 후 1차 옵션고유번호(uid)추출
					if(!$arr_op_uid[$_full_op_name1] ){
						$op_row = _MQ("
							select
								po_uid , po_poptionprice , po_poption_supplyprice,po_cnt
							from smart_product_option
							where
								po_pcode='" . $p_code . "' and
								po_depth='1' and
								po_poptionname='". addslashes($_option1) . "'
						");
						$arr_op_uid[$_full_op_name1] = $op_row['po_uid'];
						$arr_option[$_full_op_name1] = $op_row; //update에 적용할 옵션정보 추출
					}

					// 추출한 1차옵션 uid 변수 저장
					$op1_uid = $arr_op_uid[$_full_op_name1];

					//	1차 uid가 없을 경우 추가
					if(!$op1_uid){
						if( !$arr_max_sort[$_sort_op1_uid] ) {
							$arr_max_sort[$_sort_op1_uid] = _MQ_result(" select ifnull(max(po_sort),0) + 1 as max_sort from smart_product_option where po_pcode='" . $p_code . "' and po_depth='1' and po_parent='' ");
						}
						else {
							$arr_max_sort[$_sort_op1_uid] ++;
						}
						_MQ_noreturn("
							INSERT smart_product_option SET
							po_poptionname='". addslashes($_option1) . "',
							po_poptionprice='". rm_str($op_info['price']) ."',
							po_poption_supplyprice='". rm_str($op_info['supplyprice']) ."',
							po_cnt='". rm_str($op_info['cnt']) ."' , po_depth='1', po_pcode='". $p_code ."', po_sort='". $arr_max_sort[$_sort_op1_uid] ."'
						");
						$op1_uid = mysql_insert_id();
						$arr_op_uid[$_full_op_name1] = $op1_uid;
					}

					//	1차 uid가 있을 경우 공급가,판매가,재고 비교 후 수정
					else if( $arr_option['po_poptionprice'] <> $op_info['price'] || $arr_option['po_poption_supplyprice'] <> $op_info['supplyprice'] || 	$arr_option['po_cnt'] <> $op_info['cnt'] ){
						// 1차 옵션 - 추가
						_MQ_noreturn( "
							UPDATE smart_product_option SET
								po_poptionprice='".$op_info['price']."',
								po_poption_supplyprice='". $op_info['supplyprice']."',
								po_cnt='". $op_info['cnt']."'
							WHERE
								po_uid = '". $op1_uid ."' AND
								po_poptionname='".$op_info['option1']. "'
						");
					}
					break;
				// ------------------ 1차 옵션 처리 ------------------
			}
		}
	}

	// 옵션타입 저장
	if($op_type_chk){
		_MQ_noreturn( "
			UPDATE smart_product SET
				p_option_type_chk = '".$op_type_chk."'
			WHERE
				p_code = '". $p_code ."'
		");
	}

	//JJC : 옵션 적합성 검사 - p_option_valid_chk 정보 업데이트 : 2018-04-16
	product_option_validate_check($p_code);

	// SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11
	product_soldout_check($p_code);

	// 옵션 저장 후 엑셀 업로드 상품옵션 임시관리 DB에서 삭제 처리
	_MQ_noreturn("DELETE FROM smart_product_option_tmp WHERE pot_uid = '".$v['pot_uid']."' ");

}

// 임시 처리될 옵션 개수 체크
$cnt = _MQ_result(" SELECT count(*) FROM smart_product_option_tmp where pot_pucuid = '". $app_upload_uid ."' ");

// 삭제할 임시옵션이 남았을 경우 백그라운드에서 다시 실행
if($cnt > 0){
	$url =$system['url'].'/totalAdmin/_product.upload.option_pro.php?app_upload_uid='.$app_upload_uid;
	curl_async($url);
}

exit;