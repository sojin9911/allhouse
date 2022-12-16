<?php
	include_once(dirname(__FILE__).'/inc.php');
	if($_buid == '' || $_menu == ''){ return; }

	// -- 데이터 호출
	$resComment = _MQ_assoc("select *from smart_bbs_comment where bt_buid = '".$_buid."' order by bt_rdate desc ");

	// -- 게시판 데이터 초기화
	$commentData = array();
	$commentData['writeAuth'] = boardAuthChk($_menu,'comment'); // 댓글에 대한 쓰기권한을 가져온다.


	// -- 게시판 정보가 없다면 새로가져온다.
	if( count($boardInfo) < 1){
		$boardInfo = get_board_info($_menu); // 게시판정보 추출
		# 경로
		$boardInfo_tmp['_board_skin'] = ($_device_mode == 'pc'?$boardInfo['bi_skin']:$boardInfo['bi_skin_m']); // 스킨명
		$boardInfo_tmp['_board_dir'] = ($_device_mode == 'pc'?OD_BOARD_SKIN_DIR:OD_BOARD_MSKIN_DIR).'/'.$boardInfo_tmp['_board_skin'];
		$boardInfo_tmp['_board_root'] = $_SERVER['DOCUMENT_ROOT'].$boardInfo_tmp['_board_dir'];
		$boardInfo_tmp['_board_url'] = $system['url'].$boardInfo_tmp['_board_dir'];
		$boardInfo_tmp = array_merge($boardInfo_tmp, $boardInfo); // 배열 순서를 위하여 임시정보와 merge
		$boardInfo = $boardInfo_tmp;
		unset($boardInfo_tmp);
	}

	// -- 댓글쓰기 권한별 처리
	if( $commentData['writeAuth'] !== true){
		switch($commentData['writeAuth']['code']){
			case "9995": // 로그인 안되었을 시
				$commentData['placeholder'] = "로그인 후 댓글을 입력할 수 있습니다.";
				$commentData['writeAuthType'] = "login";
				$commentData['writeAttr'] = " readonly ";
			break;

			default:
				$commentData['placeholder'] = "댓글에 대한 권한이 없습니다.";
				$commentData['writeAuthType'] = "none";
				$commentData['writeAttr'] = " readonly ";
			break;
		}
	}else{ $commentData['placeholder'] = '댓글을 입력해 주세요';  }

	// -- 리캡차 사용여부
	$commentData['recaptchaUse'] = ($siteInfo['recaptcha_api'] != '' && $siteInfo['recaptcha_secret'] != '') ? true : false;
	$commentData['recaptchaUse'] = false;

	// -- 댓글 데이터 가공
	$listComment = array();  // 댓글정보를 담을 배열 초기화
	foreach($resComment as $k=>$v){
		$v['bt_writer'] = $v['bt_writer'] == '' ? '이름없음':$v['bt_writer'];
		$listComment[$k]['uid'] = $v['bt_uid'];
		$listComment[$k]['writer'] = $boardInfo['bi_writer_view_use'] == 'Y' ? $v['bt_writer'] : LastCut($v['bt_writer'],1); // 글쓴이
		$listComment[$k]['deleteAuth'] = $v['bt_inid'] == get_userid() || is_admin() === true ? true : false;
		$listComment[$k]['content'] = nl2br(htmlspecialchars(stripslashes($v['bt_content'])));
		$listComment[$k]['rdate'] = date("Y-m-d H:i:s",strtotime($v['bt_rdate']));

		$userChk = _MQ_result("select in_userlevel from smart_individual where in_id = '".$v['bt_inid']."'  ");
		if($userChk == '9'){ $listComment[$k]['writer'] = $v['bt_writer']; }

	}

	// 스킨폴더에서 해당 파일 호출
	include_once($boardInfo['_board_root'].'/board.view.comment.php');
?>


<?php if($ajaxMode == ''){ // ajaxMode 가 없을경우에만 실행 (즉 한번만 실행 )?>
<script>
	$(document).on('click','form[name="boardComment"] [name="_content"]',function(){
		<?php if( $commentData['writeAuthType'] == 'login'){ ?>
		if( confirm("로그인 후 이용가능합니다.\n로그인 하시겠습니까?") == false){ return false; }
		location.href="/?pn=member.login.form&_rurl=<?php echo urlencode("/?".$_SERVER['QUERY_STRING']); ?>";
		return false;
		<?php } ?>
	});

	// -- 등록 시
	$(document).on('submit','form[name="boardComment"]',function(){
		var chkContent = $(this).find('[name="_content"]').val();
		<?php if( $commentData['writeAuthType'] == 'none'){ ?>
		alert('<?php echo $commentData['placeholder']; ?>');
		return false;
		<?php } ?>

		<?php if( $commentData['writeAuthType'] == 'login'){ ?>
		if( confirm("로그인 후 이용가능합니다.\n로그인 하시겠습니까?") == false){ return false; }
		location.href="/?pn=member.login.form&_rurl=<?php echo urlencode("/?".$_SERVER['QUERY_STRING']); ?>";
		return false;
		<?php } ?>

		// -- 댓글 미입력
		if( chkContent.replace(/\s/gi,"") == ''){ $(this).find('[name="_content"]').focus(); alert("댓글을 입력해 주세요."); return false; }

		// -- ajax 처리
		var url = '<?php echo OD_PROGRAM_URL.'/board.view.comment.pro.php'; ?>';
		var formData = $(this).serialize();
		$.ajax({
				url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'add', formData : formData }, success: function(data){
				if(data.rst == 'success'){
					viewCommentList();
				}else{
					alert(data.msg);
				}
			},error:function(request,status,error){ console.log(request.responseText);}
		});
		return false;
	});

	// -- 댓글삭제 시
	$(document).on('click','.delete-comment',function(){
		if( confirm("선택하신 댓글을 삭제 하시겠습니까?") == false){ return false; }
		var _uid = $(this).attr('data-uid');
		if( _uid == undefined || _uid == ''){	alert('삭제할 수 없습니다.'); return false; }
		var url = '<?php echo OD_PROGRAM_URL.'/board.view.comment.pro.php'; ?>';
		var formData = $('form[name="boardComment"]').serialize();
		$.ajax({
				url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'delete', _uid : _uid, formData : formData  }, success: function(data){
				if(data.rst == 'success'){
					viewCommentList();
				}else{
					alert(data.msg);
				}
			},error:function(request,status,error){ console.log(request.responseText);}
		});
		return false;
	});

	// -- 댓글노출
	function viewCommentList()
	{
		// -- ajax 처리
		var url = '<?php echo OD_PROGRAM_URL.'/board.view.comment.php'; ?>';
		var formData = $('form[name="boardComment"]').serialize();
		$.ajax({
				url: url, cache: false,dataType : 'html', type: "get", data: formData+'&ajaxMode=view', success: function(html){
				$('.comment-reply-box').html(html);
			},error:function(request,status,error){ console.log(request.responseText);}
		});
	}

</script>
<?php } ?>