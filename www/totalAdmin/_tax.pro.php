<?PHP
	include_once(dirname(__file__) . '/inc.php');

	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {


		// --사전 체크 ---
		$CorpName = nullchk($CorpName , '상호명(법인명)을 입력해주시기 바랍니다.');
		$CEOName = nullchk($CEOName , '대표자명을 입력해주시기 바랍니다.');
		$CorpNum = nullchk($CorpNum , '사업자 등록번호를 입력해주시기 바랍니다.');
		//$BizType	= nullchk($BizType , '업태를 입력해주시기 바랍니다. ');
		//$BizClass	= nullchk($BizClass , '종목을 입력해 주시기 바랍니다.');
		$ContactName = nullchk($ContactName , '담당자명을 입력해주시기 바랍니다.');
		$Email = nullchk($Email , '담당자 E-mail을 입력해주시기 바랍니다.');
		$Addr = nullchk($Addr , '사업장 소재지 주소를 입력해주시기 바랍니다.');
		$ContactName	= nullchk($ContactName , '담당자명을 입력해 주시기 바랍니다.');
		$bt_total_price = rm_comma($bt_total_price);
		$bt_total_price	= nullchk($bt_total_price , '발행 합계금액을 입력해주시기 바랍니다.');
		// --사전 체크 ---

		$CorpName = mysql_real_escape_string(trim($CorpName));
		$CEOName = mysql_real_escape_string(trim($CEOName));
		$CorpNum = mysql_real_escape_string(trim($CorpNum));
		$Addr = mysql_real_escape_string(trim($Addr));
		$BizType = mysql_real_escape_string(trim($BizType));
		$BizClass = mysql_real_escape_string(trim($BizClass));
		$ContactName = mysql_real_escape_string(trim($ContactName));
		$HP = mysql_real_escape_string(trim($HP));
		$Email = mysql_real_escape_string(trim($Email));
		$TEL = mysql_real_escape_string(trim($TEL));
		$Name = mysql_real_escape_string(trim($Name));

		// 공급가액/세액 계산
		$Amount = 0;
		$UnitPrice = 0;
		$Tax = 0;
		if($bt_total_price > 0){
			if($TaxInvoiceType == 1) $Tax = ceil($bt_total_price/11); // 과세일때만 세액계산
			$Amount = $bt_total_price - $Tax;
			$UnitPrice = $Amount; // 수량을 1로고정하여 단가와 총액이 동일하다
		}


		// --query 사전 준비 ---
		$sque = "
			bt_total_price = '" . $bt_total_price . "'
			,TaxInvoiceType = '" . $TaxInvoiceType . "'
			,CorpNum = '" . $CorpNum . "'
			,CorpName = '" . $CorpName . "'
			,CEOName = '" . $CEOName . "'
			,Addr = '" . $Addr . "'
			,BizType = '" . $BizType . "'
			,BizClass = '" . $BizClass . "'
			,ContactName = '" . $ContactName . "'
			,TEL = '" . $TEL . "'
			,HP = '" . $HP . "'
			,Email = '" . $Email . "'
			,Name = '" . $Name . "'
			,UnitPrice = '" . $UnitPrice . "'
			,Amount = '" . $Amount . "'
			,Tax = '" . $Tax . "'
			,bt_suid = '" . $suid . "'
		";
		// --query 사전 준비 ---

	}

	// uid가 있으면 세금계산서 정보 추출
	if($_uid){
		$r = _MQ(" select * from smart_baro_tax where bt_uid = '{$_uid}' ");
		if(!$r['bt_uid']) error_msg('잘못된 접근입니다.');
	}


	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			$que = " insert smart_baro_tax set {$sque} , bt_rdate=now() , Status = '0000' ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();

			// 연동 suid가 있으면 연동데이터 상태 변경
			if($suid){
				if($TaxInvoiceType <> 2){ // 과세
					_MQ_noreturn(" update smart_order_settle_complete set s_tax_status = '1000' where s_uid = '{$suid}' ");
				}else{ // 면세
					_MQ_noreturn(" update smart_order_settle_complete set s_tax_status_vat_n = '1000' where s_uid = '{$suid}' ");
				}
			}


			if($_submode == 'quick'){// 즉시발행일경우
				error_loc('_tax.pro.php' . URI_Rebuild('?', array('_mode'=>'quick', '_uid'=>$_uid, '_PVSC'=>$_PVSC)));
			}else{
				error_loc_msg('_tax.form.php' . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$_uid, '_PVSC'=>$_PVSC)), '정상적으로 등록하였습니다.');
			}
			break;


		// 발행전에만 수정가능
		case "modify":
			// 발행전에만 수정 가능
			if($r['Status']<>'0000') error_msg('발행된 세금계산서의 정보는 수정할 수 없습니다.');

			// 발행전에만 수정이 가능하다
			if($r['bt_is_delete']<>'N') error_msg('삭제된 세금계산서 입니다.');


			// 수정
			$que = " update smart_baro_tax set $sque  where bt_uid='{$_uid}' ";
			_MQ_noreturn($que);

			if($_submode == 'quick'){// 즉시발행일경우
				error_loc('_tax.pro.php' . URI_Rebuild('?', array('_mode'=>'quick', '_uid'=>$_uid, '_PVSC'=>$_PVSC)));
			}else{
				error_loc_msg('_tax.form.php' . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$_uid, '_PVSC'=>$_PVSC)), '정상적으로 수정하였습니다.');
			}
			break;


		// 삭제 - DB에서 삭제하지 않고 bt_is_delete 값만 바꿈
		case "delete":

			// 발행전에만 수정이 가능하다
			if($r['bt_is_delete']<>'N') error_msg('이미 삭제된 세금계산서 입니다.');

			// 발행전, 발행거부, 발행취소상태일때 삭제 가능
			if( !in_array($r['Status'], array('0000','4012','5013','5031')) ) error_msg('세금계산서가 삭제불가능한 상태입니다. ');

			// 바로빌변수
			$mode = 'delete'; $uid = $_uid; $trigger_nomsg = 'Y';
			include(OD_ADDONS_ROOT . '/barobill/_tax.pro.php');

			_MQ_noreturn("update smart_baro_tax set bt_is_delete = 'Y' where bt_uid='{$_uid}' ");

			if($TaxInvoiceType <> 2){ // 과세
				_MQ_noreturn(" update smart_order_settle_complete set s_tax_status = '' where s_uid = '". $r['bt_suid'] ."' ");
			}else{ // 면세
				_MQ_noreturn(" update smart_order_settle_complete set s_tax_status_vat_n = '' where s_uid = '". $r['bt_suid'] ."' ");
			}

			error_loc_msg('_tax.list.php' . ($_PVSC ? '?'.enc('d' , $_PVSC) : null), '정상적으로 삭제하였습니다.');
			break;



		// 삭제 - DB에서 삭제하지 않고 bt_is_delete 값만 바꿈
		case "mass_delete":

			if(sizeof($_uids)<1){
				if($no_msg){
					$_result_text = "삭제할 세금계산서를 선택해주세요.";
					$_trigger_tax = false;
					return false;
				}else{
					error_msg("삭제할 세금계산서를 선택해주십시오.");
				}
			}

			// 성공건수
			$app_success = 0;

			// 실패건수
			$app_failed = 0;

			foreach($_uids  as $_uid){
				$_state = _MQ(" select Status from smart_baro_tax where bt_uid = '{$_uid}' ");
				if(!in_array($_state['Status'], array('0000','4012','5013','5031'))){// 임시저장상태만 삭제기능
					$app_failed++;
					continue;
				}

				// 현금영수증 삭제 -- 임시저장상태와 취소상태일때만 취소
				$mode = "delete";
				include( OD_ADDONS_ROOT."/barobill/_tax.pro.php");

				if(true){ // 에러발생내역 삭제를 위해 삭제는 에러체크 안함
				//if($Result > 1){
					_MQ_noreturn(" update smart_baro_tax set bt_is_delete = 'Y' where bt_uid = '${_uid}'");

					$app_success++;


				}else{

					$app_failed++;

				}

			}

			if($no_msg){
				$_result_text = $app_success . "건의 세금계산서를 삭제 하였습니다.". ($app_failed>0 ? " - 삭제실패 : ". $app_failed. "건" : "");
				$_trigger_tax = true;
				return false;
			}else{
				if($app_success==0){
					error_loc_msg("_tax.list.php?".enc("d", $_PVSC), "삭제가능한 세금계산서가 없습니다.");
				}else{
					error_loc_msg("_tax.list.php?".enc("d", $_PVSC), $app_success . "건의 세금계산서를 삭제 하였습니다.". ($app_failed>0 ? "\\n - 삭제실패 : ". $app_failed. "건" : ""));
				}
			}

			break;



		// 세금계산서발행
		case "quick":

			// 바로빌변수
			$mode = 'quick'; $uid = $_uid;
			include(OD_ADDONS_ROOT . '/barobill/_tax.pro.php');

			if ($Result < 0) {
				//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
				error_loc_msg('_tax.list.php' . ($_PVSC ? '?'.enc('d' , $_PVSC) : null), getErrStr($CERTKEY, $Result) . ' (오류코드 : '.$Result.')');
			}
			else{
				//echo $Result; //1-성공
				error_loc_msg('_tax.list.php' . ($_PVSC ? '?'.enc('d' , $_PVSC) : null), '세금계산서가 정상적으로 발행되었습니다.');
			}

			break;



		// 세금계산서발행
		case "mass_issue":

			$total = 0;
			$success = 0;
			$error = 0;
			if(count($_uids) > 0){
				foreach($_uids as $k=>$v){
					$r = _MQ(" select count(*) as cnt from smart_baro_tax where bt_uid  = '{$v}' and Status in ('0000','10000') ");
					if($r['cnt'] < 1) continue;

					$total++;

					// 바로빌변수
					$mode = 'quick'; $uid = $v; $trigger_nomsg = 'Y';
					include(OD_ADDONS_ROOT . '/barobill/_tax.pro.php');

					if ($Result < 0) {
						//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
						$error++;
					}
					else{
						$success++;
					}
				}
			}

			$msg = '발행가능한 세금 계산서가 없습니다.';
			if($total>0){
				if($total == $success){
					$msg = '발행가능한 ' . $total . '건의 세금계산서가 발행되었습니다.';
				}else{
					$msg = '발행가능한 ' . $total . '건의 세금계산서중 '. $success .'건의 세금계산서가 발행되었습니다.';
				}
			}
			error_loc_msg('_tax.list.php' . ($_PVSC ? '?'.enc('d' , $_PVSC) : null), $msg);


			break;



		// 세금계산서취소
		case "cancel":

			// 바로빌변수
			$mode = 'cancel'; $uid = $_uid;
			include(OD_ADDONS_ROOT . '/barobill/_tax.pro.php');

			if ($Result < 0) {
				//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
				error_loc_msg('_tax.list.php' . ($_PVSC ? '?'.enc('d' , $_PVSC) : null), getErrStr($CERTKEY, $Result) . ' (오류코드 : '.$Result.')');
			}
			else{
				//echo $Result; //1-성공
				error_loc_msg('_tax.list.php' . ($_PVSC ? '?'.enc('d' , $_PVSC) : null), '세금계산서가 정상적으로 취소되었습니다.');
			}

			break;



		// 세금계산서 정보 조회
		case "info":

			// 바로빌변수
			$mode = 'info'; $uid=$_uid;
			if($_uid){
				include(OD_ADDONS_ROOT . '/barobill/_tax.pro.php');

				if ($Result < 0){
					error_msgPopup_s( getErrStr($CERTKEY, $Result) . ' (오류코드 : '. $Result .')');
				}else{
					error_loc($Result);
				}
			}else{
				error_msgPopup_s('잘못된 접근 입니다.');
			}

			break;



		// 세금계산서 프린트
		case "print":

			// 문서키 추출
			$res = _MQ(" select MgtKey from smart_baro_tax where bt_uid = '". $_uid ."' and MgtKey != '' ");
			$app_tax_mgtnum = $res['MgtKey'];

			if($app_tax_mgtnum <> ''){

				// 바로빌변수
				$mode = 'print'; // $arr_tax_mgtnum;
				include(OD_ADDONS_ROOT . '/barobill/_tax.pro.php');


				if ($Result < 0){
					error_msgPopup_s( getErrStr($CERTKEY, $Result) . ' (오류코드 : '. $Result .')');
				}else{
					error_loc($Result);
				}
			}else{
				error_msgPopup_s('출력가능한 세금계산서가 없습니다..');
			}

			break;




		// 세금계산서 프린트
		case "mass_print":

			// 문서키 추출
			$arr_tax_mgtnum = array();
			if(count($_uids)>0){
				$res = _MQ_assoc(" select MgtKey from smart_baro_tax where bt_uid in ('". implode("','", $_uids) ."') and MgtKey != '' ");
				foreach($res as $k=>$v){
					$arr_tax_mgtnum[] = $v['MgtKey'];
				}
			}


			if(count($arr_tax_mgtnum)>0){

				// 바로빌변수
				$mode = 'mass_print'; // $arr_tax_mgtnum;
				include(OD_ADDONS_ROOT . '/barobill/_tax.pro.php');


				if ($Result < 0){
					error_msgPopup_s( getErrStr($CERTKEY, $Result) . ' (오류코드 : '. $Result .')');
				}else{
					error_loc($Result);
				}
			}else{
				error_msgPopup_s('출력가능한 세금계산서가 없습니다..');
			}

			break;



	}
	// - 모드별 처리 ---

	exit;