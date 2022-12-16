<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// 내부패치 68번줄 kms 2019-11-05
$page_title = "1:1 온라인 문의";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력
?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">

<div class="c_list_ctrl my_inquiry_page for_change_btn">
			<form role="search" action="/" method="get">
				<input type="hidden" name="pn" value="<?php echo $pn; ?>">
				<input type="hidden" name="search_type" value="search_title,search_content">
				<div class="search">
					<input type="search" name="search_word" value="<?php echo $search_word; ?>" class="input_search" placeholder="검색 단어를 입력해 주세요">
					<input type="submit" value="검색" class="btn_search" title="검색">
				</div>
				<?php if(isset($search_word) && $search_word != '') { ?>
					<!-- 검색한 후 노출 / 검색 전 숨김 -->
					<a href="/?pn=<?php echo $pn; ?>" class="all_btn">전체목록</a>
				<?php } ?>
			</form>
		</div>
	<!-- ◆마이페이지 리스트 탑 -->
	<div class="mypage_list_top">
		<ul>
			<li class="price">
				<span class="lineup">
					<span class="and">답변대기 <strong><?php echo number_format($TotalCountWaiting); ?>개</strong></span>
					<span class="and">답변완료 <strong><?php echo number_format($TotalCountComplete); ?>개</strong></span>
				</span>
			</li>
			<li>
				<div class="c_btnbox">
					<ul>
						<li><a href="/?pn=service.main" class="c_btn h35 line">고객센터 바로가기</a></li>
						<?php // 내부패치 68번줄 kms 2019-11-05 ?>
						<li><a href="/?pn=mypage.inquiry.form" class="c_btn h35 color">문의하기</a></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>


	<?php if(count($row) <= 0) { ?>
		<!-- 내용 없을때 위 div 숨기고 노출 -->
		<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
	<?php } else { ?>
		<!-- ◆마이페이지 리스트 공통 -->
		<div class="c_mypage_list">
			<ul>
				<?php foreach($row as $k=>$v) { ?>
					<li class="js_view" data-uid="<?php echo $v['r_uid']; ?>" data-hit="false">
						<div class="date">
							<strong>작성일 : <?php echo date('Y-m-d', strtotime($v['r_rdate'])); ?></strong>
							<?php if($v['r_status'] == '답변완료' && $v['r_admcontent']) { ?>
								<!-- 답변을 한 경우에만 노출 -->
								<strong>답변일 : <?php echo date('Y-m-d', strtotime($v['r_admdate'])); ?></strong>
							<?php } ?>
						</div>
						<div class="tit"><?php echo htmlspecialchars($v['r_title']); ?></div>
						<div class="double">
							<dl>
								<dt>
									<div class="state">
										<?php if($v['r_status'] == '답변완료') { ?>
											<span class="c_tag h22 red line">답변완료</span>
										<?php } else { ?>
											<span class="c_tag h22 light line">답변대기</span>
										<?php } ?>
									</div>
								</dt>
								<dd>
									<div class="btn">
										<a href="#none" onclick="inquiry_del(<?php echo $v['r_uid']; ?>); return false;" class="c_btn h22 line">삭제</a>
										<a href="#none" class="c_btn h22 dark js_detail_btn">답변 보기</a>
									</div>
								</dd>
							</dl>
						</div>

						<div class="view_box">
							<div class="view_tit"><?php echo htmlspecialchars($v['r_title']); ?></div>
							<div class="view_txt"><?php echo nl2br(stripslashes(htmlspecialchars($v['r_content']))); ?></div>
							<?php
							$getBoardFile = getFilesRes('smart_request', $v['r_uid'].'_user');
							if(count($getBoardFile) > 0) {
							?>
								<div class="file_down user_file">
									<div class="tit">첨부파일</div>
									<div class="link_box">
										<?php foreach($getBoardFile as $kk=>$vv) { ?>
											<a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$vv['f_uid']; ?>" class="link"><?php echo $vv['f_oldname']; ?></a>
										<?php } ?>
									</div>
								</div>
							<?php } ?>

							<?php if($v['r_status'] == '답변완료' && $v['r_admcontent']) { ?>
								<!-- 댓글 -->
								<div class="reply">
									<span class="admin">
										<span class="name">운영자</span><span class="date_num"><?php echo date('Y-m-d', strtotime($v['r_admdate'])); ?></span>
									</span>
									<?php echo nl2br(stripslashes(htmlspecialchars($v['r_admcontent']))); ?>
									<?php
									$getBoardFile = getFilesRes('smart_request', $v['r_uid']);
									if(count($getBoardFile) > 0) {
									?>
										<div class="file_down admin_file">
											<div class="tit">첨부파일</div>
											<div class="link_box">
												<?php foreach($getBoardFile as $kk=>$vv) { ?>
													<a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$vv['f_uid']; ?>" class="link"><?php echo $vv['f_oldname']; ?></a>
												<?php } ?>
											</div>
										</div>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>



	<!-- 페이지네이트 (상품목록 형) -->
	<div class="c_pagi">
		<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
	</div>
</div>
<!-- /공통페이지 섹션 -->



<script type="text/javascript">
	$(document).on('click', '.js_detail_btn', function(e) {
		e.preventDefault();
		var su = $(this).closest('.js_view');
		var _uid = su.data('uid');
		var _visible = su.hasClass('if_open');
		$('.js_view').removeClass('if_open');
		$('.js_detail_btn').attr('title', '열기');
		if(_visible === false) {
			su.addClass('if_open');
			su.find('.js_detail_btn').attr('title', '닫기');
		}
	});


	// 문의삭제
	function inquiry_del(uid) {
		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/mypage.inquiry.pro.php",
				cache: false,
				type: "POST",
				data: "_mode=delete&uid=" + uid ,
				success: function(data){
					if( data == "no data" ) {
						alert('등록하신 글이 아닙니다.');
					}
					else {
						alert('정상적으로 삭제하였습니다.');
						location.reload();
					}
				}
			});
		}
	}
</script>