<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');

if(in_array($_mode, array('get_excel', 'get_search_excel'))) { // Excel 다운로드 Start
	$toDay = date('YmdHis', time());
	$fileName = '_order_delivery_list';
	if(!$test) {

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
	}

	# 모드별 쿼리 조건
	if($_mode == 'get_excel') $s_query = " and o.o_canceled != 'Y' and o.o_paystatus = 'Y' and `npay_order` = 'N' and o.o_ordernum in ('".implode("', '", $_uid)."') ";
	else $s_query = enc('d', $_search_que);
	if(!$st) $st = 'o_rdate';
	if(!$so) $so = 'desc';

	# 쿼리
	$que = "
		select
			o.*,
			(
				select
					concat(op.op_pname, '|', if(op.op_option1 != '', op.op_option1, ''), if(op.op_option2 != '', concat(' ', op.op_option2), ''), if(op.op_option3 != '', concat(' ', op.op_option3), ''), ' ', op.op_cnt, '개')
				from
					smart_order_product as op left join
					smart_product as p on (p.p_code=op.op_pcode)
				where
					op.op_oordernum = o.o_ordernum
				order by op.op_uid asc
				limit 0 , 1
			) as app_pname,
			(select count(*) from smart_order_product as op2 where op2.op_oordernum = o.o_ordernum) as app_pcnt,
			(select count(*) from smart_order_product as op3 where op3.op_oordernum = o.o_ordernum and op3.op_cancel != 'N') as app_cancel_pcnt
		from
			smart_order as o
		where (1)
			{$s_query} and
			(select count(*) from smart_order_product as op2 where op2.op_oordernum = o.o_ordernum)-(select count(*) from smart_order_product as op3 where op3.op_oordernum = o.o_ordernum and op3.op_cancel != 'N') > 0
		order by {$st} {$so}
	";
	$res = _MQ_assoc($que);


	# 테이블 스타일
	$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
	$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
	$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
	$br = '<br style="mso-data-placement:same-cell;">';
?>
	<table>
		<thead>
			<tr>
				<th<?php echo $THStyle; ?>>주문번호</th>
				<th<?php echo $THStyle; ?>>주문일자</th>
				<th<?php echo $THStyle; ?>>주문자</th>
				<th<?php echo $THStyle; ?>>주문자전화</th>
				<th<?php echo $THStyle; ?>>주문자휴대폰</th>
				<th<?php echo $THStyle; ?>>수령인</th>
				<th<?php echo $THStyle; ?>>수령인전화</th>
				<th<?php echo $THStyle; ?>>수령인휴대폰</th>
				<th<?php echo $THStyle; ?>>배송지우편번호</th>
				<th<?php echo $THStyle; ?>>배송지주소-지번</th>
				<th<?php echo $THStyle; ?>>배송지주소-도로명</th>
				<th<?php echo $THStyle; ?>>상품정보</th>
				<th<?php echo $THStyle; ?>>실결제가</th>
				<th<?php echo $THStyle; ?>>배송상태</th>
				<th<?php echo $THStyle; ?>>택배사</th>
				<th<?php echo $THStyle; ?>>송장번호</th>
				<th<?php echo $THStyle; ?>>배송시문구</th>
				<th<?php echo $THStyle; ?>>관리자메모</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($res as $k=>$v) {
				$app_pname = str_replace('|', ' / ', $v['app_pname']);
				$v['app_pcnt'] = $v['app_pcnt']-$v['app_cancel_pcnt'];
			?>
				<tr>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_ordernum']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo date('Y-m-d', strtotime($v['o_rdate'])); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_oname']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_otel']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_ohp']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rname']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rtel']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rhp']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_rpost']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_raddr1'].' '.$v['o_raddr2']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_raddr_doro'].' '.$v['o_raddr2']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo trim($app_pname).($v['app_pcnt'] > 1?' 외 '.($v['app_pcnt']-1).'개':null); ?></td>
					<td<?php echo $TDStyle2; ?>><?php echo $v['o_price_real']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_sendstatus']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_sendcompany']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_sendnum']; ?></td>
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
	$_oordernum = array();
	foreach($_uid as $k=>$v) {
		if(IN_ARRAY($select_sendstatus , array('구매발주' , '배송준비'))) $_oordernum[] = $v; // JJC : 구매발주/배송준비 시 택배사/송장번호 입력없이 변경 가능
		else if($_sendcompany[$v] && $_sendnum[$v]) $_oordernum[] = $v;
	}
	if(count($_oordernum) <= 0) error_alt('처리할 항목이 없습니다.\\n\\n택배사 또는 송장번호를 확인 바랍니다.');
	$r = _MQ_assoc("
		select
			op.* ,o.o_otel , o.o_ohp
		from smart_order_product as op
		INNER JOIN smart_order as o ON (o.o_ordernum=op.op_oordernum)
		where
			op.op_oordernum in ('".implode("', '", $_oordernum)."')
	");

	// 배송처리
	$op_oordernum = array();
	$arr_ordernum_sms = array();
	$arr_send = array(); // 2020-04-07 SSJ :: 문자 일괄 발송
	foreach($r as $k=>$v) {
		$op_oordernum[$v['op_oordernum']] = $v['op_oordernum'];
		$que_tmp = '';
		if(in_array($select_sendstatus, array('배송완료', '배송중')) && $select_sendstatus <> $v['op_sendstatus'] && !in_array($v['op_sendstatus'] , array('배송완료', '배송중'))){
			 $que_tmp = " ,  op_senddate = now() ";
		}

		// {{{배송완료일추가}}}
		if( $select_sendstatus == '배송완료' && $select_sendstatus <> $v['op_sendstatus'] ) $que_tmp .= ", op_completedate = now() ";

		_MQ_noreturn(" update smart_order_product set op_sendstatus = '{$select_sendstatus}', op_sendcompany = '{$_sendcompany[$v['op_oordernum']]}', op_sendnum = '{$_sendnum[$v['op_oordernum']]}' {$que_tmp} where op_uid = '{$v['op_uid']}' ");


		if( !IN_ARRAY($v['op_sendstatus'] , array('배송중' , '배송완료')) && IN_ARRAY($select_sendstatus , array('배송중' , '배송완료')) ) {
			if(!$arr_ordernum_sms[$v['op_oordernum']] ) {
				// 상품 배송시 주문회원에게 문자 발송
				if($v['o_otel'] || $v['o_ohp']) {
					// 문자 발송
					$sms_to = $v['o_ohp'] ? $v['o_ohp'] : $v['o_otel'];
					$sms_pname = trim($v['op_pname']) . implode(" " , array_filter(array(' '.$v['op_option1'],$v['op_option2'],$v['op_option3'])));
					$arr_sms_replace = array('{주문번호}'=>$v['op_oordernum'], '{주문상품명}'=>$sms_pname, '{택배사}'=>$_sendcompany[$v['op_oordernum']], '{운송장번호}'=>$_sendnum[$v['op_oordernum']], '{배송일}'=>date('Y-m-d'));
					// shop_send_sms($sms_to, 'delivery', $arr_sms_replace);
					$arr_send[] = array('to'=>$sms_to, 'type'=>'delivery', 'ordernum'=>$arr_sms_replace); // 2020-04-07 SSJ :: 문자 일괄 발송
				}
				$arr_ordernum_sms[$v['op_oordernum']] ++;
			}
		}
	}

	// 2020-04-07 SSJ :: 문자 일괄 발송
	if(count($arr_send) > 0){
		shop_send_sms_multi($arr_send);
		unset($arr_send);
	}


	// 주문상테 업데이트
	if(count($op_oordernum) > 0) {
		foreach($op_oordernum as $k=>$v) {

			// 주문정보 업데이트
			_MQ_noreturn(" update smart_order set o_sendcompany = '{$_sendcompany[$v]}', o_sendnum = '{$_sendnum[$v]}' where o_ordernum='{$v}' "); // , o_sendstatus = '{$select_sendstatus}' 는 order_status_update($v)에서 자동 처리

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
			$or = _MQ(" select * from `smart_order` where `o_ordernum` = '{$k}' ");
			$opr = _MQ_assoc("
						select op.* , p.p_name, p.p_img_list , p.p_code, p.p_img_list_square
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
	$arr_send = array(); // 2020-04-07 SSJ :: 문자 일괄 발송
	foreach($EData as $k=>$v) {
		$r = _MQ("select * from smart_order where o_ordernum = '{$k}' ");
		if(!$r['o_ordernum']) continue;
		if($v['_sendstatus'] == '배송중') $op_oordernum[$k] = $k;
		$que_order = '';
		$que_product = '';

		if($v['_sendstatus'] == '배송중' || $v['_sendstatus'] == '배송완료') {
			if($v['_sendstatus'] == '배송준비' || $r['o_sendstatus'] == '구매발주' || !$r['o_sendstatus']) {
				$que_order = " ,  o_senddate = now() ";
				$que_product = " ,  op_senddate = now() ";
			}
			// {{{배송완료일추가}}}
			if( $r['o_sendstatus'] == '배송중' && $v['_sendstatus'] == '배송완료'){  $que_product = " ,  op_completedate = now() ";  }
		}
		else {
			$que_order = " ,  o_senddate = '0000-00-00' , o_completedate = '0000-00-00'  "; // {{{배송완료일추가}}}
			$que_product = " ,  op_senddate = '0000-00-00' ,  op_completedate = '0000-00-00' "; // {{{배송완료일추가}}}

		}

		// 주문정보 수정
		_MQ_noreturn("
			update smart_order set
				  o_sendcompany	= '{$v['_sendcompany']}'
				, o_sendnum = '{$v['_sendnum']}'
				, o_sendstatus = '{$v['_sendstatus']}'
				  {$que_order}
			where o_ordernum='{$r['o_ordernum']}'
		");

		// 주문상품정보 수정
		_MQ_noreturn("
			update smart_order_product set
				  op_sendcompany = '{$v['_sendcompany']}'
				, op_sendnum = '{$v['_sendnum']}'
				, op_sendstatus = '{$v['_sendstatus']}'
				  {$que_product}
			where op_oordernum='{$r['o_ordernum']}'
		");

		// 문자발송
		if(($r['o_sendstatus'] == '구매발주' || $r['o_sendstatus'] == '배송준비') && ($v['_sendstatus'] == '배송중' || $v['_sendstatus'] == '배송완료')) {
			if(!$arr_ordernum_sms[$k]) {
				if($r['o_ordernum']) $order_info = $r;
				else $order_info = _MQ("select o_otel from smart_order where o_ordernum = '{$r['o_ordernum']}' ");
				// 상품 배송시 주문회원에게 문자 발송
				if($order_info['o_otel'] || $order_info['o_ohp']) {
					// 문자 발송
					$sms_to = $order_info['o_ohp'] ? $order_info['o_ohp'] : $order_info['o_otel'];
					// shop_send_sms($sms_to, 'delivery', $k);
					$arr_send[] = array('to'=>$sms_to, 'type'=>'delivery', 'ordernum'=>$k); // 2020-04-07 SSJ :: 문자 일괄 발송
				}
				$arr_ordernum_sms[$k]++;
			}
		}

		// 주문상태 업데이트
		order_status_update($r['o_ordernum']);
	}

    // 2020-04-07 SSJ :: 문자 일괄 발송
    if(count($arr_send) > 0){
        shop_send_sms_multi($arr_send);
        unset($arr_send);
    }

	// 배송중 메일 발송
	if(count($op_oordernum) > 0) {
		$OrderData = array();
		foreach($op_oordernum as $k=>$v) {
			$OrderData[$v] = $v;
		}
		foreach($OrderData as $k=>$v) {
			$_SendMode = 'order';
			$or = _MQ(" select * from `smart_order` where `o_ordernum` = '{$v}' ");
			$opr = _MQ_assoc("
						select op.* , p.p_name, p.p_img_list , p.p_code, p.p_img_list_square
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

	error_loc_msg('_order_delivery.list.php', '적용하였습니다.');
}