<?php

// 페이지 표시
$app_current_page_name = "상품관리";
include dirname(__FILE__)."/wrap.header.php";


$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $k => $v) { if( is_array($v) ){foreach($v as $sk => $sv) { $_PVS .= "&" . $k . "[".$sk."]=$sv"; }}else {$_PVS .= "&$k=$v"; }}
$_PVSC = enc('e' , $_PVS);


// 아이콘 정보 배열로 추출
$product_icon = get_product_icon_info("product_name_small_icon");

// 상품명 체크
if($search_type == "open") {$pass_name_tmp = $pass_name ? $pass_name : $pass_name_tmp;}
else {$pass_name = $pass_name_tmp ? $pass_name_tmp : $pass_name;}

######## 검색 체크
$s_query = " from smart_product as p left join smart_category as c on (c.c_uid=p.p_cuid) where 1  ";
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
	if($pass_stock == 'x') $s_query .= " and p_stock = 0 ";
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

$s_orderby = " ORDER BY p_idx asc ";
if( $pass_orderby == "radte" ) {
	$s_orderby = " ORDER BY p_rdate desc ";
}
if(!$pass_limit) {$pass_limit = 5;}
$listmaxcount = $pass_limit ;
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


$res = _MQ(" select count(*) as cnt  $s_query ");
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);
?>

<form name="searchfrm" method="post" action="<?=$PHP_SELF?>">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="search_type" value="close">
	<!-- 상단에 들어가는 검색등 공간 검색닫기를 누르면  if_closed 처음설정을 닫혀있도록 해도 좋을듯.. -->
	<div class="page_top_area if_closed">

		<div class="title_box"><span class="txt">SEARCH</span>
			<div class="before_search">
				<button type="submit" class="btn_search"></button>
				<input type="search" name="pass_name_tmp" value="<?=$pass_name_tmp?>" class="input_design" placeholder="상품명 검색">
			</div>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_close" title="검색닫기">상세검색닫기<span class="shape"></span></a>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_open" title="검색열기">상세검색열기<span class="shape"></span></a>
		</div>

		<!-- ●●●●● 검색폼 -->
		<div class="cm_search_form">
			<ul>
				<li>
					<span class="opt">상품코드</span>
					<div class="value"><input type="text" name="pass_code" value="<?=$pass_code?>" class="input_design" placeholder="상품코드를 입력하세요." /></div>
				</li>
				<li>
					<span class="opt">상품명</span>
					<div class="value"><input type="text" name="pass_name" value="<?=$pass_name?>" class="input_design" placeholder="상품명을 입력하세요." /></div>
				</li>
				<li>
					<span class="opt">노출여부</span>
					<div class="value"><?=_InputRadio_totaladmin( "pass_view" , array('', "N" , "Y") , $pass_view , "" , array('전체', "숨김" , "노출") , "")?></div>
				</li>


				<?php // 2017-06-16 ::: 부가세율설정 ::: JJC ?>
				<?php if($siteInfo['s_vat_product'] == 'C'){ ?>
					<li>
						<span class="opt">과세여부</span>
						<div class="value">
							<?php echo _InputRadio( 'pass_vat' , array('', 'Y','N'), $pass_vat , '' , array('전체', '과세','면세')); ?>
						</div>
					</li>
				<?php } ?>
				<li>
					<span class="opt">브랜드</span>
					<div class="value">
						<div class="select">
							<span class="shape"></span>
							<?php
								// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
								//		basic : 기본정보
								//		all : 브랜드 전체 정보
								$arr_brand = brand_info('basic');
								echo _InputSelect( "pass_brand" , array_keys($arr_brand) , $pass_brand , "" , array_values($arr_brand) , "-브랜드-");
							?>
						</div>
					</div>
				</li>

				<li>
					<span class="opt">재고 검색</span>
					<div class="value">
						<div class="select">
							<span class="shape"></span>
							<?php echo _InputSelect('pass_stock' , array('x',30,50,100) , $pass_stock, '' , array('품절', '1개 ~ 30개', '31개 ~ 50개', '51개 이상'), '-재고량-');  ?>
						</div>
					</div>
				</li>
				<li class="ess">
					<span class="opt">카테고리</span>
					<div class="value">
						<?php
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
							$MobileAdmin = true;
							include_once(OD_PROGRAM_ROOT."/category.inc.php");
						?>
					</div>
				</li>
			</ul>

			<!-- ●●●●● 가운데정렬버튼 -->
			<div class="cm_bottom_button">
				<ul>
					<li><span class="button_pack"><input type="submit" class="btn_md_blue" value="검색하기"></span></li>
					<?if($mode == "search") :?><li><span class="button_pack"><a href="_product.list.php" class="btn_md_black">전체목록</a></span></li><?endif;?>
				</ul>
			</div>
			<!-- / 가운데정렬버튼 -->
		</div>
	</div>
	<!-- / 상단에 들어가는 검색등 공간 -->
</form>







<form name="frm" method="post" action="_product.pro.php">
<input type="hidden" name="_mode" value="">
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">

<?
	if(sizeof($res) == 0 ) :
		echo "<div class='cm_no_conts'><div class='no_icon'></div><div class='gtxt'>등록된 내용이 없습니다.</div></div>";
	else :
?>
	<!-- 리스트 제어영역 -->
	<div class="top_ctrl_area">
		<label class="allcheck" title="모두선택"><input type="checkbox" name="allchk" /></label>
		<span class="ctrl_button">
			<span class="button_pack"><a href="#none" onclick="selectSortModify();" class="btn_sm_white">선택순위수정</a></span>
		</span>
	</div>
	<!-- / 리스트 제어영역 -->
<? endif;?>


	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container">

		<!-- ●●●●● 데이터리스트 -->
		<div class="data_list">
<?php

	$res = _MQ_assoc(" select p.* , c.*  $s_query $s_orderby limit $count , $listmaxcount ");
	foreach($res as $k=>$v) {

		$_link_out = "<span class='button_pack'><a href='/?pn=product.view&pcode=" . $v['p_code'] . "' target='_blank' class='btn_sm_white'>미리보기</a></span>";
		$_mod = "<span class='button_pack'><a href='_product.form.php?_mode=modify&_code=" . $v['p_code'] . "&_PVSC=" . $_PVSC . "' class='btn_sm_blue'>수정</a></span>";
		$_del = "<span class='button_pack'><a href='#none' onclick='del(\"_product.pro.php?_mode=delete&_code=" . $v['p_code'] . "&_PVSC=" . $_PVSC . "\");' class='btn_sm_black'>삭제</a></span>";

		$_num = $TotalCount - $count - $k ;

		$_p_img = get_img_src($v[p_img_list]);

		echo "
			<dl>
				<dd>
					<div class='first_box'>
						<label class='check'><input type='checkbox' name='chk_pcode[".$v['p_code']."]' value='Y' class=class_pcode /></label>
						<span class='number'>no.". $_num ."</span>
						<span class='view_rank'>
							<span class='txt'>". ($v['p_view'] == "Y" ? "노출" : "<FONT COLOR='red'>숨김</FONT>") ."</span>
							<span class='input_box'><input type='tel' name='sort_group[".$v['p_code']."]' value='".$v['p_sort_group']."' class='input_design'  /></span>
						</span>
					</div>
					<!-- 상품정보 -->
					<div class='item_info'>
						<span class='thumb'>".( $v[p_img_list] ? "<img src='".$_p_img."' >" : "&nbsp;" )."</span>
						<div class='p_name'>[상품명] ". $v['p_name'] ."</div>
						<div class='p_code'>[상품코드] " . $v['p_code'] . "</div>
						<div class='p_price'>
							<span class='before'>정상가 : <span class='value'>". number_format($v['p_screenPrice']) ."원</span></span>
							<span class='after'>판매가 : <span class='value'>". number_format($v['p_price']) ."원</span></span>
						</div>
					</div>
				</dd>
				<dt>
					<div class='btn_box'>
						<ul>
							<li>" . $_mod . "</li>
							<li>" . $_del . "</li>
							<li>" . $_link_out . "</li>
						</ul>
					</div>
				</dt>
			</dl>
		";
	}
?>
		</div>
	</div>
	<!-- / 내용들어가는 공간 -->
</form>



	<?=pagelisting_mobile_totaladmin($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>


<script language="JavaScript" src="./js/_product.js"></script>
<SCRIPT>
	// 선택순위수정
	function selectSortModify() {
		if($('.class_pcode').is(":checked")){
			$("form[name=frm]").attr("action" , "_product.pro.php");
			$("input[name=_mode]").val('mass_sort');
			document.frm.submit();
		}
		else {alert('1개 이상 선택하시기 바랍니다.');}
	}
	// - 전체선택 / 해제
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){$('.class_pcode').attr('checked',true);}
			else {$('.class_pcode').attr('checked',false);}
		});
	});
</SCRIPT>



<?php

	include dirname(__FILE__)."/wrap.footer.php";

?>
