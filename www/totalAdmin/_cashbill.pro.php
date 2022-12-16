<?PHP
	include_once('inc.php');

	// SSJ : [하이센스3.0] 현금영수증 취소전 국세청 전송여부 체크 패치 : 2021-12-24
	// -- cancelbeforesend 일 경우 우선 상태 갱신 - 2000이 아닌 경우 cancel 로 진행
	if(in_array($app_mode, array("cancelbeforesend")) && $_key){

		$app_tax_mgtnum = $_key;

		// 발급후 현금영수증 상태 재확인
		unset($Result);
		$mode = "check_state";
		include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

		if ($Result->BarobillState < 0){ //실패
		}else{ //성공
			_MQ_noreturn(" update odtBaroCashbill set BarobillState = '". $Result->BarobillState ."' where MgtKey = '${app_tax_mgtnum}'");
		}
		$app_mode = ($Result->BarobillState=='2000' ? "cancelbeforesend" : "cancel");
	}

	$app_mode = $app_mode ? $app_mode : $_mode;

	// -- 변수 재가공 ----
	$IdentityNum = rm_str($IdentityNum);
	$Amount = rm_comma($Amount);
	$Tax = rm_comma($Tax);
	$ServiceCharge = rm_comma($ServiceCharge);
	$Amount = ($Amount > 0 ? $Amount : 0);
	$Tax = ($Tax > 0 ? $Tax : 0);
	$ServiceCharge = ($ServiceCharge > 0 ? $ServiceCharge : 0);
	// -- 변수 재가공 ----

	if(in_array($app_mode, array("save","inssue"))){

	}

	$_trigger_cashbill = false; // 결과저장변수
	switch($app_mode) {
		/*
			$Result = 1; -> 정상처리
			$Result = 음수; -> 에러처리
		*/

		// 임시저장
		case "save":
		// 현금영수증발행
		case "issue":

			if(!$_uid){ // 임시저장

					// 문서키 중복체크
					$mode = "check_key";
					while(true){
						// 문서키생성
						$app_tax_mgtnum = shop_ordernum_create();

						include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

						// $Result : 1->이미있음,  2->등록가능
						if($Result == "2") break;

						// 2018-08-27 SSJ :: 오류메세지 추가
						if($Result < 0 || $Result > 2){
							$_error = getErrStr($CERTKEY, $Result);
							if($no_msg){
								$_result_text = "현금영수증 발행 실패 - ". $_error;
								$_trigger_cashbill = false;
								return false;
								break;
							}else{
								error_msg("현금영수증 발행 실패 - ". $_error);
							}
						}
					}

					// 현금영수증 임시저장
					$mode = "save";
					include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

			}else{ // 임시저장내용 수정

					// 문서키 추출
					$app_tax_mgtnum = _MQ_result(" select MgtKey from smart_baro_cashbill where bc_uid = '${_uid}' ");

					// 현금영수증 임시저장내용 수정
					$mode = "update";
					include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

			}

			// 성공시
			if($Result == "1"){

					// 현금영수증 저장
					$__sque = " bc_type = 'barobill' ";
					$__sque .= "
						,bc_ordernum = '". $_ordernum ."'
						,MgtKey = '". $MgtKey ."'
						,TradeType = 'N'
						,TradeUsage = '". $TradeUsage ."'
						,TradeMethod = '". $TradeMethod ."'
						,IdentityNum = '". $IdentityNum ."'
						,Amount = '". $Amount ."'
						,Tax = '". $Tax ."'
						,ServiceCharge = '". $ServiceCharge ."'
						,ItemName = '". addslashes($ItemName) ."'
						,Email = '". $Email ."'
						,HP = '". $HP ."'
						,Memo = '". addslashes($Memo) ."'
					";

					// 현금영수증 상태 확인
					$mode = "check_state";
					include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

					if ($Result->BarobillState < 0){ //실패

						$err_msg = "PROCESS(".strtoupper($mode).") " . $arr_error_code[$Result->BarobillState];

					}else{ //성공
						$__sque .= "
							,BarobillState = '". $Result->BarobillState ."'
							,TradeDate = '". $Result->TradeDate ."'
							,RegistDT = '". $Result->RegistDT ."'
							,IssueDT = '". $Result->IssueDT ."'
							,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
							,NTSSendDT = '". $Result->NTSSendDT ."'
							,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
							,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
						";
					}



					// DB저장
					if($_uid){
						_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where bc_uid = '${_uid}'");
					}else{
						_MQ_noreturn(" insert into smart_baro_cashbill set ${__sque} ");
						$_uid = mysql_insert_id();
					}


					// 임시저장/업데이트일경우 여기까지만 진행
					if($app_mode == "save"){
						if($no_msg){
							$_result_text = "현금영수증 임시저장 완료";
							$_trigger_cashbill = true;
							return false;
						}else{
							error_loc_msg("_cashbill.form.php?_mode=modify&_uid=".$_uid."&_state=temp&_PVSC=".$_PVSC , ($err_msg ? $err_msg : "현금영수증을 임시저장 하였습니다."));
						}
					}

			}else{

				if($no_msg){
					$_result_text = $arr_error_code[$Result];
					$_trigger_cashbill = false;

								// 자동발행 에러발생시 저장 ------
								// 현금영수증 저장
								$__sque = " bc_type = 'barobill' ";
								$__sque .= "
									,bc_ordernum = '". $_ordernum ."'
									,MgtKey = '". $MgtKey ."'
									,TradeType = 'N'
									,TradeUsage = '". $TradeUsage ."'
									,TradeMethod = '". $TradeMethod ."'
									,IdentityNum = '". $IdentityNum ."'
									,Amount = '". $Amount ."'
									,Tax = '". $Tax ."'
									,ServiceCharge = '". $ServiceCharge ."'
									,ItemName = '". addslashes($ItemName) ."'
									,Email = '". $Email ."'
									,HP = '". $HP ."'
									,Memo = '". addslashes($Memo) ."'
								";
								// 현금영수증 상태 확인
								$mode = "check_state";
								include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

								if ($Result->BarobillState < 0){ //실패

									$__sque .= "
										,BarobillState = '9999'
										,NTSConfirmMessage = '". $_result_text ."'
									";

								}else{ //성공
									$__sque .= "
										,BarobillState = '". $Result->BarobillState ."'
										,TradeDate = '". $Result->TradeDate ."'
										,RegistDT = '". $Result->RegistDT ."'
										,IssueDT = '". $Result->IssueDT ."'
										,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
										,NTSSendDT = '". $Result->NTSSendDT ."'
										,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
										,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
									";
								}

								// DB저장
								if($_uid){
									_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where bc_uid = '${_uid}'");
								}else{
									_MQ_noreturn(" insert into smart_baro_cashbill set ${__sque} ");
									$_uid = mysql_insert_id();
								}
								// 자동발행 에러발생시 저장 ------


					return false;
				}else{
					error_msg("PROCESS(".strtoupper($mode).")" . $arr_error_code[$Result]);
				}

			}

			// -- 여기까지 임시저장영역 ------------------------------------------
			/********************************************/
			// -- 여기서부터 발행
			if($app_mode <> "issue"){
				if($no_msg){
					$_result_text = "잘못된접근";
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("잘못된 접근입니다.");
				}
			}
			if(!$app_tax_mgtnum){
				if($no_msg){
					$_result_text = "잘못된접근";
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("잘못된 접근입니다.");
				}
			}
			if(!$_uid){
				if($no_msg){
					$_result_text = "잘못된접근";
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("잘못된 접근입니다.");
				}
			}

			// 현금영수증 발급
			$mode = "issue";
			include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

			if($Result == "1"){ // 성공시

					// 발급후 현금영수증 상태 재확인
					$mode = "check_state";
					include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

					if ($Result->BarobillState < 0){ //실패

						if($no_msg){
							$_result_text = $arr_error_code[$Result->BarobillState];
							$_trigger_cashbill = false;
							return false;
						}else{
							error_msg("PROCESS(".strtoupper($mode).")" . $arr_error_code[$Result->BarobillState]);
						}

					}else{ //성공

						$__sque = "
							BarobillState = '". $Result->BarobillState ."'
							,TradeDate = '". $Result->TradeDate ."'
							,RegistDT = '". $Result->RegistDT ."'
							,IssueDT = '". $Result->IssueDT ."'
							,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
							,NTSSendDT = '". $Result->NTSSendDT ."'
							,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
							,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
						";

						_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where bc_uid = '${_uid}'");


						// 현금영수증 발행후 주문과 연동된건이면 주문상품의 상태 변경
						$r = _MQ(" select bc_ordernum from smart_baro_cashbill where bc_uid = '${_uid}' ");
						if($r['bc_ordernum']){
							// 취소되지 않은 주문상품의 현금영수증 발행 상태 변경
							_MQ_noreturn(" update smart_order_product set op_is_cashbill = 'Y' where op_oordernum = '". $r['bc_ordernum'] ."' and op_cancel = 'N' and op_is_cashbill = 'N' ");
							_MQ_noreturn(" update smart_order set o_tax_error = '' where o_ordernum = '". $r['bc_ordernum'] ."'  ");
						}


						if($no_msg){
							$_result_text = "현금영수증발행완료";
							$_trigger_cashbill = true;
							return false;
						}else{
							error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), "현금영수증을 발행 하였습니다.");
						}
					}

			}else{

				if($no_msg){
					$_result_text = $arr_error_code[$Result];
					$_trigger_cashbill = false;

								// 자동발행 에러발생시 저장 ------
								// 현금영수증 저장
								$__sque = " bc_type = 'barobill' ";
								$__sque .= "
									,bc_ordernum = '". $_ordernum ."'
									,MgtKey = '". $MgtKey ."'
									,TradeType = 'N'
									,TradeUsage = '". $TradeUsage ."'
									,TradeMethod = '". $TradeMethod ."'
									,IdentityNum = '". $IdentityNum ."'
									,Amount = '". $Amount ."'
									,Tax = '". $Tax ."'
									,ServiceCharge = '". $ServiceCharge ."'
									,ItemName = '". addslashes($ItemName) ."'
									,Email = '". $Email ."'
									,HP = '". $HP ."'
									,Memo = '". addslashes($Memo) ."'
								";
								// 현금영수증 상태 확인
								$mode = "check_state";
								include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

								if ($Result->BarobillState < 0){ //실패

									$__sque .= "
										,BarobillState = '9999'
										,NTSConfirmMessage = '". $_result_text ."'
									";

								}else{ //성공
									$__sque .= "
										,BarobillState = '". $Result->BarobillState ."'
										,TradeDate = '". $Result->TradeDate ."'
										,RegistDT = '". $Result->RegistDT ."'
										,IssueDT = '". $Result->IssueDT ."'
										,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
										,NTSSendDT = '". $Result->NTSSendDT ."'
										,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
										,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
									";
								}

								// DB저장
								if($_uid){
									_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where bc_uid = '${_uid}'");
								}else{
									_MQ_noreturn(" insert into smart_baro_cashbill set ${__sque} ");
									$_uid = mysql_insert_id();
								}
								// 자동발행 에러발생시 저장 ------

					return false;
				}else{
					error_msg("PROCESS(".strtoupper($mode).")" . $arr_error_code[$Result]);
				}

			}


			break;

		// 목록에서 현금영수증발행
		case "issue_one":

			$app_tax_mgtnum = $_key;

			// 현금영수증 발급
			$mode = "issue";
			include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

			if($Result == "1"){ // 성공시

					// 발급후 현금영수증 상태 재확인
					$mode = "check_state";
					include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

					if ($Result->BarobillState < 0){ //실패

						if($no_msg){
							$_trigger_cashbill = false;
							$_result_text = $arr_error_code[$Result->BarobillState];
							return false;
						}else{
							error_msg("PROCESS(".strtoupper($mode).")" . $arr_error_code[$Result->BarobillState]);
						}

					}else{ //성공

						$__sque = "
							BarobillState = '". $Result->BarobillState ."'
							,TradeDate = '". $Result->TradeDate ."'
							,RegistDT = '". $Result->RegistDT ."'
							,IssueDT = '". $Result->IssueDT ."'
							,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
							,NTSSendDT = '". $Result->NTSSendDT ."'
							,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
							,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
						";

						_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where MgtKey = '${app_tax_mgtnum}'");

						// 현금영수증 발행후 주문과 연동된건이면 주문상품의 상태 변경
						$r = _MQ(" select bc_ordernum from smart_baro_cashbill where bc_uid = '${_uid}' ");
						if($r['bc_ordernum']){
							// 취소되지 않은 주문상품의 현금영수증 발행 상태 변경
							_MQ_noreturn(" update smart_order_product set op_is_cashbill = 'Y' where op_oordernum = '". $r['bc_ordernum'] ."' and op_cancel = 'N' and op_is_cashbill = 'N' ");
							_MQ_noreturn(" update smart_order set o_tax_error = '' where o_ordernum = '". $r['bc_ordernum'] ."'  ");
						}

						if($no_msg){
							$_result_text = "현금영수증발행완료";
							$_trigger_cashbill = true;
							return false;
						}else{
							error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), "현금영수증을 발행 하였습니다.");
						}
					}

			}else{

				if($no_msg){
					$_result_text = $arr_error_code[$Result];
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("PROCESS(".strtoupper($mode).")" . $arr_error_code[$Result]);
				}

			}

			break;

		// 목록에서 현금영수증발행
		case "mass_issue":

			if(sizeof($_mgtnum)<1){
				if($no_msg){
					$_result_text = "발행할 현금영수증을 선택해주세요.";
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("발행할 현금영수증을 선택해주십시오.");
				}
			}

			// 성공건수
			$app_success = 0;

			// 실패건수
			$app_failed = 0;

			foreach($_mgtnum  as $app_tax_mgtnum){

				// 현금영수증 발급
				$mode = "issue";
				include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

				if($Result == "1"){ // 성공시

						// 발급후 현금영수증 상태 재확인
						$mode = "check_state";
						include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

						if ($Result->BarobillState < 0){ //실패

							$app_failed++;

						}else{ //성공

							$__sque = "
								BarobillState = '". $Result->BarobillState ."'
								,TradeDate = '". $Result->TradeDate ."'
								,RegistDT = '". $Result->RegistDT ."'
								,IssueDT = '". $Result->IssueDT ."'
								,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
								,NTSSendDT = '". $Result->NTSSendDT ."'
								,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
								,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
							";

							_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where MgtKey = '${app_tax_mgtnum}'");

							// 현금영수증 발행후 주문과 연동된건이면 주문상품의 상태 변경
							$r = _MQ(" select bc_ordernum from smart_baro_cashbill where MgtKey = '${app_tax_mgtnum}' ");
							if($r['bc_ordernum']){
								// 취소되지 않은 주문상품의 현금영수증 발행 상태 변경
								_MQ_noreturn(" update smart_order_product set op_is_cashbill = 'Y' where op_oordernum = '". $r['bc_ordernum'] ."' and op_cancel = 'N' and op_is_cashbill = 'N' ");
								_MQ_noreturn(" update smart_order set o_tax_error = '' where o_ordernum = '". $r['bc_ordernum'] ."'  ");
							}

							$app_success++;
						}

				}else{

					$app_failed++;

				}

			}

			if($no_msg){
				$_result_text = $app_success . "건의 현금영수증을 발행 하였습니다.". ($app_failed>0 ? " - 발행실패 : ". $app_failed. "건" : "");
				$_trigger_cashbill = true;
				return false;
			}else{
				if($app_success == 0){
					error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), "발행가능한 현금영수증이 없습니다.");
				}else{
					error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), $app_success . "건의 현금영수증을 발행 하였습니다.". ($app_failed>0 ? "\\n - 발행실패 : ". $app_failed. "건" : ""));
				}
			}


			break;

		case "cancel":

			$app_tax_mgtnum = $_key;

			// 현금영수증 발급
			$mode = "cancel";
			include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");


			if($Result == "1"){
				_MQ_noreturn(" update smart_baro_cashbill set bc_iscancel = 'Y' where MgtKey = '${app_tax_mgtnum}'");

				// 발급후 현금영수증 상태 재확인
				$mode = "check_state";
				include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

				if ($Result->BarobillState < 0){ //실패

				}else{ //성공

					$__sque = "
						BarobillState = '". $Result->BarobillState ."'
						,TradeDate = '". $Result->TradeDate ."'
						,RegistDT = '". $Result->RegistDT ."'
						,IssueDT = '". $Result->IssueDT ."'
						,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
						,NTSSendDT = '". $Result->NTSSendDT ."'
						,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
						,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
					";

					_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where MgtKey = '${app_tax_mgtnum}'");


					// 현금영수증 취소후 주문과 연동된건이면 주문상품의 상태 변경
					$r = _MQ(" select bc_ordernum from smart_baro_cashbill where MgtKey = '${app_tax_mgtnum}' ");
					if($r['bc_ordernum']){
						// 취소되지 않은 주문상품의 현금영수증 발행 상태 변경
						_MQ_noreturn(" update smart_order_product set op_is_cashbill = 'N' where op_oordernum = '". $r['bc_ordernum'] ."'  ");
						_MQ_noreturn(" update smart_order set o_tax_error = '' where o_ordernum = '". $r['bc_ordernum'] ."'  ");
					}

				}

				if($no_msg){
					$_result_text = "현금영수증취소";
					$_trigger_cashbill = true;
					return false;
				}else{
					error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), "현금영수증을 취소 하였습니다.");
				}
			}else{
				if($no_msg){
					$_result_text = $arr_error_code[$Result];
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("PROCESS(".strtoupper($mode).") " . $arr_error_code[$Result] . " " . $Result);
				}
			}


			break;

		case "cancelbeforesend":

			$app_tax_mgtnum = $_key;

			// 현금영수증 발급
			$mode = "cancelbeforesend";
			include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

			if($Result == "1"){
				_MQ_noreturn(" update smart_baro_cashbill set bc_iscancel = 'Y' where MgtKey = '${app_tax_mgtnum}'");

				// 발급후 현금영수증 상태 재확인
				unset($Result);
				$mode = "check_state";
				include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

				if ($Result->BarobillState < 0){ //실패

				}else{ //성공

					$__sque = "
						BarobillState = '". $Result->BarobillState ."'
						,TradeDate = '". $Result->TradeDate ."'
						,RegistDT = '". $Result->RegistDT ."'
						,IssueDT = '". $Result->IssueDT ."'
						,NTSConfirmNum = '". $Result->NTSConfirmNum ."'
						,NTSSendDT = '". $Result->NTSSendDT ."'
						,NTSConfirmDT = '". $Result->NTSConfirmDT ."'
						,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."'
					";

					_MQ_noreturn(" update smart_baro_cashbill set ${__sque} where MgtKey = '${app_tax_mgtnum}'");


					// 현금영수증 취소후 주문과 연동된건이면 주문상품의 상태 변경
					$r = _MQ(" select bc_ordernum from smart_baro_cashbill where MgtKey = '${app_tax_mgtnum}' ");
					if($r['bc_ordernum']){
						// 취소되지 않은 주문상품의 현금영수증 발행 상태 변경
						_MQ_noreturn(" update smart_order_product set op_is_cashbill = 'N' where op_oordernum = '". $r['bc_ordernum'] ."'  ");
						_MQ_noreturn(" update smart_order set o_tax_error = '' where o_ordernum = '". $r['bc_ordernum'] ."'  ");
					}

				}

				if($no_msg){
					$_result_text = "현금영수증취소";
					$_trigger_cashbill = true;
					return false;
				}else{
					error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), "현금영수증을 취소 하였습니다.");
				}
			}else{
				if($no_msg){
					$_result_text = $arr_error_code[$Result];
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("PROCESS(".strtoupper($mode).") " . $arr_error_code[$Result] . " " . $Result);
				}
			}


			break;

		case "delete":

			$app_tax_mgtnum = $_key;

			// 현금영수증 삭제 -- 임시저장상태와 취소상태일때만 취소
			$mode = "delete";
			include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

			if(true){ // 에러발생내역 삭제를 위해 삭제는 에러체크 안함
			//if($Result > 1){
				_MQ_noreturn(" update smart_baro_cashbill set bc_isdelete = 'Y' where MgtKey = '${app_tax_mgtnum}'");

				if($no_msg){
					$_result_text = "현금영수증삭제";
					$_trigger_cashbill = true;
					return false;
				}else{
					error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), "현금영수증을 삭제 하였습니다.");
				}
			}else{
				if($no_msg){
					$_result_text = $arr_error_code[$Result];
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("PROCESS(".strtoupper($mode).") " . $arr_error_code[$Result]) . " " . $Result;
				}
			}


			break;

		// 관리자메모 수정
		case "modify_memo":

			_MQ_noreturn(" update smart_baro_cashbill set Memo = '". addslashes($Memo) ."' where bc_uid = '". $_uid ."'");

			if($no_msg){
				$_result_text = "관리자메모 저장완료";
				$_trigger_cashbill = true;
				return false;
			}else{
				error_loc_msg("_cashbill.view.php?_uid=".$_uid."&_state=".$_state."&_PVSC=".$_PVSC , "정상적으로 저장하였습니다.");
			}

			break;


		// 목록에서 현금영수증발행
		case "mass_delete":

			if(sizeof($_mgtnum)<1){
				if($no_msg){
					$_result_text = "삭제할 현금영수증을 선택해주세요.";
					$_trigger_cashbill = false;
					return false;
				}else{
					error_msg("삭제할 현금영수증을 선택해주십시오.");
				}
			}

			// 성공건수
			$app_success = 0;

			// 실패건수
			$app_failed = 0;

			foreach($_mgtnum  as $app_tax_mgtnum){
				$_state = _MQ(" select BarobillState from smart_baro_cashbill where MgtKey = '{$app_tax_mgtnum}' ");
				if(!in_array($_state['BarobillState'], array('9999','1000','4000','6000','7000'))){// 임시저장상태만 삭제기능
					$app_failed++;
					continue;
				}

				// 현금영수증 삭제 -- 임시저장상태와 취소상태일때만 취소
				$mode = "delete";
				include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

				if(true){ // 에러발생내역 삭제를 위해 삭제는 에러체크 안함
				//if($Result > 1){
					_MQ_noreturn(" update smart_baro_cashbill set bc_isdelete = 'Y' where MgtKey = '${app_tax_mgtnum}'");

					$app_success++;


				}else{

					$app_failed++;

				}

			}

			if($no_msg){
				$_result_text = $app_success . "건의 현금영수증을 삭제 하였습니다.". ($app_failed>0 ? " - 삭제실패 : ". $app_failed. "건" : "");
				$_trigger_cashbill = true;
				return false;
			}else{
				if($app_success==0){
					error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), "삭제가능한 현금영수증이 없습니다.");
				}else{
					error_loc_msg("_cashbill.list.php?".enc("d", $_PVSC), $app_success . "건의 현금영수증을 삭제 하였습니다.". ($app_failed>0 ? "\\n - 삭제실패 : ". $app_failed. "건" : ""));
				}
			}


			break;
	}


