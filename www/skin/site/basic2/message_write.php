<script type="text/javascript" charset="utf-8" src="/include/smarteditor2/js/service/HuskyEZCreator.js"></script>
<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location hide">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<li>상품문의</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->

		<div class="mypage_section">
			<div class="left_sec">
				<!-- ◆공통탭메뉴 -->
				<?php include_once($SkinData['skin_root'].'/member.header.php'); // -- 공통해더 --  ?>
				<!-- / 공통탭메뉴 -->
			</div>

		
			<div class="right_sec">	
				<div class="right_sec_wrap">
          <!--보낸 쪽지함 start-->					
          <div class="content">
      
            <div class="board_zone_tit">
              <h2>쪽지 보내기</h2>
            </div>

            <div class="message_write_tb">
              <table>
                <tr>
                  <th>받으시는 분</th>
                  <td>올하우스</td>
                </tr>
                <tr>
                  <th>제목</th>
                  <td><input type="text"></td>
                </tr>
                <tr>
                  <th>내용</th>
                  <td>
                    <div id="smarteditor">
                      <textarea name="ir1" id="ir1" rows="20" cols="10"></textarea>
                      <script type="text/javascript">
                        var oEditors = [];
                        
                        $(function(){
                          nhn.husky.EZCreator.createInIFrame({
                              oAppRef: oEditors,
                              elPlaceHolder: "ir1",
                              //SmartEditor2Skin.html 파일이 존재하는 경로
                              sSkinURI: "include/smarteditor2/SmartEditor2Skin.html",  
                              htParams : {
                                  // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                                  bUseToolbar : true,             
                                  // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                                  bUseVerticalResizer : true,     
                                  // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                                  bUseModeChanger : true,         
                                  fOnBeforeUnload : function(){
                                      
                                  }
                              }, 
                           
                              });
                        });
                      </script>
                    </div>
                  </td>
                </tr>
              </table>
            </div>

          </div>
		


          <div class="msg_btn_wrap clearfix">
            <button type="button" class="c_btn btn">이전</button>
            <button type="button" class="c_btn color">저장</button>
          </div>

		    </div>
				
      </div>
	  </div>
  </div>
</div>



