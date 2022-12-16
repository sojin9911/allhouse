<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "쪽지 보내기";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력

?>
<div class="c_section c_mypage">
  <ul class="msg_write">
    <li>올하우스</li>
    <li>
      <input type="text" placeholder="제목">
    </li>
    <li>
      <textarea name="" id="" cols="30" rows="10"></textarea>
    </li>
  </ul>
  <div class="msg_write_btm">
    <input type="file" name="" id="">
    <button class="bg_btn">보내기</button>
  </div>
</div>