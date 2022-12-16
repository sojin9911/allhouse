<?PHP
// LMH007
	include "inc.php";

	//ViewArr($arr_policy);

	// 삭제된 항목 체크
	$arr_uid = array();
	if(sizeof($arr_policy) > 0){
		foreach($arr_policy as $k=>$v){
			// 타입체크 - S:하나의 항목만 가짐 , M:여러개 추가가능
			if($v['type'] == 'M'){

				if(sizeof($v['data']) > 0){
					foreach($v['data'] as $sk=>$sv){
						$_sque = "
							po_use = '". $v['po_use'] ."'
							,po_name = '". $k ."'
							,po_title = '". trim(($sv['po_title'])) ."'
							,po_content = '". ($sv['po_content']) ."'
						";
						// 신규추가, 업데이트 구분
						if($v['po_uid']>0){
							$que = " update smart_policy set {$_sque} where po_uid = '".$sv['po_uid']."' ";
							_MQ_noreturn($que);

							$arr_uid[] = $sv['po_uid'];
						}else{
							$que = " insert into smart_policy set {$_sque} ";
							_MQ_noreturn($que);

							$arr_uid[] = mysql_insert_id();
						}
					}
				}

			}else{

				$_sque = "
					po_use = '". $v['po_use'] ."'
					,po_name = '". $k ."'
					,po_title = '". trim(($v['po_title'])) ."'
					,po_content = '". ($v['po_content']) ."'
				";
				// 신규추가, 업데이트 구분
				if($v['po_uid']>0){
					$que = " update smart_policy set {$_sque} where po_uid = '".$v['po_uid']."' ";
					_MQ_noreturn($que);

					$arr_uid[] = $v['po_uid'];
				}else{
					$que = " insert into smart_policy set {$_sque} ";
					_MQ_noreturn($que);

					$arr_uid[] = mysql_insert_id();
				}

			}
		} // -- end foreach
	} // -- end if
	
	// 삭제된 항목 삭제
	_MQ_noreturn(" delete from smart_policy where po_uid not in ('". implode("','" , $arr_uid) ."')");

	error_loc_msg('_config.agree.form.php', '정상적으로 수정되었습니다.');
	exit;


?>