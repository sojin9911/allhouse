<?php include_once('wrap.header.php'); ?>


<!-- ●●●메인탑-->
<div class="main_top">
	<div class="layout_fix">
		<div class="right">
			<div class="sms js_sms_info">
				문자서비스 현황 : 조회중 입니다.
			</div>
			<div class="set_box">
				<!-- 모비톡 충전하기 버튼 -->
				<a href="<?php echo OD_ADMIN_URL; ?>/_config.sms.out_list.php?type=charge" class="set_btn js_charge">충전</a>
				<!-- sms 정보설정 이동 버튼 -->
				<a href="<?php echo OD_ADMIN_URL; ?>/_config.sms.form.php" class="set_btn">설정</a>
			</div>
		</div>
	</div>
</div>
<!-- /●●●메인탑-->




<!-- ●●●관리자메인 -->
<div class="main">
	<div class="layout_fix">
		<!-- 메인단락 -->
		<div class="main_box">



			<div class="left_box">
				<?php
					// ----- 주문/배송 현황 -----
					include_once('_main.static_order.php');
					// ----- 주문/배송 현황 -----
				?>
			</div>


			<div class="right_box">
				<!-- 메인타이틀 -->
				<div class="main_tt">
					<span class="tit">주요 부가서비스</span>
					<!-- 부가서비스 페이지로 이동 -->
					<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=pg_default" class="more_btn" title="더보기"></a>
				</div>
				<div class="service_box">
					<!-- li 2개씩 ul반복 -->
					<ul>
						<li>
							<!-- 클릭시 각 서비스 안내페이지로 이동 -->
							<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=pg_default" class="btn"><img src="images/ic_pg.gif" alt="" />전자결제(PG)</a>
						</li>
						<li>
							<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=member_hp" class="btn"><img src="images/ic_my.gif" alt="" />본인확인 서비스</a>
						</li>
					</ul>
					<ul>
						<li>
							<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=sms_mobitalk" class="btn"><img src="images/ic_talk.gif" alt="" />모비톡(문자메시지)</a>
						</li>
						<li>
							<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=sms_kakao" class="btn"><img src="images/ic_kt.gif" alt="" />카카오 알림톡</a>
						</li>
					</ul>
					<ul>
						<li>
							<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=security_ssl" class="btn"><img src="images/ic_ssl.gif" alt="" />SSL보안서버</a>
						</li>
						<li>
							<a href="<?php echo OD_ADMIN_URL; ?>/_config.sns.form.php?menuUid=15" class="btn"><img src="images/ic_sns.gif" alt="" />SNS 로그인</a>
						</li>
					</ul>
					<ul>
						<li>
							<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=service_mail" class="btn"><img src="images/ic_mail.gif" alt="" />대량메일</a>
						</li>
						<li>
							<a href="<?php echo OD_ADMIN_URL; ?>/_content.php?cont=pg_none_bank" class="btn"><img src="images/ic_money.gif" alt="" />무통장 자동입금</a>
						</li>
					</ul>

				</div>
			</div>
		</div>

		<!-- 메인단락 -->
		<div class="main_box">



			<div class="left_box">
				<?php
					// ----- 쇼핑몰 주요 현황 -----
					include_once('_main.static_log.php');
					// ----- 쇼핑몰 주요 현황 -----
				?>
			</div>



			<div class="right_box">
				<!-- 메인타이틀 -->
				<?php
				// 상품문의
				$Question = _MQ_assoc(" select `pt`.*, case pt_depth when 1 then pt_uid else pt_relation end as orderby_uid from `smart_product_talk` as `pt` inner join smart_product as p on (pt.pt_pcode = p.p_code) where (1) and `pt_type` = '상품문의' and `pt_depth` = 1 order by orderby_uid desc , `pt_uid` asc limit 0, 4 ");
				if(count($Question) <= 0) $Question = array();
				?>
				<div class="main_tt">
					<span class="tit">상품문의</span>
					<!-- 상품문의관리 페이지 이동 -->
					<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.list.php?pt_type=<?php echo urlencode('상품문의'); ?>" class="more_btn" title="더보기"></a>
				</div>
				<div class="post_box">
					<?php if(count($Question) > 0) { ?>
						<ul>
							<!-- li반복 / li 4개까지 노출 -->
							<?php
							foreach($Question as $qk=>$qv) {
								$reply_query = _MQ_assoc(" select * from smart_product_talk where pt_depth = '2' and pt_relation = '{$qv['pt_uid']}' ");
								$is_new = false;
								if(time() - strtotime($qv['pt_rdate']) <= ((60*60*24)*7)) $is_new = true; // 7일 기준
							?>
								<li>
									<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.list.php?_mode=modify&pt_type=<?php echo urlencode($qv['pt_type']); ?>&pt_uid=<?php echo $qv['pt_uid']; ?>" class="posting" title="<?php echo addslashes(htmlspecialchars($qv['pt_title'])); ?>">
										<?php echo ($is_new === true?'<span class="new"></span>':null); ?>
										<span class="title"><?php echo trim(htmlspecialchars($qv['pt_title'])); ?></span>
									</a>
									<!-- 답변완료시 state_ok 클래스 추가 및 답변완료 텍스트 변경 -->
									<?php if(count($reply_query) > 0) { ?>
										<span class="state state_ok">답변완료</span>
									<?php } else { ?>
										<span class="state">답변대기</span>
									<?php } ?>
								</li>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<!-- 내용 없을 경우 -->
						<div class="post_none">등록된 내용이 없습니다.</div>
					<?php } ?>
				</div>

				<!-- 메인타이틀 -->
				<?php
				// 상품후기
				$Review = _MQ_assoc(" select `pt`.*, case pt_depth when 1 then pt_uid else pt_relation end as orderby_uid from `smart_product_talk` as `pt` inner join smart_product as p on (pt.pt_pcode = p.p_code) where (1) and `pt_type` = '상품평가' and `pt_depth` = 1 order by orderby_uid desc , `pt_uid` asc limit 0, 4 ");
				if(count($Review) <= 0) $Review = array();
				?>
				<div class="main_tt">
					<span class="tit">상품후기</span>
					<!-- 상품평관리 페이지 이동 -->
					<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.list.php?pt_type=<?php echo urlencode('상품평가'); ?>" class="more_btn" title="더보기"></a>
				</div>
				<div class="post_box">
					<?php if(count($Review) > 0) { ?>
						<ul>
							<!-- li반복 / li 4개까지 노출 -->
							<?php
							foreach($Review as $rk=>$rv) {
								$reply_query = _MQ_assoc(" select * from smart_product_talk where pt_depth = '2' and pt_relation = '{$rv['pt_uid']}' ");
								$is_new = false;
								if(time() - strtotime($rv['pt_rdate']) <= ((60*60*24)*7)) $is_new = true; // 7일 기준
							?>
								<li>
									<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.list.php?_mode=modify&pt_type=<?php echo urlencode($rv['pt_type']); ?>&pt_uid=<?php echo $rv['pt_uid']; ?>" class="posting" title="<?php echo addslashes(htmlspecialchars($rv['pt_title'])); ?>">
										<?php echo ($is_new === true?'<span class="new"></span>':null); ?>
										<span class="title"><?php echo trim(htmlspecialchars($rv['pt_title'])); ?></span>
									</a>
									<!-- 답변완료시 state_ok 클래스 추가 및 답변완료 텍스트 변경 -->
									<?php if(count($reply_query) > 0) { ?>
										<span class="state state_ok">답변완료</span>
									<?php } else { ?>
										<span class="state">답변대기</span>
									<?php } ?>
								</li>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<!-- 내용 없을 경우 -->
						<div class="post_none">등록된 내용이 없습니다.</div>
					<?php } ?>
				</div>


				<!-- 메인타이틀 -->
				<div class="main_tt">
					<span class="tit">주요게시판 현황</span>
					<!-- 게시판 관리로 이동 -->
					<a href="<?php echo OD_ADMIN_URL; ?>/_bbs.post_mng.list.php" class="more_btn" title="더보기"></a>
				</div>
				<table class="notice">
					<thead>
						<tr>
							<th scope="col">게시판 명</th>
							<th scope="col">오늘 게시물</th>
							<th scope="col">전체 게시물</th>
							<th scope="col">답변/댓글</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$BoardList = _MQ_assoc("
							select
								bi_uid, bi_name,
								if(bi_uid in ('notice' , 'event') , 9999999 , bi_post_cnt )  as app_post_cnt
							from
								smart_bbs_info
							where
								bi_view = 'Y'
							order by
								app_post_cnt desc,
								bi_view_type desc,
								bi_rdate desc
							limit 0, 4
						"); // 공지 -> 이벤트 -> 게시글 많은 순
						if(count($BoardList) <= 0) $BoardList = array();
						foreach($BoardList as $bk=>$bv) {
							$bo_menu = $bv['bi_uid'];
						?>
							<tr>
								<!-- 각 페이지로 링크 -->
								<td><a href="<?php echo OD_ADMIN_URL; ?>/_bbs.post_mng.list.php?searchMode=true&select_menu=<?php echo $bo_menu; ?>" class="btn"><?php echo $bv['bi_name']; ?></a></td>
								<td><?php echo number_format(get_board_cnt($bo_menu," and b_rdate >= '".date('Y-m-d', time())."' ")); ?></td>
								<td><?php echo number_format(get_board_cnt($bo_menu)); ?></td>
								<td><?php echo number_format(get_board_talk_cnt($bo_menu)); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>


		<!-- 메인단락 -->
		<div class="main_box">
			<!-- 하단배너 -->
			<div class="left_box">
				<ul class="main_bn">
					<li><a href="http://www.onedaynet.co.kr/_hy30_content/_banner.php?target=banner1" class="left_bn" title="" target="_blank"><img src="http://www.onedaynet.co.kr/_hy30_content/images/main_bn_gobeyond.jpg" alt="" /></a></li>
					<li><a href="http://www.onedaynet.co.kr/_hy30_content/_banner.php?target=banner2" class="right_bn" title="" target="_blank"><img src="http://www.onedaynet.co.kr/_hy30_content/images/main_bn_onedaynet.jpg" alt="" /></a></li>
				</ul>
			</div>

			<div class="right_box">
				<!-- 메인타이틀 -->
				<div class="main_tt">
					<span class="tit">원데이넷 고객센터</span>
					<span class="cs_tel">☎ 1544-6937</span>
				</div>
				<div class="cs_center">
					<ul>
						<!-- li반복 -->
						<li><span class="title">09:30 ~ 18:00 (점심 12시~13시)</span></li>
						<li><span class="title">토/일요일, 공휴일 휴무</span></li>
					</ul>
				</div>
			</div>
		</div>

	</div>
</div>
<!-- /●●●관리자메인 -->




<script>
	(function(){
		// 문자서비스 현황 조회
		$.get('/totalAdmin/ajax.simple.php?_mode=onedaynet_sms_user', function( data ) {
			if(data.code === 'U00') {
				$('.js_sms_info').html('문자서비스 현황 : SMS <strong>' + data.data.comma() + '</strong>건');
			}
			else {

				if(data.data === undefined) {
					$('.js_sms_info').html('문자서비스 현황 : <strong>미등록 계정</strong>');
					$('.js_charge').prop('href', '#none');
					$('.js_charge').attr('onclick', "if(confirm('계정을 설정 후 이용바랍니다.\\n\\n설정 페이지로 이동하시겠습니까?')) location.href = '<?php echo OD_ADMIN_URL; ?>/_config.sms.form.php'; return false;");
				}
				else {
					$('.js_sms_info').html('문자서비스 현황 : <strong>' + data.data + '</strong>');
					$('.js_charge').prop('href', '<?php echo OD_ADMIN_URL; ?>/_config.sms.out_list.php?type=charge');
					$('.js_charge').removeAttr('onclick');
				}
			}
		}, 'json');
	})();
</script>

<?php include_once('wrap.footer.php'); ?>