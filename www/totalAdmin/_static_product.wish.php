<?php

	include_once('wrap.header.php');


		$pass_sdate = ($pass_sdate?$pass_sdate:date('Y-m-d', strtotime("-1 week")));
		$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d', time()));

		// 검색폼 불러오기
		//		==> 변수 s_query 에 대한 초기화 필요함
		//				s_query -> return 됨
		//		==> 검색 시 smart_product와 smart_order 적용됨.
		$s_query = " where 1 ";
		$search_type = 'wish'; // 찜 타입
		include_once("_static_product.inc_search.php");


	?>


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
				<?=_DescStr("순위는 <strong>찜한 건수가 많은 순</strong>으로 정렬됩니다.</strong>");?>
				<?=_DescStr("순위 <strong>100위까지만 제공</strong>됩니다.");?>
			</div>
		</div>
	</div>
	<!-- / 도표 -->


<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="searchfrm"]').children('input[name="_mode"]').val('product_wish_search');
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
	$arr_class_data['sum_wish_cnt'] =  array('grid_sky');


	$arr_table_data = array();


	// ------- 장바구니 상품 분석 순위별 목록 -------
	$que = "

		select 

			pcode , pname, pimg, pprice, pstock,prdate,
			COUNT(DISTINCT(con_uni)) as sum_wish_cnt

		from
		(
			SELECT 

				p.p_code as pcode,
				p.p_name as pname,
				p.p_img_list_square as pimg,
				p.p_price as pprice,
				p.p_stock as pstock,
				p.p_rdate as prdate,
				
				pw.pw_uid as con_uni

			FROM smart_product_wish as pw
			INNER JOIN smart_product AS p ON ( p.p_code = pw.pw_pcode ) 
			
			" . $s_query . "

		) as tbl_view

		GROUP BY pcode
		ORDER BY 
			sum_wish_cnt DESC ,
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
			'sum_wish_cnt' => number_format($v['sum_wish_cnt'] * 1),
			'pprice' => number_format($v['pprice'] * 1),
			'pstock' => number_format($v['pstock'] * 1),
			'prdate' => DATE("Y-m-d" , strtotime($v['prdate']))

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

			{"title" : "<b>찜한건수</b>", "columnName" : "sum_wish_cnt", "align" : "right", "width" : 150 },
			{"title" : "<b>판매가</b>", "columnName" : "pprice", "align" : "right", "width" : 150 },
			{"title" : "<b>재고량</b>", "columnName" : "pstock", "align" : "right", "width" : 150 },
			{"title" : "<b>등록일</b>", "columnName" : "prdate", "align" : "center", "width" : 150 }

        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>



<?php
	include_once('wrap.footer.php');
?>