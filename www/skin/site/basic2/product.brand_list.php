<?php
if( $uid ) {
	if(is_mobile()) {
		//<!-- ◆ 상품리스트 : 기본 3단 / 2단 if_col2 -->
		$ActiveListColClass = '';
	}
	else {
		//<!-- ◆ 상품리스트 : 기본 4단 / 1단 if_col1 / 2단 if_col2 / 5단 if_col5 -->
		// 상품목록 노출형태
		//		기본 섬네일 형태
		//		형태 : thumb , list
		$list_type =  $list_type ? $list_type : 'thumb';
		switch($list_type){
			case 'thumb': $ActiveListCol = 4; $ActiveListColClass = ''; break;
			case 'list': $ActiveListCol = 1; $ActiveListColClass = ' if_col1'; break;
		}
	}
}
?>
<!-- ◆브랜드 -->
<div class="c_section c_brand">
	<div class="layout_fix">

		<div class="title"><strong>브랜드 상품</strong></div>

		<!-- 단어별 브랜드 분류 -->
		<div class="word">
			<div class="word_tit">브랜드 상품보기</div>
			<ul>
				<!-- 활성화 시 btn에 hit 클래스 추가 / 처음에는 전체에 hit -->
				<!-- 한글 -->
				<li>
					<a href="#none" class="btn all <?=$brand_prefix ? '' : 'hit'?>" data-key="all">전체</a>
					<?php
						// 한글 브랜드 부분
						foreach( $arr_prefix_kor as $k=>$v ){
							echo (sizeof($arr_brand_prefix[$v]) > 0 ? '<a href="#none" class="btn '. ($brand_prefix == $v ? 'hit' : '') .'" data-key="'. $v .'">'. $v .'</a>' : '<span class="btn none">'. $v .'</span>') ;
						}

						// 기타 브랜드 부분
						echo (sizeof($arr_brand_prefix['기타']) > 0 ? '<a href="#none" class="btn etc '. ($brand_prefix == '기타' ? 'hit' : '') .'" data-key="etc">기타</a>' : '<span class="btn none">기타</span>') ;

					?>
				</li>
				<!-- 영문 -->
				<li class="en">
					<?php
						// 영문 브랜드 부분
						foreach( $arr_prefix_eng as $k=>$v ){
							echo (sizeof($arr_brand_prefix[$v]) > 0 ? '<a href="#none" class="btn '. ($brand_prefix == $v ? 'hit' : '') .'" data-key="'. $v .'">'. $v .'</a>' : '<span class="btn none">'. $v .'</span>') ;
						}
					?>
				</li>
			</ul>
		</div>


		<!-- 해당 단어 브랜드 목록 / 여기에서 스크롤하면 전체 스크롤 안되도록 -->
		<div class="menu_box">
			<div class="menu" id="brand_menu_box">
				<?php include OD_PROGRAM_ROOT."/product.brand_ajax.menu_box.php"; ?>
			</div>
		</div>

		<div class="brand_name"><strong><?=$app_brand_name?></strong></div>


		<!-- ◆ 공통영역의 상품리스트 (각 스킨 상품리스트가 들어옴) -->
		<div class="c_item_list">
			<div class="layout_fix">


				<?php
				/*
					리스트형 보기는 if_col1을 사용하면되며
					리스트 형태가 1단이라면 기본 리스트 형태는 리스트형으로 하고 썸네일형 버튼을 클릭 하면 4단이 나오면 된다.
				*/
				// 브랜드가 선택된 경우만 노출
				if( $uid ) {
				?>

				<!-- ◆리스트 제어 -->
				<div class="item_list_ctrl">
					<div class="total">전체 상품<strong><?=number_format($TotalCount)?></strong>개</div>
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
				$res = $p_res;
				// 상품리스트 호출
				include(OD_SITE_SKIN_ROOT.'/ajax.product.list.php');
				?>

				<!-- 페이지네이트 (상품목록 형) -->
				<div class="c_pagi">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
				</div>
				<!-- / 페이지네이트 (상품목록 형) -->


				<?php } else { ?>
					<!-- 내용없을경우 -->
					<!-- <div class="c_none"><div class="gtxt">브랜드를 선택해주세요.</div></div> -->
				<?php } ?>

			</div>
		</div>
		<!-- /공통영역의 상품리스트 -->



	</div>
</div>
<!-- /브랜드 -->

<script type="text/javascript">
	$(document).on('click', '.word ul li a.btn', function(e) {

		e.preventDefault();

		var brand = $(this).data("key");

		$(".word ul li a.btn").removeClass("hit");// 전체 브랜드 hit 풀기
		$(this).addClass("hit");// 선택 브랜드 hit 적용

		// 클릭 브랜드 - box 불러오기
		$.ajax({
			data: {brand: brand},
			type: 'POST',
			cache: false,
			url : "<?php echo OD_PROGRAM_URL; ?>/product.brand_ajax.menu_box.php" ,
			success: function(data) {
				$("#brand_menu_box").html(data);
			}
		});
	});
</script>





