<?php
	# KAY :: 에디터 이미지 관리 :: 파일 생성
	// 에디터 이미지 전체관리 list 
	include_once('wrap.header.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }}
		else {$_PVS .= "&$key=$val";}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	// 검색 조건
	$s_query = '';
	if( $pass_sdate !="" ) { $s_query .= " and eif_rdate >= '{$pass_sdate} 00:00:00' "; } //이미지 수정일 기간검색
	if( $pass_edate !="" ) { $s_query .= " and eif_rdate <= '{$pass_edate} 23:59:59' "; } //이미지 수정일 기간검색
	if( $pass_view!= "") { $pass_view =='Y'? $s_query .= " and eif_use_cnt > 0 ": $s_query .= " and eif_use_cnt <= 0 "; }// -- 노출여부
	// 검색 조건

	// 데이터 조회
	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'eif_rdate';
	if(!$so) $so = 'desc';

	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	// 에디터 이미지 정보추출을 위한 join
	$res = _MQ(" select count(*) as cnt from smart_editor_images_files where 1 {$s_query} ");

	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount/$listmaxcount);
	 
	// 데이터 조회
	$res = _MQ_assoc(" select * from  smart_editor_images_files where 1 {$s_query} order by {$st} {$so} limit {$count}, {$listmaxcount} ");

?>

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>에디터 이미지 검색</strong>
	</div>
	<!-- 검색 -->
	<div class="data_form if_search">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="hidden" name="_mode" value="search">
			<input type="hidden" name="st" value="<?php echo $st; ?>">
			<input type="hidden" name="so" value="<?php echo $so; ?>">
			<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<thead>
					<tr>
						<th>이미지 변경일</th>
						<td>
							<input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day" style="width:85px" readonly>
							<span class="fr_tx">-</span>
							<input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day" style="width:85px" readonly>
						</td>
						<th>이미지 노출여부</th>
						<td>
							<?php echo _InputRadio( 'pass_view' , array('', 'Y', 'N'), ($pass_view) , '' , array('전체', '노출', '비노출') , ''); ?>
						</td>
					</tr>
					<tr>
					<td colspan="4">
						<div class="tip_box">
							<div class="c_tip black">이미지 수정 시 동일 확장자로만 수정 가능합니다.</div>
							<div class="c_tip black">용량이 큰 이미지가 있을 경우 로딩 속도가 느려질 수 있습니다.</div>
							<div class="c_tip black">이미지 파일 삭제를 원하시는 경우 현재 페이지에서 이미지를 삭제하여 주시기 바랍니다.</div>
							<div class="c_tip black"><strong>이미지 파일 삭제 시 사용하는 모든 이미지가 삭제됩니다. 이미지 사용 개수 확인 후 삭제하여 주시기 바랍니다.</strong></div>
							<div class="c_tip black">이미지는 하루한번 3일 이내 수정된 파일이 업데이트 되며, 현재를 기준으로 일주일동안 수정이 없을 시 사용여부 체크 후 사용하지 않으면 삭제됩니다.</div>
							<div class="c_tip black">이미지 개별관리에서의 삭제가 아닌 상품, 게시판 등을 삭제 하였을 시 노출 페이지 수/파일은 변경되지 않으며 일주일 뒤 변경됩니다.</div>
						</div>
					</td>
				</tr>
				</thead>
			</table>
			<div class="c_btnbox">
				<ul>
					<li>
						<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
					</li>
					<?php if($_mode == 'search') { ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</form>
	</div>
	<!-- // 검색 -->

	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<form name="frm" method="post" action="" enctype="multipart/form-data"  target= "common_frame" >
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_uid" value="">

			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>
				</div>
			</div>
			<!-- / 리스트 컨트롤영역 -->

			<table class="table_list">
				<colgroup>
					<col width="40"/><col width="60"/><col width="70"/><col width="130"/><col width="*"/><col width="110"/><col width="100"/><col width="220"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
						<th scope="col">NO</th>
						<th scope="col">노출여부</th>
						<th scope="col">현재 이미지</th>
						<th scope="col">이미지 관리</th>
						<th scope="col">노출 페이지 수</th>
						<th scope="col">변경일</th>
						<th scope="col" >관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if(sizeof($res) > 0){
							foreach($res as $k=>$v){

								// 에디터 이미지 절대경로
								$edit_img_link = IMG_DIR_SMARTEDITOR.$v['eif_img'];
								$edit_img_link = str_replace('%2F','/',urldecode($edit_img_link)); // 한글명 이미지 출력을 위한 인코딩
								
								if(@file_exists($_SERVER['DOCUMENT_ROOT'].$edit_img_link)){
									$edit_img_link = str_replace('%2F','/',urlencode($edit_img_link)); // 한글명 이미지 출력을 위한 인코딩
									$edit_file_tag = '<img src="'.$edit_img_link.'?v='.time().'">';
								}
								else{
									$edit_file_tag = $edit_img_link = ''; // 태그, 링크정보 초기화
								}

								// --- 소스코드,url복사 배열 암축 암호화 ---
								$img_tag = '<img src="'. $edit_img_link .'">'; // 소스코드 복사 절대경로
								$img_tag_full = '<img src="'.$system['__url'].$edit_img_link.'">'; //소스코드 복사 Full URL
								$img_url_full = $system['__url'].$edit_img_link; // URL복사 - Full URL

								$edit_url_link = base64_encode(json_encode(array($img_tag, $img_tag_full, $edit_img_link,$img_url_full)));  // 이미지 경로 배열
								// --- 소스코드,url복사 배열 암축 암호화 ---

								$_num = $TotalCount - $count - $k ;

								$ext_days = floor(((strtotime($v['eif_rdate']) + 3600 * 24 * 7)-time())/86400);
					?>

								<?php //사용하지 않는 이미지는 테이블 배경색 변경 ?>
								<?php if($v['eif_use_cnt'] <= 0|| !$edit_img_link){?><tr style="background-color : #f5f5f5;"><?php }?>
								<td><label class="design"><input type="checkbox" class="js_ck" name="editimg_chk[<?php echo $v['eif_uid']; ?>]"></td>
								<td><?php echo $_num; ?></td><?php //NO ?>
								<td>
									<div class="lineup-center">
										<?php echo $arr_adm_button[($v['eif_use_cnt'] > 0 ? '노출' : '비노출')]; ?>
									</div>
								</td>
								<td class="img80" style="max-width:80px;"><?php echo $edit_file_tag; ?></td><?php // 이미지노출 ?>
								<td>
									<?php //이미지 관리 테이블 ?>
									<?php echo _PhotoForm( '../upfiles/smarteditor', '_img_edit_'.$v['eif_uid'] ,'', 'style="width:300px"');?><!-- 파일 변경 폼 -->
									<a href="#none" onclick="upload(<?php echo $v['eif_uid']; ?>);return false;" class="c_btn h27 blue img_modify">수정</a><!-- 에디터 이미지 파일수정 버튼 -->
									<a href="#none" onclick="del('_config.editor_img.pro.php?_mode=delete&_uid=<?php echo $v['eif_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h27 gray">삭제</a><!-- 에디터 이미지 삭제 버튼 -->
									
									<?php if($v['eif_use_cnt'] >= 1 && $edit_img_link){  //이미지 개별 다운로드?>	
										<span class="bar"></span>
										<a href="_config.editor_img.download.php?uid=<?php echo $v['eif_uid']; ?>"  class="c_btn h27 " target="common_frame">다운로드</a>
										<a href="#none" class="c_btn h27 file_btn" data-uid="<?php echo $v['eif_uid']; ?>">사용관리</a><!-- 에디터 이미지 사용처 노출 -->
									<?php }?>
								</td>
								<td>	<?php echo $v['eif_use_cnt']; ?></td><?php // 이미지 노출 페이지 수 ?>
								<td class="date_time">
										<?php echo date('Y-m-d' , strtotime($v['eif_rdate'])); ?><span class="t_light"><?php echo date('H:i:s' , strtotime($v['eif_rdate'])); ?></span>
										<br>
										<span style="color:red">
											<?php if($v['eif_use_cnt'] <= 0|| !$edit_img_link){
													if($ext_days>0){	
														echo $ext_days."일후 삭제";	
													}else{				
														echo "삭제예정";
													}
											}?>
										</span>
								</td>
								<td>
									<div class="lineup-vertical wrap_img_btn_all" data-uid="<?php echo $v['eif_uid']; ?>" data-url="<?php echo $edit_url_link; ?>">
										<a href="#none" onclick="img_btn_all('preview' , <?php echo $v['eif_uid']; ?>)" class="c_btn h22 gray" >미리보기</a>
										<a href="#none" onclick="img_btn_all('tag_copy' , <?php echo $v['eif_uid']; ?>)"  class="c_btn h22 tag_btn" >소스복사</a>
										<a href="#none" onclick="img_btn_all('url_copy' , <?php echo $v['eif_uid']; ?>)" class="c_btn h22 url_btn" >URL복사</a>
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
		</form>
		<!-- / 데이터 리스트 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>
	</div>


	<?// KAY :: 2021-06-07 ::  미리보기,소스복사,태그복사(url복사) 팝업 ?>
	<div class="popup _img_preview" style="display:none; width:700px; background:#fff;" >
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
							<img id="preview_img" src="" class="design" data-idx="'preview_img'" style="max-width:100%">
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

	<!-- 에디터 사용관리 레이어팝업 -->
	<div class="popup _img_file" style="display:none; width:800px; background:#fff;" >
		<div class="edit_img_layerpop">
			<?php	include dirname(__FILE__)."/_config.editor_img.ajax.php";	?>
		</div>
	</div>
	<!-- 에디터 사용관리 레이어팝업 -->

	<script>
	// KAY :: 에디터 이미지 관리 :: 관리(미리보기, 소스복사, URL복사) 레이어팝업 띄우기
	function img_btn_all(btn_type , eiuid){

		// 변수설정
		var data = $(".wrap_img_btn_all[data-uid='"+ eiuid +"']").data('url');
		var json_obj = atob(data); // 미리보기,소스복사,url복사 링크값 받아온거 encode 풀어주기
		var url_obj = JSON.parse(json_obj); // 미리보기,소스복사,url복사 링크배열

		// --- 레이어 팝업 띄우기 ---
		$('._img_preview').lightbox_me({centered: true, closeEsc: false,onLoad: function() {},onClose: function(){}});
		// --- 레이어 팝업 띄우기 ---

		// --- 레이어 팝업 내용 넣기 ---
		$("#preview_img").attr("src" , url_obj[2]);// 미리보기 적용
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
	// KAY :: 에디터 이미지 관리 :: 관리(미리보기, 소스복사, URL복사) 레이어팝업 띄우기

	// 관리 레이어 팝업 내 탭 클릭
	$(document).on('click', '.tab_menu', function() {
		var btn_type = $(this).data('idx'); // 탭 타입 설정

		// 전부 막기
		$('._img_preview .c_tab li').removeClass("hit"); // 탭 히트 전부 없애기
		$('._img_preview .tab_conts').hide(); // 탭 영역 전부 막기

		// 선택 열기
		$("._img_preview .c_tab .tab_menu[data-idx='"+ btn_type +"']").parent("li").addClass("hit"); // 선택 탭 히트 적용
		$("._img_preview .tab_conts[data-idx='"+ btn_type +"']").show();// 선택 탭 영역 열기
	});

	// 선택삭제 함수
	function selectDelete(){
		if($('.js_ck').is(":checked")){
			if(confirm("정말 삭제하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
				$("form[name=frm]").attr("action" , '_config.editor_img.pro.php');
				document.frm.submit();
			}
		}
		else {
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	}

	// 에디터 이미지 수정
	// edit_uid 수정할 이미지 uid(고유번호)
	function upload(edit_uid){

		var app_img = $(".realFile[name='_img_edit_"+edit_uid+"']").val();	// 에디터 이미지 수정할 파일 값 추출
		
		// 수정하는 이미지 파일이 없는 경우 파일 재입력
		if(!app_img){
			alert("수정파일이 존재하지 않습니다. 파일을 입력해주시기바랍니다.");
			return false;
		}

		// 수정 시 이미지명은 동일하게 파일만 수정됨
		if(confirm("수정 시 이미지명은 동일하며 파일만 변경됩니다. 변경하시겠습니까?")){
			$("form[name=frm]").children("input[name=_mode]").val("modify");
			$("form[name=frm]").children("input[name=_uid]").val(edit_uid);
			$("form[name=frm]").attr("action" , "_config.editor_img.pro.php");
			document.frm.submit();
		}
	}

	// 에디터 이미지 사용관리 레이어팝업 띄우기
	$('.file_btn').on('click',function(){
		
		var uid = $(this).data('uid');		// 에디터 이미지 고유번호
		var url = '_config.editor_img.ajax.php';	// 에디터 이미지 레이어팝업 ajax

		// 에디터 이미지 전체숨김후 uid맞는것만 출력이아닌 uid맞는것만 출력
		$.ajax({
			url: url, cache: false,dataType : 'html', type: "get", data: {_mode : 'edtimg_pop', uid : uid }, async:false,
			success: function(html){
				$('.edit_img_layerpop').html(html);
			},error:function(request,status,error){ console.log(error);}
		});
		
		// 사용관리 레이어 팝업 띄우기 
		$('._img_file').lightbox_me({centered: true, closeEsc: false,onLoad: function() {},onClose: function(){}});
	});
	// KAY :: 에디터 이미지 관리 :: 레이어 팝업창 띄우기

	</script>

<?php include_once('wrap.footer.php'); ?>