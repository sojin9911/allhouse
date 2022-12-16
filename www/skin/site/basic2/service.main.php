<!-- ◆공통페이지 섹션 -->
<div class="c_section c_cs_main">
	<div class="layout_fix">

		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<div class="title"><a href="/?pn=service.main" class="tit">고객센터</a></div>
			<!-- 로케이션 -->
			<div class="c_location">
				<ul>
					<li>홈</li>
					<li>고객센터</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->

		<?php include_once($SkinData['skin_root'].'/service.header.php'); // -- 공통해더 --  ?>

		<!-- ◆고객센터 정보 -->
		<div class="cs_info">
			<ul>
				<li>
					<div class="info_box">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/cs_tel.png" alt="" /></div>
						<dl class="tel">
							<dt><?php echo $siteInfo['s_glbtel']; ?></dt>
							<dd>대표메일 : <a href="mailto:<?php echo $siteInfo['s_ademail']; ?>"><?php echo $siteInfo['s_ademail']; ?></a></dd>
							<?php if( rm_str($siteInfo['s_fax']) > 0) {  ?><dd>팩스번호 : <?php echo $siteInfo['s_fax']; ?></dd> <?php } ?>
						</dl>
					</div>
				</li>
				<li>
					<div class="info_box">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/cs_time.png" alt="" /></div>
						<dl class="time">
							<dt>고객센터 운영시간</dt>
							<dd><?php echo nl2br($siteInfo['s_cs_info']); ?></dd>
						</dl>
					</div>
				</li>
			</ul>
		</div>
		<!-- / 고객센터 정보 -->






		<!-- ◆자주묻는질문 검색창 -->
		<div class="c_faq_search">
			<form name="faqSearch" method="get">
				<input type="hidden" name="pn" value="faq.list">
				<input type="hidden" name="searchMode" value="tc">
				<ul>
					<li class="faq_tit">FAQ</li>
					<li class="search">
						<div class="input_box">
							<div class="search_box"><input type="text" class="input_search" name="searchWord" placeholder="궁금하신 점을 먼저 검색할 수 있습니다."/></div>
							<div class="search_btn"><input type="submit" class="btn" value="질문검색"/></div>
						</div>

						<?php
							$faqKeyword = trim($siteInfo['s_faq_keyword']) != '' ? explode(",",$siteInfo['s_faq_keyword']) : array();
							if( count($faqKeyword) > 0){
						?>
						<!-- 인기키워드 -->
						<div class="keyword">
							<div class="tit">인기키워드</div>
							<div class="word_box">
								<div class="wrapping">
									<?php foreach($faqKeyword as $k=>$v) {  ?>
									<a href="/?pn=faq.list&searchMode=tc&searchWord=<?php echo $v ?>" class="btn"><?php echo $v;?></a>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } // -- FAQ 키워드 있을 시 ?>
					</li>
				</ul>
			</form>
		</div>
		<!-- / 자주묻는질문 검색창 -->






		<!-- ◆자주묻는질문 베스트 -->
		<div class="c_faq_best">
			<div class="faq_list">
				<div class="faq_tit">자주 묻는 질문 TOP <?php echo $arrFaqBoardConfig['bestCnt'];?></div>
				<?php
					$resBest = _MQ_assoc("select *from smart_bbs_faq where bf_best = 'Y' order by bf_uid desc limit 0,".$arrFaqBoardConfig['bestCnt']."");
					if( count($resBest) > 0) {
				?>
				<!-- 자주묻는질문 5개 -->
				<table>
					<colgroup>
						<col width="30"/><col width="100"/><col width="*"/><col width="40"/>
					</colgroup>
					<tbody>
					<?php
					foreach($resBest as $k=>$v) {
						$_title = htmlspecialchars(stripslashes($v['bf_title']));
						$_content = stripslashes($v['bf_content']);
						$arrIcon = array();
						if(time() - strtotime($v['bf_rdate'])< (60*60*24*$arrFaqBoardConfig['newIcon'])) {
							$arrIcon[] = '<img src="'.$SkinData['skin_url'].'/images/c_img/ic_new.gif" alt="새글">';
						}
						$_num = $k+1;
						$_type = $arrFaqBoardConfig['faqType'][$v['bf_type']];
					?>
						<!-- 클릭시 board_box 열림 / if_open 클래스 추가 -->
						<tr class="js_faq_list_item" data-uid="<?php echo $v['bf_uid'] ?>">
							<td class="num"><?php echo $_num; ?></td>
							<td class="field"><?php echo $_type; ?></td>
							<td>
								<div class="posting">
									<a href="#none" onclick="return false" data-uid="<?php echo $v['bf_uid']; ?>" class="upper_link js_open_faq_content" title="<?php echo $_title; ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""></a>
									<!-- 아이콘 -->
									<span class="icon">
										<?php echo count($arrIcon) > 0 ? implode("",$arrIcon): null;?>
									</span>
									<span class="tit"><?php echo $_title; ?></span>
								</div>
							</td>
							<td><a href="#none" onclick="return false;" title="내용열기" data-uid="<?php echo $v['bf_uid']; ?>" class="arrow js_open_faq_content"></a></td>
						</tr>
						<!-- 자주하는질문 답변 박스 -->
						<tr class="board_box">
							<td colspan="4">
								<div class="board_tit"><?php echo $_title; ?></div>
								<!-- 에디터 들어감 -->
								<div class="editor"><?php echo $_content; ?></div>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			<?php }else{ ?>
				<!-- 내용 없을때 -->
				<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
			<?php }?>
			</div>
			<div class="more_btn"><a href="/?pn=faq.list" class="btn"><span class="txt">자주 묻는 질문 전체보기</span></a></div>
		</div>
		<!-- / 자주묻는질문 베스트 -->






		<!-- ◆고객센터 메인 하단 : 공지사항,이벤트 , 1:1 온라인 문의, 광고제휴문의 버튼 -->
		<?php
			// -- event, notice 게시물 최신순으로 가져온다. (공지사항, 비밀글이,2뎁스 아닌것)
			$arrBbs = array('notice'=>'공지사항','event'=>'이벤트');
			$resPost = array();
			foreach($arrBbs as $k=>$v){
				$resPost[$k] = _MQ_assoc("select *from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu) where b_menu = '".$k."' and b_depth = '1' and b_secret != 'Y' order by b_rdate desc limit 0,5  ");
			}
		?>
		<div class="cs_post">
			<!-- ◆공지사항/이벤트 최근글 -->
			<div class="new_post ">
				<div class="tab_menu">
					<ul>
					<?php foreach($arrBbs as $k=>$v){ ?>
						<!-- 활성화시 hit클래스 추가 -->
						<li class="js_post_tab_list<?php echo $k == 'notice' ? ' hit':''?>"><a href="#none" onclick="return false;" class="btn js_post_tab" data-uid="<?php echo $k ?>"><?php echo $v; ?></a></li>
					<?php } ?>
					</ul>
					<!-- 선택된 탭 전체보기 -->
					<?php foreach($arrBbs as $k=>$v){ ?>
					<a href="/?pn=board.list&_menu=<?php echo $k ?>" <?php echo $k != 'notice' ? 'style="display:none;"':''; ?>  data-uid="<?php echo $k ?>" class="btn_more js_post_tab_more"><span class="tx">더보기</span></a>
					<?php } ?>
				</div>
				<?php
				foreach($arrBbs as $k=>$v) {
				?>
				<!-- 이벤트 리스트는 if_event 클래스 추가 -->
				<!-- 리스트 5개까지 노출 -->
				<div class="js_post_list notice_list<?php echo $k != 'notice' ? ' if_'.$k:''; ?>" <?php echo $k != 'notice' ? 'style="display:none;"':''; ?> data-uid="<?php echo $k; ?>" >
				<?php if( count($resPost[$k]) > 0) { ?>
					<ul>
					<?php
					foreach($resPost[$k] as $sk=>$sv){
						$_title = htmlspecialchars(stripslashes($sv['b_title']));
						$arrIcon = array();
						if(time() - strtotime($sv['b_rdate'])< (60*60*24*$sv['bi_newicon_view'])) {
							$arrIcon[] = '<img src="'.$SkinData['skin_url'].'/images/c_img/cs_new.gif" alt="">';
						}

						$addItem = '';
						if($sv['bi_list_type'] == 'event'){
							$_rdate = date('Y-m-d',strtotime($sv['b_sdate'])).' ~ '.date('Y-m-d',strtotime($sv['b_edate']));

							if( $v['b_edate'] < date('Y-m-d') ){
								 $addItem = '<span class="c_tag h21 light line">마감</span>';
							}else{
								$addItem = '<span class="c_tag h21 color line">진행</span>';
							}

						}else{
							$_rdate = date('Y-m-d',strtotime($sv['b_rdate']));
						}

					?>
						<li>
							<div class="posting">
								<a href="/?pn=board.view&_uid=<?php echo $sv['b_uid'] ?>&_menu=<?php echo $sv['b_menu']; ?>" class="upper_link" title="<?php echo $_title;?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""></a>
								<!-- 이벤트리스트에서는 이벤트기간 노출 -->
								<!-- 날짜 --><span class="date"><?php echo $_rdate; ?></span>
								<!-- 아이콘 (게시판 설정과 동일한 기간노출) -->
								<span class="icon"><?php echo count($arrIcon) > 0 ? implode(",",$arrIcon) : null; ?></span>
								<span class="txt"><?php echo $_title ?></span>
								<!-- 이벤트리스트에서만 노출 -->
								<?php echo $addItem; ?>
							</div>
						</li>
					<?php } ?>
					</ul>
				<?php }else { ?>
					<!-- 내용없을경우 ul이 없어지고 -->
					<div class="post_none"><span class="txt">등록된 내용이 없습니다.</span></div>
				<?php } ?>

				</div>
				<?php } ?>
			</div>
			<!-- / 공지사항/이벤트 최근글 -->

			<!-- ◆1:1 온라인 문의, 광고제휴문의 버튼 -->
			<div class="right_faq">
				<div class="faq_box">
					<span class="txt">
						더 궁금하신 점이 있나요?<br/>
						온라인 문의를 통해 해결하실 수 있습니다.
					</span>
					<div class="c_btnbox">
						<ul>
							<?php // 내부패치 68번줄 kms 2019-11-05 ?>
							<li><a href="/?pn=mypage.inquiry.list" class="c_btn h55 color<?php echo is_login() == false ? ' js_login':'' ?>">1:1 온라인문의</a></li>
							<li><a href="/?pn=service.partner.form" class="c_btn h55 color line">광고/제휴문의</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- / 1:1 온라인 문의, 광고제휴문의 버튼 -->
		</div>
		<!-- / 고객센터 메인 하단 -->

	</div>
</div>
<!-- /공통페이지 섹션 -->

<script>

	// -- 공지사항/이벤트 게시판 탭 클릭시
	$(document).on('click','.js_post_tab',function(){
		var _uid = $(this).attr('data-uid');
		$('.js_post_tab_list').removeClass('hit');
		$('.js_post_tab_more').hide();
		$('.js_post_list').hide();

		$(this).closest('li').addClass('hit');
		$('.js_post_tab_more[data-uid="'+_uid+'"]').show();
		$('.js_post_list[data-uid="'+_uid+'"]').show();

	});

	// -- FAQ 검색
	$(document).on('submit','form[name="faqSearch"]',function(){
		var sw = $(this).find('[name="searchWord"]').val(); // 검색값
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
			$('.js_open_faq_content.arrow[data-uid="'+_uid+'"]').attr('title','내용열기');
		}else{
			$('.js_faq_list_item[data-uid="'+_uid+'"]').addClass('if_open');
			$('.js_open_faq_content.arrow[data-uid="'+_uid+'"]').attr('title','내용닫기');
		}
	});
</script>