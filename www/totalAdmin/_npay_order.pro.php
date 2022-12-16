<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');

include_once('inc.php');


# 네이버페이 결제 데이터 엑셀다운로드
if(in_array($_mode, array('get_excel', 'get_search_excel'))) { // Excel 다운로드 Start


	# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
	$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
	$AdminPathData = explode('/', $AdminPathData['path']);
	$AdminPath = $AdminPathData[1]; unset($AdminPathData); // 'totalAdmin' or 'subAdmin'

	$toDay = date('YmdHis', time());
	$fileName = 'npay_order_list';
	if(!$test) {

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
	}

	# 모드별 쿼리 조건
	if($_mode == 'get_excel') $s_query = " where (1) and `o`.`npay_order` = 'Y' and `op`.`op_uid` in ('".implode("', '", $op_uid)."') ";
	else $s_query = enc('d', $_search_que);


	# 쿼리
	$que = "
		select
			`op`.*,
			`o`.*,
			/* LDD: 2019-01-18 네이버페이 패치 */
				`op`.`npay_status` as `npay_status`,
			/* LDD: 2019-01-18 네이버페이 패치 */
			`o_rtel` as `ordertel`,
			`o_rhp` as `orderhtel`,
			`p_name`
		from
			`smart_order_product` as `op` left join
			`smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`) left join
			`smart_product` as `p` on(`op`.`op_pcode` = `p`.`p_code`)
			{$s_query}
		order by `o_rdate` desc
	";
	$res = _MQ_assoc($que);

	# 공금업체 리스트 추출
	$arr_customer = arr_company();

	# 테이블 스타일
	$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
	$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
	$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
	$br = '<br style="mso-data-placement:same-cell;">';

	// LDD: 2019-01-18 네이버페이 패치
	$StatusArray = array(
		  'PAYED' => '결제 완료'
		, 'DISPATCHED' => '발송 처리'
		, 'CANCEL_REQUESTED' => '취소 요청'
		, 'RETURN_REQUESTED' => '반품 요청'
		, 'EXCHANGE_REQUESTED' => '교환 요청'
		, 'EXCHANGE_REDELIVERY_READY' => '교환 재배송 준비'
		, 'HOLDBACK_REQUESTED' => '구매 확정 보류 요청'
		, 'CANCELED' => '취소'
		, 'RETURNED' => '반품'
		, 'EXCHANGED' => '교환'
		, 'PURCHASE_DECIDED' => '구매 확정'
	);
	$SyncIcon = array(
		'이전주문'=>'<span style="font-weight:bold; color:#C5C5C5;">이전주문</span>', // 솔루션에서는 제거
		'Y'=>'<span style="font-weight:bold; color:#3F48CC;">연동완료</span>',
		'R'=>'<span style="font-weight:bold; color:#FF7F27;">연동대기</span>',
		'A'=>'<span style="font-weight:bold; color:#22B14C;">후연동</span>'
	);
?>
	<table>
		<thead>
			<tr>
				<th<?php echo $THStyle; ?>>SerialNum</th>
				<th<?php echo $THStyle; ?>>주문일</th>
				<th<?php echo $THStyle; ?>>주문번호</th>
				<?php // LDD: 2019-01-18 네이버페이 패치 ?>
					<th<?php echo $THStyle; ?>>N 주문번호</th>
					<th<?php echo $THStyle; ?>>N 상품주문번호</th>
					<th<?php echo $THStyle; ?>>연동상태</th>
					<th<?php echo $THStyle; ?>>진행상태</th>
				<?php // LDD: 2019-01-18 네이버페이 패치 ?>
				<th<?php echo $THStyle; ?>>상품정보</th>
				<th<?php echo $THStyle; ?>>수량</th>
				<th<?php echo $THStyle; ?>>주문가격</th>

				<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
				<th<?php echo $THStyle; ?>>N 포인트 사용(전체 주문기준)</th>
				<th<?php echo $THStyle; ?>>N 적립금 사용(전체 주문기준)</th>
				<?php } ?>

				<th<?php echo $THStyle; ?>>배송비용</th>
				<th<?php echo $THStyle; ?>>택배업체</th>
				<th<?php echo $THStyle; ?>>송장번호</th>
				<th<?php echo $THStyle; ?>>배송일</th>

				<th<?php echo $THStyle; ?>>주문자</th>
				<th<?php echo $THStyle; ?>>주문자 이메일</th>
				<th<?php echo $THStyle; ?>>주문자 전화번호</th>
				<th<?php echo $THStyle; ?>>주문자 휴대전화번호</th>

				<th<?php echo $THStyle; ?>>받는분</th>
				<th<?php echo $THStyle; ?>>받는분 전화번호</th>
				<th<?php echo $THStyle; ?>>받는분 휴대전화번호</th>

				<th<?php echo $THStyle; ?>>우편번호</th>
				<th<?php echo $THStyle; ?>>주소</th>
				<th<?php echo $THStyle; ?>>배송메시지</th>
				<th<?php echo $THStyle; ?>>주문/결제일시</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($res as $k=>$v) {

				// 상태아이콘
				$StatusIcon = '';
				if($v['npay_status'] == 'PAYED') $StatusIcon = '결제완료';
				if($v['npay_status'] == 'PLACE') $StatusIcon = '발주처리';
				if($v['npay_status'] == 'DISPATCHED') $StatusIcon = '배송처리';
				if($v['npay_status'] == 'CANCELED') $StatusIcon = '취소';

				// LDD: 2019-01-18 네이버페이 패치
				if(in_array($v['npay_status'], array('PAYED', 'PLACE', 'DISPATCHED', 'CANCELED')) === false) {
					$StatusIcon = $StatusArray[$v['npay_status']];
				}

				// 구매디바이스 아이콘
				if($v['mobile'] == 'Y') $device_icon = 'MOBLIE주문';
				else $device_icon = 'PC주문';
			?>
			<tr>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_uid']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo date('Y-m-d H:i', strtotime($v['o_rdate'])); ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_oordernum']; ?>
				</td>
				<?php // LDD: 2019-01-18 네이버페이 패치 ?>
					<td<?php echo $TDStyle; ?>>
						<?php echo ($v['npay_order_group']?$v['npay_order_group']:'이전주문'); ?>
					</td>
					<td<?php echo $TDStyle; ?>>
						<?php echo ($v['npay_order_code']?$v['npay_order_code']:'연동대기'); ?>
					</td>
					<td<?php echo $TDStyle; ?>><?php echo strip_tags(!$v['npay_order_group']?$SyncIcon['이전주문']:$SyncIcon[$v['npay_sync']]); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $StatusIcon; ?></td>
				<?php // LDD: 2019-01-18 네이버페이 패치 ?>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['p_name']; ?>
					<?php echo ($v['op_option1']?$br.$v['op_option1']:null); ?>
					<?php echo ($v['op_option2']?$br.$v['op_option2']:null); ?>
					<?php echo ($v['op_option3']?$br.$v['op_option3']:null); ?>
				</td>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['op_cnt']); ?>
				</td>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['op_price'] * $v['op_cnt']); ?>원
				</td>

				<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['npay_point']); ?>
				</td>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['npay_point2']); ?>
				</td>
				<?php } ?>

				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['op_delivery_price'] + $v['op_add_delivery_price']); ?>원
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_sendcompany']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo ($v['op_sendcompany'] == $v['op_sendnum']?'-':$v['op_sendnum']); ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_senddate']; ?>
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_oname']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_oemail']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_otel']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_ohp'] ?>
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rname'] ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rtel'] ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rhp'] ?>
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rpost']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_raddr1'].' '.$v['o_raddr2']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo str_replace(array('<br>', '<br/>', '<br />'), $br, $v['o_content']); ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rdate']; ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
<?php
} // excel 다운로드 End



# 주문수정
if($_mode == 'modify') {

	$sque = "
		update `smart_order` set
			o_rname			= '".trim($o_rname)."',
			o_rtel			= '".trim($o_rtel)."',
			o_rhp			= '".trim($o_rhp)."',
			o_rpost			= '".rm_str($_rzip1).'-'.rm_str($_rzip2)."',
			o_raddr1		= '".trim($_raddress)."',
			o_raddr2		= '".trim($_raddress1)."',
			o_raddr_doro	= '".trim($_raddress_doro)."',
			o_rzonecode		= '".rm_str($_rzonecode)."',
			o_content		= '".trim($comment)."',
			o_admcontent	= '".trim($o_admcontent)."'
		WHERE
			o_ordernum		='" . $ordernum . "'
	"; // #LDD018 (delivery_date	= '".$delivery_date."')
	_MQ_noreturn($sque);

	// 주문발송 상태 변경
	order_status_update($ordernum);

	error_loc_msg("_npay_order.form.php?_mode=modify&_uid={$_uid}&_PVSC=${_PVSC}" , "수정이 잘 되었습니다.");
}


# 강제취소기능
if($_mode == 'force_cancel') {

	$_result_msg = '네이버페이 강제취소';
	# 주문정보를 가져온다.
	$ordr = _MQ("
		select
			*
		from
			`smart_order_product` as `op` left join
			`smart_order` as `o` on(`op`.`op_oordernum` = `o`.`o_ordernum`)
		where
			`o`.`npay_order` = 'Y' and
			`op`.`npay_order_code` = '{$npay_code}'
		");
	$_ordernum = $ordr['op_oordernum']; // o_ordernum
	$_uid = $ordr['op_uid'];

	# 주문상품 취소처리
	include(OD_PROGRAM_ROOT.'/shop.order.salecntdel_pro.php');
	_MQ_noreturn(" update `smart_order_product` set
		`op_cancel` = 'Y',
		`op_cancel_returnmsg` = '{$_result_msg}',
		`op_cancel_tid` = '',
		`op_cancel_cdate` = now(),
		`npay_status` = 'CANCELED'
		where `op_oordernum` = '{$_ordernum}' and `op_uid` = '{$_uid}'
	");

	# 추가옵션 취소처리
	$add_res = _MQ_assoc(" select * from `smart_order_product` where `op_is_addoption` = 'Y' and `op_addoption_parent` = '{$ordr['op_pouid']}' and `op_oordernum` = '{$ordr['op_oordernum']}' ");
	if(count($add_res) > 0) {
		foreach($add_res as $adk=>$adv) {

			_MQ_noreturn(" update `smart_order_product` set
				`op_cancel` = 'Y',
				`op_cancel_returnmsg` = '{$_result_msg}',
				`op_cancel_tid` = '',
				`op_cancel_cdate` = now(),
				`npay_status` = 'CANCELED'
				where `op_oordernum` = '{$adv['op_oordernum']}' and `op_uid` = '{$adv['op_uid']}'
			");
		}
	}

	# 마지막 부분취소일 경우 주문 전체 취소
	$tmp = _MQ(" select count(*) as `cnt` from `smart_order_product` where `op_cancel` != 'Y' and `op_oordernum` = '{$_ordernum}' ");
	if($tmp['cnt'] == 0) {

		include(OD_PROGRAM_ROOT.'/shop.order.pointdel_pro.php');
		_MQ_noreturn(" update `smart_order` set `o_canceled` = 'Y' where `o_ordernum` = '{$_ordernum}' ");
	}

	# 주문발송 상태 변경
	order_status_update($_ordernum);

	error_loc_msg("_npay_order.form.php?_mode=modify&_uid={$_uid}&_PVSC=${_PVSC}" , "네이버페이 강제취소가 완료 되었습니다.");
}


# 정산대기 --------- 네이버 정산과 사이트 내부의 정산 시스템이 다르기 때문에 사용 금지 // 만약 원하는 경우만 주석을 풀고 사용(실정산에는 책임 질 수 없음)
/*
	if($_mode == 'settlementstatus_ready') {

		foreach($op_uid as $k=>$v) {

			$r = _MQ(" select `npay_status` from `smart_order_product` where `op_uid` = '{$v}' ");
			if($r['npay_status'] != 'DISPATCHED') {
				unset($op_uid[$k]);
				$error_msg = "배송완료상태가 아닌 주문상품을 제외하고,\\n";
			}
		}
		if(sizeof($op_uid) > 0) {
			$sque = " update smart_order_product set op_settlementstatus='ready' where op_uid in ('". implode("' , '" , array_values($op_uid) ) ."') and op_settlementstatus='none' ";
			if($test) {
				echo $sque.'<br>';
			}
			else {

				_MQ_noreturn($sque);
				order_settlement_status_opuid(array_values($op_uid));//2015-08-19 추가 - 정준철
			}
		}
		if(!$test) error_frame_reload($error_msg."선택한 주문상품이 정산대기로 변경되었습니다."); // 부모창 reload
	}
*/