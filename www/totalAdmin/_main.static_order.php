<?
	// 관리자 메인 페이지 적용용
	// ------------- 주문/배송 현황 -------------
?>
	<!-- 메인타이틀 -->
	<div class="main_tt">
		<span class="tit">주문/배송 현황</span>
		<!-- 주문/배송 페이지로 이동 -->
		<a href="_order.list.php" class="more_btn" title="더보기"></a>
	</div>


	<?php

		// 배열 설정
		$admin_main_order_type = array('today' => '오늘' , 'week' => '1주일' , 'month' => '1개월');
		$admin_main_order_status = array('결제대기' , '결제완료' , '배송준비' , '배송중', '배송완료' , '주문취소' , '부분취소요청', '부분취소완료' , '교환요청' , '환불요청');



		// ----- 주문 상태값에 따른 건수 정보 추출 - 오늘/1주일/1개월  -----
		$order_status = array();
		$r = _MQ_assoc("
			select
				DATE(o_rdate) as rdate,
				sum(IF( o_status = '결제대기' and o_paymethod IN ('online' , 'virtual') and o_paystatus ='N' and o_canceled='N' , 1 , 0 )) as cnt_step01 ,
				sum(IF( (o_status = '결제완료' or o_status = '배송대기') and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step02 ,
				sum(IF( (o_status = '배송준비') and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step06 ,
				sum(IF( (o_status = '배송중') and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step03 ,
				sum(IF( o_status = '배송완료' and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step04 ,
				sum(IF( o_canceled='Y' , 1 , 0 )) as cnt_step05
			from smart_order
			WHERE DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['결제대기'] = $v['cnt_step01'];
				$order_status['today']['결제완료'] = $v['cnt_step02'];
				$order_status['today']['배송준비'] = $v['cnt_step06']; // LCY : 2021-01-19 : 배송준비 추가
				$order_status['today']['배송중'] = $v['cnt_step03'];
				$order_status['today']['배송완료'] = $v['cnt_step04'];
				$order_status['today']['주문취소'] = $v['cnt_step05'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['결제대기'] += $v['cnt_step01'];
				$order_status['week']['결제완료'] += $v['cnt_step02'];
				$order_status['week']['배송준비'] += $v['cnt_step06']; // LCY : 2021-01-19 : 배송준비 추가 
				$order_status['week']['배송중'] += $v['cnt_step03'];
				$order_status['week']['배송완료'] += $v['cnt_step04'];
				$order_status['week']['주문취소'] += $v['cnt_step05'];
			}
			// 1개월 정보 추출
			$order_status['month']['결제대기'] += $v['cnt_step01'];
			$order_status['month']['결제완료'] += $v['cnt_step02'];
			$order_status['month']['배송준비'] += $v['cnt_step06']; // LCY : 2021-01-19 : 배송준비 추가 
			$order_status['month']['배송중'] += $v['cnt_step03'];
			$order_status['month']['배송완료'] += $v['cnt_step04'];
			$order_status['month']['주문취소'] += $v['cnt_step05'];
		}
		// ----- 주문 상태값에 따른 건수 정보 추출 - 오늘/1주일/1개월  -----



		// ----- 부분취소 완료건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(op_cancel_rdate) as rdate,
				COUNT(*)  as cnt
			from smart_order_product
			WHERE
				op_cancel = 'Y' and
				DATE_ADD(DATE(op_cancel_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['부분취소완료'] = $v['cnt'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['부분취소완료'] = $v['cnt'];
			}
			// 1개월 정보 추출
			$order_status['month']['부분취소완료'] = $v['cnt'];
		}
		// ----- 부분취소 완료건수 정보 추출 - 오늘/1주일/1개월  -----

		// ----- 부분취소 요청건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(op_cancel_rdate) as rdate,
				COUNT(*)  as cnt
			from smart_order_product
			WHERE
				op_cancel = 'R' and
				DATE_ADD(DATE(op_cancel_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['부분취소요청'] = $v['cnt'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['부분취소요청'] = $v['cnt'];
			}
			// 1개월 정보 추출
			$order_status['month']['부분취소요청'] = $v['cnt'];
		}
		// ----- 부분취소 요청건수 정보 추출 - 오늘/1주일/1개월  -----



		// ----- 교환요청 건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(o_rdate) as rdate,
				COUNT(*)  as cnt
			from smart_order_product as op
			inner join smart_order as o on (o.o_ordernum=op.op_oordernum)
			WHERE
				o.o_canceled!='Y' and
				op.op_complain!='' and
				DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['교환요청'] = $v['cnt'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['교환요청'] = $v['cnt'];
			}
			// 1개월 정보 추출
			$order_status['month']['교환요청'] = $v['cnt'];
		}
		// ----- 교환요청 건수 정보 추출 - 오늘/1주일/1개월  -----



		// ----- 환불요청 건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(o_moneyback_date) as rdate,
				COUNT(*)  as cnt
			from smart_order
			where
				o_canceled = 'Y' and
				o_moneyback_status = 'request ' and
				IF( UNIX_TIMESTAMP(o_moneyback_date) > 0  , DATE_ADD(DATE(o_moneyback_date) , INTERVAL + 1 MONTH) >= CURDATE() , 1 )
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['환불요청'] = $v['cnt'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['환불요청'] = $v['cnt'];
			}
			// 1개월 정보 추출
			$order_status['month']['환불요청'] = $v['cnt'];
		}
		// ----- 환불요청 건수 정보 추출 - 오늘/1주일/1개월  -----




		// 주문/배송 현황 링크 함수
		//			status - 결제대기, 결제완료, 배송중 등 o_status 값
		//			type - today, week, month
		if (!function_exists('order_link')) {
			function order_link($status , $type){

				$view = ''; $pass_paystatus = '';
				switch( $status ){
					case "결제대기":
						$pass_paystatus = 'A'; $view = 'online'; $file_name = '_order.list.php';
					break;
					case "결제완료": case "배송중": case "배송준비": case "배송완료":
						$pass_paystatus = 'Y'; $file_name = '_order.list.php';
					break;
					case "주문취소":
						$pass_paystatus = 'A';  $file_name = '_order.cancel_list.php';
					break;
					case "부분취소요청":
						 $pass_cancel ='R'; $file_name = '_cancel.list.php';
					break;
					case "부분취소완료":
						 $pass_cancel ='Y'; $file_name = '_cancel.list.php';
					break;
					case "교환요청":
						$file_name = '_order_complain.list.php';
					break;
					case "환불요청":
						$file_name = '_cancel_order.list.php';
					break;
				}

				switch( $type ){
					case "today": $pass_sdate = DATE("Y-m-d"); $pass_edate = DATE("Y-m-d"); break;
					case "week": $pass_sdate = DATE("Y-m-d" , strtotime("-1 week")); $pass_edate = DATE("Y-m-d"); break;
					case "month": $pass_sdate = DATE("Y-m-d" , strtotime("-1 month")); $pass_edate = DATE("Y-m-d"); break;
					default : $pass_sdate = ''; $pass_edate = '';  break; // 없는 경우에 대한 처리
				}

				$_link = $file_name . '?mode=search&pass_status='. urlencode($status).'&pass_paystatus='. $pass_paystatus .'&pass_sdate='. urlencode($pass_sdate) .'&pass_edate=' . urlencode($pass_edate) . '&view='. $view. '&pass_cancel='.$pass_cancel ;

				return $_link;
			}
		}

	?>
	<table class="order">
		<thead>
			<tr>
				<th scope="col">기간</th>
				<?php
					foreach($admin_main_order_status as $sk=>$sv){
						echo '<th scope="col"><a href="' . order_link($sv , '') . '" class="btn">'. $sv .'</a></th>';
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($admin_main_order_type as $k=>$v){
					echo '
						<tr>
							<td>'. $v .'</td>
					';
					foreach($admin_main_order_status as $sk=>$sv){
						echo '<td><a href="' . order_link($sv , $k) . '" class="btn'. ($order_status[$k][$sv]<1?' if_none':null) .'">' . number_format($order_status[$k][$sv]) . '</a></td>';
					}
					echo '
						</tr>
					';
				}
			?>
		</tbody>
	</table>
<?// ------------- 주문/배송 현황 ------------- ?>