<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');

if(in_array($_mode, array('get_excel', 'get_search_excel'))) { // Excel 다운로드 Start
	$toDay = date('YmdHis', time());
	$fileName = '_order_product_list';
	if(!$test) {

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
	}

	# 모드별 쿼리 조건
	if($_mode == 'get_excel') $s_query = " and o.o_canceled = 'N' and o.o_paystatus = 'Y' and npay_order = 'N' and op.op_cancel = 'N' and op.op_uid in ('".implode("', '", $_uid)."') ";
	else $s_query = enc('d', $_search_que);
	if(!$st) $st = 'o_rdate';
	if(!$so) $so = 'desc';
	$res = _MQ_assoc("
		select
			op.*, o.*, p.p_name, p.p_img_list
		from
			smart_order_product as op inner join
			smart_order as o on (o.o_ordernum=op.op_oordernum) left join
			smart_product as p on (p.p_code=op.op_pcode)
		where (1)
			{$s_query}
		order by {$st} {$so}
	");

	# 테이블 스타일
	$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
	$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
	$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
	$br = '<br style="mso-data-placement:same-cell;">';
?>
	<table>
		<thead>
			<th<?php echo $THStyle; ?>>고유번호</th>
			<th<?php echo $THStyle; ?>>주문번호</th>
			<th<?php echo $THStyle; ?>>주문일자</th>
			<th<?php echo $THStyle; ?>>주문자</th>
			<th<?php echo $THStyle; ?>>주문자전화</th>
			<th<?php echo $THStyle; ?>>주문자휴대폰</th>
			<th<?php echo $THStyle; ?>>수령인</th>
			<th<?php echo $THStyle; ?>>수령인전화</th>
			<th<?php echo $THStyle; ?>>수령인휴대폰</th>
			<th<?php echo $THStyle; ?>>배송지우편번호</th>
			<th<?php echo $THStyle; ?>>배송지주소-도로명</th>
			<th<?php echo $THStyle; ?>>배송지주소-지번</th>
			<th<?php echo $THStyle; ?>>상품코드</th>
			<th<?php echo $THStyle; ?>>대표상품명</th>
			<th<?php echo $THStyle; ?>>옵션1</th>
			<th<?php echo $THStyle; ?>>옵션2</th>
			<th<?php echo $THStyle; ?>>옵션3</th>
			<th<?php echo $THStyle; ?>>판매단가</th>
			<th<?php echo $THStyle; ?>>수량</th>
			<th<?php echo $THStyle; ?>>금액</th>
			<th<?php echo $THStyle; ?>>배송상태</th>
			<th<?php echo $THStyle; ?>>택배사</th>
			<th<?php echo $THStyle; ?>>송장번호</th>
			<th<?php echo $THStyle; ?>>배송시문구</th>
			<th<?php echo $THStyle; ?>>관리자메모</th>
		</thead>
		<tbody>
			<?php foreach($res as $k=>$v) { ?>
				<tr>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_uid']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_oordernum']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_rdate']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_oname']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_otel']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_ohp']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rname']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rtel']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rhp']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rzonecode']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_raddr_doro'].' '.$v['o_raddr2']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_raddr1'].' '.$v['o_raddr2']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_pcode']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['p_name']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_option1']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_option2']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_option3']; ?></td>
					<td<?php echo $TDStyle2; ?>><?php echo $v['op_price']; ?></td>
					<td<?php echo $TDStyle2; ?>><?php echo $v['op_cnt']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_price']*$v['op_cnt']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_sendstatus']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_sendcompany']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_sendnum']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_content']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_admcontent']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php
}
else if($_mode == 'modify_sendstatus') { // 배송상태변경
	if(count($_uid) <= 0) error_alt('처리할 주문을 1건 이상 선택 바랍니다.');
	if(!$select_sendstatus) error_alt('배송상태를 선택하세요.');

	// 처리 가능한 항목 추출
	$_uid_tmp = array();
	foreach($_uid as $k=>$v) {
		if(IN_ARRAY($select_sendstatus , array('배송대기' , '배송준비'))) $_uid_tmp[] = $v; // JJC : 배송대기/배송준비 시 택배사/송장번호 입력없이 변경 가능
		else if($_sendcompany[$v] && $_sendnum[$v]) $_uid_tmp[] = $v;
	}
	if(count($_uid_tmp) <= 0) error_alt('처리할 항목이 없습니다.\\n\\n택배사 또는 송장번호를 확인 바랍니다.');
	$r = _MQ_assoc("select * from smart_order_product where op_uid in ('".implode("', '", $_uid_tmp)."')");


	// 배송처리
	$op_oordernum = array();
	$arr_ordernum_sms = array();
	foreach($r as $k=>$v) {
		$op_oordernum[$v['op_oordernum']] = $v['op_oordernum'];
		$que_tmp = '';
		if(in_array($select_sendstatus, array('배송완료', '배송중')) && $select_sendstatus <> $v['op_sendstatus'] && !in_array($v['op_sendstatus'] , array('배송완료', '배송중'))){
			$que_tmp = " ,  op_senddate = now() ";
		}

		// {{{배송완료일추가}}}
		if( $select_sendstatus == '배송완료' && $select_sendstatus <> $v['op_sendstatus'] ){ $que_tmp .= " ,op_completedate = now() ";  }

		_MQ_noreturn(" update smart_order_product set op_sendstatus = '{$select_sendstatus}', op_sendcompany = '{$_sendcompany[$v['op_uid']]}', op_sendnum = '{$_sendnum[$v['op_uid']]}' {$que_tmp} where op_uid = '{$v['op_uid']}' ");


		if( !IN_ARRAY($v['op_sendstatus'] , array('배송중' , '배송완료')) && IN_ARRAY($select_sendstatus , array('배송중' , '배송완료')) ) {
			if(!$arr_ordernum_sms[$v['op_oordernum']] ) {
				$order_info = _MQ("select * from smart_order where o_ordernum = '{$v['op_oordernum']}' ");
				// 상품 배송시 주문회원에게 문자 발송
				if($order_info['o_otel'] || $order_info['o_ohp']) {
					// 문자 발송
					$sms_to = $order_info['o_ohp'] ? $order_info['o_ohp'] : $order_info['o_otel'];
					$tmp = _MQ(" select count(*) as cnt from smart_order_product where op_cancel!='Y' and op_oordernum = '".$v['op_oordernum']."' ");// 마지막 부분취소인지 체크
					if($tmp['cnt']==1) $smskbn = "order_cancel"; // 마지막 부분취소일 경우 주문 전체 취소  // 문자 발송 유형
					else $smskbn = "order_cancel_part";  // 문자 발송 유형
					$sms_pname = trim($v['op_pname']) . implode(" " , array_filter(array(' '.$v['op_option1'],$v['op_option2'],$v['op_option3'])));
					$arr_sms_replace = array('{주문번호}'=>$k, '{주문상품명}'=>$sms_pname, '{택배사}'=>$_sendcompany[$v['op_uid']], '{운송장번호}'=>$_sendnum[$v['op_uid']], '{배송일}'=>date('Y-m-d'));
					shop_send_sms($sms_to, 'delivery', $v['op_oordernum']);
				}
				$arr_ordernum_sms[$v['op_oordernum']] ++;
			}
		}
	}


	// 주문상테 업데이트
	if(count($op_oordernum) > 0) {
		foreach($op_oordernum as $k=>$v) {

			// 주문상태 업데이트
			order_status_update($v);
		}
	}


	// 배송중 메일 발송
	if($select_sendstatus == '배송중') {
		$OrderData = array();
		foreach($r as $k=>$v) {
			$OrderData[$v['op_oordernum']][] = $v['op_uid'];
		}
		foreach($OrderData as $k=>$v) {
			$_SendMode = 'order';
			$or = _MQ(" select * from smart_order where o_ordernum = '{$k}' ");
			$opr = _MQ_assoc("
						select op.* , p.p_name, p.p_img_list , p.p_code
						from smart_order_product as op
						left join smart_product as p on ( p.p_code=op.op_pcode )
						where op_uid in ('".implode("', '", $v)."') and op.op_cancel = 'N'
					");
			include(OD_MAIL_ROOT.'/shop.order.mail_delivery.php');
			$_title = "[{$siteInfo['s_adshop']}] 주문하신 상품이 발송되었습니다!";
			$_content = get_mail_content($mailing_app_content); // 메인 본문조합
			mailer($or['o_oemail'], $_title, $_content);
		}
	}

	error_frame_reload('적용하였습니다.');
}
else if($_mode == 'ins_excel') { // 엑셀업로드
	// 처리 가능 항목 정리
	$EData = array();
	foreach($w_check as $k=>$v) {
		if($v != 'Y') continue; // 처리가 N이면 제외
		$EData[$k] = array(
			'_sendstatus'=>($_sendstatus[$k]?$_sendstatus[$k]:'배송중'),
			'_sendcompany'=>$_sendcompany[$k],
			'_sendnum'=>$_sendnum[$k]
		);
	}
	if(count($EData) <= 0) $EData = array();

	// 배송처리
	$op_oordernum = array();
	$arr_ordernum_sms = array();
	foreach($EData as $k=>$v) {
		$r = _MQ("select * from smart_order_product where op_uid = '{$k}' ");
		if(!$r['op_uid']) continue;
		if($v['_sendstatus'] == '배송중') $op_oordernum[$k] = $k;
		$que_order = '';
		$que_product = '';

		if($v['_sendstatus'] == '배송중' || $v['_sendstatus'] == '배송완료') {
			if($r['op_sendstatus'] == '배송대기' || $r['op_sendstatus'] == '배송준비' || !$r['op_sendstatus']) {
				$que_product = " ,  op_senddate = now() ";
			}
			// {{{배송완료일추가}}}
			if( $r['op_sendstatus'] == '배송중' && $v['_sendstatus'] == '배송완료'){  $que_product = " ,  op_completedate = now() ";  }
		}
		else {
			$que_product = " ,  op_senddate = '0000-00-00' ,  op_completedate = '0000-00-00'  "; // {{{배송완료일추가}}}
		}

		// 주문상품정보 수정
		_MQ_noreturn("
			update smart_order_product set
				  op_sendcompany = '{$v['_sendcompany']}'
				, op_sendnum = '{$v['_sendnum']}'
				, op_sendstatus = '{$v['_sendstatus']}'
				  {$que_product}
			where op_oordernum='{$r['op_oordernum']}' AND op_uid ='{$r['op_uid']}'
		");

		// 문자발송
		if($v['_sendstatus'] == '배송대기' && ($select_sendstatus == '배송중' || $select_sendstatus == '배송완료')) {
			if(!$arr_ordernum_sms[$k]) {
				if($r['op_oordernum']) $order_info = $r['op_oordernum'];
				else $order_info = _MQ("select * from smart_order where o_ordernum = '{$r['op_oordernum']}' ");
				// 상품 배송시 주문회원에게 문자 발송
				if($order_info['o_otel'] || $order_info['o_ohp']) {
					// 문자 발송
					$sms_to = $order_info['o_ohp'] ? $order_info['o_ohp'] : $order_info['o_otel'];
					$tmp = _MQ(" select count(*) as cnt from smart_order_product where op_cancel!='Y' and op_oordernum = '".$k."' ");// 마지막 부분취소인지 체크
					if($tmp['cnt']==1) $smskbn = "order_cancel"; // 마지막 부분취소일 경우 주문 전체 취소 // 문자 발송 유형
					else $smskbn = "order_cancel_part";  // 문자 발송 유형
					$sms_pname = trim($order_info['op_pname']) . implode(" " , array_filter(array(' '.$order_info['op_option1'],$order_info['op_option2'],$order_info['op_option3'])));
					$arr_sms_replace = array('{주문번호}'=>$k, '{주문상품명}'=>$sms_pname, '{택배사}'=>$v['_sendcompany'], '{운송장번호}'=>$v['_sendnum'], '{배송일}'=>date('Y-m-d'));
					shop_send_sms($sms_to, 'delivery', $arr_sms_replace);
				}
				$arr_ordernum_sms[$k]++;
			}
		}

		// 주문상태 업데이트
		order_status_update($r['op_oordernum']);
	}

	// 배송중 메일 발송
	if(count($op_oordernum) > 0) {
		$OrderData = array();
		foreach($op_oordernum as $k=>$v) {
			$OrderData[$v] = $v;
		}
		foreach($OrderData as $k=>$v) {
			$_SendMode = 'order';
			$or = _MQ(" select * from smart_order where o_ordernum = '{$v}' ");
			$opr = _MQ_assoc("
						select op.* , p.p_name, p.p_img_list , p.p_code
						from smart_order_product as op
						left join smart_product as p on ( p.p_code=op.op_pcode )
						where op_oordernum = '{$v}' and op.op_cancel = 'N'
					");
			include(OD_MAIL_ROOT.'/shop.order.mail_delivery.php');
			$_title = "[{$siteInfo['s_adshop']}] 주문하신 상품이 발송되었습니다!";
			$_content = get_mail_content($mailing_app_content); // 메인 본문조합
			mailer($or['o_oemail'], $_title, $_content);
		}
	}

	error_loc_msg('_order_product.list.php', '적용하였습니다.');
}