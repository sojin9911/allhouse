<?php 
	// 메뉴 고정
	include_once('wrap.header.php');


	/*
		$siteInfo['s_bbs_forbidden_word'] :: serialize 화 되어있음 
		array['writer']
		array['title']
		array['content']
	*/

	// -- 금지어를 가져 온다 :: serialize
	if( $siteInfo['s_bbs_forbidden_word'] != ''){
		$arrFw = unserialize(stripslashes($siteInfo['s_bbs_forbidden_word']));
		$fwWriter = 	$arrFw['writer'] ; 
		$fwTitle = 		$arrFw['title'] ;
		$fwContent = 	$arrFw['content'] ;
	}
?>
<form id="frmBbsFw" name="frmBbsFw" method="post" action="_bbs.forbidden_word.pro.php">
	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>게시판 금지어 관리</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
			<table class="table_form">	
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>도움말</th>
						<td>
							<div class="tip_box">
								<?php echo _DescStr('게시글 작성 시 금지어를 설정하여 적용할 수 있습니다.'); ?>
								<?php echo _DescStr('금지어 입력후 콤마(,) 또는 엔터(ENTER) 또는 탭(TAB) 키를 입력하시면 됩니다. '); ?>
								<?php echo _DescStr('기본적으로 관리자는 금지어 적용에서 제외됩니다.'); ?>
								<?php echo _DescStr('반드시 하단의 확인버튼을 클릭하여 저장을 하셔야 적용이됩니다.'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<th>작성자</th>
						<td>
							<a href="#none" onclick="return false;" class="c_btn h27 icon icon_plus_b delete-item" data-idx="writer">전체삭제</a>
							<div class="dash_line"><!-- 점선라인 --></div>
							<input type="text" name="fwWriter" class="design js_tag item-writer" value="<?php echo $fwWriter; ?>" style="width:100%;">
							<?php echo _DescStr('게시글 작성시 작성자에 적용될 금지어를 입력해 주세요.'); ?>
						</td>
					</tr>

					<tr>
						<th>제목</th>
						<td>
							<a href="#none" onclick="return false;" class="c_btn h27 icon icon_plus_b delete-item" data-idx="title">전체삭제</a>
							<div class="dash_line"><!-- 점선라인 --></div>
							<input type="text" name="fwTitle" class="design js_tag item-title" value="<?php echo $fwTitle; ?>" style="width:100%;">
							<?php echo _DescStr('게시글 작성시 제목에 적용될 금지어를 입력해 주세요.'); ?>						
						</td>
					</tr>

					<tr>
						<th>내용</th>
						<td>
							<a href="#none" onclick="return false;" class="c_btn h27 icon icon_plus_b delete-item" data-idx="content">전체삭제</a>
							<div class="dash_line"><!-- 점선라인 --></div>							
							<input type="text" name="fwContent" class="design js_tag item-content" value="<?php echo $fwContent; ?>" style="width:100%;">
							<div class="tip_box">
								<?php echo _DescStr('게시글 작성시 내용에 적용될 금지어를 입력해 주세요.'); ?>							
								<?php echo _DescStr('댓글내용도 함께 적용이 됩니다.'); ?>							
							</div>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h46 red"><input type="submit" name="" value="확인"></span></li>
			</ul>
		</div>

</form>


<script>
	$(document).on('click','.delete-item',function(){
		var idx = $(this).attr('data-idx');
		$('.item-'+idx).tagEditor('destroy').val('').tagEditor();
	});
</script>


<?php include_once('wrap.footer.php'); ?>
