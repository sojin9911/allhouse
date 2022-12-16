<?php

	// 2017-07-11 ::: 보안서버 ::: JJC
	include_once('wrap.header.php');

?>


<form name="frm" method='post' action='_config.ssl.pro.php' ENCTYPE='multipart/form-data'>
<input type='hidden' name='pass_menu' value='_config.ssl.m_form'>


	<!-- 관리자 보안서버 설정 -->
	<div class="group_title"><strong>모바일 사용자 보안서버 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">보안서버 적용페이지</th>
					<td>
						<?=_InputRadio( "_ssl_m_loc" , array("N" , "A" , "P") ,  (!$siteInfo['s_ssl_m_loc'] ? "N" : $siteInfo['s_ssl_m_loc'] ) , "" , array("미사용" , "전체 페이지" , "개인정보 이용 페이지") , "")?>
						<div class="tip_box">
							<?=_DescStr("개인정보 이용 페이지 : 로그인, 회원가입, 게시판, 주문 결제페이지 등이며 , 아래의 모바일 개인정보 이용페이지 섹션에 상세히 기술하였습니다.");?>
						</div>						
					</td>
				</tr>

				<tr class="auth_view" style="display:none;">
					<th class="ess">보안서버 추가 적용페이지</th>
					<td >

						<a href="#none" onclick="page_add();" class="c_btn h27 black">보안서버 페이지 추가하기</a>

						<div style='clear:both; padding-top:5px;' ID="page_area">

							<?PHP
								$page_ex = explode("§" , $siteInfo['s_ssl_m_page']);
								echo "<input type='hidden' name='page_add_cnt' value='" . ($siteInfo['s_ssl_m_page'] ? sizeof($page_ex) : 1) . "'>";
								if(sizeof($page_ex) > 0 ) {
									echo "
										<!-- ● 데이터 리스트 --><table class='table_list'>
											<colgroup><col width='50'><col width='*'><col width='70'></colgroup>
											<thead>
												<tr>
													<th scope='col'>순번</th>
													<th scope='col'>페이지명</th>
													<th scope='col'>괸리</th>
												</tr>
											</thead> 
											<tbody>
									";
									foreach( $page_ex as $k=>$v ){
										echo '
											<tr data-key="'. ($k + 1) .'">
												<td >'. ($k + 1) .'</td>
												<td class="t_left"><input type="text" name="page_value['. ($k + 1) .']" class="design" value="'. $v .'" style="width:100%" ></td>
												<td ><div class="lineup-vertical"><a href="#none" onclick="page_delete('. ($k + 1) .');" class="c_btn h22 gray">삭제</a></div></td>
											</tr>
										';
									}
									echo "
											</tbody> 
										</table>
									";
								}
							?>
						</div>
						<div class="tip_box">
							<?=_DescStr("보안서버 적용페이지가 미사용이 아닌 경우 보안서버에 추가로 적용하고자 하는 페이지를 설정할 수 있습니다.")?>
							<?=_DescStr("pn 값을 입력하세요. 예를들어 http://domain.com/?pn=member.join.agree 일 경, pn의 값에 해당하는 <strong>member.join.agree</strong>을 입력하시기 바랍니다. ")?>
							<?=_DescStr("삭제 후 확인버튼을 클릭하여야 반영됩니다.")?>
						</div>

						<SCRIPT LANGUAGE="JavaScript">
							// 이미지 추가
							function page_add(){
								var _cnt = $("input[name='page_add_cnt']").val() * 1 + 1;
								var _str = ""; 
								_str += "<tr data-key='"+ _cnt  +"'>";
								_str += "	<td >"+ _cnt  +"</td>";
								_str += "	<td class='t_left'><input type='text' name='page_value["+ _cnt +"]' class='design' value='' style='width:100%' ></td>";
								_str += "	<td ><div class='lineup-vertical'><a href='#none' onclick='page_delete("+ _cnt +");' class='c_btn h22 gray'>삭제</a></div></td>";
								_str += "</tr>";
								$("#page_area tr:last").after(_str);
								$("input[name='page_add_cnt']").val(_cnt);
							}

							// 삭제 - 마지막 element 삭제
							function page_delete(_idx){
								//if( confirm("정말 삭제하시겠습니까?") ){
									$("#page_area tr[data-key='"+_idx+"']").remove();
									var _cnt = $("input[name='page_add_cnt']").val() * 1 - 1;
									$("input[name='page_add_cnt']").val(_cnt);
								//}
							}

						<?if(sizeof($page_ex) == 0 ) {?>
							page_add();
						<?}?>

						</SCRIPT>

					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- 검색영역 -->




	<?php
		//보안서버 설정 정보
		include_once("_config.ssl.default_inc.php");
	?>



	<!-- 관리자 보안서버 설정 -->
	<div class="group_title"><strong>모바일 개인정보 이용페이지</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>모바일 개인정보 이용페이지</th>
					<td >
						<?PHP
							if(sizeof($arr_ssl_m_page) > 0 ) {
								echo "
									<!-- ● 데이터 리스트 --><table class='table_list'>
										<colgroup><col width='400'><col width='*'><col width='70'></colgroup>
										<thead>
											<tr>
												<th scope='col'>페이지명</th>
												<th scope='col'>파일명</th>
											</tr>
										</thead> 
										<tbody>
								";
								foreach( $arr_ssl_m_page as $k=>$v ){
									echo '
										<tr>
											<td style="text-align:left; margin-left:5px;">'. $v .'</td>
											<td style="text-align:left; margin-left:5px;">'. $k .'</td>
										</tr>
									';
								}
								echo "
										</tbody> 
									</table>
								";
							}
						?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- 검색영역 -->

	<?=_submitBTNsub()?>

</form>



<script>
	/*  ON/OFF ---------- */
	var onoff = function() {
		if($("input[name='_ssl_m_loc']").filter(function() {if (this.checked) return this;}).val() == "P") {
			$(".auth_view").show();
		}
		else {
			$(".auth_view").hide();
		}
	}
	onoff();
	$("input[name='_ssl_m_loc']").click(function() {onoff();});
	/*  ON/OFF ---------- */
</script>




<?php 
	
	include_once('wrap.footer.php'); 

?>