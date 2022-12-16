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
<!-- ◆통합검색 -->
<div class="c_comb_search c_section">
	<div class="layout_fix">
		<div class="result">
			<ul>
				<?php if($TotalCount > 0) { // 검색결과가 있는경우 ?>
					<li>'<?php echo ($search_word?$search_word:($search_hashtag?'#'.implode(', #', explode(',', $search_hashtag)).'':null)); ?>' 검색결과</li>
					<li><?php echo number_format($TotalCount); ?>개</li>
				<?php } else { ?>
					<li>검색결과가 없습니다.</li>
				<?php } ?>
			</ul>
		</div>

		<!-- 검색폼 -->
		<form action="/" method="get" onsubmit="return searchPagesFunction(this);">
			<input type="hidden" name="pn" value="product.search.list">
			<input type="hidden" name="cuid" value="<?php echo $cuid; ?>">
			<input type="hidden" name="search_hashtag" value="<?php echo $search_hashtag; ?>">
			<input type="hidden" name="search_price" value="<?php echo $search_price; ?>">
			<input type="hidden" name="search_brand" value="<?php echo $search_brand; ?>">
			<input type="hidden" name="search_boon" value="<?php echo $search_boon; ?>">
			<div class="form_box">
				<div class="search_form">
					<label>
						<input type="checkbox" name="detail_search" value="Y" class="check js_detail_search"<?php echo ($detail_search == 'Y'?' checked':null); ?>/>결과 내 재검색
					</label>
					<input type="text" name="search_word" value="<?php echo $search_word; ?>" class="input_design js_search_word" placeholder="검색어를 입력해주세요."<?php echo ($detail_search == 'Y'?' style="display:none"':null); ?>/>
					<input type="text" name="search_word_detail" value="<?php echo $search_word_detail; ?>" class="input_design js_detail_search_word" placeholder="상세 검색어를 입력해주세요."<?php echo ($detail_search == 'Y'?null:' style="display:none"'); ?>/>
					<button type="submit" class="btn_search"><span class="btn_txt">통합검색</span></button>
				</div>
			</div>
		</form>

		<?php if(($TotalCount > 0 || $TotalAllCount > 0)) { // 검색결과가 있는경우 ?>
			<?php if(count($search_option) > 0) { // 추가검색(상세검색) ?>
				<!-- 조건박스 / 검색결과 없을때 안나옴 -->
				<div class="condition">
					<ul class="ul">
						<?php
						if(in_array('category', $search_option)) { // 카테고리
							// 카테고리 전체 선택 여부(초기: false)
							$AllChecked = false;

							// 카테고리 전체 선택 여부
							if(empty($cuid) || $cuid == '') $AllChecked = true;

							// 카테고리 전체 선택 여부따른 전체 선택 URL 변경
							$AllCheckUrl = ProductOrderLinkBuild(array('cuid'=>'', 'listpg'=>1));
						?>
							<li class="li">
								<div class="division">
									<span class="tit">카테고리</span>
									<?php/*
									<!-- 처음에는 전체 선택, 다른 카테고리를 선택했을 경우 checked 해제 -->
									<label class="all"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>/>전체</label>
									*/?>
								</div>
								<div class="list js_scroll_fix">
									<ul>
										<!-- 2차 카테고리 있을 경우 have_ctg클래스 추가 / 클릭시 if_open 클래스 추가  -->
										<?php
										foreach($arr_category as $ack=>$acv) {
											$cateUrl = ProductOrderLinkBuild(array('cuid'=>$ack, 'listpg'=>1)); // 각 해시태그 스위칭 URL
										?>
											<li class="<?php echo (count($acv['item']) > 0?'have_ctg':null); ?><?php echo (isset($ActiveCate['cuid'][0]) && $ActiveCate['cuid'][0] == $ack?' if_open':null); ?>">
												<!-- 1차 카테고리 / 활성화시 hit 클래스 추가 -->
												<a href="<?php echo $cateUrl; ?>" class="ctg<?php echo (isset($ActiveCate['cuid'][0]) && $ActiveCate['cuid'][0] == $ack?' hit':null); ?>"><?php echo $acv['name']; ?></a>
												<?php if(count($acv['item']) > 0) { ?>
													<!-- 2차 카테고리 / 활성화시 hit 클래스 추가 -->
													<div class="open_box">
														<?php
														foreach($acv['item'] as $ack2=>$acv2) {
															$cateSubUrl = ProductOrderLinkBuild(array('cuid'=>$ack2, 'listpg'=>1)); // 각 해시태그 스위칭 URL
														?>
															<a href="<?php echo $cateSubUrl; ?>" class="btn<?php echo (isset($ActiveCate['cuid'][1]) && $ActiveCate['cuid'][1] == $ack2?' hit':null); ?>"><?php echo $acv2['name']; ?></a>
														<?php } ?>
													</div>
												<?php } ?>
											</li>
										<?php } ?>
									</ul>
								</div>
							</li>
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
							<li class="li">
								<div class="division">
									<span class="tit">브랜드</span>
									<?php/*
									<label class="all"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>/>전체</label>
									*/?>
								</div>
								<div class="list js_scroll_fix">
									<ul>
										<?php
										foreach($arr_brand as $bk=>$bv) {
											$brandUrl = ProductOrderLinkBuild(array('search_brand'=>switch_text($search_brand, $bk), 'listpg'=>1)); // 각 해시태그 스위칭 URL
										?>
											<li><label class="opt"><input type="checkbox" onclick="location.href='<?php echo $brandUrl; ?>';"<?php echo (in_array($bk, explode(',', $search_brand))?' checked':null); ?>/><span class="txt"><?php echo $bk; ?></span></label></li>
										<?php } ?>
									</ul>
								</div>
							</li>
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
							<li class="li">
								<div class="division">
									<span class="tit">해시태그</span>
									<?php/*
									<label class="all"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>/>전체</label>
									*/?>
								</div>
								<div class="list js_scroll_fix">
									<ul>
										<?php
										foreach($arr_hashtag as $hk=>$hv) {
											$HashTagUrl = ProductOrderLinkBuild(array('search_hashtag'=>switch_text($search_hashtag, $hk), 'listpg'=>1)); // 각 해시태그 스위칭 URL
										?>
											<li><label class="opt"><input type="checkbox" onclick="location.href='<?php echo $HashTagUrl; ?>';"<?php echo (in_array($hk, explode(',', $search_hashtag))?' checked':null); ?>/><span class="txt">#<?php echo $hk; ?></span></label></li>
										<?php } ?>
									</ul>
								</div>
							</li>
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
							<li class="li">
								<div class="division">
									<span class="tit">가격대</span>
									<?php/*
									<label class="all"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>/>전체</label>
									*/?>
								</div>
								<div class="list js_scroll_fix">
									<ul>
										<li><label class="opt"><input type="radio" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>/><span class="txt">전체</span></label></li>
										<?php
										foreach($skin_price as $pk=>$pv) {
											$priceUrl = ProductOrderLinkBuild(array('search_price'=>$pv, 'listpg'=>1)); // 각 해시태그 스위칭 URL
										?>
											<li><label class="opt"><input type="radio" onclick="location.href='<?php echo $priceUrl; ?>';"<?php echo ($search_price == $pv?' checked':null); ?>/><span class="txt"><?php echo $pk; ?></span></label></li>
										<?php } ?>
									</ul>
								</div>
							</li>
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
							<li class="li">
								<div class="division">
									<span class="tit">혜택구분</span>
									<?php/*
									<label class="all"><input type="checkbox" onclick="location.href='<?php echo $AllCheckUrl; ?>';"<?php echo ($AllChecked === true?' checked':null); ?>/>전체</label>
									*/?>
								</div>
								<div class="list js_scroll_fix">
									<ul>
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
						<?php } ?>
					</ul>
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


			<?php if(count($res) > 0) { ?>
				<!-- ◆ 상품리스트 -->
				<div class="sub_item" id="total_cnt">
					<div class="layout_fix">
						<?php
						$ActiveListCol = $siteInfo['s_search_display'];

						// {{{스킨유형별개수설정}}}
						$ActiveColList = array();
						$tempSkinInfo = SkinInfo('category');
						if($tempSkinInfo['pc_list_depth']) $ActiveColList = explode(',', $tempSkinInfo['pc_list_depth']); // pc_list_depth or mo_list_depth
						$ActiveColListDefault = $tempSkinInfo['pc_list_depth_default']; // pc_list_depth_default or mo_list_depth_default
						if(count($ActiveColList) > 0) {
							$FindDefaultKeyArr = array_flip($ActiveColList);
							if(in_array($ActiveColListDefault, $ActiveColList)) {
								unset($FindDefaultKeyArr[$ActiveColListDefault]);
								$ActiveColList = array_values(array_flip($FindDefaultKeyArr));
							}
						}

						$ActiveListColClass = '';
						if($list_type == 'list') $ActiveListCol = '1';
						if(in_array($ActiveListCol, $ActiveColList)) $ActiveListColClass = ' if_col'.$ActiveListCol; // 기본4단 이외 5, 1일 경우 클래스 변경
						?>
						<!-- 리스트 제어 -->
						<div class="item_list_ctrl">
							<div class="total">전체 상품 <strong><?php echo number_format($TotalCount); ?></strong>개</div>
							<div class="ctrl_right">
								<!-- 리스트 정렬 -->
								<div class="range">
									<ul>
										<!-- 활성화시 hit클래스 추가 -->
										<li<?php echo (!$_order || $_order == ''?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'', 'listpg'=>1)); ?>#total_cnt" class="btn">기본순</a></li>
										<li<?php echo ($_order == 'sale'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'sale', 'listpg'=>1)); ?>#total_cnt" class="btn">인기순</a></li>
										<li<?php echo ($_order == 'date'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'date', 'listpg'=>1)); ?>#total_cnt" class="btn">등록일순</a></li>
										<li<?php echo ($_order == 'price_desc'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'price_desc', 'listpg'=>1)); ?>#total_cnt" class="btn">높은 가격순</a></li>
										<li<?php echo ($_order == 'price_asc'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'price_asc', 'listpg'=>1)); ?>#total_cnt" class="btn">낮은 가격순</a></li>
										<li<?php echo ($_order == 'pname'?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('_order'=>'pname', 'listpg'=>1)); ?>#total_cnt" class="btn">상품명순</a></li>
									</ul>
								</div>
								<div class="select">
									<div class="this_ctg ">
										<!-- 여기에 선택한 값이 나타남 -->
										<div class="btn" onclick="return false;"><?php echo $listmaxcount; ?>개씩 보기</div>
										<!-- <a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>$listmaxcount, 'listpg'=>1)); ?>#total_cnt" class="btn"><?php echo $listmaxcount; ?>개씩 보기</a> -->
										<div class="open_ctg">
											<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>20, 'listpg'=>1)); ?>#total_cnt" class="option">20개씩 보기</a>
											<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>40, 'listpg'=>1)); ?>#total_cnt" class="option">40개씩 보기</a>
											<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>60, 'listpg'=>1)); ?>#total_cnt" class="option">60개씩 보기</a>
											<a href="<?php echo ProductOrderLinkBuild(array('_event'=>$_event, 'typeuid'=>$typeuid, '_order'=>$_order, 'listmaxcount'=>80, 'listpg'=>1)); ?>#total_cnt" class="option">80개씩 보기</a>
										</div>
									</div>
									<script>
										$(document).on('click','.btn',function(){
											var targetClass = '.this_ctg'; // 클릭 시 타겟이 되는 클래스 (css 선택자 지정할때처럼 선택 지정자)
											var addClassName = 'if_open'; // 클릭 시 추가되는 클래스 (명만 써주시면됩니다.)
											var chk = $(targetClass).hasClass(addClassName);
											if( chk == false){ $(targetClass).addClass(addClassName); }
											else {  $(targetClass).removeClass(addClassName);  }
										});
									</script>
								</div>
							</div>
						</div>
						<!-- / 리스트 제어 -->




						<?php
						// 상품리스트 호출
						include(OD_SITE_SKIN_ROOT.'/ajax.product.list.php');
						?>


						<?php if(count($res) > 0) { ?>
							<!-- 페이지네이트 (상품목록 형) -->
							<div class="c_pagi">
								<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
							</div>
							<!-- / 페이지네이트 (상품목록 형) -->
						<?php } ?>
					</div>
				</div>
				<!-- /상품리스트 -->
			<?php } else { ?>
				<!-- 검색결과 없을때 -->
				<div class="none">
					<div class="gtxt">
						입력하신 단어로 검색된 결과가 없습니다.
						<div class="sub_txt" style="display:none">
							오타가 없는 정확한 검색어인지 확인해주세요.<br/>
							보다 일반적인 검색어나 띄어쓰기를 다르게 해서 다시 검색해보세요.<br/>
							조건검색을 했다면, 해당조건이 맞지 않을 수 있으니 다른조건으로 검색해보세요.
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<!-- 검색결과 없을때 -->
			<div class="none">
				<div class="gtxt">
					입력하신 단어로 검색된 결과가 없습니다.
					<div class="sub_txt" style="display:none">
						오타가 없는 정확한 검색어인지 확인해주세요.<br/>
						보다 일반적인 검색어나 띄어쓰기를 다르게 해서 다시 검색해보세요.<br/>
						조건검색을 했다면, 해당조건이 맞지 않을 수 있으니 다른조건으로 검색해보세요.
					</div>
				</div>
			</div>
		<?php } ?>



		<?php if((($TotalCount <= 0 && $TotalAllCount <= 0) || count($res) <= 0) && count($HitProduct) > 0) { // 검색결과가 없는경우 ?>
		<!-- 검색된 상품없을 경우 (랜덤으로 상품노출 ㅣ 관리자 설정 (장바구니 없을때와 같은 형태로 설정) -->
		<!-- ◆ 공통영역의 상품리스트 (디자인에 맞춰서 들어감) -->
		<div class="c_item_list">
			<div class="layout_fix">
				<div class="c_other_item">다른 고객이 많이 찾은 상품</div>

				<!-- ◆ 상품리스트-->
				<div class="item_list">
					<ul>
						<?php
						foreach($HitProduct as $k=>$v) {
						?>
							<li class="js_active_list_col">
							<?php 
								$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
								$locationFile = basename(__FILE__); // 파일설정
								include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
							?>
							</li>
						<?php } ?>
					</ul>
				</div>
				<!-- / 상품리스트 -->
			</div>
		</div>
		<!-- / 공통영역의 상품리스트 -->
	<?php } ?>


	</div>
</div>
<!-- /통합검색 -->







<script type="text/javascript">
	// 페이지 내부 검색 필수 여부 체크
	function searchPagesFunction(target) {
		var ck = $(target).find('.js_detail_search').is(':checked');
		if($(target).find('.js_search_word').val() == '') {
			alert('검색어를 입력해주세요');
			$(target).find('.js_search_word').focus();
			return false;
		}
		if(ck === true && $(target).find('.js_detail_search_word').val() == '') {
			alert('상세 검색어를 입력해주세요');
			$(target).find('.js_detail_search_word').focus();
			return false;
		}
		return true;
	}

	// 결과 내 재검색(상세검색)
	function DetailSearch() {
		var ck = $('.js_detail_search').is(':checked');
		if(ck === true) {
			$('.js_search_word').hide();
			$('.js_detail_search_word').show();
			$('.js_detail_search_word').focus();
		}
		else {
			$('.js_search_word').show();
			$('.js_detail_search_word').hide();
			$('.js_detail_search_word').val(''); // 상세 검색어 초기화
		}
	}
	$(document).ready(DetailSearch);
	$(document).on('click', '.js_detail_search', DetailSearch);
</script>