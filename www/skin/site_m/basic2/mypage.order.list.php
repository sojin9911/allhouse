<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "주문내역";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력

?>


<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">


	<!-- ◆마이페이지 주문통계 -->
	<div class="c_mypage_total">
		<ul>
			<li>
				<div class="inner_box">
					<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_wait.png" alt="결제대기"></div>
					<div class="txt">결제대기</div>
					<!-- 내역 없으면 0으로 표기 -->
					<div class="total_num"><?php echo number_format($order_status['결제대기']); ?></div>
				</div>
			</li>
			<li>
				<div class="inner_box">
					<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_complete.png" alt="결제완료"></div>
					<div class="txt">결제완료</div>
					<div class="total_num"><?php echo number_format($order_status['결제완료']); ?></div>
				</div>
			</li>
			<li>
				<div class="inner_box">
					<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_ing.png" alt="배송중"></div>
					<div class="txt">배송중</div>
					<div class="total_num"><?php echo number_format($order_status['배송중']); ?></div>
				</div>
			</li>
			<li>
				<div class="inner_box">
					<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_delivery.png" alt="배송완료"></div>
					<div class="txt">배송완료</div>
					<div class="total_num"><?php echo number_format($order_status['배송완료']); ?></div>
				</div>
			</li>
			<li>
				<div class="inner_box">
					<div class="icon"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_cancel.png" alt="주문취소"></div>
					<div class="txt">주문취소</div>
					<div class="total_num"><?php echo number_format($order_status['주문취소']); ?></div>
				</div>
			</li>
		</ul>
	</div>
	<!-- / 마이페이지 주문통계 -->




	<!-- ◆마이페이지 기간검색 -->
	<form name="od_search" method="get">
	<input type="hidden" name="pn" value="mypage.order.list">
		<div class="order_search">
			<!-- 기간선택 -->
			<div class="period_box">
				<!-- 활성화시 a에 hit 클래스 추가 -->
				<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'all', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'all' ? ' hit' : null); ?>">전체</a>
				<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'today', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'today' ? ' hit' : null); ?>">오늘</a>
				<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'week', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'week' ? ' hit' : null); ?>">일주일</a>
				<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'month1', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'month1' ? ' hit' : null); ?>">1개월</a>
				<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'month3', 'o_status'=>$o_status)); ?>" class="btn<?php echo ($date == 'month3' ? ' hit' : null); ?>">3개월</a>
			</div>

			<ul>
				<!-- 날짜선택 / 날짜선택시 달력 노출 -->
				<li class="date">
					<div class="input_box">
						<input type="text" name="o_rdate_start" class="input_design js_pic_day" value="<?php echo $o_rdate_start; ?>" readonly>
						<span class="dash">~</span>
						<input type="text" name="o_rdate_end" class="input_design js_pic_day" value="<?php echo $o_rdate_end; ?>" readonly>
					</div>
				</li>
				<!-- 주문상태선택 -->
				<li class="state">
					<div class="select">
						<select name="o_status">
							<option value="">전체상태</option>
							<option value="결제대기" <?php echo ($o_status == '결제대기' ? 'selected' : null); ?>>결제대기</option>
							<option value="결제완료" <?php echo ($o_status == '결제완료' ? 'selected' : null); ?>>결제완료</option>
							<option value="배송준비" <?php echo ($o_status == '배송준비' ? 'selected' : null); ?>>배송준비</option>
							<option value="배송중" <?php echo ($o_status == '배송중' ? 'selected' : null); ?>>배송중</option>
							<option value="배송완료" <?php echo ($o_status == '배송완료' ? 'selected' : null); ?>>배송완료</option>
							<option value="주문취소" <?php echo ($o_status == '주문취소' ? 'selected' : null); ?>>주문취소</option>
						</select>
					</div>
				</li>
				<li class="this_btn"><a href="#none" onclick="document.od_search.submit();" class="btn_search"><span class="txt">조회하기</span></a></li>
			</ul>
		</div>
	</form>
	<!-- / 마이페이지 기간검색 -->




	<!-- ◆마이페이지 주문내역 -->
	<div class="c_order_list">
		<?php
			// 주문내역이 있을때
			if(count($res) > 0 ) {
		?>
			<ul>
				<?php
					foreach($res as $k=>$v){
						# 상품별 정보를 가져온다
						$app_product_list = array();
						$app_product_list = _MQ_assoc("
							select op.op_pname, p.p_img_list_square, op.*, sum( op_cnt * (op_price) ) as op_tPrice
							from smart_order_product as op
							left join smart_product as p on (p.p_code=op.op_pcode) where op_oordernum = '".$v['o_ordernum']."' group by op_pcode order by op_uid asc
						");
						$app_product_name = $app_product_list[0]['op_pname'];
						if( count($app_product_list)>1 ) { $app_product_name .= ' 외 '.(count($app_product_list)-1).'개 '; }

						# 주문 상세보기 URL
						$order_view_url = '/' . URI_Rebuild('?', array('pn'=>'mypage.order.view', 'ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC));

						# 상품 이미지
						$thumb_img	= get_img_src('thumbs_s_'.$app_product_list[0]['p_img_list_square']);
						if($thumb_img=='') $thumb_img = $SkinData['skin_url']. '/images/skin/thumb.gif';


                        # 주문 상태에 따른 취소 버튼
                        unset($app_btn_cancel);
                        if($v['o_canceled'] == "N"  && $v['npay_order'] != 'Y'  ) {  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
							if( in_array($v['o_status'] , array('결제대기','결제완료','배송대기')) ){

								if($v['o_status']!='결제대기'&&in_array($v['o_paymethod'], $arr_refund_payment_type)) { // SSJ : 주문/결제 통합 패치 : 2021-02-24
									$cancel_function = 'order_cancel_virtual(\''.$v['o_ordernum'].'\', \''.$v['o_price_real'].'\')'; // 가상계좌
								}else {
									$cancel_function = 'order_cancel(\''.$v['o_ordernum'].'\')'; // 일반
								}

								// 주문취소 생성
								$app_btn_cancel = '<div class="order_cancel"><a href="#none" onclick="'. $cancel_function .'" class="c_btn h22 light line">주문취소</a></div>';

								// 상품이 /취소/반품/교환 요청중인 상품 검사
								$chk_part_cancel = _MQ_result(" select count(*) from smart_order_product where op_oordernum = '".$v['o_ordernum']."' and op_is_addoption = 'N' and op_cancel != 'N' ");
								if( $chk_part_cancel > 0){
									$app_btn_cancel = "<div class='order_cancel'><a href='#none' onclick='alert(\"취소/반품/교환 요청중인 상품이 있습니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h22 light line'>주문취소</a></div>" ;
								}
							}
							else {
								$app_btn_cancel = "<div class='order_cancel'><a href='#none' onclick='alert(\"주문취소가 불가능한 상태입니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h22 light line'>주문취소</a></div>" ;
							}
						}


						# 주문상태 // <!-- 각주문 단계별 클래스 추가해주세요 : if_wait / if_complete / if_ing / if_delivery / if_cancel -->
						unset($o_status_print, $class_ostatus);
						switch($v['o_status']){
							case '배송대기':
							case '결제완료':
								$o_status_print = '<span class="icon complete">결제완료</span>';
								$class_ostatus = 'if_complete';
							break;

							case '결제대기':
								$o_status_print = '<span class="icon wait">결제대기</span>';
								$class_ostatus = 'if_wait';
							break;

							case '배송준비':
								$o_status_print = '<span class="icon ing">배송준비</span>';
								$class_ostatus = 'if_ing';
							break;

							case '배송완료':
								$o_status_print = '<span class="icon delivery">배송완료</span>';
								$class_ostatus = 'if_delivery';
							break;

							case '배송중':
								$o_status_print = '<span class="icon ing">배송중</span>';
								$class_ostatus = 'if_ing';
							break;

							case '주문취소':
								$o_status_print = '<span class="icon cancel">주문취소</span>';
								$class_ostatus = 'if_cancel';
							break;

							case '환불요청':
								$o_status_print = '<span class="icon cancel">환불요청</span>';
							break;
						}

						# 배송조회
						$delivery_print = ''; $arr_sendnum = array();
						if(count($app_product_list)>0){
							foreach($app_product_list as $sk=>$sv){
								if(in_array($sv['op_sendstatus'], array('배송중','배송완료')) && $sv['op_sendcompany'] && $sv['op_sendnum']){
									if($arr_sendnum[$sv['op_sendnum']] > 0) continue; // 중복제거
									$arr_sendnum[$sv['op_sendnum']]++;
									$delivery_print .= '
										<div class="delivery_num">
											<a href="'. ($v['npay_order'] == 'Y' ? ($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]) : $arr_delivery_company[$sv['op_sendcompany']]) . rm_str($sv['op_sendnum']) .'" class="num_box" target="_blank"><span class="txt tit">'. $sv['op_sendcompany'] .'</span><span class="txt">'. $sv['op_sendnum'] .'</span></a>
										</div>
									';
								}
							}
						}

						/*****
						# 이미지 스크립트 효과
						unset($img_bxSlider);
						if(count($app_product_list)>1 ) {
						$img_bxSlider = "
							<script>
								$(window).on('load',function(){
									var mypage_main_product_slider_".$k." = $('.mypage_main_product_slider_".$k."').bxSlider({
										auto: true, autoHover: false, speed: 700, mode: 'fade',
										slideSelector: '', easing: 'easeInOutCubic', useCSS: false,
										slideMargin: 0, slideWidth: 0, minSlides: 1, maxSlides: 1,
										pager: false, controls: false,
										onSlideBefore: function() { mypage_main_product_slider_".$k.".stopAuto(); },
										onSlideAfter: function() { mypage_main_product_slider_".$k.".startAuto(); }
									});
								});
							</script>";
						}
						*****/
				?>
						<li class="<?php echo $class_ostatus; ?>">
							<dl>
								<dt>
									<div class="thumb_box">
										<a href="<?php echo $order_view_url; ?>" class="thumb" title="상세보기"><img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($app_product_name); ?>"></a>
										<!-- 주문상태 -->
										<div class="state_icon">
											<?php echo $o_status_print; ?>
										</div>
									</div>
								</dt>
								<dd>
									<div class="date"><?php echo date('Y-m-d',strtotime($v['o_rdate'])); ?>
                                        <?php if( $v['npay_order'] == 'Y'){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: 네이버페이의 경우 아이콘 표기  --   ?>
                                        <span class="nv_icon" style="color: #fff !important; border-radius: 2px; background: #01c73c;padding: 0 5px 0 3px;height: 16px;line-height: 15px;font-weight: 400 !important;  opacity: 0.7;">네이버페이</span>
                                        <?php } ?>
									</div>
									<div class="name"><a href="<?php echo $order_view_url; ?>" class="tit" title="상세보기"><?php echo $app_product_name; ?></a></div>
									<div class="order_num"><?php echo $v['o_ordernum']; ?></div>
									<div class="price"><?php echo number_format($v['o_price_real']); ?>원</div>
									<?php echo $delivery_print; ?>
									<?php echo $app_btn_cancel; ?>
								</dd>
							</dl>
							<a href="<?php echo $order_view_url; ?>" class="arrow" title="상세보기"><span class="icon"></span></a>
						</li>
				<?php } ?>
			</ul>
		<?php
			}
		?>

		<?php
			# 내용 없을때 table 없어지고 노출
			if(count($res) < 1 ) {
		?>
				<div class="c_none"><span class="gtxt">주문내역이 없습니다.</span></div>
		<?php
			}
		?>


	</div>
	<!-- /마이페이지 주문내역 -->




	<!-- 페이지네이트 (상품목록 형) -->
	<div class="c_pagi">
		<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
	</div>


</div>
<!-- /공통페이지 섹션 -->


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