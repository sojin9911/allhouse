<?php


	// --------------------- 상품 일괄 관리 --- 검색폼 부분 ---------------------
	//			해당 파일 include 전 s_query가 정의되어야 함.
	//			예) $s_query = " from smart_product as p where 1 ";
	// 추가파라메터
	if(!$arr_param) $arr_param = array();

	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info("product_name_small_icon");


	// 2017-06-16 ::: 부가세율설정 ::: JJC
	if( $pass_vat !="" ) { $s_query .= " AND p.p_vat = '".$pass_vat."' "; }

	// JJC ::: 브랜드관리 ::: 2017-11-03
	if( $pass_brand !="" ) { $s_query .= " AND p.p_brand = '".$pass_brand."' "; }

	if( $pass_code !="" ) { $s_query .= " and p_code like '%" . trim($pass_code) . "%' "; }
	if( $pass_name !="" ) { $s_query .= " and p_name like '%" . trim($pass_name) . "%' "; }
	if( $pass_view !="" ) { $s_query .= " and p_view='${pass_view}' "; }
	if( $pass_option_valid_chk !="" ) { $s_query .= " and p_option_valid_chk='${pass_option_valid_chk}' "; }
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

	// LCY : 네이버페이 사용유무 추가 : 2020-10-20 
	if( $pass_npay_use !="" ) { $s_query .= " and npay_use ='".$pass_npay_use."' "; }

?>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">

		<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
		<input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">
		<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
		<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
		<?php }} ?>

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
					</tr>
					<tr>
						<th>옵션등록오류체크</th>
						<td>
							<?php echo _InputRadio('pass_option_valid_chk' , array('', 'Y','N') , $pass_option_valid_chk, '' , array('전체', '정상','오류'));  ?>
							<div class="tip_box">
								<?php echo _DescStr('상품의 옵션설정과 상품옵션의 등록된 실제 옵션 차수가 맞지 않을 경우 오류로 표시됩니다.'); ?>
							</div>
						</td>
						<th>재고검색</th>
						<td>
							<?php echo _InputSelect('pass_stock' , array('x',30,50,100) , $pass_stock, '' , array('품절', '1개 ~ 30개', '31개 ~ 50개', '51개 이상'), '-재고량-');  ?>
						</td>
					</tr>
					<tr>
						<?php // 2017-06-16 ::: 부가세율설정 ::: JJC ?>
						<?php if($siteInfo['s_vat_product'] == 'C'){ ?>
						<th>과세여부</th>
						<td>
							<?php echo _InputRadio( 'pass_vat' , array('', 'Y','N'), $pass_vat , '' , array('전체', '과세','면세')); ?>
						</td>
						<?php } ?>
						<th>상품 아이콘</th>
						<td<?php echo ($siteInfo['s_vat_product'] <> 'C' ? ' colspan="3" ' : null); ?>>
							<?
							$r2 = $product_icon;
							$pi_uid_array = is_array($pass_icon) ? $pass_icon : array();
							if(sizeof($r2) > 0){
								foreach($r2 as $k2 => $v2) {
									$checked = (@array_search($v2['pi_uid'],$pi_uid_array) === false || !IS_ARRAY($pi_uid_array) ? NULL : " checked ");
									echo "<label class='design'><input type='checkbox' name='pass_icon[]' value='".$v2['pi_uid']."' ".$checked."><img src='".IMG_DIR_ICON.$v2['pi_img']."' title = '".$v2['pi_title']."'></label>";
								}
							}else{
								echo _DescStr('등록된 상품 아이콘이 없습니다.');
							}
							?>
						</td>
					</tr>

					<?php if( $_SERVER['PHP_SELF'] == '/totalAdmin/_category.best_product.pop.php' ) { ?>

					<?php }else { ?>
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
					<?php } ?>
					<?php
						if($SubAdminMode === true && $AdminPath == 'totalAdmin') { // 입점업체 검색기능 2016-05-26 LDD
							$arr_customer = arr_company();
							$arr_customer2 = arr_company2();
					?>
					<tr>
						<th>입점업체</th>
						<td colspan="3">
							<!-- 20개 이상일때만 select2적용 -->
							<?php if(sizeof($arr_customer) > 20){ ?>
							<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
							<script src="/include/js/select2/js/select2.min.js"></script>
							<script>$(document).ready(function() { $('.select2').select2(); });</script>
							<?php } ?>
							<?php echo _InputSelect( 'pass_com' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '-입점업체-'); ?>
						</td>
					</tr>
					<?php } ?>

					<?php // LCY : 네이버페이 사용유무 추가 : 2020-10-20 ?>
					<tr>
						<th>네이버페이사용유무</th>
						<td colspan="3">
							<?php echo _InputRadio('pass_npay_use' , array('', 'Y','N') , $pass_npay_use, '' , array('전체', '사용','미사용'));  ?>
						</td>
					</tr>
					<?php // LCY : 네이버페이 사용유무 추가 : 2020-10-20 ?>



				</tbody>
			</table>
			<!-- 폼테이블 2단 -->


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php
						if($mode == 'search'){
							$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount),$arr_param));
					?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

		</form>

	</div>
