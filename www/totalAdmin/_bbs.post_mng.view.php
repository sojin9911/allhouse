<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_bbs.post_mng.list.php';
$app_current_name = '게시글 보기';
include_once('wrap.header.php');

// -- 게시물 조회 
$r = _MQ("select *  from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu)  where b_uid = '".$_uid."' ");

// -- 게시물의 형식에 따라 달리 노출한다. :: qna의 경우 답글은 본문에 노출
if(in_array($r['bi_list_type'],array('qna')) == true){
	$replyMode = true; // 간소화
	if( $r['b_depth'] > 1 && $r['b_relation'] > 0){ // 2뎁스라면 1뎁스와 교체
		$rowReply = $r;
		$r = _MQ("select * from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu)  where b_uid = '".$r['b_relation']."' ");
		
		$_uid = $r['b_uid'];
	}else{
		$rowReply = _MQ("select *  from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu)  where b_relation = '".$r['b_uid']."' ");
	}

	// -- 답글 작성자 정보 
	$printReplyWriterInfo = in_array($rowReply['b_writer_type'], array('member','admin')) == true ? showUserInfo($rowReply['b_inid'],$rowReply['b_writer'],$rowReply) : showUserInfo(false,$rowReply['b_writer']);	


	// -- 게시물 첨부파일을 불러온다.
	$getReplyBoardFile = getFilesRes('smart_bbs',$rowReply['b_uid']);
	$arrReplyFile = array();
	foreach($getReplyBoardFile as $k=>$v){
		$arrReplyFile[] = '<a href="'.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].'" class="c_btn  h27"  title="'.$v['f_oldname'].'">'.$v['f_oldname'].'</a>';
	}

}
if( count($r) < 1 ){ error_msg("게시물이 존재하지 않습니다."); }
// -- 작성자 정보 
$printWriterInfo = in_array($r['b_writer_type'], array('member','admin')) == true ? showUserInfo($r['b_inid'],$r['b_writer'],$r) : showUserInfo(false,$r['b_writer']);


// -- 게시물 첨부파일을 불러온다.
$getBoardFile = getFilesRes('smart_bbs',$_uid);
$arrFile = array();
foreach($getBoardFile as $k=>$v){
	$arrFile[] = '<a href="'.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].'" class="c_btn  h27"  title="'.$v['f_oldname'].'">'.$v['f_oldname'].'</a>';
}

// -- 게시물의 댓글을 가져온다. 
$resComment = _MQ_assoc("select *from smart_bbs_comment where bt_buid = '".$_uid."' ");

$arrIcon = array();
if($r['b_notice'] == 'Y'){  $arrIcon[] = '<span class="c_tag h18 yellow">공지</span>'; } // 공지사항 아이콘
if($r['b_secret'] == 'Y'){  $arrIcon[] = '<span class="c_tag h18 gray">비밀글</span>'; } // 비밀글 아이콘
$printIcon = count($arrIcon) > 0 ? implode("",$arrIcon) : '';
$printTitle = $printIcon.strip_tags(stripslashes($r['b_title']));

// -- 답글을 쓸 수 있는지 체크
$replyUse = boardAuthChk($r['b_menu'],'reply') === true && $r['b_relation'] == 0 && $r['b_depth'] == 1 && $r['b_notice'] != 'Y' ? true : false;

?>

<form name="frmBbs" id="frmBbs" action="_bbs.post_mng.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_uid" value="<?php echo $r['b_uid']; ?>">

	<div class="group_title"><strong><?php echo '게시글 본문'?></strong><!-- 메뉴얼로 링크 --> </div>

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>

				<tr>
					<th>제목</th>
					<td colspan="3"><?php echo $printTitle; ?></td>
				</tr>

				<tr>
					<th>작성자</th>
					<td><?php echo $printWriterInfo; ?></td>
					<th>작성일</th>
					<td><?php echo date('Y.m.d H:i:s',strtotime($r['b_rdate'])); ?></td>
				</tr>

				<?php if( count($arrFile) > 0){ ?> 
				<tr>
					<th>첨부 파일</th>
					<td colspan="3">
						<?php echo implode($arrFile);?>
					</td>
				</tr>
				<?php } ?>

				<tr>
					<th>내용</th>
					<td colspan="3">
						<div class="editor">
							<?php echo stripslashes($r['b_content']);?>
						</div>	
					</td>
				</tr>

				<?php if( $r['bi_comment_use'] == 'Y'){ // 댓글이 사용중일 시 에만  ?> 
				<tr>
					<th>댓글</th>
					<td colspan="3" class="if_view_reply">
						<!-- ● 데이터 리스트 -->
						<table class="table_list">
							<colgroup>
								<col width="*"/><col width="120"/>
							</colgroup>
							<tbody>
								<tr>
									<td class="t_left">
										<textarea id="add-comment-content" rows="3" cols="" class="design" style="resize:none;" placeholder="댓글을 등록할 수 있습니다."></textarea>											
									</td>
									<td>
										<div class="lineup-vertical">
											<a href="#none" class="c_btn h46 black add-comment-submit">등록</a>
										</div>
									</td>
								</tr>
							</tbody> 
						</table>											
						<div class="ajax-comment-list"></div>
					</td>
				</tr>
				<?php }?>



			</tbody>
		</table>
	</div>

	<?php if( $replyMode === true) { // 답변이 본문에 노출되는 게시판일경우  ?> 
	<div class="group_title"><strong><?php echo '게시글 답변'?></strong><!-- 메뉴얼로 링크 --> </div>
	
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>

				<tr style="display: none;">
					<th>제목</th>
					<td colspan="3"><?php echo strip_tags(stripslashes($rowReply['b_title'])) ?></td>
				</tr>

				<tr>
					<th>작성자</th>
					<td><?php echo $printReplyWriterInfo; ?></td>
					<th>작성일</th>
					<td><?php echo date('Y.m.d H:i:s',strtotime($rowReply['b_rdate'])); ?></td>
				</tr>

				<?php if( count($arrReplyFile) > 0){ ?> 
				<tr>
					<th>첨부 파일</th>
					<td colspan="3">
						<?php echo implode($arrReplyFile);?>
					</td>
				</tr>
				<?php } ?>

				<tr>
					<th>내용</th>
					<td colspan="3">
						<div class="editor">
							<?php echo stripslashes($rowReply['b_content']);?>
						</div>	
					</td>
				</tr>



			</tbody>
		</table>
	</div>

	<?php } ?>


	<div class="c_btnbox">
		<ul>
			
			<li><a href="_bbs.post_mng.list.php?<?php echo  enc('d' , $_PVSC ); ?>" class="c_btn h46 black line" accesskey="l">목록</a></li>
			<li><a href="_bbs.post_mng.form.php?_mode=modify&_uid=<?php echo $_uid ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h46 black line">수정</a></span></li>
			<li><a href="#none" onclick="return false;"  class="c_btn h46 black line delete-item">삭제</a></span></li>

			<?php if( $replyUse === true ) { ?>
			<li><a href="_bbs.post_mng.form.php?_mode=reply&_uid=<?php echo $_uid ?>&_PVSC=<?php echo $_PVSC ?>" class="c_btn h46 red">답글</a></span></li>
			<?php } ?>
		</ul>
	</div>

	<div class="fixed_save js_fixed_save" style="display: none;">
		<div class="wrapping">
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
			<li><a href="_bbs.post_mng.list.php?<?php echo  enc('d' , $_PVSC ); ?>" class="c_btn h34 black line" accesskey="l">목록</a></li>
			<li><a href="_bbs.post_mng.form.php?_mode=modify&_uid=<?php echo $_uid ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h34 black line">수정</a></span></li>
			<li><a href="#none" onclick="return false;"  class="c_btn h34 black line delete-item">삭제</a></span></li>
			<?php if( $replyUse === true) { ?>
			<li><a href="_bbs.post_mng.form.php?_mode=reply&_uid=<?php echo $_uid ?>&_PVSC=<?php echo $_PVSC ?>" class="c_btn h34 red" >답글</a></span></li>
			<?php } ?>
				</ul>
			</div>
		</div>
	</div>

</form>
<div class="ajax-data-box" data-comment-ahref=""></div>

<style>.post_replay .fr_bullet:before{ display:none; }</style>
<script type="text/javascript">
	var postComment = {}; 
	// -- 게시글 삭제
	$(document).on('click','.delete-item',function(){
		if(confirm('해당 게시물을 삭제하시겠습니까?')  == false){ return false; }
		var _uid = $('#frmBbs [name="_uid"]').val();
		var _PVSC = $('#frmBbs [name="_PVSC"]').val();
		if( _uid == '' || _uid == undefined){ alert("잘못된 접근입니다."); return false; }
		$('#frmBbs [name="_mode"]').val('delete');
		$('#frmBbs').submit();
		return true;
	});

	// -- 댓글등록
	$(document).on('click','.add-comment-submit',function(){
		var _buid= $('#frmBbs [name="_uid"]').val(); // 게시글의 고유번호(댓글의 고유번호 아님)
		var _content= $('#add-comment-content').val();
		var commentWriteLen = <?php echo $varCommentWriteLen; ?>; // 등록가능한 댓글내용 길이 
		var _depth= 1 // 댓글은 1차고정
		var _relation= 0  // 부모없음
		$('.ajax-data-box').attr('data-comment-ahref',''); // 등록시에는 1페이지로 고정
		var url = '_bbs.post_mng.ajax.php';

		if( _buid == '' ||_buid == undefined){  return false; }
		if( _content == ''){ alert("댓글의 내용을 입력해 주세요"); return false; }
		if( _content.length > (commentWriteLen*1) ){ alert("댓글내용은 <?php echo $varCommentWriteLen; ?>자 이하로 입력해 주세요."); return false; }

		$.ajax({
			url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode:'addComment', _buid : _buid , _content : _content , _depth : _depth , _relation : _relation }, success: function(data){
					alert(data.msg);
					$('#add-comment-content').val('')
					if(data.rst == 'success'){
						postCommentList();
					}
					return false;
			},error:function(request,status,error){ console.log(request.responseText);}
		});				
	});

	// -- 댓글등록
	$(document).on('click','.modify-comment-submit',function(){
		var _buid= $('#frmBbs [name="_uid"]').val(); // 게시글의 고유번호(댓글의 고유번호 아님)
		var _uid = $(this).attr('data-uid');
		var _content= $('#modify-comment-content').val(); 
		var _depth= 1 // 댓글은 1차고정
		var _relation= 0  // 부모없음
		var commentWriteLen = <?php echo $varCommentWriteLen; ?>; // 등록가능한 댓글내용 길이
		var url = '_bbs.post_mng.ajax.php';

		if( _buid == '' ||_buid == undefined || _uid == '' ||_uid == undefined){  return false; }
		if( _content == ''){ alert("댓글의 내용을 입력해 주세요"); return false; }
		if( _content.length > (commentWriteLen*1) ){ alert("댓글내용은 <?php echo $varCommentWriteLen; ?>자 이하로 입력해 주세요."); return false; }

		$.ajax({
			url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode:'modifyComment', _uid : _uid, _buid : _buid , _content : _content , _depth : _depth , _relation : _relation 
			}, success: function(data){
				alert(data.msg);
				if(data.rst == 'success'){
					postCommentList();					
				}
				return false;
			},error:function(request,status,error){ console.log(request.responseText);}
		});				
	});

	// -- 댓글수정클릭시
	$(document).on('click','.modify-comment-item',function(){
		var _uid = $(this).attr('data-uid');
		$('.comment-content[data-uid="'+_uid+'"]').hide();
		$('.modify-comment-form[data-uid="'+_uid+'"]').show();
	})

	// -- 댓글수정에서 취소 클릭 시
	$(document).on('click','.cancel-comment-item',function(){
		var _uid = $(this).attr('data-uid');
		$('.comment-content[data-uid="'+_uid+'"]').show();
		$('.modify-comment-form[data-uid="'+_uid+'"]').hide();
	})




	// -- 댓글삭제
	$(document).on('click','.delete-comment-item',function(){
		var _uid = $(this).attr('data-uid');
		var _buid= $('#frmBbs [name="_uid"]').val(); // 게시글의 고유번호(댓글의 고유번호 아님)
		if( _buid == '' ||_buid == undefined){  return false; }
		if( confirm("댓글을 삭제하시겠습니까?") == false){ return false; }
		var url = '_bbs.post_mng.ajax.php';
		$.ajax({	url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode:'deleteComment', _buid : _buid , _uid : _uid }, success: function(data){
			if(data.rst == 'success'){
				postCommentList();
			}else{ alert(data.msg); }
				return false;
			},error:function(request,status,error){ console.log(request.responseText);}
		});	
	})


	$(document).ready(function(){
		postCommentList();
	});

	$(document).on('click','.paginate .lineup a',function(){
		var ahref = $(this).attr('href');
		var hasHit = $(this).hasClass('hit');
		$('.ajax-data-box').attr('data-comment-ahref',ahref);
		if(hasHit == true){ return false; }
		else{
			postCommentList();
		}
		
		var $root = $('html, body');
		$root.animate({
			scrollTop: $('#add-comment-content').offset().top - 10
		}, 500, 'easeInOutCubic');
		return false;
	});


	// -- 댓글 리스트 뷰
	function postCommentList()
	{
		var chkLen = $('.comment-list-item').length;
		var _buid= $('#frmBbs [name="_uid"]').val(); // 게시글의 고유번호(댓글의 고유번호 아님)
		if( _buid == '' ||_buid == undefined){  return false; }
		var ahref = $('.ajax-data-box').attr('data-comment-ahref'); // 페이징 클릭 시 저장되는 정보
		var url = '_bbs.post_mng.ajax.php';
		var currentPage = ahref.replace(/\?listpg\=/gi,'')*1;
		if( currentPage == '' || currentPage == undefined){ currentPage = 1; }
		if( (chkLen < 2 || chkLen == undefined) && currentPage > 1){ahref = '?listpg='+(currentPage-1) ; }

		$.ajax({	
			url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'listComment', _buid : _buid, ahref : ahref }, success: function(data){
				if(data.rst == 'success'){
					$('.ajax-comment-list').html(data.html);
				}else{
					$('.ajax-comment-list').html('');
					return false;
				}
			},error:function(request,status,error){ console.log(request.responseText);}
		});	
	}


</script>
<?php 		
		// -- 게시판 정보를 불러온다. {{{
		include_once('wrap.footer.php'); 
?>