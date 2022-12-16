<?php
# LDD010
include_once("inc.php");

// string 만들기
switch($pass_mode) {

    // 1차 , 2차 옵션 삭제
    case "2depth_del":
    case "1depth_del":

        // 삭제전 하위정보 확인
        $ique = " select count(*) as cnt from smart_product_option where po_pcode='{$pass_code}' and find_in_set('{$pass_uid}' , po_parent) > 0 and po_depth <= '2' "; // 1차 정보
		$ires = _MQ($ique);
		if($ires['cnt'] > 0) {
            echo "is_subcategory"; // 하위 카테고리가 있음 표시
            exit;
        }

        // 순번재조정
        $r = _MQ(" select * from smart_product_option where po_uid='" . $pass_uid . "' ");
        _MQ_noreturn(" update smart_product_option set po_sort=po_sort-1  where po_pcode='" . $r['po_pcode'] . "' and po_depth='". $r['po_depth'] ."' and po_parent='" . $r['po_uid'] . "' and po_sort > '". $r['po_sort'] ."' ");


		//옵션 삭제 전 옵션 이미지 삭제
		option_img_del($pass_uid);


        // 삭제
        _MQ_noreturn("delete from smart_product_option where po_uid='{$pass_uid}'");
		// 하위카테고리 삭제
        _MQ_noreturn("delete from smart_product_option where find_in_set('{$pass_uid}',po_parent)>0 ");

    break;

    // 2차 옵션 추가
    case "2depth_add":

        $ique = " select * from smart_product_option where po_pcode='{$pass_code}' and po_uid='{$pass_uid}' "; // 1차 정보
        $ir = _MQ($ique);

        // 순번추출
        $r = _MQ(" select ifnull(max(po_sort),0) as max_sort from smart_product_option where po_pcode='" . $pass_code . "' and po_depth='2' and po_parent='" . $ir['po_uid'] . "' ");
        $max_sort = $r['max_sort'] + 1;


        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_product_option set
                po_pcode='{$pass_code}',
                po_poptionname='',
                po_depth='2',
                po_parent='{$ir['po_uid']}',
                po_sort='". $max_sort ."'
        ");

    break;

    // 1차 옵션 추가
    case "1depth_add":

        // 순번추출 - 1차
        $r = _MQ(" select ifnull(max(po_sort),0) as max_sort from smart_product_option where po_pcode='" . $pass_code . "' and po_depth='1' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 1차
        _MQ_noreturn("
            insert smart_product_option set
                po_pcode='{$pass_code}',
                po_poptionname='',
                po_depth='1',
                po_sort='". $max_sort ."'
        ");
        $uid_1depth = mysql_insert_id();

        // 순번추출 - 2차
        $r2 = _MQ(" select ifnull(max(po_sort),0) as max_sort from smart_product_option where po_pcode='" . $pass_code . "' and po_depth='2' and po_parent='" . $uid_1depth . "' ");
        $max_sort2 = $r2['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_product_option set
                po_pcode='{$pass_code}',
                po_poptionname='',
                po_depth='2',
                po_parent='{$uid_1depth}',
                po_sort='". $max_sort2 ."'
        ");

    break;
}


	// 상품정보 추출
	$p_info = get_product_info($pass_code);
	// 부모창이 바뀌어 옵션형태를 가져오지 못할 경우 - 기존 상품정보에서 반영함
	if( !in_array( $app_option1_type , array('normal' , 'color', 'size'))) {$app_option1_type = $p_info['p_option1_type'];}
	if( !in_array( $app_option2_type , array('normal' , 'color', 'size'))) {$app_option2_type = $p_info['p_option2_type'];}
	if( !in_array( $app_option3_type , array('normal' , 'color', 'size'))) {$app_option3_type = $p_info['p_option3_type'];}

?>



<form action="_product_option.pro.php" name="frm_option" id="frm_option" target="common_frame"  method="post" ENCTYPE="multipart/form-data">
<input type="hidden" name="pass_code" value="<?php echo $pass_code; ?>">
<input type="hidden" name="pass_type" value="">
<input type="hidden" name="pass_depth" value="">
<input type="hidden" name="pass_uid" value="">

	<?php
		//1차 추출
		$save_chk = 0;
		$que = " select * from smart_product_option where po_pcode='{$pass_code}' and po_depth='1' order by po_sort asc , po_uid asc ";
		$res = _MQ_assoc($que);

		if(sizeof($res) <= 0){ echo '<!-- 내용없을경우 --><div class="table_form"><div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div></div>'; }
		else{
	?>

		<?php
			// ------------ 1차 옵션 loop ------------
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
						<?php //KAY : 상품일괄업로드 개선 : 2021-07-02 -- 옵션명에서 특수문자 제거 ?>
						<?php $op_str = array(">","|","§"); //변경할 특수문자 종류 배열?>
						<input type="text" name="po_info[<?=$r['po_uid']?>][po_poptionname]" class="design" placeholder="옵션명을 입력해주세요." value="<?php echo str_replace($op_str,"",$r['po_poptionname']); ?>"  style="width:<?=($app_option1_type == 'color' ? '300px;' : '100%')?>" />
						<?php
							// ----- 컬러형일 경우 -----
							if($app_option1_type == 'color') { fn_option_color( $r['po_uid'] , $r['po_color_type'] , $r['po_color_name']); }
						?>
					</td>
					<td>
						<div class="lineup-center">
							<?php echo _InputRadio('po_info['. $r['po_uid'] .'][po_view]', array('Y', 'N'), ($r['po_view']?$r['po_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
						</div>
					</td>
					<td>
						<div class="lineup-center">
							<a href="#none" onclick="f_sort('U' , '1', '<?php echo $r['po_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
							<a href="#none" onclick="f_sort('D' , '1', '<?php echo $r['po_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
							<a href="#none" onclick="category_apply_save('2depth_add', '<?php echo $r['po_uid']; ?>'); return false;" class="c_btn h22">추가</a><!-- 끼워넣기 추가버튼 -->
							<a href="#none" onclick="category_apply_save('1depth_del', '<?php echo $r['po_uid']; ?>'); return false;" class="c_btn h22 gray">삭제</a>
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
					$que2 = " select * from smart_product_option where po_pcode='{$pass_code}' and po_depth='2' and po_parent='{$r['po_uid']}' order by po_sort asc , po_uid asc ";
					$res2 = _MQ_assoc($que2);
					if(sizeof($res2) <= 0){
						echo '<!-- 내용없을경우 --><div class="common_none" style="margin:17px 0 10px 0;;"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div>';
					}
					else {
				?>
					<!-- 해당1차의 2차 옵션 -->
					<table class="table_list">
						<colgroup>
							<col width="*"/><col width="100"/><col width="100"/><col width="70"/><col width="70"/><col width="120"/><col width="150"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col">2차 옵션 정보</th>
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
									if($r2['po_poptionname'] == "" || !$r2['po_poptionname'])  $save_chk ++;
							?>
								<tr class="depth_2<?php echo ($r2['po_view'] == 'Y'?null:' if_option_hide'); ?>">
									<td class="option_depth1 option_name">
										<?php //KAY : 상품일괄업로드 개선 : 2021-07-02 -- 옵션명에서 특수문자 제거 ?>
										<input type="text" name="po_info[<?php echo $r2['po_uid']; ?>][po_poptionname]" class="design" placeholder="옵션명을 입력해주세요." value="<?php echo str_replace($op_str,"",$r2['po_poptionname']); ?>"  style="width:<?=($app_option2_type == 'color' ? '200px;' : '100%')?>" />
										<?php
											// ----- 컬러형일 경우 -----
											if($app_option2_type == 'color') { fn_option_color( $r2['po_uid'] , $r2['po_color_type'] , $r2['po_color_name']); }
										?>
									</td>
									<td><input type="text" name="po_info[<?php echo $r2['po_uid']; ?>][po_poption_supplyprice]" class="design number_style" placeholder="" value="<?php echo number_format($r2['po_poption_supplyprice']); ?>" style="width:100%" /></td>
									<td><input type="text" name="po_info[<?php echo $r2['po_uid']; ?>][po_poptionprice]" class="design number_style" placeholder="" value="<?php echo number_format($r2['po_poptionprice']); ?>" style="width:100%" /></td>
									<td><input type="text" name="po_info[<?php echo $r2['po_uid']; ?>][po_cnt]" class="design number_style" placeholder="" value="<?php echo number_format($r2['po_cnt']); ?>" style="width:100%" /></td>
									<td><input type="text" name="" class="design" placeholder="" value="<?php echo number_format($r2['po_salecnt']); ?>" style="width:100%" disabled /></td>
									<td>
										<div class="lineup-center">
											<?php echo _InputRadio('po_info['. $r2['po_uid'] .'][po_view]', array('Y', 'N'), ($r2['po_view']?$r2['po_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
										</div>
									</td>
									<td>
										<div class="lineup-center">
											<a href="#none" onclick="f_sort('U' , '2', '<?php echo $r2['po_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
											<a href="#none" onclick="f_sort('D' , '2', '<?php echo $r2['po_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
											<a href="#none" onclick="f_insert('2', '<?php echo $r2['po_uid']; ?>'); return false;" class="c_btn h22">삽입</a><!-- 끼워넣기 추가버튼 -->
											<a href="#none" onclick="category_apply_save('2depth_del', '<?php echo $r2['po_uid']; ?>'); return false;" class="c_btn h22 gray">삭제</a>
										</div>
									</td>
								</tr>
							<?php
								}
							?>
						</tbody>
					</table>

				<?php
					} // ------------3차 옵션 loop ------------
				?>


					</td>
				</tr>
			</tbody>
		</table>

		<?php
			} // ------------ 1차 옵션 loop ------------
		?>
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




	// 색상 - 이미지에 따른 파일찾기 / color picker 적용
	$(document).ready(function(){
		// color_type 중 선택된 타입 열기
		$(".color_type:checked").each(function(){
			var check_val = $(this).val();
			var pouid = $(this).data("pouid");
			$(".right_box[data-pouid='"+ pouid +"']").hide(); // 동일 pouid 모두 닫기
			$(".right_box." + check_val + "[data-pouid='"+ pouid +"']").show(); // 동일 pouid 중 선택 열기
		});
	});

	// 색상 이미지 선택 처리
	$(document).on("click" , ".color_type" , function(){
		var check_val = $(this).val();
		var pouid = $(this).data("pouid");
		$(".right_box[data-pouid='"+ pouid +"']").hide(); // 동일 pouid 모두 닫기
		$(".right_box." + check_val + "[data-pouid='"+ pouid +"']").show(); // 동일 pouid 중 선택 열기
	});


</script>