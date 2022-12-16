<?php


	ini_set('memory_limit', '-1');
	include_once('inc.php');


	// -------------- 검색에 따른 Query 처리 부분 --------------

		$pass_sdate = ($pass_sdate?$pass_sdate:date('Y-m-d', strtotime("-1 week")));
		$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d', time()));

		// 검색폼 불러오기
		//		==> 변수 s_query 에 대한 초기화 필요함
		//				s_query -> return 됨
		//		==> 검색 시 smart_product와 smart_order 적용됨.

		// 장바구니, 찜 상품 등 특정 페이지
		if( IN_ARRAY($_mode , array('product_cart_search' , 'product_wish_search')) ){
			$s_query = " where 1 ";
		}
		else {
			$s_query = " where o.o_canceled!='Y' and o.o_paystatus = 'Y' and op.op_cancel = 'N' ";
		}


		// JJC ::: 브랜드관리 ::: 2017-11-03
		if( $pass_brand !="" ) { $s_query .= " AND p.p_brand = '".$pass_brand."' "; }

		if( $pass_code !="" ) { $s_query .= " and p_code like '%${pass_code}%' "; }
		if( $pass_name !="" ) { $s_query .= " and p_name like '%${pass_name}%' "; }
		if( $pass_view !="" ) { $s_query .= " and p_view='${pass_view}' "; }

		// 입점업체 검색기능 2016-05-26 LDD
		if($pass_com) { $s_query .= " and `p_cpid` = '{$pass_com}' "; }

		if( $_cpid !="" ) { $s_query .= " and p_cpid='${_cpid}' "; }
		if( $_cuid !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$_cuid."') > 0 "; }
		else if( $pass_parent03_real !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$pass_parent03_real."') > 0 "; }
		else if( $pass_parent02_real !="" ) {
			$s_query .= "
				and (
					select
						count(*)
					from smart_product_category as pct
					left join smart_category as c on (c.c_uid = pct.pct_cuid)
					where
						pct.pct_pcode=p.p_code and
						(
							SUBSTRING_INDEX(c.c_parent , ',' , -1) = '" . $pass_parent02_real . "' or
							pct.pct_cuid = '" . $pass_parent02_real . "'
						)
				) > 0
			";
		}
		else if( $pass_parent01 !="" ) {
			$s_query .= "
				and (
					select
						count(*)
					from smart_product_category as pct
					left join smart_category as c on (c.c_uid = pct.pct_cuid)
					where
						pct.pct_pcode=p.p_code and
						(
							SUBSTRING_INDEX(c.c_parent , ',' , 1) = '" . $pass_parent01 . "' or
							pct.pct_cuid = '" . $pass_parent01 . "'
						)
				) > 0
			";
		}

		// - 검색기간
		// 장바구니 상품 분석
		if( IN_ARRAY($_mode , array('product_cart_search')) ){
			if( $pass_sdate && $pass_edate ) { $s_query .= " AND DATE(c.c_rdate) between '". $pass_sdate ."' and '". $pass_edate ."' "; }
			else if( $pass_sdate ) { $s_query .= " AND DATE(c.c_rdate) >= '". $pass_sdate ."' "; }
			else if( $pass_edate ) { $s_query .= " AND DATE(c.c_rdate) <= '". $pass_edate ."' "; }
		}
		// 찜 상품 분석
		else if( IN_ARRAY($_mode , array('product_wish_search')) ){
			if( $pass_sdate && $pass_edate ) { $s_query .= " AND DATE(pw.pw_rdate) between '". $pass_sdate ."' and '". $pass_edate ."' "; }
			else if( $pass_sdate ) { $s_query .= " AND DATE(pw.pw_rdate) >= '". $pass_sdate ."' "; }
			else if( $pass_edate ) { $s_query .= " AND DATE(pw.pw_rdate) <= '". $pass_edate ."' "; }
		}
		// 일반 상품 분석
		else {
			if( $pass_sdate && $pass_edate ) { $s_query .= " AND DATE(o.o_rdate) between '". $pass_sdate ."' and '". $pass_edate ."' "; }
			else if( $pass_sdate ) { $s_query .= " AND DATE(o.o_rdate) >= '". $pass_sdate ."' "; }
			else if( $pass_edate ) { $s_query .= " AND DATE(o.o_rdate) <= '". $pass_edate ."' "; }
		}

	// -------------- 검색에 따른 Query 처리 부분 --------------






	switch($_mode){



		// -------------- 상품분석 ::: 카테고리 판매 순위 분석  ----------------------------
		case "product_category_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_product_category_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
						<tr>
							<td rowspan="2" >순위</td>
							<td rowspan="2" >카테고리</td>
							<td colspan="5" >구매건수</td>
							<td colspan="5" >구매수량</td>
							<td colspan="5" >구매금액</td>
						</tr>
						<tr>
							<!-- 구매건수 -->
								<td >총건수</td>
								<td>회원</td>
								<td >비회원</td>
								<td>PC</td>
								<td >MOBILE</td>
							<!-- 구매수량 -->
								<td >총수량</td>
								<td>회원</td>
								<td >비회원</td>
								<td>PC</td>
								<td >MOBILE</td>
							<!-- 구매금액 -->
								<td >총금액</td>
								<td>회원</td>
								<td >비회원</td>
								<td>PC</td>
								<td >MOBILE</td>
						</tr>
			';

			// 전체 카테고리 정보 추출
			$arr_category_name = array();
			$c_que = " select c_uid , c_name from smart_category ";
			$c_res = _MQ_assoc($c_que);
			foreach( $c_res as $k=>$v ){
				$arr_category_name[$v['c_uid']] = $v['c_name'];
			}
			// 전체 카테고리 정보 추출

			// 전체 카테고리 --> 1,2,3차 전 차수 표시
			$arr_product_category_string = array();
			$pct_que = "SELECT * FROM smart_category ";
			$pct_res = _MQ_assoc($pct_que);
			foreach($pct_res as $pct_sk=>$pct_sv){
				$arr_tmp_string = array();
				if( $pct_sv['c_parent'] > 0 ){
					$ex = explode("," , $pct_sv['c_parent']);
					if($ex[0] > 0 ){$arr_tmp_string[] = $arr_category_name[$ex[0]];}
					if($ex[1] > 0 ){$arr_tmp_string[] = $arr_category_name[$ex[1]];}
				}
				$arr_tmp_string[] = $arr_category_name[$pct_sv['c_uid']];
				$arr_product_category_string[$pct_sv['c_uid']] = implode(" &gt; " , $arr_tmp_string);
			}


			// ------- 카테고리 판매 분석 순위별 목록 -------
			$que = "
				select

					cuid ,

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

							pct.pct_cuid as cuid,
							CONCAT(pct.pct_cuid , '_' , op.op_oordernum) as con_uni,



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


						FROM smart_product_category as pct
						INNER JOIN smart_order_product as op on (op.op_pcode = pct.pct_pcode)
						INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
						LEFT JOIN smart_product AS p ON ( p.p_code = op.op_pcode )

						" . $s_query . "

						AND op.op_pcode is not null

						group by con_uni

					) as tbl_view

					GROUP BY cuid
					ORDER BY
						sum_buy_price DESC,
						sum_order_cnt DESC ,
						sum_buy_cnt DESC,
						cuid ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k => $v ){

				echo '
					<tr>
						<td>'. ($k+1) .'</td>
						<td >'. $arr_product_category_string[$v['cuid']] .'</td>
						<!-- 구매건수 -->
							<td >' . number_format($v['sum_order_cnt'] * 1) . '</td>
							<td>' . number_format($v['sum_memtypeY_order_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_memtypeN_order_cnt'] * 1) . '</td>
							<td>' . number_format($v['sum_mobileN_order_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_mobileY_order_cnt'] * 1) . '</td>
						<!-- 구매수량 -->
							<td >' . number_format(( $v['sum_buy_cnt'] )* 1) . '</td>
							<td>' . number_format($v['sum_memtypeY_buy_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_memtypeN_buy_cnt'] * 1) . '</td>
							<td>' . number_format($v['sum_mobileN_buy_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_mobileY_buy_cnt'] * 1) . '</td>
						<!-- 구매금액 -->
							<td>' . number_format(( $v['sum_buy_price'])* 1) . '</td>
							<td>' . number_format($v['sum_memtypeY_buy_price'] * 1) . '</td>
							<td >' . number_format($v['sum_memtypeN_buy_price'] * 1) . '</td>
							<td>' . number_format($v['sum_mobileN_buy_price'] * 1) . '</td>
							<td >' . number_format($v['sum_mobileY_buy_price'] * 1) . '</td>
					</tr>
				';
			}
			// ------- 카테고리 판매 분석 순위별 목록 -------

			echo '</table>';

			exit;
			break;
		// -------------- 상품분석 ::: 카테고리 판매 순위 분석  ----------------------------



		// -------------- 상품분석 ::: 판매 상품 순위 분석  ----------------------------
		case "product_order_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_product_order_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
						<tr>
							<td rowspan="2" >순위</td>
							<td rowspan="2" >상품명</td>
							<td colspan="5" >구매건수</td>
							<td colspan="5" >구매수량</td>
							<td colspan="5" >구매금액</td>
						</tr>
						<tr>
							<!-- 구매건수 -->
								<td >총건수</td>
								<td>회원</td>
								<td >비회원</td>
								<td>PC</td>
								<td >MOBILE</td>
							<!-- 구매수량 -->
								<td >총수량</td>
								<td>회원</td>
								<td >비회원</td>
								<td>PC</td>
								<td >MOBILE</td>
							<!-- 구매금액 -->
								<td >총금액</td>
								<td>회원</td>
								<td >비회원</td>
								<td>PC</td>
								<td >MOBILE</td>
						</tr>
			';

			// ------- 판매 상품 분석 순위별 목록 -------
			$que = "
				select

					pcode , pname,

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

				echo '
					<tr>
						<td>'. ($k+1) .'</td>
						<td>'. $v['pname'] .'</td>
						<!-- 구매건수 -->
							<td >' . number_format($v['sum_order_cnt'] * 1) . '</td>
							<td>' . number_format($v['sum_memtypeY_order_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_memtypeN_order_cnt'] * 1) . '</td>
							<td>' . number_format($v['sum_mobileN_order_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_mobileY_order_cnt'] * 1) . '</td>

						<!-- 구매수량 -->
							<td >' . number_format(( $v['sum_buy_cnt'] )* 1) . '</td>
							<td>' . number_format($v['sum_memtypeY_buy_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_memtypeN_buy_cnt'] * 1) . '</td>
							<td>' . number_format($v['sum_mobileN_buy_cnt'] * 1) . '</td>
							<td >' . number_format($v['sum_mobileY_buy_cnt'] * 1) . '</td>
						<!-- 구매금액 -->
							<td>' . number_format(( $v['sum_buy_price'])* 1) . '</td>
							<td>' . number_format($v['sum_memtypeY_buy_price'] * 1) . '</td>
							<td >' . number_format($v['sum_memtypeN_buy_price'] * 1) . '</td>
							<td>' . number_format($v['sum_mobileN_buy_price'] * 1) . '</td>
							<td >' . number_format($v['sum_mobileY_buy_price'] * 1) . '</td>
					</tr>
				';
			}
			// ------- 카테고리 판매 분석 순위별 목록 -------

			echo '</table>';

			exit;
			break;
		// -------------- 상품분석 ::: 판매 상품 순위 분석  ----------------------------



		// -------------- 상품분석 ::: 장바구니 상품 순위 분석  ----------------------------
		case "product_cart_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_product_cart_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
						<tr>
							<td>순위</td>
							<td>상품명</td>
							<td>담은건수</td>
							<td>담은수량</td>
							<td>담은금액</td>
							<td>적립금</td>
						</tr>
			';

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

				echo '
					<tr>
						<td>'. ($k+1) .'</td>
						<td >'. $v['pname'] .'</span></td>
						<td>' . number_format($v['sum_cart_cnt'] * 1) . '</td>
						<td>' . number_format(( $v['sum_buy_cnt'] )* 1) . '</td>
						<td>' . number_format(( $v['sum_buy_price'])* 1) . '</td>
						<td>' . number_format(( $v['sum_cart_point'])* 1) . '</td>

					</tr>
				';
			}
			// ------- 장바구니 상품 분석 순위별 목록 -------

			echo '</table>';

			exit;
			break;
		// -------------- 상품분석 ::: 장바구니 상품 순위 분석  ----------------------------



		// -------------- 상품분석 ::: 찜 상품 순위 분석  ----------------------------
		case "product_wish_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_product_wish_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
						<tr>
							<td>순위</td>
							<td>상품명</td>
							<td >찜한건수</td>
							<td >판매가</td>
							<td >재고량</td>
							<td >등록일</td>
						</tr>
			';

			// ------- 찜 상품 분석 순위별 목록 -------
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

				echo '
					<tr>
						<td>'. ($k+1) .'</td>
						<td >'. $v['pname'] .'</span></td>
						<td>' . number_format($v['sum_wish_cnt'] * 1) . '</td>
						<td>' . number_format(( $v['pprice'] )* 1) . '</td>
						<td>' . number_format(( $v['pstock'])* 1) . '</td>
						<td>' . DATE("Y-m-d" , strtotime($v['prdate'])) . '</td>
					</tr>
				';
			}
			// ------- 찜 상품 분석 순위별 목록 -------

			echo '</table>';

			exit;
			break;
		// -------------- 상품분석 ::: 찜 상품 순위 분석  ----------------------------




	}
exit;