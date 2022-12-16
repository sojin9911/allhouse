<?PHP

	include_once("inc.php");


	// 넘어온 변수
	//		==> _cmode : add , delete , list
	//		==> _code : 상품코드
	//		==> _cuid : 카테고리


	// 사전체크
	if( in_array($_cmode , array("add" , "delete")) ){
		$_code = nullchk($_code , "상품코드가 확인되지 않습니다.");
	}


	switch($_cmode){


		// -- 카테고리 추가 ---
		case "add":

			// 선택한 최종 카테고리 코드 지정
			$_cuid = $pass_parent01 ? $pass_parent01 : $_cuid ;
			$_cuid = $pass_parent02 ? $pass_parent02 : $_cuid ;
			$_cuid = $pass_parent03 ? $pass_parent03 : $_cuid ;
			$_cuid = $pass_parent04 ? $pass_parent04 : $_cuid ;

			if(!$_cuid) exit;

			// 중복 배제 추가
			$que = "
				INSERT INTO smart_product_category (pct_pcode, pct_cuid) VALUES ('". $_code ."', '". $_cuid ."')
				ON DUPLICATE KEY UPDATE pct_pcode='". $_code ."' , pct_cuid='". $_cuid ."'
			";
			_MQ_noreturn($que);

			_MQ_noreturn("update smart_product set p_cuid='".$_cuid."' where p_code='".$_code."'");
			// 카테고리 상품 갯수 업데이트
			update_catagory_product_count();

			break;
		// -- 카테고리 추가 ---




		// -- 카테고리 삭제 ---
		case "delete":

			$_cuid = nullchk($_cuid , "상품분류를 선택해주시기 바랍니다.");

			// 삭제
			$que = "delete from smart_product_category where pct_pcode='". $_code ."' and pct_cuid='". $_cuid ."' ";
			_MQ_noreturn($que);

			// 카테고리 상품 갯수 업데이트
			update_catagory_product_count();

			break;
		// -- 카테고리 삭제 ---




		// -- 카테고리 목록 ---
		case "list":

			// SSJ :: 삭제된 카테고리 일괄 삭제 ---- 2020-02-05
			$r = _MQ(" select group_concat(pct_uid) as uids from smart_product_category as pct where (select count(*) from smart_category as ct where ct.c_uid = pct.pct_cuid) = 0 ");
			if($r['uids'] <> ''){ _MQ_noreturn(" delete from smart_product_category where pct_uid in (". $r['uids'] .") "); }
			// SSJ :: 삭제된 카테고리 일괄 삭제 ---- 2020-02-05

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
			echo "
				<!-- ● 데이터 리스트 -->
				<table class='table_list'>
					<colgroup>
						<col width='*'><col width='70'>
					</colgroup>
					<thead>
						<tr>
							<th scope='col'>카테고리 명</th>
							<th scope='col'>관리</th>
						</tr>
					</thead>
					<tbody>
			";
			if(sizeof($r) > 0){
				foreach( $r as $k=>$v ){

					// --- 부모 카테고리 정보 정리 ---
					$arr_parent = array();
					$ex = explode("," , $v["c_parent"]);
					foreach( $ex as $sk=>$sv ){
						$arr_parent[$sk] = $arr_cate[$sv]['c_name'];
					}
					$arr_parent[($sk+1)] = $v['c_name'];
					$arr_parent = array_filter($arr_parent);
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
			}else{
				echo "
					<tr>
						<td class='t_left' colspan='2'>※ 카테고리를 선택해주세요.</td>
					</tr>
				";
			}
			echo "
					</tbody>
				</table>
			";
			break;
		// -- 카테고리 목록 ---

	}

?>