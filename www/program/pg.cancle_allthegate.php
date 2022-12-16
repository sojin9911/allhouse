<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 가상계좌 취소 준비
$e = _MQ("select * from smart_order_onlinelog where ool_ordernum = '$ordernum' order by ool_uid desc");

if($e[ool_escrow]=='Y') {

	$account = $e[ool_account_num];
	$depositor = $e[ool_deposit_name];
	$amount = $e[ool_amount_current];
	$es_code = $e[ool_escrow_code];

	$TrCode = 'E400';                   //거래코드
	$PayKind = '03';                //결제종류
	$RetailerId =  $siteInfo[s_pg_code];			//상점ID
	$DealTime = substr($e[ool_respdate],0,8);				//결제일자
	$SendNo = $e[ool_escrow_code];					//거래고유번호
	$IdNo = '';						//주민등록번호

	$IsDebug = 0;
	$LOCALADDR  = "220.85.12.74";
	$LOCALPORT  = "29760";
	$ENCTYPE    = "E";
	$CONN_TIMEOUT = 10;
	$READ_TIMEOUT = 30;

	$ERRMSG = "";
	if( empty( $TrCode ) || $TrCode == "" )	{ $ERRMSG .= "거래코드 입력여부 확인요망 <br>"; }
	if( empty( $PayKind ) || $PayKind == "" ) { $ERRMSG .= "결제종류 입력여부 확인요망 <br>"; }
	if( empty( $RetailerId ) || $RetailerId == "" )	{ $ERRMSG .= "상점아이디 입력여부 확인요망 <br>"; }
	if( empty( $DealTime ) || $DealTime == "" )	{ $ERRMSG .= "결제일자 입력여부 확인요망 <br>"; }
	if( empty( $SendNo ) || $SendNo == "" )	{ $ERRMSG .= "거래고유번호 입력여부 확인요망 <br>"; }
	if( strlen($ERRMSG) == 0 ) {
		/****************************************************************************
	    * TrCode = "E100" 발송완료
		* TrCode = "E200" 구매확인
		* TrCode = "E300" 구매거절
		* TrCode = "E400" 결제취소
		****************************************************************************/

		/****************************************************************************
		*
		* [4] 발송완료/구매확인/구매거절/결제취소요청 (E100/E101)/(E200/E201)/(E300/E301)/(E400/E401)
		* 
		* -- 데이터 길이는 매뉴얼 참고
		* 
		* -- 발송완료 요청 전문 포멧
		* + 데이터길이(6) + 자체 ESCROW 구분(1) + 데이터
		* + 데이터 포멧(데이터 구분은 "|"로 한다.)
		* 거래코드(10)	| 결제종류(2)	| 업체ID(20)	| 주민등록번호(13) | 
		* 결제일자(8)	| 거래고유번호(6)	| 
		* 
		* -- 발송완료 응답 전문 포멧
		* + 데이터길이(6) + 데이터
		* + 데이터 포멧(데이터 구분은 "|"로 한다.
		* 거래코드(10)	|결제종류(2)	| 업체ID(20)	| 결과코드(2)	| 결과 메시지(100)	| 
		*    
		*****************************************************************************/

		$ENCTYPE = "E";

		/****************************************************************************
		* 전송 전문 Make
		****************************************************************************/
		
		$sDataMsg = $ENCTYPE.
			$TrCode."|".
			$PayKind."|".
			$RetailerId."|".
			$IdNo."|".
			$DealTime."|".
			$SendNo."|";

		$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
		
		/****************************************************************************
		* 
		* 전송 메세지 프린트
		* 
		****************************************************************************/
		
		if( $IsDebug == 1 )
		{
			print $sSendMsg."<br>";
		}

		/****************************************************************************
		* 
		* 암호화Process와 연결을 하고 승인 데이터 송수신
		* 
		****************************************************************************/
		
		$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );
		
		
		if( !$fp )
		{
			/** 연결 실패로 인한 거래실패 메세지 전송 **/
			
			$rSuccYn = "n";
			$rResMsg = "연결 실패로 인한 거래실패";
		}
		else 
		{
			/** 연결에 성공하였으므로 데이터를 받는다. **/
			
			$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
			
			
			/** 승인 전문을 암호화Process로 전송 **/
			
			fputs( $fp, $sSendMsg );
			
			socket_set_timeout($fp, $READ_TIMEOUT);
			
			/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
			
			$sRecvLen = fgets( $fp, 7 );
			$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
		
			/****************************************************************************
			*
			* 데이터 값이 정상적으로 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
			* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
			* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
			* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
			* $sRecvLen = fgets( $fp, 6 );
			* $sRecvMsg = fgets( $fp, $sRecvLen );
			*
			****************************************************************************/

			/** 소켓 close **/
			
			fclose( $fp );
		}
		
		/****************************************************************************
		* 
		* 수신 메세지 프린트
		* 
		****************************************************************************/
		
		if( $IsDebug == 1 )	
		{
			print $sRecvMsg."<br>";
		}
		
		if( strlen( $sRecvMsg ) == $sRecvLen )
		{
			/** 수신 데이터(길이) 체크 정상 **/
			
			$RecvValArray = array();
			$RecvValArray = explode( "|", $sRecvMsg );
			
			$rTrCode        = $RecvValArray[0];
			$rPayKind       = $RecvValArray[1];
			$rRetailerId    = $RecvValArray[2];
			$rSuccYn        = $RecvValArray[3];
			$rResMsg        = $RecvValArray[4];
			
			/****************************************************************************
			*
			* 에스크로 통신 결과가 정상적으로 수신되었으므로 DB 작업을 할 경우 
			* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
			*
			* TrCode = "E101" 발송완료응답
			* TrCode = "E201" 구매확인응답
			* TrCode = "E301" 구매거절응답
			* TrCode = "E401" 취소요청응답
			*
			* 여기서 DB 작업을 해 주세요.
			* 주의) $rSuccYn 값이 'y' 일경우 에스크로배송등록및구매확인성공
			* 주의) $rSuccYn 값이 'n' 일경우 에스크로배송등록및구매확인실패
			* DB 작업을 하실 경우 $rSuccYn 값이 'y' 또는 'n' 일경우에 맞게 작업하십시오. 
			*
			****************************************************************************/
			
			
			
			
		}
		else
		{
			/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
			
			$rSuccYn = "n";
			$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
		}
	}
	else
	{
		$rSuccYn = "n";
		$rResMsg = $ERRMSG;
	}
	$is_pg_status = ($rSuccYn == "y") ? true : false;		

} else { // 카드결제 취소 시작


// 거래번호
$rApprNo = $r[oc_tid]; // PG사 거래 번호

// 취소에 필요한 카드결제 정보 추출
$card_log_tmp = _MQ("select oc_content from smart_order_cardlog where oc_tid = '".$rApprNo."' and oc_uid = '".$r[oc_uid]."' limit 1");
$card_log_tmp = explode("§§",$card_log_tmp[oc_content]);
foreach($card_log_tmp as $tmp_val) {
	list($key,$val) = explode("||",$tmp_val);
	if($key) $card_log_value[$key] = $val;
}


# 거래번호가 없다면 취소 중단 2016-09-27 {
if($rApprNo == '' || $card_log_value["rDealNo"] == '') {

    $is_pg_status = false;
    return;
}
# 거래번호가 없다면 취소 중단 2016-09-27 }

#############################################################################################
## 올더게이트 결제 취소 START
#############################################################################################
require_once(PG_DIR."/Ags/lib/AGSLib.php");

$agspay = new agspay40;

$agspay->SetValue("AgsPayHome",PG_DIR."/Ags");     
$agspay->SetValue("log","true");                                                    //true : 로그기록, false : 로그기록안함.
$agspay->SetValue("logLevel","DEBUG");                                      //로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
$agspay->SetValue("Type", "Cancel");                                            //고정값(수정불가)
$agspay->SetValue("RecvLen", 7);                                                    //수신 데이터(길이) 체크 에러시 6 또는 7 설정. 

$agspay->SetValue("StoreId", $siteInfo[s_pg_code]);                                      //상점아이디
$agspay->SetValue("AuthTy",  "card");                                           //결제형태
$agspay->SetValue("SubTy",   trim($card_log_value["SubTy"]));      //서브결제형태
$agspay->SetValue("rApprNo", trim($rApprNo));     //승인번호
$agspay->SetValue("rApprTm", trim($card_log_value["rApprTm"]));     //승인일자
$agspay->SetValue("rDealNo", trim($card_log_value["rDealNo"]));     //거래번호

$agspay->startPay();

// 취소 성공 여부
$is_pg_status = $agspay->GetResult("rCancelSuccYn") == "y" ? true : false;

// 취소결과 로그 기록
card_cancle_log_write($rApprNo,iconv("EUC-KR","UTF-8",$agspay->GetResult('rCancelResMsg')));	// 카드거래번호 , 결과 메세지

}

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행