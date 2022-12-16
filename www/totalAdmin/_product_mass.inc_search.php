<?php


	// --------------------- 상품 일괄 관리 --- 검색폼 부분 ---------------------
	//			해당 파일 include 전 s_query가 정의되어야 함.
	//			예) $s_query = " from smart_product as p where 1 ";


	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info("product_name_small_icon");


	// 2017-06-16 ::: 부가세율설정 ::: JJC
	if( $pass_vat !="" ) { $s_query .= " AND p.p_vat = '".$pass_vat."' "; }

	// JJC ::: 브랜드관리 ::: 2017-11-03
	if( $pass_brand !="" ) { $s_query .= " AND p.p_brand = '".$pass_brand."' "; }

	if( $pass_code !="" ) { $s_query .= " and p_code like '%${pass_code}%' "; }
	if( $pass_name !="" ) { $s_query .= " and p_name like '%${pass_name}%' "; }
	if( $pass_view !="" ) { $s_query .= " and p_view='${pass_view}' "; }
	if( $pass_newview !="" ) { $s_query .= " and p_newview='${pass_newview}' "; }
	if( $pass_bestview !="" ) { $s_query .= " and p_bestview='${pass_bestview}' "; }
	if( $pass_saleview !="" ) { $s_query .= " and p_saleview='${pass_saleview}' "; }
	if($pass_stock != '') {
		if($pass_stock == 'x') $s_query .= " and p_stock <= 0 ";
		else if($pass_stock == 30) $s_query .= " and p_stock > 0 and p_stock <= 30 ";
		else if($pass_stock == 50) $s_query .= " and p_stock > 30 and p_stock <= 50 ";
		else if($pass_stock == 100) $s_query .= " and p_stock > 50 ";
	}
	// 입점업체 검색기능 2016-05-26 LDD
	if($pass_com) {
		$s_query .= " and `p_cpid` = '{$pass_com}' ";
	}

	if( sizeof($pass_icon) > 0 ) {
		foreach($pass_icon as $k0 => $v0) $s_query_icon[] = " find_in_set('".$v0."',p_icon) ";

		$s_query .= " and (". implode(" or ",$s_query_icon) .") ";
	}

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
					find_in_set('" . $pass_parent02_real . "' , c.c_parent)>0
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
					find_in_set('" . $pass_parent01 . "' , c.c_parent)>0
			) > 0
		";
	}

?>


	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>상품검색</strong>
	</div>




	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">

		<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="pass_limit" value="<?php echo $pass_limit; ?>">
		<input type="hidden" name="pass_orderby" value="<?php echo $pass_orderby; ?>">
		<input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">

			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>상품코드</th>
						<td><input type="text" name="pass_code" class="design" style="" value="<?php echo $pass_code; ?>"></td>
						<th>상품명</th>
						<td><input type="text" name="pass_name" class="design" style="" value="<?php echo $pass_name; ?>"></td>
					</tr>
					<tr>
						<th>상품 노출여부</th>
						<td>
							<?php echo _InputRadio( "pass_view" , array('', 'Y','N'), $pass_view , "" , array('전체', '노출','숨김') ); ?>
						</td>
						<th>베스트 상품</th>
						<td>
							<?php echo _InputRadio( "pass_bestview" , array('', 'Y','N'), $pass_bestview , "" , array('전체', '노출','숨김') ); ?>
						</td>
					</tr>
					<tr>
						<th>신규 상품</th>
						<td>
							<?php echo _InputRadio( "pass_newview" , array('', 'Y','N'), $pass_newview , "" , array('전체', '노출','숨김') ); ?>
						</td>
						<th>MD's pick</th>
						<td>
							<?php echo _InputRadio( "pass_saleview" , array('', 'Y','N'), $pass_saleview , "" , array('전체', '노출','숨김') ); ?>
						</td>
					</tr>
					<tr>
						<th>브랜드</th>
						<td>
							<?php
								// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
								//		basic : 기본정보
								//		all : 브랜드 전체 정보
								$arr_brand = brand_info('basic');
								echo _InputSelect( "pass_brand" , array_keys($arr_brand) , $pass_brand , "" , array_values($arr_brand) , "-브랜드-");
							?>
						</td>
						<th>상품 아이콘</th>
						<td >
							<?
							$r2 = $product_icon;
							$pi_uid_array = is_array($pass_icon) ? $pass_icon : array();
							if(sizeof($r2) > 0){
								foreach($r2 as $k2 => $v2) {
									$checked = @array_search($v2['pi_uid'],$pi_uid_array) === false ? NULL : " checked ";
									echo "<label class='design'><input type='checkbox' name='pass_icon[]' value='".$v2['pi_uid']."' ".$checked."><img src='".IMG_DIR_ICON.$v2['pi_img']."' title = '".$v2['pi_title']."'></label>";
								}
							}else{
								echo _DescStr('등록된 상품 아이콘이 없습니다.');
							}
							?>
						</td>
					</tr>
					<tr>
						<th>카테고리</th>
						<td colspan="3">
							<?PHP
								// 상품 카테고리 분류 (list , form 을 공통으로 쓰기 위한 조치)
								// 1차 - pass_parent01 -> app_depth1
								// 2차 - pass_parent02_real -> app_depth2
								// 3차 - pass_parent03_real -> $row[p_cuid]
								if( $pass_parent01 ) {
									$app_depth1 =  $pass_parent01 ;
								}
								if( $pass_parent02_real ) {
									$app_depth2 =  $pass_parent02_real ;
								}
								if( $pass_parent03_real ) {
									$app_depth3 =  $pass_parent03_real ;
								}

								$pass_parent03_no_required = "Y";

								include_once(OD_PROGRAM_ROOT."/category.inc.php");
							?>
						</td>
					</tr>

					<?php
						if($SubAdminMode === true) { // 입점업체 검색기능 2016-05-26 LDD
							$arr_customer = arr_company();
							$arr_customer2 = arr_company2();
					?>
					<tr>
						<th>입점업체</th>
						<td colspan="3">
							<?=_InputSelect( "pass_com" , array_keys($arr_customer) , $pass_com, "" , array_values($arr_customer) , "-입점업체-")?>
						</td>
					</tr>
					<?php } ?>

				</tbody>
			</table>
			<!-- 폼테이블 2단 -->


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" /></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?=$_SERVER['SCRIPT_NAME']?><?php echo ($pass_limit || $pass_orderby?'?'.implode('&', array_filter(array(($pass_limit?'pass_limit='.$pass_limit:''), ($pass_orderby?'pass_orderby='.$pass_orderby:'')))):null); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

		</form>

	</div>
