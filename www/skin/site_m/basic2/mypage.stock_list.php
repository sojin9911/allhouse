<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "미입고 내역";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력

?>

<div class="c_section c_mypage">
  <div class="sl_wrap">
    <h3>미입고 내역 총 <span class="cnt_clr">2</span>건</h3>
    <ul class="sl_table">
      <li>
        <div class="img_wrap">
          <img src="<?php echo $SkinData['skin_url']; ?>/images/sample/thumb2.jpg" alt="">
        </div>
      </li>
      <li>
        <ul>
          <li class="order_number"><a href="">28371-34235-74800</a></li>
          <li class="goods_tit">벌룬곰세트</li>
          <li>주문수량 : 4</li>
          <li>재고량 : 0</li>
          <li>부족재고 : 4</li>
          <li>품절여부 : 품절</li>
        </ul>
      </li>
    </ul>
    <ul class="sl_table">
      <li>
        <div class="img_wrap">
          <img src="<?php echo $SkinData['skin_url']; ?>/images/sample/thumb2.jpg" alt="">
        </div>
      </li>
      <li>
        <ul>
          <li class="order_number"><a href="">28371-34235-74800</a></li>
          <li class="goods_tit">벌룬곰세트</li>
          <li>주문수량 : 4</li>
          <li>재고량 : 0</li>
          <li>부족재고 : 4</li>
          <li>품절여부 : 품절</li>
        </ul>
      </li>
    </ul>
  </div>
</div>