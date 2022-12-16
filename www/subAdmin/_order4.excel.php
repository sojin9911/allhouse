<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');


if(count($_uid) <= 0 && $_mode != 'get_search_excel') error_msg('항목을 선택하시기 바랍니다.');
$toDay = date('YmdHis');
$fileName = "order4excel";


# 모드별 쿼리 조건
$s_query = " and s_partnerCode = '{$com_id}' ";
if($_mode == 'get_excel') $s_query .= " and s_uid in ('".implode("', '", $_uid)."') ";
else $s_query .= enc('d', $_search_que);
if(!$st) $st = 's_uid';
if(!$so) $so = 'desc';
$res = _MQ_assoc("
	select
		*
	from
		smart_order_settle_complete
	where (1)
		{$s_query}
	order by {$st} {$so}
");

## Exel 파일로 변환 #############################################
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">"); 


# 테이블 스타일
$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
$br = '<br style="mso-data-placement:same-cell;">';
?>
<table>
	<thead>
		<tr>
			<th<?php echo $THStyle; ?>>정산일</th>
			<th<?php echo $THStyle; ?>>총금액</th>
			<th<?php echo $THStyle; ?>>정산수량</th>
			<th<?php echo $THStyle; ?>>배송비</th>
			<th<?php echo $THStyle; ?>>정산금액</th>
			<th<?php echo $THStyle; ?>>할인액</th>
			<th<?php echo $THStyle; ?>>수수료</th>
			<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
				<th<?php echo $THStyle; ?>>세금계산서<?php echo $br; ?>발행상태</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($res as $k=>$v) { ?>
			<tr>
				<td<?php echo $TDStyle; ?>><?php echo date('Y-m-d', strtotime($v['s_date'])); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($v['s_price']+$v['s_price_vat_n']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($v['s_count']+$v['s_count_vat_n']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($v['s_delivery_price']+$v['s_delivery_price_vat_n']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($v['s_com_price']+$v['s_com_price_vat_n']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($v['s_usepoint']+$v['s_usepoint_vat_n']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($v['s_discount']+$v['s_discount_vat_n']); ?></td>
				<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
					<td<?php echo $TDStyle; ?>>
						<?php
							switch($v['s_tax_status']){
								case 1000 :echo '임시저장'; break;
								case 2010 : case 2011 :echo '세금계산서발행중'; break;
								case 4012 :echo '발행거부'; break;
								case 3014 : case 3011 : echo '발행완료'; break;
								case 5013 : case 5031 : echo '발행취소'; break;
								default : echo '미발행'; break;
							}
						?>
					</td>
				<?php } ?>
			</tr>
		<?php } ?>
	</tbody>
</table>