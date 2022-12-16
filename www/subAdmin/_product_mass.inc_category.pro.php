<?PHP

	include_once("inc.php");

 
	// 넘어온 변수
	//		==> _mode : add , delete , list 
	//		==> _code : 상품코드
	//		==> _cuid : 카테고리


	// 사전체크
	if( in_array($_mode , array("add" , "delete")) ){
		$_code = nullchk($_code , "상품코드가 확인되지 않습니다.");
	}


	switch($_mode){


		// -- 카테고리 갯수 ---
		case "cnt":

			$_code = nullchk($_code , "상품코드가 확인되지 않습니다.");

			// 갯수
			$cnt = _MQ_result("select count(*) from smart_product_category where pct_pcode='". $_code ."' ");
			echo $cnt;

			break;
		// -- 카테고리 목록 ---



		// -- 카테고리 추가 ---
		case "add":

			//$_code = "TMP01234"; // 임시 상품번호

			// 선택한 최종 카테고리 코드 지정
			$_cuid = $pass_parent01 ? $pass_parent01 : $_cuid ;
			$_cuid = $pass_parent02 ? $pass_parent02 : $_cuid ;
			$_cuid = $pass_parent03 ? $pass_parent03 : $_cuid ;

			// 중복 배제 추가
			$que = "
				INSERT INTO smart_product_category (pct_pcode, pct_cuid) VALUES ('". $_code ."', '". $_cuid ."') 
				ON DUPLICATE KEY UPDATE pct_pcode='". $_code ."' , pct_cuid='". $_cuid ."'
			";
			_MQ_noreturn($que);

			break;
		// -- 카테고리 추가 ---




		// -- 카테고리 삭제 ---
		case "delete":
		
			$_cuid = nullchk($_cuid , "상품분류를 선택해주시기 바랍니다.");

			// 삭제
			$que = "delete from smart_product_category where pct_pcode='". $_code ."' and pct_cuid='". $_cuid ."' ";
			_MQ_noreturn($que);

			break;
		// -- 카테고리 삭제 ---




		// -- 카테고리 목록 ---
		case "list":

			$_code = nullchk($_code , "상품코드가 확인되지 않습니다.");

			// 카테고리 정보 추출 - 배열 정리
			$arr_cate = array();
			$cque = " select * from smart_category ";
			$cres = _MQ_assoc($cque);
			foreach( $cres as $k=>$v ){
				foreach( $v as $sk=>$sv ){
					$arr_cate[$v['c_uid']][$sk] = $sv;
				}
			}

			// 삭제
			$que = "
				select pct.* , ct.*
				from smart_product_category as pct 
				left join smart_category as ct on (ct.c_uid = pct.pct_cuid )
				where 
					pct.pct_pcode='". $_code ."'
					order by pct.pct_uid asc
			";
			$r = _MQ_assoc($que);
			if(sizeof($r) > 0){

				echo "

					<div class='dash_line'><!-- 점선라인 --></div>

					<!-- ● 데이터 리스트 -->
					<table class='table_list'>
						<colgroup>
							<col width='*'><col width='80'>
						</colgroup>
						<thead>
							<tr>
								<th scope='col'>카테고리 명</th>
								<th scope='col'>관리</th>
							</tr>
						</thead> 
						<tbody>
				";
				foreach( $r as $k=>$v ){
					
					// --- 부모 카테고리 정보 정리 ---
					$arr_parent = array();
					$ex = array_merge(explode("," , $v["c_parent"]), array($v['c_uid']));
					foreach( $ex as $sk=>$sv ){
						$arr_parent[$sk] = $arr_cate[$sv]['c_name'];
					}
					// --- 부모 카테고리 정보 정리 ---
					echo "
						<tr>
							<td class='t_left'>". implode(" &gt; " , array_values($arr_parent)) ."</td>
							<td>
								<div class='lineup-vertical'>
									<a href='#none' onclick=\"category_delete('". $v['pct_cuid'] ."');\" class='c_btn h22 gray'>삭제</a>
								</div>
							</td>
						</tr>
					";
				}
				echo "
						</tbody> 
					</table>			
				";
			}
			break;
		// -- 카테고리 목록 ---

	}

?>