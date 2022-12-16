<div class="c_section c_mypage">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<!-- 마이페이지 메인으로 이동 -->
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location hide">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<li>주문내역</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->




		<div class="mypage_section">
			<div class="left_sec">

				<!-- ◆공통탭메뉴 -->
				<?php
					// PC 탑 네비
					$pn = 'mypage.cancel_list';
					include_once($SkinData['skin_root'].'/member.header.php');
				?>
				<!-- / 공통탭메뉴 -->
			</div>




		<div class="right_sec">
			<div class="right_sec_wrap">
		<!-- ◆마이페이지 주문통계 -->
		




			<div class="mypage_info">
				<div class="my_info">
					<div class="info">
						<?php // {{{회원등급추가}}}   ?>
						<!-- <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/mypage_info.png" alt=""> -->
						<!-- 등급이미지 : 75 * 75 -->
						<div class="level_img hide">
							<?php
								// == 등급전체 정보를 가져온다.
								$getGroupInfo = getGroupInfo();
							?>
							<img src="<?php echo get_img_src($getGroupInfo[$mem_info['in_mgsuid']]['icon'],IMG_DIR_ICON); ?>" alt="" />

						</div>
						
						<div class="name hide"><?php echo $mem_info['in_name']; ?>님의<br>회원등급은 </div>
						<div class="id hide"><?php echo LastCut($mem_info['in_id'], (strlen($mem_info['in_id'])-0)); ?></div> 
						<!-- 등급이름 -->
						<div class="level_name hide"><?php echo $getGroupInfo[$mem_info['in_mgsuid']]['name'] ?>등급 입니다.</div>

						<div class="info_mylevel">
							<p>
								<?php echo $mem_info['in_name']; ?>님의<br>회원등급은 <?php echo $getGroupInfo[$mem_info['in_mgsuid']]['name'] ?>등급 입니다.
							</p>
						</div>
						<?php // {{{회원등급추가}}} ?>
					</div>




					<?php // {{{회원등급추가}}}   ?>
					<!-- 등급별 혜택정보 -->
					<div class="about_level js_level_stage">
						<a href="#none" class="tip js_level_btn" onclick="return false;"><span class="tx">등급별혜택보기</span></a>

						<div class="level_info">
							<div class="in_box">
								<div class="tit">등급혜택안내<a href="#none" class="btn_close js_level_btn" title="닫기" onclick="return false;"></a></div>
								<div class="table">
									<ul class="thead">
										<li class="opt">회원등급</li>
										<li class="condi">등급조건</li><!--원래는 등급조건 입니다-->
										<li class="bene">등급혜택</li><!--원래는 등급혜택 입니다-->
									</ul>
									<?php
										foreach($getGroupInfo as $mgsuid=>$val){
											// 등급조건
											$arrCondition = array(); $printCondition = '';
											if($val['condition_totprice'] > 0){ $arrCondition[] = number_format($val['condition_totprice']).'원 이상 구매시 '; }
											if($val['condition_totcnt'] > 0){ $arrCondition[] = number_format($val['condition_totcnt']).'회 이상 구매'; }
											if(count($arrCondition) > 0){ $printCondition = implode("<br>",$arrCondition); }
											else{ $printCondition = '제한없음'; }

											// 등급혜택
											$arrBoon = array(); $printBoon = '';
											if($val['give_point_per'] > 0){ $arrBoon[] = odt_number_format($val['give_point_per'],1).'% 적립'; }
											if($val['sale_price_per'] > 0){ $arrBoon[] = odt_number_format($val['sale_price_per'],1).'% 할인'; }
											if(count($arrBoon) > 0){ $printBoon = implode("<br>",$arrBoon); }
											else{ $printBoon = '없음'; }
									?>
									<!-- 한 등급당 반복구간 -->
									<ul <?php echo $mgsuid == $mem_info['in_mgsuid']  ? ' class="hit" ': null?>><!-- 자신의 등급에 표기 -->
										<li class="opt"><?php echo $val['name']; ?></li>
										<li class="condi"><?php echo $printCondition; ?></li>
										<li class="bene"><?php echo $printBoon; ?></li>
									</ul>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<script>
						$(document).on('click','.js_level_btn',function(){
							var targetClass = '.js_level_stage'; // 클릭 시 타겟이 되는 클래스 (css 선택자 지정할때처럼 선택 지정자)
							var addClassName = 'if_level_open'; // 클릭 시 추가되는 클래스 (명만 써주시면됩니다.)
							var chk = $(targetClass).hasClass(addClassName);
							if( chk == false){ $(targetClass).addClass(addClassName); }
							else {  $(targetClass).removeClass(addClassName);  }
						});
					</script>
					<!-- / 등급별 혜택정보 -->
					<?php // {{{회원등급추가}}}   ?>


				</div>
				<!--거래잔액-->
				<div class="mypage_balance">
					<div class="balance_txt-box">
						<img class="balance_img" src="<?php echo $SkinData['skin_url']; ?>/images/skin/icon_balance.png" alt="거래잔액 지갑 아이콘">
						<p class="balance_tit">거래잔액</p>
						<!--수정 전에는 거래 잔액이 없었는데 추가되어 기능 없이 가격만 넣었습니다-->
						<p class="balance_num">467,000<span>원</span></p>
					</div>

				</div>
				<!-- 기본정보 -->
				<div class="default_info hide">
					<div class="top_tit">
						<span class="tit">기본 정보</span>
						<a href="/?pn=mypage.modify.form" class="btn">정보수정</a>
					</div>
					<!-- 정보 없을때 txt클래스에 '등록된 내용이 없습니다.' 문구 표기 -->
					<div class="info_box">
						<?php
							if($mem_info['in_tel2']){
								// 전화번호 부분 감추기
								$ex_hp = explode('-', tel_format($mem_info['in_tel2']));
								foreach($ex_hp as $k=>$v){
									if($k>0) $ex_hp[$k] = LastCut($v, (strlen($v)-2));
								}
								$private_hp = implode('-', $ex_hp);
						?>
							<div class="txt_box"><span class="sub_tit">휴대폰</span><span class="txt"><?php echo $private_hp; ?></span></div>
						<?php } ?>
						<?php if($mem_info['in_email']){ ?>
							<div class="txt_box"><span class="sub_tit">이메일</span><span class="txt"><a href="mailto:<?php echo $mem_info['in_email']; ?>" class="mail"><?php echo $mem_info['in_email']; ?></a></span></div>
						<?php } ?>
						<?php if($mem_info['in_address_doro']){ ?>
							<div class="txt_box"><span class="sub_tit">배송지</span><span class="txt"><?php echo $mem_info['in_address_doro']; ?> ****</span></div>
						<?php }else if($mem_info['in_address1']){ ?>
							<div class="txt_box"><span class="sub_tit">배송지</span><span class="txt"><?php echo $mem_info['in_address1']; ?> ****</span></div>
						<?php } ?>
					</div>
				</div>
				<!-- 쇼핑정보 -->
				<div class="shop_info hide">
					<div class="top_tit">
						<span class="tit">쇼핑 정보</span>
						<a href="/?pn=mypage.inquiry.list" class="btn">1:1 온라인 문의</a>
					</div>
					<!-- 내역 없을때 0으로 표기 -->
					<div class="info_box">
						<div class="txt_box"><span class="sub_tit le_2">주문</span><span class="txt">진행중인 주문 <strong><?php echo number_format(get_order_ing_cnt(array('결제대기', '결제완료', '배송대기', '배송중'))); ?>건</strong></span></div>
						<div class="txt_box"><span class="sub_tit">적립금</span><span class="txt"><strong><?php echo number_format($mem_info['in_point']); ?>원</strong></span></div>
						<div class="txt_box"><span class="sub_tit le_2">쿠폰</span><span class="txt"><strong><?php echo number_format(get_coupon_enable_cnt()); ?>장</strong></span></div>
					</div>
				</div>
			</div>


		<div class="c_mypage_total hide">
			<ul>
				<li>
					<div class="inner_box">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_wait.gif" alt="결제대기"></div>
						<div class="txt">결제대기</div>
						<!-- 내역 없으면 0으로 표기 -->
						<div class="total_num"><?php echo number_format($order_status['결제대기']); ?></div>
					</div>
				</li>
				<li>
					<div class="inner_box">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_complete.gif" alt="결제완료"></div>
						<div class="txt">결제완료</div>
						<div class="total_num"><?php echo number_format($order_status['결제완료']); ?></div>
					</div>
				</li>
				<li>
					<div class="inner_box">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_ing.gif" alt="배송중"></div>
						<div class="txt">배송중</div>
						<div class="total_num"><?php echo number_format($order_status['배송중']); ?></div>
					</div>
				</li>
				<li>
					<div class="inner_box">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_delivery.gif" alt="배송완료"></div>
						<div class="txt">배송완료</div>
						<div class="total_num"><?php echo number_format($order_status['배송완료']); ?></div>
					</div>
				</li>
				<li>
					<div class="inner_box">
						<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_cancel.gif" alt="주문취소"></div>
						<div class="txt">주문취소</div>
						<div class="total_num"><?php echo number_format($order_status['주문취소']); ?></div>
					</div>
				</li>
			</ul>
		</div>
		<!-- / 마이페이지 주문통계 -->




		<!-- ◆마이페이지 기간검색 -->
		<div class="mypage-lately_tit">
			<h3>주문취소/반품/교환</h3>
		</div>
		<form name="od_search" method="get">
		<input type="hidden" name="pn" value="mypage.order.list">
			<div class="order_search">
				<ul>
					<!-- 기간선택 -->
					<li class="lately_date-form"><h3>조회기간</h3></li>
					<li class="period">
						<div class="period_box">
						<!-- 활성화시 a에 hit 클래스 추가 -->
						<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'all', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'all' ? ' hit' : null); ?>">전체</a>
						<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'today', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'today' ? ' hit' : null); ?>">오늘</a>
						<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'week', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'week' ? ' hit' : null); ?>">일주일</a>
						<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'month1', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'month1' ? ' hit' : null); ?>">1개월</a>
						<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'month3', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'month3' ? ' hit' : null); ?>">3개월</a>
						<!--3개월 코드로 복사했습니다-->
						<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'month3', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'month3' ? ' hit' : null); ?>">1년</a>
						</div>
					</li>
					<!-- 날짜선택 / 날짜선택시 달력 노출 -->
					<li class="date">
						<div class="input_box">
							<input type="text" name="o_rdate_start" class="input_design js_pic_day" value="<?php echo $o_rdate_start; ?>" style="width:120px">
							<span class="dash">~</span>
							<input type="text" name="o_rdate_end" class="input_design js_pic_day" value="<?php echo $o_rdate_end; ?>" style="width:120px">
						</div>
					</li>
					<!-- 주문상태선택 -->
					<li class="state_select hide">
						<select name="o_status">
							<option value="">전체상태</option>
							<option value="결제대기" <?php echo ($o_status == '결제대기' ? 'selected' : null); ?>>결제대기</option>
							<option value="결제완료" <?php echo ($o_status == '결제완료' ? 'selected' : null); ?>>결제완료</option>
							<option value="배송준비" <?php echo ($o_status == '배송준비' ? 'selected' : null); ?>>배송준비</option>
							<option value="배송중" <?php echo ($o_status == '배송중' ? 'selected' : null); ?>>배송중</option>
							<option value="배송완료" <?php echo ($o_status == '배송완료' ? 'selected' : null); ?>>배송완료</option>
							<option value="주문취소" <?php echo ($o_status == '주문취소' ? 'selected' : null); ?>>주문취소</option>
						</select>
					</li>
					<li class="search_btn"><a href="#none" onclick="document.od_search.submit();" class="btn"><span class="txt">조회</span></a></li>
				</ul>
			</div>
		</form>
		<!-- / 마이페이지 기간검색 -->




		<!-- ◆마이페이지 주문내역 -->
		<div class="c_order_list">
					<p class="c_order-tit">주문목록 / 배송조회 내역 총 <span>0</span>건</p>
					<!--?php
						// 주문내역이 있을때
						if(count($res) > 0 ) {
					?-->
					<table class="order_table">
						<thead>
							<tr class="order_tr">
								<th>날짜/주문번호</th>
								<th>상품명/옵션</th>
								<th>상품금액/수량</th>
								<th>주문상태</th>
								<th>처리시간</th>
								<th>확인/리뷰</th>
							</tr>
						</thead>
						<tbody>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tbody>
					</table>
					<!--?php
						}
					?-->

					<!--?php
						# 내용 없을때 table 없어지고 노출
						if(count($res) < 1 ) {
					?-->
							<div class="c_none"><span class="none_none">조회내역이 없습니다.</span></div>
					<!--?php
						}
					?-->
					<div class="more_btn hide"><a href="/?pn=mypage.order.list" class="btn"><span class="txt">전체 주문내역 보기</span></a></div>
				</div>
				<!-- /마이페이지 주문내역 -->

		<!-- / 마이페이지 주문내역 -->
		</div>
		
		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi c_pagi-mid-blue">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
		</div>
</div>
					</div>
					</div>




<?php
	# 가상계좌 주문취소일 경우 환불계정 레이아웃 미리 생성
	include_once($SkinData['skin_root'].'/mypage.order.pro.cancel_virtual.php');
?>



<script id="mypage_order_list">

	// 주문취소
	var cancel_trigger = true; // SSJ : 중복취소 방지 : 2021-12-31
	function order_cancel(ordernum){
		if(ordernum == '' || ordernum == undefined){
			alert('잘못된 접근입니다.');
			return false;
		}

		// SSJ : 중복취소 방지 : 2021-12-31
		if(cancel_trigger === false){
			alert('주문 취소를 진행중입니다. 잠시만 기다려 주시기 바랍니다.');
			return false;
		}

		if( confirm('정말 주문을 취소하시겠습니까?') == true ) {
			cancel_trigger = false; // SSJ : 중복취소 방지 : 2021-12-31
			common_frame.location.href=("<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php?_mode=cancel&ordernum=" + ordernum + "&_PVSC=<?php echo $_PVSC; ?>");
		}

	}

	// 가상계좌/무통장 주문취소
	function order_cancel_virtual(ordernum, price){
		// 콤마추가
		price = (price + '').comma();

		// 데이터 입력
		$('.cancel_virtual').find('input[name=ordernum]').val(ordernum);
		$('.cancel_virtual').find('.js_data_ordernum').text(ordernum);
		$('.cancel_virtual').find('.js_data_price').text(price);

		$('.cancel_virtual').lightbox_me({
			centered: true,
			closeEsc: false,
			onLoad: function() {
				$('.cancel_virtual').find('input:first').focus();
			},
			onClose: function() {
				// 데이터 삭제
				$('.cancel_virtual').find('input[name=ordernum]').val('');
				$('.cancel_virtual').find('.js_data_ordernum').text('');
				$('.cancel_virtual').find('.js_data_price').text('');
			}
		});
	}

</script>