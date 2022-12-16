<?php
	include_once("inc.php");

	if($_mode == ''){ echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

	// -- 공통쿼리문 실행
	if( in_array($_mode,array("add","modify")) == true){

		if( isset($_uid)){
			$row = _MQ("select *from smart_display_main_set where dms_uid = '".$_uid."' ");
		}

		if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'분류명을 입력해 주세요.','key'=>'_name')); exit;}
		if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view')); exit;}

		$sque = "
			dms_view													= '".$_view."'
			, dms_name												='".addslashes($_name)."'
		";

		// -- 2차만 가능한 아이템들
		if( $_depth != 1) {
			$sque .= "
				,	dms_list_product_view													= '".$_list_product_view."'
				,	dms_list_product_mobile_view													= '".$_list_product_mobile_view."'
				,	dms_list_product_display													= '".$_list_product_display."'
				,	dms_list_product_mobile_display													= '".$_list_product_mobile_display."'
			";
		}
	}

	switch($_mode){

		// -- 수정
		case "modify":
			// -- 해당 메뉴의 정보를 가져온다.
			if(count($row) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			_MQ_noreturn("update smart_display_main_set set
				".$sque."
				where dms_uid = '".$_uid."'  ");

			echo json_encode(array('rst'=>'success','msg'=>'저장되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 추가
		case "add":

			if($_depth == 1){
				echo json_encode(array('rst'=>'fail','msg'=>'1차분류는 추가가 불가능합니다.')); exit;
			}

			$_parent = 0;
			$_parent = $locUid1;
			// -- 부모의 정보를 가져온다.
			$rowParent = _MQ("select *from smart_display_main_set where dms_uid = '".$_parent."' ");

			// -- 부모 정보가 없다면 무조건 오류처리
			if(count($rowParent) < 1){ echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit; }


			// -- 분류 순서 초기화
			$rowIdx = _MQ("select ifnull(max(dms_idx),0) + 1 as max_idx from smart_display_main_set where dms_depth = '".$_depth."' ".($_depth > 1 ? "and dms_parent = '".$_parent."' ":""));
			_MQ_noreturn("insert smart_display_main_set set
				".$sque."
				, dms_depth		= '".$_depth."'
				, dms_parent	= '".$_parent."'
				, dms_idx			= '".$rowIdx['max_idx']."'
				, dms_type		= '".$rowParent['dms_type']."'


			");

			echo json_encode(array('rst'=>'success','msg'=>'분류가 추가되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 삭제
		case "delete":

			if($_depth == 1){ echo json_encode(array('rst'=>'fail','msg'=>'1차 분류는 삭제할 수 없습니다.')); exit; }

			// -- 해당  정보를 가져온다.
			$row = _MQ("select *from smart_display_main_set where dms_uid = '".$_uid."' ");
			if(count($row) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			// 해당  부모로 가진 분류 있을 경우 삭제 불가
			$rowChk = _MQ("select count(*) as cnt from smart_display_main_set where find_in_set('".$_uid."' , dms_parent) > 0 ");
			if( $rowChk['cnt'] > 0 ) {
				error_msgPopup_s("하위 분류가 있어 삭제할 수 없습니다.");
			}


			_MQ_noreturn("delete from smart_display_main_set where dms_uid = '".$_uid."' or find_in_set('".$_uid."',dms_parent) > 0 ");
			echo json_encode(array('rst'=>'success','msg'=>'분류가 삭제되었습니다.','_depth'=>$_depth)); exit;
		break;

		// -- 순서변경
		case "idx":

			if( $_type == 'up'){
		    // 정보 불러오기
		    $que  = " SELECT dms_idx , dms_depth , dms_parent FROM smart_display_main_set WHERE dms_uid = '$_uid' ";
		    $r = _MQ($que);

		    $_idx = $r[dms_idx];
		    $_parent = $r[dms_parent];
		    $_depth = $r[dms_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_display_main_set WHERE dms_idx = '$_idx' and dms_parent='$_parent' ";
		        $r = _MQ($que);
		        if($r[cnt] > 1) {
		      _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = dms_idx+1 WHERE dms_idx >= '$_idx' and dms_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MIN(dms_idx),0) as mindms_idx FROM smart_display_main_set WHERE dms_parent='$_parent' ";
		    $r = _MQ($que);
		    $mindms_idx = $r[mindms_idx];

		    if ($mindms_idx == $_idx) {
		       echo json_encode(array('rst'=>'fail','msg'=>'더이상 상위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계위 데이터와 dms_idx 값 바꿈
		        $sque = "select dms_idx , dms_uid from smart_display_main_set WHERE dms_parent='$_parent' and dms_idx < '$_idx' order by dms_idx desc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $_idx WHERE dms_uid='$sr[dms_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $sr[dms_idx] WHERE dms_uid = '$_uid' ");

		    }
		  }else if($_type == 'down'){

		    // 정보 불러오기
		    $que  = " SELECT dms_idx , dms_depth , dms_parent FROM smart_display_main_set WHERE dms_uid = '$_uid' ";
		    $r = _MQ($que);
		    $_idx = $r[dms_idx];
		    $_parent = $r[dms_parent];
		    $_depth = $r[dms_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_display_main_set WHERE dms_idx = '$_idx' and dms_parent='$_parent' ";
		        $r = _MQ($que);

		        if($r[cnt] > 1) {
		      		_MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = dms_idx-1 WHERE dms_idx <= '$_idx' and dms_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MAX(dms_idx),0) as maxdms_idx FROM smart_display_main_set WHERE dms_parent='$_parent' ";
		    $r = _MQ($que);
		    $maxdms_idx = $r[maxdms_idx];

		    if ($maxdms_idx == $_idx) {
		    	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계 아래 데이터와 dms_idx 값 바꿈
		        $sque = "select dms_idx , dms_uid from smart_display_main_set WHERE 1 and dms_idx > '$_idx' ".($_depth != 1 ? " and dms_parent='$_parent'  ":"" )." order by dms_idx asc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $_idx WHERE dms_uid='$sr[dms_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $sr[dms_idx] WHERE dms_uid = '$_uid' ");
		        //	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }

		  }else{
		  	echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit;
		  }

			echo json_encode(array('rst'=>'success')); exit;


		break;

		// -- 선택된 상품 리스트 목록 가져오기
		case "selectMainProductList":

			// -- 분류 정보를 가져온다.
			$row = _MQ("select *from smart_display_main_set where dms_uid = '".$_uid."' ");

			$viewMain = $row['dms_list_product_view'] == 'Y' ? '노출':'숨김';
			$viewMainMobile = $row['dms_list_product_mobile_view'] == 'Y' ? '노출':'숨김';

			$sque = "smart_display_main_product as dmp inner join smart_product as p on(p.p_code = dmp.dmp_pcode ) where dmp_dmsuid = '".$_uid."'";
			$arr_customer = arr_company2(); // 공급업체정보를 가져온다.
			$listmaxcount = 10;
			$listpg = rm_str($ahref);
			if(!rm_str($listpg)) $listpg = 1;
			$count = $listpg * $listmaxcount - $listmaxcount;
			$row = _MQ("select count(*) as cnt from ".$sque);
			$TotalCount = $row['cnt'];
			$st = 'dmp_idx';
			$so = 'asc';
			$Page = ceil($TotalCount/$listmaxcount);
			$res = _MQ_assoc("select * from  ".$sque." order by ".$st." ".$so." limit ".$count.", ".$listmaxcount." ");
			$printList = '';

			foreach($res as $k=>$v){
				$_num = $TotalCount-$count-$k;
				$_num = number_format($_num);
				$stockStatus = $v['p_stock'] > 0 ? '정상':'품절'; // 품절상태표시
				$pname = addslashes(strip_tags($v['p_name']));

				$comInfoLink  = "_entershop.form.php?_mode=modify&_id=".$v['p_cpid'];


				// 이미지 체크
				$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
				//if(is_file('../upfiles/product/'.'thumbs_s_'.$v['p_img_list_square']) == false) $_p_img = 'images/thumb_no.jpg';
				if($_p_img == '') $_p_img = 'images/thumb_no.jpg'; // SSJ : 썸네일 체크 변경 : 2021-02-17

				$plink = '/?pn=product.view&pcode='.$v['p_code'].'';


				// -- 리스트
				$printList .= '<tr>';
				$printList .= '	<td><label class="design"><input type="checkbox" class="js_ck main-pcode" value="'.$v['p_code'].'" name="chk_pcode[]"></label></td>';
				$printList .= '	<td>'.$_num.'</td>';
				$printList .= '	<td>
										<div class="lineup-center" style="margin-bottom:5px;">
											<input type="text" name="sort_group[\''.$v['p_code'].'\']" value="'.$v['dmp_sort_group'].'" class="design number_style sort_group_'.$v['p_code'].'" placeholder="" style="width:45px;margin-right:0;">
											<a href="#none" onclick="sort_group(\''.$v['p_code'].'\',\''.$_uid.'\')" class="c_btn h27 " style="width:45px;">수정</a>
										</div>
										<div class="lineup-center">
											<a href="#none" onclick="sort_up(\''.$v['p_code'].'\' ,\'up\',\''.$_uid.'\')" class="c_btn h22 icon_up" title="위로"></a>
											<a href="#none" onclick="sort_up(\''.$v['p_code'].'\' ,\'down\',\''.$_uid.'\')" class="c_btn h22 icon_down" title="아래로"></a>
											<a href="#none" onclick="sort_up(\''.$v['p_code'].'\' ,\'top\',\''.$_uid.'\')" class="c_btn h22 icon_top" title="맨위로"></a>
											<a href="#none" onclick="sort_up(\''.$v['p_code'].'\' ,\'bottom\',\''.$_uid.'\')" class="c_btn h22 icon_bottom" title="맨아래로"></a>
										</div>
									</td>';
				$printList .= '	<td class="img50"><a href="'.$plink.'" target="_blank" title="'.$pname.'"><img src="'.$_p_img.'" alt="'.$pname.'" /></a></td>';
				$printList .= '	<td class="t_left ctg_name"><a href="'.$plink.'" target="_blank">'.$pname.'</a></td>';
				$printList .= '	<td class="t_black">'.number_format($v['p_price']).'원</td>';
				$printList .= '	<td>'.$stockStatus.'</td>';

				if( $SubAdminMode === true ) {
					$printList .= '	<td><a href="'.$comInfoLink.'" target="_blank">'.$arr_customer[$v['p_cpid']].'</a></td>';
				}else{
					$printList .= '	<td>'.$arr_customer[$v['p_cpid']].'</td>';
				}

				$printList .= '	<td><div class="lineup-center">'.$arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')].'</div></td>';
				$printList .= '	</tr>';
			}

			$printPaginate .= '
				'.pagelisting($listpg, $Page, $listmaxcount, "?{$_PVS}&listpg=", 'Y').'
			';

			echo json_encode(array('rst'=>'success','cnt'=>count($res),'printPaginate'=>$printPaginate,'printList'=>$printList));
			exit;

		break;


		// -- 선택된 아이템 삭제
		case "selectMainProductDelete":

			main_product_resort($_uid);

			if( rm_str($_uid) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'분류 추가 후 선택 가능합니다.')); exit; }
			// -- 변수로 해석 :: 선택된 아이팀을 변수로 해석
			if($selectVar != '' ) parse_str($selectVar);

			if( count($chk_pcode) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택된 값이 없습니다.')); exit; }
			_MQ_noreturn("delete from smart_display_main_product where dmp_dmsuid = '".$_uid."' and find_in_set(dmp_pcode, '".implode(",",array_values($chk_pcode))."' ) > 0    ");
			echo json_encode(array('rst'=>'success','msg'=>'선택된 상품이 삭제되었습니다.')); exit;
		break;

		// -- 선택된 아이템 추가
		case "selectMainProductAdd":

			if( rm_str($_uid) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택적용이 불가능합니다.')); exit; }
			if($selectVar != '' ) parse_str($selectVar);
			if( count($chk_pcode) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택된 값이 없습니다.')); exit; }
			$que = " select p_code from smart_product where p_code in ('". implode("','", $chk_pcode) ."') order by p_idx desc ";
			$pres = _MQ_assoc($que);
			foreach($pres as $k=>$v){
				$pcode = $v['p_code'];
				// -- 중복의 경우 업데이트 처리 :: duplicate
				_MQ_noreturn("
					insert into smart_display_main_product 
						(dmp_pcode,dmp_dmsuid,dmp_idx,dmp_sort_idx,dmp_sort_group ) 
					values 
						('".$pcode."','".$_uid."','0.5','0.5','100')
					on duplicate key 
					update dmp_pcode = '".$pcode."', dmp_dmsuid = '".$_uid."', dmp_idx = '".delComma($_idx)."', dmp_sort_idx = '".delComma($_sort_idx)."', dmp_sort_group = '".delComma($_sort_group)."'  ");
			}
			main_product_resort($_uid);

			echo json_encode(array('rst'=>'success','msg'=>'선택하신 상품이 적용 되었습니다.')); exit;

		break;
	}
?>