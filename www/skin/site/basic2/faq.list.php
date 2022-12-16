
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix">


		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<div class="title"><a href="/?pn=service.main" class="tit">고객센터</a></div>
			<!-- 로케이션 -->
			<div class="c_location">
				<ul>
					<li>홈</li>
					<li>고객센터</li>
					<li>자주 묻는 질문</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->

		<?php 
			include_once($SkinData['skin_root'].'/service.header.php'); // -- 공통해더 -- 
		?>

		<!-- ◆게시판 목록 (공통) -->
		<div class="c_board_list ">
			<!-- 리스트 제어 -->
			<!-- 리스트 제어 -->
			<div class="c_list_ctrl">
				<div class="tit_box">
					<!-- 게시판명 -->
					<span class="tit"><?php echo "자주 묻는 질문"; ?></span>
					<!-- 게시판 목록 수 -->
					<div class="total">TOTAL <strong><?php echo number_format($TotalCount); ?></strong></div>
				</div>
				<form name="boardSearch">
					<input type="hidden" name="pn" value="faq.list">
					<input type="hidden" name="_type" value="<?php echo $_type ?>">
					<div class="ctrl_right">
						<select name="searchMode">
							<option value="tc">제목 + 내용</option>
							<option value="t">제목</option>
							<option value="c">내용</option>
						</select>
						<div class="search">
							<input type="text" name="searchWord" value="<?php echo $searchWord; ?>" class="input_search" placeholder="검색어를 입력해주세요.">
							<input type="submit" name="" value="" class="btn_search" title="검색">
						</div>
						<?php if( in_array($searchMode,array('t','c','tc')) == true) { ?>
						<!-- 검색한 후 노출 / 검색 전 숨김 -->
						<a href="/?pn=faq.list&_menu=<?php echo $_menu; ?>" class="all_btn">전체목록</a>
						<?php } ?>
											
					</div>
				</form>
			</div>


			<!-- 게시판 분류 -->
			<div class="c_depth_box">
				<ul>
					<!-- 활성화시 a에 hit 클래스 추가 -->
					<li><a href="/?pn=faq.list" class="depth<?php echo $_type == '' ? ' hit':'' ?>">전체보기</a></li>
					<?php foreach($arrFaqBoardConfig['faqType'] as $k=>$v) { ?>
					<li><a href="/?pn=faq.list&_type=<?php echo $k ?>" class="depth<?php echo $k == $_type ? ' hit':''; ?>"><?php echo $v?></a></li>
					<?php } ?>
				</ul>
			</div>
			<?php if(count($listFaq) < 1){  ?>
			<!-- 내용 없을때 -->
			<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
			<?php }else{?>
			<table>
				<colgroup>
					<col width="65"/><col width="110"/><col width="*"/><col width="55"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col">번호</th>
						<th scope="col">분류</th>
						<th scope="col">내용</th>
						<th scope="col">보기</th>
					</tr>
				</thead> 
				<tbody>
				<?php foreach($listFaq as $k=>$v) {  ?>
					<!-- 보기 클릭시 if_open 클래스 추가 / 한번더 클릭시 닫기 -->
					<tr class="js_faq_list_item" data-uid="<?php echo $v['uid'] ?>">
						<td class="num"><?php echo $v['num']?></td>
						<td class="field"><?php echo $v['type'] ?></td>
						<td class="tit">
							<div class="title">
								<!-- 클릭시 열림 -->
								<div class="tit_box">
									<a href="#none" onclick="return false;" data-uid="<?php echo $v['uid']; ?>" class="upper_link js_open_faq_content" title="<?php echo $_title; ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""/></a>
									<!-- 아이콘 -->
									<span class="icon">
										<?php echo $v['icon'] ?>
									</span>
									<span class="tt"><?php echo $v['title']; ?></span>
								</div>
							</div>
						</td>
						<!-- 열렸을때 타이틀 닫기로 변경 -->
						<td class="arrow"><a href="#none" onclick="return false;" class="arrow_btn js_open_faq_content" title="내용열기"   data-uid="<?php echo $v['uid']; ?>" ><span class="icon"></span></a></td>
					</tr>
					<!-- 자주묻는질문박스 / 보기 클릭시 노출 -->
					<tr class="board_box">
						<td colspan="4">
							<div class="board_tit"><?php echo $v['title']; ?></div>
							<div class="editor"><?php echo $v['content']; ?></div>		
						</td>
					</tr>
				<?php } ?>
				</tbody> 
			</table>
			
		<?php } ?>
		</div>



		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
		<!-- / 페이지네이트 (상품목록 형) -->

	</div>
</div>
<!-- /공통페이지 섹션 -->

<script>

	// -- 게시물 검색 :: 공통
	$(document).on('submit','form[name="boardSearch"]',function(){
		var sw = $(this).find('[name="searchWord"]').val();
		if( sw.replace(/\s/gi,'') == ''){ alert("검색어를 입력해 주세요."); $(this).find('[name="searchWord"]').focus(); return false; }
		return true; 
	});

	// -- faq 컨텐츠 보기/숨기기
	$(document).on('click','.js_open_faq_content',function(){
		var _uid = $(this).attr('data-uid');
		if( _uid == '' || _uid == undefined){ return false; }
		var chk = $('.js_faq_list_item[data-uid="'+_uid+'"]').hasClass('if_open');

		$('.js_faq_list_item').removeClass('if_open');

		if(chk == true){	
			$('.js_faq_list_item[data-uid="'+_uid+'"]').removeClass('if_open'); 	
			$('.js_open_faq_content.arrow_btn[data-uid="'+_uid+'"]').attr('title','내용열기'); 
		}else{	
			$('.js_faq_list_item[data-uid="'+_uid+'"]').addClass('if_open');	
			$('.js_open_faq_content.arrow_btn[data-uid="'+_uid+'"]').attr('title','내용닫기');
		} 

	});
</script>

