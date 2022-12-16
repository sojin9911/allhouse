<?php
	include_once('inc.php');

	switch($_mode){
		# 모비톡 계정정보를 json형태로 반환
		case 'onedaynet_sms_user':
			echo json_encode(onedaynet_sms_user());
			break;

		# 바로빌 잔여포인트
		case 'getBalanceCostAmount':
			include_once( OD_ADDONS_ROOT."/barobill/api_ti/_tax.GetBalanceCostAmount.php");
			if ($Result < 0){
				echo $return_balance;
			}else{
				echo number_format($return_balance);
			}
			break;

		# 바로빌-현금영수증 문서키 중복 체크를 통해 아이디 유효성 체크
		case 'check_key':
			$idx=0;
			while(true){
				$idx++; if($idx > 10) break;

				// 문서키 중복체크
				$mode = "check_key";
				// 문서키생성
				$app_tax_mgtnum = shop_ordernum_create();
				$trigger_msg = true; // 2019-04-02 SSJ :: 경고문구 구분 처리
				include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

				// $Result : 1->이미있음,  2->등록가능
				if($Result == "2") break;

				// 오류일 경우
				if($Result < 0 || $Result > 2){
					$_error = getErrStr($CERTKEY, $Result);
					echo $_error;
					exit;
				}
			}
			break;
	}
	exit;