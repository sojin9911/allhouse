<?php
	$app_mode = 'popup';
	include_once('inc.header.php');
?>

<div class="popup" >
	<div class="pop_title"><strong>일괄업로드</strong></div>
	<?php //KAY :: 2021-05-31 :: form submit처리 -> id 설정후 jquery에서리처리 ?>
	<form name="frm" method="post" action="" enctype="multipart/form-data" >
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="">일괄업로드</th>
						<td>
							<div class="input_file" style="width:300px">
								<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="">
								<div class="fileDiv">
									<input type="button" class="buttonImg" value="파일찾기">
									<input type="file" name="excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value; return false;">
								</div>
							</div>

							<a href="<?php echo IMG_DIR_NORMAL; ?>/product-sample<?php echo ($SubAdminMode === true ? '-sub' : null) ?>.xls" onclick=""  class="c_btn h27">샘플파일 다운</a>

							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="tip_box">
								<?php
									// KAY :: 2021-05-31 :: 상품쿠폰타입, 옵션 문구 추가
									echo _DescStr('
										상품 등록 시 <br>
										-	상품 카테고리는 1개만 지정 가능하며 등록되어있지 않은 카테고리는 제외 됩니다.<br>
										-	상품 옵션은 특수문자(§,>,|)로 구분되니 옵션 등록시 특수문자를 제외하고 입력 해주시기 바랍니다.<br>
										-	상품쿠폰 타입 입력 시 상품쿠폰금액은 "할인금액", 상품쿠폰율은 "할인율"로 입력 해주시기 바랍니다.<br>
										-	상품은 5000개 미만으로 업로드 해주시기 바랍니다.
									');
								?>
								<?php echo _DescStr('상품 수정 시 : 상품 카테고리 설정은 무시되며, 상품 분류 추가/변경은 업로드 수정/확인에서 가능합니다.');?>
								<?php echo _DescStr('파일은 최대 '. $MaxUploadSize .'까지 업로드 가능 하며, 용량에 따라 다소시간이 걸릴 수 있습니다.');?>
								<?php echo _DescStr('상품 사용 정보, 업체 이용 정보, 상품 상세설명(PC/모바일), 주문확인서 주의사항의 내용은 엔터를 제외 하고 HTML로 입력 바랍니다.');?>
								<?php echo _DescStr('엑셀 내용 중 엔터는 생략 하시고, 금액 또는 수수료의 %, 콤마(,), 원 등의 기호를 생략 해주시기 바랍니다.');?>

								<?php
									// SSJ : 상품이미지 등록방법 안내 문구 수정 : 2020-12-28
									echo _DescStr('
										상품이미지 등록 방법<br>
										1. 이미지 업로드 시 : '. IMG_DIR_PRODUCT .' 폴더에 미리 업로드를 하시고 엑셀에는 파일명과 확장자만 입력 바랍니다.<br>
										2. 외부 이미지 사용 시 : 엑셀에 http(s)://를 포함한 이미지 경로를 입력해 주시기 바랍니다.<br>
										※ 외부 이미지 사용 시 보안서버(https://)를 사용하는 사이트는 보안서버(https://)가 적용된 사이트의 이미지만 사용 가능합니다.
									');
								?>
								<?php // KAY :: 2021-05-31 :: 엑셀 버전 업그레이드 문구 수정
									echo _DescStr('일괄업로드는 파일등록 후 업로드저장 버튼을 누르면 바로 처리됩니다.');?>
								<?php echo _DescStr('상품 엑셀다운로드 후 업로드 시 엑셀을 xlsx, xls로 저장한 후 업로드해주시기 바랍니다.');?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php // KAY :: 2021-05-31 :: 옵션 프로그래스바 생성 - 상품 등록 끝난 후 적용 ?>
		<div id="progressbar" style="display:none; margin:20px;"><div class="progress-label"> 상품을 업로드 중입니다. 수초 ~ 수분까지 진행될 수 있습니다. 잠시만 기다려 주시기 바랍니다....</div></div>

		<div class="c_btnbox">
			<ul>
 				<li><a id="upload_submit" href="#none" onclick="return false;" class="c_btn h34 black">업로드 저장</a></li>
				<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal">닫기</a></li>
			</ul>
		</div>
	</form>
</div>

<?// --------------------- progress bar 적용 --------------------- //?>
<style>
	.ui-progressbar {position: relative;}
	.progress-label {position: absolute;left: 25%; top: 4px;font-weight: bold;text-shadow: 1px 1px 0 #fff;}
</style>

<script>
	var progressbar = $( "#progressbar" ), progressLabel = $( ".progress-label" );

	progressbar.progressbar({
		value: false,
		change: function() { progressLabel.text( progressbar.progressbar( "value" ) + "%" ); progressLabel.css("left", "50%"); },
		complete: function() {	progressLabel.text( "Complete!" );	}
	});

	function progress(uid) {
		if(uid){
			progressbar = $( "#progressbar" ).show();
			$.ajax({
				data: {'_mode':'upload_chk' , 'uid':uid },
				type: 'POST', cache: false, dataType:'json',
				url: '/totalAdmin/_product.pro.php',
				success: function(data) {

					// 옵션 업로드 개수 체크
					var total  = data['total'] * 1; // 총 업로드 개수
					var cnt = data['cnt'] * 1; // 남은 옵션 개수

					progressbar.progressbar( "value",  Math.round( (total - cnt) * 100  / total  ) + 1 );

					// 처리할 옵션이 남았을 경우 재실행
					if ( cnt > 0 ) { setTimeout( function(){ progress(uid); }, 500 ); }

					// 임시 옵션 관리에서 값이 다 삭제 됐을 경우 업로드 완료
					if( cnt == 0 ) {
						alert("상품 업로드를 완료하였습니다.");
						opener.location.reload();
						window.close();
					}
				}
			});
		}else{
			progressbar.progressbar( "value",  0 );
			$( "#progressbar" ).hide();
			$( "#upload_submit" ).show(); // 업로드 저장 버튼 숨김
		}
	}

	// KAY :: 2021-06-01 :: 일괄업로드 저장 클릭 시 프로그래스바 노출
	$("#upload_submit").click(function(){
		if(confirm("상품 또는 옵션 형태에 따라 시간이 걸리 수 있습니다. \n\n정말 업로드하시겠습니까?")){
			$( "#progressbar" ).show(); // 프로그래스바 노출
			$( "#upload_submit" ).hide(); // 업로드 저장 버튼 숨김
			$("form[name=frm]").attr({"action":"_product.upload.pro.php" , "target":"common_frame"});
			document.frm.submit();
			return true;
		}
	});

</script>

<?php
	include_once('inc.footer.php');
?>