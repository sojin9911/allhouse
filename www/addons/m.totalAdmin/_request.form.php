<?php

	$pass_menu = $_REQUEST['pass_menu'] ? $_REQUEST['pass_menu'] : "inquiry";

	// 페이지 표시
	$app_current_page_name = "1:1문의관리";
	include dirname(__FILE__)."/wrap.header.php";


	if( $_mode == "modify" ) {
		$row = _MQ(" select * from smart_request where r_uid='{$_uid}' ");
	}

	if( !$pass_menu ) {
		error_msg("메뉴를 선택해주시기 바랍니다.");
	}


?>


<form name="frm" method="post" action="_request.pro.php">
<input type=hidden name=_mode value='<?=$_mode?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type=hidden name=_uid value='<?=$_uid?>'>
<input type=hidden name=pass_menu value='<?=$pass_menu?>'>
<input type=hidden name=_menu value='<?=$pass_menu?>'>



	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container">
		
		<!-- ●●●●● 데이터폼 -->
		<div class="data_form">
			
			
			
			<!-- 테이블형 div colspan 하고 싶으면 ul에 if_full -->
			<div class="like_table">	
<?if( in_array($row[r_menu] , array("inquiry")) ) {?>
				<ul class="">
					<li class="opt ess">회원ID</li>
					<li class="value">
						<!-- 인풋없이 글만 들어갈경우 -->
						<div class="only_txt"><?=$row[r_inid]?>(<?=$row[name]?>)</div>
					</li>
				</ul>
<? } ?>
				<ul class="">
					<li class="opt ess">제목</li>
					<li class="value">
						<input type="text" name="_title" class="input_design" value="<?=stripslashes(strip_tags($row[r_title])) ?>" />
					</li>
				</ul>
				<ul class="if_full ">
					<li class="opt ess">문의내용</li>
					<li class="value">
						<textarea cols="" rows="" class="textarea_design" name="_content"><?=stripslashes($row[r_content])?></textarea>
					</li>
				</ul>
				<ul class="">
					<li class="opt ess">답변상태</li>
					<li class="value">
						<label><input type="radio" name="_status" value="답변대기" <?=($row[r_status]=="답변대기" || !$row[r_status] ? "checked" : "")?> />답변대기</label>
						<label><input type="radio" name="_status" value="답변완료" <?=($row[r_status]=="답변완료" ? "checked" : "")?> />답변완료</label>
					</li>
				</ul>
				<ul class="if_full ">
					<li class="opt ">관리자답변(메모)</li>
					<li class="value">
						<textarea cols="" rows="" class="textarea_design" name="_admcontent"><?=stripslashes($row[r_admcontent])?></textarea>
					</li>
				</ul>
				<ul class="">
					<li class="opt ">첨부파일</li>
					<li class="value">
						 <!-- ●●●●● 도움말 공간 dt는 주황색 dd는 파란색 -->
						<div class="guide_box">
							<dl>
								<dd>첨부파일은 PC버전에서 확인/등록가능합니다.</dd>
							</dl>
						</div>
						<!-- 도움말 공간 -->
					</li>
				</ul>
				<ul class="">
					<li class="opt">참고사항</li>
					<li class="value">
						<!-- 텍스트만 나올경우 -->
						<div class="only_txt">문의등록시간 : <?=$row[r_rdate]?></div>
					</li>
				</ul>
			</div>

		</div>
		<!-- / 데이터폼 -->

	</div>
	<!-- / 내용들어가는 공간 -->
	
	

	<!-- ●●●●●●●●●● 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><input type="submit" class="btn_lg_red" value="<?=( $_mode == "modify" ? "수정" : "등록")?>"></span></li>	
			<li><span class="button_pack"><a href="_request.list.php?pass_menu=<?=$pass_menu?>&<?=enc('d' , $_PVSC)?>" class="btn_lg_white">목록으로</a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->

</form>
<script type="text/javascript">

    $(document).ready(function(){
		// -  validate --- 
        $("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
            rules: {
				_title: { required: true},//제목
				_content: { required: true},//문의내용
				_status: { required: true}//답변상태
            },
            messages: {
				_title: { required: "제목을 입력하시기 바랍니다."},//제목
				_content: { required: "문의내용을 입력하시기 바랍니다."},//문의내용
				_status: { required: "답변상태를 선택하시기 바랍니다."}//답변상태				
            }
        });
		// - validate --- 
	});

</script>


<?php

	include dirname(__FILE__)."/wrap.footer.php";

?>