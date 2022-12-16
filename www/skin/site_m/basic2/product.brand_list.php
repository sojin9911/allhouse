<div class="c_page_tit if_nomenu"><!-- 열리면 if_open / 메뉴없으면 if_nomenu -->
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
		<div class="tit">브랜드 상품</div>
	</div>
</div>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_brand">


	<div class="brand_wrap "><!-- 아래 펼쳐보기 버튼 누르면 if_unfold -->

		<!-- 단어별 브랜드 분류 -->
		<div class="word">
			<div class="word_lang_wrap">
				<ul class="lang_tab clearfix">
					<li class="tab_kor on">ㄱㄴㄷ</li>
					<li class="tab_en">ABC</li>
				</ul>
				<div class="word_btn_area">
					<ul class="word_wrap">
						<!-- 활성화 시 btn에 hit 클래스 추가 / 처음에는 전체에 hit -->
						<!-- 한글 -->
						<li class="kor">
							<a href="#none" class="btn all <?=$brand_prefix ? '' : 'hit'?>" data-key="all">전체 브랜드 보기</a>
							<?php
								// 한글 브랜드 부분
								foreach( $arr_prefix_kor as $k=>$v ){
									echo (sizeof($arr_brand_prefix[$v]) > 0 ? '<a href="#none" class="btn '. ($brand_prefix == $v ? 'hit' : '') .'" data-key="'. $v .'">'. $v .'</a>' : '<span class="btn none">'. $v .'</span>') ;
								}
								
								?>
						</li>
						<!-- 영문 -->
						<li class="en">
							<?php
								// 영문 브랜드 부분
								foreach( $arr_prefix_eng as $k=>$v ){
									echo (sizeof($arr_brand_prefix[$v]) > 0 ? '<a href="#none" class="btn '. ($brand_prefix == $v ? 'hit' : '') .'" data-key="'. $v .'">'. $v .'</a>' : '<span class="btn none">'. $v .'</span>') ;
								}
								
								
								// 기타 브랜드 부분
								echo (sizeof($arr_brand_prefix['기타']) > 0 ? '<a href="#none" class="btn all '. ($brand_prefix == '기타' ? 'hit' : '') .'" data-key="etc">기타</a>' : '<span class="btn none">ETC</span>') ;
								?>
						</li>
					</ul>
				</div>	
			</div>
		</div>



		<!-- 해당 단어 브랜드 목록 / 여기에서 스크롤하면 전체 스크롤 안되도록 -->
		<div class="menu_box">
			<div class="menu" id="brand_menu_box">
				<?php include OD_PROGRAM_ROOT."/product.brand_ajax.menu_box.php"; ?>
			</div>
		</div>

		<div class="ctrl"><a href="#none" class="btn" title="펼쳐보기" style="display:none;"></a></div>

	</div>

<script type="text/javascript">

	// 펼쳐보기 클릭
	$(document).on('click', '.brand_wrap .ctrl .btn', function(e) {
		if( $(".brand_wrap").hasClass("if_unfold") ) {
			$(".brand_wrap").removeClass("if_unfold");
		}
		else {
			$(".brand_wrap").addClass("if_unfold");
		}
	});

	// 컨텐츠 크기에 따라 더보기 버튼 감추기
	function BrandMenuBoxMore() {
		var MaxHeight = $('.menu_box').height();
		var ContentHeight = $('#brand_menu_box').height();
		if(MaxHeight < ContentHeight) {
			$('.brand_wrap .ctrl .btn').show();
		}
		else {
			$('.brand_wrap .ctrl .btn').hide();
			$(".brand_wrap").removeClass("if_unfold");
		}
	}
	$(function() {
		BrandMenuBoxMore();
	});

	// 브랜드 클릭
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
				BrandMenuBoxMore();
			}
		});
	});


	$(".tab_kor").click(function () {
      $(".en").removeClass("on");
      $(".tab_en").removeClass("on");
			
      $(".tab_kor").addClass("on");
      $(".kor").addClass("on");
	});

 
  $(".tab_en").click(function () {
      $(".kor").removeClass("on");
			$(".tab_kor").removeClass("on");
			
			$(".en").addClass("on");
      $(".tab_en").addClass("on");
    });

		if ($(".tab_kor").hasClass("on")) {
		$(".kor").addClass("on");
	}
</script>




	<div class="brand_name"><strong><?=$app_brand_name?></strong></div>


	<!-- ◆ 공통영역의 상품리스트 (각 스킨 상품리스트가 들어옴) -->
	<div class="c_item_list">

		<?php
		/*
			리스트형 보기는 if_col1을 사용하면되며
			리스트 형태가 1단이라면 기본 리스트 형태는 리스트형으로 하고 썸네일형 버튼을 클릭 하면 4단이 나오면 된다.
		*/
		// 브랜드가 선택된 경우만 노출
		if( $uid ) {
		?>

		<!-- ******************************************
			상품리스트
		  -- ****************************************** -->
		<div class="sub_item">
			<!-- 리스트 제어 -->
			<div class="item_list_ctrl">
				<ul>
					<div class="select">
						<select onchange="location.href=this.value;">
							<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'date', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'date'?' selected':null); ?>>등록순</option>
							<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'price_asc', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'price_asc'?' selected':null); ?>>낮은 가격순</option>
							<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'price_desc', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'price_desc'?' selected':null); ?>>높은 가격순</option>
							<option class="hide" value="<?php echo ProductOrderLinkBuild(array('_order'=>'', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo (!$_order || $_order == ''?' selected':null); ?>>기본 상품순</option>
							<option class="hide" value="<?php echo ProductOrderLinkBuild(array('_order'=>'sale', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'sale'?' selected':null); ?>>인기 상품순</option>
							<option class="hide" value="<?php echo ProductOrderLinkBuild(array('_order'=>'pname', 'listpg'=>1, 'uid'=>$uid)); ?>"<?php echo ($_order == 'pname'?' selected':null); ?>>상품 이름순</option>
							</select>
						</div>
					</li>
				</ul>
			</div>
			<!-- / 리스트 제어 -->


		<?php
		$res = $p_res;
		// 상품리스트 호출
		include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');
		?>


		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi">
			<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
		<!-- / 페이지네이트 (상품목록 형) -->

	</div>
	<!-- /상품리스트 -->


	<?php } else { ?>
		<!-- 내용없을경우 -->
		<!-- <div class="c_none"><div class="gtxt">브랜드를 선택해주세요.</div></div> -->
	<?php } ?>


	</div>

</div>