<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$TotalCount -> 전체 검색 수 => (/program/product.search.list.php에서 지정)
	$res -> 검색결과 데이터 => (/program/product.search.list.php에서 지정)
	$arr_hashtag -> 검색결과에 해당되는 해시태그 => (/program/product.search.list.php에서 지정)
	$arr_brand -> 검색결과에 해당되는 브랜드 => (/program/product.search.list.php에서 지정)
	$arr_price -> 기본 가격 정렬 변수(직접 사용 금지: $skin_price에 필요한 항목만 추출하여 사용) => (/program/product.search.list.php에서 지정)
		EX>
		// 가격 검색 리스트 / 필요한 항목만 사용
		$skin_price = array(
			'1만원 이하'=>'10000_lower',
			'3만원 이하'=>'30000_lower',
			'5만원 이하'=>'50000_lower',
			'7만원 이하'=>'70000_lower',
			'7만원 이상'=>'70000_upper',
		);
	$search_word -> 검색키워드 => (/program/product.search.list.php에서 지정)
	$search_hashtag -> 해시키워드/검색키워드에서 해시 추출 외 다이렉트 해시 검색 시 => (/program/product.search.list.php에서 지정)
	$search_option -> 추가 검색 항목 배열 => (/program/product.search.list.php에서 지정)
	$HitProduct -> 다른 고객이 많이 찾은 상품 => (/program/product.search.list.php에서 지정)
*/


// 가격 검색 리스트 / 필요한 항목만 사용
$skin_price = array(
	'1만원 이하'=>'10000_lower',
	'3만원 이하'=>'30000_lower',
	'5만원 이하'=>'50000_lower',
	'7만원 이하'=>'70000_lower',
	'7만원 이상'=>'70000_upper',
);


// 쉼표 기준으로 글자를 추가, 삭제 하는 스위치 함수 - 스킨마다 다를 수 있음으로 해당 페이지에서 함수 생성
function switch_text($origin_text='', $s_text='') {

	$origin_text = array_filter(explode(',', $origin_text));

	if(isset($s_text) && $s_text != '') $s_text = trim($s_text);
	if(count($origin_text) > 0) {
		$origin_text = array_map('trim', $origin_text);
		ksort($origin_text);
	}
	if(isset($s_text) && $s_text != '') {
		if(in_array($s_text, $origin_text)) {
			$origin_text = array_flip($origin_text); // index와 value를 flip
			unset($origin_text[$s_text]);
			$origin_text = array_keys($origin_text); // index와 value를 flip 하면서 index초기화
		}
		else {
			$origin_text[] = $s_text;
		}

		// ㄱㄴㄷ순(인덱스) 으로 정렬
		$origin_text = array_flip($origin_text);
		ksort($origin_text);
		$origin_text = array_keys($origin_text); // index와 value를 flip
	}
	return (count($origin_text) > 0?implode(',', $origin_text):'');
}
?>
<!-- ******************************************
     공통페이지 상단(공통)
  -- ****************************************** -->
<div class="c_page_tit if_nomenu"><!-- 열리면 if_open / 메뉴없으면 if_nomenu -->
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
		<div class="tit">통합검색</div>
	</div>
</div>



<!-- ******************************************
     검색결과
  -- ****************************************** -->
<div class="c_section c_search">
	<!-- ◆통합검색 -->
	<div class="c_comb_search">
		<div class="layout_fix">

			<div class="result">
				<ul>
					<?php if($TotalCount > 0) { // 검색결과가 있는경우 ?>
						<!-- 검색결과가 없을때 "검색결과가 없습니다." -->
						<li>'<?php echo ($search_word?$search_word:($search_hashtag?'#'.implode(', #', explode(',', $search_hashtag)).'':null)); ?>' 검색결과</li>
						<li><?php echo number_format($TotalCount); ?>개</li>
					<?php } else { ?>
						<li>검색결과가 없습니다.</li>
					<?php } ?>
				</ul>
			</div>

			<form action="/" method="get" onsubmit="return searchPagesFunction(this);">
				<input type="hidden" name="pn" value="product.search.list">
				<input type="hidden" name="cuid" value="<?php echo $cuid; ?>">
				<input type="hidden" name="search_hashtag" value="<?php echo $search_hashtag; ?>">
				<input type="hidden" name="search_price" value="<?php echo $search_price; ?>">
				<input type="hidden" name="search_brand" value="<?php echo $search_brand; ?>">
				<input type="hidden" name="search_boon" value="<?php echo $search_boon; ?>">
				<!-- 검색폼 -->
				<div class="form_box">
					<div class="search_form">
						<!-- <label class="check"><input type="checkbox" name="" />결과 내 재검색</label> -->
						<input type="text" name="search_word" value="<?php echo $search_word; ?>" class="input_design js_search_word" placeholder="검색어를 입력해주세요." />
						<button type="submit" class="btn_search"><span class="btn_txt">통합검색</span></button>
					</div>
				</div>
			</form>

			<?php if(($TotalCount > 0 || $TotalAllCount > 0)) { // 검색결과가 있는경우 ?>
				<?php if(count($search_option) > 0) { // 추가검색(상세검색) ?>
					<!-- 조건박스 / 검색결과 없을때 안나옴 -->
					<div class="condition"><!-- 아래 상세검색열기 버튼 누르면 if_unfold / 처음에는 닫혀있음 -->
						<?php
						if(in_array('category', $search_option)) { // 카테고리
							// 카테고리 전체 선택 여부(초기: false)
							$AllSelected = false;

							// 카테고리 전체 선택 여부
							if(empty($cuid) || $cuid == '') $AllSelected = true;

							// 카테고리 전체 선택 여부따른 전체 선택 URL 변경
							$AllCheckUrl = ProductOrderLinkBuild(array('cuid'=>'', 'listpg'=>1));
						?>
							<ul class="ul">
								<li class="li"><span class="tit">카테고리</span></li>
								<li class="li">
									<div class="list">
										<!-- 1차를 선택해서 2차가 있으면 2차나옴 (3차안나옴) / 처음에는 1차만 -->
										<div class="select">
											<select onchange="location.href=this.value;">
												<option value="<?php echo $AllCheckUrl; ?>"<?php echo ($AllSelected === true?' checked':null); ?>>전체 카테고리</option>
												<?php
												foreach($arr_category as $ack=>$acv) {
													$cateUrl = ProductOrderLinkBuild(array('cuid'=>$ack, 'listpg'=>1));
												?>
													<option value="<?php echo $cateUrl; ?>"<?php echo (isset($ActiveCate['cuid'][0]) && $ActiveCate['cuid'][0] == $ack?' selected':null); ?>><?php echo $acv['name']; ?></option>
												<?php } ?>
											</select>
										</div>
										<?php if(isset($cuid) && $cuid != '' && count($arr_category[$ActiveCate['cuid'][0]]['item']) > 0) { ?>
											<div class="select"><!-- 2차 -->
												<select onchange="location.href=this.value;">
													<option value="<?php echo ProductOrderLinkBuild(array('cuid'=>$ActiveCate['cuid'][0], 'listpg'=>1)); ?>">전체</option>
													<?php
													foreach($arr_category[$ActiveCate['cuid'][0]]['item'] as $ack2=>$acv2) {
														$cateSubUrl = ProductOrderLinkBuild(array('cuid'=>$ack2, 'listpg'=>1));
													?>
														<option value="<?php echo $cateSubUrl; ?>"<?php echo (isset($ActiveCate['cuid'][1]) && $ActiveCate['cuid'][1] == $ack2?' selected':null); ?>><?php echo $acv2['name']; ?></option>
													<?php } ?>
												</select>
											</div>
										<?php } ?>
									</div>
								</li>
							</ul>
						<?php } ?>
						<?php
						if(in_array('brand', $search_option) && count($arr_brand) > 0) { // 브랜드
							// 브랜드 전체 선택 여부(초기: false)
							$AllChecked = false;

							// 브랜드 전체 선택 여부(URL의 브랜드 값과 전체 브랜드 값이 같다면 true)
							if(empty($search_brand) || $search_brand == '') $AllChecked = true;

							// 브랜드 전체 선택 여부따른 전체 선택 URL 변경
							$AllCheckUrl = ProductOrderLinkBuild(array('search_brand'=>'', 'listpg'=>1));
						?>
							<ul class="ul">
								<li class="li"><span class="tit">브랜드</span></li>
								<li class="li">
									<div class="list">
										<ul>
											<?php/*
											<li><label class="opt"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>><span class="txt">전체</span></label></li>
											*/?>
											<?php
											foreach($arr_brand as $bk=>$bv) {
												$brandUrl = ProductOrderLinkBuild(array('search_brand'=>switch_text($search_brand, $bk), 'listpg'=>1));
											?>
												<li><label class="opt"><input type="checkbox" onclick="location.href='<?php echo $brandUrl; ?>';"<?php echo (in_array($bk, explode(',', $search_brand))?' checked':null); ?>/><span class="txt"><?php echo $bk; ?></span></label></li>
											<?php } ?>
										</ul>
									</div>
								</li>
							</ul>
						<?php } ?>
						<?php
						if(in_array('price', $search_option) && count($arr_price) > 0 && count($skin_price) > 0) { // 가격대
							// 가격 전체 선택 여부(초기: false)
							$AllChecked = true;

							// 가격 전체 선택 여부
							if(isset($search_price) && $search_price != '') $AllChecked = false;

							// 가격 전체 선택 여부따른 전체 선택 URL 변경
							$AllCheckUrl = ProductOrderLinkBuild(array('search_price'=>'', 'listpg'=>1));
						?>
							<ul class="ul">
								<li class="li"><span class="tit">가격대</span></li>
								<li class="li">
									<div class="list">
										<ul>
											<li><label class="opt"><input type="radio" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?> /><span class="txt">전체</span></label></li>
											<?php
											foreach($skin_price as $pk=>$pv) {
												$priceUrl = ProductOrderLinkBuild(array('search_price'=>$pv, 'listpg'=>1));
											?>
												<li><label class="opt"><input type="radio" onclick="location.href='<?php echo $priceUrl; ?>';"<?php echo ($search_price == $pv?' checked':null); ?>/><span class="txt"><?php echo $pk; ?></span></label></li>
											<?php } ?>
										</ul>
									</div>
								</li>
							</ul>
						<?php } ?>
						<?php
						if(in_array('boon', $search_option)) { // 혜택구분

							// 혜택구분 전체 선택 여부(초기: false)
							$AllChecked = false;

							// 혜택구분 전체 선택 여부
							if($search_boon == '') $AllChecked = true;

							// 혜택구분 전체 선택 여부따른 전체 선택 URL 변경
							$AllCheckUrl = ProductOrderLinkBuild(array('search_boon'=>'', 'listpg'=>1));
						?>
							<ul class="ul">
								<li class="li"><span class="tit">혜택구분</span></li>
								<li class="li">
									<div class="list">
										<ul>
											<?php/*
											<li><label class="opt"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>/><span class="txt">전체</span></label></li>
											*/?>
											<li>
												<label class="opt">
													<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '무료배송'), 'listpg'=>1)); ?>'"<?php echo (in_array('무료배송', explode(',', $search_boon))?' checked':null); ?> /><span class="txt">무료배송</span>
												</label>
											</li>
											<li>
												<label class="opt">
													<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '조건부 무료배송'), 'listpg'=>1)); ?>'"<?php echo (in_array('조건부 무료배송', explode(',', $search_boon))?' checked':null); ?> /><span class="txt">조건부 무료배송</span></label>
											</li>
											<li>
												<label class="opt">
													<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '할인'), 'listpg'=>1)); ?>'"<?php echo (in_array('할인', explode(',', $search_boon))?' checked':null); ?> /><span class="txt">할인 상품</span>
												</label>
											</li>
											<li>
												<label class="opt">
													<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '쿠폰'), 'listpg'=>1)); ?>'"<?php echo (in_array('쿠폰', explode(',', $search_boon))?' checked':null); ?> /><span class="txt">쿠폰 상품</span>
												</label>
											</li>
											<li>
												<label class="opt">
													<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '적립금'), 'listpg'=>1)); ?>'"<?php echo (in_array('적립금', explode(',', $search_boon))?' checked':null); ?> /><span class="txt">적립금 지급</span>
												</label>
											</li>
										</ul>
									</div>
								</li>
							</ul>
						<?php } ?>
						<?php
						if(in_array('hashtag', $search_option) && count($arr_hashtag) > 0) { // 해시태그

							// 해시태그 전체 선택 여부(초기: false)
							$AllChecked = false;

							// 해시태그 전체 선택 여부(URL의 해시태그 값과 전체 해시태그 값이 같다면 true)
							if(empty($search_hashtag) || $search_hashtag == '') $AllChecked = true; // 해시태그가 없는 경우도 있다.

							// 해시태그 전체 선택 여부따른 전체 선택 URL 변경
							$AllCheckUrl = ProductOrderLinkBuild(array('search_hashtag'=>'', 'search_word'=>urlencode($search_word), 'listpg'=>1)); // 해시태그가 없는 경우도 있다.
						?>
							<ul class="ul">
								<li class="li"><span class="tit">해시태그</span></li>
								<li class="li">
									<div class="list">
										<ul>
											<?php/*
											<li><label class="opt"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>><span class="txt">전체</span></label></li>
											*/?>
											<?php
											foreach($arr_hashtag as $hk=>$hv) {
												$HashTagUrl = ProductOrderLinkBuild(array('search_hashtag'=>switch_text($search_hashtag, $hk), 'listpg'=>1));
											?>
												<li><label class="opt"><input type="checkbox" onclick="location.href='<?php echo $HashTagUrl; ?>';"<?php echo (in_array($hk, explode(',', $search_hashtag))?' checked':null); ?>/><span class="txt">#<?php echo $hk; ?></span></label></li>
											<?php } ?>
										</ul>
									</div>
								</li>
							</ul>
						<?php } ?>
						<div class="ctrl"><a href="#none" class="ctrl_btn js_detail_search" title="상세검색 열기"><span class="tx">상세검색 열기</span></a></div>
					</div>

					<?php if($search_hashtag || $search_price || $search_brand || $search_boon || $cuid) { ?>
						<!-- 검색조건 선택시에만 나타남 -->
						<div class="search_btn_reset">
							<!-- <div class="selected">
								<dl>
									<dt>선택한 조건</dt>
									<dd><strong>BASIC</strong><strong>3만원 이하</strong></dd>
								</dl>
							</div> -->
							<a href="/?pn=<?php echo $pn; ?>&search_word=<?php echo $search_word.($detail_search?'&detail_search='.$detail_search:null).($search_word_detail?'&search_word_detail='.$search_word_detail:null); ?>" class="btn_reset"><span class="tx">검색 초기화</span></a>
						</div>
					<?php } ?>

				<?php } ?>
			<?php } else { ?>
				<!-- 검색결과 없을때 -->
				<div class="none">
					<div class="gtxt">
						입력하신 단어로 검색된 결과가 없습니다.
						<!-- <div class="sub_txt">
							오타가 없는 정확한 검색어인지 확인해주세요.<br/>
							보다 일반적인 검색어나 띄어쓰기를 다르게 해서 다시 검색해보세요.<br/>
							조건검색을 했다면, 해당조건이 맞지 않을 수 있으니 다른조건으로 검색해보세요.
						</div> -->
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<!-- /통합검색 -->


	<?php
	if(count($res) > 0) { // 검색된 상품 리스트

		// {{{스킨유형별개수설정}}}
		$ActiveColList = array();
		$tempSkinInfo = SkinInfo('category');
		if($tempSkinInfo['mo_list_depth']) $ActiveColList = explode(',', $tempSkinInfo['mo_list_depth']); // pc_list_depth or mo_list_depth
		$ActiveColListDefault = $tempSkinInfo['mo_list_depth_default']; // pc_list_depth_default or mo_list_depth_default
		if(count($ActiveColList) > 0) {
			$FindDefaultKeyArr = array_flip($ActiveColList);
			if(in_array($ActiveColListDefault, $ActiveColList)) {
				unset($FindDefaultKeyArr[$ActiveColListDefault]);
				$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
			}
		}

		$ActiveListCol = $siteInfo['s_search_mobile_display'];
		if($list_type == 'list') $ActiveListCol = '1';
		if(in_array($ActiveListCol, $ActiveColList)) $ActiveListColClass = ' if_col'.$ActiveListCol; // 기본3단 이외 2일 경우 클래스 변경
	?>
		<!-- ◆ 공통영역의 상품리스트 (각 스킨 상품리스트가 들어옴) -->
		<div class="c_item_list" id="total_cnt">
			<!-- 리스트 제어 -->
			<div class="item_list_ctrl">
				<ul>
					<li><div class="total">전체 상품 <strong><?php echo ($category_info['c_list_product_view'] == 'N'?'0':number_format($TotalCount)); ?></strong>개</div></li>
					<li>
						<div class="select">
							<select onchange="location.href=this.value;">
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'', 'listpg'=>1)); ?>#total_cnt"<?php echo (!$_order || $_order == ''?' selected':null); ?>>기본 상품순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'sale', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'sale'?' selected':null); ?>>인기 상품순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'date', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'date'?' selected':null); ?>>최근 등록순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'price_desc', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'price_desc'?' selected':null); ?>>높은 가격순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'price_asc', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'price_asc'?' selected':null); ?>>낮은 가격순</option>
								<option value="<?php echo ProductOrderLinkBuild(array('_order'=>'pname', 'listpg'=>1)); ?>#total_cnt"<?php echo ($_order == 'pname'?' selected':null); ?>>상품 이름순</option>
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


			<!-- 페이지네이트 (상품목록 형) -->
			<div class="c_pagi">
				<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
			</div>
		</div>
		<!-- /공통영역의 상품리스트 -->
	<?php } ?>


	<?php if((($TotalCount <= 0 && $TotalAllCount <= 0) || count($res) <= 0) && count($HitProduct) > 0) { // 검색결과가 없는경우 ?>
		<!-- 검색된 상품없을 경우 (랜덤으로 상품노출 ㅣ 관리자 설정 (장바구니 없을때와 같은 형태로 설정) -->
		<div class="c_item_list">
			<div class="c_other_item">다른 고객이 많이 찾은 상품</div>
			<!-- ◆ 상품리스트 : 기본 3단 / 2단 if_col2 -->
			<div class="item_list ">
				<ul>
					<?php
					foreach($HitProduct as $k=>$v) {
					?>
						<li>
							<?php 
								$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
								$locationFile = basename(__FILE__); // 파일설정
								include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
							?>
						</li>
					<?php } ?>
				</ul>
			</div>
			<!-- / ◆ 상품리스트 -->
		</div>
	<?php } ?>
</div>



<script type="text/javascript">
	// 페이지 내부 검색 필수 여부 체크
	function searchPagesFunction(target) {
		var ck = $(target).find('.js_detail_search').is(':checked');
		if($(target).find('.js_search_word').val() == '') {
			alert('검색어를 입력해주세요');
			$(target).find('.js_search_word').focus();
			return false;
		}
	}

	$(document).on('click', '.js_detail_search', function(e) {
		e.preventDefault();
		var view_status = $(this).closest('.condition').hasClass('if_unfold');
		if(view_status === true) {
			$(this).closest('.condition').removeClass('if_unfold');
			$(this).prop('title', '상세검색 열기');
			$(this).find('span.tx').text('상세검색 열기');
		}
		else {
			$(this).closest('.condition').addClass('if_unfold');
			$(this).prop('title', '상세검색 닫기');
			$(this).find('span.tx').text('상세검색 닫기');
		}
	});
</script>