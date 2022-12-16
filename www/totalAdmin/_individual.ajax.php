<?php // -- LCY -- 회원관리 AJAX 처리
	include_once('inc.php');

	switch($ajaxMode){

		// -- 회원 탈퇴처리
		case "getOut":
			$rowChk = _MQ("select *from smart_individual where in_id = '".$inID."' ");
			if($rowChk['in_id'] == ''){ echo json_encode(array('rst'=>'fail','msg'=>'회원정보가 없습니다.')); exit; }

			if($rowChk['in_userlevel'] >= 9 ){ echo json_encode(array('rst'=>'fail','msg'=>'관리자 계정은 탈퇴 처리 할 수 없습니다.')); exit;  } // 관리자 계정 차단
			if($rowChk['in_sleep_type'] == 'Y' ){ echo json_encode(array('rst'=>'fail','msg'=>'휴면회원은 탈퇴 처리 할 수 없습니다.')); exit;  } // 휴면회원 차단
			if($rowChk['in_out'] == 'Y' ){ echo json_encode(array('rst'=>'fail','msg'=>'이미 탈퇴한 회원은 탈퇴 처리 할 수 없습니다.')); exit;  } // 이미 탈퇴한 회원 차단

			memberGetOut($rowChk['in_id']); // 회원 탈퇴 처리

			echo json_encode(array('rst'=>'success', 'msg'=>'선택하신 회원 계정이 탈퇴 처리 되었습니다.'));
			exit;

		break;

		// -- 회원접속정보
		case "viewVisitList":

			// SSJ : 페이징 수정 : 2021-12-29
			if($ahref <> ''){
				$ex = explode("listpg=", $ahref);
				if(count($ex) > 1){ $listpg = $ex[1]; }
			}

			// 데이터 조회
			$listmaxcount = 20 ;
			if( !$listpg ) {$listpg = 1 ;}
			$count = $listpg * $listmaxcount - $listmaxcount;

			$s_query = "
				FROM smart_cntlog_list as sc
				INNER JOIN smart_cntlog_detail as scd ON (sc.sc_uid = scd.sc_uid)
				where sc.sc_memtype = 'Y' and sc.sc_mid = '".$_id."'
			";

			// ------- 순위별 목록 -------
			$que = "SELECT  count(*) as cnt " . $s_query . " ";
			$res = _MQ($que);
			$TotalCount = $res['cnt'];
			$Page = ceil($TotalCount / $listmaxcount);


			// ------- 순위별 목록 -------
			$que = " SELECT  sc.*, scd.* " . $s_query . " ORDER BY sc.sc_uid DESC LIMIT " . $count . " , " . $listmaxcount . " ";
			$res = _MQ_assoc($que);


			$printVisitList = $que;
			if($TotalCount > 0) {
				foreach( $res as $datek => $datev ){
					$_num = $TotalCount - $count - $datek ;
					$_device = ($datev['sc_mobile'] == 'Y' ? '<span class="c_tag h18 mo">MO</span>' : '<span class="c_tag h18 t3 pc">PC</span>');// 접속기기
					$printVisitList .= '<tr>';
					$printVisitList .= '	<td>'. $_num .'</td>';
					$printVisitList .= '	<td>'. $datev['sc_date'] .'</td>';
					$printVisitList .= '	<td>'. $datev['sc_ip'] .'</td>';
					$printVisitList .= '	<td><span class="shop_state_pack">'. $_device .'</span></td>';
					$printVisitList .= '	<td>'. $datev['sc_keyword'] .'</td>';
					$printVisitList .= '	<td class="t_left">' . ( $datev['sc_referer'] ? '<a href="'.$datev['sc_referer'].'" target="_blank">'.$datev['sc_referer'].'</a>' : '' ) . '</td>';
					$printVisitList .= '	<td>'. $datev['sc_browser'] .'</td>';
					$printVisitList .= '</tr>';
				}
			}

		 $printVisitListPaginate .= pagelisting($listpg, $Page, $listmaxcount, "?{$_PVS}&listpg=", 'Y');

		 // -- 결과값 노출
		 echo json_encode(array('rst'=>'success','cnt'=>$TotalCount,'html'=>$printVisitList,'paginate'=>$printVisitListPaginate)); exit;
		break;

		// -- 입력값 체크
		case "inputChk":
			if( empty($_mgsuid) ){ echo json_encode(array('rst'=>'fail','key'=>'_mgsuid','msg'=>'회원등급을 선택해 주세요.')); exit; }
			if( empty($_name) ){ echo json_encode(array('rst'=>'fail','key'=>'_name','msg'=>'이름을 입력해 주세요.')); exit; }
			if( empty($_pw) == false && empty($_rpw) == true ){ echo json_encode(array('rst'=>'fail','key'=>'_rpw','msg'=>'비밀번호 확인을 입력해 주세요.')); exit; }
			if( $_pw != $_rpw ){ echo json_encode(array('rst'=>'fail','key'=>'_rpw','msg'=>'입력된 비밀번호가 서로 다릅니다.')); exit; }
			//if( $_birth == ''){ echo json_encode(array('rst'=>'fail','key'=>'_birth','msg'=>'생년월일을 선택해 주세요.')); exit; }
			if( checkInputValue($_tel2,'htel') !== true && $_tel2){ echo json_encode(array('rst'=>'fail','key'=>'_tel2','msg'=>'올바른 휴대폰 번호를 입력해 주세요.')); exit;  } // LDD: 2018-03-23 휴대폰이 있는 경우만 유효성 검사(필수 입력은 처리 하지 않음:: SNS로그인시 문제생김 -> SNS로그인 회원은 휴대폰 번호가 없다.)
			if( checkInputValue($_email,'email') !== true){ echo json_encode(array('rst'=>'fail','key'=>'_email','msg'=>'올바른 이메일을 입력해 주세요.')); exit;  }
			echo json_encode(array('rst'=>'success')); exit;

		break;

	}



?>