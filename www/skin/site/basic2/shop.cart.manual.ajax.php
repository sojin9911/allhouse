<?php
    $arr_product_sum = $arr_push_product = array();  // 변수 초기화
?>

<?php if(count($arr_cart) > 0){ ?>
    <br/>
    <form name="frm" method="post">
    <input type="hidden" name="mode" value=""/>
    <input type="hidden" name="cuid" value=""/>
    <input type="hidden" name="code" value=""/>
    <input type="hidden" name="allcheck" value="Y"/>
    <input type="hidden" name="buy_type" value="manual"/>

            <!-- ◆장바구니 리스트 -->
            <div class="c_cart_list">

                <div class="table_top">
                    <div class="tit_box">
                        <span class="txt hide">업체배송</span>
                        <span class="txt shop_tit hide"><?php echo $arr_customer[$crk]['cName']; ?></span>
                    </div>
                    <div class="guide_txt hide"><?php echo ($arr_customer[$crk]['com_delprice_free'] > 0 ? '<strong>'. number_format($arr_customer[$crk]['com_delprice_free']) .'원</strong> 이상 구매시 배송비 무료 (개별배송 제외)' : ''); ?></div>
                </div>


                <div class="table_top">
                    <div class="tit_box">
                        <span class="txt">※ 치수정보는 한개씩만 등록하여 주시고, 추가 사이즈는 추가 버튼을 클릭하여 추가해주세요.</span>
                    </div>
                </div>
                <div class="cart_table">
                    <table>
                    <colgroup>
                        <col width="200"><col width="*"><col width="110"><col width="100"><col width="100"><col width="130">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">브랜드명</th>
                            <th scope="col">상품명</th>
                            <th scope="col">컬러</th>
                            <th scope="col">치수정보</th>
                            <th scope="col">수량</th>
                            <th scope="col">비고</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                // -- 변수 초기화
                                unset($del_chk_customer);
                                $arr_product = array(); // 업체별 상품 합계
                                $arr_per_product = array(); // 상품별 합계 // ----- JJC : 상품별 배송비 : 2018-08-16 -----

                                foreach($arr_cart as $k=>$v) { // 업체별 상품 반복 구간
                            ?>
                                    <tr style="height:45px">
                                        <td>
                                            <?=$v['c_brand']?>
                                        </td>
                                        <td>
                                            <?=$v['c_item_name']?>
                                        </td>
                                        <td>
                                            <?=$v['c_color']?>
                                        </td>
                                        <td>
                                            <?=$v['c_size']?>
                                        </td>
                                        <td>
                                            <?=$v['c_cnt']?>
                                        </td>
                                        <td>
                                            &nbsp;
                                        </td>
                                    </tr>
                            <?php
                                }
                                // 전체 총계를 $arr_prouct_sum 배열에 담는다 $ak 는 키값으로 총계의 구분 키값이다.
                                foreach($arr_product as $ak=>$av){ $arr_product_sum[$ak] += $av; }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>




        <div class="c_cart_ctrl">
            <div class="left_box">
            <!--
                <a href="#none" onclick="cart_select_delete(); return false;" class="c_btn h30 light line">선택주문 삭제</a>
                <a href="#none" onclick="cart_remove_all(); return false;" class="c_btn h30 light">수기주문 비우기</a>
            -->
            </div>
            <div class="select_num hide">
                선택 상품 <span class="num">( <strong class="js_cart_selected">1</strong> / <?php echo count($arr_product_info); ?> )</span>
            </div>
        </div>

        <div class="c_btnbox ">
            <ul>
                <li><a href="/" class="c_btn h55 black line hide">쇼핑 계속하기</a></li>
                <!-- 장바구니 상품 없을때 구매하기 버튼 숨김 -->
                <?php if(is_login() ){ ?>
                    <!-- 로그인 후 -->
                    <li><a href="#none" onclick="cart_submit();return false;" class="c_btn h55  color">전체 상품 주문</a></li>
                 <?php }else { ?>
                    <!-- 로그인 전 -->
                        <?php // === 비회원 구매 설정 kms 2019-06-24 ==== ?>
                        <?php if (  $none_member_buy === true ) { ?>
                            <li><a href="#none" onclick="cart_confirm_submit();return false;" class="c_btn h55 color ">전체 상품 주문</a></li>
                        <?php } else { ?>
                            <li><a href="#none" onclick="cart_submit();return false;" class="c_btn h55 color "><?php echo ($siteInfo['s_none_member_login_skip'] == 'Y' ? '비회원 ' : null); ?>전체 상품 주문</a></li>
                        <?php } ?>
                        <?php // === 비회원 구매 설정 kms 2019-06-24 ==== ?>
                 <?php } ?>
            </ul>
        </div>

    </form>



<?php }else{ ?>
    <!-- 장바구니 없을때 / 리스트, 총결제금액 div 숨김 -->
    <div class="none">
        <div class="gtxt">수기 주문건이 없습니다.</div>
    </div>


    <div class="c_btnbox ">
        <ul>
            <li><a href="#none" class="c_btn h55  color">전체 상품 주문</a></li>
            <!-- 장바구니 상품 없을때 구매하기 버튼 숨김 -->
        </ul>
    </div>

    <!-- <div class="c_btnbox ">
        <ul>
            <li><a href="/" class="c_btn h55 black line">쇼핑 계속하기</a></li>
            /*장바구니 상품 없을때 구매하기 버튼 숨김*/
        </ul>
    </div> -->
<?php } ?>