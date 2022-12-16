<?PHP

	include_once "inc.php";
	header("Content-Type: text/html; charset=UTF-8");
	### APIBOX 무통장입금자동통보서비스에 가입하신 뒤 계좌번호등록, 해당은행의 SMS입금통보서비스까지 신청하셔야 콜백URL을 받으실 수 있습니다.
	### 입금자명(한글)이 깨지는 경우, APIBOX > 계좌번호관리메뉴에서 언어셋을 UTF-8 또는 EUC-KR 로 수정하세요.
	### 변수의정의 및 은행코드 등 세부항목에 대한 내용은 APIBOX > 콜백URL메뉴얼에서 확인하실 수 있습니다.
	### 정상적으로 프로세스 진행이 완료되면, 화면에 OK 값을 출력해 주시기바랍니다. (다른 문자열이나 공백값 없이 OK 값만 출력)
	/*
	$_GET[tid];			// 거래고유번호
	$_GET[bankcode];	// 은행코드 (숫자만반환)
	$_GET[account];		// 계좌번호 (숫자만반환)
	$_GET[price];		// 입금금액 (숫자만반환)
	$_GET[name];		// 입금자명
	$_GET[paydt]		// 입금일
	$_GET[uid];			// APIBOX아이디
	$_GET[charset];		// 언어셋
	*/

	//테스트용 샘플코드
	//$_GET[tid]			= "14072314581684"; // 고유거래번호
	//$_GET[bankcode]		= "4"; // 은행코드
	//$_GET[account]		= "11111105000000"; // 계좌번호
	//$_GET[price]		= "49900";
	//$_GET[name]			= "김길동";
	//$_GET[paydt]		= "2018-03-27 20:37";
	//$_GET[uid]			= "test1";
	//$_GET[charset]		= "UTF-8";


	/*
	// 확인해야할 부분
	주문자 이름
	가격 :
	입금일
	*/




	### 접속허용 IP 체크 ############################################################################################################
	// 허용IP리스트
	$r_arrow_ip = array(
		gethostbyname('apibox.kr'),		/* APIBOX 실서버 */
		gethostbyname('whenji.com'),	/* APIBOX 백업서버 */
		'112.219.125.10',	/* 이곳에 귀사의 개발자 컴퓨터 IP주소 등 접속허용IP를 기록하세요 */
		);
	// 허용IP가 아닌경우 차단
	if (!in_array($_SERVER['REMOTE_ADDR'],$r_arrow_ip)) exit;

	// 거래 고유번호가 없을 시
	if(!$_GET[tid]) exit;

	// 무통장 자동입금 확인 서비스가 꺼져있을 시
	if($siteInfo['s_bank_autocheck_use'] <> 'Y') exit;

	// API BOX 아이디와 콜백으로 받은 uid 가 다를 시
	if($siteInfo['s_apibox_id'] <> $_GET['uid']) exit;

	// SSJ : 2018-05-14 동일한 거래번호가 있는지 체크하여 중복등록 방지
	$is_exist = _MQ_result(" select count(*) as cnt from smart_orderbank_log where ob_tid = '". $_GET['tid'] ."' ");
	if($is_exist > 0){
		// 이미 등록된 값이면 OK출력만
		echo "OK"; exit;
	}

	// 콜백으로 받은 정보와 싸이트 내 주문자의 정보가 맞는 지 확인
	// : 입금자명,  결제방식이 무통장이고, 가격, 취소가 없고, 지불이 없고, 무통장 자동입금 상태가 N 이고, PG 사 승인번호가  없고, 무통장 계좌정보가 일치할 시
	$chk_row = _MQ_assoc("
		select * from smart_order
		where
				o_deposit = '".$_GET['name']."' and
				o_paymethod = 'online' and
				o_price_real = '".$_GET['price']."' and
				o_canceled = 'N' and
				o_paystatus = 'N' and
				replace(o_bank , '-' , '' ) like '%". $_GET['account'] ."%'
	");


// 검색된 값이 없다면 exit;
if(count($chk_row) <= 0 ) {

	$sque = "
		insert smart_orderbank_log set
			ob_ordernum				= '',
			ob_tid							= '" . $_GET['tid'] . "',
			ob_content					= '[". date("Y-m-d H:i:s") ."] 입금미확인(신원미상)',
			ob_status						= 'N',
			ob_status_type				= 'ready',
			ob_paydate					= '".$_GET['paydt']."',
			ob_date						= now() ,
			ob_ordername				= '".$_GET['name']."',
			ob_orderprice				= '".$_GET['price']."',
			ob_account					= '[".$ksnet_bank[str_pad($_GET[bankcode], 2, "0", STR_PAD_LEFT)] .'] '. $_GET['account']."'
	";

	_MQ_noreturn($sque);
	$_uid = mysql_insert_id();

	// -- SSJ : 2017-11-21 미확인 입금자 관리에 자동등록 설정 사용시 자동등록 ----
	if($siteInfo['s_online_notice_auto']=='Y')	{
		$_query = "
			insert into smart_online_notice set
				on_view = 'Y'
				,on_name = '". $_GET['name'] ."'
				,on_price = '". $_GET['price'] ."'
				,on_bank = '[".$ksnet_bank[str_pad($_GET[bankcode], 2, "0", STR_PAD_LEFT)] .'] '. $_GET['account']."'
				,on_date = '". date('Y-m-d' , strtotime($_GET['paydt'])) ."'
				,on_obuid = '". $_uid ."'
				,on_rdate = now()
		";
		_MQ_noreturn($_query);
	}
	// -- SSJ : 2017-11-21 미확인 입금자 관리에 자동등록 설정 사용시 자동등록 ----

	echo "OK"; // SSJ: 2017-09-21 처음에 로그를 남기고 로그기록을 통해서 입금처리를 한도록 변경

	exit;

}

if(count($chk_row)  > 1){ // 1개 이상일 경우 관리자가 확인할 수 있도록 따로 로그를 남긴다.


	# 무통장로그  기록
	$sque = "
		insert smart_orderbank_log set
			ob_ordernum				= '',
			ob_tid							= '" . $_GET['tid'] . "',
			ob_content					= '[". date("Y-m-d H:i:s") ."] 입금미확인(중복)',
			ob_status						= 'N',
			ob_status_type				= 'ready',
			ob_paydate					= '".$_GET['paydt']."',
			ob_date						= now() ,
			ob_ordername				= '".$_GET['name']."',
			ob_orderprice				= '".$_GET['price']."',
			ob_account					= '[".$ksnet_bank[str_pad($_GET[bankcode], 2, "0", STR_PAD_LEFT)] .'] '. $_GET['account']."'
	";

	_MQ_noreturn($sque);
	$_uid = mysql_insert_id();

	// -- SSJ : 2017-11-21 미확인 입금자 관리에 자동등록 설정 사용시 자동등록 ----
	if($siteInfo['s_online_notice_auto']=='Y')	{
		$_query = "
			insert into smart_online_notice set
				on_view = 'Y'
				,on_name = '". $_GET['name'] ."'
				,on_price = '". $_GET['price'] ."'
				,on_bank = '[".$ksnet_bank[str_pad($_GET[bankcode], 2, "0", STR_PAD_LEFT)] .'] '. $_GET['account']."'
				,on_date = '". date('Y-m-d' , strtotime($_GET['paydt'])) ."'
				,on_obuid = '". $_uid ."'
				,on_rdate = now()
		";
		_MQ_noreturn($_query);
	}
	// -- SSJ : 2017-11-21 미확인 입금자 관리에 자동등록 설정 사용시 자동등록 ----

	echo "OK"; // SSJ: 2017-09-21 처음에 로그를 남기고 로그기록을 통해서 입금처리를 한도록 변경

	exit;

}else{  // 자동처리될 항목이 1개 일경우 :: 실질적으로 정상처리

	$row = $chk_row[0];

	$OrderBankDiv = explode(',',$row['o_bank']); // 주문 테이블의 무통장입금정보를 활용하여 정보를 나눈다.
	$BankPerson = $OrderBankDiv[1]; // 예금주 명

	// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
	$ordernum = $row['o_ordernum'];
	$_ordernum = $ordernum ;


	# 무통장로그  기록  // 상세기록 // ob_content					= '주문자 : ".$row['o_ordername']." , 입금자 : ".$_GET['name']." 님의 무통장 입금확인 및 결제 완료처리',
	$sque = "
		insert smart_orderbank_log set
			ob_ordernum				= '". $row['o_ordernum'] ."',
			ob_ordername							= '".$_GET['name']."',
			ob_orderprice							= '".$_GET['price']."',
			ob_tid							= '" . $_GET['tid'] . "',
			ob_content					= '[". date("Y-m-d H:i:s") ."] 입금확인(". $ordernum .")',
			ob_status						= 'Y',
			ob_status_type				= 'order',
			ob_paydate					= '".$_GET['paydt']."',
			ob_account					= '[".$ksnet_bank[str_pad($_GET[bankcode], 2, "0", STR_PAD_LEFT)] .'] '. $_GET['account']."',
			ob_date						= now()
	";

	_MQ_noreturn($sque);

	// -- 입금확인처리 --------
	// 필수변수 : $_ordernum;
	include(OD_ADMIN_ROOT."/inc.order_online.payconfirm.php");


	echo "OK";
	exit;
}

?>