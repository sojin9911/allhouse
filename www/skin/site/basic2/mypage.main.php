<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage_main">
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
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->




		<div class="mypage_section">
			<div class="left_sec">
				<!-- ◆공통탭메뉴 -->
				<?php
					// PC 탑 네비
					include_once($SkinData['skin_root'].'/member.header.php');
				?>
				<!-- / 공통탭메뉴 -->
			</div>




			<div class="right_sec">
				<div class="right_sec_wrap">
					<!-- ◆마이페이지 상단 기본,쇼핑정보 -->
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
        						<p class="balance_num"><?=number_format($mem_info['in_point'])?><span>원</span></p>
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
					<!-- / 마이페이지 상단 기본,쇼핑정보 -->





					<!-- ◆마이페이지 주문통계 -->
					<div class="c_mypage_total">
						<p class="mypage_div-tit">진행중인 주문 <span>최근 30일 내에 진행중인 주문정보입니다.</span></p>
						<ul class="total_big-ul">
							<li>
								<div class="inner_box">
									<div class="icon hide"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_wait.gif" alt="결제대기"></div>
									<div class="txt">접수대기</div>
									<!-- 내역 없으면 0으로 표기 -->
									<div class="total_num"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_list_bg.png" alt="숫자 밑의 장식용 동그라미"><p><?php echo number_format($order_status['결제대기']); ?></p></div>
									<div class="total_arrow"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_next_bg" alt="오른쪽 화살표"></div>
								</div>
							</li>
							<li>
								<div class="inner_box">
									<div class="icon hide"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_complete.gif" alt="결제완료"></div>
									<div class="txt">접수완료</div>
									<div class="total_num"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_list_bg.png" alt="숫자 밑의 장식용 동그라미"><p><?php echo number_format($order_status['결제완료']); ?></p></div>
									<div class="total_arrow"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_next_bg" alt="오른쪽 화살표"></div>
								</div>
							</li>
							<!--사입진행중과 입고보관중의 기능은 결제완료로 뒀습니다-->
							<li>
								<div class="inner_box">
									<div class="icon hide"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_complete.gif" alt="결제완료"></div>
									<div class="txt">사입진행중</div>
									<div class="total_num"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_list_bg.png" alt="숫자 밑의 장식용 동그라미"><p><?php echo number_format($order_status['결제완료']); ?></p></div>
									<div class="total_arrow"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_next_bg" alt="오른쪽 화살표"></div>
								</div>
							</li>
							<li>
								<div class="inner_box">
									<div class="icon hide"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_complete.gif" alt="결제완료"></div>
									<div class="txt">입고보관중</div>
									<div class="total_num"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_list_bg.png" alt="숫자 밑의 장식용 동그라미"><p><?php echo number_format($order_status['결제완료']); ?></p></div>
									<div class="total_arrow"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_next_bg" alt="오른쪽 화살표"></div>
								</div>
							</li>
							<li>
								<div class="inner_box">
									<div class="icon hide"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_ing.gif" alt="배송중"></div>
									<div class="txt">배송중</div>
									<div class="total_num"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_list_bg.png" alt="숫자 밑의 장식용 동그라미"><p><?php echo number_format($order_status['배송중']); ?></p></div>
									<div class="total_arrow"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_next_bg" alt="오른쪽 화살표"></div>
								</div>
							</li>
							<li>
								<div class="inner_box last-child_transform">
									<div class="icon hide"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_delivery.gif" alt="배송완료"></div>
									<div class="txt">배송완료</div>
									<div class="total_num"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_list_bg.png" alt="숫자 밑의 장식용 동그라미"><p><?php echo number_format($order_status['배송완료']); ?></p></div>
								</div>
							</li>
							<!--
							<li>
								<div class="inner_box">
									<div class="icon hide"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/my_total_cancel.gif" alt="주문취소"></div>
									<div class="txt">주문취소</div>
									<div class="total_num"><?php echo number_format($order_status['주문취소']); ?></div>
									<div class="total_arrow"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/mypgae_ing_next_bg" alt="오른쪽 화살표"></div>
								</div>
							</li>
							-->
							<!--주문취소 함수는 있는데 교환 반품 함수는 없는 것 같아 주문취소만 넣었습니다-->
							<li>
								<ul class="total_last-child">
									<li><span class="dot"></span> 취소 <span><?php echo number_format($order_status['주문취소']); ?>건</span></li>
									<li><span class="dot"></span> 교환 <span>0건</span></li>
									<li><span class="dot"></span> 반품 <span>0건</span></li>
								</ul>
							</li>
						</ul>
					</div>
					<!-- / 마이페이지 주문통계 -->




					<!-- ◆마이페이지 주문내역 / 5개 노출 -->
					<div class="c_order_list">
						<p class="mypage_div-tit">최근 주문 정보 <span>최근 30일 내에 진행중인 주문하신 내역입니다.</span></p>
						<?php
							// 주문내역이 있을때
							if(count($res) > 0 ) {
						?>
						<table class="order_table">
							<colgroup>
								<col style="width:15%">
								<!--날짜/주문번호-->
								<col style="width:40%">
								<!--상품명/옵션-->
								<col style="width:30%">
								<!--상품금액/수량-->
								<col style="width:15%">
								<!--확인/리뷰-->
							</colgroup>
							<thead>
								<tr class="order_tr">
									<th>날짜/주문번호</th>
									<th>상품명</th>
									<th>상품수량</th>
									<th>확인/리뷰</th>
								</tr>
							</thead>
							<tbody>
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
																	unset($app_btn_cancel);   // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
																	if($v['o_canceled'] == "N" && $v['npay_order'] != 'Y'  ) {
											if( in_array($v['o_status'] , array('결제대기','결제완료','결제대기')) ){

												if($v['o_status']!='결제대기'&&($v['o_paymethod']=='virtual'||$v['o_paymethod']=='online')) { // 주문상태가 결제대기가 아니고, 결제방법이 가상,무통장 인건만
													$cancel_function = 'order_cancel_virtual(\''.$v['o_ordernum'].'\', \''.$v['o_price_real'].'\')'; // 가상계좌
												}else {
													$cancel_function = 'order_cancel(\''.$v['o_ordernum'].'\')'; // 일반
												}

												// 주문취소 생성
												$app_btn_cancel = '<div class="order_cancel"><a href="#none" onclick="'. $cancel_function .'" class="c_btn h22 light line">주문취소</a></div>';

												// 상품이 /취소/반품/교환 요청중인 상품 검사
												$chk_part_cancel = _MQ_result(" select count(*) from smart_order_product where op_oordernum = '".$v['o_ordernum']."' and op_cancel != 'N' ");
												if( $chk_part_cancel > 0){
													$app_btn_cancel = "<div class='order_cancel'><a href='#none' onclick='alert(\"취소/반품/교환 요청중인 상품이 있습니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h22 light line'>주문취소</a></div>" ;
												}
											}
											else {
												$app_btn_cancel = "<div class='order_cancel'><a href='#none' onclick='alert(\"주문취소가 불가능한 상태입니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h22 light line'>주문취소</a></div>" ;
											}
										}


										# 주문상태
										unset($o_status_print);
										switch($v['o_status']){
											case '배송대기':
											case '결제완료':
												$o_status_print = '<span class="icon complete">결제완료</span>';
											break;

											case '결제대기':
												$o_status_print = '<span class="icon wait">결제대기</span>';
											break;

											case '배송준비':
												$o_status_print = '<span class="icon ing">배송준비</span>';
											break;

											case '배송완료':
												$o_status_print = '<span class="icon delivery">배송완료</span>';
											break;

											case '배송중':
												$o_status_print = '<span class="icon ing">배송중</span>';
											break;

											case '주문취소':
												$o_status_print = '<span class="icon cancel">주문취소</span>';
											break;
										}

										# 배송조회
										$delivery_print = ''; $arr_sendnum = array();
										if(count($app_product_list)>0){
											foreach($app_product_list as $sk=>$sv){
												if($sv['op_sendstatus']=='배송중' && $sv['op_sendcompany'] && $sv['op_sendnum']){
													if($arr_sendnum[$sv['op_sendnum']] > 0) continue; // 중복제거
													$arr_sendnum[$sv['op_sendnum']]++;
													$delivery_print .= '
														<div class="delivery_num">
															<a href="'. ($v['npay_order'] == 'Y' ?($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]): $arr_delivery_company[$sv['op_sendcompany']]) . rm_str($sv['op_sendnum']) .'" class="num_box" target="_blank"><span class="txt tit">'. $sv['op_sendcompany'] .'</span><span class="txt">'. $sv['op_sendnum'] .'</span></a>
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
										<!-- 주문취소 상품일 경우 tr에 if_cancel 클래스 추가 -->
										<tr<?php echo ($v['o_status'] == '주문취소' ? ' class="if_cancel" ' : null) ?>>
											<td class="tbody_border-right">
												<!--날짜-->
												<p class="order_date"><?php echo date('Y/m/d',strtotime($v['o_rdate'])); ?></p>
												<!--주문번호-->
												<p class="order_number"><a href="#"><?php echo $v['o_ordernum']; ?></a></p>
											</td>
											<td class="thumb_box">
												<div class="thumb_inner">
													<a href="<?php echo $order_view_url; ?>" class="thumb" title="상세보기"><img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($app_product_name); ?>"></a>
													<div class="name"><a href="<?php echo $order_view_url; ?>" class="tit" title="상세보기"><?php echo $app_product_name; ?></a></div>
												</div>

											</td>
											<td style="padding-left:14px;"><!--상품수량이 들어가야합니다-->1</td>
											<td>
												<div class="info hide">
													<!-- 주문일자 -->
													<div class="date"><?php echo date('Y-m-d',strtotime($v['o_rdate'])); ?>
																									<?php
																											// ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: 네이버페이 아이콘 표기  --
																											if( $v['npay_order'] == 'Y'){
																													echo '<span class="nv_icon" style="color: #fff !important; border-radius: 2px; opacity: 0.7; background: #01c73c;padding: 0 5px 0 3px;height: 16px;line-height: 15px;font-weight: 400 !important;">네이버페이</span>';
																											}
																									?>
													</div>
													<!-- 상품명 -->
													<div class="name"><a href="<?php echo $order_view_url; ?>" class="tit" title="상세보기"><?php echo $app_product_name; ?></a></div>
													<!-- 주문상태 -->
													<div class="state_icon hide">
														<?php echo $o_status_print; ?>

														<!-- 택배사,송장번호 / 주문 배송중 상태일경우 노출 -->
														<?php echo $delivery_print; ?>
													</div>
												</div>
											</td>
											<td>
												<div class="price_info hide">
													<!-- 총 주문금액 -->
													<div class="price"><?php echo number_format($v['o_price_real']); ?>원</div>
													<!-- 주문번호 -->
													<div class="order_num"><?php echo $v['o_ordernum']; ?></div>
													<!-- 주문취소 -->
													<?php echo $app_btn_cancel; ?>
												</div>
											</td>
											<td>
												<a href="<?php echo $order_view_url; ?>" class="arrow hide" title="상세보기"><span class="icon"></span></a>
											</td>
										</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php
							}
						?>

						<?php
							# 내용 없을때 table 없어지고 노출
							if(count($res) < 1 ) {
						?>
								<div class="c_none"><span class="none_none">조회내역이 없습니다.</span></div>
						<?php
							}
						?>
						<div class="more_btn hide"><a href="/?pn=mypage.order.list" class="btn"><span class="txt">전체 주문내역 보기</span></a></div>
					</div>
					<!-- /마이페이지 주문내역 -->





					<!-- ◆마이페이지 나의찜한상품 / 6개 노출 -->
					<!--원래 찜한 상품인데 최근 본 상품으로 변경했습니다-->
					<!--올하우스 원래 페이지에 최근본상품이 뜰 때 경우가 안나와서 상품 찜했을때 부분도 수정없이 그대로 두고-->
					<!--찜한 상품 없을 때 부분만 수정했습니다-->
					<div class="c_wish_list">
						<div class="wish_tit">
							<p class="mypage_div-tit">최근 본 상품 <span><?php echo $mem_info['in_name']; ?>님께서 본 최근 상품입니다.</span></p>
							<span class="tit hide">찜한 상품</span>
							<!-- 찜한상품 없을때 0개 -->
							<span class="total hide">총 <?php echo number_format(get_wish_cnt()); ?>개</span>
							<!-- 찜한상품 페이지로 이동 -->
							<a href="/?pn=mypage.wish.list" class="more hide">더보기</a>
						</div>

						<div class="wish_item">
							<?php if(count($myWishList) > 0){ ?>
								<ul>
									<?php
										foreach($myWishList as $k=>$v){
											$_img = get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT);
									?>
											<li>
												<div class="wish_box">
													<div class="item">
														<a href="/?pn=product.view&pcode=<?php echo $v['p_code']; ?>" class="upper_link" title="<?php echo addslashes($v['p_name']); ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/blank.gif" alt=""></a>
														<!-- 상품이미지 338 * 338 -->
														<div class="thumb">
															<?php if($_img) { ?>
																<div class="real_img"><img src="<?php echo $_img; ?>" alt="<?php echo addslashes($v['p_name']); ?>" /></div>
															<?php } ?>
															<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($v['p_name']); ?>" /></div>

															<?php if($v['p_stock'] <= 0) { ?>
																<!-- 솔드아웃일 경우 item_quick 삭제 -->
																<div class="soldout"><span class="inner">SOLD OUT</span></div>
															<?php } ?>
														</div>
														<!-- 상품정보 -->
														<div class="info">
															<div class="item_name"><?php echo stripslashes($v['p_name']); ?></div>

															<div class="price">
																<div class="after"><span class="won"><?php echo number_format($v['p_price']); ?></span>원</div>
															</div>
														</div>
													</div>
												</div>
											</li>
									<?php } ?>
								</ul>
							<?php } ?>


							<?php if(count($myWishList) < 1){ ?>
								<!-- 내용 없을때 ul 없어지고 노출 -->
								<!--원래 찜한 상품인데 최근 본 상품으로 변경되어 기능수정없이 모양만 만들었습니다-->
								<div class="none_box"><span class="none_none">상품이 존재하지 않습니다</span></div>
							<?php } ?>
						</div>
					</div>
					<!-- / 마이페이지 나의찜한상품 -->
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