<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>주문내역 인쇄하기</title>
	<!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->
	<link rel="apple-touch-icon-precomposed" href="images/homeicon.png" />
	<meta name="format-detection" content="telephone=no" />
	<SCRIPT src="/include/js/jquery-1.11.2.min.js"></SCRIPT>
	<style type="text/css">
		html {width:100%;}
		body {margin:0; padding:20px; background:#fff;}
		body,p,pre,form,span,div,table,td,ul,ol,li,dl,dt,dd,input,textarea,label,button {color:#888; word-wrap:break-word; word-break:keep-all; font-family:"맑은고딕", sans-serif ; font-size:13px; font-weight:400; color:#333;}
		b,strong {word-wrap:break-word; word-break:break-all; font-family:inherit; font-size:inherit; font-weight:600; letter-spacing:0px;}

		p,form,span,h1,h2,h3,h4,h5,h6 {margin:0; padding:0; font-weight:normal}
		div,table {margin:0; padding:0; border-spacing:0; border-collapse:collapse; border:0px none; }
		ul,ol,li,td,dl,dt,dd {margin:0; padding:0; list-style:none;}
		em,i {font-style:normal}
		a,span {display:inline-block;}
		img {border:0;}
		span,div,a,b,strong,label {color:inherit; font-size:inherit; font-weight:inherit}
		table caption {width:0px; height:0px; font-size:0; visibility:hidden; }
		table {width:100%;}

		.wrap {max-width:1000px; margin:0 auto}

		.print_title {text-align:center; font-size:27px; color:#666; border:1px solid #ccc; padding:10px 0 15px 0}
		.print_title strong {color:#000;}

		.info_box {overflow:hidden; margin-top:20px}
		.info_title {font-weight:600; font-size:16px; padding-bottom:5px; border-bottom:2px solid #333}
		.info_table th {background:#eee; border-bottom:1px solid #ccc; padding:10px 15px;}
		.info_table td {background:#fff; border-bottom:1px solid #ccc; padding:10px 15px;}

		.item_list {margin-top:30px; border-bottom:1px solid #333;}
		.item_title {font-weight:600; font-size:16px; border:2px solid #333; border-radius:10px 10px 0 0; overflow:hidden; padding:15px 20px;}
		.item_title strong {float:left;}
		.item_title .time_box {float:right; overflow:hidden; margin-top:3px}
		.item_title .time_box li {float:left; margin-left:30px; position:relative; padding-left:10px}
		.item_title .time_box li:before {content:""; width:4px; height:4px; background:#333; position:absolute; left:0; top:50%; margin-top:-2px }
		.item_table th {background:#ddd; border:1px solid #ccc; padding:10px 15px; border-right:0; border-top:0}
		.item_table th:first-child {border-left:0}
		.item_table td {padding-left:10px; padding-right:10px;padding-top:2px;padding-bottom:2px; border:1px solid #ccc; border-right:0; font-size:11px;}
		.item_table td:first-child {border-left:0}
		.item_table .number {text-align:center;}
		.item_table .name {font-weight:300;}
		.item_table .name strong {font-weight:600;}
		.item_table .price {text-align:right; font-size:12px;}
		.item_table .count {text-align:center;}
		.item_table .count .box {background:#fff; border:1px solid #888; font-weight:400; padding:2px; min-width:15px; border-radius:20px;}
		.item_table .count.if_upto .box {background:#ccc; color:#000; border:1px solid #bbb;}
		.item_table .code {text-align:center;}
		.item_list .add_txt {padding:20px 20px 25px 20px; font-weight:600; font-size:18px; background:#fff; text-align:center;}

		.total_sum {font-weight:600; font-size:14px; border:2px solid #333; border-radius:50px; overflow:hidden; padding:0 10px; margin-top:30px; text-align:center;}
		.total_sum ul {overflow:hidden; display:inline-block}
		.total_sum li {float:left; font-size:14px; padding:0 10px 0 30px; position:relative; line-height:80px;}
		.total_sum .mark {font-size:20px; font-weight:400; position:absolute; left:0px; top:50%; border:1px solid #666; width:20px; height:20px; line-height:17px; margin-top:-10px; text-align:center; border-radius:100px;}
		.total_sum strong {text-decoration:underline; font-weight:600;}
		.total_sum .this_price {font-weight:600; font-size:15px; line-height:78px;}

		/* SSJ : 부분취소 표시 : 2020-09-04 */
		tr.cancel td {position:relative; color:#999;}
		tr.cancel td:before {content: "";position: absolute;left: 0;top: 48%;width: 100%;height: 1px;border-top: 1px solid #333;}
	</style>
</head>
<body>
<div class="wrap">



<?php
	foreach($row_array as $k=>$v) {

		$ordernum = $v['o_ordernum'];
		$row = $v;

		// 페이지 인쇄 구분
		if($k <> 0 ) {
			echo "<div style='page-break-before: always;'/></div>";
		}

?>

	<!-- ◆ 프린트 타이틀 -->
	<div class="print_title"><strong><?php echo $siteInfo['s_adshop']; ?></strong> 주문 내역서</div>
	<!-- / 프린트 타이틀 -->


	<!-- ◆ 주문자정보 -->
	<div class="info_box">
		<div class="info_title">
			주문자(수령자) 정보
		</div>

		<!-- 가로형 정보테이블 -->
		<table class="info_table">
			<colgroup>
				<col width="12%"/><col width="38%"/><col width="12%"/><col width="38%"/>
			</colgroup>
			<tbody>
				<tr>
					<th>성명</th>
					<td><?php echo cutstr_new($row['o_rname'],2,'') . '*'; ?> (<?php echo $row['o_mid']; ?>)</td>
					<th>주문번호</th>
					<td><?php echo $ordernum; ?></td>
				</tr>
					<th>연락처</th>
					<td>
						<?php
							$arr_phone = array_filter(array(($row['o_rtel'] ? substr($row['o_rtel'],0,-2) . '**' : '') , ($row['o_rhp'] ? substr($row['o_rhp'],0,-2) . '**' : '')));
							echo implode('/' , $arr_phone);
						?>
					</td>
					<th>주소</th>
					<td>
						<?php

							echo $row['o_raddr_doro'] .' '. $row['o_raddr2'];
							if($row['o_raddr1']){
								echo '<br>(지번주소: '.$row['o_raddr1'] .' '. $row['o_raddr2'].')';
							}
						?>
					</td>
				</tr>
				<?php if(trim($row['o_content'])) { ?>
				<tr>
					<th>배송 메모</th>
					<td colspan="3"><?php echo nl2br(stripslashes($row['o_content'])); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<!-- 정보테이블 -->
	</div>
	<!-- / 주문자정보 -->


	<!-- ◆ 주문상품정보 -->
	<div class="item_list">

		<div class="item_title">
			<strong>주문상품 리스트</strong>
			<div class="time_box">
				<ul>
					<li>주문시간 : <?php echo date('Y-m-d H시 i분' , strtotime($row['o_rdate'])); ?></li>
					<li>출력시간 : <?php echo date('Y-m-d H시 i분'); ?></li>
				</ul>
			</div>
		</div>

		<!-- 세로형 테이블 -->
		<table class="item_table">
			<colgroup>
				<col width="6%"/><col width="12%"/><col width="17%"/><col width="*"/><col width="8%"/><col width="10%"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">번호</th>
					<th scope="col">분류</th>
					<th scope="col">상품번호</th>
					<th scope="col">상품명</th>
					<th scope="col">수량</th>
					<th scope="col">가격</th>
				</tr>
			</thead>
			<tbody>
<?php
	$NpayDcPrice = 0; // LDD: 2018-07-21 네이버 페이 할인 (N포인트+N적립금)
	$arr_pcode = array();
	$arr_pcode_idx = array();
	$arr_op = array();
	$sres = _MQ_assoc("
		select
			op.* , p.*
		from smart_order_product as op
		left join smart_product as p on ( p.p_code=op.op_pcode )
		where op_oordernum='" . $ordernum . "'
	");
	foreach( $sres as $k=>$v ){

		/* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */
		$NpayDcPrice += ($v['npay_point']+$v['npay_point2']);
		/* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */

		// 옵션 정보
		$op_option_name = sizeof(array_filter(array($v['op_option1'],$v['op_option2'],$v['op_option3']))) > 0 ? "(".implode(' ', array_filter(array($v['op_option1'],$v['op_option2'],$v['op_option3']))).')' : NULL;

		// 분류정보
		$pct_r = _MQ(" select c.c_name from smart_product_category as pct inner join smart_category as c on (c.c_uid = pct.pct_cuid) where pct.pct_pcode = '". $v['op_pcode'] ."' limit 1 ");

		$app_pname = stripslashes($v['op_pname']) . $op_option_name;

		echo "
			<tr class='". ($v['op_cancel'] == 'Y' ? 'cancel' : null) /* SSJ : 부분취소 표시 : 2020-09-04 */ ."'>
				<td class='number'>". ($k+1)  ."</td>
				<td class='field'>". $pct_r['c_name'] ."</td>
				<td class='code'>". $v['op_pcode'] ."</td>
				<td class='name'>". $app_pname ."</td>
				<td class='count ".($v['op_cnt'] > 1 ? "if_upto" : "")."'><span class='box'>".$v['op_cnt']."</span></td>
				<td class='price'>". number_format($v['op_price'] * $v['op_cnt']) ."</td>
			</tr>
		";

	}
?>
			</tbody>
		</table>
		<!-- / 세로형 테이블 -->

		<?php echo (trim($row['o_printcontent']) ? '<div class="add_txt">'.stripslashes($row['o_printcontent']).'</div>' : ''); ?>

	</div>
	<!-- / 주문상품정보 -->


	<!-- ◆ 최종 주문가격 -->
	<div class="total_sum">
		<ul>
			<li>상품 : <strong><?php echo number_format($row['o_price_total']); ?></strong></li>
			<li><span class="mark">+</span>배송료 : <strong><?php echo number_format($row['o_price_delivery']); ?></strong></li>
			<li><span class="mark">-</span>할인 : <strong><?php echo number_format( 1 * $row['o_price_total'] + $row['o_price_delivery'] - $row['o_price_real'] + $NpayDcPrice); ?></strong></li>
			<?php if(($row['o_price_refund'] + $row['o_price_usepoint_refund']) > 0){ ?>
				<!-- SSJ : 부분취소 표시 : 2020-09-04 -->
				<li><span class="mark">-</span>취소 : <strong><?php echo number_format( 1 * ($row['o_price_refund'] + $row['o_price_usepoint_refund'])); ?></strong></li>
			<?php } ?>
			<!-- 진짜 최종결제금액 -->
			<li class="this_price"><span class="mark">=</span>결제총액 <strong><?php echo number_format($row['o_price_real']-($row['o_price_refund'] + $row['o_price_usepoint_refund'])); // SSJ : 부분취소 표시 : 2020-09-04 ?></strong></li>
		</ul>
	</div>
	<!-- / 최종 주문가격 -->

<? } ?>


</div>

<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function() {
		print();
	});
</SCRIPT>

</body>
</html>