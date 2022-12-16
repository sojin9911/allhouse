<?PHP
	include_once("./inc.php");


	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("modify") ) ) {

		// --사전 체크 ---
		$_memtype = nullchk($_memtype , '회원타입을 선택해주시기 바랍니다.');
		$_mid = nullchk($_mid , '주문자 아이디를 입력해주시기 바랍니다.');
		$_oname = nullchk($_oname , '주문자명을 입력해주시기 바랍니다.');
		$_ohp = nullchk($_ohp , '주문자 휴대폰번호를 입력해주시기 바랍니다.');
		$_oemail = nullchk($_oemail , '주문자 이메일 주소를 입력해주시기 바랍니다.');
		$_rname = nullchk($_rname , '받는 분 이름을 입력해주시기 바랍니다.');
		$_rhp = nullchk($_rhp , '받는 분 휴대폰번호를 입력해주시기 바랍니다.');
		$_rzonecode = nullchk($_rzonecode , '우편번호 찾기 버튼을 눌러 배송지 주소(우편번호)를 입력해주시기 바랍니다.');
		$_raddr_doro = nullchk($_raddr_doro , '우편번호 찾기 버튼을 눌러 배송지 주소를 입력해주시기 바랍니다.');
		$_raddr2 = nullchk($_raddr2 , '배송지 주소를 입력해주시기 바랍니다.');
		// 현금 영수증 신청시
		if($_get_tax == 'Y' && $_paymethod == 'online'){
			$_tax_TradeUsage = nullchk($_tax_TradeUsage , '거래용도를 선택해주시기 바랍니다.');
			$_tax_TradeMethod = nullchk($_tax_TradeMethod , '신분확인번호 구분을 선택해주시기 바랍니다.');
			$_tax_IdentityNum = nullchk($_tax_IdentityNum , '신분확인번호를 입력해주시기 바랍니다.');
			$_tax_IdentityNum = onedaynet_encode($_tax_IdentityNum); // 암호화
		}
		// --사전 체크 ---

		// 우편번호
		$_rpost = implode('-', array_filter(array($_rpost1, $_rpost2)));

		// --query 사전 준비 ---
		$sque = "
			 o_memtype = '". $_memtype ."'
			, o_mid = '". $_mid ."'
			, o_oname = '". $_oname ."'
			, o_otel = '". $_otel ."'
			, o_ohp = '". $_ohp ."'
			, o_oemail = '". $_oemail ."'
			, o_rname = '". $_rname ."'
			, o_rtel = '". $_rtel ."'
			, o_rhp = '". $_rhp ."'
			, o_rpost = '". $_rpost ."'
			, o_raddr1 = '". $_raddr1 ."'
			, o_raddr2 = '". $_raddr2 ."'
			, o_raddr_doro = '". $_raddr_doro ."'
			, o_content = '". $_content ."'
			, o_admcontent = '". $_admcontent ."'
			, o_deposit = '". $_deposit ."'
			, o_bank = '". $_bank ."'
			, o_get_tax = '". $_get_tax ."'
			, o_rzonecode = '". $_rzonecode ."'
		";
		// --query 사전 준비 ---

		// 현금 영수증 신청시
		if($_get_tax == 'Y'){
			$sque .= "
				, o_tax_TradeUsage				= '". $_tax_TradeUsage ."'
				, o_tax_TradeMethod			= '". $_tax_TradeMethod ."'
				, o_tax_IdentityNum				= '". $_tax_IdentityNum ."'
			";
		}

		// SSJ : 주문/결제 통합 패치 : 2021-02-24
		if(isset($_moneyback_comment)){
			$sque .= " , o_moneyback_comment = '환불계좌: ". addslashes($_moneyback_comment) ."' ";
		}
	}
	// - 입력수정 사전처리 ---


	if( $_ordernum ) {
		$que = " select * from smart_order where o_ordernum='" . $_ordernum . "' ";
		$row = _MQ($que);
	}



	// - 모드별 처리 ---
	switch( $_mode ){


		// 주문수정
		case "modify":

			$que = " update smart_order set $sque where o_ordernum='{$_ordernum}' ";
			_MQ_noreturn($que);

			// 주문상태 업데이트
			order_status_update($_ordernum);

			error_loc("_order.form.php" . URI_Rebuild('?', array('view'=>$view, '_mode'=>$_mode, '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)));
			break;


		// 입금확인
		case "payconfirm":

			// $_ordernum 적용
			// 입금완료처리 :: 접수대기 => 접수완료
			include(dirname(__file__).'/inc.order_online.payconfirm.php');

			error_loc("_order.form.php" . URI_Rebuild('?', array('view'=>$view, '_mode'=>'modify', '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)));
			break;


		// 입금취소
		case "paycancel":

			// $_ordernum 적용
			// 입금완료처리 :: 접수완료 => 접수대기
			include(dirname(__file__).'/inc.order_online.paycancel.php');

			error_loc("_order.form.php" . URI_Rebuild('?', array('view'=>$view, '_mode'=>'modify', '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)));
			break;



		# -- 2016-11-28 LCY :: 무통장다수개처리
		case "select_paystatus":

			$_paystatus = 'Y'; // 결제확인
			$_apply_point = 'Y'; // 연동확인
			$_status = '결제확인'; // 상태표기명
			$exec_count = 0; // 무통장 처리건수
			if(count($chk_ordernum) > 0) {

				foreach($chk_ordernum as $ordernum => $val) {

					$_ordernum = $ordernum; // 주문번호 변수 제공

					$que = " select * from smart_order where o_ordernum='" . $_ordernum . "' AND o_paymethod = 'online' ";
					$row = _MQ($que);

					if(count($row) < 1) { continue; }

					# -- 저장된 결제상태가 다르고, 결제상태가 N 이고, 상태에 따른 표기가 접수대기
					if( ($row['o_paystatus'] <> $_paystatus) && $row['o_paystatus'] == 'N' && $row['o_status'] == '접수대기' ){

						# -- 무통장입금확인 카운터를 증가시킨다.
						$exec_count ++;

						// $_ordernum 적용
						// 입금완료처리 :: 접수완료 => 접수대기
						$isMultiSms = 'Y'; // 문자 일괄 발송
						include(dirname(__file__).'/inc.order_online.payconfirm.php');

					} # -- end :: if( ($row['o_paystatus'] <> $_paystatus) && $row['o_paystatus'] == 'N' && $row['o_status'] == '접수대기' ){
				} # -- end :: foreach($chk_ordernum as $ordernum => $val) {
			} # -- end :: if(count($chk_ordernum) > 0) {

			// 2020-04-07 SSJ :: 문자 일괄 발송
			if(count($arr_send) > 0){
				shop_send_sms_multi($arr_send);
				unset($arr_send);
			}

			error_loc_msg("_order.list.php?".enc('d' , $_PVSC) , "무통장입금확인건 총 [".$exec_count."] 건이 처리되었습니다.");
			break;
		# -- 2016-11-28 LCY :: 무통장다수개처리



		// 주문삭제
		case "delete":
			_MQ_noreturn("delete from smart_order_product where op_oordernum='{$_ordernum}' ");
			//_MQ_noreturn("delete from smart_order_company where ocp_oordernum='{$_ordernum}' ");
			_MQ_noreturn("delete from smart_order where o_ordernum='{$_ordernum}' ");
			error_loc("_order.cancel_list.php?".enc('d' , $_PVSC));
			break;




		// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----
		// 주문취소
		case "cancel":

			// --- JJC : 부분취소 개선 : 2021-02-10 ---
			// 부분취소 요청 및 완료 건 있는지 확인
			$chk = _MQ("SELECT COUNT(*) AS cnt FROM smart_order_product WHERE op_oordernum = '{$_ordernum}' AND op_cancel != 'N'");
			if($chk['cnt'] > 0 ) {error_msg("부분취소 하였거나 요청 중인 건이 있을 경우\\n주문취소를 실행할 수 없습니다.\\n\\n부분취소 요청을 삭제하신 후 취소를 진행하시거나\\n주문상품을 모두 부분취소하시기 바랍니다.");}
			// --- JJC : 부분취소 개선 : 2021-02-10 ---


			$r = _MQ("
				select o.* , oc.oc_tid, oc.oc_uid, ( select ool_tid from smart_order_onlinelog where ool_ordernum=o.o_ordernum order by ool_uid desc limit 1 ) as ool_tid
				from smart_order as o
				left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum AND oc.oc_tid !='')
				where o.o_ordernum='".$_ordernum."'
			");


			// 공통취소
			//		넘길변수
			//			-> 취소위치 : _loc (관리자일 경우 - admin / 사용자일 경우 - user)
			//			-> 주문번호 : _ordernum
			//			-> 주문정보 : $osr
			//		return 정보
			//			-> 성공여부 : cancel_status = Y/N
			//			-> 메시지 : cancel_msg
			$_loc = "admin";
			//$_ordernum = $_ordernum;
			$osr = $r ;
			include_once(OD_PROGRAM_ROOT."/pg.cancel.inc.php");

			if($cancel_status && $cancel_msg){
				error_loc_msg(($_submode =='_cancel_order' ? "_cancel_order.list.php?" : "_order.list.php?") . enc('d' , $_PVSC) , $cancel_msg );
			}


			break;
		// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----



		// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----
		// 일괄취소
		case "mass_cancel":


			// - 적용된 포인트, 쿠폰적용 취소 ---
			$arr_error = array();// --- JJC : 부분취소 개선 : 2021-02-10 ---
			if( sizeof($chk_ordernum) >0 )	{

				foreach($chk_ordernum as $k=>$v){

					// --- JJC : 부분취소 개선 : 2021-02-10 ---
					// 부분취소 요청 및 완료 건 있는지 확인
					$chk = _MQ("SELECT COUNT(*) AS cnt FROM smart_order_product WHERE op_oordernum = '{$k}' AND op_cancel != 'N'");
					if($chk['cnt'] > 0 ) {
						$arr_error[] = '['.  $k  .'] 부분취소 하였거나 요청 중인 건이 있을 경우 주문취소를 실행할 수 없습니다';
						continue;
					}
					// --- JJC : 부분취소 개선 : 2021-02-10 ---

					$r = _MQ("
						select o.* , oc.*
						from smart_order as o
						left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum AND oc.oc_tid !='')
						where o.o_ordernum='".$k."'
					");

					if($_submode =='_cancel_order'){ // 환불완료된 주문만 취소
						if($r['o_moneyback_status'] <> 'complete'){continue;}
					}

					// 공통취소
					//		넘길변수
					//			-> 취소위치 : _loc (관리자일 경우 - admin / 사용자일 경우 - user)
					//			-> 주문번호 : _ordernum
					//			-> 주문정보 : $osr
					//		return 정보
					//			-> 성공여부 : cancel_status = Y/N
					//			-> 메시지 : cancel_msg
					$_loc = "admin";
					$_ordernum = $k ;
					$osr = $r ;
					include(OD_PROGRAM_ROOT."/pg.cancel.inc.php");

					// 실패일 경우 우선 오류 처리
					if($cancel_status == "N" && $cancel_msg){
                        $arr_error[] = '['.  $_ordernum  .'] '.$cancel_msg;
					}

				}
			}else{
                error_msg('결제 취소할 주문이 선택되지 않았습니다.');
            }

			// 2020-04-07 SSJ :: 문자 일괄 발송
			if(count($arr_send) > 0){
				shop_send_sms_multi($arr_send);
				unset($arr_send);
            }

            // 에러 메세지 추출
            $msg = '결제 취소요청이 완료되었습니다.';
            if(count($arr_error) > 0){
                if(count($chk_ordernum) == count($arr_error)){
                    $msg = '결제 취소 시 오류가 발생하였습니다.\\n\\n'.implode("\\n", $arr_error);// --- JJC : 부분취소 개선 : 2021-02-10 ---
                }else{
                    $msg = '총 ['.count($chk_ordernum).']건의 주문중 ['.(count($chk_ordernum) - count($arr_error)).']건의 결제가 취소 되었습니다.\\n\\n- 취소실패 ['.count($arr_error).']건 -\\n'.implode("\\n", $arr_error);
                }
            }

			if($_submode =='_cancel_order'){error_loc_msg("_cancel_order.list.php?".enc('d' , $_PVSC) , $msg);}
			else{error_loc_msg("_order.list.php?".enc('d' , $_PVSC) , $msg);}

			break;
		// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----


		// 일괄 수정
		case "mass_modify":

			$arr_send = array(); // 2020-04-07 SSJ :: 문자 일괄 발송

			// -- 사전 체크 ---
			if(sizeof($chk_ordernum) == 0) {
				error_msg("수정할 주문이 선택되지 않았습니다.");
			}
			// -- 사전 체크 ---

			// -- query 사전 준비 ---
			$arr_query_field = array();
			if($_paystatus) {
				$arr_query_field[] = " o_paystatus='". $_paystatus ."' ";
			}
			if($_canceled) {
				$arr_query_field[] = " o_canceled='". $_canceled ."' ";
			}
			if($_status) {
				$arr_query_field[] = " o_status='". $_status ."' ";
			}

			if(sizeof($arr_query_field) > 0) {
				// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----
				$sque = " select o_ordernum , o_oemail from smart_order where o_ordernum in ('".implode("','" , array_keys($chk_ordernum))."') ";
				$sr = _MQ_assoc($sque);
				foreach( $sr as $k=>$v){
					$_ordernum = $v['o_ordernum'];
					if($_paystatus == "Y" && $_apply_point == "Y"){

						// 공통결제
						//		넘길변수
						//			-> 주문번호 : $ordernum
						//			return 정보
						//					-> 성공여부 : pay_status = Y/N
						//					-> 메시지 : pay_msg
						$ordernum = $_ordernum;
						$isMultiSms = 'Y'; // 문자 일괄 발송
						include(OD_PROGRAM_ROOT."/shop.order.result.pro.php"); // ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----
						if($pay_status == 'N') {error_msg($pay_msg);}

					}
					elseif($_paystatus == "N" && $_apply_point == "N"){

						_MQ_noreturn(" update smart_order set ". implode(" , " , array_values($arr_query_field)) ." where o_ordernum = '". $_ordernum ."' ");

						// 제공변수 : $_ordernum
						include(OD_PROGRAM_ROOT."/shop.order.pointdel_pro.php");
						// 상품 재고 증가 및 판매량 차감 : $_ordernum
						include(OD_PROGRAM_ROOT."/shop.order.salecntdel_pro.php");
					}

				}
				// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----

				// 2020-04-07 SSJ :: 문자 일괄 발송
				if(count($arr_send) > 0){
					shop_send_sms_multi($arr_send);
					unset($arr_send);
				}

				error_loc("_order.list.php?".enc('d' , $_PVSC));
			}
			else {
				error_msg("항목을 한개이상 수정해 주셔야 합니다.");
			}
			// -- query 사전 준비 ---
			break;


		case "get_excel" :
		case "get_search_excel" : // LCY : 2022-02-15 : 검색엑셀다운로드 기능추가
			

			// LCY : 2022-02-15 : 검색엑셀다운로드 기능추가 --------------------- {
			if($_mode == 'get_search_excel') {
				$s_query = enc('d', $_search);
			}
			else {
				$_ordernum_array = array();
				foreach($chk_ordernum as $k=>$v) {$_ordernum_array[] = $k;}

				// LCY : 2022-06-14 : 선택엑셀 보완패치
				$s_query = " from smart_order as o left join smart_individual as indr on (indr.in_id=o.o_mid)  where o_ordernum in ('".implode("','",$_ordernum_array)."') " ;
			}

			$r = _MQ_assoc("select
											o_ordernum,
											if(o_memtype='Y',o_mid,'비회원'),
											o_oname,
											o_otel,
											o_ohp,
											o_oemail,
											o_rname,
											o_rtel,
											o_rhp,
											o_rzonecode,
											/* o_rpost, -- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -- */
											o_raddr_doro,
											o_raddr2,
											o_raddr1,
											o_content,
											o_admcontent,
											o_price_real,
											o_price_total,
											o_price_delivery,
											o_price_supplypoint,
											o_price_usepoint,
											o_price_coupon_individual,
											o_coupon_individual_uid,
											o_price_coupon_product,
											o_promotion_code,
											o_promotion_price,
											o_paymethod,
											o_paystatus,
											o_canceled,
											o_status,
											o_bank,
											o_deposit,
											o_rdate
											".$s_query."
											");

			// LCY : 2022-02-15 : 검색엑셀다운로드 기능추가 --------------------- }

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=order_$toDay.xls");

			$excel_print[] = "
<td>주문번호</td>
<td>주문자아이디</td>
<td>주문자명</td>
<td>주문자-전화</td>
<td>주문자-휴대폰</td>
<td>주문자-이메일</td>
<td>수신자명</td>
<td>수신자-전화</td>
<td>수신자-휴대폰</td>
<td>수신자-우편번호</td>
<!-- <td>수신자-우편번호</td> -- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -- -->
<td>수신자-도로명주소</td>
<td>수신자-상세주소</td>
<td>수신자-지번주소</td>
<td>배송시문구</td>
<td>관리자메모</td>
<td>실결제가</td>
<td>구매총액</td>
<td>배송액</td>
<td>적립금 제공액</td>
<td>적립금 사용액</td>
<td>회원할인쿠폰사용액</td>
<td>회원할인쿠폰번호</td>
<td>보너스쿠폰사용액</td>
<td>프로모션코드</td>
<td>프로모션코드사용액</td>
<td>결제방식</td>
<td>결제상태</td>
<td>결제취소상태</td>
<td>주문상태</td>
<td>무통장입금시-입금계좌정보</td>
<td>무통장입금시-입금자명</td>
<td>주문일시</td>
			";

			foreach($r as $k => $v) $excel_print[] = "<td>".implode("</td><td>",$v)."</td>";

			?>
			<html>
			<head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<title></title>
			</head>
			<body>
				<table border=1>
					<tr>
					<?=implode("</tr><tr>",$excel_print)?>
					</tr>
				</table>
			</body>
			</html>
			<?

			exit;

				break;

        // 수기 주문 - 수량 변경
        case "modify_op_cnt" :
            $_ordernum = $_GET["_ordernum"];
            $_uid = $_GET["_uid"];
            $_op_cnt = $_GET["_op_cnt"];

            if($_ordernum && $_uid) {
                if (!$_op_cnt) $_op_cnt = 0;
                _MQ_noreturn(" update smart_order_product set op_cnt = '{$_op_cnt}' where op_oordernum='{$_ordernum}' and op_uid='{$_uid}' ");
                echo " update smart_order_product set op_cnt = '{$_op_cnt}' where op_oordernum='{$_ordernum}' and op_uid='{$_uid}' ";
            }

            error_frame_reload('변경하였습니다.');
            
    }
	// - 모드별 처리 ---

	exit;
?>