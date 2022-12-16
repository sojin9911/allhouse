<?php

	include_once('wrap.header.php');


		$pass_sdate = ($pass_sdate?$pass_sdate:date('Y-m-d', strtotime("-1 week")));
		$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d', time()));

		// 검색폼 불러오기
		//		==> 변수 s_query 에 대한 초기화 필요함
		//				s_query -> return 됨
		//		==> 검색 시 smart_product와 smart_order 적용됨.
		$s_query = " where o.o_canceled!='Y' AND o.o_paystatus = 'Y' and op.op_cancel = 'N' ";

		include_once("_static_product.inc_search.php");



		$arr_sum = array();

		// ---- 구매건수, 구매수량, 구매금액 요약 ----
		$que = "
			SELECT

				SUM(subsum_mobileY_order_cnt) as sum_mobileY_order_cnt,
				SUM(subsum_mobileN_order_cnt) as sum_mobileN_order_cnt,

				SUM(subsum_memtypeN_order_cnt) as sum_memtypeN_order_cnt,
				SUM(subsum_memtypeY_order_cnt) as sum_memtypeY_order_cnt,

				COUNT(DISTINCT(ordernum)) as sum_order_cnt ,


				SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
				SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

				SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
				SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

				SUM(sub_sum_buy_cnt) as sum_buy_cnt,

				SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
				SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

				SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
				SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

				SUM(sub_sum_buy_price) as sum_buy_price

			FROM
			(
				SELECT

					op.op_oordernum as ordernum,

					IF( o.mobile = 'Y' , 1 , 0 ) as subsum_mobileY_order_cnt,
					IF( o.mobile != 'Y' , 1 , 0 ) as subsum_mobileN_order_cnt,

					IF( o.o_memtype = 'N' , 1 , 0 ) as subsum_memtypeN_order_cnt,
					IF( o.o_memtype != 'N' , 1 , 0 ) as subsum_memtypeY_order_cnt,



					SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
					SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

					SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
					SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

					SUM( op.op_cnt ) as sub_sum_buy_cnt,

					SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
					SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

					SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
					SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

					SUM( op_price * op_cnt ) as sub_sum_buy_price

				FROM smart_order_product as op
				INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
				LEFT JOIN smart_product AS p ON ( p.p_code = op.op_pcode )

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

	?>


	<? // ------- 판매 상품 순위 분석 Summary ------- ?>
	<div class="group_title"><strong><?=DATE("Y년 m월 d일" , strtotime($pass_sdate) )?> ~ <?=DATE("Y년 m월 d일" , strtotime($pass_edate) )?> 판매 상품 순위 분석 요약</strong></div>
	<div class="data_list" style="margin-bottom:30px; ">
		<table class="table_list if_counter_table">
			<colgroup>
				<col width="6.6%"><col width="6.6%"><col width="6.6%"><col width="6.6%"><col width="6.7%">
				<col width="6.6%"><col width="6.6%"><col width="6.6%"><col width="6.6%"><col width="6.7%">
				<col width="6.6%"><col width="6.6%"><col width="6.6%"><col width="6.6%"><col width="6.7%">
			</colgroup>
			<tbody>
				<tr>
					<th colspan="5" class='line'>구매건수</th>
					<th colspan="5" class='line'>구매수량</th>
					<th colspan="5" >구매금액</th>
				</tr>
				<tr>
					<!-- 구매건수 -->
						<th class="line">총건수</th>
						<th>회원</th>
						<th class='line'>비회원</th>
						<th>PC</th>
						<th class='line'>MOBILE</th>
					<!-- 구매수량 -->
						<th class="line">총수량</th>
						<th>회원</th>
						<th class='line'>비회원</th>
						<th>PC</th>
						<th class='line'>MOBILE</th>
					<!-- 구매금액 -->
						<th >총금액</th>
						<th>회원</th>
						<th class='line'>비회원</th>
						<th>PC</th>
						<th class='line'>MOBILE</th>
				</tr>
				<tr>
					<!-- 구매건수 -->
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_order_cnt'] * 1); ?></span></span></td>
						<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeY_order_cnt'] * 1); ?></span></span></td>
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeN_order_cnt'] * 1); ?></span></span></td>
						<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileN_order_cnt'] * 1); ?></span></span></td>
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileY_order_cnt'] * 1); ?></span></span></td>
					<!-- 구매수량 -->
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format(( $arr_sum['sum_buy_cnt'] )* 1); ?></span></span></td>
						<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeY_buy_cnt'] * 1); ?></span></span></td>
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeN_buy_cnt'] * 1); ?></span></span></td>
						<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileN_buy_cnt'] * 1); ?></span></span></td>
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileY_buy_cnt'] * 1); ?></span></span></td>
					<!-- 구매금액 -->
						<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format(( $arr_sum['sum_buy_price'])* 1); ?></span></span></td>
						<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeY_buy_price'] * 1); ?></span></span></td>
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeN_buy_price'] * 1); ?></span></span></td>
						<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileN_buy_price'] * 1); ?></span></span></td>
						<td class='line'><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileY_buy_price'] * 1); ?></span></span></td>
				</tr>
			</tbody>
		</table>
		<div class="data_summery" style="border-top:0px">
			<div class="tip_box">
				<?=_DescStr("취소되지 않고 결제가 된 <strong>정상적인 주문 건을 기준으로 분석 정보</strong>를 추출합니다.");?>
				<?=_DescStr("구매 건수는 주문 횟수를 의미합니다.");?>
				<?=_DescStr("총건수, 총수량 ,총금액은 <strong>회원/비회원 또는 PC/MOBILE의 합산</strong>으로  <strong><u>회원/비회원/PC/MOBILE을 합산한 수치가 아닙니다.</u></strong>");?>
			</div>
		</div>
	</div>
	<? // ------- 판매 상품 순위 분석 Summary ------- ?>




	<div class="data_list">
		<div class="list_ctrl">
			<div class="right_box">
				<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel">엑셀다운로드</a>
			</div>
		</div>



		<?// ----- 표 테이블  -----?>
		<div ID="grid_table"></div>

		<div class="data_summery" style="border-top:0px">
			<div class="tip_box">
				<?=_DescStr("상품의 순위는 <strong>구매금액이 많은 순 &gt; 구매건수가 많은 순 &gt; 구매수량이 많은 순 &gt; 상품명 순</strong>으로 지정됩니다.</strong>");?>
				<?=_DescStr("상품 순위 <strong>100위까지는만 제공</strong>됩니다.");?>
			</div>
		</div>
	</div>
	<!-- / 도표 -->


<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="searchfrm"]').children('input[name="_mode"]').val('product_order_search');
		$('form[name="searchfrm"]').attr('method', 'post');
		$('form[name="searchfrm"]').attr('action', '_static_product.pro.php');
		$('form[name="searchfrm"]')[0].submit();
		$('form[name="searchfrm"]').attr('method', 'get');
		$('form[name="searchfrm"]').attr('action', '<?php echo $_SERVER["PHP_SELF"]?>');
	}
	// --- 검색 엑셀 ---
</script>









<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['idx'] = array('grid_no'); // 순위 grid_no 클래스 적용
	$arr_class_data['order_cnt_tot'] =  array('grid_sky');
	$arr_class_data['buy_cnt_tot'] =  array('grid_sky');
	$arr_class_data['buy_price_tot'] =  array('grid_sky');


	$arr_table_data = array();


	// ------- 판매 상품 분석 순위별 목록 -------
	$que = "
		select

			pcode , pname, pimg,

			SUM(subsum_mobileY_order_cnt) as sum_mobileY_order_cnt,
			SUM(subsum_mobileN_order_cnt) as sum_mobileN_order_cnt,

			SUM(subsum_memtypeN_order_cnt) as sum_memtypeN_order_cnt,
			SUM(subsum_memtypeY_order_cnt) as sum_memtypeY_order_cnt,

			COUNT(DISTINCT(con_uni)) as sum_order_cnt ,


			SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
			SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

			SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
			SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

			SUM(sub_sum_buy_cnt) as sum_buy_cnt,

			SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
			SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

			SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
			SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

			SUM(sub_sum_buy_price) as sum_buy_price

		from
		(
			SELECT

				op.op_pcode as pcode,
				CONCAT(op.op_pcode , '_' , op.op_oordernum) as con_uni,
				p.p_name as pname,
				p.p_img_list_square as pimg,


				IF( o.mobile = 'Y' , 1 , 0 ) as subsum_mobileY_order_cnt,
				IF( o.mobile != 'Y' , 1 , 0 ) as subsum_mobileN_order_cnt,

				IF( o.o_memtype = 'N' , 1 , 0 ) as subsum_memtypeN_order_cnt,
				IF( o.o_memtype != 'N' , 1 , 0 ) as subsum_memtypeY_order_cnt,



				SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
				SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

				SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
				SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

				SUM( op.op_cnt ) as sub_sum_buy_cnt,

				SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
				SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

				SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
				SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

				SUM( op_price * op_cnt ) as sub_sum_buy_price

			FROM smart_order_product as op
			INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
			LEFT JOIN smart_product AS p ON ( p.p_code = op.op_pcode )

			" . $s_query . "

			group by con_uni

		) as tbl_view

		GROUP BY pcode
		ORDER BY
			sum_buy_price DESC ,
			sum_order_cnt DESC ,
			sum_buy_cnt DESC ,
			pname ASC,
			pcode ASC
		LIMIT 0, 100
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k => $v ){

		// 이미지 검사
		if($v['pimg'] && file_exists('..' . IMG_DIR_PRODUCT . $v['pimg'])){
			$_p_img = get_img_src($v['pimg']);
		}else{
			$_p_img = 'images/thumb_no.jpg';
		}

		// ----- 소계 -----
		$arr_table_data[] = array(
			'_extraData' => array(
				'className' =>array(
					'column' => $arr_class_data
				)
			),
			'idx' => ($k+1),
			'product_img' => '<img src="' . $_p_img . '" alt=' . addslashes(strip_tags($v['pname'])) . '" style="width:50px;">',
			'product_name' => $v['pname'],

			// 구매건수
			'order_cnt_tot' => number_format($v['sum_order_cnt'] * 1),
			'order_cnt_memY' => number_format($v['sum_memtypeY_order_cnt'] * 1),
			'order_cnt_memN' => number_format($v['sum_memtypeN_order_cnt'] * 1),
			'order_cnt_mobileN' => number_format($v['sum_mobileN_order_cnt'] * 1),
			'order_cnt_mobileY' => number_format($v['sum_mobileY_order_cnt'] * 1),

			// 구매수량
			'buy_cnt_tot' => number_format($v['sum_buy_cnt'] * 1),
			'buy_cnt_memY' => number_format($v['sum_memtypeY_buy_cnt'] * 1),
			'buy_cnt_memN' => number_format($v['sum_memtypeN_buy_cnt'] * 1),
			'buy_cnt_mobileN' => number_format($v['sum_mobileN_buy_cnt'] * 1),
			'buy_cnt_mobileY' => number_format($v['sum_mobileY_buy_cnt'] * 1),

			// 구매금액
			'buy_price_tot' => number_format($v['sum_buy_price'] * 1),
			'buy_price_memY' => number_format($v['sum_memtypeY_buy_price'] * 1),
			'buy_price_memN' => number_format($v['sum_memtypeN_buy_price'] * 1),
			'buy_price_mobileN' => number_format($v['sum_mobileN_buy_price'] * 1),
			'buy_price_mobileY' => number_format($v['sum_mobileY_buy_price'] * 1)
		);
		// ----- 소계 -----

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

        columnFixCount: 3,
        headerHeight: 80,
		rowHeight  : 60,
        displayRowCount: 8,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnMerge : [
			{"title" : "<b>상품정보</b>", "columnName" : "product_info", "columnNameList" : ["product_img" , "product_name"] },
			{"title" : "<b>구매건수</b>", "columnName" : "order_cnt", "columnNameList" : ["order_cnt_tot", "order_cnt_memY", "order_cnt_memN", "order_cnt_mobileN", "order_cnt_mobileY"] },
			{"title" : "<b>구매수량</b>", "columnName" : "buy_cnt", "columnNameList" : ["buy_cnt_tot", "buy_cnt_memY", "buy_cnt_memN", "buy_cnt_mobileN", "buy_cnt_mobileY"] },
			{"title" : "<b>구매금액</b>", "columnName" : "buy_price", "columnNameList" : ["buy_price_tot", "buy_price_memY", "buy_price_memN", "buy_price_mobileN", "buy_price_mobileY"] }
        ],

        columnModelList: [
            {"title" : "<b>순위</b>", "columnName" : "idx", "align" : "center", "width" : 60 },
			{"title" : "<b>이미지</b>", "columnName" : "product_img", "align" : "center", "width" : 68  },
			{"title" : "<b>상품명</b>", "columnName" : "product_name", "align" : "left", "width" : 350, whiteSpace: 'normal'  },

			// 구매건수
			{"title" : "<b>총건수</b>", "columnName" : "order_cnt_tot", "align" : "right", "width" : 80 },
			{"title" : "<b>회원</b>", "columnName" : "order_cnt_memY", "align" : "right", "width" : 70 },
			{"title" : "<b>비회원</b>", "columnName" : "order_cnt_memN", "align" : "right", "width" : 70 },
			{"title" : "<b>PC</b>", "columnName" : "order_cnt_mobileN", "align" : "right", "width" : 70 },
			{"title" : "<b>MOBILE</b>", "columnName" : "order_cnt_mobileY", "align" : "right", "width" : 70 },

			// 구매수량
			{"title" : "<b>총수량</b>", "columnName" : "buy_cnt_tot", "align" : "right", "width" : 80 },
			{"title" : "<b>회원</b>", "columnName" : "buy_cnt_memY", "align" : "right", "width" : 70 },
			{"title" : "<b>비회원</b>", "columnName" : "buy_cnt_memN", "align" : "right", "width" : 70 },
			{"title" : "<b>PC</b>", "columnName" : "buy_cnt_mobileN", "align" : "right", "width" : 70 },
			{"title" : "<b>MOBILE</b>", "columnName" : "buy_cnt_mobileY", "align" : "right", "width" : 70 },

			// 구매금액
			{"title" : "<b>총금액</b>", "columnName" : "buy_price_tot", "align" : "right", "width" : 110 },
			{"title" : "<b>회원</b>", "columnName" : "buy_price_memY", "align" : "right", "width" : 90 },
			{"title" : "<b>비회원</b>", "columnName" : "buy_price_memN", "align" : "right", "width" : 90 },
			{"title" : "<b>PC</b>", "columnName" : "buy_price_mobileN", "align" : "right", "width" : 90 },
			{"title" : "<b>MOBILE</b>", "columnName" : "buy_price_mobileY", "align" : "right", "width" : 90 }

        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>



<?php
	include_once('wrap.footer.php');
?>