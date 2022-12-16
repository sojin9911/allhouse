<div class="brand_wrap layout_fix">
  <div class="brand_top">
    <h2>브랜드</h2>
    <div class="top_search">
      <input type="text" placeholder="브랜드명 검색"><button><p><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/btn_top_search_gray.png" alt=""></p></button>
    </div>
    <script>
      //탭메뉴 스크립트입니다
      window.onload = function(){

        //함수 호출 반복문
        for(let i = 0; i < $('.top_search-btn').length; i++){
            tabOpen(i); 
        }

        //함수에 보관
        function tabOpen(e){
            $('.top_search-btn').eq(e).click(function(){
                $('.top_search-btn').removeClass('search-btn-change');
                $('.brand_bottom').removeClass('bottom-diplay');
                $('.top_search-btn').eq(e).addClass('search-btn-change');
                $('.brand_bottom').eq(e).addClass('bottom-diplay');
            });
        }
      }
    </script>
    <div class="top_search-list">
      <ul class="search_btn-ul">
        <li><button class="top_search-btn search-btn-change">전체</button></li>
        <li><button class="top_search-btn">ㄱ</button></li>
        <li><button class="top_search-btn">ㄴ</button></li>
        <li><button class="top_search-btn">ㄷ</button></li>
        <li><button class="top_search-btn">ㄹ</button></li>
        <li><button class="top_search-btn">ㅁ</button></li>
        <li><button class="top_search-btn">ㅂ</button></li>
        <li><button class="top_search-btn">ㅅ</button></li>
        <li><button class="top_search-btn">ㅇ</button></li>
        <li><button class="top_search-btn">ㅈ</button></li>
        <li><button class="top_search-btn">ㅊ</button></li>
        <li><button class="top_search-btn">ㅋ</button></li>
        <li><button class="top_search-btn">ㅌ</button></li>
        <li><button class="top_search-btn">ㅍ</button></li>
        <li><button class="top_search-btn">ㅎ</button></li>
      </ul>
      <ul class="search_btn-ul search-list_2">
        <li><button class="top_search-btn">기타</button></li>
        <li><button class="top_search-btn">A</button></li>
        <li><button class="top_search-btn">B</button></li>
        <li><button class="top_search-btn">C</button></li>
        <li><button class="top_search-btn">D</button></li>
        <li><button class="top_search-btn">E</button></li>
        <li><button class="top_search-btn">F</button></li>
        <li><button class="top_search-btn">G</button></li>
        <li><button class="top_search-btn">H</button></li>
        <li><button class="top_search-btn">I</button></li>
        <li><button class="top_search-btn">J</button></li>
        <li><button class="top_search-btn">K</button></li>
        <li><button class="top_search-btn">L</button></li>
        <li><button class="top_search-btn">M</button></li>
        <li><button class="top_search-btn">N</button></li>
        <li><button class="top_search-btn">O</button></li>
        <li><button class="top_search-btn">P</button></li>
        <li><button class="top_search-btn">Q</button></li>
        <li><button class="top_search-btn">R</button></li>
        <li><button class="top_search-btn">S</button></li>
        <li><button class="top_search-btn">T</button></li>
        <li><button class="top_search-btn">U</button></li>
        <li><button class="top_search-btn">V</button></li>
        <li><button class="top_search-btn">W</button></li>
        <li><button class="top_search-btn">X</button></li>
        <li><button class="top_search-btn">Y</button></li>
        <li><button class="top_search-btn">Z</button></li>
      </ul>
    </div>
  </div>
  <div class="brand_bottom bottom-diplay">
    <div class="bottom_row1">
      <ul>
        <li><a href="#">부르뎅</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
      </ul>
      <ul>
        <li><a href="#">마마</a></li>
      </ul>
      <ul>
        <li><a href="#">포키</a></li>
      </ul>
      <ul>
        <li><a href="#">탑랜드</a></li>
      </ul>
      <ul>
        <li><a href="#">원아등복</a></li>
      </ul>
    </div>
    <div class="bottom_row2">
      <ul>
        <li><a href="#">크레용</a></li>
      </ul>
      <ul>
        <li><a href="#">페인트타운</a></li>
      </ul>
      <ul>
        <li><a href="#">8</a></li>
      </ul>
      <ul>
        <li><a href="#">아이라인</a></li>
      </ul>
    </div>
  </div>
  <div class="brand_bottom">
    <div class="bottom_row1">
      <ul>
        <li><a href="#">부르뎅</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
      </ul>
      <ul>
        <li><a href="#">마마</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
      </ul>
      <ul>
        <li><a href="#">포키</a></li>
      </ul>
      <ul>
        <li><a href="#">탑랜드</a></li>
      </ul>
      <ul>
        <li><a href="#">원아등복</a></li>
      </ul>
    </div>
    <div class="bottom_row2">
      <ul>
        <li><a href="#">크레용</a></li>
      </ul>
      <ul>
        <li><a href="#">페인트타운</a></li>
      </ul>
      <ul>
        <li><a href="#">8</a></li>
      </ul>
      <ul>
        <li><a href="#">아이라인</a></li>
      </ul>
    </div>
    
  </div>
  <div class="brand_bottom">
    <div class="bottom_row1">
      <ul>
        <li><a href="#">부르뎅</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
      </ul>
      <ul>
        <li><a href="#">마마</a></li>
        <li><a href="#">데님펌프</a></li>
      </ul>
      <ul>
        <li><a href="#">포키</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
      </ul>
      <ul>
        <li><a href="#">탑랜드</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
        <li><a href="#">데님펌프</a></li>
      </ul>
      <ul>
        <li><a href="#">원아등복</a></li>
      </ul>
    </div>
    <div class="bottom_row2">
      <ul>
        <li><a href="#">크레용</a></li>
      </ul>
      <ul>
        <li><a href="#">페인트타운</a></li>
      </ul>
      <ul>
        <li><a href="#">8</a></li>
      </ul>
      <ul>
        <li><a href="#">아이라인</a></li>
      </ul>
    </div>
    
  </div>




</div>