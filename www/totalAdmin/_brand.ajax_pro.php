<?php
	include_once("inc.php");

	if($_mode == ''){ echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }



	// --- 브래드 초성정보 추출 ---
	$_prefix_str = '';
	if($_name) {
		$_prefix_str = strtoupper(cutstr_new(linear_hangul(trim($_name)),1,'')); // 대문자로 저장
	}


	switch($_mode){

		// -- 수정
		case "modify":
			// -- 해당 브랜드의 정보를 가져온다.
			$rowAdminMenu = _MQ("select *from smart_brand where c_uid = '".$_uid."' ");
			if(count($rowAdminMenu) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'이름을 입력해 주세요.','key'=>'_name['.$_uid.']')); exit;}
			if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view['.$_uid.']')); exit;}

			// 브랜드명 중복 검색
			$rowAdminMenu = _MQ("select count(*) as cnt from smart_brand where c_name = '".$_name."' and c_uid != '".$_uid."' ");
			if($rowAdminMenu['cnt'] > 0 ) {
				echo json_encode(array('rst'=>'error','msg'=>'브랜드명이 중복되었습니다.','key'=>'_name')); exit;
			}

			_MQ_noreturn("update smart_brand set c_view = '".$_view."' , c_name ='".addslashes($_name)."' , c_prefix_str = '". addslashes($_prefix_str) ."'  ".$addQue." where c_uid = '".$_uid."'  ");
			echo json_encode(array('rst'=>'success','msg'=>'브랜드가 저장되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 추가
		case "add":

			if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'이름을 입력해 주세요.','key'=>'ADD_name')); exit;}
			if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'ADD_view')); exit;}

			// 브랜드명 중복 검색
			$rowAdminMenu = _MQ("select count(*) as cnt from smart_brand where c_name = '".$_name."' ");
			if($rowAdminMenu['cnt'] > 0 ) {
				echo json_encode(array('rst'=>'error','msg'=>'브랜드명이 중복되었습니다.','key'=>'ADD_name')); exit;
			}

			$_parent = 0;
			if($_depth == 2){
				$_parent = $locUid1;
			}else if($_depth == 3){
				$_parent = $locUid1.",".$locUid2;
			}

			$rowIdx = _MQ("select ifnull(max(c_idx),0) + 1 as max_idx from smart_brand where c_depth = '".$_depth."' ".($_depth > 1 ? "and c_parent = '".$_parent."' ":""));

			_MQ_noreturn("insert smart_brand set c_view = '".$_view."' , c_name ='".addslashes($_name)."' , c_prefix_str = '". addslashes($_prefix_str) ."', c_depth = '1', c_idx = '".$rowIdx['max_idx']."'  ".$addQue." ");

			echo json_encode(array('rst'=>'success','msg'=>'브랜드가 추가되었습니다.','_depth'=>$_depth)); exit;

		break;


		// -- 삭제
		case "delete":
			// -- 해당 브랜드의 정보를 가져온다.
			$rowAdminMenu = _MQ("select *from smart_brand where c_uid = '".$_uid."' ");
			if(count($rowAdminMenu) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			_MQ_noreturn("delete from smart_brand where c_uid = '".$_uid."' or find_in_set('".$_uid."',c_parent) > 0 ");

			echo json_encode(array('rst'=>'success','msg'=>'브랜드가 삭제되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 순서변경
		case "idx":

			if( $_type == 'up'){
		    // 정보 불러오기
		    $que  = " SELECT c_idx , c_depth , c_parent FROM smart_brand WHERE c_uid = '$_uid' ";
		    $r = _MQ($que);

		    $_idx = $r[c_idx];
		    $_parent = $r[c_parent];
		    $_depth = $r[c_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_brand WHERE c_idx = '$_idx' and c_parent='$_parent' ";
		        $r = _MQ($que);
		        if($r[cnt] > 1) {
		      _MQ_noreturn(" UPDATE smart_brand SET c_idx = c_idx+1 WHERE c_idx >= '$_idx' and c_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MIN(c_idx),0) as minc_idx FROM smart_brand WHERE c_parent='$_parent' ";
		    $r = _MQ($que);
		    $minc_idx = $r[minc_idx];

		    if ($minc_idx == $_idx) {
		       echo json_encode(array('rst'=>'fail','msg'=>'더이상 상위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계위 데이터와 c_idx 값 바꿈
		        $sque = "select c_idx , c_uid from smart_brand WHERE c_parent='$_parent' and c_idx < '$_idx' order by c_idx desc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_brand SET c_idx = $_idx WHERE c_uid='$sr[c_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_brand SET c_idx = $sr[c_idx] WHERE c_uid = '$_uid' ");

		    }
		  }else if($_type == 'down'){

		    // 정보 불러오기
		    $que  = " SELECT c_idx , c_depth , c_parent FROM smart_brand WHERE c_uid = '$_uid' ";
		    $r = _MQ($que);
		    $_idx = $r[c_idx];
		    $_parent = $r[c_parent];
		    $_depth = $r[c_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_brand WHERE c_idx = '$_idx' and c_parent='$_parent' ";
		        $r = _MQ($que);

		        if($r[cnt] > 1) {
		      		_MQ_noreturn(" UPDATE smart_brand SET c_idx = c_idx-1 WHERE c_idx <= '$_idx' and c_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MAX(c_idx),0) as maxc_idx FROM smart_brand WHERE c_parent='$_parent' ";
		    $r = _MQ($que);
		    $maxc_idx = $r[maxc_idx];

		    if ($maxc_idx == $_idx) {
		    	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계 아래 데이터와 c_idx 값 바꿈
		        $sque = "select c_idx , c_uid from smart_brand WHERE 1 and c_idx > '$_idx' ".($_depth != 1 ? " and c_parent='$_parent'  ":"" )." order by c_idx asc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_brand SET c_idx = $_idx WHERE c_uid='$sr[c_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_brand SET c_idx = $sr[c_idx] WHERE c_uid = '$_uid' ");
		        //	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }

		  }else{
		  	echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit;
		  }

			echo json_encode(array('rst'=>'success')); exit;
		break;



		// -- 일괄 수정
		case "mass_modify":

			if(sizeof($_view) > 0 ) {
				foreach($_view as $k=>$v){

					$app_name = $_name[$k];
					$_prefix_str = strtoupper(cutstr_new(linear_hangul(trim($app_name)),1,'')); // 대문자로 저장

					_MQ_noreturn("
						update smart_brand set
							c_view = '". $v ."' ,
							c_name ='" . addslashes($app_name)."' ,
							c_prefix_str = '". addslashes($_prefix_str) ."'
						where
							c_uid = '". $k ."'
					");
				}
			}
			error_loc_msg("_brand.list.php" , "브랜드가 저장되었습니다.");

		break;



	}
?>