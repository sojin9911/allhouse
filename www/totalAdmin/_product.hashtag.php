<tr>
	<th>해시태그 설정</th>
	<td colspan="3">
		<span class="fr_tx">해시태그 자동완성 검색</span>
		<input type="text" name="_hashtag_search" id="_hashtag_search" class="design" placeholder="" value="" style="width:150px">
		<a href="#none" onclick="addField(document.frm._hashtag);" class="c_btn h27 icon icon_plus_b">추가하기</a>
		<div class="dash_line"><!-- 점선라인 --></div>
		<textarea name="_hashtag" rows="2" cols="" class="design"><?php echo stripslashes($row['p_hashtag']); ?></textarea>
		<label class="design"><input type="checkbox" name="_hashtag_shuffle" value="Y" <?php echo ($row['p_hashtag_shuffle']=='Y'?'checked':''); ?>>무작위 순서로 노출합니다.</label>
		<div class="tip_box">
			<?php echo _DescStr('다른 상품에 등록된 해시태그를 1글자 이상 입력하시면 자동 완성된 해시태그를 선택할 수 있습니다.'); ?>
			<?php echo _DescStr('해시태그를 콤마(,)로 구분하여 직접 입력 후 추가할 수 있습니다.'); ?>
		</div>
	</td>
</tr>
<script>
	<?php
		$hashtag_row = _MQ_assoc(" select p_hashtag from smart_product where p_code != '".$row['p_code']."' "); $hashtag_array = array();
		foreach($hashtag_row as $hk=>$hv){ $hashtags = explode(',',$hv['p_hashtag']); foreach($hashtags as $hkk=>$hvv) { $hashtag_array[trim($hvv)]++; }}
		$hashtag_array = array_filter(array_keys($hashtag_array));
	?>
	$(document).ready(function(){
		$('input[name=_hashtag_search]').on('keypress',function(e){ if ( e.which == 13 ) e.preventDefault(); });
		var availableTags = [<? $_cnt=0; foreach($hashtag_array as $kkk=>$vvv) { ?>{ value:'<?=$vvv?>', label:'<?=$vvv?>' }<?=($_cnt+1==count($hashtag_array))?'':','?><? $_cnt++; } ?>];
		$( '#_hashtag_search' ).autocomplete({
			source: availableTags,
			focus: function( event, ui ) { $( '[name=_hashtag_search]' ).val( ui.item.value ); return false; },
			select: function( event, ui ) { $( '[name=_hashtag]' ).val( ui.item.value + ',' + $('[name=_hashtag]').val() ); $( '#_hashtag_search' ).val(''); return false; }
		});
	});
	//관련상품삭제
	function delField(objTemp) {
		objTemp.value='';
	}
	// 추가하기
	function addField() {
		if($( '#_hashtag_search' ).val().trim() !='' ) {
			$( '[name=_hashtag]' ).val( $( '#_hashtag_search' ).val() + ($('[name=_hashtag]').val() != '' ? ',' + $('[name=_hashtag]').val() : '') );
			$( '#_hashtag_search' ).val(''); 
		}
		return false;
	}
</script>