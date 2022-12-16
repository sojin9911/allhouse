<?php
	# LDD010
	$app_mode = "popup";
	$pass_mode = '2depth';
	include_once("inc.header.php");

?>



<?// 팝업을 위한 css 추가 --- window.open시 1120px로 띄움 ?>
<style>
	body {min-width:1100px;}
	.wrap {padding-bottom:0px;}
</style>



<div class="popup" style="border:0;">

	<div class="pop_title"><strong>상품추가옵션 설정</strong></div>


	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="category_apply_save('1depth_add','');return false;" class="c_btn h27 icon icon_plus_b">옵션추가</a>
				<a href="#none" onclick="submit_alert();return false;" class="c_btn h27 red">옵션저장</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="option_excel_toggle();return false;" class="c_btn icon icon_excel">엑셀 일괄등록</a>
			</div>

			<!-- 엑셀일괄등록 열림 -->
			<div class="open_excel" style="display:none;">

				<form action="_product_addoption.excel_pro.php" name="wFrm" method="post" enctype="multipart/form-data">
				<input type="hidden" name="tran_type" value="ins_excel">
				<input type="hidden" name="pass_mode" value="<?=$pass_mode?>">
				<input type="hidden" name="pass_code" value="<?=$pass_code?>">

				<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
				<table class="table_form">
					<colgroup>
						<col width="140"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th>일괄업로드</th>
							<td>
								<div class="input_file" style="width:300px">
									<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="">
									<div class="fileDiv">
										<input type="button" class="buttonImg" value="파일찾기">
										<input type="file" name="w_excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
									</div>
								</div>
								<a href="/upfiles/normal/option<?php echo $pass_mode; ?>_sample.xls" class="c_btn h27">샘플파일 다운</a>
								<a href="#none" onclick="_product_option_submit();return false;" class="c_btn h27 black">엑셀파일 업로드</a>
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="tip_box">
									<div class="c_tip">엑셀 일괄등록의 경우, 업로드를 함과 동시에 자동으로 정보가 저장 됩니다.</div>
									<div class="c_tip">차수에 맞게 옵션을 등록할 수 있습니다. (샘플파일을 다운받아서 업로드 하시기 바랍니다.)</div>
									<div class="c_tip">1차, 2차, 3차 옵션의 합산명칭이 같은 경우에는 기존 항목이 수정되고, 다를 경우 새롭게 추가 됩니다.</div>
									<div class="c_tip">엑셀97~2003 버전 파일만 업로드가 가능하며, 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록해주십시오.</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>

				</form>

			</div>


		</div>
		<!-- / 리스트 컨트롤영역 -->



		<!-- 옵션데이터 {-->
		<div id="span_option_3depth"></div>
		<!--} 옵션데이터 -->


		<div class="tip_box">
			<div class="c_tip">옵션 정보 입력 후 반드시 [옵션저장]버튼을 클릭하여 변경 및 추가 내용을 저장하시기 바랍니다.</div>
			<!-- <div class="c_tip">옵션은 공급가가 아닌, 판매가로 계산됩니다.</div> -->
			<div class="c_tip">공급가, 판매가 항목은 입력 금액을 적용하는 방식으로 적용됩니다.<br>예) 상품 판매가 : <em>10,000원</em> ,옵션 판매가 : <em>15,000원</em> 일 경우 옵션 적용 시 <em>15,000원</em>이 적용됩니다.</div>
		</div>

	</div>

	<!-- 가운데정렬버튼 -->
	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="submit_alert();return false;" class="c_btn h34 black">옵션저장</a></li>
			<li><a href="#none" onclick="window.close();" class="c_btn h34 black line normal">닫기</a></li>
		</ul>
	</div>

</div>



<script>
// 저장하기
function submit_alert(){
	document.frm_option.submit();
	alert('저장하였습니다.');
	category_apply();
}

// 옵션 추가
function category_apply_save(mode , uid) {
	document.frm_option.submit();
	category_apply(mode , uid);
}

function category_apply(mode , uid) {

	var app_url = '_product_addoption.ajax.php';
	if(document.frm_option) { document.frm_option.submit(); }

	setTimeout(function() {
		$.ajax({
			url: app_url,
			cache: false,
			type: "POST",
			data: "app_mode=popup&pass_code=<?=$pass_code?>&pass_mode=" + mode + "&pass_uid=" + uid,
			success: function(data){
				if(data == "is_subcategory") {
					alert('하위 카테고리가 존재하여 삭제할 수 없습니다.');
				}
				else {
					$("#span_option_3depth").html(data);
					if($("input[name=no_save_num]").val() * 1 == 0 ){
						$("input[name=save_chk]").val("save");
					}	else {
						$("input[name=save_chk]").val("");
					}
				}
			}
		});
	}, 50);
}

// 엑셀저장 # LDD011
function _product_option_submit() {

	if(confirm("엑셀일괄등록을 실행하시겠습니까?")) {

		document.wFrm.submit();
	}
}

// 옵션 엑셀 업로드창 열기/닫기
function option_excel_toggle() {

	var Action = $('.open_excel').hasClass('if_open') ? 'close' : 'open';
	option_excel_status(Action);
}

// 옵션엑셀업로드 펼치기 제어[M] # LDD011
function option_excel_status(Action) {

	var Target = $('.open_excel');

	if(!Action) Action = getCookie('option_excel_open');
	if(Action == 'close') Target.removeClass('if_open').hide();
	else Target.removeClass('if_open').addClass('if_open').show();

	document.cookie = 'option_excel_open='+Action+';';
}

// 지정된 이름의 쿠키를 가져온다. # LDD011
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

// 옵션 SORTing
// _type - U : up , D :down
// _depth - 1, 2, 3
// _uid  -옵션 고유번호
function f_sort(_type , _depth , _uid) {
	if( _type && _depth && _uid ) {

		$("input[name='pass_type']").val(_type);
		$("input[name='pass_depth']").val(_depth);
		$("input[name='pass_uid']").val(_uid);
		document.frm_option.submit();
		category_apply();
	}
	else {
		alert("잘못된 접근입니다. 관리자에게 문의하세요.");
	}
}

// 옵션 삽입
// _depth - 1, 2, 3
// _uid  -옵션 고유번호
function f_insert(_depth , _uid) {
	if( _depth && _uid ) {
		$("input[name='pass_type']").val("insert");
		$("input[name='pass_depth']").val(_depth);
		$("input[name='pass_uid']").val(_uid);
		document.frm_option.submit();
		category_apply();
	}
	else {
		alert("잘못된 접근입니다. 관리자에게 문의하세요.");
	}
}


$(document).ready(function() {

	option_excel_status(); // # LDD011
	category_apply();
});
</script>

<?php include_once("inc.footer.php"); ?>