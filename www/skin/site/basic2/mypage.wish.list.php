<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
	<div class="layout_fix">
	

		<div class="mypage_section">
			<div class="left_sec">
				<!-- ◆공통탭메뉴 -->
				<?php include_once($SkinData['skin_root'].'/member.header.php'); // -- 공통해더 --  ?>
				<!-- / 공통탭메뉴 -->
			</div>


			<div class="right_sec">
				<div class="right_sec_wrap">

					<form class="wish_frm" action="<?php echo OD_PROGRAM_URL; ?>/product.wish.pro.php" method="post" target="common_frame">
						<input type="hidden" name="mode" value="choice_delete">
						<!-- ◆마이페이지 나의찜한상품 -->
						<div class="c_wish_list js_wish_list">
							<div class="wish_tit">
								<span class="tit">찜 리스트</span>
								<!-- 찜한상품 없을때 0개 -->
								<span class="total">총 <?php echo number_format($TotalCount); ?>개</span>
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" onclick="all_check(); return false;" class="c_btn h22 light line">전체선택</a></li>
										<li><a href="#none" onclick="all_uncheck(); return false;" class="c_btn h22 light line">선택해제</a></li>
										<li><a href="#none" onclick="select_delete(); return false;" class="c_btn h22 black">선택삭제</a></li>
									</ul>
								</div>
							</div>

							<div class="wish_item">
								<?php if(count($row) <= 0) { ?>
									<!-- 내용 없을때 ul 없어지고 노출 -->
									<div class="c_none"><span class="gtxt">찜한 상품이 없습니다.</span></div>
								<?php } else { ?>
									<ul>
										<?php
										foreach($row as $k=>$v) {
											$pro_img = get_img_src($v['p_img_list2'], IMG_DIR_PRODUCT); // 상품이미지
											$pro_link = '/?pn=product.view&pcode='.$v['p_code'];
										?>
											<li>
												<div class="wish_box">
													<div class="item">
														<a href="<?php echo $pro_link; ?>" class="upper_link" title="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""/></a>
														<!-- 상품이미지 썸네일 그대로 -->
														<div class="thumb">
															<?php if($pro_img) { ?>
																<div class="real_img"><img src="<?php echo $pro_img; ?>" alt="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>"/></div>
															<?php } ?>
															<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>"/></div>
															<?php if($v['p_stock'] <= 0) { ?>
																<!-- 솔드아웃일 경우 item_quick 삭제 -->
																<div class="soldout"><span class="inner">SOLD OUT</span></div>
															<?php } ?>
														</div>
														<!-- 상품정보 -->
														<div class="info">
															<div class="item_name"><?php echo htmlspecialchars($v['p_name']); ?></div>

															<div class="price">
																<div class="after"><span class="won"><?php echo number_format($v['p_price']); ?></span>원</div>
															</div>
														</div>
													</div>
													<!-- 찜상품 선택 / 찜삭제버튼 -->
													<div class="wish_select">
														<label><input type="checkbox" name="_chk[<?php echo $v['pw_uid']; ?>]" class="check _chk_class" value="Y" /></label>
														<a href="#none" class="c_btn h22 light line" onclick="wish_del('<?php echo $v['pw_uid']; ?>'); return false;">찜삭제</a>
													</div>
												</div>
											</li>
										<?php } ?>
									</ul>
								<?php } ?>
							</div>
						</div>
						<!-- / 마이페이지 나의찜한상품 -->
					</form>
				</div>


				<!-- 페이지네이트 (상품목록 형) -->
				<div class="c_pagi">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /공통페이지 섹션 -->

<script type="text/javascript">
function select_delete() {
	if($('._chk_class').length < 1) { alert('찜한 상품이 없습니다.'); return; }
	if($('._chk_class').is(':checked') != true) { alert('삭제할 상품을 선택하세요.'); return; }
	if(!confirm('선택한 찜 상품을 삭제하시겠습니까?')) return;
	$('.wish_frm').submit();

}
function all_check() {
	if($('._chk_class').length < 1) { alert('찜한 상품이 없습니다.'); return; }
	$('._chk_class').attr('checked', true);
}
function all_uncheck() {
	if($('._chk_class').length < 1) { alert('찜한 상품이 없습니다.'); return; }
	$('._chk_class').attr('checked', false);
}
function wish_del(pw_uid) {
	if(confirm('정말 삭제하시겠습니까?')) common_frame.location.href=('<?php echo OD_PROGRAM_URL; ?>/product.wish.pro.php?mode=delete&uid='+pw_uid);
}
</script>