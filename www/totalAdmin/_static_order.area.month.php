<?php


	// 일자계산 
	$pass_date = ($pass_date?$pass_date:date('Y', time()));
	$Select_Year = $pass_date;


		$s_query = " 
			where 
				o.o_canceled!='Y' 
				AND o.o_paystatus = 'Y' 
				AND op.op_cancel = 'N' 
				AND LEFT(o.o_rdate,4) = '". $pass_date ."' 
		";


		$arr_sum = array();

		// ---- 구매건수, 구매수량, 구매금액 요약 ----
		$que = "
			SELECT

				COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
				COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

				COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,

				SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
				SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

				SUM(sub_sum_buy_cnt) as sum_buy_cnt,

				SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
				SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

				SUM(sub_sum_buy_price) as sum_buy_price

			FROM
			(
				SELECT 

					op.op_oordernum ,

					IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
					IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


					SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
					SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,


					SUM( op.op_cnt ) as sub_sum_buy_cnt,

					SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
					SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

					SUM( op_price * op_cnt ) as sub_sum_buy_price

				FROM smart_order_product as op 
				INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum ) 

				" . $s_query . "

				group by op.op_oordernum

			) as tbl_view
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){
			foreach( $v as $sk=>$sv ){
				$arr_sum[$sk] = $sv;
			}
		}
		// ---- 구매건수, 구매수량, 구매금액 요약 ----




		// ------- 목록 -------
		$arr_res = $arr_all_res = array();
		$que = "
			select 

				rdate, area,

				COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
				COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

				COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


				SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
				SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

				SUM(sub_sum_buy_cnt) as sum_buy_cnt,

				SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
				SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

				SUM(sub_sum_buy_price) as sum_buy_price

			from
			(
				SELECT 

					LEFT(o.o_rdate,7) as rdate ,
					o.o_area as area,
					op.op_oordernum ,

					IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
					IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


					SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
					SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

					SUM( op.op_cnt ) as sub_sum_buy_cnt,

					SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
					SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


					SUM( op_price * op_cnt ) as sub_sum_buy_price

				FROM smart_order_product as op 
				INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum ) 
				
				" . $s_query . "

				group by op.op_oordernum

			) as tbl_view

			GROUP BY rdate, area
			ORDER BY 
				rdate ASC
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){

			// 구매건수
			$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
			$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
			$arr_res[$v['rdate']][$v['area']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

			$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
			$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
			$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


			// 구매수량
			$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
			$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
			$arr_res[$v['rdate']][$v['area']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

			$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
			$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
			$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


			// 구매금액
			$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
			$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
			$arr_res[$v['rdate']][$v['area']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
			$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
			$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

		}
		// ------- 목록 -------
	?>


	<!-- 기간검색 -->
	<form name="searchfrm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="hidden" name="_type" value="<?php echo $_type; ?>">
		<input type="hidden" name="_mode" value="search">
		<div class="data_form if_search">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>기간선택</th>
						<td>
							<input type="text" name="pass_date" class="design js_pic_year_max_today" value="<?php echo $pass_date; ?>" style="width:70px" readonly>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span></li>
					<?php if($_mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=<?=$_type?>" class="c_btn h34 black line normal" accesskey="l">검색풀기</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</form>
	<!-- / 기간검색 -->




	<? // ------- 주문 Summary ------- ?>
	<div class="group_title"><strong><?php echo $Select_Year; ?>년 주문 요약</strong></div>
	<div class="data_list" style="margin-bottom:30px; ">
		<table class="table_list if_counter_table">
			<colgroup>
				<col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%">
				<col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%">
				<col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="16.6%"><col width="16.6%">
			</colgroup>
			<tbody>
				<tr>
					<th colspan="4" class='line'>구매건수</th>
					<th colspan="4" class='line'>구매수량</th>
					<th colspan="4" >구매금액</th>
				</tr>

				<?php
					$app_sum_order_cnt = $arr_sum['sum_order_cnt'] > 0 ? $arr_sum['sum_order_cnt'] : 1;
					$app_sum_buy_cnt = $arr_sum['sum_buy_cnt'] > 0 ? $arr_sum['sum_buy_cnt'] : 1;
					$app_sum_buy_price = $arr_sum['sum_buy_price'] > 0 ? $arr_sum['sum_buy_price'] : 1;
				?>

				<!-- PC / MOBILE -->
				<tr>
					<!-- 구매건수 -->
						<td class="disabled">PC</td>
						<td class=''>
							<span class=" no_left">
								<span class="t_orange "><?php echo number_format($arr_sum['sum_mobileN_order_cnt'] * 1); ?></span><br>
								<span class="t_sky b">(<?=number_format($arr_sum['sum_mobileN_order_cnt'] * 100 / $app_sum_order_cnt , 2)?>%)</span>
							</span>
						</td>
						<td class="disabled">MOBILE</td>
						<td class='line'>
							<span class=" no_left">
								<span class="t_orange "><?php echo number_format($arr_sum['sum_mobileY_order_cnt'] * 1); ?></span><br>
								<span class="t_sky b">(<?=number_format($arr_sum['sum_mobileY_order_cnt'] * 100 / $app_sum_order_cnt , 2)?>%)</span>
							</span>
						</td>

					<!-- 구매수량 -->
						<td class="disabled">PC</td>
						<td class=''>
							<span class=" no_left">
								<span class="t_orange "><?php echo number_format($arr_sum['sum_mobileN_buy_cnt'] * 1); ?></span><br>
								<span class="t_sky b">(<?=number_format($arr_sum['sum_mobileN_buy_cnt'] * 100 / $app_sum_buy_cnt , 2)?>%)</span>
							</span>
						</td>
						<td class="disabled">MOBILE</td>
						<td class='line'>
							<span class=" no_left">
								<span class="t_orange "><?php echo number_format($arr_sum['sum_mobileY_buy_cnt'] * 1); ?></span><br>
								<span class="t_sky b">(<?=number_format($arr_sum['sum_mobileY_buy_cnt'] * 100 / $app_sum_buy_cnt , 2)?>%)</span>
							</span>
						</td>

					<!-- 구매금액 -->
						<td class="disabled">PC</td>
						<td>
							<span class=" no_left">
								<span class="t_orange "><?php echo number_format($arr_sum['sum_mobileN_buy_price'] * 1); ?></span><br>
								<span class="t_sky b">(<?=number_format($arr_sum['sum_mobileN_buy_price'] * 100 / $app_sum_buy_price , 2)?>%)</span>
							</span>
						</td>
						<td class="disabled">MOBILE</td>
						<td>
							<span class=" no_left">
								<span class="t_orange "><?php echo number_format($arr_sum['sum_mobileY_buy_price'] * 1); ?></span><br>
								<span class="t_sky b">(<?=number_format($arr_sum['sum_mobileY_buy_price'] * 100 / $app_sum_buy_price , 2)?>%)</span>
							</span>
						</td>

				</tr>
				<!-- 총건수 -->
				<tr>
					<td class="disabled"><span class="fr_tx no_left"><span class="bold">총 구매건수</span></span></td><!-- 구매건수 -->
					<td class='line' colspan="3"><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_order_cnt'] * 1); ?></span></span></td>
					<td class="disabled"><span class="fr_tx no_left"><span class="bold">총 구매수량</span></span></td><!-- 구매수량 -->
					<td class='line' colspan="3"><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_buy_cnt'] * 1); ?></span></span></td>
					<td class="disabled"><span class="fr_tx no_left"><span class="bold">총 구매금액</span></span></td><!-- 구매금액 -->
					<td colspan="3"><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_buy_price'] * 1); ?></span></span></td>
				</tr>

			</tbody>
		</table>
		<div class="data_summery" style="border-top:0px">
			<div class="tip_box">
				<?=_DescStr("취소되지 않고 결제가 된 <strong>정상적인 주문 건을 기준으로 정보</strong>를 추출합니다.");?>
				<?=_DescStr("구매 건수는 주문 횟수를 의미합니다.");?>
				<?=_DescStr("총건수, 총수량 ,총금액은 <strong>회원/비회원 또는 PC/MOBILE의 합산</strong>으로  <strong><u>회원/비회원/PC/MOBILE을 합산한 수치가 아닙니다.</u></strong>");?>
			</div>
		</div>
	</div>
	<? // ------- 주문 Summary ------- ?>




	<div class="data_list">
		<div class="list_ctrl">
			<div class="right_box">
				<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel">엑셀다운로드</a>
			</div>
		</div>


		<?// ----- 표 테이블  -----?>
		<div ID="grid_table"></div>


	</div>
	<!-- / 도표 -->




<form name="frmSearch" method="post" action="_static_order.pro.php" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="Select_Year" value="<?php echo $Select_Year; ?>">
</form>


<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('area_month_search');
		$('form[name="frmSearch"]').attr('action', '_static_order.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---
</script>










<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid'); // 날짜 영역 grid_no 클래스 적용 
	$arr_class_data['area_all_order_cnt_num'] =  array('grid_sky');
	$arr_class_data['area_all_buy_cnt_num'] =  array('grid_sky');
	$arr_class_data['area_all_buy_price_num'] =  array('grid_sky');

	$arr_table_data = array();

	for($i=1 ; $i<=12 ; $i++){

		$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
		$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

		// ----- 소계 -----
		$arr_table_data_tmp1 = array(
				'_extraData' => array(
					'rowSpan' =>array('rdate' => 3),// 날짜 열별합 - 3개(rowspan=3)
					'className' =>array(
						'row' => array('grid'),// 행에 디자인 클래스를 적용
						'column' => $arr_class_data
					)
				),
				'rdate' => $app_date,
				'device' => '소계',

				'area_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['sum']),
				'area_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']),
				'area_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['sum'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_area_basic as $sk=>$sv) {
			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'area_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sv]['order_cnt']['sum']),
				'area_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sv]['buy_cnt']['sum']),
				'area_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sv]['buy_price']['sum'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- 소계 -----



		// ----- PC -----
		$arr_table_data_tmp1 = array(
				'device' => 'PC',

				'area_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']),
				'area_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']),
				'area_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['mobileN'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_area_basic as $sk=>$sv) {

			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'area_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileN']),
				'area_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileN']),
				'area_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileN'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- PC -----



		// ----- 모바일 -----
		$arr_table_data_tmp1 = array(
				'device' => '모바일',

				'area_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']),
				'area_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']),
				'area_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['mobileY'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_area_basic as $sk=>$sv) {

			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'area_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileY']),
				'area_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileY']),
				'area_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileY'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- 모바일 -----

	}
	// ------- 표 - 테이블 데이터 -------

?>

<link rel="stylesheet" type="text/css" href="./js/tui.grid/tui-grid.min.css">
<script type="text/javascript" src="/include/js/underscore.min.js"></script>
<script type="text/javascript" src="/include/js/backbone-min.js"></script>
<script type="text/javascript" src="./js/tui.grid/tui-code-snippet.min.js"></script>
<script src="./js/tui.grid/grid.min.js"></script>
<script type="text/javascript" class="code-js">

    var grid = new tui.Grid({
        el: $('#grid_table'),

        columnFixCount: 2,
        headerHeight: 99,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnMerge : [
			{
				"title" : "<b>전체</b>", "columnName" : "area_all", 
				"columnNameList" : [
					"area_all_order_cnt_num", 
					"area_all_buy_cnt_num", 
					"area_all_buy_price_num"
				] 
			},
			<?foreach($arr_order_area_basic as $sk=>$sv) {?>
				<?echo ( $sk <> 0 ? ',' : '' )?>
				{
					"title" : "<b><?=$sv?></b>", "columnName" : "area_<?=$sk?>", 
					"columnNameList" : [
						"area_<?=$sk?>_order_cnt_num", 
						"area_<?=$sk?>_buy_cnt_num", 
						"area_<?=$sk?>_buy_price_num",
					] 
				}
			<?}?>
        ],

        columnModelList: [
            {"title" : "<b>년월</b>", "columnName" : "rdate", "align" : "center", "width" : 90 },
			{"title" : "<b>접속기기</b>", "columnName" : "device", "align" : "center", "width" : 90 },

			{"title" : "구매건수", "columnName" : "area_all_order_cnt_num", "align" : "right", "width" : 70 }, 
			{"title" : "구매수량", "columnName" : "area_all_buy_cnt_num", "align" : "right", "width" : 70 }, 
			{"title" : "구매금액", "columnName" : "area_all_buy_price_num", "align" : "right", "width" : 120 }, 

			<?foreach($arr_order_area_basic as $sk=>$sv) {?>
				<?echo ( $sk <> 0 ? ',' : '' )?>
				{"title" : "구매건수", "columnName" : "area_<?=$sk?>_order_cnt_num", "align" : "right", "width" : 70 }, 
				{"title" : "구매수량", "columnName" : "area_<?=$sk?>_buy_cnt_num", "align" : "right", "width" : 70 }, 
				{"title" : "구매금액", "columnName" : "area_<?=$sk?>_buy_price_num", "align" : "right", "width" : 120 }
			<?}?>
        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>