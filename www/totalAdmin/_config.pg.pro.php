<?PHP
	include "./inc.php";

	if($_pg_type == ''){ error_msg('PG사가 선택 되지 않았습니다.'); }

	$arrSque = array();
	// 2017-06-16 ::: 부가세율설정 ::: JJC
	//		- 빌게이트일 경우 부가세 적용되지 않음.
	if($_pg_type == 'billgate') {
		$arrSque[] = " s_vat_product = 'Y' ";
		$arrSque[] = " s_vat_delivery = 'Y' ";
		$arrSque[] = " s_vat_discount = 'Y' ";
	}
	// 2017-06-16 ::: 부가세율설정 ::: JJC

	// -- 가상계좌 입금일 설정 값
	$_pg_virtual_date = (!is_numeric($_pg_virtual_date))?$siteInfo[s_pg_virtual_date]:$_pg_virtual_date;
	$_pg_virtual_date = ($_pg_virtual_date < 0)?$_pg_virtual_date*-1:$_pg_virtual_date;
	$_pg_virtual_date = ($_pg_virtual_date > 14)?'14':$_pg_virtual_date;

	// -- 에스크로 가입여부
	$_view_escrow_join_info = $_view_escrow_join_info == "Y" ? "Y" : "N";

	// -- 일반 할 부 설정이 Y일경우 기간
	if( $_pg_installment == 'Y'){

		if( $_pg_type != 'kcp' && $_pg_type != 'lgpay'){ // SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
			if(  is_array($_pg_installment_peroid) == true &&  count($_pg_installment_peroid) > 0 ){
				$_pg_installment_peroid = implode(",",$_pg_installment_peroid);
			}else{
				error_msg('일반 할부 기간은 최소 1개이상 선택하셔야합니다.');
			}
		}else{
			if( $_pg_installment_peroid == ''){ error_msg('일반 할부 기간을 선택해 주세요.');  }
		}
	}

	// -- 무이자 할 부 설정이 Y일경우 기간
	if( $_pg_noinstallment == 'Y'){
		if(is_array($_pg_noinstallment_peroid) == true && count($_pg_noinstallment_peroid) > 0  ){
			$_pg_noinstallment_peroid = implode(",",$_pg_noinstallment_peroid);
		}else{
			//error_msg('무이자 할부 기간은 최소 1개이상 선택하셔야합니다.');
		}
	}

	// -- 에스크로 이미지 처리
	// $_pg_escrow_mark_name = _PhotoPro('..'.IMG_DIR_NORMAL, '_pg_escrow_mark') ; // 배너이미지



	$arrSque[] = " s_pg_type = '".$_pg_type."' "; // PG사 타입
	if( trim($_pg_code) != '' ) { $arrSque[] = " s_pg_code = '".trim($_pg_code)."' "; } // PG아이디
	if( $_pg_mode != ''){ $arrSque[] = " s_pg_mode = '".$_pg_mode."' "; }  // PG사 모드
	if( $_pg_key != '') { $arrSque[] = " s_pg_key = '".$_pg_key."' "; } // 사이트 키값 (KCP , LG PAY)
	if( $_pg_enc_key != ''){  $arrSque[] = " s_pg_enc_key = '".$_pg_enc_key."' "; }  // 가맹점 암호화키 (키움페이)


	if( $_pg_skey != ''){ $arrSque[] = " s_pg_skey = '".$_pg_skey."' ";} // 사인키 :: 이니시스
	if( $_pg_code_escrow != ''){ $arrSque[] = " s_pg_code_escrow = '".$_pg_code_escrow."' ";} // 에스크로 코드 :: 이니시스
	if( $_pg_escrow_skey != ''){ $arrSque[] = " s_pg_escrow_skey = '".$_pg_escrow_skey."' ";} // 에스크로 사인키 :: 이니시스
	if( $_view_escrow_join_info != ''){ $arrSque[] = " s_view_escrow_join_info = '".$_view_escrow_join_info."' ";} // 에스크로 가입정보 노출여부

	if( $_pg_installment != ''){ $arrSque[] = " s_pg_installment = '".$_pg_installment."' ";} // 일반 할부 설정 (Y,N)
	if( $_pg_installment_peroid != ''){ $arrSque[] = " s_pg_installment_peroid = '".$_pg_installment_peroid."' ";} // 일반 할부 설정 이 Y 일시 기간선택
	if( $_pg_noinstallment != ''){ $arrSque[] = " s_pg_noinstallment = '".$_pg_noinstallment."' ";} // 무이자 할부 설정 (Y,N)
	if( $_pg_noinstallment_peroid != ''){ $arrSque[] = " s_pg_noinstallment_peroid = '".$_pg_noinstallment_peroid."' ";} // 무이자 할부 설정 이 Y 일시 기간선택

	if( $_pg_virtual_date != ''){ $arrSque[] = " s_pg_virtual_date = '".$_pg_virtual_date."' ";} // 계상계좌 입금기한
	$arrSque[] = " s_pg_app_scheme = '".$_pg_app_scheme."' "; // 앱 스키마


	if( $_cash_receipt_use != ''){ $arrSque[] = " s_cash_receipt_use = '".$_cash_receipt_use."' ";} // 현금영수증 설정(Y,N)
	if( $_cash_receipt_sel != ''){ 	$arrSque[] = " s_cash_receipt_sel = '".$_cash_receipt_sel."' ";} // 현금영수증 설정 Y 일경우 :: 발급 필수설정(Y,N)
	if( $_cash_receipt_issued_type != ''){ $arrSque[] = " s_cash_receipt_issued_type = '".$_cash_receipt_issued_type."' "; } // 현금영수증 설정 Y 일경우 :: 현금영수증 발급 방법(auto : 자동 발급, admin : 관리자 발급)

	// $arrSque[] = " s_pg_escrow_mark = '".$_pg_escrow_mark_name."' "; // 에스크로 인증마크

    /* LCY : 2021-07-04 : 신용카드 간편결제 추가 */
    $arrSque[] = "s_pg_paymethod_easypay = '".(count($s_pg_paymethod_easypay) > 0 ? implode(",",$s_pg_paymethod_easypay) : '')."'"; // 간편결제수단 설정 추가

	$sque = implode(", ",$arrSque);
	_MQ_noreturn("update smart_setup set ".$sque." where s_uid = '1' ");
	error_loc("_config.pg.form.php");