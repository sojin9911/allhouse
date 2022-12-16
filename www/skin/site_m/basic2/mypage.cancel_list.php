<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "취소/반품/교환 처리 내역";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력

?>
<div class="sel_box_area">
  <div class="select">
    <select name="" id="">
      <option value="">오늘</option>
      <option value="" selected>최근 7일</option>
      <option value="">최근 15일</option>
      <option value="">최근 1개월</option>
      <option value="">최근 3개월</option>
      <option value="">최근 6개월</option>
      <option value="">최근 1년</option>
    </select>
  </div>
</div>
<div class="c_section c_mypage">
  <div class="my_cl_wrap">
    <div class="mcl_list_area">
      <button type="button" class="c_btn h40 light line">더보기</button>
    </div>
  </div>
</div>