<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// 기본파일 include
include_once(OD_PROGRAM_ROOT.'/inc.header.php'); // 스킨 내부파일로 직접 include 하지 마세요.
include_once($SkinData['skin_root'].'/inc.slide_menu.php'); // 슬라이드 메뉴
?>
<!-- ******************************************
     HEADER (공통)
  -- ****************************************** -->
<div class="header js_top_header"><!-- 검색버튼 누르면 : if_search_open 추가 -->
	<ul class="inner">
		<li class="this_left">
			<span class="lineup">
				<a href="#none" class="btn btn_slide js_slide_open" title="슬라이드열기"></a>
				<a href="#none" class="btn btn_search js_header_search_toggle" title="검색창 열기"></a><!-- 한번더 누르면 닫힘 -->
			</span>
		</li>
		<li class="this_logo">
			<?php
			$TopLogo = info_banner($_skin.',mobile_top_logo', 1, 'data'); // [MOBILE]공통 : 상단 로고 (가로 280 이하 x 세로 60 이하, 1개)
			if(count($TopLogo) > 0) {
			?>
				<a href="<?php echo ($TopLogo[0]['b_link']?$TopLogo[0]['b_link']:'/'); ?>"><img src="<?php echo IMG_DIR_BANNER_URL.$TopLogo[0]['b_img']; ?>" alt="<?php echo addslashes($TopLogo[0]['b_title']); ?>" /></a>
			<?php } else { ?>
				<!-- [모바일]공통 : 상단 로고 (가로 280 이하 * 세로 60 이하 권장 / 자동조정 ) -->
				<a href="/"><img src="<?php echo $SkinData['skin_url']; ?>/images/sample/header_logo.png" alt="" /></a>
			<?php } ?>
		</li>
		<li class="this_right">
			<span class="lineup">
				<a href="/?pn=mypage.main" class="btn btn_mypage" title="마이페이지 바로가기"></a>
				<a href="/?pn=shop.cart.list" class="btn btn_cart" title="장바구니 바로가기"><span class="cart_num js_cart_cnt"><?php echo ($cart_cnt>0?$cart_cnt:null); ?></span><!-- 0일때 숨김 --></a>
			</span>
		</li>
	</ul>
	<script type="text/javascript">
		$(document).on('click', '.js_header_search_toggle', function(e) {
			e.preventDefault();
			var open_status = $('.js_top_header').hasClass('if_search_open');
			if(open_status === true) $('.js_top_header').removeClass('if_search_open');
			else $('.js_top_header').addClass('if_search_open');
		});
	</script>

	<!-- 검색열리는 박스 (클릭으로 열리는데 확인을 위해 마우스 오버로 해두었어요, 작업 시 삭제해주세요~) -->
	<div class="search_box">
		<div class="form_box">
			<form role="search" action="/" method="get" onsubmit="return searchFunction(this);">
				<input type="hidden" name="pn" value="product.search.list">
				<input type="search" name="search_word" value="<?php echo (isset($search_word)?$search_word:null); ?>" class="input_search search_word" placeholder="Search products">
				<input type="submit" value="" class="btn_search" title="검색" />
			</form>
			<script type="text/javascript">
				function searchFunction(target) {
					if($(target).find('.search_word').val() == '' || $(target).find('.search_word').val() == 'Search products') {
						alert('검색할 단어를 입력하세요');
						$(target).find('.search_word').focus();
						return false;
					}
					return true;
				}
			</script>
		</div>
		<?php
		$GetHashTag = explode(',', $siteInfo['s_recommend_hashtag']); // 인기 해시태그
		if(count($GetHashTag) == 1 && $GetHashTag[0] == '')  $GetHashTag = array();
		if(count($GetHashTag) > 0) {
			$GetHashTag = array_unique($GetHashTag); // 중복값제거
			if(count($GetHashTag) > 1) shuffle($GetHashTag); // 셔플
		?>
			<!-- 모바일에서는 해시태그가 나옴 / 없으면 전체숨김 -->
			<div class="keyword_box">
				<?php foreach($GetHashTag as $k=>$v) { ?>
					<a href="/?pn=product.search.list&search_word=%23<?php echo urlencode($v); ?>" class="link">#<?php echo $v; ?></a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<div class="slide_wrap hide" style="position:inherit;">
		<div class="ctg_box">
			<?php foreach($AllCate as $k=>$v) { ?>
				<dl class="js_slide_cate<?php echo (count($v['sub']) <= 0?' if_no2':null); ?><?php echo (isset($ActiveCate['cuid'][0]) && $ActiveCate['cuid'][0] == $v['c_uid']?' if_open':null); ?>"><!-- 클릭시 if_open 클래스 추가, 2차 카테고리 노출,  2차가 없으면 if_no2해서 해당1차로 바로연결 -->
					<dt>
						<!-- 1차 -->
						<a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="ctg1<?php echo (count($v['sub']) > 0?' js_slide_cate_more':null); ?>"><?php echo $v['c_name']; ?></a>
						<a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="btn_go">상품보기</a>
						<a href="#none" class="btn_ctrl js_slide_cate_more" title="열고닫기"></a>
					</dt>
					<?php foreach($v['sub'] as $kk=>$vv) { ?>
						<dd><a href="/?pn=product.list&cuid=<?php echo $vv['c_uid']; ?>" class="ctg2"><?php echo $vv['c_name']; ?></a></dd>
					<?php } ?>
				</dl>
			<?php } ?>
		</div>
	</div><!--nav 끝-->

	<div class="hd_cate_wrap">
		<ul class="">
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_01.png" alt="티셔츠"><span>티셔츠</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_02.png" alt="바지류"><span>바지류</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_03.png" alt="상하 SET"><span>상하 SET</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_04.png" alt="원피스 치마"><span>원피스 치마</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_05.png" alt="점퍼 자켓"><span>점퍼 자켓</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_06.png" alt="코트 망토"><span>코트 망토</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_07.png" alt="남방 Blouse"><span>남방 Blouse</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_08.png" alt="조끼"><span>조끼</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_09.png" alt="가디건"><span>가디건</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_10.png" alt="아동내의"><span>아동내의</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_11.png" alt="아동신발"><span>아동신발</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_12.png" alt="매장용품"><span>매장용품</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_13.png" alt="한복"><span>한복</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_14.png" alt="우비 우산"><span>우비 우산</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_15.png" alt="유아복"><span>유아복</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_16.png" alt="잡화"><span>잡화</span></a></li>
			<li><a href=""><img src="//ssip.co.kr/skin/site_m/basic2/images/skin/cate_img/ico_cate_17.png" alt="엄마아빠용"><span>엄마아빠용</span></a></li>
		</ul>
</div>


</div>
<!-- / HEADER -->