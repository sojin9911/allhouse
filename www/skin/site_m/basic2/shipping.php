<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "배송지 관리";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력

?>

<div class="c_section c_mypage">
  <div class="my_ship_wrap">
    <div class="ms_cont_box">
      <ul>
        <li class="ship_addr">. (기본배송지)</li>
        <li class="ship_detail_addr">
          <ul>
            <li>(13494)</li>
            <li>경기 성남시 분당구 판교역로 235 111</li>
          </ul>
        </li>
        <li class="ship_detail_info">
          <ul class="ship_di_tb">
            <li class="shop_di_th">받으실분</li>
            <li class="ship_di_td">홍길동</li>
          </ul>
          <ul class="ship_di_tb">
            <li class="shop_di_th">전화번호</li>
            <li class="ship_di_td"></li>
          </ul>
          <ul class="ship_di_tb">
            <li class="shop_di_th">휴대폰</li>
            <li class="ship_di_td">010-1111-1111</li>
          </ul>
        </li>
        <li class="ship_modi_btn">
          <button type="button">수정</button>
          <button type="button">삭제</button>
        </li>
      </ul>
    </div>
    <button type="button" class="ms_add_addr">배송지 추가</button>
  </div>
</div>
<div class="add_addr_page">
  <div class="ship_add_header">
    배송지 등록
    <button type="button" class="msAdd_page_close">닫기</button>
  </div>

  <div class="ship_add_info">
    <table>
      <colgroup>
        <col width="30%">
      </colgroup>
      <tr>
        <th>배송지 이름</th>
        <td><input type="text"></td>
      </tr>
      <tr>
        <th>받으실 분</th>
        <td><input type="text"></td>
      </tr>
      <tr>
        <th>받으실 곳</th>
        <td class="received_addr_wrap">
          <div class="received_addr">
            <input type="text">
            <button>우편번호</button>
          </div>
          <input type="text">
          <input type="text">
        </td>
      </tr>
      <tr>
        <th>전화번호</th>
        <td><input type="text"></td>
      </tr>
      <tr>
        <th>휴대폰번호</th>
        <td><input type="text"></td>
      </tr>
    </table>
    <label for="default_addr" class="add_shipping_chk">
      <input type="checkbox" name="default_addr" id="default_addr">
      기본 배송지로 설정 합니다. 
    </label>
    
    <div class="ship_add_btn clearfix">
      <button class="ship_add_cancle c_btn h40 light line">취소</button>
      <button class="ship_add_save c_btn h40 color">저장</button>
    </div>
  </div>

</div>

<script>
  $(".ms_add_addr").click(function () {
    if ($(".add_addr_page").hasClass("on")) {
      $(".add_addr_page").removeClass("on");
    } else {
      $(".add_addr_page").addClass("on");
    }
  });
 
  $(".msAdd_page_close").click(function () {
    if ($(".add_addr_page").hasClass("on")) {
      $(".add_addr_page").removeClass("on");
    }
  })
</script>