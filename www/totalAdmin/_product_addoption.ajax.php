<?php
# LDD010
include_once("inc.php");

// string 만들기
switch( $pass_mode ){

    // 1차 , 2차 옵션 삭제
    case "2depth_del":
    case "1depth_del":

        // 삭제전 하위정보 확인
        $ique = " select count(*) as cnt from smart_product_addoption where pao_pcode='{$pass_code}' and find_in_set('{$pass_uid}' , pao_parent) > 0 "; // 1차 정보
		$ires = _MQ($ique);
		if($ires['cnt'] > 0) {
            echo "is_subcategory"; // 하위 카테고리가 있음 표시
            exit;
        }

        // 순번재조정
        $r = _MQ(" select * from smart_product_addoption where pao_uid='" . $pass_uid . "' ");
        _MQ_noreturn(" update smart_product_addoption set pao_sort=pao_sort-1  where pao_pcode='" . $r['pao_pcode'] . "' and pao_depth='". $r['pao_depth'] ."' and pao_parent='" . $r['pao_uid'] . "' and pao_sort > '". $r['pao_sort'] ."' ");

        // 삭제
        _MQ_noreturn("delete from smart_product_addoption where pao_uid='{$pass_uid}'");

        break;




    // 2차 옵션 추가
    case "2depth_add":

        $ique = " select * from smart_product_addoption where pao_pcode='{$pass_code}' and pao_uid='{$pass_uid}' "; // 1차 정보
        $ir = _MQ($ique);

        // 순번추출
        $r = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $ir['pao_uid'] . "' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_product_addoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='2',
                pao_parent='{$ir['pao_uid']}',
                pao_sort='". $max_sort ."'
        ");

        break;



    // 1차 옵션 추가
    case "1depth_add":

        // 순번추출 - 1차
        $r = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='1' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 1차
        _MQ_noreturn("
            insert smart_product_addoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='1',
                pao_sort='". $max_sort ."'
        ");
        $uid_1depth = mysql_insert_id();

        // 순번추출 - 2차
        $r2 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $uid_1depth . "' ");
        $max_sort2 = $r2['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_product_addoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='2',
                pao_parent='{$uid_1depth}',
                pao_sort='". $max_sort2 ."'
        ");

        break;

}


?>



<form action="_product_addoption.pro.php" name="frm_option" id="frm_option" target="common_frame"  method="post">
<input type="hidden" name="pass_code" value="<?php echo $pass_code; ?>">
<input type="hidden" name="pass_type" value="">
<input type="hidden" name="pass_depth" value="">
<input type="hidden" name="pass_uid" value="">


	<?php
		//1차 추출
		$que = " select * from smart_product_addoption where pao_pcode='{$pass_code}' and pao_depth='1' order by pao_uid ";
		$res = _MQ_assoc($que);
		if(sizeof($res) <= 0){ echo '<!-- 내용없을경우 --><div class="table_form"><div class="common_none" style="margin:50px 0 45px 0;;"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div></div>'; }
		else{
	?>

		<?php
			foreach($res as $k=>$r) {
		?>

		<table class="table_list">
			<colgroup>
				<col width="70"/><col width="*"/><col width="120"/><col width="161"/>
			</colgroup>
			<tbody>

				<tr class="option_depth1">
					<td>
						<span class="fr_bullet">1차</span>
					</td>
					<td>
						<input type="text" name="pao_info[<?=$r['pao_uid']?>][pao_poptionname]" class="design" placeholder="옵션명을 입력해주세요." value="<?=$r['pao_poptionname']?>" style="width:100%;" />
					</td>
					<td>
						<div class="lineup-center">
							<?php echo _InputRadio('pao_info['. $r['pao_uid'] .'][pao_view]', array('Y', 'N'), ($r['pao_view']?$r['pao_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
						</div>
					</td>
					<td>
						<div class="lineup-center">
							<a href="#none" onclick="f_sort('U' , '1', '<?php echo $r['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
							<a href="#none" onclick="f_sort('D' , '1', '<?php echo $r['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
							<a href="#none" onclick="category_apply_save('2depth_add', '<?php echo $r['pao_uid']; ?>'); return false;" class="c_btn h22">추가</a><!-- 끼워넣기 추가버튼 -->
							<a href="#none" onclick="category_apply_save('1depth_del', '<?php echo $r['pao_uid']; ?>'); return false;" class="c_btn h22 gray">삭제</a>
						</div>
					</td>
				</tr>

				<tr>
					<td class="option_depth2"><!-- 3차인 경우 클래스값 option_depth3 -->
						<span class="fr_bullet">2차</span>
					</td>
					<td colspan="3">
					<?php
						// ------------ 2차 옵션 loop ------------
						$que2 = " select * from smart_product_addoption where pao_pcode='{$pass_code}' and pao_depth='2' and pao_parent='{$r['pao_uid']}' order by pao_sort asc , pao_uid asc ";
						$res2 = _MQ_assoc($que2);
						if(sizeof($res2) <= 0){
							echo '<!-- 내용없을경우 --><div class="common_none" style="margin:17px 0 10px 0;;"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div>';
						}
						else {
					?>
							<!-- 2차 옵션 -->
							<table class="table_list">
								<colgroup>
									<col width="*"><col width="120"><col width="120"><col width="70"><col width="70"><col width="120"/><col width="150"/>
								</colgroup>
								<thead>
									<tr>
										<th scope="col">2차 옵션명</th>
										<th scope="col">공급가(원)</th>
										<th scope="col">판매가(원)</th>
										<th scope="col">재고량</th>
										<th scope="col">판매량</th>
										<th scope="col">노출여부</th>
										<th scope="col">관리</th>
									</tr>
								</thead>
								<tbody>
							   <?php
								foreach($res2 as $k2=>$r2) {
									if($r2['pao_poptionname'] == "" || !$r2['pao_poptionname']) $save_chk ++;
								?>
									<tr class="depth_2<?php echo ($r2['pao_view'] == 'Y'?null:' if_option_hide'); ?>">
										<td class="option_name"><input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_poptionname]" class="design" placeholder="" value="<?php echo $r2['pao_poptionname']; ?>" style="width:100%"></td>
										<td><input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_poptionpurprice]" class="design number_style" placeholder="" value="<?php echo number_format($r2['pao_poptionpurprice']); ?>" style="width:100%"></td>
										<td><input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_poptionprice]" class="design number_style" placeholder="" value="<?php echo number_format($r2['pao_poptionprice']); ?>" style="width:100%"></td>
										<td><input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_cnt]" class="design number_style" placeholder="" value="<?php echo number_format($r2['pao_cnt']); ?>" style="width:100%"></td>
										<td><input type="text" name="" class="design number_style" placeholder="" value="<?php echo number_format($r2['pao_salecnt']); ?>" style="width:100%" disabled=""></td>
										<td>
											<div class="lineup-center">
												<?php echo _InputRadio('pao_info['. $r2['pao_uid'] .'][pao_view]', array('Y', 'N'), ($r2['pao_view']?$r2['pao_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
											</div>
										</td>
										<td>
											<div class="lineup-center">
												<a href="#none" onclick="f_sort('U' , '2', '<?php echo $r2['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
												<a href="#none" onclick="f_sort('D' , '2', '<?php echo $r2['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
												<a href="#none" onclick="f_insert('2', '<?php echo $r2['pao_uid']; ?>'); return false;" class="c_btn h22">추가</a><!-- 끼워넣기 추가버튼 -->
												<a href="#none" onclick="category_apply_save('2depth_del', '<?php echo $r2['pao_uid']; ?>'); return false;" class="c_btn h22 gray">삭제</a>
											</div>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						<?php } ?>

						</td>
					</tr>
				</tbody>
			</table>
		<?php } ?>
	<?php } ?>


	<input type="hidden" name="no_save_num" value="<?php echo $save_chk; ?>">


</form>


<script>
// 옵션 숨기기 효과 {
//$(function() {
//
//    $('.btn_hide_input').on('click', function() {
//
//        var checked = $(this).is(':checked');
//        if(checked === true) {
//
//            $(this).closest('li').removeClass('if_option_hide');
//            $(this).closest('label').attr('title', '옵션 숨기기');
//            $('.ui-tooltip-content').html('옵션 숨기기');
//        }
//        else {
//
//            $(this).closest('li').removeClass('if_option_hide').addClass('if_option_hide');
//            $(this).closest('label').attr('title', '옵션 보이기');
//            $('.ui-tooltip-content').html('옵션 보이기');
//        }
//
//        category_apply();
//    });
//});
// } 옵션 숨기기 효과
</script>