<?PHP
	// --- KAY :: 에디터 이미지 관리 :: 2021-06-10 ---
	//		에디터 이미지 개별관리 팝업 파일

	$app_mode = "popup";
	include_once("inc.header.php");

	// -- 데이터가 있는 경우 처리 ---
	$res = _MQ_assoc("
		SELECT 
			eiu.eiu_uid,	eif.eif_img, eif.eif_rdate,eif.eif_uid
		FROM smart_editor_images_files as eif
		LEFT JOIN smart_editor_images_use as eiu on (eiu.eiu_eifuid = eif.eif_uid )
		where 
			eiu.eiu_datauid = '{$_uid}'	and eiu.eiu_tablename ='{$tn}'
	");

	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스
	$TotalCount =  sizeof($res);

?>

<?// 팝업을 위한 css 추가 --- window.open시 1120px로 띄움 ?>
<style>
	body {min-width:1100px;}
	.wrap {padding-bottom:0px;}
</style>

<div class="popup" style="border:0;">

	<div class="pop_title"><strong>에디터 이미지 관리설정</strong></div>
	<div class="tip_box">
		<div class="c_tip black">이미지 수정 시 동일 확장자로만 등록이 가능하며, 다른 사용처 이미지도 수정됩니다.</div>
		<div class="c_tip black">용량이 큰 이미지가 있을 경우 로딩 속도가 느려질 수 있습니다.</div>
		<div class="c_tip black"><strong>이미지 삭제 시 파일은 삭제되지 않습니다. 에디터 사용하는 곳에서 변경, 혹은 파일삭제를 하여주시기 바랍니다.</strong></div>
		<div class="c_tip black">파일삭제를 원하실 경우 ( 환경설정 > 운영 관리 설정 > 에디터 등록 이미지 관리 ) 부분에서 해당 파일을 삭제하실 수 있습니다.</div>
		<div class="c_tip black">파일관리를 클릭하시면 위의 환경설정페이지로 이동합니다.</div>
	<div>

	<form name="frm_editimginfo" method="post" enctype="multipart/form-data"  target= "common_frame">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_edit_uid" value=""><!-- 에디터 이미지 고유번호 -->
	<input type="hidden" name="_uid" value="<?php echo $_uid;?>"><!-- 각 에디터 이미지 사용하는 곳의 고유번호 -->
	<input type="hidden" name="_tn" value="<?php echo $tn;?>"><!-- 각 에디터 이미지 사용하는 테이블명 -->

		<!-- ● 데이터 리스트 -->
		<div class="data_list">
			<table class="table_list">
				<colgroup>
					<col width="60"><col width="100"><col width="*"><col width="110"><col width="200">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO</th>
						<th scope="col">이미지</th>
						<th scope="col">파일업로드</th>
						<th scope="col">변경일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php

					 if(sizeof($res) > 0){
						foreach($res as $k=>$v){
							
							// 에디터 이미지 절대경로
							$edit_img_link = IMG_DIR_SMARTEDITOR.$v['eif_img'];

							$edit_img_link = str_replace('%2F','/',urldecode($edit_img_link)); // 한글명 파일을 읽기 위한 변환시 디코딩

							if(@file_exists($_SERVER['DOCUMENT_ROOT'].$edit_img_link)){
					
								$edit_img_link = str_replace('%2F','/',urlencode($edit_img_link)); // 한글명 파일을 읽기 위한 변환시 디코딩
								$edit_file_tag = '<img src="'.$edit_img_link.'?v='.time().'">';
							}
							else{
								$edit_file_tag = $edit_img_link = ''; // 태그, 링크정보 초기화
							}

							$edit_img_link = iconv("EUC-KR","UTF-8",$edit_img_link);  //  한글명 파일을 읽기위한 변환

							$img_tag_full = '<img src="'.$system['__url'].$edit_img_link.'">'; //소스코드 복사 Full URL
							$img_url_full = $system['__url'].$edit_img_link; // URL복사 - Full URL
							$edit_url_link = base64_encode(json_encode(array($edit_file_tag, $img_tag_full, $edit_img_link,$img_url_full)));  // 이미지 경로 배열
							// --- 소스코드,url복사 배열 암축 암호화 ---

							$_num = $TotalCount - $count - $k ;
					?>
							<tr>
								<td><?php echo $_num; ?></td>
								<td class="img80"><?php echo $edit_file_tag; ?></td>
								<td>
									<?php // 파일 수정 시 파일입력 text창 ?>
									<?php echo _PhotoForm( '../upfiles/smarteditor', '_img_edit_' . $v['eiu_uid'] ,'', 'style="width:250px"');?>
									<a href="#none" onclick="upload(<?php echo $v['eiu_uid']; ?>);return false;" class="c_btn h27 blue img_modify">수정</a>
									
									<?php //개별 삭제?>
									<a href="#none" onclick="del('_config.editor_img.pop.pro.php?_mode=delete&_edit_uid=<?php echo $v['eiu_uid']; ?>&_uid=<?php echo $_uid; ?>&_tn=<?php echo $tn;?>'); return false;" class="c_btn h27 gray">삭제</a>
									<span class="bar"></span>

									<a href="_config.editor_img.download.php?uid=<?php echo $v['eif_uid']; ?>" class="c_btn h27" target="common_frame">다운로드</a>	<?php // 이미지 개별 다운로드 ?>
									<a href="_config.editor_img.list.php" target="_blank" class="c_btn h27 ">파일관리</a> <?php // 이미지 파일 삭제 시 체크?>
								</td>
								<?php // 에디터 이미지 변경일 ?>
								<td class="date_time"><?php echo date('Y-m-d' , strtotime($v['eif_rdate'])); ?><strong class="t_light"><?php echo date('H:i:s' , strtotime($v['eif_rdate'])); ?></strong></td>
								<td>
									<div class="lineup-vertical wrap_img_btn_all" data-uid="<?php echo $v['eiu_uid']; ?>" data-url="<?php echo $edit_url_link;?>">
										<a href="#none" onclick="img_btn_all('preview' , <?php echo $v['eiu_uid']; ?>)" class="c_btn h22 gray" >미리보기</a>
										<a href="#none" onclick="img_btn_all('tag_copy' , <?php echo $v['eiu_uid']; ?>)"  class="c_btn h22 tag_btn" >소스복사</a>
										<a href="#none" onclick="img_btn_all('url_copy' , <?php echo $v['eiu_uid']; ?>)" class="c_btn h22 url_btn" >URL복사</a>

									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>
			<?php if(sizeof($res) < 1){ ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>
		</div>

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="window.close();" class="c_btn h34 black line normal">닫기</a></li>
			</ul>
		</div>
	</form>
</div>


<?// KAY :: 2021-06-07 ::  미리보기, 소스복사, 태그복사(url복사) 팝업 ?>
<div class="popup _img_preview" style="display:none; width:700px; height:450px;background:#fff; " >
	<div class="pop_title">에디터 이미지 관리<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

	<!-- ● 내부탭 -->
		<div class="c_tab">
			<ul>
				<li><a href="#none" class="btn tab_menu" data-idx="preview" ><strong>미리보기</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="tag_copy"><strong>소스복사</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="url_copy"><strong>URL복사</strong></a></li>
			</ul>
		</div>

		<table class="table_form tab_conts" data-idx="preview">
			<tbody>
				<tr>
					<td>
						<div style="overflow:auto; height:258px;">
							<img id="preview_img" src="" class="design " data-idx="'preview_img'" style="max-width:100%">
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="table_form tab_conts" data-idx="tag_copy">
			<tbody>
				<tr>
					<td>
						<div>
							<span class="text" style="margin:10px;">절대경로</span>
								<input type="text"  id="tag_img"  data-idx="tag_img" class="design" style="width:90%;height:30px;">
								<a href="#none"  data-clipboard-target="#tag_img" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
						</div>
						<div>
							<span class="text" style="margin:10px;">도메인경로</span>
								<input type="text"  id="tag_img_full"  data-idx="tag_img_full" class="design" style="width:90%;height:30px;">
								<a href="#none"  data-clipboard-target="#tag_img_full" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="table_form tab_conts" data-idx="url_copy">
			<tbody>
				<tr>
					<td>
						<div>
							<span class="text" style="margin:10px;">절대경로</span>
								<input type="text"  id="url_img"  data-idx="url_img" class="design" style="width:90%;height:30px;">
								<a href="#none"  data-clipboard-target="#url_img" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
						</div>
						<div>
							<span class="text" style="margin:10px;">도메인경로</span>
								<input type="text"  id="url_img_full"  data-idx="url_img_full" class="design" style="width:90%;height:30px;">
								<a href="#none"  data-clipboard-target="#url_img_full" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
</div>





<script>

function img_btn_all(btn_type , eiuid){

	// 변수설정
	var data = $(".wrap_img_btn_all[data-uid='"+ eiuid +"']").data('url');
	var json_obj = atob(data); // 미리보기,소스복사,url복사에 링크 encode풀어주기
	var url_obj = JSON.parse(json_obj); // 미리보기,소스복사,url복사에 링크배열

	// --- 레이어 팝업 띄우기 ---
	$('._img_preview').lightbox_me({centered: true, closeEsc: false,onLoad: function() {},onClose: function(){}});
	// --- 레이어 팝업 띄우기 ---
	
	// --- 레이어 팝업 내용 넣기 ---
	$("#preview_img").attr("src" , url_obj[2]+'?v='+Date.now());// 미리보기 적용
	$('._img_preview .tab_conts[data-idx="tag_copy"] input[type="text"][data-idx="tag_img"]').val(url_obj[0]); // 소스복사 - 절대경로 이미지태그
	$('._img_preview .tab_conts[data-idx="tag_copy"] input[type="text"][data-idx="tag_img_full"]').val(url_obj[1]); // 소스복사 - Full URL 이미지태그
	$('._img_preview .tab_conts[data-idx="url_copy"] input[type="text"][data-idx="url_img"]').val(url_obj[2]); // URL 복사 - 절대경로
	$('._img_preview .tab_conts[data-idx="url_copy"] input[type="text"][data-idx="url_img_full"]').val(url_obj[3]);// URL 복사 - Full URL
	// --- 레이어 팝업 내용 넣기 ---

	// --- 레이어 팝업 내 탭 지정하기 ---
	// 전부 막기
	$('._img_preview .c_tab li').removeClass("hit"); // 탭 히트 전부 없애기
	$('._img_preview .tab_conts').hide(); // 탭 영역 전부 막기

	// 선택 열기
	$("._img_preview .c_tab .tab_menu[data-idx='"+ btn_type +"']").parent("li").addClass("hit"); // 선택 탭 히트 적용
	$("._img_preview .tab_conts[data-idx='"+ btn_type +"']").show();// 선택 탭 영역 열기
	// --- 레이어 팝업 내 탭 지정하기 ---

}

// 레이어 팝업 내 탭 클릭
$(document).on('click', '.tab_menu', function() {
	var btn_type = $(this).data('idx'); // 탭 타입 설정

	// 전부 막기
	$('._img_preview .c_tab li').removeClass("hit"); // 탭 히트 전부 없애기
	$('._img_preview .tab_conts').hide(); // 탭 영역 전부 막기

	// 선택 열기
	$("._img_preview .c_tab .tab_menu[data-idx='"+ btn_type +"']").parent("li").addClass("hit"); // 선택 탭 히트 적용
	$("._img_preview .tab_conts[data-idx='"+ btn_type +"']").show();// 선택 탭 영역 열기
});


// 에디터 이미지 수정
// edit_uid - 에디터 이미지 고유번호 
function upload(edit_uid){
	// 파일수정 시 파일 값이 있는 지 체크하는 변수
	var app_img = $(".realFile[name='_img_edit_"+edit_uid+"']").val();

	// 파일 체크 , 파일이 없는 경우 수정 x
	if(!app_img){
		alert("수정파일이 존재하지 않습니다. 파일을 입력해주시기바랍니다.");
		return false;
	}
	// 파일 변경 할 경우 팝업pro에서 실행
	if(confirm("수정 시 이미지명은 동일하며 파일만 변경됩니다. 변경하시겠습니까?")){
		$("form[name=frm_editimginfo]").children("input[name=_mode]").val("modify");
		$("form[name=frm_editimginfo]").children("input[name=_edit_uid]").val(edit_uid);
		$("form[name=frm_editimginfo]").attr('action' , '_config.editor_img.pop.pro.php');
		document.frm_editimginfo.submit();
	}
}

</script>


<?PHP
	include_once("inc.footer.php");
?>