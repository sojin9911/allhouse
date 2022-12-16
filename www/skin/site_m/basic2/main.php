<?php
# [MOBILE]메인 : 메인비주얼배너 (1,000 x 548, 슬라이드형 무제한) - 스와이퍼 슬라이드
$MainVisual = info_banner($_skin.',mobile_main_visual', 99999, 'data');
if(count($MainVisual) > 0) {
?>
	<!-- ******************************************
	     메인비주얼
	  -- ****************************************** -->
	<div class="main_visual hide">
		<!-- [모바일]메인 : 메인비주얼배너 (1000 x 548) 무제한 -->
		<div class="rolling_box js_main_visual">
			<?php foreach($MainVisual as $k=>$v) { ?>
				<div class="visual_box"<?php echo ($k > 0?' style="display:none;"':null); ?>>
					<?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?><a href="<?php echo $v['b_link']; ?>" target="<?php echo $v['b_target']; ?>" class="" title="<?php echo addslashes($v['b_title']); ?>"><?php } ?>
						<img src="<?php echo IMG_DIR_BANNER_URL.$v['b_img']; ?>" alt="" />
					<?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?></a><?php } ?>
				</div>
			<?php } ?>
		</div>

		<?php if(count($MainVisual) > 1) { ?>
			<!-- 롤링아이콘 (롤링이 1개일때는 숨김) (해당 롤링일때 active 추가) -->
			<div class="rolling_icon">
				<span class="lineup js_main_visual_pager">
					<?php foreach($MainVisual as $k=>$v) { ?>
						<a href="#none" class="icon<?php echo ($k === 0?' active':null); ?>" data-slide-index="<?php echo $k; ?>"></a>
					<?php } ?>
				</span>
			</div>
			<script type="text/javascript">
				$(document).ready(function(){
					setTimeout(function(){ // parallax 문자들이 겹치는 현상을 방지하기위해 setTimeout으로 실행시간 delay


						$('.js_main_visual').find('.visual_box').show();
						var main_visual = $('.js_main_visual').bxSlider({
							auto: true,
							autoHover: false,
							pagerCustom: '.js_main_visual_pager',
							controls: false,
							maxSlides:1,
							moveSlides:1,
							slideMargin : 0,
							onSliderLoad: function() { },
							onSlideBefore: function() { main_visual.stopAuto(); },
							onSlideAfter: function() { main_visual.startAuto(); }
						});
					}, 500);
				});
			</script>
		<?php } ?>
	</div>
	<!-- /메인비주얼 -->
<?php } ?>


<!-- 공지사항 -->
<div class="hd_btm_menu right_time_search">
	<div class="rolling_box">
		<p class="main_notice_tit">NOTICE</p>
		<ul id ="rolling_box">
			<li class="card_sliding" id ="first"><a href=""></a></li>
			<li class="" id ="second"><a href=""></a></li>
			<li class="" id ="third"><a href=""></a></li>
		</ul>
	</div>
</div>

<script>

	let rollingData = [
	'1138차-04월08일[대량입고-검수지연공지]검수지연공지',
	'1138차-04월08일[대량입고-검수지연공지]',
	'1138차-04월08일[대량입고-검수지연공지]검수지연공지',
	'1138차-04월08일[대량입고-검수지연공지]',
	'1138차-04월08일[대량입고-검수지연공지]',
	'1138차-04월08일[대량입고-검수지연공지]검수지연공지',
	'1138차-04월08일[대량입고-검수지연공지]',
	'1138차-04월08일[대량입고-검수지연공지]',
	'1138차-04월08일[대량입고-검수지연공지]검수지연공지',
	'1138차-04월08일[대량입고-검수지연공지]'
	]

	let timer = 2000 // 롤링되는 주기 입니다 (1000 => 1초)

	let first = document.getElementById('first'),
		second = document.getElementById('second'),
		third = document.getElementById('third')
	let move = 2
	let dataCnt = 1
	let listCnt = 1

	//위 선언은 따로 완전히 수정하지 않는 한 조정할 필요는 없습니다.

	first.children[0].innerHTML = rollingData[0]

	setInterval(() => {
		if(move == 2){
				first.classList.remove('card_sliding')
				first.classList.add('card_sliding_after')

				second.classList.remove('card_sliding_after')
				second.classList.add('card_sliding')

				third.classList.remove('card_sliding_after')
				third.classList.remove('card_sliding')

				move = 0
		} else if (move == 1){
				first.classList.remove('card_sliding_after')
				first.classList.add('card_sliding')

				second.classList.remove('card_sliding_after')
				second.classList.remove('card_sliding')

				third.classList.remove('card_sliding')
				third.classList.add('card_sliding_after')

				move = 2
		} else if (move == 0) {
				first.classList.remove('card_sliding_after')
				first.classList.remove('card_sliding')

				second.classList.remove('card_sliding')
				second.classList.add('card_sliding_after')

				third.classList.remove('card_sliding_after')
				third.classList.add('card_sliding')

				move = 1
		}
		
		if(dataCnt < (rollingData.length - 1)) {
				document.getElementById('rolling_box').children[listCnt].children[0].innerHTML = rollingData[dataCnt]
						dataCnt++
		} else if(dataCnt == rollingData.length - 1) {
				document.getElementById('rolling_box').children[listCnt].children[0].innerHTML = rollingData[dataCnt]
				dataCnt = 0
		}

		if(listCnt < 2) {
				listCnt++
		} else if (listCnt == 2) {
				listCnt = 0
		}

		console.log(listCnt)
	}, timer);
</script>
<!-- 공지사항 -->



<?php
# [MOBILE]메인 : 와이드 배너 (900 x free, 노출형 무제한)
$MainWide = info_banner($_skin.',mobile_main_wide', 99999, 'data');
if(count($MainWide) > 0) {
?>
	<!-- ******************************************
	     메인 중간배너 (없을 시 전체 숨김)
	  -- ****************************************** -->
	<div class="main_ad">

		<!-- [모바일]메인 : 와이드 배너 (900 x free) 무제한 -->
		<div class="triple">
			<ul>
				<?php foreach($MainWide as $k=>$v) { ?>
					<li>
						<div class="banner">
							<?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?><a href="<?php echo $v['b_link']; ?>" target="<?php echo $v['b_target']; ?>" title="<?php echo addslashes($v['b_title']); ?>"><?php } ?>
								<img src="<?php echo IMG_DIR_BANNER_URL.$v['b_img']; ?>" alt="<?php echo addslashes($v['b_title']); ?>" />
							<?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?></a><?php } ?>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<!-- /메인 중간배너  -->
<?php } ?>





<div class="today_reg_brand">
	<h3 class="main_display_title">오늘 등록된 브랜드 <span>25</span></h3>
	<ul class="main_trb_wrap">
    <li class="main_trb_tab current" data-tab="main_trb_cont1">비다이어리</li>
    <li class="main_trb_tab" data-tab="main_trb_cont2">데일리데일리</li>
    <li class="main_trb_tab" data-tab="main_trb_cont3">키삭스</li>
    <li class="main_trb_tab" data-tab="main_trb_cont3">골드피쉬</li>
    <li class="main_trb_tab" data-tab="main_trb_cont3">깰그쇼즈</li>
  </ul>

  <div id="main_trb_cont1" class="main_tab_cont current">content1</div>
  <div id="main_trb_cont2" class="main_tab_cont">content2</div>
  <div id="main_trb_cont3" class="main_tab_cont">content3</div>

</div>
<script>
  $('.main_trb_wrap li').click(function(){
    var tab_id = $(this).attr('data-tab');

    $('.main_trb_wrap li').removeClass('current');
    $('.main_tab_cont').removeClass('current');

    $(this).addClass('current');
    $("#"+tab_id).addClass('current');
  })
</script>

<div class="main_display_prod_wrap">
	<div class="main_display_prod">
		<h3 class="main_display_title">T-SHIRTS</h3>
	</div>
	<div class="main_display_prod">
		<h3 class="main_display_title">OUTER</h3>
	</div>
	<div class="main_display_prod">
		<h3 class="main_display_title">SET ITEM</h3>
	</div>
</div>




<?php
// -- 메인상품을 가져온다. smart_display_main_set
$resMain = _MQ_assoc("
	select
		dms1.*,
		(select count(*) from smart_product as p inner join smart_display_main_product as dmp on(p.p_code = dmp.dmp_pcode) where dmp_dmsuid = `dms1`.`dms_uid` ) as pcnt
	from
		`smart_display_main_set` as dms1 inner join
		`smart_display_main_set` as dms2 on(dms2.dms_type = dms1.dms_type and dms2.dms_depth = 1 and dms2.dms_view = 'Y')
	where
		dms1.dms_type = 'main' and
		dms1.dms_depth = '2' and
		dms1.dms_view = 'Y'
	having pcnt > 0
	order by `dms_idx` asc
");
if( count($resMain) > 0) {
	foreach($resMain as $mk => $mv) {
?>
	<!-- ******************************************
	     메인 상품리스트
	  -- ****************************************** -->
	<div class="main_item">
		<div class="main_title"><?php echo $mv['dms_name'] ?></div>
		<?php
		$dmsuid = $mv['dms_uid'];
		$_event = 'main_product';
		$_list_type = 'ajax.main_product';
		include(OD_PROGRAM_ROOT.'/product.list.php');
		?>
	</div>
	<!-- /메인 상품리스트 -->
<?php }} ?>






<?php
$TopEventList = _MQ_assoc(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_list_product_mobile_view = 'Y' order by dts_idx asc ");
if(count($TopEventList) > 0) {
?>
	<!-- ******************************************
	     메인 바로가기
	  -- ****************************************** -->
	<div class="main_nav hide">
		<div class="inner">
			<ul>
				<?php
				$TopEventNum = 0;
				foreach($TopEventList as $k=>$v) {
					if($k > 0 && $k%2 <= 0) {
						echo '</ul><ul>';
						$TopEventNum = 0;
					}
					$TopEventNum++;
				?>
					<li><a href="/?pn=product.list&_event=type&typeuid=<?php echo $v['dts_uid']; ?>" class="btn"><?php echo $v['dts_name']; ?></a></li>
				<?php } ?>
				<?php for($i=$TopEventNum; $i<2; $i++) { ?>
					<li></li>
				<?php } ?>
			</ul>
		</div>
	</div>
<?php } ?>



<?php
// -- md 상품을 가져온다. smart_display_main_set
$resMd = _MQ_assoc("
	select
		dms2.*  ,
		dms1.dms_view as dms1_view , dms1.dms_name as dms1_name ,
		(select count(*) from smart_product as p INNER join smart_display_main_product as dmp on(p.p_code = dmp.dmp_pcode) where dmp_dmsuid = `dms2`.`dms_uid` ) as cnt
	from `smart_display_main_set` as dms2
	INNER JOIN `smart_display_main_set` as dms1 ON ( `dms1`.`dms_type` =  `dms2`.`dms_type` and `dms1`.`dms_depth` = '1' and `dms1`.`dms_view` = 'Y')
	where
		`dms2`.`dms_type` = 'md' and `dms2`.`dms_depth` = '2' and `dms2`.`dms_view` = 'Y'
	having cnt > 0
	order by `dms2`.`dms_idx` asc
");
if(count($resMd) > 0) {
?>
	<!-- ******************************************
	     메인 md's pick
		 ▶ 그룹당 스와이프는 if_col1을 설정하지 않음
	  -- ****************************************** -->
	<div class="main_md">
		<div class="main_title"><?php echo stripslashes($resMd[0]['dms1_name']) ?></div><!-- SSJ : (내부패치)스킨2 타이틀이 MD's Pick으로 고정되어있었음 : 2021-03-10 -->
		<?php foreach($resMd as $mk=>$mv) { ?>
			<!-- 한그룹당 스와이프 보기 / 끝에서 멈춰야함/공간더 남으면 안됨 -->
			<div class="group">
				<div class="g_tit"><?php echo stripslashes($mv['dms_name']);  ?></div>
				<?php
				$dmsuid = $mv['dms_uid'];
				$_event = 'main_md';
				$_list_type = 'ajax.main_md';
				include(OD_PROGRAM_ROOT.'/product.list.php');
				?>
			</div>
		<?php } ?>
	</div>
	<!-- /메인 md's pick -->
<?php } ?>



<?php
$Depth1Cate = _MQ_assoc("
	select
		c1.*,
		(
			select
				count(*)
			from
				smart_product as p left join
				smart_product_category_best as pcb on(p.p_code = pcb.pctb_pcode)
			where (1) and
			pcb.pctb_cuid = c1.c_uid
		) as pcnt
	from
		`smart_category` as c1
	where (1) and
		c_depth = 1 and
		c_view = 'Y' and
		c_best_product_mobile_view = 'Y'
	having pcnt > 0
	order by c_idx asc
");
if(count($Depth1Cate) > 0) {
?>
	<!-- ******************************************
	     메인 카테고리베스트
	  -- ****************************************** -->
	<div class="main_ctg hide">
		<div class="main_title">Category Best</div>
		<?php foreach($Depth1Cate as $dk=>$dv) { ?>
			<!-- 한그룹당 스와이프 보기 -->
			<div class="group">
				<div class="g_tit"><?php echo $dv['c_name']; ?></div>
				<?php
				$cuid = $dv['c_uid'];
				$_event = 'main_category_best';
				$_list_type = 'ajax.main_best_category';
				include(OD_PROGRAM_ROOT.'/product.list.php');
				?>
			</div>
		<?php } ?>
	</div>
	<!-- /메인 카테고리베스트 -->
<?php } ?>



<?php
if($siteInfo['instagram_main_use'] == 'Y' && trim($siteInfo['instagram_id']) != '' && trim($siteInfo['insta_token']) != '') {

	$InstaContent = array();
	$InstaUrl = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='.$siteInfo['insta_token'];
	$InstaJson = json_decode(CurlExec($InstaUrl, 10), true);
	$InstaContent = $InstaJson['data'];
	if($InstaContent > 0) $InstaContent = array_slice($InstaContent, 0, 12); // 최대 20개만 가능함.
	else $InstaContent = array();
	if(count($InstaContent) > 0) {
?>
	<!-- ******************************************
	     메인 인스타그램
	  -- ****************************************** -->
	<!-- (인스타 on/off 설정하면서 8개 노출) (없을 시 전체 숨김) / 없을때 개수 채우기 -->
	<div class="main_insta">
		<div class="insta_title">
			<span class="title">Instagram</span><!-- 인스타그램 아이디 : 관리자 설정 -->
			<div class="insta_id"><a href="https://www.instagram.com/<?php echo $siteInfo['instagram_id']; ?>" class="go" target="_blank">FOLLOW on @<?php echo $siteInfo['instagram_id']; ?></a></div>
		</div>
		<div class="insta_list">
			<ul>
				<?php
				$InstaNum = 0;
				foreach($InstaContent as $k=>$v) {
					$InstaLink = $v['link']; // 인스타그램 링크
					$InstaCaption = htmlspecialchars(str_replace(PHP_EOL, ' ', $v['caption']['text'])); // 캡션내용
					$InstaDate = $v['created_time']; // 등록일
					$InstaLike = $v['likes']['count']; // 좋아요 수
					$InstaComment = $v['comments']['count']; // 코멘트 수
					$InstaUserName = $v['user']['full_name']; // 사용자 이름
					$InstaUserUid = $v['user']['id']; // 사용자 고유 아이디
					$InstaUserId = $v['user']['username']; // 사용자 아이디
					$InstaUserPic = $v['user']['profile_picture']; // 사용자 프로필 이미지
					$InstaLocation = array('lat'=>$v['location']['latitude'], 'long'=>$v['location']['longitude'], 'name'=>$v['location']['name']); // 좌표정보
					$InstaTagArr = $v['tags']; // 태그 배열
					$insta_img = array(
						'thumb'=>$v['images']['thumbnail'], // 썸네일 (약 150 * 150)
						'low'=>$v['images']['low_resolution'], // 저해상도 (약 320 * 320)
						'standard'=>$v['images']['standard_resolution']  // 표준해상도 (약 640 * 640)
					);

					$imgType = ' if_rowimg';
					if($insta_img['standard']['width'] > $insta_img['standard']['height']) $imgType = ' if_colimg';
				?>
					<li>
						<div class="insta_box<?php echo $imgType; ?>">
							<a href="<?php echo $InstaLink; ?>" class="upper_link" title="<?php echo $InstaCaption; ?>" target="_blank"></a>
							<div class="real"><img src="<?php echo $insta_img['low']['url']; ?>" alt="<?php echo $InstaCaption; ?>" /></div>
							<div class="fake"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/insta_fake.gif" alt="<?php echo $InstaCaption; ?>" /></div>
							<div class="ov_box">
								<div class="text ellipsis"><?php echo strip_tags(rm_enter($InstaCaption)); ?></div>
								<span class="date"><?php echo date('Y-m-d H:i', $InstaDate); ?></span>
							</div>

						</div>
					</li>
				<?php } ?>
				<?php
				if(count($InstaContent) <= 8) {
					for($i=0; $i<8-count($InstaContent); $i++) {
				?>
					<li><div class="insta_box"><div class="fake"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/insta_fake.gif" alt="" /></div></div></li>
				<?php }} ?>
			</ul>
		</div>
	</div>
	<!-- /메인 인스타그램 -->
<?php }} ?>



<!-- ******************************************
     메인 고객센터
  -- ****************************************** -->
<div class="main_cs hide">
	<ul class="ul">

		<li class="li cs_box">
			<div class="title_box"><span class="tit">SERVICE CENTER</span></div>
			<div class="cs_info">
				<div class="tel"><a href="tel:<?php echo $siteInfo['s_glbtel']; ?>"><?php echo $siteInfo['s_glbtel']; ?></a></div>
				<div class="email"><a href="mailto:<?php echo $siteInfo['s_ademail']; ?>" title="이메일 보내기"><?php echo $siteInfo['s_ademail']; ?></a></div>
			</div>
			<div class="cs_time">
				<?php echo nl2br($siteInfo['s_cs_info']); ?>
			</div>
			<div class="btn_box">
				<ul>
					<?php // 내부패치 68번줄 kms 2019-11-05 ?>
					<li><a href="/?pn=mypage.inquiry.list" class="btn">1:1 온라인문의</a></li>
					<li><a href="/?pn=pages.view&type=agree&data=guide" class="btn">이용안내</a></li>
					<li><a href="/?pn=board.list&_menu=notice" class="btn">공지사항</a></li>
				</ul>
			</div>
		</li>

		<li class="li bank_box">
			<div class="title_box"><span class="tit">BANK ACCOUNT</span></div>
			<div class="bank">
				<?php
				$NoneBank = _MQ_assoc(" select * from smart_bank_set where (1) order by bs_idx asc ");
				if(count($NoneBank) <= 0) $NoneBank = array();
				foreach($NoneBank as $k=>$v) {
				?>
					<ul>
						<li class="left_tit"><?php echo $v['bs_bank_name']; ?></li>
						<li class="right_num">
							<div class="number"><?php echo $v['bs_bank_num']; ?></div>
							<div class="name">(예금주 : <?php echo $v['bs_user_name']; ?>)</div>
						</li>
					</ul>
				<?php } ?>
			</div>
			<div class="btn_box">
				<ul>
					<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
					<?php if ( !$none_member_buy ) { ?>
					<!-- 로그인전에도 메뉴 계속 노출 로그인페이지로 넘기고 이동 -->
					<li><a href="<?php echo (is_login()?'/?pn=mypage.order.list':'/?pn=service.guest.order.list'); ?>" class="btn"><?php echo (is_login()?'주문조회':'비회원 주문조회'); ?></a></li><!-- 로그인후에는 "주문조회"로 -->
					<?php } ?>
					<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
					<?php if(!is_login()) { ?>
						<li><a href="/?pn=pages.view&type=agree&data=guide" class="btn">회원가입 혜택</a></li><!-- 이용안내로 링크, 로그인 후에는 li숨김 -->
					<?php } ?>
					<li><a href="/?pn=mypage.main" class="btn">마이페이지</a></li>
				</ul>
			</div>
		</li>

	</ul>
</div>
<!-- / 메인 고객센터 -->