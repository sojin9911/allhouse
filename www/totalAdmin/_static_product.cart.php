<?php
	include_once('wrap.header.php');


		$pass_sdate = ($pass_sdate?$pass_sdate:date('Y-m-d', strtotime("-1 week")));
		$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d', time()));

		// 검색폼 불러오기
		//		==> 변수 s_query 에 대한 초기화 필요함
		//				s_query -> return 됨
		//		==> 검색 시 smart_product와 smart_order 적용됨.
		$s_query = " where 1 ";
		$search_type = 'cart'; // 장바구니 타입
		include_once("_static_product.inc_search.php");



		$arr_sum = array();

		// ---- 담은건수, 담은수량, 담은금액, 적립금 요약 ----
		$que = "
			SELECT

				COUNT(DISTINCT(cookie)) as sum_cart_cnt ,
				SUM(sub_sum_buy_cnt) as sum_buy_cnt,
				SUM(sub_sum_buy_price) as sum_buy_price,
				SUM(sub_sum_cart_point) as sum_cart_point

			FROM
			(
				SELECT

					c.c_cookie as cookie,
					SUM( c.c_cnt ) as sub_sum_buy_cnt,
					SUM( c.c_price * c.c_cnt ) as sub_sum_buy_price,
					SUM( c.c_point ) as sub_sum_cart_point

				FROM smart_cart as c
				INNER JOIN smart_product AS p ON ( p.p_code = c.c_pcode )

				" . $s_query . "

				group by cookie

			) as tbl_view
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){
			foreach( $v as $sk=>$sv ){
				$arr_sum[$sk] = $sv;
			}
		}
		// ---- 담은건수, 담은수량, 담은금액 요약 ----

	?>


	<? // ------- 장바구니 상품 순위 분석 Summary ------- ?>
	<div class="group_title"><strong><?=DATE("Y년 m월 d일" , strtotime($pass_sdate) )?> ~ <?=DATE("Y년 m월 d일" , strtotime($pass_edate) )?> 장바구니 상품 순위 분석 요약</strong></div>
	<div class="data_list" style="margin-bottom:30px; ">
		<table class="table_list if_counter_table">
			<colgroup>
				<col width="25%"><col width="25%"><col width="25%"><col width="25%">
			</colgroup>
			<tbody>
				<tr>
					<th >담은건수</th>
					<th >담은수량</th>
					<th >담은금액</th>
					<th >적립금</th>
				</tr>
				<tr>
					<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_cart_cnt'] * 1); ?></span></span></td>
					<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_buy_cnt'] * 1); ?></span></span></td>
					<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_buy_price'] * 1); ?></span></span></td>
					<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_cart_point'] * 1); ?></span></span></td>
				</tr>
			</tbody>
		</table>
		<div class="data_summery" style="border-top:0px">
			<div class="tip_box">
				<?=_DescStr("<strong>장바구니에 담은 상품을 기준으로 분석 정보</strong>를 추출합니다. 이에 따라 장바구니에 없는 정보는 분석에 포함되지 않습니다.");?>
				<?=_DescStr("담은 건수는 <strong>장바구니에 상품을 담은 고객의 수</strong>를 의미합니다.");?>
			</div>
		</div>
	</div>
	<? // ------- 장바구니 상품 순위 분석 Summary ------- ?>




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
				<?=_DescStr("순위는 <strong>담은금액이 많은 순 &gt; 담은 건수가 많은 순 &gt; 담은 수량이 많은 순 &gt; 상품명 순</strong>으로 지정됩니다.</strong>");?>
				<?=_DescStr("순위 <strong>100위까지는만 제공</strong>됩니다.");?>
			</div>
		</div>
	</div>
	<!-- / 도표 -->


<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="searchfrm"]').children('input[name="_mode"]').val('product_cart_search');
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
	$arr_class_data['sum_cart_cnt'] =  array('grid_sky');
	$arr_class_data['sum_buy_cnt'] =  array('grid_sky');
	$arr_class_data['sum_buy_price'] =  array('grid_sky');


	$arr_table_data = array();


	// ------- 장바구니 상품 분석 순위별 목록 -------
	$que = "

		SELECT

			p.p_name as pname,
			p.p_img_list_square as pimg,

			COUNT(*) as sum_cart_cnt ,
			c.c_cnt as sum_buy_cnt,
			( c.c_price * c.c_cnt ) as sum_buy_price,
			c.c_point as sum_cart_point

		FROM smart_cart as c
		LEFT JOIN smart_product AS p ON ( p.p_code = c.c_pcode )

		" . $s_query . "

		GROUP BY c.c_pcode

		ORDER BY
			sum_buy_price DESC ,
			sum_cart_cnt DESC ,
			sum_buy_cnt DESC ,
			sum_cart_point DESC ,
			pname ASC

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
			'sum_cart_cnt' => number_format($v['sum_cart_cnt'] * 1),
			'sum_buy_cnt' => number_format($v['sum_buy_cnt'] * 1),
			'sum_buy_price' => number_format($v['sum_buy_price'] * 1),
			'sum_cart_point' => number_format($v['sum_cart_point'] * 1)

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

        columnModelList: [
            {"title" : "<b>순위</b>", "columnName" : "idx", "align" : "center", "width" : 60 },
			{"title" : "<b>이미지</b>", "columnName" : "product_img", "align" : "center", "width" : 68  },
			{"title" : "<b>상품명</b>", "columnName" : "product_name", "align" : "left", "width" : 450, whiteSpace: 'normal'  },

			{"title" : "<b>담은건수</b>", "columnName" : "sum_cart_cnt", "align" : "right", "width" : 150 },
			{"title" : "<b>담은수량</b>", "columnName" : "sum_buy_cnt", "align" : "right", "width" : 150 },
			{"title" : "<b>담은금액</b>", "columnName" : "sum_buy_price", "align" : "right", "width" : 150 },
			{"title" : "<b>적립금</b>", "columnName" : "sum_cart_point", "align" : "right", "width" : 150 }

        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>



<?php
	include_once('wrap.footer.php');
?>