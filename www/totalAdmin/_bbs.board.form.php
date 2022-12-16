<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}

	# -- 스킨에 따른 선택 확인
	스킨변경 시 PC 기준으로 MOBILE 은 자동인지

*/

if($_REQUEST['_mode'] == 'modify') {
	$app_current_name = '게시판 수정';
	$app_current_link = '_bbs.board.list.php';
}
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$r = _MQ(" select * from smart_bbs_info where bi_uid = '{$_uid}' ");
	if( count($r) < 1){ error_msg("게시판 정보가 없습니다."); }
}else{
	$_mode = 'add';
}

// -- 게시판 정보를 불러온다. {{{
$getBoardSkinInfo = getBoardSkinInfo(); // 게시판 스킨 정보 배열로 호출
$arrBoardKink = array();
foreach($getBoardSkinInfo as $k=>$v){
	$arrBoardKink[$k] = $v['skin']['title'];
}
// -- 게시판 정보를 불러온다. }}}

// -- 권한별 값을 지정
$resGroup = _MQ_assoc("select * from smart_member_group_set where 1 order by mgs_rank asc"); // -- 그룹정보를 가져온다.
$authGroup['list'] = $r['bi_auth_list_group'] != '' ? explode(',',$r['bi_auth_list_group']):array();
$authGroup['view'] = $r['bi_auth_view_group'] != '' ? explode(',',$r['bi_auth_view_group']):array();
$authGroup['write'] = $r['bi_auth_write_group'] != '' ? explode(',',$r['bi_auth_write_group']):array();
$authGroup['reply'] = $r['bi_auth_reply_group'] != '' ? explode(',',$r['bi_auth_reply_group']):array();
$authGroup['comment'] = $r['bi_auth_comment_group'] != '' ? explode(',',$r['bi_auth_comment_group']):array();
$authGroup['editor'] = $r['bi_auth_editor_group'] != '' ? explode(',',$r['bi_auth_editor_group']):array();
$printGroupChk = array();
foreach($authGroup as $k=>$v){
	$printGroupChk[$k] .='<div class="set-member-type-group" style="display:none;" data-idx="'.$k.'">';
	$printGroupChk[$k] .='	<div class="dash_line"><!-- 점선라인 --></div>';
	$printGroupChk[$k] .='	<span class="fr_tx">회원등급 지정 : </span>';
	foreach($resGroup as $gk=>$gv){
		$printGroupChk[$k] .='	<label class="design">';
		$printGroupChk[$k] .='		<input type="checkbox" class="set-group-uid-'.$k.'" name="_auth_group['.$k.'][]" value="'.$gv['mgs_uid'].'" '.(in_array($gv['mgs_uid'],$authGroup[$k]) == true || $_mode == 'add'  ? 'checked':'' ).' />'.$gv['mgs_name'];
		$printGroupChk[$k] .='	</label>';
	}
	$printGroupChk[$k] .='</div>';
}

// -- 템플릿 정보를 가져온다.
$resShopTemplate = _MQ_assoc("select *from smart_bbs_template where bt_type = 'shop' order by  bt_rdate desc");
$arrShopTemplate = array();
foreach($resShopTemplate as $k=>$v){
	$arrShopTemplate[$v['bt_uid']] = $v['bt_title'];
}


?>

<form name="frmBbsInfo" id="frmBbsInfo" action="_bbs.board.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="chkUid" value="<?php echo 0; ?>">
	<input type="hidden" name="_list_type" value="<?php echo $r['bi_list_type']; ?>">

	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>게시판 기본설정</strong><!-- 메뉴얼로 링크 --> </div>

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">게시판 아이디</th>
					<td>
					<?php if(count($r) < 1 ) { ?>
						<input type="text" name="_uid" class="design bold t_black chk-bbs-uid" placeholder="게시판 아이디" value="" style="width:240px;">
						<div class="tip_box">
							<?=_DescStr("게시판 아이디는 영문(대문자, 소문자), 숫자, 언더바<span class='le0'>(_)</span>만 사용 가능하며 초기 등록 후 변경 할 수 없습니다.");?>
						</div>
					<?php }else{ ?>
						<input type="text" name="_uid" class="design bold t_black" placeholder="게시판 아이디" value="<?php echo $r['bi_uid'] ?>" style="width:240px;" readonly>
						<?=_DescStr("게시판 아이디는 초기 등록 후 변경이 불가능합니다.");?>
					<?php } ?>
					</td>
					<th class="ess">게시판 이름</th>
					<td>
						<input type="text" name="_name" class="design bold t_black" placeholder="게시판 이름" value="<?php echo $r['bi_name'] ?>" style="width:240px;">
					</td>
				</tr>



				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio( '_view' , array('Y', 'N'), ($r['bi_view'] ? $r['bi_view'] : 'N' ) , '' , array('노출', '숨김') , ''); ?>
					</td>
					<th>노출구분</th>
					<td>
						<?php echo _InputRadio( '_view_type' , array_keys($arrBoardViewType), ($r['bi_view_type']) , '' , array_values($arrBoardViewType) , ''); ?>
					</td>
				</tr>


			</tbody>
		</table>
	</div>



	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>게시판 스킨설정</strong><!-- 메뉴얼로 링크 --> </div>

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>스킨선택</th>
					<td>
						<?=_InputSelect( "_board_skin" , array_keys($arrBoardKink) , $r['bi_skin'], " class='select-bbs-skin' " , array_values($arrBoardKink) , "-스킨선택-")?>
						<div class="tip_box">
							<?=_DescStr("게시판 스킨에 따라 게시판의 권한설정 및 기본설정이 달라집니다.");?>
						</div>
					</td>
				</tr>

				<tr class="skin-info-wrap" style="display: none;">
					<th>스킨정보</th>
					<td>
						<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
						<div class="data_form">

							<!-- ● 내부탭 -->
							<div class="c_tab bbs-skin-tab-wrap">
								<ul>
									<li class="hit"><a href="#none" class="btn bbs-skin-tab" onclick="return false;" data-agent="pc"><strong>PC스킨정보</strong></a></li>
									<li><a href="#none" class="btn tab_menu bbs-skin-tab"  onclick="return false;" data-agent="mobile"><strong>MOBILE스킨정보</strong></a></li>
								</ul>
							</div>

							<table class="table_form">
								<tbody>
									<tr>
										<td>
											<div class="tab_conts data-skin-info" data-agent='pc'>

											</div>

											<div class="tab_conts data-skin-info" data-agent='mobile' style="display: none;">

											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>

				<tr class="tr-set-skin" data-type="upload-file" style="display:none;">
					<th>파입첨부 사용여부</th>
					<td>
						<?=_InputRadio( "_file_upload_use" , array('Y','N') , !$r['bi_file_upload_use'] ? 'N':$r['bi_file_upload_use'], " class='upload-file' " ,array('사용','미사용') , "")?>
						<div class="tip_box">
							<?=_DescStr("파일 업로드는 <em>".(implode("</em>, <em> ",$arrUpfileConfig['ext']['file']))."</em> 확장자를 가진 파일만 업로드 가능합니다.");?>
						</div>
					</td>
				</tr>

				<tr class="tr-set-skin" data-type="upload-images" style="display:none;">
					<th>이미지첨부 사용여부</th>
					<td>
						<?=_InputRadio( "_images_upload_use" , array('Y','N') , !$r['bi_images_upload_use'] ? 'N':$r['bi_images_upload_use'], " class='upload-images' " ,array('사용','미사용') , "")?>
						<div class="tip_box">
							<?=_DescStr("이미지 업로드는 <em>".(implode("</em>, <em> ",$arrUpfileConfig['ext']['images']))."</em> 확장자를 가진 이미지만 업로드 가능합니다.");?>
						</div>
					</td>
				</tr>

				<tr class="tr-set-skin" data-type="option-date-temp" style="display:none;">
					<th>기간 옵션 사용여부</th>
					<td>
						<?=_InputRadio( "_option_date_use" , array('Y','N') , !$r['bi_option_date_use'] ? 'N':$r['bi_option_date_use'], " class='option-date' " ,array('사용','미사용') , "")?>
						<div class="tip_box">
							<?=_DescStr("기간 이벤트 사용여부를 선택해 주세요.");?>
						</div>
					</td>
				</tr>


			</tbody>
		</table>
	</div>


	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>권한설정</strong><!-- 메뉴얼로 링크 --> </div>

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>

				<tr>
					<th>리스트권한 설정</th>
					<td>
						<?php echo _InputRadio( '_auth_list' , array_keys($arrBoardAuthValue), ($r['bi_auth_list']) , ' class="get-event get-auth-member" data-idx="list" ' , array_values($arrBoardAuthValue) ); ?>

						<?php echo $printGroupChk['list']; ?>
					</td>
					<th>보기권한 설정</th>
					<td>
						<?php echo _InputRadio( '_auth_view' , array_keys($arrBoardAuthValue), ($r['bi_auth_view']) , ' class="get-event get-auth-member" data-idx="view" ' , array_values($arrBoardAuthValue) ); ?>
						<?php echo $printGroupChk['view']; ?>
					</td>
				</tr>


				<tr>
					<th>쓰기권한 설정</th>
					<td>
						<?php echo _InputRadio( '_auth_write' , array_keys($arrBoardAuthValue), ($r['bi_auth_write']) , ' class="get-event get-auth-member" data-idx="write" ' , array_values($arrBoardAuthValue) ); ?>
						<?php echo $printGroupChk['write']; ?>
					</td>
					<th>에디터권한 설정</th>
					<td>
						<?php echo _InputRadio( '_auth_editor' , array_keys($arrBoardAuthValue), ($r['bi_auth_editor']) , ' class="get-event get-auth-member" data-idx="editor" ' , array_values($arrBoardAuthValue) ); ?>
						<?php echo $printGroupChk['editor']; ?>
					</td>
				</tr>



				<tr>
					<th>답글쓰기 사용여부</th>
					<td colspan="3">
						<?php echo _InputRadio( '_reply_use' , array('Y','N'), (!$r['bi_reply_use']  ? 'N': $r['bi_reply_use']) , ' class="get-event get-auth-use" ' , array('사용','미사용') ); ?>
					</td>
				</tr>

				<tr class="tr-auth-reply" style="display:none;">
					<th>답글쓰기권한 설정</th>
					<td colspan="3">
						<?php echo _InputRadio( '_auth_reply' , array_keys($arrBoardAuthValue), ($r['bi_auth_reply']) , ' class="get-event get-auth-member" data-idx="reply" ' , array_values($arrBoardAuthValue) ); ?>
						<?php echo $printGroupChk['reply']; ?>
					</td>
				</tr>

				<tr class="tr-set-skin" data-type="option-comment">
					<th>댓글쓰기 사용여부</th>
					<td colspan="3">
						<?php echo _InputRadio( '_comment_use' , array('Y','N'), (!$r['bi_comment_use']  ? 'N': $r['bi_comment_use']) , ' class="get-event get-auth-use" ' , array('사용','미사용') ); ?>

					</td>
				</tr>

				<tr class="tr-auth-comment" style="display: none;">
					<th>댓글쓰기권한 설정</th>
					<td colspan="3">
						<?php echo _InputRadio( '_auth_comment' , array_keys($arrBoardCommentAuthValue), ($r['bi_auth_comment'] == '' ? '2':$r['bi_auth_comment']) , ' class="get-event get-auth-member" data-idx="comment" ' , array_values($arrBoardCommentAuthValue) ); ?>
						<?php echo $printGroupChk['comment']; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>기능설정</strong><!-- 메뉴얼로 링크 --> </div>

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>

				<tr>
					<th>쇼핑몰 게시글 양식 설정</th>
					<td colspan="3">
						<?php echo _InputSelect( '_btuid' , array_keys($arrShopTemplate) , ($r['bi_btuid']) , '' , array_values($arrShopTemplate) ); ?>
						<?=_DescStr("쇼핑몰 게시글 양식 설정은 <em>게시판 > 게시판관리 > 게시글 양식 관리</em> 에서 등록 가능합니다.");?>
					</td>
				</tr>

				<tr>
					<th>비밀글 사용여부</th>
					<td>
						<?php echo _InputRadio( '_secret_use' , array('Y','N'), (!$r['bi_secret_use']  ? 'N': $r['bi_secret_use']) , '' , array('사용','미사용') ); ?>
						<div class="tip_box">
							<?=_DescStr("게시판 스킨에 따라 비밀글기능 사용에 제한이 있을 수 있습니다.");?>
						</div>
					</td>
					<th>리캡챠(스팸방지 기능) 사용여부</th>
					<td>
						<?php echo _InputRadio( '_recaptcha_use' , array('Y','N'), (!$r['bi_recaptcha_use']  ? 'N': $r['bi_recaptcha_use']) , ' class="get-event get-recaptcha-use" ' , array('사용','미사용') ); ?>

						<div class="set-recaptcha" style="display: none;">
						<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _InputRadio( '_recaptcha_set' , array('all','nonemember'), (!$r['bi_recaptcha_set']  ? 'all': $r['bi_recaptcha_set']) , '' , array('회원+비회원','비회원') ); ?>
						</div>

						<div class="tip_box">
							<?=_DescStr("관리자는 기본적으로 스팸방지 기능이 적용되지 않습니다. ");?>
						</div>
					</td>
				</tr>

				<tr class="tr-set-writer-view">
					<th>작성자노출 설정</th>
					<td>
						<?php echo _InputRadio( '_writer_view_use' , array('Y','N'), (!$r['bi_writer_view_use']  ? 'N': $r['bi_writer_view_use']) , '' , array('전체노출','부분노출') ); ?>
						<div class="tip_box">
							<?=_DescStr("부분노출로 설정 시 작성자명은 첫 한글자만 노출되며 모두 * 처리 됩니다. ");?>
							<?=_DescStr("게시판 스킨에 따라 작성자 노출에 제한이 있을 수 있습니다.");?>
						</div>
					</td>
					<th>글쓰기 제한</th>
					<td>
						<?php echo _InputRadio( '_write_day_use' , array('Y','N'), ($r['bi_write_day_use'] == '' ? 'N':$r['bi_write_day_use']) , ' class="get-event get-write-day-use" ' , array('사용','미사용') ); ?>

						<div class="set-write-day" style="display: none;">
						<div class="dash_line"><!-- 점선라인 --></div>
							<span class="fr_tx">당일 기준</span>
							<input type="text" name="_write_day_cnt" class="design bold t_black number_style" placeholder="" value="<?php echo $r['bi_write_day_cnt'] == '' ? 20 : $r['bi_write_day_cnt'] ?>" style="width:100px;">
							<span class="fr_tx">개 등록가능</span>
						</div>
					</td>
				</tr>



				<tr>
					<th>NEW아이콘 노출기간</th>
					<td>
						<input type="text" name="_newicon_view" class="design bold t_black number_style" placeholder="" value="<?php echo $r['bi_newicon_view'] ?>" style="width:60px;">
						<span class="fr_tx">일</span>
						<div class="tip_box">
							<?=_DescStr("게시판 스킨에 따라 NEW 아이콘 노출이 되지 않을 수 있습니다.");?>
						</div>
					</td>
					<th  class="ess">페이지당 게시물 수</th>
					<td>
						<input type="text" name="_listmaxcnt" class="design bold t_black number_style" placeholder="" value="<?php echo $r['bi_listmaxcnt'] == '' ? 20 : $r['bi_listmaxcnt'] ?>" style="width:100px;">
						<span class="fr_tx">개</span>
					</td>
				</tr>




			</tbody>
		</table>
	</div>


	<!-- KAY :: 게시판 카테고리설정 -->
	<div class="group_title"><strong>게시판 카테고리설정</strong> </div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용여부</th>
					<td colspan="3">
						<?php echo _InputRadio( '_category_use' , array('Y','N'), ($r['bi_category_use'] =='' ? 'N': $r['bi_category_use']) , ' class="js_target"' , array('사용','미사용') ); ?>
						<script type="text/javascript">
							// 게시판 카테고리 사용/미사용 시 카테고리 설졍영역 노출/비노출
							$(document).on('change', '.js_target', Category);
							$(document).ready(Category);
							function Category() {
								var _type = $('.js_target:checked').val();
								if(_type == 'N') $('.js_set_category').hide();
								else $('.js_set_category').show();
							}
						</script>
					</td>
				</tr>
				<tr class="js_set_category">
					<th>게시판 카테고리설정</th>
					<td colspan="3">
						<input type="text" name="_category" class="design js_tag" placeholder="카테고리" value="<?php echo $r['bi_category']; ?>" style="width:100%;">
						<div class="tip_box">
							<?=_DescStr("카테고리 추가 시 콤마단위(,)로 처리 됩니다.");?>
							<?=_DescStr("카테고리 변경 시 카테고리 연동이 안될수 있습니다.");?>
						</div>
					</td>
				</tr>
			<tbody>
		</table>
	</div>
	<!-- KAY :: 게시판 카테고리설정 -->


	<?php echo _submitBTN('_bbs.board.list.php'); ?>
</form>
<script type="text/javascript">
	// -- 게시판 스킨정보
	getBoardSkinInfo = function(agent){

		// -- 게시판 아이디 중복체크
		var _skinName = $('.select-bbs-skin').val(); // 스킨고유아이디
		if( _skinName == '' || _skinName == undefined){ $('.skin-info-wrap').hide(); }
		else{ $('.skin-info-wrap').show(); }
		$('.bbs-skin-tab-wrap ul li').removeClass('hit');
		$('.bbs-skin-tab[data-agent="'+agent+'"]').closest('li').addClass('hit');

		<?php if($_mode == 'modify'){ ?>
		if( _skinName == 'qna'){ $('.select-bbs-skin').val('qna'); }
		<?php } ?>

		var url = '_bbs.board.ajax.php';
	  $.ajax({
	      url: url, async:false, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'selectSkin', _skinName : _skinName,agent:agent }, success: function(data){

	      		// -- 스킨정보를 노출
	      		$('.data-skin-info').hide();
	      		$('.data-skin-info[data-agent="'+agent+'"]').html(data.htmlSkin).show();

	      		// -- 스킨의 유형을 변경
	      		$('[name="_list_type"]').val(data.skinType);

	      		// -- 스킨별 옵션에 따른 기능 보이기와 미적용
	      		$('.tr-set-skin').hide();
	      		if( data.skinOption.length > 0){
	      			$.each(data.skinOption,function(i,v){
	      				$('.tr-set-skin[data-type="'+v+'"]').show();
	      			})
	      		}

						// -- 튤팁 따로 선언
					  $('img.js_thumb_img').tooltip({
							show: null, hide: null,
							items: 'img.js_thumb_img[data-img]',
							content: function(e) {
								if(!$(this).data('img')) return;
								return '<img src="'+$(this).data('img')+'" alt="" />';
							}
						});

	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	}

	// -- 게시판스킨 선택 시
	$(document).on('change','.select-bbs-skin',function(){ getBoardSkinInfo('pc') });

	// -- 시큰정보에서 PC,MOBILE 탭 선택 시
	$(document).on('click','.bbs-skin-tab',function(){
		var agent = $(this).attr('data-agent');
		var _skinName = $('.select-bbs-skin').val(); // 스킨고유아이디
		if( agent == '' || agent == undefined){ return false; }
		if( _skinName == '' || _skinName == undefined){ alert('스킨을 먼저 선택해 주세요.'); return false; }
		getBoardSkinInfo(agent);
	});

	// -- 답글 / 댓글 쓰기권한에 따른 처리
	eventBoardAuth = function()
	{
		var chkReply = $('#frmBbsInfo [name="_reply_use"]:checked').val();
		var chkComment = $('#frmBbsInfo [name="_comment_use"]:checked').val();

		// -- 답글 및 댓글쓰기 기능이 사용이라면;
		if( chkReply == 'Y'){ $('.tr-auth-reply').show();  }
		else{ $('.tr-auth-reply').hide(); }
		if( chkComment == 'Y'){ $('.tr-auth-comment').show();  }
		else{ $('.tr-auth-comment').hide(); }

		// -- 권한설정에 따른 이벤트
		$('.set-member-type-group').hide();
		$('.get-auth-member:checked').each(function(i,v){
			var chkIdx = $(v).attr('data-idx');
			var chkVal = $(v).val()*1;
			if( chkVal == 2){  $('.set-member-type-group[data-idx="'+chkIdx+'"]').show(); }
		});

		// -- 글쓰기 제한에따른이벤트
		var chkWriteDayUse = $('.get-write-day-use:checked').val();
		$('.set-write-day').hide();
		if( chkWriteDayUse == 'Y'){  $('.set-write-day').show(); }

		// -- 리캡챠 이벤트에 따른 처리
		var chkRecaptchaUse = $('.get-recaptcha-use:checked').val();
		$('.set-recaptcha').hide();
		if( chkRecaptchaUse == 'Y' ){ $('.set-recaptcha').show(); }


	}
	$(document).on('click','.get-event',eventBoardAuth );
	// -- 답글 / 댓글 쓰기권한에 따른 처리

	$(document).ready(function() {

		// -- 스킨선택에 따른 정보 초기화
		getBoardSkinInfo('pc');

		// -- 답글 / 댓글 쓰기권한에 따른 처리
		eventBoardAuth();

		// -  validate ---
		$('form[name=frmBbsInfo]').validate({
			ignore: '.ignore',
			rules: {
				<?php if($_mode == 'add') { ?>
				_uid : {required : true  } ,
				<?php } ?>
				_name : {required : true  },
				_view_type : {required : true  }
				, _board_skin : { required : true }
				, _listmaxcnt : {
					required : true,
					min : { param : 1 }
				}
				, _write_day_cnt : {
					required : function(){ return $('[name="_write_day_use"]:checked').val() == 'Y' ? true : false  }

				}

			},
			messages: {
				<?php if($_mode == 'add') { ?>
					_uid : {required : '게시판 아이디를 입력해 주세요.'  } ,
				<?php } ?>
				_name : {required : '게시판 이름을 입력해 주세요.'  } ,
				_view_type : {required : '노출구분을 선택해 주세요.'  }
				, _board_skin : { required : '게시판 스킨을 선택해 주세요.' }
				, _listmaxcnt : {
					required : '페이지당 게시물 수 를 입력해 주세요.',
					min  : '페이지당 게시물 수 는 최소 1이상 입력해 주세요.'
				}
				, _write_day_cnt : {
					required : '글쓰기 제한 개수를 입력해 주세요.'
				}

			},
			submitHandler : function(form) {

				if( $('[name="_write_day_use"]:checked').val() == 'Y'){
					var chkWriteDayCnt = $('[name="_write_day_cnt"]').val()*1;
					if( chkWriteDayCnt <1){  alert('글쓰기 제한은 최소 1이상 입력하셔야 합니다.'); $('[name="_write_day_cnt"]').focus(); return false; }
				}

				<?php if($_mode == 'add') { ?>
				// -- 게시판 아이디 중복체크
				var chkUid = $('.chk-bbs-uid').val();
				var url = '_bbs.board.ajax.php';
			  $.ajax({
			      url: url, cache: false,dataType : 'json', type: "POST", data: {ajaxMode:'chkUid', chkUid : chkUid }, success: function(data){
			      	if(data.rst == 'success'){
			      		form.submit();
			      	}else{
			      		alert(data.msg);
						$('[name="_uid"]').focus();
			      		return false;
			      	}
			      },error:function(request,status,error){ console.log(request.responseText);}
			  });
			  <?php }else{ ?>
				form.submit();
			  <?php } ?>
			}
		});
		// - validate ---
	});

</script>

<?php
		// -- 게시판 정보를 불러온다. {{{
		include_once('wrap.footer.php');
?>