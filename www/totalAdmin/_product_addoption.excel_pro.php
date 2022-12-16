<?php
# LDD011
include_once("inc.php");

switch ($tran_type) {
	
	// 엑셀 업로드
	case 'ins_excel':

		# Error Reproting level modify
		error_reporting(E_ALL ^ E_NOTICE);

		# 첨부파일 확인
		if($_FILES['w_excel_file']['size'] <= 0) error_loc_msg("_product_addoption.popup.php?pass_code=" . $pass_code, "첨부파일이 없습니다.");

		// --------------------- 상품옵션 정보 추출 --------------------- //
		$arr_option_uid = array(); // -- UID 별 옵션 저장
		$r = _MQ_assoc("select * from smart_product_addoption where pao_pcode='" . $pass_code . "' ");
		foreach( $r as $k=>$v ){
			$arr_option_uid[$v[pao_uid]] = $v;
		}

		$arr_option_name = array(); // -- optionname 별 옵션 저장
		foreach( $arr_option_uid as $k=>$v ){
			switch( $v['pao_depth'] ){
				case "3": 
					$ex = explode("," , $v['pao_parent']);
					$app_depth1_poptionname = $arr_option_uid[$ex[0]]['pao_poptionname'];
					$app_depth2_poptionname = $arr_option_uid[trim($ex[1]." " . $ex[2])]['pao_poptionname'];
					$arr_option_name[$app_depth1_poptionname][$app_depth2_poptionname][$v['pao_poptionname']] = $v;
				break;
				case "2": 
					$app_depth1_poptionname = $arr_option_uid[$v['pao_parent']]['pao_poptionname'];
					$arr_option_name[$app_depth1_poptionname][$v['pao_poptionname']][0] = $v;
				break;
				case "1": 
					$arr_option_name[$v['pao_poptionname']][0][0] = $v;
				break;
			}
		}
		// --------------------- 상품옵션 정보 추출 --------------------- //

		# Excel Class Load
		include_once(OD_ADDONS_ROOT.'/excelAddon/loader.php');
		$Excel = ExcelLoader($_FILES['w_excel_file']['tmp_name']);
		//die(ViewArr($Excel)); // 출력해보기

		# Excel 처리
		$arr_option_chk = array();
		foreach($Excel as $k=>$r) {
			if($k < 2) continue; // 파일정보와 헤더는 제외

			switch( $pass_mode ){
				case "3depth": 
					$_option1		= trim($r[0]);
					$_option2		= trim($r[1]);
					$_option3		= trim($r[2]);
					$_supplyprice	= trim($r[3]);
					$_price			= trim($r[4]);
					$_stock			= trim($r[5]);
				break;
				case "2depth": 
					$_option1		= trim($r[0]);
					$_option2		= trim($r[1]);
					$_option3		= 0;
					$_supplyprice	= trim($r[2]);
					$_price			= trim($r[3]);
					$_stock			= trim($r[4]);
				break;
				case "1depth": 
					$_option1		= trim($r[0]);
					$_option2		= 0;
					$_option3		= 0;
					$_supplyprice	= trim($r[1]);
					$_price			= trim($r[2]);
					$_stock			= trim($r[3]);
				break;
			}

			# 수정 또는 추가 처리 {
			if($arr_option_name[$_option1][$_option2][$_option3]) { // 수정

				$que = "
					update smart_product_addoption set 
						pao_poptionprice='". $_price."',
						pao_poptionpurprice='". $_supplyprice."',
						pao_cnt='". $_stock."'
					where pao_uid='". $arr_option_name[$_option1][$_option2][$_option3]['pao_uid'] ."'
				";
				_MQ_noreturn($que);
			}
			else { // 추가

				switch( $pass_mode ){
					case "3depth": 

						// 1차 입력
						if(!$arr_option_chk[$_option1]) {

							// 순번추출 - 1차
					        $r1 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='1' ");
					        $max_sort = $r1['max_sort'] + 1;

							_MQ_noreturn(" insert smart_product_addoption set pao_poptionname='". $_option1 ."', pao_poptionprice='0', pao_poptionpurprice='0', pao_cnt='0' , pao_depth='1', pao_pcode='". $pass_code ."', pao_sort='". $max_sort ."' ");
							$_uid1 = mysql_insert_id();
							$arr_option_chk[$_option1] ++;
						}
						// 2차 입력
						if(!$arr_option_chk[$_option1 . $_option_date . $_option2]) {

							// 순번추출 - 2차
					        $r2 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $_uid1 . "' ");
					        $max_sort2 = $r2['max_sort'] + 1;

							_MQ_noreturn(" insert smart_product_addoption set pao_poptionname='". $_option2 ."', pao_poptionprice='0', pao_poptionpurprice='0', pao_cnt='0' , pao_depth='2', pao_parent = '". $_uid1 ."', pao_pcode='". $pass_code ."', pao_sort='". $max_sort2 ."' ");
							$_uid2 = mysql_insert_id();
							$arr_option_chk[$_option1 . $_option_date . $_option2] ++;
						}

						// 순번추출 - 3차
				        $r3 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='3' and find_in_set('" . $_uid2 . "' , pao_parent) > 0 ");
				        $max_sort3 = $r3['max_sort'] + 1;

						// 3차 입력
						_MQ_noreturn(" insert smart_product_addoption set pao_poptionname='". $_option3 ."', pao_poptionprice='".$_price."' , pao_poptionpurprice='".$_supplyprice."', pao_cnt='".$_stock."', pao_depth='3', pao_parent = '". $_uid1 .",". $_uid2 ."', pao_pcode='". $pass_code ."', pao_sort='". $max_sort3 ."' ");
					break;

					case "2depth": 

						// 1차 입력
						if(!$arr_option_chk[$_option1]) {

							// 순번추출 - 1차
					        $r1 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='1' ");
					        $max_sort = $r1['max_sort'] + 1;

							_MQ_noreturn(" insert smart_product_addoption set  pao_poptionname='". $_option1 ."', pao_poptionprice='0', pao_poptionpurprice='0', pao_cnt='0' , pao_depth='1', pao_pcode='". $pass_code ."', pao_sort='". $max_sort ."' ");
							$_uid1 = mysql_insert_id();
							$arr_option_chk[$_option1] ++;
						}

						// 순번추출 - 2차
				        $r2 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $_uid1 . "' ");
				        $max_sort2 = $r2['max_sort'] + 1;

						// 2차 입력
						_MQ_noreturn(" insert smart_product_addoption set  pao_poptionname='". $_option2 ."', pao_poptionprice='".$_price."', pao_poptionpurprice='".$_supplyprice."', pao_cnt='".$_stock."', pao_depth='2', pao_parent = '". $_uid1 ."', pao_pcode='". $pass_code ."', pao_sort='". $max_sort2 ."' ");
					break;

					case "1depth": 

						// 순번추출
						$r1 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='1' ");
						$max_sort = $r1['max_sort'] + 1;

						// 1차 입력
						_MQ_noreturn(" insert smart_product_addoption set  pao_poptionname='". $_option1 ."', pao_poptionprice='".$_price."', pao_poptionpurprice='".$_supplyprice."', pao_cnt='".$_stock."', pao_depth='1', pao_pcode='". $pass_code ."', pao_sort='". $max_sort ."' ");
					break;
				}
			}
			# } 수정 또는 추가 처리
		}
		error_loc("_product_addoption.popup.php?pass_code=" . $pass_code);
	break;
	
	case 'down_excel':
		# code...
	break;
}