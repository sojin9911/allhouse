<?php
// 기획전 상태
//<!-- 이벤트 기간일 경우 if_day 클래스 추가 및 'D-DAY' 문구 변경 / 마감일 경우 if_close 클래스 추가 및 '마감' 문구 변경 -->
//			시작전 -> D-123
//			진행중 -> 진행 (진행시 d_day 클래스에 if_day 클래스 추가)
//			종료후 -> 마감 (종료된 기획전일 경우 d_day 클래스에 if_close 클래스 추가 ,  li에 if_end_promo 클래스 추가)
$app_status = '';
//종료후
if($row['pp_edate']<DATE('Y-m-d')) {
	$app_status = '<span class="d_day if_close">마감</span>';// 종료문구
}
//시작전
else if($row['pp_sdate']>DATE('Y-m-d')) {
	$app_status = '<span class="d_day">D-'. fn_date_diff($row['pp_sdate'],DATE("Y-m-d")) .'</span>';
}
//진행중
else {
	$app_status = '<span class="d_day if_day">진행</span>';
}

//<!-- [모바일] 기획전별 배너 (1000 x free) / 없으면 div 숨김 / PC있으면 PC불러옴  -->
$app_content = ($row['pp_content_m'] ? '<div class="banner">' . stripslashes($row['pp_content_m']) . '</div>' : ($row['pp_content'] ? '<div class="banner">' . stripslashes($row['pp_content']) . '</div>' : ''));

//<!-- ◆ 상품리스트 : 기본 3단 / 2단 if_col2 -->
$ActiveListColClass = '';
?>
<div class="c_page_tit if_nomenu"><!-- 열리면 if_open / 메뉴없으면 if_nomenu -->
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
		<div class="tit">쇼핑몰 기획전</div>
	</div>
</div>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_promotion">


	<div class="pro_view_top">
		<!-- 기획전 타이틀 -->
		<div class="title"><strong><?php echo stripslashes(strip_tags($row['pp_title'])); ?></strong></div>
	</div>


	<?php
		//<!-- [모바일] 기획전별 배너 (1000 x free) / 없으면 div 숨김 / PC있으면 PC불러옴  -->
		echo $app_content;
	?>








	<!-- ◆ 공통영역의 상품리스트 (각 스킨 상품리스트가 들어옴) -->
	<div class="c_item_list">


		<?php
		/*
			리스트형 보기는 if_col1을 사용하면되며
			리스트 형태가 1단이라면 기본 리스트 형태는 리스트형으로 하고 썸네일형 버튼을 클릭 하면 4단이 나오면 된다.
		*/
		if(count($res) > 0) {
		?>

		<!-- ******************************************
			상품리스트
		  -- ****************************************** -->
		<div class="sub_item">
			<!-- 리스트 제어 -->
			<div class="item_list_ctrl">
				<ul>
					<li class="this_total"><div class="total"><strong><?=number_format($TotalCount)?></strong> Products</div></li>
					<li>
						<div class="select">
							<select onchange="location.href=this.value;">
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo (!$_order || $_order == ''?' selected':null); ?>>기본 상품순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'sale', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'sale'?' selected':null); ?>>인기 상품순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'date', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'date'?' selected':null); ?>>최근 등록순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'price_desc', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'price_desc'?' selected':null); ?>>높은 가격순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'price_asc', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'price_asc'?' selected':null); ?>>낮은 가격순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'pname', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'pname'?' selected':null); ?>>상품 이름순</option>
							</select>
						</div>
					</li>
				</ul>
			</div>
			<!-- / 리스트 제어 -->




			<?php
			// 상품리스트 호출
			include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');
			?>
		</div>
		<!-- /상품리스트 -->

	</div>
	<!-- /공통영역의 상품리스트 -->

	<?php
		}
	?>



	<div class="c_btnbox">
		<ul>
			<li><a href="/?pn=product.promotion_list&<?=enc('d',$_PVSC)?>" class="c_btn h40 light">기획전 목록보기</a></li>
		</ul>
	</div>



</div>
<!-- /공통페이지 섹션 -->