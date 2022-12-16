<?php 
		# 
	// 카테고리 정보에 대한 3단 select 배열을 위한 ajaxLDD010
	$app_mode = "popup";
	include_once("inc.php");

	// error_loc("_product_common_option.pop.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
	switch($_mode){

		// -- 선택적용
		case "selectApply":
		case "apply":		

			$dir = $_SERVER['DOCUMENT_ROOT']."/upfiles/option/";
			if($_mode == 'apply'){ $arrUid[] = $chkVar; } // 개별일경우

			if(count($arrUid) < 1){error_loc_msg("_product_common_option.pop.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),'선택된 공통옵션이 없습니다.'); }
			$res = _MQ_assoc("select *from smart_common_option_set where find_in_set(cos_uid, '".implode(",",$arrUid)."' ) > 0 ");
			if(count($res) < 1){error_loc_msg("_product_common_option.pop.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),'조회된 공통옵션이 없습니다.'); }

			$arrImage = array();
			foreach($res as $k=>$v){

				// -- 일반/추가 옵션에 따른 접두사 추가  // -- 공급가의 경우 일반/추가가 달라 $supplyprice 로 정의 --> 추후 변수가 너무 복잡하면 풀어도 됨...
				if($v['cos_type'] == 'addoption'){ $prefixFiled = 'pao'; $prefixTable = 'add'; $supplyprice = "_poptionpurprice"; }
				else{ $prefixFiled = 'po'; $prefixTable = ''; $supplyprice = "_poption_supplyprice"; }
				
				// -- 공통옵션설정에 등록된 옵션 추가 
				$resOption = _MQ_assoc("select *from smart_common_option where co_suid = '".$v['cos_uid']."'  order by co_sort asc ");
				$arrTempOption = array();
				foreach($resOption as $sk => $sv){

					// -- 컬러, 사이즈 추가 
					$colorOptionSque = "";
					if($v['cos_type']  == 'option'){
						$colorOptionSque = "  , ".$prefixFiled."_color_type = '".$sv['co_color_type']."'   ";

						$img_name = $sv['co_color_name'];

						// -- 이미지 별도 저장  ::: 이미지가 있을 경우에만 처리
						if($sv['co_color_type'] == 'img' && is_file($dir.$sv['co_color_name']) == true ){
							$ex_image_name = explode(".",$sv['co_color_name']); $app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); // 확장자
							$img_name = sprintf("%u" , crc32($sv['co_color_name'] . time())) . "." . $app_ext ;
							@copy($dir.$sv['co_color_name'] , $dir.$img_name);
						}

						$colorOptionSque .= " , ".$prefixFiled."_color_name = '".$img_name."'  ";
					}

					_MQ_noreturn("insert smart_product_".$prefixTable."option set ".$prefixFiled."_pcode = '".$pass_code."', ".$prefixFiled."_poptionname = '".$sv['co_poptionname']."',  ".$prefixFiled."_cnt = '".$sv['co_cnt']."', ".$prefixFiled."_poptionprice = '".$sv['co_poptionprice']."',  ".$prefixFiled.$supplyprice." = '".$sv['co_poption_supplyprice']."', ".$prefixFiled."_depth = '".$sv['co_depth']."', ".$prefixFiled."_parent = '".$sv['co_parent']."',  ".$prefixFiled."_view = '".$sv['co_view']."', ".$prefixFiled."_sort = '".$sv['co_sort']."',  ".$prefixFiled."_temp_cosuid = '".$v['cos_uid']."' ".$colorOptionSque."   ");


					$arrTempOption[$sv['co_uid']] = mysql_insert_id(); //  고유번호를 배열로 저장

				}

				// -- 부모를 찾아서 처리 :: 조건 추가 => 상품고유번호)
				$resApplyOption = _MQ_assoc("select *from smart_product_".$prefixTable."option where ".$prefixFiled."_temp_cosuid = '".$v['cos_uid']."' and ".$prefixFiled."_pcode = '".$pass_code."' and ".$prefixFiled."_depth > 1   order by ".$prefixFiled."_depth asc ");

			
				if( count($resApplyOption) > 0){ // 2차 또는 3차가 있을경우
					foreach($resApplyOption as $ak=>$av){
						$arrParent = array();
						if( $av[$prefixFiled.'_depth'] == 2){ // 2 차일경우
							$arrParent[] = $arrTempOption[$av[$prefixFiled.'_parent']]; // 고유번호를 가져온다.
						}else if(  $av[$prefixFiled.'_depth'] == 3 ){ // 3차일경우
							$expParent = explode(",",$av[$prefixFiled.'_parent']);
							$arrParent[] = $arrTempOption[$expParent[0]]; // 고유번호를 가져온다. 1차
							$arrParent[] = $arrTempOption[$expParent[1]]; // 고유번호를 가져온다. 2차
						}
						_MQ_noreturn("update smart_product_".$prefixTable."option set ".$prefixFiled."_parent = '".implode(",",$arrParent)."' where ".$prefixFiled."_temp_cosuid = '".$v['cos_uid']."'  and ".$prefixFiled."_uid = '".$av[$prefixFiled.'_uid']."' ");
					}
				}

				_MQ_noreturn("update smart_product_".$prefixTable."option set ".$prefixFiled."_temp_cosuid = '' where  ".$prefixFiled."_temp_cosuid = '".$v['cos_uid']."' ");				
			} // end foreach

			error_loc("_product_common_option.pop.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
		break;
	}

?>