<?PHP
	$app_mode = 'popup';
	include_once("inc.header.php");


	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_GET , $_POST)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);


?>



<?// 팝업을 위한 css 추가 --- window.open시 1120px로 띄움 ?>
<style>
	body {min-width:1100px;}
	.wrap {padding-bottom:0px;}
</style>




<!-- ● 일반 새창 팝업창 1100px -->
<div class="popup" style="border:0">

	<div class="pop_title"><strong>프로모션 - 상품설정</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<?php


		// 상품 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_product as p where 1 ";

		// 검색조건 추가 ==> 선택상품 제외
		if( $uid  > 0) {

			// 상품정보 추출
			$arr_pcode = array();
			$ppps_que = " select ppps_pcode from smart_promotion_plan_product_setup  where  ppps_ppuid = '". $uid ."'";
			$ppps_res = _MQ_assoc($ppps_que);
			foreach($ppps_res as $pppsk=>$pppsv){
				$arr_pcode[$pppsv['ppps_pcode']]++;
			}

			$s_query .= " and p_code NOT IN ('" . implode("' , '" , array_keys($arr_pcode)) . "') ";

		}

		// 추가파라메터
		$arr_param = array('uid'=>$uid);
		include_once("_product.inc_search.php");
		//	==> s_query 리턴됨.


		if(!$listmaxcount) $listmaxcount = 50;
		if(!$listpg) $listpg = 1;
		if(!$st) $st = 'p_idx';
		if(!$so) $so = 'asc';
		$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


		$res = _MQ(" select count(*) as cnt  $s_query ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$res = _MQ_assoc(" select p.* $s_query order by {$st} {$so} limit $count , $listmaxcount ");


	?>



<form name="relationForm2" method="post" action="_promotion_plan.product_pro.php" target="common_frame">
<input type="hidden" name="uid" value="<?=$uid?>">


	<!-- ● 데이터 리스트 -->
	<div class="data_list">


		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<span class="c_btn h27 red"><input type="submit" name="" value="선택적용" /></span>
			</div>
			<div class="right_box">
				<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'asc'?' selected':null); ?>>상품명 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'desc'?' selected':null); ?>>상품명 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'asc'?' selected':null); ?>>판매가 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'desc'?' selected':null); ?>>판매가 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'asc'?' selected':null); ?>>재고량 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'desc'?' selected':null); ?>>재고량 ▼</option>
					</select>

					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->

		<table class="table_list">
			<colgroup>
				<col width="50"><col width="60"><col width="60"><col width="*"><col width="140"><col width="100"><col width="90"><col width="70">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">NO</th>
					<th scope="col">이미지</th>
					<th scope="col">상품명</th>
					<th scope="col">상품코드</th>
					<th scope="col">판매가</th>
					<th scope="col">재고량</th>
					<th scope="col">노출여부</th>
				</tr>
			</thead>
			<tbody>
				<?php


				if(sizeof($res) > 0){
					foreach($res as $k=>$v){

						$_num = $TotalCount-$count-$k; // NO 표시
						$_title = strip_tags($v['p_name']);

						// 이미지 검사
						$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
						if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

				?>
						<tr>
							<td>
								<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $v['p_code']; ?>]" class="js_ck class_pcode" value="Y" <?=( IN_ARRAY($v['p_code'] , array_keys($arr_pcode)) ? 'checked' : '')?>></label>
							</td>
							<td><?php echo $_num; ?></td>
							<td class="img50"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['p_name'])); ?>"></td>
							<td class="t_left"><?php echo $_title; ?></td>
							<td><?php echo $v['p_code']; ?></td>
							<td class="t_black"><?php echo number_format($v['p_price']); ?>원</td>
							<td><?php echo number_format($v['p_stock']); ?></td>
							<td><div class="lineup-center"><?php echo $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')]; ?></div></td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>


		<?php if(sizeof($res) < 1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</div>
	<!-- / 데이터 리스트 -->

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?pass_menu={$pass_menu}&${_PVS}&listpg=" , "Y")?>
	</div>


	<!-- 가운데정렬버튼 -->
	<div class="c_btnbox">
		<ul>
			<li><span class="c_btn h34 black"><input type="submit" name="" value="선택적용" /></span></li>
			<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
		</ul>
	</div>
	<div class="fixed_save js_fixed_save" style="display:none;">
		<div class="wrapping" style="margin:0;">
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox" style="margin:0 !important;">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="선택적용" /></span></li>
					<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
				</ul>
			</div>
		</div>
	</div>


</form>



</div>

<?PHP
	include_once("inc.footer.php");
?>

<SCRIPT LANGUAGE="JavaScript">

    $(document).ready(function() {
        $("input[name='allchk']").click(function (){
            if($(this).is(':checked')){
                $('.class_pcode').attr('checked',true);
            }
            else {
                $('.class_pcode').attr('checked',false);
            }
        });
    });

</script>