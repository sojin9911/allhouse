<?php // -- LCY -- 입점업체관리 AJAX 처리
	@ini_set("precision", "20");
	@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림
	include_once('inc.php');

	if( $SubAdminMode !== true){  error_msg("이용할 수 없는 메뉴입니다."); }


	if( in_array($_mode, array('add','modify') ) == true){

		// -- 입점업체 등록/수정 공통 처리
		$sque = "
			cp_name						= '". $_name ."'
			,cp_number					= '". $_number ."'
			,cp_snumber					= '". $_snumber ."'			/*       JJC : 2019-05-15 : 판매자 정보 */
			,cp_ceoname					= '". $_ceoname ."'
			,cp_address					= '". $_address ."'
			,cp_item1					= '". $_item1 ."'
			,cp_item2					= '". $_item2 ."'
			,cp_charge					='" . $_charge . "'
			,cp_email					='" . $_email . "'
			,cp_tel						= '". $_tel ."'
			,cp_tel2					= '". $_tel2 ."'
			,cp_fax						= '". $_fax ."'
			,cp_homepage				= '". $_homepage ."'
			,cp_delivery_price			= '$_delivery_price'
			,cp_delivery_freeprice		= '$_delivery_freeprice'
			,cp_delivery_use			= '$_delivery_use'
			,cp_delivery_company		= '$_delivery_company'
			,cp_delivery_date			= '$_delivery_date'
			,cp_delivery_complain_price	= '$_delivery_complain_price'
			,cp_delivery_return_addr	= '$_delivery_return_addr'
			,cp_complain_ok				= '$_complain_ok'
			,cp_complain_fail			= '$_complain_fail'

		";

		// echo $sque;
		// exit;

		// -- 패스워드 입력이 있을경우 처리
		if( $_pw && $_repw ){
			if($_pw == $_repw)
				$sque .= " , cp_pw = password('". $_pw ."')";
			else
				error_msg("비밀번호를 확인해 주세요.");
		}

		// 추가배송비 설정 추가 2017-04-16 :: SSJ
		$sque .= "
			, cp_del_addprice_use      ='" . $_del_addprice_use . "'
			, cp_del_addprice_use_normal   ='" . $_del_addprice_use_normal . "'
			, cp_del_addprice_use_unit ='" . $_del_addprice_use_unit . "'
			, cp_del_addprice_use_free ='" . $_del_addprice_use_free . "'
		";
		// --query 사전 준비 ---

		// 2017-06-16 ::: 부가세율설정 ::: JJC
		$_vat_delivery = ($siteInfo['s_vat_delivery'] == 'C' ? $_vat_delivery : $siteInfo['s_vat_delivery']); // 복합과세가 아닐 경우 전체설정에 적용됨
		$sque .= " , cp_vat_delivery = '" . $_vat_delivery . "' ";
		// 2017-06-16 ::: 부가세율설정 ::: JJC

		// 입점 아이디 공백 제거 추가 kms 2019-08-05
		if ( preg_match( "/\s/", $_id ) > 0 ) {
			$_id = trim($_id);
		}

	}
	// - 입력수정 사전처리 ---


	switch ($_mode) {

		// -- 입점업체등록
		case "add":
			// -- 이메일 중복 체크 ---
			$r = _MQ("select count(*) as cnt from smart_company where cp_id='${_id}' ");
			if( $r[cnt] > 0 ) {
				error_msg("이미 등록된 아이디 입니다.");
			}
			// -- 이메일 중복 체크 ---

			$que = " insert smart_company set $sque , cp_id='{$_id}' , cp_rdate = now() ";
			_MQ_noreturn($que);
			error_loc("_entershop.form.php?_mode=modify&_id=${_id}&_PVSC=${_PVSC}");
			break;
		break;


		// --  입점업체수정
		case "modify":

			$que = " update smart_company set $sque where cp_id='{$_id}' ";
			_MQ_noreturn($que);
			error_loc("_entershop.list.php?".enc('d' , $_PVSC ));
			break;

		break;

		// -- 엑셀다운로드
		case "getExcelDownload":

			// -- 입점업체 키
			$arrKey = array(
				'cp_id'=>'아이디',
				'cp_name'=>'입점업체명',
				'cp_number'=>'사업자번호',
				'cp_ceoname'=>'대표자',
				'cp_address'=>'주소',
				'cp_item1'=>'업태',
				'cp_item2'=>'업종',
				'cp_charge'=>'담당자',
				'cp_email'=>'담당자 이메일',
				'cp_tel'=>'담당자 전화',
				'cp_tel2'=>'담당자 휴대폰',
				'cp_fax'=>'담당자 팩스',
				'cp_homepage'=>'담당자 홈페이지',
				'cp_rdate'=>'등록일',
			);

			if( $ctrlMode  == 'select'){
				if( count($arrID) < 1 ){ error_msg("한개이상 선택해 주세요."); }
				$sque = " from smart_company where 1 and find_in_set(cp_id, '".implode(",",$arrID)."' ) > 0   ";
			}else if( $ctrlMode == 'search'){
				$sque = "  from smart_company  ".enc('d', $searchQue);
			}else{
				error_msg('실행이 올바르지 않습니다.');
			}

			// 	echo "<table><tr><td>".implode("</td><td>",$arrNkey)."</tr></table>";
			if($orderby == '' ){ $orderby = 'order by cp_rdate desc'; }
			$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
			if( count($res) < 1){ error_msg('입점업체 조회에 실패하였습니다.'); }
			$toDay = date('YmdHis');
			$varExcelStyle['th'] = 'background:#e6e9eb;height:48px;';
			## Exel 파일로 변환 #############################################
			header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
			header("Content-Disposition: attachment; filename=입점업체리스트_$toDay.xls");
			print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

			// -- SQL 에서 처리못할 부분이 있을 수 있으니, 될 수 있으면 데이터 뽑아낼떄는 FOREACH 에서 ..
			$arrData = '';
			foreach($res as $k=>$v){
				$arrData[] = "<tr>";
				foreach($v as $key=>$val){
					if( $key == 'cp_tel' || $key == 'cp_tel2' ){  $val = tel_format($val); }
					if( $key == 'cp_rdate'){ $val = date('Y-m-d',strtotime($val)); }
					if( trim($val) == '') { $val = '-'; }
					$arrData[] = "<td>".$val."</td>";
				}
				$arrData[] ="</tr>";
			}


			echo '
			<table border="1">
				<thead>
					<th style="'.$varExcelStyle['th'].'">'.implode("</th><th style='".$varExcelStyle['th']."'>",array_values($arrKey)).'</th></tr>
				</thead>
				<tbody>
					'.implode("",$arrData).'
				</tbody>
			</table>
			';
		break;


	}

?>