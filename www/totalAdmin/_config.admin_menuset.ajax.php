<?php // -- LCY :: ADMIN -- 운영자별 메뉴관리 AJAX 처리
	include "./inc.php";

	switch($ajaxMode){

		// -- 운영자별 메뉴리스트 로드
		case "adminMenuSetList":

			echo json_encode(array('rst'=>'success'));
			exit;

		break;

		// -- Admin 메뉴 리스트 
		case "adminMenuList":

			// -- 전체일 시
			$sque = "";
			if($sval != 'all'){ $sque = " and am_uid = '".$sval."' "; }
			$resAdminMenu = _MQ_assoc("select * from smart_admin_menu where am_depth = '1' and am_view = 'Y' ".($sval != 'all' ? " and am_uid = '".$sval."'  ":"" )." order by  am_idx asc ");

			$arrAdminMenuSet = array();
			$resAdminMenuSet = _MQ_assoc("select *from smart_admin_menu_set where ams_auid = '".$adminUid."' ");
			if( count($resAdminMenuSet) > 0){ foreach($resAdminMenuSet as $k=>$v){ $arrAdminMenuSet[$v['ams_amuid']] = $v; } }

			$printAdminMenuList = "";
			foreach($resAdminMenu as $k=>$v){
					$v['ams_uid'] = $arrAdminMenuSet[$v['am_uid']]['ams_uid'];
					$v['ams_amuid'] = $arrAdminMenuSet[$v['am_uid']]['ams_amuid'];
					$v['ams_auid'] = $arrAdminMenuSet[$v['am_uid']]['ams_auid'];

					// <span class="c_tag blue h18 blue line">노출</span>
					$btnView = '';
					if($v['ams_uid'] != ''){ $btnView = '<span class="c_tag blue h18 blue line">노출</span>';  }
					else{ $btnView = '<span class="c_tag gray h18 gray">숨김</span>';  }

					$printAdminMenuList .= '<tr class="amdin-menu-list" data-depth="1" data-uid="'.$v['am_uid'].'">';
					$printAdminMenuList .= '	<td class=""><label class="design"><input type="checkbox" name="adminMenu[]" value="'.$v['am_uid'].'" class="js_ck"></label></td>';
					$printAdminMenuList .= '	<td class=""><div class="lineup-vertical">'.$btnView.'</div></td>';
					$printAdminMenuList .= '	<td class="t_left">'.$v['am_name'].'</td>';
					$printAdminMenuList .= '	<td>';
					$printAdminMenuList .= '		<div class="lineup-center">';
					$printAdminMenuList .= '			<select name="adminMenuConfig['.$v['am_uid'].']" class="select-admin-view-single" data-depth="1" data-uid="'.$v['am_uid'].'">';
					$printAdminMenuList .= '				<option '.($v['ams_uid'] != '' ? 'selected':'').' value="Y">노출</option>';
					$printAdminMenuList .= '				<option '.($v['ams_uid'] == '' ? 'selected':'').' value="N">숨김</option>';
					$printAdminMenuList .= '			</select>';
					$printAdminMenuList .= '			<a href="#none" onclick="return false;" class="c_btn h28 black btn-admin-view-single" data-depth="1" data-uid="'.$v['am_uid'].'">바로적용</a>';
					$printAdminMenuList .= '		</div>';
					$printAdminMenuList .= '	</td>';
					$printAdminMenuList .= '</tr>';

					$resAdminMenuDepth2 = _MQ_assoc("select *from smart_admin_menu  where  am_depth = '2' and am_view = 'Y' and am_parent = '".$v['am_uid']."' order by  am_idx asc ");
					foreach($resAdminMenuDepth2 as $k2=>$v2){
							$v2['ams_uid'] = $arrAdminMenuSet[$v2['am_uid']]['ams_uid'];
							$v2['ams_amuid'] = $arrAdminMenuSet[$v2['am_uid']]['ams_amuid'];
							$v2['ams_auid'] = $arrAdminMenuSet[$v2['am_uid']]['ams_auid'];
							$btnView = '';
							if($v2['ams_uid'] != ''){ $btnView = '<span class="c_tag blue h18 blue line">노출</span>';  }
							else{ $btnView = '<span class="c_tag gray h18 gray">숨김</span>';  }

							// -- 하단메뉴 :: 상위메뉴 처리
							$disabledChk = $v['ams_uid'] == '' ? ' disabled = "disabled" ':'';

							$printAdminMenuList .= '<tr class="amdin-menu-list" data-depth="2" data-uid="'.$v2['am_uid'].'">';
							$printAdminMenuList .= '	<td class=""><label class="design"><input type="checkbox" name="adminMenu[]" value="'.$v2['am_uid'].'" class="js_ck"></label></td>';
							$printAdminMenuList .= '	<td class=""><div class="lineup-vertical">'.$btnView.'</div></td>';
							$printAdminMenuList .= '	<td class="t_left">'.$v['am_name'].'>'.$v2['am_name'].'</td>';
							$printAdminMenuList .= '	<td>';
							$printAdminMenuList .= '		<div class="lineup-center">';
							$printAdminMenuList .= '			<select name="adminMenuConfig['.$v2['am_uid'].']" class="select-admin-view-single" data-depth="2" '.$disabledChk.' data-uid="'.$v2['am_uid'].'">';
								$printAdminMenuList .= '				<option '.($v2['ams_uid'] != '' ? 'selected':'').' value="Y">노출</option>';
								$printAdminMenuList .= '				<option '.($v2['ams_uid'] == '' ? 'selected':'').' value="N">숨김</option>';
							$printAdminMenuList .= '			</select>';
							$printAdminMenuList .= '			<a href="#none" onclick="return false;" class="c_btn h28 black btn-admin-view-single" data-depth="2"  data-uid="'.$v2['am_uid'].'">바로적용</a>';
							$printAdminMenuList .= '		</div>';
							$printAdminMenuList .= '	</td>';
							$printAdminMenuList .= '</tr>';

						$resAdminMenuDepth3 = _MQ_assoc("select *from smart_admin_menu where  am_depth = '3' and am_view = 'Y' and find_in_set('".$v2['am_uid']."',am_parent) > 0  order by  am_idx asc ");
						foreach($resAdminMenuDepth3 as $k3=>$v3){
								$v3['ams_uid'] = $arrAdminMenuSet[$v3['am_uid']]['ams_uid'];
								$v3['ams_amuid'] = $arrAdminMenuSet[$v3['am_uid']]['ams_amuid'];
								$v3['ams_auid'] = $arrAdminMenuSet[$v3['am_uid']]['ams_auid'];
								$btnView = '';
								if($v3['ams_uid'] != ''){ $btnView = '<span class="c_tag blue h18 blue line">노출</span>';  }
								else{ $btnView = '<span class="c_tag gray h18 gray">숨김</span>';  }

								// -- 하단메뉴 :: 상위메뉴 처리
								$disabledChk = $v['ams_uid'] == '' || $v2['ams_uid'] == '' ? ' disabled = "disabled" ':'';

								$printAdminMenuList .= '<tr class="amdin-menu-list" data-depth="3" data-uid="'.$v3['am_uid'].'">';
								$printAdminMenuList .= '	<td class=""><label class="design"><input type="checkbox" name="adminMenu[]" value="'.$v3['am_uid'].'" class="js_ck"></label></td>';
								$printAdminMenuList .= '	<td class=""><div class="lineup-vertical">'.$btnView.'</div></td>';
								$printAdminMenuList .= '	<td class="t_left">'.$v['am_name'].'>'.$v2['am_name'].' > '.$v3['am_name'].'</td>';
								$printAdminMenuList .= '	<td>';
								$printAdminMenuList .= '		<div class="lineup-center">';
								$printAdminMenuList .= '			<select name="adminMenuConfig['.$v3['am_uid'].']" class="select-admin-view-single" data-depth="3" '.$disabledChk.' data-uid="'.$v3['am_uid'].'">';
								$printAdminMenuList .= '				<option '.($v3['ams_uid'] != '' ? 'selected':'').' value="Y">노출</option>';
								$printAdminMenuList .= '				<option '.($v3['ams_uid'] == '' ? 'selected':'').' value="N">숨김</option>';
								$printAdminMenuList .= '			</select>';
								$printAdminMenuList .= '			<a href="#none" onclick="return false;" class="c_btn h28 black btn-admin-view-single" data-depth="3"  data-uid="'.$v3['am_uid'].'">바로적용</a>';
								$printAdminMenuList .= '		</div>';
								$printAdminMenuList .= '	</td>';
								$printAdminMenuList .= '</tr>';
						}
					}
			}

			echo $printAdminMenuList;
		break;

		// -- 메뉴 개별선택
		case "adminMenuSelectSingle":
			$row = _MQ("select *from smart_admin_menu where am_uid = '".$adminMenuUid."'");
			if( count($row) < 1){ echo json_encode(array("rst"=>'fail')); exit; }
			// -- 각 차수별 처리
			if($row['am_depth'] == '1'){
				// -- 1뎁스일경우 하위의 모든 카테고리를 숨김처리로 변경해야한다. 
				$resChk = _MQ_assoc("select am_uid from smart_admin_menu where  find_in_set('".$row['am_uid']."', am_parent) > 0 and am_view = 'Y'   ");
				if( count($resChk) < 1){ echo json_encode(array("rst"=>'success', 'depth'=>$row['am_depth'], 'data'=>$resChk)); exit; }
					echo json_encode(array("rst"=>'success', 'depth'=>$row['am_depth'], 'data'=>$resChk)); exit;
			}
			else if($row['am_depth'] == '2'){
				// -- 2뎁스일경우 하위의 모든 카테고리를 숨김처리로 변경해야한다. 
				$resChk = _MQ_assoc("select am_uid from smart_admin_menu where  am_depth = '3' and find_in_set('".$row['am_uid']."', am_parent) > 0 and am_view = 'Y'   ");
				if( count($resChk) < 1){ echo json_encode(array("rst"=>'success', 'depth'=>$row['am_depth'], 'data'=>$resChk)); exit; }
				echo json_encode(array("rst"=>'success', 'depth'=>$row['am_depth'], 'data'=>$resChk)); exit;
			}
			else if($row['am_depth'] == '3'){ echo json_encode(array("rst"=>'success', 'depth'=>$row['am_depth'], 'data'=>false)); exit; }
			else{ echo json_encode(array("rst"=>'fail')); exit; }

		break;

		// -- 선택 숨김/노출 처리
		case "selectAdminMenuSet":

			if( in_array($chkValue, array('Y','N')) == false){ echo json_encode(array('rst'=>'fail','msg'=>'처리방식이 올바르지 않습니다.')); exit; }

			// -- 변수로 해석
			if($selectVar != '' ) parse_str($selectVar);

			if( count($adminMenu) < 1){ echo json_encode(array('rst'=>'fail','msg'=>$chkValueName.' 처리하실 메뉴를 선택해 주세요.')); exit; }

			// -- 데이터 조회
			$resAdminMenu = _MQ_assoc("select *from smart_admin_menu where find_in_set(am_uid , '".implode(",",$adminMenu)."' ) > 0 ");
			if( count($resAdminMenu) < 1){ echo json_encode(array('rst'=>'fail','msg'=>'처리할 수 있는 메뉴가 존재하지 않습니다.')); exit; }

			$arrExecHideUid = $arrExecUid  =  array(); // 처리할 관리자 고유번호 생성
			foreach($resAdminMenu as $k=>$v){
				// -- 노출이라면
				if( $chkValue == 'Y'){
					$arrExecUid[] = $v['am_uid'];
				}else{
					$arrExecHideUid[] = $v['am_uid'];
				}	

				// -- 3뎁스일경우 부모 판별


				// -- 1뎁스이고 숨김처리라면 기존에등록된 데이터삭제를 위해 저장
				if($v['am_depth'] == '1' && $chkValue == 'N'){
					$resAdminMenuChild = _MQ_assoc("select am_uid from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."' and ams.ams_uid is not null and find_in_set('".$v['am_uid']."',am_parent) > 0  ");
					foreach( $resAdminMenuChild as $sk=>$sv){
						$arrExecHideUid[] = $sv['am_uid'];
					}

					continue;
				}

				// -- 2뎁스이고 숨김처리라면 하위 메뉴도 숨김처리
				if( $v['am_depth'] == '2' && $chkValue == 'N'){
					$resAdminMenuChild = _MQ_assoc("select am_uid from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."'  and ams.ams_uid is not null and am_depth = '3' and find_in_set('".$v['am_uid']."',am_parent) > 0  ");
					foreach( $resAdminMenuChild as $sk=>$sv){
						$arrExecHideUid[] = $sv['am_uid'];
					}					
					continue;
				}

				// -- 3뎁스라면 기본처리
				if( $v['am_depth'] == '3' && $chkValue == 'N'){
					$arrExecHideUid[] = $v['am_uid'];
					continue;
				}
			}

			// -- 중복값 제거
			$arrExecHideUid = array_unique($arrExecHideUid);
			$arrExecUid = array_unique($arrExecUid);

			// -- 숨김처리 시 자식메뉴 전부 숨김처리
			if( count($arrExecHideUid) > 0 ){
				_MQ_noreturn("delete from smart_admin_menu_set where find_in_set(ams_amuid,'".implode(",",$arrExecHideUid)."') > 0 and ams_auid = '".$adminUid."'  ");
			}


			// -- 노출일시 시 
			if( count($arrExecUid) > 0){
				foreach($arrExecUid as $adminMenuUid){ 
					_MQ_noreturn("insert into smart_admin_menu_set (ams_amuid,ams_auid) values ('".$adminMenuUid."','".$adminUid."') on duplicate key update ams_amuid = '".$adminMenuUid."', ams_auid = '".$adminUid."'  ");
				}
			}

			if( count($arrExecUid) > 0){
				$arrReturnUid = $arrViewParent =  array(); // 원상복구 되는 uid , $arrViewParent  : 부모가 없을 경우 강제로 생성
				$resView = _MQ_assoc("select am_uid, am_depth, am_parent from smart_admin_menu where am_view = 'Y' and find_in_set(am_uid, '".implode(",",$arrExecUid)."') > 0  ");
				foreach( $resView as $k=>$v){
					if( in_array($v['am_depth'], array(1,2)) == true){
						// -- 3depth 가 있는지 체크
						$rowChk = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."' and ams.ams_uid is not null and am_depth = '3' and find_in_set('".$v['am_uid']."',am_parent) > 0  ");
						if( $rowChk['cnt'] < 1){  $arrReturnUid[] = $v['am_uid'];}
					}else{

						// -- 2뎁스 체크
						$rowChk = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."' and ams.ams_uid is not null and am_depth = '2' and find_in_set(am_uid,'".$v['am_parent']."') > 0  ");	
						
						if( $rowChk['cnt'] < 1){

							$arrParent = explode(",",$v['am_parent']); 
							$arrViewParent[] = $arrParent[0]; 
							$arrViewParent[] = $arrParent[1]; 
						} // 오차피 2뎁스가 없다면 1뎁스의 경우 체크할 필요가 없다		

					}
				}

				// -- 다시 복구한다.
				if( count($arrReturnUid) > 0 ){
					_MQ_noreturn("delete from smart_admin_menu_set where find_in_set(ams_amuid,'".implode(",",$arrReturnUid)."') > 0 and ams_auid = '".$adminUid."'  ");
					echo json_encode(array('rst'=>'fail','msg'=>'하위메뉴는 최소 1개이상 노출로 설정하셔야 적용가능합니다.'));exit;
				}

				// -- 3뎁스중 노출일경우
				if( count($arrViewParent) > 0){
					foreach( $arrViewParent as $k=>$v){
						_MQ_noreturn("insert into smart_admin_menu_set (ams_amuid,ams_auid) values ('".$v."','".$adminUid."') on duplicate key update ams_amuid = '".$v."', ams_auid = '".$adminUid."'  ");
					}
				}
			}


			echo json_encode(array('rst'=>'success', 'data'=>count($arrExecUid), 'data2'=>$arrExecHideUid,'data3'=>$adminUid )); exit;

		break;


		// -- 관리자메뉴 바로적용
		case "submitAdminMenuSet":

			// -- 변수로 해석
			if($selectVar != '' ) parse_str($selectVar);			

			if( count($adminMenuConfig) < 1){ echo json_encode(array('rst'=>'fail')); exit; }

			$arrExecUid = $arrExecHideUid = $arrChkChild =  array();
			foreach($adminMenuConfig as $adminMenuUid=>$chkValue){

				// -- 데이터 조회
				$rowAdminMenu = _MQ("select *from smart_admin_menu where am_uid = '".$adminMenuUid."'  ");

				// -- 노출이라면
				if( $chkValue == 'Y'){
					$arrExecUid[] = $rowAdminMenu['am_uid'];

				}else{
					$arrExecHideUid[] = $rowAdminMenu['am_uid'];
				}
				
				// -- 1뎁스이고 숨김처리라면 기존에등록된 데이터삭제를 위해 저장
				if($rowAdminMenu['am_depth'] == '1' && $chkValue == 'N'){
					$resAdminMenuChild = _MQ_assoc("select am_uid from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."' and ams.ams_uid is not null and find_in_set('".$rowAdminMenu['am_uid']."',am_parent) > 0  ");
					foreach( $resAdminMenuChild as $sk=>$sv){
						$arrExecHideUid[] = $sv['am_uid'];
					}

					continue;
				}

				// -- 2뎁스이고 숨김처리라면 하위 메뉴도 숨김처리
				if( $rowAdminMenu['am_depth'] == '2' && $chkValue == 'N'){
					$resAdminMenuChild = _MQ_assoc("select am_uid from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."'  and ams.ams_uid is not null and am_depth = '3' and find_in_set('".$rowAdminMenu['am_uid']."',am_parent) > 0  ");
					foreach( $resAdminMenuChild as $sk=>$sv){
						$arrExecHideUid[] = $sv['am_uid'];
					}					
					continue;
				}

				// -- 3뎁스라면 기본처리
				if( $rowAdminMenu['am_depth'] == '3' && $chkValue == 'N'){
					$arrExecHideUid[] = $rowAdminMenu['am_uid'];
					continue;
				}

			}

			// -- 중복값 제거
			$arrExecHideUid = array_unique($arrExecHideUid);
			$arrExecUid = array_unique($arrExecUid);

			// -- 숨김처리 시 자식메뉴 전부 숨김처리
			if( count($arrExecHideUid) > 0 ){
				_MQ_noreturn("delete from smart_admin_menu_set where find_in_set(ams_amuid,'".implode(",",$arrExecHideUid)."') > 0 and ams_auid = '".$adminUid."'  ");
			}

			// -- 노출일시 시 
			if( count($arrExecUid) > 0){
				foreach($arrExecUid as $adminMenuUid){ 
					_MQ_noreturn("insert into smart_admin_menu_set (ams_amuid,ams_auid) values ('".$adminMenuUid."','".$adminUid."') on duplicate key update ams_amuid = '".$adminMenuUid."', ams_auid = '".$adminUid."'  ");
				}
			}

			if( count($arrExecUid) > 0){
				$arrReturnUid = $arrViewParent =  array(); // 원상복구 되는 uid , $arrViewParent  : 부모가 없을 경우 강제로 생성
				$resView = _MQ_assoc("select am_uid, am_depth, am_parent from smart_admin_menu where am_view = 'Y' and find_in_set(am_uid, '".implode(",",$arrExecUid)."') > 0  ");
				foreach( $resView as $k=>$v){
					if( in_array($v['am_depth'], array(1,2)) == true){
						// -- 3depth 가 있는지 체크
						$rowChk = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."' and ams.ams_uid is not null and am_depth = '3' and find_in_set('".$v['am_uid']."',am_parent) > 0  ");
						if( $rowChk['cnt'] < 1){  $arrReturnUid[] = $v['am_uid'];}
					}else{

						// -- 2뎁스 체크
						$rowChk = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where 1 and ams.ams_auid = '".$adminUid."' and ams.ams_uid is not null and am_depth = '2' and find_in_set(am_uid,'".$v['am_parent']."') > 0  ");	
						
						if( $rowChk['cnt'] < 1){

							$arrParent = explode(",",$v['am_parent']); 
							$arrViewParent[] = $arrParent[0]; 
							$arrViewParent[] = $arrParent[1]; 
						} // 오차피 2뎁스가 없다면 1뎁스의 경우 체크할 필요가 없다		

					}
				}

				// -- 다시 복구한다.
				if( count($arrReturnUid) > 0 ){
					_MQ_noreturn("delete from smart_admin_menu_set where find_in_set(ams_amuid,'".implode(",",$arrReturnUid)."') > 0 and ams_auid = '".$adminUid."'  ");
					echo json_encode(array('rst'=>'fail','msg'=>'하위메뉴는 최소 1개이상 노출로 설정하셔야 적용가능합니다.'));exit;
				}

				// -- 3뎁스중 노출일경우
				if( count($arrViewParent) > 0){
					foreach( $arrViewParent as $k=>$v){
						_MQ_noreturn("insert into smart_admin_menu_set (ams_amuid,ams_auid) values ('".$v."','".$adminUid."') on duplicate key update ams_amuid = '".$v."', ams_auid = '".$adminUid."'  ");
					}
				}
			}


			echo json_encode(array('rst'=>'success', 'data'=>array_keys($adminMenuConfig) )); exit;
			

		break;
	}
?>