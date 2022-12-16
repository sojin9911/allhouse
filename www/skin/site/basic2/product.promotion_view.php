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

// <!-- [PC] 기획전별 배너 (1050 x free) / 없으면 div 숨김 -->
$app_content = ($row['pp_content'] ? '<div class="banner">' . stripslashes($row['pp_content']) . '</div>' : '');


//<!-- ◆ 상품리스트 : 기본 4단 / 1단 if_col1 / 2단 if_col2 / 5단 if_col5 -->
// 상품목록 노출형태
//		기본 섬네일 형태
//		형태 : thumb , list
$list_type =  $list_type ? $list_type : 'thumb';
switch($list_type){
	case 'thumb': $ActiveListCol = 4; $ActiveListColClass = ''; break;
	case 'list': $ActiveListCol = 1; $ActiveListColClass = ' if_col1'; break;
}
?>
<!-- ◆기획전 탑 -->
<div class="c_section c_promotion if_view">
	<div class="layout_fix">

		<div class="pro_view_top">

			<!-- 기획전 타이틀 -->
			<div class="title"><strong><?php echo stripslashes(strip_tags($row['pp_title'])); ?></strong></div>

			<!-- 디데이 -->
			<div class="date_info">
			<?php
				//<!-- 이벤트 기간일 경우 if_day 클래스 추가 및 'D-DAY' 문구 변경 / 마감일 경우 if_close 클래스 추가 및 '마감' 문구 변경 -->
				echo $app_status;
			?>
			</div>

			<a href="/?pn=product.promotion_list&<?=enc('d',$_PVSC)?>" class="c_btn h30 light line">목록으로</a>

		</div>

		<?php
			//<!-- [PC] 기획전별 배너 (1050 x free) / 없으면 div 숨김 -->
			echo $app_content;
		?>

	</div>


	<!-- ◆ 공통영역의 상품리스트 (디자인에 맞춰서 들어감) -->
	<div class="c_item_list">
		<div class="layout_fix">



			<?php
			/*
				리스트형 보기는 if_col1을 사용하면되며
				리스트 형태가 1단이라면 기본 리스트 형태는 리스트형으로 하고 썸네일형 버튼을 클릭 하면 4단이 나오면 된다.
			*/
			if(count($res) > 0) {
			?>


			<!-- ◆리스트 제어 -->
			<div class="item_list_ctrl">
				<div class="total">전체 상품 <strong><?=number_format($TotalCount)?></strong>개</div>
				<div class="ctrl_right">
					<!-- 리스트 정렬 -->
					<div class="range">
						<ul>
							<!-- 활성화시 hit클래스 추가 -->
							<li<?php echo (!$_order || $_order == ''?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'', 'listpg'=>1, 'uid'=>$uid)); ?>" class="btn">기본순</a></li>
							<li<?php echo ($_order == 'sale'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'sale', 'listpg'=>1, 'uid'=>$uid)); ?>" class="btn">인기순</a></li>
							<li<?php echo ($_order == 'date'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'date', 'listpg'=>1, 'uid'=>$uid)); ?>" class="btn">등록일순</a></li>
							<li<?php echo ($_order == 'price_desc'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'price_desc', 'listpg'=>1, 'uid'=>$uid)); ?>" class="btn">높은 가격순</a></li>
							<li<?php echo ($_order == 'price_asc'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'price_asc', 'listpg'=>1, 'uid'=>$uid)); ?>" class="btn">낮은 가격순</a></li>
							<li<?php echo ($_order == 'pname'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'pname', 'listpg'=>1, 'uid'=>$uid)); ?>" class="btn">상품명순</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- / 리스트 제어 -->


			<?php
			// 상품리스트 호출
			include(OD_SITE_SKIN_ROOT.'/ajax.product.list.php');
			?>

				<!-- 내용없을경우 --><!-- <div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div> -->


			<?php
				}
			?>


			<div class="c_btnbox pro_btn">
				<ul>
					<li><a href="/?pn=product.promotion_list&<?=enc('d',$_PVSC)?>" class="c_btn h40 black line">기획전 목록보기</a></li>
				</ul>
			</div>

		</div>
	</div>
	<!-- / ◆ 공통영역의 상품리스트 -->
</div>
<!-- /◆기획전 탑 -->






