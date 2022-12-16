<?php
	// 관련상품 개수 추출
	$relation_cnt = number_format(count(array_filter(explode('|', stripslashes($row['p_relation'])))));

	// 관련상품 노출용데이터 추출
	$ex_relation = array_filter(explode('|', stripslashes($row['p_relation'])));
	$arr_str_view = array();
	if(sizeof($ex_relation)>0){
		foreach($ex_relation as $k=>$v){
			$_tmp = _MQ(" select p_name from smart_product where p_code = '". $v ."' ");
			$arr_str_view[] = stripslashes($_tmp['p_name']) . '('. $v .')';
		}
	}
	$app_str_view = implode(" | " , $arr_str_view);
?>
<tr>
	<th>관련 상품 설정</th>
	<td colspan="3">
		<?php echo _InputRadio('_relation_type', array('none' , 'category' , 'manual'), ($row['p_relation_type'] ? $row['p_relation_type'] : 'none'), '', array('사용안함' , '자동지정 (동일 카테고리 상품 랜덤 노출)' , '수동지정 (직접 선택한 상품 노출)') , ''); ?>
		<!-- 수동일경우에 노출 -->
		<a href="#none" onclick="relationWin();return false;" class="c_btn h27 black js_relation_hide" style="<?php echo ($row['p_relation_type'] <> 'manual' ? 'display:none;' : null); ?>">관련상품 등록/수정</a>
		<span class="fr_tx t_orange js_relation_hide" style="<?php echo ($row['p_relation_type'] <> 'manual' ? 'display:none;' : null); ?>">(현재 선택된 관련상품 총 <span class="js_relation_cnt"><?php echo $relation_cnt; ?></span>개)</span>
		<div class="dash_line"><!-- 점선라인 --></div>							
		<!-- 관련상품 노출용 데이터 --><textarea name="" rows="3" cols="" class="js_relation_view design<?php echo ($row['p_relation_type'] <> 'manual' ? ' disabled' : null); ?>" readonly="" onclick="relationHelp(); return false;"><?php echo stripslashes($app_str_view); ?></textarea>
		<!-- 관련상품 실 데이터 --><input type="hidden" name="_relation" value="<?php echo stripslashes($row['p_relation']); ?>">
	</td>
</tr>
<script>
	//관련상품삭제
	function delField(objTemp) {
		objTemp.value='';
	}

	//관련상품수정/입력
	function relationWin() {
		setCookie('relation_prop_code_<?php echo $row[p_code]?>', $('input[name=_relation]').val());
		window.open('_product.relation.pop.php?relation_code=<?php echo $row[p_code]?>','relation', 'width=1120, height=800, scrollbars=yes');
	}

	//관련상품 안내문구
	function relationHelp() {
		if(!$('.js_relation_view').hasClass('disabled')){
			alert('위 "관련상품 등록/수정"버튼을 이용하여 입력해 주시기 바랍니다.   ');
		}
	}

	// 관련상품 노출 설정시 
	//$(document).ready(controller_realtion_display);
	$(document).on('click', 'input[name=_relation_type]', controller_realtion_display);
	function controller_realtion_display(){
		var selected = $('input[name=_relation_type]:checked').val();
		if(selected == 'manual'){
			$('.js_relation_hide').show();
			$('.js_relation_view').removeClass('disabled')
		}
		else{
			$('.js_relation_hide').hide();
			$('.js_relation_view').addClass('disabled')
		}
	}

	// 관련상품 변경시 관련상품 개수 갱싱
	//$(document).ready(sync_relation_cnt);
	$(document).on('change', 'input[name=_relation]', sync_relation_cnt);
	function sync_relation_cnt(){
		var value = $('input[name=_relation]').val();
		var cnt = 0;
		if(value != '') cnt = value.split('|').length;
		$('.js_relation_cnt').text(cnt.toString().comma());
	}
</script>