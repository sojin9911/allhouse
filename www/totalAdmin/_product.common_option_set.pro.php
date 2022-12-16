<?php # 자주쓰는 옵션 처리프로세서
	include "./inc.php";

	// -- 추가/수정 시 공통 처리
	if( in_array($_mode, array('add','modify')) == true){
		$_name = nullchk($_name , '옵션관리명을 입력해 주세요.');

		if( $_type == 'addoption'){  
			$cos_option1_type = 'normal';
			$cos_option2_type = 'normal';
			$cos_option3_type = 'normal';
		}

		$que = " 
			cos_type = '".$_type."'
			, cos_name = '".addslashes($_name)."'
			, cos_depth = '".$_depth."'

			, cos_option1_type		= '" . $cos_option1_type  . "'
			, cos_option2_type		= '" . $cos_option2_type  . "'
			, cos_option3_type		= '" . $cos_option3_type  . "'

		"; 
	}

	// -- 모드별 처리 
	switch($_mode){
		case "add":

			_MQ_noreturn("insert smart_common_option_set set  ".$que." ,cos_rdate = now() "); // 자주쓰는옵션 추가
			$_uid = mysql_insert_id(); // 추가된 primary 키 
			error_loc("_product.common_option_set.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC."");

		break;

		case "modify":
			if( $_uid == ''){ error_msg("수정이 불가능합니다.");  }
			_MQ_noreturn("update smart_common_option_set set  ".$que."  where cos_uid = '".$_uid."' "); // 자주쓰는옵션 추가
			error_loc("_product.common_option_set.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC."");			
		break;

		// -- 하나삭제
		case "delete":
			$rowChk = _MQ("select count(*) as cnt from smart_common_option_set where cos_uid = '".$chkVar."' ");
			if($rowChk['cnt'] < 1){ error_msg("삭제할 공통옵션이 존재 하지 않습니다."); }

			_MQ_noreturn("delete from smart_common_option_set where cos_uid = '".$chkVar."' "); // 공통옵션 삭제
			_MQ_noreturn("delete from smart_common_option where co_suid = '".$chkVar."' "); // 등록된 옵션 삭제 
			error_loc("_product.common_option_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));

		break;

		// -- 선택처리
		case "selectCtrl":

			if(count($arrUid) < 1){error_loc_msg("_product.common_option_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),'선택된 공통옵션이 없습니다.'); }
			$res = _MQ_assoc("select *from smart_common_option_set where find_in_set(cos_uid, '".implode(",",$arrUid)."' ) > 0 ");
			if(count($res) < 1){error_loc_msg("_product.common_option_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),'조회된 공통옵션이 없습니다.'); }

			if($ctrlMode == 'copy'){ // 복사
				foreach($res as $k=>$v){
					
					// -- 공통옵션설정 추가
					$_name = '[복사]'.$v['cos_name'];
					_MQ_noreturn("insert smart_common_option_set set cos_name = '".$_name."' , cos_type = '".$v['cos_type']."' , cos_depth = '".$v['cos_depth']."' , cos_rdate = now()  ");
					$cos_uid = mysql_insert_id();

					// -- 공통옵션설정에 등록된 옵션 추가 
					$resOption = _MQ_assoc("select *from smart_common_option where co_suid = '".$v['cos_uid']."'  order by co_sort asc ");
					$arrTempOption = array();
					foreach($resOption as $sk => $sv){
						_MQ_noreturn("insert smart_common_option set co_suid = '".$cos_uid."', co_poptionname = '".$sv['co_poptionname']."',  co_cnt = '".$sv['co_cnt']."', co_poptionprice = '".$sv['co_poptionprice']."',  co_poption_supplyprice = '".$sv['co_poption_supplyprice']."', co_depth = '".$sv['co_depth']."', co_parent = '".$sv['co_parent']."',  co_view = '".$sv['co_view']."', co_sort = '".$sv['co_sort']."', co_rdate = now()  ");
						$arrTempOption[$sv['co_uid']] = mysql_insert_id(); //  고유번호를 배열로 저장
					}
					// -- 부모를 찾아서 처리
					$resCopyOption = _MQ_assoc("select *from smart_common_option where co_suid = '".$cos_uid."' and co_depth > 1   order by co_depth asc ");
					if( count($resCopyOption) > 0){ // 2차 또는 3차가 있을경우
						foreach($resCopyOption as $ck=>$cv){
							$arrParent = array();
							if( $cv['co_depth'] == 2){ // 2 차일경우
								$arrParent[] = $arrTempOption[$cv['co_parent']]; // 고유번호를 가져온다.
							}else if(  $cv['co_depth'] == 3 ){ // 3차일경우
								$expParent = explode(",",$cv['co_parent']);
								$arrParent[] = $arrTempOption[$expParent[0]]; // 고유번호를 가져온다. 1차
								$arrParent[] = $arrTempOption[$expParent[1]]; // 고유번호를 가져온다. 2차
							}
							_MQ_noreturn("update smart_common_option set co_parent = '".implode(",",$arrParent)."' where co_suid = '".$cos_uid."' and co_uid = '".$cv['co_uid']."' ");

						}
					}
				}
			}else if($ctrlMode == 'delete'){ // 삭제
				_MQ_noreturn("delete from smart_common_option_set where find_in_set(cos_uid,'".implode(",",$arrUid)."') > 0 "); // 공통옵션 삭제
				_MQ_noreturn("delete from smart_common_option where find_in_set(co_suid,'".implode(",",$arrUid)."') > 0 "); // 등록된 옵션 삭제 				
			}else{
				error_loc("_product.common_option_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null),'처리에 실패하였습니다.');
			}

			error_loc("_product.common_option_set.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
		break;

	}

?>