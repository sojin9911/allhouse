<?php include_once(OD_PROGRAM_ROOT.'/product.top_nav.php'); // 상단 네비게이션 출력 ?>
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
<style>
.view_option {
    display:block;
}
.view_table-content {
    margin: 0 10px 0 0;
    color: #888888;
    font-weight: normal;
    word-wrap: break-word;
    display: table-cell;
    vertical-align: middle;
    padding: 7px 0px;
    min-height: 24px;
    font-size: 12px;
}
</style>

<div class="view_section">

    <!-- ◆ 상품상세 : 사진,기본정보 -->
    <div class="view_top">
        <div class="layout_fix">
            <ul class="ul">
                <li class="li view_photo" id="li_photo-box">


                    <!-- 상품 사진 -->
                    <div class="photo_box" id="photo-box">
                        <!-- 큰사진 롤링박스 -->
                        <div class="photo-box_resize">
                            <!-- 이 div 롤링 / 470 * 470 -->
                            <?php
                                if(count($pro_img)>0){
                                    foreach($pro_img as $k=>$v){
                                        $_pimg = get_img_src($v);
                            ?>
                                        <div class="thumb" style="<?php echo ($k>0?'display:none;':null); ?>">
                                            <?php if($_pimg){ ?><div class="real_img"><img src="<?php echo $_pimg; ?>" alt="<?php echo addslashes($pro_name); ?>"></div><?php } ?>
                                            <div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($pro_name); ?>"></div>
                                        </div>
                            <?php
                                    }
                                }else{
                            ?>
                                <div class="thumb">
                                    <div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($pro_name); ?>"></div>
                                </div>
                            <?php } ?>
                        </div>

                        <?php if(count($pro_img)>1){ ?>
                        <!-- 롤링사진 1개일 경우 숨김 -->
                        <div class="rolling_thumb display-none">
                            <ul class="js_photo_large_pager">
                                <!-- li 최대 5개 노출 / 활성화시 li에 hit클래스 추가 -->
                                <?php
                                    foreach($pro_img as $k=>$v){
                                        $_pimg = get_img_src('thumbs_s_' . $v);
                                        if($_pimg == '') $_pimg = $SkinData['skin_url'] . '/images/skin/thumb.gif';
                                ?>
                                        <li class="<?php echo ($k === 0?'hit':''); ?>" ><a href="#none;" onclick="return false;" data-slide-index="<?php echo $k; ?>" class="<?php echo ($k === 0?'active':''); ?>"><img src="<?php echo $_pimg; ?>" alt=""></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>

                    <?php if(sizeof($pro_img)>1) { ?>
                        <script>
                        $(window).on('load',function(){
                                $('.js_photo_large_slider').find('.thumb').show();
                                var photo_large = $('.js_photo_large_slider').bxSlider({
                                    auto: true,
                                    autoHover: false,
                                    pagerCustom: '.js_photo_large_pager',
                                    controls: false,
                                    maxSlides:1,
                                    moveSlides:1,
                                    slideMargin : 0,
                                    slideWidth: 472,
                                    onSliderLoad: function() { },
                                    onSlideBefore: function($slideElement, oldIndex, newIndex) {
                                        $('.js_photo_large_pager li').removeClass('hit');
                                        $('.js_photo_large_pager li a[data-slide-index='+newIndex+']').parent().addClass('hit');
                                        photo_large.stopAuto();
                                    },
                                    onSlideAfter: function($slideElement, oldIndex, newIndex) { photo_large.startAuto(); }
                                });
                            });
                        </script>
                    <?php } ?>



                    <!-- 상품평점/sns공유 -->
                    <div class="view_summery display-none">
                        <div class="score">
                            <!-- 상품후기 탭으로 이동 -->
                            <a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="upper_link" title=""><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif" alt=""></a>
                            <span class="mark"><span class="star" style="width:<?php echo $star_persent; ?>%"></span></span>
                            <span class="num">
                                <?php
                                    // <!-- 0과 10 제외하고 소수점 한자리까지 노출 -->
                                    if($star_persent == 0 || $star_persent == 100)
                                        echo number_format($star_persent/10,0);
                                    else
                                        echo number_format($star_persent/10,1);
                                ?>
                            </span>
                            <span class="total">(<?php echo $eval_cnt; ?>건)</span>
                        </div>

                        <?php
                        $SNSSendUse = array($siteInfo['facebook_share_use'], $siteInfo['kakao_share_use'], $siteInfo['twitter_share_use'], $siteInfo['pinter_share_use']);
                        if(in_array('Y', $SNSSendUse)) {
                        ?>
                            <div class="sns">
                                <ul>
                                    <?php if($siteInfo['kakao_share_use'] == 'Y' && $siteInfo['kakao_js_api'] != '' ) { ?>
                                        <li>
                                            <a href="#none" onclick="sendSNS('kakao'); return false;" class="btn" title="카카오톡 공유하기">
                                                <img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_kakao.png" class="on" alt="카카오톡 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_kakao_ov.png" class="ov" alt="카카오톡 공유하기">
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if($siteInfo['facebook_share_use'] == 'Y' && $siteInfo['s_facebook_key'] != '' ) { ?>
                                        <li>
                                            <a href="#none" onclick="sendSNS('facebook'); return false;" class="btn" title="페이스북 공유하기">
                                                <img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_face.png" class="on" alt="페이스북 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_face_ov.png" class="ov" alt="페이스북 공유하기">
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if($siteInfo['twitter_share_use'] == 'Y') { ?>
                                        <li>
                                            <a href="#none" onclick="sendSNS('twitter'); return false;" class="btn" title="트위터 공유하기">
                                                <img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_twitt.png" class="on" alt="트위터 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_twitt_ov.png" class="ov" alt="트위터 공유하기">
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if($siteInfo['pinter_share_use'] == 'Y') { ?>
                                        <li>
                                            <a href="#none" onclick="sendSNS('pinterest'); return false;" class="btn" title="핀터레스트 공유하기">
                                                <img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_pin.png" class="on" alt="핀터레스트 공유하기"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/ic_pin_ov.png" class="ov" alt="핀터레스트 공유하기">
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>


                    <?php if(count($pro_hashtag)>0){ ?>
                    <!-- 해시태그 -->
                    <div class="view_hash">
                        <ul>
                            <li class="title">이 상품의<br>관련 태그</li>
                            <li>
                                <?php foreach($pro_hashtag as $k=>$v){ ?>
                                    <a href="/?pn=product.search.list&search_word=%23<?php echo urlencode(trim($v)); ?>" class="btn" target="_blank">#<?php echo trim($v); ?></a>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                    <?php } ?>


                </li>

                <?php
                    // 2018-07-16 SSJ :: 배송정보에서 노출되도록 위치이동
                    // 배송비 <!-- 기본 <em>2,500</em>원 (<em>30,000</em>원 이상 무료) --><!-- 무료배송일경우, 아이콘없으면 텍스트로 노출 -->
                    switch($p_info['p_shoppingPay_use']){
                        case 'Y': $pro_delivery = '개별배송 <em>' . number_format($pro_delivery_info['price']) . '</em>원'; break;
                        case 'N':
                            $pro_delivery = '기본 <em>' . number_format($pro_delivery_info['price']) . '</em>원';
                            if($pro_delivery_info['freePrice'] > 0) $pro_delivery .= ' (<em>'.number_format($pro_delivery_info['freePrice']) . '</em>원 이상 무료)';
                            break;
                        case 'F': $pro_delivery = '무료배송'; break; //무료배송 // SSJ :: 무료배송 아이콘대신 문구로 노출 ---- 2020-02-05
                        case 'P': $pro_delivery = '상품별배송 <em>' . number_format($pro_delivery_info['price']) . '</em>원'. ($pro_delivery_info['freePrice'] > 0 ? ' (<em>'.number_format($pro_delivery_info['freePrice']) . '</em>원 이상 무료)' : null); break; // 상품별 // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
                    }
                ?>
                <li class="li view_info">


                    <!-- 상품이름/설명/아이콘 -->
                    <div class="view_name">
                        <?php echo ($app_pro_icon ? '<span class="upper_icon">'.$app_pro_icon.'</span>' : ''); ?>
                        <div class="title view-name_title"><?php echo $pro_name; ?></div>
                        <?php if($pro_subname){ ?>
                        <div class="sub_name view-name_sub"><?php echo $pro_subname; ?></div>
                        <?php } ?>
                    </div>




                    <?php if(count($ex_display_pc) > 0){ ?>
                    <!-- 상품기본정보 -->
                    <div class="view_default">
                        <?php foreach($ex_display_pc as $k=>$v){ ?>

                            <?php if($pro_screenprice && $v == 'screenPrice'){ ?>
                            <dl>
                                <dt class="view_table-txt">정가</dt>
                                <dd class="view_table-txt"><span class="before_price"><strong><?php echo $pro_screenprice; ?></strong>원</span></dd>
                            </dl>
                            <?php } ?>

                            <?php if($v == 'price'){ ?>
                            <dl>
                                <dt class="view_table-txt">판매가</dt>
                                <dd class="view_table-txt"><span class="after_price"><strong><?php echo $pro_price; ?></strong>원</span>
                                    <?php // {{{회원등급혜택 ?>
                                    <?php if( $groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) { ?>
                                    <span class="point_plus">
                                        <span class="txt">회원할인</span> <strong><?=odt_number_format($groupSetInfo['mgs_sale_price_per'],1)?>%</strong>
                                    </span>
                                    <?php } ?>
                                    <?php // {{{회원등급혜택 ?>
                                </dd>
                            </dl>
                            <?php } ?>

                            <?php if($pro_point && $v == 'point'){ ?>
                            <dl>
                                <dt class="view_table-txt"><span class="tit">적립금</span></dt>
                                <dd class="view_table-txt"><span class="point"><strong><?php echo $pro_point; ?></strong>원</span>
                                    <?php // {{{회원등급혜택 ?>
                                    <?php if( $groupSetUse === true && $groupSetInfo['mgs_give_point_per'] > 0 ) { ?>
                                    <span class="point_plus">
                                        <span class="txt">회원추가적립</span> <strong><?=odt_number_format($groupSetInfo['mgs_give_point_per'],1)?>%</strong>
                                    </span>
                                    <?php } ?>
                                    <?php // {{{회원등급혜택 ?>
                                </dd>
                            </dl>
                            <?php } ?>

                            <?php if($pro_brand_name && $v == 'brand'){ ?>
                            <dl>
                                <dt class="view_table-txt">브랜드</dt>
                                <dd class="view_table-txt">
                                    <span class="brand_tx"><?php echo $pro_brand_name; ?></span>
                                    <a href="/?pn=product.brand_list&uid=<?php echo $pro_brand_uid; ?>" target="_blank"  class="btn_brand">브랜드 다른 상품보기</a>
                                </dd>
                            </dl>
                            <?php } ?>

                            <?php if($v == 'deliveryInfo'){ ?>
                            <dl>
                                <dt class="view_table-txt">배송정보</dt>
                                <dd class="view_table-txt"><?php echo $pro_del_info; //<!-- 슬래시 사이 간격 유지 --> ?></dd>
                            </dl>
                            <?php } ?>
                            <?php if($v == 'deliveryPrice'){ ?>
                            <dl>
                                <dt class="view_table-txt">배송비</dt>
                                <dd class="view_table-txt">
                                    <?php
                                        echo '<span class="delivery">'.$pro_delivery.'</span>';
                                    ?>

                                    <?php // {{{무료배송이벤트}}} ?>
                                    <?php if( $freeEventChk === true && $p_info['p_free_delivery_event_use'] == 'Y' ) { ?>
                                    <span class="point_plus delivery_free">
                                        <strong><?=number_format($freeEventInfo['minPrice'])?>원</strong><span class="txt">이상 주문 시 무료배송 이벤트 진행</span>
                                    </span>
                                    <?php } ?>
                                    <?php // {{{무료배송이벤트}}} ?>

                                </dd>
                            </dl>
                            <?php } ?>

                            <?php if($ex_coupon['name'] && $ex_coupon[1] && $v == 'coupon'){ ?>
                                <!-- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22-->
                                <dl>
                                    <dt><?php echo $arrDisplayPinfo['coupon'] ?></dt>
                                    <dd>
                                        <div class="view_coupon">
                                            <span class="coupon_name">
                                                <?php echo stripslashes($ex_coupon['name']); ?>
                                            </span>
                                            <span class="coupon_about">
                                            <?php if($ex_coupon[1]=="price"){
                                                echo number_format($ex_coupon['price']); ?>원
                                            <?php }else {?>
                                                <?php echo floor($ex_coupon['per']*10)/10; ?>%
                                                <?php if($ex_coupon['max']>0){?>
                                                    <span class="txt"> ( 최대: <?php echo number_format($ex_coupon['max']); ?>원 할인 )</span>
                                                <?php }?>
                                            <?php }?>
                                            </span>

                                            <div class="guide">
                                                <div class="open_box">
                                                    <div class="tt">상품 쿠폰 사용하는 방법</div>
                                                    <span class="txt">상품 쿠폰이 있는 상품을 구매하실 경우 주문서 작성 시 쿠폰을 선택하시면 해당 상품 가격에 할인이 적용되어 최종금액에 반영됩니다.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                            <?php } ?>

                        <?php } ?>

                    </div>
                    <?php } ?>
                    <dl class="view-info_dl">
                            <dt class="view_table-txt">원단</dt>
                            <dd class="view_table-content"><?php echo implode(' / ', array_filter(array($pro_maker, $pro_orgin))); //<!-- 슬래시 사이 간격 유지 / 제조사,원산지 중 한개만 있을경우 슬래시 삭제--> ?></dd>
                    </dl>
                    <dl class="view-info_dl">
                            <dt class="view_table-txt">사이즈</dt>
                            <dd class="view_table-content"><?php echo implode(' / ', array_filter(array($pro_maker, $pro_orgin))); //<!-- 슬래시 사이 간격 유지 / 제조사,원산지 중 한개만 있을경우 슬래시 삭제--> ?></dd>
                    </dl>
                    <dl class="view-info_dl">
                            <dt class="view_table-txt">등록일</dt>
                            <dd class="view_table-content"><?php echo implode(' / ', array_filter(array($pro_maker, $pro_orgin))); //<!-- 슬래시 사이 간격 유지 / 제조사,원산지 중 한개만 있을경우 슬래시 삭제--> ?></dd>
                    </dl>
                    <dl class="view-info_dl">
                            <dt class="view_table-txt">특이사항</dt>
                            <dd class="view_table-content"><?php echo implode(' / ', array_filter(array($pro_maker, $pro_orgin))); //<!-- 슬래시 사이 간격 유지 / 제조사,원산지 중 한개만 있을경우 슬래시 삭제--> ?></dd>
                    </dl>

                    <?php if($p_info['p_option_valid_chk']=='Y'  && $isSoldOut == false){ ?>

                        <?php
                            // -- 옵션 없을 경우 ----
                            if($p_info['p_option_type_chk'] == 'nooption'){
                        ?>

                                <!-- 옵션없이 수량선택 -->
                                <div class="view_option">
                                    <dl>
                                        <dt>상품 수량</dt>
                                        <dd class="counter">
                                            <div class="counter_box">
                                                <?php if($p_info['p_stock'] > 0){ ?>
                                                <input type="text" name="option_select_cnt" class="updown_input" id="option_select_cnt" value="1" readonly/>
                                                <span class="updown"><a href="#none" onclick="pro_cnt_up(); return false;" class="btn_up" title="더하기"></a><a href="#none" onclick="pro_cnt_down(); return false;" class="btn_down" title="빼기"></a></span>
                                                <?php }else{ ?>
                                                    품절<input type="hidden" name="option_select_cnt" class="input_num" id="option_select_cnt" value="0" />
                                                <?php } ?>
                                                <input type="hidden" name="option_select_expricesum" ID="option_select_expricesum" value="<?php echo ($p_info['p_price']-getGroupSetPer($p_info['p_price'],'price',$pcode)); ?>">
                                                <input type="hidden" name="option_select_type" id="option_select_type" value="<?php echo $p_info['p_option_type_chk']; ?>">
                                            </div>
                                        </dd>
                                        <dd class="price"><span class="price"><strong><?php echo number_format($p_info['p_price']-getGroupSetPer($p_info['p_price'],'price',$pcode)); ?></strong>원</span></dd>
                                    </dl>
                                </div>

                        <?php
                            // -- 옵션 있을 경우 ----
                            }else if(count($options) > 0){
                        ?>

                            <input type="hidden" name="_option_select1" ID="option_select1_id" value="">
                            <div class="open_box" id="option_select_1_box">
                                <?php foreach( $options as $k=>$sr){
                                    $k_idx = $k+1;
                                    $option_select_tmp .= "option_select_tmp('1' , '{$p_info['p_option_type_chk']}' , '{$sr['po_uid']}' , '{$sr['po_poptionname']}' , '{$p_info['p_code']}','$k_idx');\n";
                                ?>
                                    <dl class="view-info_dl">
                                            <dt class="view_table-txt"><?php echo $sr['po_poptionname']; ?></dt>
                                            <dd class="view_table-content">
                                                <?php if( in_array($p_info['p_option_type_chk'], array('2depth','3depth')) ){?>
                                                    <div class="view_option_size span_option2_<?=$k_idx?>" id="span_option2" data-idx="2">
                                                    </div>
                                                <?php } ?>
                                                <?php if($p_info['p_option_type_chk'] == '3depth'){ ?>
                                                    <div class="view_option_size span_option3_<?=$k_idx?>" id="span_option3" data-idx="3">
                                                    </div>
                                                <?php } ?>
                                            </dd>
                                    </dl>
                                <?php } ?>
                            </div>
                            <script>
                            $(document).ready(function(){ 
                                <?=$option_select_tmp?>
                            });
                            </script>



                                <!-- 옵션 있을 경우 -->
                                <div class="view_option">
                                    <?php if(count($add_options)>0 && $p_info['p_stock'] > 0){ ?>
                                        <dl>
                                            <dt>추가 옵션</dt>
                                            <dd>
                                                <!-- 셀렉트박스 / 옵션 선택전 before 클래스 추가  -->
                                                <?php foreach($add_options as $k=>$v) { ?>
                                                    <div class="view_select select_box" data-idx="<?php echo ($k+1); ?>">
                                                        <!-- 여기에 선택한 값이 나타남 -->
                                                        <div class="this"><?php echo trim($v['pao_poptionname']); ?><span class="shape"></span></div>
                                                        <div class="open_box" id="add_option_select_<?php echo ($k+1); ?>_box">
                                                            <?php foreach($v['add_sub_options'] as $key=>$value){ ?>
                                                                <a href="#none" onclick="add_option_select_add('<?php echo $pcode; ?>', <?php echo $value['pao_uid']; ?> , '<?php echo $value['pao_poptionname'].($value['pao_cnt']>0 ? ($isOptionStock ? ' (잔여:'.number_format($value['pao_cnt']).')' : null) . ' / '. number_format($value["pao_poptionprice"]) . '원' : "품절"); ?>' ,  <?php echo ($k+1); ?>); return false;" class="option">
                                                                    <?php echo $value['pao_poptionname'].($value['pao_cnt']>0 ? ($isOptionStock ? ' (잔여:'.number_format($value['pao_cnt']).')' : null) . ' / '. number_format($value["pao_poptionprice"]) . '원' : '<span class="soldout">품절</span>'); ?>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                        <input type="hidden" name="_add_option_select_<?php echo ($k+1); ?>" id="add_option_select_<?php echo ($k+1); ?>_id" class="add_option add_option_chk">
                                                    </div>
                                                <?php } ?>

                                            </dd>
                                        </dl>
                                    <?php } ?>
                                </div>

                                <!-- 선택한 옵션 -->
                                <div class="view_option result" id="span_seleced_list">
                                    <dl>
                                        <dt class="if_before">구매하실 상품 옵션을 선택해 주시기 바랍니다.</dt>
                                    </dl>
                                </div>
                        <?php } ?>






                        <!-- 결제금액계산 -->
                        <?php
                            // 상품 옵션 설정이 등록되었을때만 노출
                            if($p_info['p_option_type_chk'] == 'nooption' || count($options) > 0){
                        ?>
                        <div class="view_total">
                            <div class="total_top-box" style="border-top:1px #ccc solid; border-left:1px #ccc solid; border-right:1px #ccc solid;">
                                <div class="total_bottom-box" style="padding-left:50px; line-height:60px;"><span class="total_tt">총 합계금액</span></div>
                                <div class="tolat_input-btn">
                                    <div class="after_price"><strong id="option_select_expricesum_display">0</strong>원</div>
                                </div>
                            </div>
    
                        </div>
                        <?php } ?>



                    <?php }else if($isSoldOut == false){ ?>
                        <span class="view_total_error">현재 상품판매를 준비중입니다.</span>
                    <?php } ?>




                    <?php
                        // 상품 옵션 설정이 등록되었을때만 노출
                        if(($p_info['p_option_type_chk'] == 'nooption' || count($options) > 0) && $p_info['p_option_valid_chk']=='Y'){
                    ?>
                        <!-- 구매,장바구니,찜하기 버튼 -->
                        <div class="view_btn view_cart_ask view-btn_display">
                            <?php if($isSoldOut === false){  ?>
                            <!-- 장바구니 담고 묻는창 나오도록 클래스값 추가 (모션을위함) if_cart_save -->
                            <!-- 장바구니 눌렀을때 선택버튼 -->
                            <div class="how">
                                <div class="box">
                                    <div class="tip">상품을 장바구니에 담았습니다! <br>장바구니로 이동할까요?</div>
                                    <ul>
                                        <li><a href="#none" onclick="return false;" class="btn2 no_cart">계속 쇼핑</a></li>
                                        <li><a href="/?pn=shop.cart.list" class="btn2 go_cart">바로가기</a></li>
                                    </ul>
                                </div>
                                <script type="text/javascript">
                                    $(document).ready(function(){ $('.view_cart_ask .how .no_cart').click(function(){ $('.view_cart_ask').removeClass('if_cart_save'); }); });
                                </script>
                            </div>
                            <?php }?>
                            <ul>
                                <?php if($isSoldOut){  ?>
                                    <li><a href="#none" onclick="return false;" class="btn btn_soldout">품절된 상품입니다.</a></li>
                                <?php }else{ ?>
                                    <li><a href="#none" onclick="<?php echo ($p_info['p_stock'] < 1 ? "app_soldout();" : "app_submit('".$pcode."','cart');"); ?>return false;" class="btn btn_cart">장바구니</a></li>
                                    <li><a href="#none" onclick="<?php echo ($p_info['p_stock'] < 1 ? "app_soldout();" : "app_submit('".$pcode."','order');"); ?>return false;" class="btn btn_order" style="display:none;">바로구매</a></li>
                                <?php }?>
                            </ul>
                            <!-- 찜하기버튼 / 활성화 시 hit 클래스 추가 -->
                            <a href="#none" onclick="return false;" class="btn btn_wish js_wish<?php echo (is_wish($p_info['p_code'])?' hit':null); ?>" data-pcode="<?php echo $p_info['p_code']; ?>" title="찜하기"></a>
                        </div>
                        <div class="btn-cart_sub">
                                <p class="btn-cart_subtxt">주문은 장바구니에서 통합주문이 가능합니다. 우선 장바구니에 상품을 담아주세요 </p>
                        </div>


                        <?php // LDD NPAY { ?>
                            <?php
                            $NPayTrigger = 'N';
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $p_info['npay_use'] == 'Y') $NPayTrigger = 'Y';
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'test' && $nt == 'test') $NPayTrigger = 'Y';
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'test' && $nt != 'test') $NPayTrigger = 'N'; // 버튼+주문연동 작업
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'real' && $p_info['npay_use'] == 'Y') $NPayTrigger = 'Y'; // 버튼+주문연동 작업

                            // LCY : 네이버페이 사용유무 추가 : 2020-10-20 - 어떠한 경우라도 상품의 네이버페이 사용유무가 Y가 아니라면 노출하지 않는다.
                            if( $p_info['npay_use'] != 'Y'){ $NPayTrigger = 'N'; }

                            if($NPayTrigger == 'Y') {
                            ?>
                            <div style="padding-top:20px; text-align:center;">
                                <script type="text/javascript" src="//<?php echo ($siteInfo['npay_mode'] == 'test'?'test-':null); ?>pay.naver.com/customer/js/naverPayButton.js" charset="UTF-8"></script>
                                <script type="text/javascript">
                                //<![CDATA[
                                    function NPayBuy() {

                                        var pcode = '<?php echo $pcode; ?>';
                                        var _type = 'view';
                                        if( !( $("#option_select_cnt").val() * 1 > 0 ) ) {
                                            alert("옵션을 하나 이상 선택해주시기 바랍니다.")
                                        }
                                        else if( !( $("#option_select_expricesum").val() * 1 > 0 ) ) {
                                            alert("옵션 합계금액이 0원을 초과해야 합니다.")
                                        }
                                        else {
                                            location.href = ('/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val());
                                            //var LocationUrl = '/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val();
                                            //window.open(LocationUrl, '', "scrollbars=yes, width=1200, height=500");
                                        }
                                    }
                                    function NPayWish() {

                                        var pcode = '<?php echo $pcode; ?>';
                                        var _type = 'wish';
                                        var LocationUrl = '/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val();
                                        window.open(LocationUrl, '', "scrollbars=yes, width=400, height=267");
                                        return false;
                                    }
                                    naver.NaverPayButton.apply({
                                        BUTTON_KEY: "<?php echo $siteInfo['npay_bt_key']; ?>", // 페이에서 제공받은 버튼 인증 키 입력
                                        TYPE: "A", // 버튼 모음 종류 설정
                                        COLOR: 1, // 버튼 모음의 색 설정
                                        COUNT: 2, // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
                                        ENABLE: "Y", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
                                        BUY_BUTTON_HANDLER: NPayBuy, // 구매하기 버튼
                                        WISHLIST_BUTTON_HANDLER: NPayWish, // 찜하기 버튼
                                        "":"",
                                    });
                                //]]>
                                </script>
                            </div>
                            <?php } ?>
                        <?php // } LDD NPAY ?>

                    <?php } ?>



                </li>
                <li class="view_ranking">
                  <p class="ranking_tit">카테고리 인기순위</p>
                    <p class="ranking_p">아우터 카테고리 실시간 인기상품입니다</p>
                    <div class="ranking_div">
                        <p class="ranking_product">상품이 존재하지 않습니다.</p>
                    </div>
                </li>
            </ul>
            <ul class="view-top_banner">
                <li><a href="#"></a></li>
                <li><a href="#"></a></li>
                <li><a href="#"></a></li>
            </ul>
        </div>
    </div>
    <!-- / ◆ 상품상세 : 사진,기본정보 -->







    <?php
        if(count($ProductMiddle)>0){
            foreach($ProductMiddle as $k=>$v){
                $_img = get_img_src($v['b_img'], IMG_DIR_BANNER);
                if($_img == '') continue;
    ?>
                <!-- ◆ 상세배너 (없으면 전체 숨김)  -->
                <div class="view_banner">
                    <div class="layout_fix">
                        <!-- [PC]공통 : 상품상세 중간 배너 (1050 x free) -->
                        <?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?><a href="<?php echo $v['b_link']; ?>" target="<?php echo $v['b_target']; ?>"><?php } ?>
                        <img src="<?php echo $_img; ?>" alt="<?php echo addslashes($v['b_title']); ?>">
                        <?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?></a><?php } ?>
                    </div>
                </div>
                <!-- / ◆ 상세배너 -->
    <?php
            }
        }
    ?>




    <?php if(count($relation) > 0){ ?>
        <!-- ◆ 다른관련상품 (없으면 전체 숨김)  -->
        <div class="view_relative">
            <div class="layout_fix">

                <div class="relative_top">
                    <div class="tt">다른 관련상품</div>

                    <?php if(count($relation)>$SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]){ ?>
                    <div class="rolling_nate">
                        <span class="num js_list_relation_slide_pager"><strong>1</strong>/<?php echo ceil(count($relation)/$SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']])?></span>
                            <!-- 이전다음버튼 (롤링안될때 숨김) -->
                            <span class="prevnext prev "><a href="#none" onclick="return false;" class="js_list_relation_slide_prev" title="이전"><span class="icon"></span></a></span>
                            <span class="prevnext next"><a href="#none" onclick="return false;" class="js_list_relation_slide_next" title="다음"><span class="icon"></span></a></span>
                    </div>
                    <?php } ?>
                </div>

                <div class="rolling_box">
                    <!-- ◆ 상품리스트 : 기본 4단 / 5단 if_col5 -->
                    <div class="item_list if_col<?php echo $SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]; ?>">
                        <?php//슬라이드의 크기를 판단하고 첫페이지의 모양을 잡기위한 더미 슬라이드?>
                        <ul class="js_list_relation_slide_tmp" style="min-width:100%;"><!-- SSJ : 관련상품 넓이 오류 수정 : 2020-07-29 -->
                            <?php
                            foreach($relation as $bi_k=>$bi_v) {
                                if($SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']] - $bi_k < 1) break;

                            ?>
                                <li>
                                <?php 
                                    $incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
                                    $locationFile = basename(__FILE__); // 파일설정
                                    $k = $bi_k; $v = $bi_v;
                                    include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
                                ?>
                                </li>
                            <?php } ?>
                            <?php
                            if(count($relation) < $SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]) {
                                for($i=0; $i<$SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]-count($relation); $i++) {
                            ?>
                                <li></li>
                            <?php }} ?>
                        </ul>
                        <ul class="js_list_relation_slide">
                            <?php
                            foreach($relation as $bi_k=>$bi_v) {
                            ?>
                                <li>
                                <?php 
                                    $incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
                                    $locationFile = basename(__FILE__); // 파일설정
                                    $k = $bi_k; $v = $bi_v;
                                    include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
                                ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <!-- / ◆ 상품리스트 -->
                </div>

            </div>
        </div>
        <!-- / ◆ 다른연관상품 -->

        <?php if(count($relation)>$SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]){ ?>
            <script type="text/javascript">
                $(window).load(function() {
                    var RelationSlideMargin = $('.js_list_relation_slide_tmp').find('.item_box').css('margin-left').replace('px', '')*1;
                    var RelationSlideWrap = $('.js_list_relation_slide_tmp').outerWidth();
                    var RelationSlideWidth = $('.js_list_relation_slide_tmp').find('.item_box').outerWidth();

                    var RelationSlideMarginTop = $('.js_list_relation_slide_tmp').find('.item_box').css('margin-top').replace('px', '')*1;
                    var RelationSlideWrapHeight = $('.js_list_relation_slide_tmp').outerHeight();

                    $('.js_list_relation_slide_tmp').remove();
                    $('.js_list_relation_slide').show();
                    $('.js_list_relation_slide').css('width', RelationSlideWrap);

                    $('.js_list_relation_slide').css('margin-left', 0);
                    $('.js_list_relation_slide .item_box').css('margin-left', 0);
                    $('.view_relative .rolling_box').css('height', RelationSlideWrapHeight-RelationSlideMarginTop);
                    var RelationSlide = $('.js_list_relation_slide').bxSlider({
                        auto: false,
                        autoHover: false,
                        pager:false,
                        //pagerCustom: '.js_list_relation_slide_pager',
                        controls: false,
                        useCSS: false,
                        minSlides: <?php echo $SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]; ?>,
                        maxSlides: <?php echo $SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]; ?>,
                        moveSlides: <?php echo $SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]; ?>,
                        slideMargin: RelationSlideMargin,
                        slideWidth: RelationSlideWidth+RelationSlideMargin,
                        holdWidth: RelationSlideWidth+RelationSlideMargin, // LDD: 2018-01-09 새롭게 추가된 옵션(자동 크기 변경을 차단하고 지정값으로 강제로 맞춘다)
                        onSliderLoad: function() { },
                        onSlideBefore: function() {
                            insertCount();
                            RelationSlide.stopAuto();
                        },
                        onSlideAfter: function() { RelationSlide.startAuto(); }
                    });

                    $('.js_list_relation_slide_prev').on('click', function(e) {
                        e.preventDefault();
                        RelationSlide.goToPrevSlide();
                    });

                    $('.js_list_relation_slide_next').on('click', function(e) {
                        e.preventDefault();
                        RelationSlide.goToNextSlide();
                    });

                    // 숫자형 페이지
                    function insertCount() {
                        var slide_count = Math.ceil(RelationSlide.getSlideCount()/<?php echo $SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']]; ?>);
                        var slide_curr = RelationSlide.getCurrentSlide();
                        $('.js_list_relation_slide_pager').html('<strong>' + (slide_curr + 1) + '</strong>/' + slide_count);
                    };
                });

            </script>
        <?php } ?>

    <?php } ?>








    <!-- ◆ 상세탭 -->
    <div class="view_tab js_info_position">
        <div class="layout_fix">
            <ul>
                <!-- 활성화시 hit클래스  -->
                <li class="hit"><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab">상품상세정보</a></li>
                <!--
                <li class=""><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab">상품후기 <em class="num eval_cnt">(<?php echo $eval_cnt; ?>)</em></a></li>
                <li class=""><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab">상품문의 <em class="num qna_cnt">(<?php echo $qna_cnt; ?>)</em></a></li>
            -->
                <li class=""><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab">배송안내</a></li>
            </ul>
        </div>
    </div>
    <!-- / ◆ 상세탭 -->











    <!-- ◆상품정보 -->
    <div class="view_conts">
        <div class="layout_fix">

            <!-- 에디터 : 상품상세안에 들어가는 이미지 가로최대 1050px -->
            <div class="view_detail editor"><?php echo stripcslashes($p_info['p_content']); ?></div>

            <?php if(count($notify_info) > 0 ) { ?>
                <!-- 상품정보제공고시 -->
                <div class="view_notify">
                    <div class="group_title">상품 정보 제공고시</div>
                    <table>
                        <colgroup>
                            <col width="180"><col width="*"><col width="180"><col width="*">
                        </colgroup>
                        <tbody>
                            <tr>
                                <?php
                                foreach($notify_info as $nik=>$niv) {
                                    if($nik>0 && $nik%2==0) echo '</tr><tr>';
                                ?>
                                    <th><?=stripslashes($niv['pri_key'])?></th>
                                    <td><?=stripslashes($niv['pri_value'])?></td>
                                <?php } ?>
                                <?php if($nik%2==0) echo '<td></td><td></td>'; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php } ?>



        </div>
    </div>
    <!-- / ◆상품정보 -->








    <!-- ◆ 상세탭 -->
    <div class="view_tab js_eval_position display-none">
        <div class="layout_fix">
            <ul>
                <!-- 활성화시 hit클래스  -->
                <li class=""><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab">상품정보</a></li>
                <!--
                <li class="hit"><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab">상품후기 <em class="num eval_cnt">(<?php echo $eval_cnt; ?>)</em></a></li>
                <li class=""><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab">상품문의 <em class="num qna_cnt">(<?php echo $qna_cnt; ?>)</em></a></li>
                                -->
                <li class="hit"><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab">배송안내</a></li>
            </ul>
        </div>
    </div>
    <!-- / ◆ 상세탭 -->

    <!-- ◆상품후기 -->
    <div class="view_conts">
        <div class="layout_fix display-none" id="eval_ajax">
            <?php include OD_PROGRAM_ROOT."/product.eval.form.php"; ?>
        </div>
    </div>
    <!-- / ◆상품후기 -->





    <!-- ◆ 상세탭 -->
    <div class="view_tab js_qna_position display-none">
        <div class="layout_fix">
            <ul>
                <!-- 활성화시 hit클래스  -->
                <li class=""><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab">상품정보</a></li>
                <li class=""><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab">상품후기 <em class="num eval_cnt">(<?php echo $eval_cnt; ?>)</em></a></li>
                <li class="hit"><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab">상품문의 <em class="num qna_cnt">(<?php echo $qna_cnt; ?>)</em></a></li>
                <li class=""><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab">배송안내</a></li>
            </ul>
        </div>
    </div>
    <!-- / ◆ 상세탭 -->

    <!-- ◆상품문의 -->
    <div class="view_conts display-none">
        <div class="layout_fix" id="qna_ajax">
            <?php include OD_PROGRAM_ROOT."/product.qna.form.php"; ?>
        </div>
    </div>
    <!-- / ◆상품문의 -->







    <!-- ◆ 상세탭 -->
    <div class="view_tab js_guide_position">
        <div class="layout_fix">
            <ul>
                <!-- 활성화시 hit클래스  -->
                <li class=""><a href="#none" onclick="scrolltoClass('.js_info_position'); return false;" class="tab">상품정보</a></li>
                <!--
                <li class=""><a href="#none" onclick="scrolltoClass('.js_eval_position'); return false;" class="tab">상품후기 <em class="num eval_cnt">(<?php //echo $eval_cnt; ?>)</em></a></li>
                <li class=""><a href="#none" onclick="scrolltoClass('.js_qna_position'); return false;" class="tab">상품문의 <em class="num qna_cnt">(<?php //echo $qna_cnt; ?>)</em></a></li>
                                -->
                <li class="hit"><a href="#none" onclick="scrolltoClass('.js_guide_position'); return false;" class="tab">배송안내</a></li>
            </ul>
        </div>
    </div>
    <!-- / ◆ 상세탭 -->








    <!-- ◆배송/교환/반품 안내 -->
    <div class="view_conts">
        <div class="layout_fix">

            <?php // JJC : 2019-05-15 : 판매자 정보 ?>
            <div class="view_notify">
                <div class="sub_tit">판매자 정보</div>
                <table>
                    <colgroup>
                        <col width="180"><col width="*"><col width="180"><col width="*">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>상호명</th>
                            <td><?php echo $app_adshop; ?></td>
                            <th>대표전화</th>
                            <td><?php echo $app_glbtel; ?></td>
                        </tr>
                        <tr>
                            <th>대표자</th>
                            <td><?php echo $app_ceo_name; ?></td>
                            <th>팩스전화</th>
                            <td><?php echo $app_fax; ?></td>
                        </tr>
                        <tr>
                            <th>사업자등록번호</th>
                            <td><?php echo $app_company_num; ?></td>
                            <th>대표 이메일</th>
                            <td><?php echo $app_ademail; ?></td>
                        </tr>
                        <tr>
                            <th>통신판매업번호</th>
                            <td><?php echo $app_company_snum; ?></td>
                            <th>사업장소재지</th>
                            <td><?php echo $app_company_addr; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php // JJC : 2019-05-15 : 판매자 정보 ?>





            <!-- 배송 기본정보 -->
            <div class="view_notify">
                <div class="sub_tit">배송 기본정보</div>
                <table>
                    <colgroup>
                        <col width="180"><col width="*"><col width="180"><col width="*">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>지정택배사</th>
                            <td><?php echo $del_company; ?></td>
                            <th>평균배송기간</th>
                            <td><?php echo $del_date; ?></td>
                        </tr>
                        <tr>
                            <th>기본배송비</th>
                            <td><?php echo $pro_delivery; ?></td>
                            <th>반송주소</th>
                            <td><?php echo $del_return_addr; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>




            <?php
                if(count($arrProGuideType)>0){
                    foreach($arrProGuideType as $_guide_key=>$_guide_title){
                        // 내용을 저장할 변수 초기화
                        $_guide_text = '';

                        // 내용 추출 - 직접입력
                        if($p_info['p_guide_type_'.$_guide_key] == 'manual'){
                            $_guide_text = $p_info['p_guide_'.$_guide_key];
                        }
                        // 내용 추출 - 선택입력
                        else if($p_info['p_guide_type_'.$_guide_key] == 'list'){
                            $_guide_text = _MQ_result(" select g_content  from smart_product_guide where g_uid = '". $p_info['p_guide_uid_'.$_guide_key] ."' and g_user in ('_MASTER_', '". $p_info['p_cpid'] ."') ");
                        }
                        // 사용안함 체크
                        else{
                            continue;
                        }

                        // 내용이 없으면 노출하지 않음
                        if(trim($_guide_text) == ''){ continue; }
            ?>

                        <!-- 배송 구매/배송안내 / 제목과 내용 모두 관리자에서 설정가능 -->
                        <div class="view_guide">
                            <div class="sub_tit"><?php echo stripslashes($_guide_title); ?><span class="add">※ 상품정보에 별도 기재된 경우 ,아래의 내용보다 우선하여 적용됩니다.</span></div>
                            <div class="txt_box editor"><?php echo stripslashes($_guide_text); ?></div>
                        </div>

            <?php
                    }
                }
            ?>






        </div>
    </div>
    <!-- / ◆배송/교환/반품 안내 -->

</div>

<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript">
    var old_idx = "0";
    var now_idx = "1";
    var max_idx = $(".photo_thumb img").length;
    var auto_change = true;
    function view_thumb_img(idx,mode) {
        if(!auto_change && mode=="auto") return;

        img_src = $("#thumb_"+idx).attr("src");
        img_src = img_src.replace("thumbs_s_","");
        $("#main_img").attr("src",img_src);

        // 셈네일 이미지 class on/off
        $("#thumb_"+idx).removeClass("off");
        $("#thumb_"+idx).addClass("on");
        $("#thumb_"+old_idx).removeClass("on");
        $("#thumb_"+old_idx).addClass("off");

        old_idx = idx;
        now_idx = idx*1+1 > max_idx ? 1 :idx*1+1;
    }
    $(".photo_thumb .fix").hover(
        function() {
            auto_change = false;
        },
        function() {
            auto_change = true;
        }
    );

    function view_thumb_img_auto() {
        view_thumb_img(now_idx,"auto");
        setTimeout(view_thumb_img_auto,2000);
    }

    function sale_info(mode) {
        if(mode == "show") $(".ly_notice").show();
        else $(".ly_notice").hide();
    }

    function pro_cnt_up() {
        cnt = $("#option_select_cnt").val()*1;
        // 2019-07-24 SSJ :: 옵션이 없는 상품의 재고체크 추가
        $.ajax({
            url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
            data: {'_mode':'get_pstock' , 'pcode':'<?php echo $pcode; ?>'},
            type: 'post',
            dataType: 'text',
            success: function(data){
                if(data == 0){
                    alert('해당 상품은 품절된 상품입니다.');
                    location.reload();
                }else if(cnt+1 > data){
                    alert('해당 상품의 재고량이 부족합니다.');
                    $("#option_select_cnt").val(data);
                }else{
                    $("#option_select_cnt").val(cnt+1);
                }
                update_sum_price();
            }
        });
    }
    function pro_cnt_down() {
        cnt = $("#option_select_cnt").val()*1;
        // 2019-07-24 SSJ :: 옵션이 없는 상품의 재고체크 추가
        $.ajax({
            url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
            data: {'_mode':'get_pstock' , 'pcode':'<?php echo $pcode; ?>'},
            type: 'post',
            dataType: 'text',
            success: function(data){
                if(data == 0){
                    alert('해당 상품은 품절된 상품입니다.');
                    location.reload();
                }else if(cnt-1 > data){
                    alert('해당 상품의 재고량이 부족합니다.');
                    $("#option_select_cnt").val(data);
                }else{
                    if(cnt > 1) $("#option_select_cnt").val(cnt-1);
                }
                update_sum_price();
            }
        });
    }
    function update_sum_price() {
        var sumprice = 0;
        sumprice = String($("#option_select_expricesum").val()*$("#option_select_cnt").val());
        if(sumprice == "NaN") sumprice = "0";
        $("#option_select_expricesum_display").html(sumprice.comma());
    }


    $(document).ready(function() {
        // 섬네일 이미지 자동 변경
        //view_thumb_img_auto();
        update_sum_price();
    });

    function cate_change(obj) {
        location.href="/?pn=product.list&cuid="+obj.value;
    }

    // SNS공유하기 버튼
    function sendSNS(type) {
        var url = 'http://<?=$system['host']?>/?pn=product.view&pcode=<?=$pcode?>';
        var title = '<?=$pro_name?>';
        var image = '<?=$main_img?>';
        var desc = '<?=cutstr(trim(str_replace("  "," ",str_replace(":","-",str_replace("\t"," ",str_replace("\r"," ",str_replace("\n"," ",str_replace("'","`",stripslashes(($p_info['p_subname']?$p_info['p_subname']:$siteInfo['s_glbtlt']))))))))) , 24 , "..")?>';
        if(type == 'kakao') {
            try {
                Kakao.cleanup();
                Kakao.init('<?php echo $siteInfo['kakao_js_api']; ?>');
                Kakao.Link.sendDefault({
                    objectType: 'feed',
                    content: {
                        title: title,
                        description: desc,
                        imageUrl: image,
                        imageWidth: 470, // 없으면 이미지가 찌그러짐
                        imageHeight: 470, // 없으면 이미지가 찌그러짐
                        link: {
                            mobileWebUrl: url,
                            webUrl: url
                        }
                    },
                    buttons: [
                        {
                            title: og_site_name,
                            link: {
                                mobileWebUrl: url,
                                webUrl: url
                            }
                        }
                    ],
                    installTalk: true,
                    fail: function(err) {
                        alert(JSON.stringify(err));
                    }
                });
            } catch(e) {
                alert('카카오톡으로 공유 할 수 없는 상태 입니다.');
            };
        }
        else if(type=='facebook') {
            postToFeed(title, desc, url, image);
        }
        else if(type=='twitter') {
            var wp = window.open("http://twitter.com/intent/tweet?text=" + encodeURIComponent(title) + " " + encodeURIComponent(url), 'twitter', 'width=550,height=256');
            if(wp) { wp.focus(); }
        }
        else if(type=='pinterest') {
            var href = "http://www.pinterest.com/pin/create/button/?url="+encodeURIComponent(url)+"&media="+encodeURIComponent(image)+"&description="+encodeURIComponent(title);
            var a = window.open(href, 'pinterest', 'width=734, height=734');
            if ( a ) {
                a.focus();
            }
        }
        $.ajax({
            data: {'pcode':'<?=$pcode?>','type':type},
            type: 'GET', cache: false, url: '<?php echo OD_PROGRAM_URL; ?>/ajax.sns.update.php',
            success: function(data) { return true; },
            error:function(request,status,error){ alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error); }
        });
    }





    function option_select_tmp(idx,pro_depth,pouid,poname,pcode,k_idx) {

        $('#option_select'+idx+'_id').val(pouid);
        $('#option_select'+idx+'_poname').html(poname);
        $('#option_select_'+idx+'_box').css({'display':'none'});
        setTimeout(function(){ $('#option_select_'+idx+'_box').attr({'style':''}); }, 100);

        if(idx+'depth' == pro_depth){
            option_select_add(pcode);
        }else{
            option_select(idx,pcode,k_idx,poname);
        }

    }


    // 해시이동(주소해시에 상응하는 클래스 객체가 있다면 스크롤 자동 이동)
    $(function() {
        var UrlHash = window.location.hash;
        if(UrlHash) {
            UrlHash = UrlHash.replace('#', '');
            if($('.'+UrlHash).length > 0) {
                scrolltoClass('.'+UrlHash, -100);
            }
        }
    });
</script>
<script src="<?php echo $SkinData['skin_url']; ?>/js/option_select.js?v221129" type="text/javascript"></script>