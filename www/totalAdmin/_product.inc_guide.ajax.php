<?php
	include_once(dirname(__file__).'/inc.php');

	if($_code && !$row){
		$que = " select * from smart_product where p_code='${_code}'  ";
		$row = _MQ($que);

		// - 텍스트 정보 추출 ---
		if($row['p_code']) $_text_info_extraction = _text_info_extraction( "smart_product" , $row['p_code'] );
		if(is_array($_text_info_extraction)) $row = array_merge($row, $_text_info_extraction);
	}

	$_cpid = $_cpid?$_cpid:$row['p_cpid'];
	$arr_guide_info = getProductGuideInfo($_cpid);

	$is_hit = true;
	$first_type = ''; // 첫번째 항목의 입력타입저장
	$first_content = ''; // 첫번째 항목의 내용 저장
	foreach($arrProGuideType as $k=>$v){
		// 2019-02-11 SSJ :: 기본값 설정 변경
		$row['p_guide_type_'.$k] = $row['p_guide_type_'.$k] ? $row['p_guide_type_'.$k] : 'list';
		$row['p_guide_uid_'.$k] = $row['p_guide_uid_'.$k] ? $row['p_guide_uid_'.$k] : $arr_guide_info[$k]['default'];

		// 선택입력시 리스트업
		$_guide_list = sizeof($arr_guide_info[$k]['title']) > 0 ? $arr_guide_info[$k]['title'] : array();
		$_guide_content = sizeof($arr_guide_info[$k]['content']) > 0 ? $arr_guide_info[$k]['content'] : array();

		// 첫번째 항목의 입력타입저장
		if($is_hit && $first_type == '') $first_type = ($row['p_guide_type_'.$k] ? $row['p_guide_type_'.$k] : 'none');
		// 첫번째 항목의 내용 저장
		if($is_hit && $$first_content == '') $first_content = ($row['p_guide_type_'.$k] ? $row['p_guide_type_'.$k] : 'none') <> 'list' ? $row['p_guide_'.$k] : $_guide_content[$row['p_guide_uid_'.$k]];

?>
	<div class="tab_conts" data-idx="<?php echo $k; ?>" style="<?php echo ($is_hit?null:'display:none;'); ?>">
		<!-- 사용여부/입력방식 선택 -->
		<?php echo _InputRadio('p_guide_type_'.$k, array('none' , 'manual' , 'list'), ($row['p_guide_type_'.$k] ? $row['p_guide_type_'.$k] : 'none'), ' class="js_chg_guide_type" data-idx="'. $k .'" ', array('사용안함' , '직접입력' , '선택입력')); ?>
		<input type="hidden" name="p_guide_type_<?php echo $k; ?>_save" value="<?php echo ($row['p_guide_type_'.$k] ? $row['p_guide_type_'.$k] : 'none'); ?>">

		<!-- 선택입력 항목, 선택입력 선택 시 노출 -->
		<?php echo _InputSelect('p_guide_uid_'.$k, array_keys($_guide_list), ($row['p_guide_uid_'.$k] ? $row['p_guide_uid_'.$k] : $arr_guide_info[$k]['default']), ' onchange="set_guide_info(\''. $k .'\');" style="'. ($row['p_guide_type_'.$k]<>'list'?'display:none;':null) .'" ', array_values($_guide_list)); ?>

		<!-- 선택입력항목 상세내용 저장, 비노출 -->
		<?php foreach($_guide_content as $sk=>$sv){ ?>
			<textarea name="" id="guide_content_<?php echo $sk; ?>" style="display:none;" readonly><?php echo stripslashes($sv); ?></textarea>
		<?php } ?>

		<!-- 직접입력항목 상세내용 저장, 비노출 -->
		<textarea name="p_guide_<?php echo $k; ?>" class="design" style="display:none;"><?php echo stripslashes($row['p_guide_'.$k]); ?></textarea>

	</div>
<?php
	$is_hit=false; }
?>