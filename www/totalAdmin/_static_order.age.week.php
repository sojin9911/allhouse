<?php


	// 일자계산 - 시작일자 정의
	$pass_date = ($pass_date?$pass_date:date('Y-m-d', strtotime("-1 week")));
	$Select_Year = date('Y', strtotime($pass_date));
	$Select_Month = date('m', strtotime($pass_date));
	$Select_Day = date('d', strtotime($pass_date));

	// 일자계산 - 종료일자 정의
	$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d', time()));
	$Select_eYear = date('Y', strtotime($pass_edate));
	$Select_eMonth = date('m', strtotime($pass_edate));
	$Select_eDay = date('d', strtotime($pass_edate));


		$s_query = "
			where
				o.o_canceled!='Y'
				AND o.o_paystatus = 'Y'
				AND op.op_cancel = 'N'
				AND DATE(o.o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
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
				INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

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

				rdate, age,

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

					DATE_FORMAT(o.o_rdate, '%w') as rdate ,
					TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
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
				INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

				" . $s_query . "

				group by op.op_oordernum

			) as tbl_view

			GROUP BY rdate, age
			ORDER BY
				rdate ASC
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){

			// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
			$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

			// 구매건수
			$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
			$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
			$arr_res[$v['rdate']][$v['age']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

			$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
			$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
			$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


			// 구매수량
			$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
			$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
			$arr_res[$v['rdate']][$v['age']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

			$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
			$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
			$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


			// 구매금액
			$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
			$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
			$arr_res[$v['rdate']][$v['age']]['buy_price']['sum'] += $v['sum_buy_price'] ;

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
							<input type="text" name="pass_date" class="design js_pic_day_max_today" value="<?php echo $pass_date; ?>" style="width:90px" readonly>
							<span class="fr_tx">~</span>
							<input type="text" name="pass_edate" class="design js_pic_day_max_today" value="<?php echo $pass_edate; ?>" style="width:90px" readonly>
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
	<div class="group_title"><strong><?=DATE("Y년 m월 d일" , strtotime($pass_date) )?> ~ <?=DATE("Y년 m월 d일" , strtotime($pass_edate) )?> 주문 요약</strong></div>
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
				<?=_DescStr("구매자가 <strong>휴면상태가 아니고 탈퇴하지 않은 회원이 구매한 주문</strong> 중 , 취소되지 않고 결제가 된 <strong>정상적인 주문 건을 기준으로 정보</strong>를 추출합니다.");?>
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
	<input type="hidden" name="Select_Month" value="<?php echo $Select_Month; ?>">
	<input type="hidden" name="Select_Day" value="<?php echo $Select_Day; ?>">
	<input type="hidden" name="Select_eYear" value="<?php echo $Select_eYear; ?>">
	<input type="hidden" name="Select_eMonth" value="<?php echo $Select_eMonth; ?>">
	<input type="hidden" name="Select_eDay" value="<?php echo $Select_eDay; ?>">
</form>


<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('age_week_search');
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
	$arr_class_data['age_all_order_cnt_num'] =  array('grid_sky');
	$arr_class_data['age_all_buy_cnt_num'] =  array('grid_sky');
	$arr_class_data['age_all_buy_price_num'] =  array('grid_sky');

	$arr_table_data = array();

	for($i=0 ; $i<=6 ; $i++){

		$app_date = week_name( $i , '요일');
		$app_date_key = $i;

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

				'age_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['sum']),
				'age_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']),
				'age_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['sum'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_age as $sk=>$sv) {
			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'age_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']),
				'age_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']),
				'age_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sk]['buy_price']['sum'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- 소계 -----



		// ----- PC -----
		$arr_table_data_tmp1 = array(
				'device' => 'PC',

				'age_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']),
				'age_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']),
				'age_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['mobileN'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_age as $sk=>$sv) {

			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'age_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']),
				'age_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']),
				'age_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- PC -----



		// ----- 모바일 -----
		$arr_table_data_tmp1 = array(
				'device' => '모바일',

				'age_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']),
				'age_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']),
				'age_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['mobileY'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_age as $sk=>$sv) {

			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'age_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']),
				'age_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']),
				'age_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY'])
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
				"title" : "<b>전체</b>", "columnName" : "age_all",
				"columnNameList" : [
					"age_all_order_cnt_num",
					"age_all_buy_cnt_num",
					"age_all_buy_price_num"
				]
			},
			<?foreach($arr_order_age as $sk=>$sv) {?>
				<?echo ( $sk <> 10 ? ',' : '' )?>
				{
					"title" : "<b><?=$sv?></b>", "columnName" : "age_<?=$sk?>",
					"columnNameList" : [
						"age_<?=$sk?>_order_cnt_num",
						"age_<?=$sk?>_buy_cnt_num",
						"age_<?=$sk?>_buy_price_num",
					]
				}
			<?}?>
        ],

        columnModelList: [
            {"title" : "<b>요일</b>", "columnName" : "rdate", "align" : "center", "width" : 90 },
			{"title" : "<b>접속기기</b>", "columnName" : "device", "align" : "center", "width" : 90 },

			{"title" : "구매건수", "columnName" : "age_all_order_cnt_num", "align" : "right", "width" : 70 },
			{"title" : "구매수량", "columnName" : "age_all_buy_cnt_num", "align" : "right", "width" : 70 },
			{"title" : "구매금액", "columnName" : "age_all_buy_price_num", "align" : "right", "width" : 120 },

			<?foreach($arr_order_age as $sk=>$sv) {?>
				<?echo ( $sk <> 10 ? ',' : '' )?>
				{"title" : "구매건수", "columnName" : "age_<?=$sk?>_order_cnt_num", "align" : "right", "width" : 70 },
				{"title" : "구매수량", "columnName" : "age_<?=$sk?>_buy_cnt_num", "align" : "right", "width" : 70 },
				{"title" : "구매금액", "columnName" : "age_<?=$sk?>_buy_price_num", "align" : "right", "width" : 120 }
			<?}?>
        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>