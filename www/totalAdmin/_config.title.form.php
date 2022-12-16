<?php

	include_once('wrap.header.php');

	// DB 생성 여부 체크
	$row_chk = _MQ(" SHOW TABLES LIKE 'smart_site_title' ");
	if(count($row_chk) < 1){
		echo '
			<div class="data_form">
				<table class="table_form">
					<tbody>
						<tr>
						<tr>
							<td>
								<div class="tip_box">
									<div class="c_tip black">사이트 타이틀 설정에 필요한 DB가 생성되지 않았습니다.</div>
								</div>
								<div class="tip_box">
									<div class="c_tip black">하단의 <em>DB생성</em>버튼을 눌러 DB를 추가해주세요.</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- 가운데정렬버튼 -->
				<div class="c_btnbox">
					<ul><li><a href="_config.title.pro.php?_mode=create" class="c_btn h46 red line">DB생성</a></li></ul>
				</div>
			</div>
		';
		include_once('wrap.footer.php');
		exit;
	}


	// 2018-11-09 SSJ :: 사이트 타이틀 기본 적용 페이지
	$arr_site_title_page = array(
		// 기본페이지
		'default' => array(
			'name'=> '기본페이지',
			'list' => array(
				'/' => array('name'=>'메인페이지', 'default'=>'{공통타이틀}'),
				'/?pn=product.list' => array('name'=>'상품목록', 'default'=>'{카테고리명} - {사이트명}'),
				'/?pn=product.promotion_list' => array('name'=>'쇼핑몰 기획전 홈', 'default'=>'쇼핑몰 기획전 - {사이트명}'),
				'/?pn=product.promotion_view' => array('name'=>'쇼핑몰 기획전 상세', 'default'=>'{기획전명} - {사이트명}'),
				'/?pn=product.brand_list' => array('name'=>'브랜드상품', 'default'=>'브랜드 상품 - {사이트명}'),
				'/?pn=product.brand_list&uid=' => array('name'=>'브랜드상품(브랜드 선택 시)', 'default'=>'{브랜드명} - {사이트명}'),
				'/?pn=product.view' => array('name'=>'상품상세보기', 'default'=>'{상품명} - {사이트명}'),
				'/?pn=product.search.list' => array('name'=>'상품검색', 'default'=>'{검색어} 검색결과 - {사이트명}'),
				'/?pn=shop.cart.list' => array('name'=>'장바구니', 'default'=>'장바구니 - {사이트명}'),
				'/?pn=shop.order.form§§/?pn=shop.order.result' => array('name'=>'주문/결제', 'default'=>'주문/결제 - {사이트명}'),
				'/?pn=shop.order.complete' => array('name'=>'주문완료', 'default'=>'주문완료 - {사이트명}'),
			),
		),

		// 회원/로그인
		'member' => array(
			'name'=> '멤버쉽',
			'list' => array(
				'/?pn=member.login.form' => array('name'=>'로그인', 'default'=>'로그인 - {사이트명}'),
				'/?pn=member.join.agree§§/?pn=member.join.form' => array('name'=>'회원가입', 'default'=>'회원가입 - {사이트명}'),
				'/?pn=member.join.complete' => array('name'=>'가입완료', 'default'=>'가입완료 - {사이트명}'),
				'/?pn=member.find.form' => array('name'=>'아이디/비밀번호 찾기', 'default'=>'아이디/비밀번호 찾기 - {사이트명}'),
			),
		),

		// 회원/마이페이지
		'mypage' => array(
			'name'=> '마이페이지',
			'list' => array(
				'/?pn=mypage.main' => array('name'=>'메인', 'default'=>'마이페이지 - {사이트명}'),
				'/?pn=mypage.order.list§§/?pn=mypage.order.view' => array('name'=>'주문내역', 'default'=>'주문내역 - {사이트명}'),
				'/?pn=mypage.point.list' => array('name'=>'적립금', 'default'=>'적립금 - {사이트명}'),
				'/?pn=mypage.coupon.list' => array('name'=>'쿠폰', 'default'=>'쿠폰 - {사이트명}'),
				'/?pn=mypage.inquiry.form' => array('name'=>'1:1 온라인 문의', 'default'=>'1:1 온라인 문의 - {사이트명}'),
				'/?pn=mypage.wish.list' => array('name'=>'찜한상품', 'default'=>'찜한상품 - {사이트명}'),
				'/?pn=mypage.inquiry.list' => array('name'=>'문의내역', 'default'=>'문의내역 - {사이트명}'),
				'/?pn=mypage.eval.list' => array('name'=>'상품후기', 'default'=>'상품후기 - {사이트명}'),
				'/?pn=mypage.qna.list' => array('name'=>'상품문의', 'default'=>'상품문의 - {사이트명}'),
				'/?pn=mypage.modify.form' => array('name'=>'정보수정', 'default'=>'정보수정 - {사이트명}'),
				'/?pn=mypage.login.log' => array('name'=>'로그인기록', 'default'=>'로그인기록 - {사이트명}'),
				'/?pn=mypage.leave.form' => array('name'=>'회원탈퇴', 'default'=>'회원탈퇴 - {사이트명}'),
			),
		),

		// 게시판
		'board' => array(
			'name'=> '게시판',
			'list' => array(
				'/?pn=board.list' => array('name'=>'게시판 리스트', 'default'=>'{게시판명} - {사이트명}'),
				'/?pn=board.view' => array('name'=>'게시판 상세보기', 'default'=>'{게시물제목} - {사이트명}'),
				'/?pn=board.form' => array('name'=>'게시판 글쓰기', 'default'=>'{게시판명} - {사이트명}'),
			),
		),

		// 고객센터
		'service' => array(
			'name'=> '고객센터',
			'list' => array(
				'/?pn=service.main' => array('name'=>'메인', 'default'=>'고객센터 - {사이트명}'),
				'/?pn=faq.list' => array('name'=>'자주 묻는 질문', 'default'=>'자주 묻는 질문 - {사이트명}'),
				'/?pn=service.deposit.list' => array('name'=>'미확인 입금자', 'default'=>'미확인 입금자 - {사이트명}'),
			),
		),

		// 커뮤니티
		'cummunity' => array(
			'name'=> '커뮤니티',
			'list' => array(
				'/?pn=service.eval.list' => array('name'=>'상품후기', 'default'=>'상품후기 - {사이트명}'),
				'/?pn=service.qna.list' => array('name'=>'상품문의', 'default'=>'상품문의 - {사이트명}'),
				'/?pn=service.partner.form' => array('name'=>'제휴문의', 'default'=>'제휴문의 - {사이트명}'),
				'/?pn=promotion.attend' => array('name'=>'출석체크', 'default'=>'출석체크 - {사이트명}'),
			),
		),

		// 일반페이지
		'normal' => array(
			'name'=> '일반페이지',
			'list' => array(
				'/?pn=pages.view&type=agree&data=company' => array('name'=>'회사소개', 'default'=>'회사소개 - {사이트명}'),
				'/?pn=pages.view&type=agree&data=guide' => array('name'=>'이용안내', 'default'=>'이용안내 - {사이트명}'),
				'/?pn=pages.view&type=agree&data=agree' => array('name'=>'이용약관', 'default'=>'이용약관 - {사이트명}'),
				'/?pn=pages.view&type=agree&data=privacy' => array('name'=>'개인정보처리방침', 'default'=>'개인정보처리방침 - {사이트명}'),
				'/?pn=pages.view&type=agree&data=deny' => array('name'=>'이메일무단수집거부', 'default'=>'이메일무단수집거부 - {사이트명}'),
			),
		),
	);


	// 2018-11-09 SSJ :: 사이트 타이틀 적용가능 치환자
	$arr_site_title_replace = array(
		'/?pn=product.list' => '{카테고리명}',
		'/?pn=product.promotion_view' => '{기획전명}',
		'/?pn=product.brand_list&uid=' => '{브랜드명}',
		'/?pn=product.view' => '{상품명}',
		'/?pn=product.search.list' => '{검색어}',
		'/?pn=board.list' => '{게시판명}',
		'/?pn=board.view' => '{게시판명},{게시물제목}',
		'/?pn=board.form' => '{게시판명}',
	);

	// --- {공통타이틀}, {사이트명} 기본적용
	$app_replace = '<li data-text="{공통타이틀}" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">{공통타이틀}</strong> : 공통타이틀</li><li data-text="{사이트명}" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">{사이트명}</strong> : 사이트명</li>';

	/*
	CREATE TABLE  `hy30_db`.`smart_site_title` (
	`sst_uid` INT( 11 ) UNSIGNED NULL AUTO_INCREMENT COMMENT  '고유번호',
	`sst_name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지명',
	`sst_page` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지URL',
	`sst_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지 타이틀',
	PRIMARY KEY (  `sst_uid` ) ,
	INDEX (  `sst_name` )
	) ENGINE = MYISAM COMMENT =  '사이트 타이틀 설정';
	*/


	// 2018-11-09 SSJ :: 페이지 타이틀 설정 불러오기
	$arrTitleSet = array();
	$res = _MQ_assoc(" select * from smart_site_title where 1 order by sst_uid ");
	if(count($res) > 0){
		foreach($res as $k=>$v){
			$v['sst_page'] = implode('§§', array_filter(explode('§§', $v['sst_page'])));
			$arrTitleSet[$v['sst_page']] = $v;
		}
	}
?>

<style>
/* ------------ 스타일 시트 불러오기 ------------------ */
.sms_code {border:1px solid #ccc; border-top:0; display:none;}
.sms_code .inner_box {display:table; width:100%; box-sizing:border-box; table-layout:fixed;}
.sms_code ul {display:table-cell; vertical-align:middle; padding:3px 10px; }
.sms_code li {cursor: move; list-style:none; float:left; background:#eee; border:1px solid #ddd; border-radius:100px; box-sizing:border-box; letter-spacing:-1px;}
.sms_code li {height:27px; line-height:24px; line-height:23px\0; margin:3px; padding:0 15px; }
.sms_code li strong {letter-spacing:0px;}
</style>



<form name="frm" method="post" action="_config.title.pro.php" ENCTYPE="multipart/form-data" onsubmit="return submitFunc();">
<input type="hidden" name="_mode" value="modify">
<input type="hidden" name="menuUid" value="<?php echo $menuUid; ?>">

	<div class="group_title"><strong>기본 페이지</strong></div>

	<div class="data_form">
		<!-- ● 데이터 리스트 -->
		<table class="table_list">
			<colgroup><col width="200"><col width="200"><col width="300"><col width="*"></colgroup>
			<thead>
				<tr>
					<th scope="col">구분</th>
					<th scope="col">페이지명</th>
					<th scope="col">페이지 URL</th>
					<th scope="col">타이틀 설정</th>
				</tr>
			</thead>
			<tbody>
				<?PHP
					if(sizeof($arr_site_title_page) > 0 ) {
						foreach( $arr_site_title_page as $k=>$v ){
							if(count($v['list']) == 0) continue; // 세부 목록 체크

							// 적용 메뉴 count
							$app_cnt = count($v['list']);

							echo '<tr>';
							echo '	<th class="" rowspan="'. $app_cnt .'">'. $v['name'] .'</th>';
							// 세부목록
							$_idx = 0;
							foreach($v['list'] as $sk=>$sv){
								if($_idx > 0) echo '</tr><tr>';

								// 치환자 추출
								$_replace = $app_replace;
								if($arr_site_title_replace[$sk]){
									$_ex =  explode(',', $arr_site_title_replace[$sk]);
									foreach($_ex as $ek=>$ev) $_replace .= '<li data-text="'. $ev .'" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">'. $ev .'</strong> : '. str_replace(array('{','}'), '', $ev) .'</li>';
								}

								// 페이지 URL 추출
								$exUrl = array_filter(explode("§§", $sk));
								$strUrl = '';
								if(count($exUrl) > 0){
									foreach($exUrl as $ek=>$ev){
										if($strUrl <> '') $strUrl .= '<br>';
										$strUrl .= stripslashes($ev);
										//$strUrl .= '<a href="'. stripslashes($ev) .'" target="_blank" title="'. $sv .'">'. stripslashes($ev) .'</a>';
									}
								}
								echo '
										<td class="t_left">'. $sv['name'] .'</td>
										<td class="t_left">'. $strUrl .'</td>
										<td class="t_left">
											<div class="js_drop_wrap">
												<input type="hidden" name="_uid[]" value="1">
												<input type="hidden" name="_name[]" value="'. $v['name'] .' - '. $sv['name'] .'" class="js_input_name">
												<input type="hidden" name="_page[]" value="'. stripslashes($sk) .'" class="js_input_page">
												<input type="text" name="_title[]" value="'. ($arrTitleSet[$sk]['sst_title'] ? stripslashes($arrTitleSet[$sk]['sst_title']) : $sv['default']) .'" class="design js_drop_me js_input_title" placeholder="'. $sv['default'] .'" style="width:100%">
												<div class="sms_code"><div class="inner_box"><ul class="replace_item">'. $_replace .'</ul></div></div>
											</div>
										</td>
								';

								// 매칭되는 값은 제외
								unset($arrTitleSet[$sk]);
								$_idx++;
							}
							echo '</tr>';
						}
					}
				?>
				<tr>
					<td class="t_left" colspan="4">
						<div class="tip_box">
							<?php echo _DescStr('<strong>{사이트명} : </strong><em>공통</em>, [환경설정 > 기본설정 > 쇼핑몰 기본정보]메뉴의 "사이트명"항목에 설정된 사이트명이 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{공통타이틀} : </strong><em>공통</em>, [환경설정 > 기본설정 > 쇼핑몰 기본정보]메뉴의 "Title"항목에 설정된 기본 타이틀이 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{카테고리명} : </strong><em>상품목록 전용</em>, 선택된 카테고리명이 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{기획전명} : </strong><em>쇼핑몰 기획전 상세 전용</em>, 선택된 기회전명이 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{브랜드명} : </strong><em>브랜드상품(브랜드 선택 시) 전용</em>, 선택된 브랜드명이 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{상품명} : </strong><em>상품상세보기 전용</em>, 선택된 상품명이 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{검색어} : </strong><em>상품검색 전용</em>, 입력한 검색어가 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{게시판명} : </strong><em>게시판 공통</em>, 선택된 게시판명이 노출됩니다.'); ?>
							<?php echo _DescStr('<strong>{게시물제목} : </strong><em>게시판 상세보기 전용</em>, 선택된 게시물제목이 노출됩니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<div class="group_title"><strong>추가적용 페이지</strong></div>

	<div class="data_form"><table class="table_list"><tr><td>


		<a href="#none" onclick="page_add();" class="c_btn h27 black">페이지 추가하기</a>

		<div ID="page_area" class="clear_both">

			<!-- ● 데이터 리스트 -->
			<table class='table_list'>
				<colgroup><col width="50"><col width="200"><col width="300"><col width="*"><col width='70'></colgroup>
				<thead>
					<tr>
						<th scope="col">순번</th>
						<th scope="col">페이지명</th>
						<th scope="col">페이지 URL</th>
						<th scope="col">타이틀 설정</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?PHP

					$_idx = -1;
					if(sizeof($arrTitleSet) > 0 ) {
						foreach( $arrTitleSet as $k=>$v ){
							$_idx++;

							// 치환자 추출
							$_replace = $app_replace;
							if($arr_site_title_replace[$sk]){
								$_ex =  explode(',', $arr_site_title_replace[$sk]);
								foreach($_ex as $ek=>$ev) $_replace .= '<li data-text="'. $ev .'" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">'. $ev .'</strong> : '. str_replace(array('{','}'), '', $ev) .'</li>';
							}

							// 페이지 URL 추출
							$strUrl = '<a href="'. stripslashes($v['sst_page']) .'" target="_blank" title="'. $v['sst_name'] .'">'. stripslashes($v['sst_page']) .'</a>';
							echo '
								<tr>
									<td ><span class="num">'. ($_idx+1) .'</span></td>
									<td class="t_left"><input type="text" name="_name[]" value="'. stripslashes($v['sst_name']) .'" class="design js_input_name" style="width:100%"></td>
									<td class="t_left"><input type="text" name="_page[]" value="'. stripslashes($v['sst_page']) .'" class="design js_input_page" style="width:100%"></td>
									<td class="t_left">
										<div class="js_drop_wrap">
											<input type="hidden" name="_uid[]" value="1">
											<input type="text" name="_title[]" value="'. ($v['sst_title'] ? stripslashes($v['sst_title']) : '{공통타이틀}') .'" class="design js_drop_me js_input_title" placeholder="{공통타이틀}" style="width:100%">
											<div class="sms_code"><div class="inner_box"><ul class="replace_item">'. $_replace .'</ul></div></div>
										</div>
									</td>
									<td ><div class="lineup-vertical"><a href="#none" onclick="page_delete(this);" class="c_btn h22 gray">삭제</a></div></td>
								</tr>
							';
						}
						echo "
						";
					}
				?>
				</tbody>
			</table>
		</div>
		<div class="tip_box">
			<?php echo _DescStr("<strong>페이지명</strong>은 페이지 구분용으로 관리자 페이지에서만 노출됩니다. "); ?>
			<?php echo _DescStr("<strong>페이지 URL</strong>은 <em>필수항목</em>입니다. 반드시 입력해주세요."); ?>
			<?php echo _DescStr("<strong>페이지 URL</strong>은 추가적용할 페이지의 주소창에서 <em>도매인을 제외한 부분</em>을 모두 입력해주세요. (ex. http://". $_SERVER['SERVER_NAME'] ."/?pn=member.login.form => /?pn=member.login.form)"); ?>
			<?php echo _DescStr("<strong>페이지 URL</strong>은 중복될 수 없습니다. 중복될 경우 나중에 등록된 내용으로 적용됩니다."); ?>
			<?php echo _DescStr("<em>기본페이지에 포함된</em> <strong>페이지 URL</strong>을 등록할 경우 새로 추가되지 않고 기본페이지에 적용됩니다."); ?>
			<?php echo _DescStr("페이지 추가/삭제 후 <strong>확인</strong>버튼을 클릭하여야 반영됩니다."); ?>
		</div>

	</td></tr></table></div>


	<?php echo _submitBTNsub(); ?>


</form>



<script>
$(document).ready(auto_init);
// 스크립트 셋팅
function auto_init(){
	// 치환자 끌어놓기
	$('.replace_item li').disableSelection();
	$(".replace_item li").draggable({helper: 'clone',
		 start: function(e, ui)
		 {
			var _w = ($(this).outerWidth()+1); // SSJ: 2017-09-28 넓이에 소수점이 포함될경우 클론의 텍스트가 두줄되는것 방지
			$(ui.helper).css({'width': _w + 'px'});
		 }
	});
	$(".js_drop_me").droppable({ accept: ".replace_item li", drop: function(ev, ui) {
		$(this).insertAtCaret(ui.draggable.data('text'));
	}});

	// 치환자 노출
	$('.js_drop_me').on('focus', function(){
		$this = $(this).closest('td').find('.sms_code');

		if($this.is(':visible') === false){
			$('.js_drop_wrap .sms_code').hide();
			$(this).closest('td').find('.sms_code').show();
		}
	});

	// 치환자 노출
	$('html').on('click', function(e) {
		if(!$(e.target).hasClass("js_drop_me") && !$(e.target).hasClass("replace_item") && !$(e.target).hasClass("ui-draggable") && !$(e.target).hasClass("replace_item_el")) {
			$('.js_drop_wrap .sms_code').hide();
		}
	});

	$('input').attr({'autocomplete':'off'});

	autoNum();
}


// 항목 추가
function page_add(){
	var _str = '';
	_str += '<tr>';
	_str += '	<td ><span class="num">0</span></td>';
	_str += '	<td class="t_left"><input type="text" name="_name[]" value="" class="design js_input_name" style="width:100%"></td>';
	_str += '	<td class="t_left"><input type="text" name="_page[]" value="" class="design js_input_page" style="width:100%"></td>';
	_str += '	<td class="t_left">';
	_str += '		<div class="js_drop_wrap">';
	_str += '			<input type="hidden" name="_uid[]" value="1">';
	_str += '			<input type="text" name="_title[]" value="" class="design js_drop_me js_input_title" placeholder="{공통타이틀}" style="width:100%">';
	_str += '			<div class="sms_code"><div class="inner_box"><ul class="replace_item"><?php echo $app_replace ?></ul></div></div>';
	_str += '		</div>';
	_str += '	</td>';
	_str += '	<td ><div class="lineup-vertical"><a href="#none" onclick="page_delete(this);" class="c_btn h22 gray">삭제</a></div></td>';
	_str += '</tr>';
	$('#page_area tbody').append(_str);

	auto_init();
}
<?php if(sizeof($arrTitleSet) < 1 ) { echo 'page_add();'; } ?>


// 삭제 - 마지막 element 삭제
function page_delete(o){
	if( confirm("정말 삭제하시겠습니까?") ){
		$(o).closest('tr').remove();

		auto_init();
	}
}

function autoNum(){
	var num = 0;
	$('#page_area tbody .num').each(function(){
		num++;
		var str = (num+'').comma();
		$(this).text(str);
	});
}


// 폼체크
function submitFunc(){
	var chk = 0;
	$('.js_input_page').each(function(){
		if($(this).val().trim() == ''){
			$wrap = $(this).closest('tr');
			if($wrap.find('.js_input_name').val().trim() != '' || $wrap.find('.js_input_title').val().trim() != '') chk++;
		}
	});

	var result = false;
	if(chk>0){
		if(confirm('페이지 URL은 필수 입력항목입니다.\n페이지 URL이 입력되지 않은 항목은 저장되지 않습니다. \n계속진행하시겠습니까?')) result =  true;
		else result =  false;
	}else{
		result =  true;
	}

	if(result === true){
		$('.js_input_title').each(function(){
			if( $(this).val().trim() == '' ) $(this).val($(this).attr('placeholder'));
		});
		return true;
	}else{
		return false;
	}
}
</script>


<?php

	include_once('wrap.footer.php');

?>