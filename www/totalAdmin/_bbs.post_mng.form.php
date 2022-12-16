<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
if($_REQUEST['_mode'] == 'modify') {
	$app_current_name = '게시글 수정';
}else if( $_REQUEST['_mode'] == 'reply'){
	$app_current_name = '게시글 답글';
}else{
	$app_current_name = '게시글 등록';
}
$app_current_link = '_bbs.post_mng.list.php';
include_once('wrap.header.php');
if( in_array($_mode, array('add','modify','reply')) == false){  error_msg("잘못된 접근입니다."); }

// -- 게시판 정보를 불러온다.
$getBoardList = get_board_list_array(false,true);
$defaultMenu = array_shift(array_keys($getBoardList)); // 게시판이 없다면 첫번째 게시판이 선택될 수 있도록 처리

// 변수 설정
if($_mode == 'modify'){
	$r = _MQ("select *from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu)  where b_uid = '".$_uid."' ");
	if( count($r) < 1 ){ error_msg("게시물이 존재하지 않습니다."); }
	$_menu = $_menu != '' ? $_menu : $r['b_menu'];
	$_title = htmlspecialchars(stripslashes($r['b_title']));
	$_content = stripslashes($r['b_content']);
	$_writer = $r['b_writer'];

	// -- 게시물 첨부파일을 불러온다.
	$getBoardFile = getFilesRes('smart_bbs',$_uid);

	// -- 1차일경우 자식이 있는지 체크
	if( $r['b_depth'] == 1 && $r['b_relation'] == 0){
		$chkIsReply = _MQ_result("select count(*) as cnt  from smart_bbs where b_depth > 1 and b_relation = '".$_uid."' ") > 0 ? true : false;
	}

}else if($_mode == 'reply'){ // 답글일 시
	$pfContent = "답글";
	$_relation = $_uid; // 부 모 글;

	// -- 부모글의 정보를 가져온다.
	$rp = _MQ("select *from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu)  where b_uid = '".$_uid."' ");
	$_menu = $rp['b_menu']; // 자식글의 경우 부모글의 게시판 형식 고정

	// -- 부모글의 정보를 가공
	$parentWriter = in_array($rp['b_writer_type'], array('member','admin')) == true ? showUserInfo($rp['b_inid'],$rp['b_writer'],$rp) : showUserInfo(false,$rp['b_writer']);
	$parentContent = stripslashes($rp['b_content']); // 본문

	// -- 부모 게시물 첨부파일을 불러온다.
	$getParentBoardFile = getFilesRes('smart_bbs',$_uid);
	$arrParentFile = array();
	foreach($getParentBoardFile as $k=>$v){
		$arrParentFile[] = '<a href="'.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].'" class="c_btn  h27"  title="'.$v['f_oldname'].'">'.$v['f_oldname'].'</a>';
	}

	// -- qna :: 답글이 한개인경우 답글 쓰더라도 있을경우 뽑는다. => 답글은 한개만 존재
	$r = _MQ(" select *from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu)  where b_relation = '".$_uid."' and b_depth = 2 and bi_list_type = 'qna'   ");
	if( count($r) > 0 ) {
		$_title = htmlspecialchars(stripslashes($r['b_title']));
		$_content = stripslashes($r['b_content']);
		$_writer = $r['b_writer'];

		// -- 게시물 첨부파일을 불러온다.
		$getBoardFile = getFilesRes('smart_bbs',$r['b_uid']);
	}

	// -- 답글추가라면
	if(count($r) < 1){
		$r = _MQ(" select *from smart_bbs_info where bi_uid = '".$_menu."'  ");
		$_title = "RE : ".htmlspecialchars(stripslashes($rp['b_title']));
		$_content = '';
		$_writer = $siteInfo['s_adshop'];
	}

}else{ // 추가
	if($select_menu != ''){ $_menu = $select_menu; } // 만약 게시글 리스트에서 게시판 선택 후 들어왔다면
	$_menu = $_menu != '' ? $_menu : $defaultMenu;
	// -- 추가일 경우 게시물 정보는 없으니 게시판 정보만 불러온다.
	$r = _MQ("select *from smart_bbs_info where bi_uid = '".$_menu."' ");
	if( count($r) < 1){ error_msg("게시판 정보가 존재하지 않습니다."); }

	// -- 쇼핑몰 게시물 양식  정보를 가져온다.
	// $rowShopTemplate = _MQ("select *from smart_bbs_template where bt_type = 'shop' and bt_uid = '".$r['bi_btuid']."'  ");
	// $_title = $rowShopTemplate['bt_title']; // 게시글양식이 있을경우 :: 추가일시에만 적용
	// $_content = $rowShopTemplate['bt_content']; // 게시글양식이 있을경우 :: 추가일시에만 적용
	$_writer = $siteInfo['s_adshop'];
}


// -- 게시글 권한
$getBoardAuth = boardAuthChkAll($r['bi_uid']);

// -- 관리자 템플릿 정보를 가져온다.
$resAdminTemplate = _MQ_assoc("select *from smart_bbs_template where bt_type = 'admin' order by bt_rdate desc ");
$arrAdminTemplate = array();
foreach($resAdminTemplate as $k=>$v){ $arrAdminTemplate[$v['bt_uid']] = $v['bt_title']; }
// -- 답글인지 체크
$chkReply = ($r['b_depth'] > 1 && $r['b_relation'] > 0) || $_mode == 'reply' ? true : false;


// -- 스킨의 정보를 가져온다.
$skinInfo = getBoardSkinInfo($r['bi_skin'],'pc');
$skinOption = $skinInfo[$r['bi_skin']]['skin']; // 변수를 짧게 줄인다

// -- 공지사항 노출여부
$useNotice = 	($_mode != 'reply' ||  ( $r['b_depth'] == 1 && $r['b_relation'] == 0) ) && $chkIsReply !== true ? true : false;
?>

<form name="frmBbs" id="frmBbs" action="_bbs.post_mng.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid ?>">
	<input type="hidden" name="_relation" value="<?php echo $_relation == '' ? 0 : $_relation ?>">
	<?php if($chkReply === true){  ?>
	<input type="hidden" name="_menu" value="<?php echo $_menu ?>">
	<?php } ?>

	<?php if( count($rp) > 0 && $chkReply === true) { ?>
	<!-- ●단락타이틀 -->
	<div class="group_title"><strong><?php echo '게시글 본문'?></strong><!-- 메뉴얼로 링크 --> </div>

		<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>

				<tr>
					<th>제목</th>
					<td colspan="3"><?php echo strip_tags(stripslashes($rp['b_title'])) ?></td>
				</tr>

				<tr>
					<th>작성자</th>
					<td><?php echo $parentWriter; ?></td>
					<th>작성일</th>
					<td><?php echo date('Y.m.d H:i:s',strtotime($rp['b_rdate'])); ?></td>
				</tr>

				<tr>
					<th>내용</th>
					<td colspan="3">
						<div class="editor">
							<?php echo $parentContent;?>
						</div>
					</td>
				</tr>

				<?php if( count($arrParentFile) > 0){ ?>
				<tr>
					<th>첨부 파일</th>
					<td colspan="3">
						<?php echo implode($arrParentFile);?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php } ?>


	<!-- ●단락타이틀 -->
	<div class="group_title"><strong><?php echo $app_current_name;?></strong><!-- 메뉴얼로 링크 --> </div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>

				<?php if($_mode == 'add' || ( $r['b_depth'] == 1 && $r['b_relation'] == 0) ) { ?>
				<tr>
					<th class="ess">게시판</th>
					<td colspan="3">
						<?php echo _InputSelect( '_menu' , array_keys($getBoardList), ($_menu) , '' , array_values($getBoardList) , ''); ?>
						<?php
							// KAY :: 게시판 카테고리설정
							$_categoryload = array_filter(explode(",", $r['bi_category']));
							if($r['bi_category_use']=='Y' && $_categoryload){
								echo _InputSelect('_category', array_values($_categoryload) ,$r['b_category'] , array_values($_categoryload),'' ) ;
							};
						?>
						<?php echo _DescStr("게시판은 저장 후 변경이 불가능합니다."); ?>
					</td>
				</tr>

				<?php } ?>



				<tr <?php echo $rp['bi_list_type'] == 'qna' ? 'style="display:none;"':''; ?>>
					<th class="ess"><?php echo $pfContent." "?>제목</th>
					<td colspan="3">
						<input type="text" name="_title" class="design bold t_black" placeholder="제목" value="<?php echo $_title ?>" style="width:100%;">
					</td>
				</tr>

				<tr>
					<th class="ess">작성자</th>
					<td>
						<input type="text" name="_writer" class="design bold t_black" placeholder="작성자" value="<?php echo stripslashes($_writer) ?>" style="width:120px;">
					</td>
					<th>관리자 게시글 양식</th>
					<td>
						<?php
								echo _InputSelect( 'setTemplate' , array_keys($arrAdminTemplate), '' , ' data-current-btuid="" ' , array_values($arrAdminTemplate) , '');
						?>
					</td>
				</tr>

				<?php if( $useNotice === true || $r['bi_secret_use'] == 'Y' ) { ?>
				<tr>
					<th>선택항목</th>
					<td colspan="3">

						<?php echo $useNotice === true ? _InputCheckbox( '_notice' , array('Y'), ($r['b_notice']) , '' , array('공지사항으로 등록합니다.') , '') : null;?>
						<?php echo $r['bi_secret_use'] == 'Y' ?  _InputCheckbox( '_secret' , array('Y'), ($r['b_secret']) , '' , array('비밀글로 등록합니다.') , '') : null;?>
					</td>
				</tr>
				<?php } ?>


				<?php if($r['bi_option_date_use'] == 'Y'){  ?>
				<tr>
					<th class="ess">기간</th>
					<td colspan="3">
						<input type="text" name="_sdate" value="<?php echo rm_str($r['b_sdate']) < 1 ? '': $r['b_sdate'] ?>" class="design js_datepic" readonly style="width:95px">
						<span class="fr_tx">-</span>
						<input type="text" name="_edate" value="<?php echo rm_str($r['b_edate']) < 1?  '': $r['b_edate'] ?>" class="design js_datepic" readonly style="width:95px">
					</td>
				</tr>
				<?php } ?>


				<?php if( $r['bi_images_upload_use'] == 'Y') { // 목록이미지 업로드가 TRUE 라면 ?>
				<tr>
					<th>이미지첨부</th>
					<td colspan="3">
						<?php echo _PhotoForm($_SERVER['DOCUMENT_ROOT'].'/upfiles/board', '_img1', $r['b_img1'], 'style="width:280px"'); ?>
						<?php echo ($skinOption['images_width'] != '' && $skinOption['images_height'] != '') ? _DescStr('권장 사이즈 : '.$skinOption['images_width'].' × '.$skinOption['images_height'].' (pixel)') : ''; ?>
					</td>
				</tr>
				<?php } ?>


				<?php if( $r['bi_file_upload_use'] == 'Y') { ?>
				<tr>
					<th>파일첨부</th>
					<td colspan="3">
						<!-- ● 데이터 리스트 -->
						<table class="table_form">
							<colgroup>
								<col width="140"/><col width="*"/>
							</colgroup>
							<tbody>
							<?php if( count($getBoardFile) > 0) { ?>
								<?php foreach( $getBoardFile as $k=>$v){  $idx = ($k+1);?>
								<tr class="tr-files" data-idx="<?php echo $idx ?>" data-mode="modify">
									<th>첨부파일 <span class="files_idx" data-idx="<?php echo $idx ?>"><?php echo $idx?></span></th>
									<td>
										<div class="input_file" style="width:250px">
											<input type="text" id="fakeFileTxt<?php echo $idx;?>" class="fakeFileTxt" readonly="readonly" value="<?php echo $v['f_oldname']; ?>" disabled>
											<div class="fileDiv">
												<input type="button" class="buttonImg" value="파일찾기" />
												<input type="file" name="modifyFile[<?php echo $v['f_uid'] ?>]" value="<?php echo $v['f_oldname']; ?>" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt<?php echo $idx;?>').value = this.value" />
											</div>
										</div>
										<div class="preview_thumb"><a href="<?php echo ''.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].''; ?>" class="btn_file"><?php echo $v['f_oldname']; ?></a></div>
										<label class="design"><input type="checkbox" name="modifyFile_DEL[]" value="<?php echo $v['f_uid']; ?>" >삭제</label>
										<input type="hidden" name="modifyFile_OLD[]" value="<?php echo $v['f_uid'] ?>">
										<?php if($k == 0){ ?>
										<a href="#none" onclick="return false;" data-idx="<?php echo $idx; ?>" class="c_btn h27 icon icon_plus_b exec-addfile">추가</a>
										<div class="c_tip"><?php echo implode(",",$arrUpfileConfig['ext']['file']) ?> 파일만 등록 가능합니다.(최대 <em><?php echo number_format($arrUpfileConfig['cnt']).'</em>개 까지 추가가능' ?>)</div>
										<?php } ?>
									</td>
								</tr>
								<?php }?>
							<?php }else{ ?>
								<tr class="tr-files" data-mode="add">
									<th>첨부파일 <span class="files-idx">1</span></th>
									<td>
										<div class="input_file" style="width:250px">
											<input type="text" id="fakeFileTxt<?php echo 1;?>" class="fakeFileTxt" readonly="readonly" disabled>
											<div class="fileDiv">
												<input type="button" class="buttonImg" value="파일찾기" />
												<input type="file" name="addFile[]" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt<?php echo 1;?>').value = this.value" />
											</div>
										</div>
										<a href="#none" onclick="return false;" class="c_btn h27 icon icon_plus_b exec-addfile">추가</a>
										<div class="c_tip"><?php echo implode(",",$arrUpfileConfig['ext']['file']) ?> 파일만 등록 가능합니다.(최대 <em><?php echo number_format($arrUpfileConfig['cnt']).'</em>개 까지 추가가능' ?>)</div>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</td>
				</tr>
				<?php } ?>


				<tr>
					<th class="ess">내용</th>
					<td colspan="3">
						<textarea name="_content" class="input_text SEditor" <?php echo $getBoardAuth['editor'] !== true ? 'data-text-mode="true"' : ''  ; ?> style="width:100%;height:300px;"><?php echo $_content; ?></textarea>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<?php echo _submitBTN('_bbs.post_mng.list.php'); ?>
</form>

<script type="text/javascript">

	// -- 파일 추가 --
	var addfile_auth=true;
	$(document).on('click','.exec-addfile',function(){
		var idx = $('.tr-files').length*1;
		var buid = $('#frmBbs [name="_uid"]').val();
		var upfileCnt = <?php echo $arrUpfileConfig['cnt']; ?>;

		if(addfile_auth !=true){return false}
		addfile_auth = false;

		if( idx >= upfileCnt){ alert('파일첨부는 최대 '+number_format(upfileCnt)+'개 까지 첨부가능합니다.'); return false; }

		var url = '_bbs.post_mng.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'execAddfile',idx : idx , buid : buid  }, success: function(data){
	      	if( data.rst == 'success') {
				addfile_auth = true;
		      	$('tr.tr-files:last-child').after(data.html);
		      	return true;
		      }else{
		      	return false;
		      }
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	});

	// -- 파일 삭제
	$(document).on('click','.exec-delfile',function(){
		$(this).closest('tr.tr-files[data-mode="add"]').remove();
		$('tr.tr-files').each(function(i,v){
			$(v).find('.files-idx').text(i+1);
		});
	});


	// -- 게시판 선택
	$(document).on('change','select[name="_menu"]',function(){
		var _menu = $(this).val();
		// select_menu
		if( _menu == ''){ return false; }

		if( confirm("게시판 변경 시 작성중인 내용들이 초기화 됩니다.\n변경하시겠습니까?") == false){ $(this).val('<?php echo $_menu; ?>'); return false; }

		location.href="_bbs.post_mng.form.php?_mode=<?php echo $_mode ?>&_PVSC=<?php echo $_PVSC; ?>&_uid=<?php echo $_uid; ?>&select_menu="+_menu;


	});

	// -- 관리자 게시글 양식 선택
	$(document).on('change','[name="setTemplate"]',function(event){

		var btuid = $(this).val();
		var currentBtuid = $(this).attr('data-current-btuid');

		if( btuid == '' || btuid == undefined){return false; }
		if( confirm("관리자 게시글양식을 선택하시겠습니까?\n작성중인 게시글의 내용이 초기화 됩니다.") == false){ $(this).val(currentBtuid); return false; }

		var btuid = $(this).val();
		var url = '_bbs.post_mng.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'setAdminTemplate', btuid : btuid }, success: function(data){
	      	if(data.rst == 'success'){
	      		$(this).attr('data-current-btuid',btuid);

//	      		$('[name="_title"]').val(data._title);
	      		oEditors[0].setContents(data._content)  // $('[name="_content"]').val(data._content);

	      	}else{
	      		alert(data.msg);
	      		 $(this).val(currentBtuid);
	      	}
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	});

	$(document).ready(function(){

		// -  validate ---
		$('form[name=frmBbs]').validate({
			ignore: '.ignore',
			rules: {
				_menu : {required : true  } ,
				_title : {required : true  },
				_writer : {required : true  },

				<?php if($r['bi_option_date_use'] == 'Y'){  ?>
				_sdate : {required : true  },
				_edate : {required : true  },
				<?php } ?>

				_content : {required : true  }
			},
			messages: {
					_menu : {required : '게시판을 선택해 주세요.'  } ,
				_title : {required : '제목을 입력해 주세요.'  } ,
				_writer : {required : '작성자를 입력해 주세요.'  },

				<?php if($r['bi_option_date_use'] == 'Y'){  ?>
				_sdate : {required : '기간(시작일)을 입력해 주세요.'  },
				_edate : {required : '기간(종료일)을 입력해 주세요.'  },
				<?php } ?>

				_content : {required : '내용을 입력해 주세요.' }
			},
			submitHandler : function(form) {

				<?php if($r['bi_option_date_use'] == 'Y'){  ?>
				var _sdate = $('[name="_sdate"]').val().replace(/-/gi,'')*1;
				var _edate = $('[name="_edate"]').val().replace(/-/gi,'')*1;
				if( _sdate > _edate){ alert('기간을 확인해 주세요.');return false; }
				<?php } ?>
				form.submit();
			}

		});
		// - validate ---
	});
</script>
<?php
		// -- 게시판 정보를 불러온다. {{{
		include_once('wrap.footer.php');
?>