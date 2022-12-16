<?php
# LDD010
include_once("inc.php");

// string 만들기
switch($pass_mode) {

    // 1차 , 2차 옵션 삭제
    case "2depth_del":
    case "1depth_del":

        // 삭제전 하위정보 확인
        $ique = " select count(*) from smart_common_option where co_suid='{$pass_common_uid}' and find_in_set('{$pass_uid}' , co_parent) > 0 and co_depth <= '2' "; // 1차 정보
        $ires = mysql_query($ique);
        if(mysql_result($ires,0,0) > 0) {

            echo "is_subcategory"; // 하위 카테고리가 있음 표시
            exit;
        }

        // 순번재조정
        $r = _MQ(" select * from smart_common_option where co_uid='" . $pass_uid . "' ");
        _MQ_noreturn(" update smart_common_option set co_sort=co_sort-1  where co_suid='" . $r['co_suid'] . "' and co_depth='". $r['co_depth'] ."' and co_parent='" . $r['co_uid'] . "' and co_sort > '". $r['co_sort'] ."' ");


		//자주쓰는 옵션 삭제 전 자주쓰는 옵션 이미지 삭제
		common_option_img_del($pass_uid);


        // 삭제
        _MQ_noreturn("delete from smart_common_option where co_uid='{$pass_uid}'");
    break;

    // 2차 옵션 추가
    case "2depth_add":

        $ique = " select * from smart_common_option where co_suid='{$pass_common_uid}' and co_uid='{$pass_uid}' "; // 1차 정보
        $ir = _MQ($ique);

        // 순번추출
        $r = _MQ(" select ifnull(max(co_sort),0) as max_sort from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='2' and co_parent='" . $ir['co_uid'] . "' ");
        $max_sort = $r['max_sort'] + 1;


        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_common_option set
                co_suid='{$pass_common_uid}',
                co_poptionname='',
                co_depth='2',
                co_parent='{$ir['co_uid']}',
                co_sort='". $max_sort ."'
        ");
    break;

    // 1차 옵션 추가
    case "1depth_add":

        // 순번추출 - 1차
        $r = _MQ(" select ifnull(max(co_sort),0) as max_sort from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='1' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 1차
        _MQ_noreturn("
            insert smart_common_option set
                co_suid='{$pass_common_uid}',
                co_poptionname='',
                co_depth='1',
                co_sort='". $max_sort ."'
        ");
        $uid_1depth = mysql_insert_id();

        // 순번추출 - 2차
        $r2 = _MQ(" select ifnull(max(co_sort),0) as max_sort from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='2' and co_parent='" . $uid_1depth . "' ");
        $max_sort2 = $r2['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_common_option set
                co_suid='{$pass_common_uid}',
                co_poptionname='',
                co_depth='2',
                co_parent='{$uid_1depth}',
                co_sort='". $max_sort2 ."'
        ");
    break;
}


	// 상품정보 추출
	$co_info = _MQ("select *from smart_common_option_set where cos_uid = '".$pass_common_uid."' ");
	// 부모창이 바뀌어 옵션형태를 가져오지 못할 경우 - 기존 상품정보에서 반영함
	if( !in_array( $app_option1_type , array('normal' , 'color', 'size'))) {$app_option1_type = $co_info['co_option1_type'];}
	if( !in_array( $app_option2_type , array('normal' , 'color', 'size'))) {$app_option2_type = $co_info['co_option2_type'];}
	if( !in_array( $app_option3_type , array('normal' , 'color', 'size'))) {$app_option3_type = $co_info['co_option3_type'];}

?>


<form action="_product.common_option.pro.php" name="frm_option" id="frm_option" target="common_frame"  method="post" method="post" ENCTYPE="multipart/form-data">
<input type="hidden" name="pass_common_uid" value="<?php echo $pass_common_uid; ?>">
<input type="hidden" name="pass_type" value="">
<input type="hidden" name="pass_depth" value="">
<input type="hidden" name="pass_uid" value="">

	<?php
		//1차 추출
		$save_chk = 0;
		$que = " select * from smart_common_option where co_suid='{$pass_common_uid}' and co_depth='1' order by co_sort asc , co_uid asc ";
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
						<input type="text" name="co_info[<?php echo $r['co_uid']; ?>][co_poptionname]" class="design" placeholder="옵션명을 입력해주세요." value="<?=$r['co_poptionname']?>" style="width:<?=($app_option1_type == 'color' ? '300px;' : '100%')?>" />
						<?php
							// ----- 컬러형일 경우 -----
							if($app_option1_type == 'color') { fn_common_option_color( $r['co_uid'] , $r['co_color_type'] , $r['co_color_name']); }
						?>
					</td>
					<td>
						<div class="lineup-center">
							<?php echo _InputRadio('co_info['. $r['co_uid'] .'][co_view]', array('Y', 'N'), ($r['co_view']?$r['co_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
						</div>
					</td>
					<td>
						<div class="lineup-center">
							<a href="#none" onclick="f_sort('U' , '1', '<?php echo $r['co_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
							<a href="#none" onclick="f_sort('D' , '1', '<?php echo $r['co_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
							<a href="#none" onclick="category_apply_save('2depth_add', '<?php echo $r['co_uid']; ?>'); return false;" class="c_btn h22">추가</a><!-- 끼워넣기 추가버튼 -->
							<a href="#none" onclick="category_apply_save('1depth_del', '<?php echo $r['co_uid']; ?>'); return false;" class="c_btn h22 gray">삭제</a>
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
						$que2 = " select * from smart_common_option where co_suid='{$pass_common_uid}' and co_depth='2' and co_parent='{$r['co_uid']}' order by co_sort asc , co_uid asc ";
						$res2 = _MQ_assoc($que2);
						if(sizeof($res2) <= 0){
							echo '<!-- 내용없을경우 --><div class="common_none" style="margin:17px 0 10px 0;;"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div>';
						}
						else {
					?>
						<table class="table_list">
							<colgroup>
								<col width="*"/><col width="100"/><col width="100"/><col width="70"/><col width="120"/><col width="150"/>
							</colgroup>
							<thead>
								<tr>
									<th scope="col">2차 옵션명</th>
									<th scope="col">공급가(원)</th>
									<th scope="col">판매가(원)</th>
									<th scope="col">재고량</th>
									<th scope="col">노출여부</th>
									<th scope="col">관리</th>
								</tr>
							</thead>
							<tbody>
						   <?php
							foreach($res2 as $k2=>$r2) {
								if($r2['co_poptionname'] == "" || !$r2['co_poptionname']) $save_chk ++;
							?>
								<tr class="depth_2<?php echo ($r2['co_view'] == 'Y'?null:' if_option_hide'); ?>">
									<td class="option_name">
										<input type="text" name="co_info[<?php echo $r2['co_uid']; ?>][co_poptionname]" class="design" placeholder="옵션명을 입력해주세요." value="<?php echo $r2['co_poptionname']; ?>" style="width:<?=($app_option2_type == 'color' ? '200px;' : '100%')?>" />
										<?php
											// ----- 컬러형일 경우 -----
											if($app_option2_type == 'color') { fn_common_option_color( $r2['co_uid'] , $r2['co_color_type'] , $r2['co_color_name']); }
										?>
									</td>
									<td><input type="text" name="co_info[<?php echo $r2['co_uid']; ?>][co_poption_supplyprice]" class="design number_style" placeholder="" value="<?php echo number_format($r2['co_poption_supplyprice']); ?>" style="width:100%"></td>
									<td><input type="text" name="co_info[<?php echo $r2['co_uid']; ?>][co_poptionprice]" class="design number_style" placeholder="" value="<?php echo number_format($r2['co_poptionprice']); ?>" style="width:100%"></td>
									<td><input type="text" name="co_info[<?php echo $r2['co_uid']; ?>][co_cnt]" class="design number_style" placeholder="" value="<?php echo number_format($r2['co_cnt']); ?>" style="width:100%"></td>

									<td>
										<div class="lineup-center">
											<?php echo _InputRadio('co_info['. $r2['co_uid'] .'][co_view]', array('Y', 'N'), ($r2['co_view']?$r2['co_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
										</div>
									</td>
									<td>
										<div class="lineup-center">
											<a href="#none" onclick="f_sort('U' , '2', '<?php echo $r2['co_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
											<a href="#none" onclick="f_sort('D' , '2', '<?php echo $r2['co_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
											<a href="#none" onclick="f_insert('2', '<?php echo $r2['co_uid']; ?>'); return false;" class="c_btn h22">추가</a><!-- 끼워넣기 추가버튼 -->
											<a href="#none" onclick="category_apply_save('2depth_del', '<?php echo $r2['co_uid']; ?>'); return false;" class="c_btn h22 gray">삭제</a>
										</div>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>

					<?php
						} // ------------2차 옵션 loop ------------
					?>

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
$(function() {

/*    $('.btn_hide_input').on('click', function() {

        var checked = $(this).is(':checked');
        if(checked === true) {

            $(this).closest('li').removeClass('if_option_hide');
            $(this).closest('label').attr('title', '옵션 숨기기');
            $('.ui-tooltip-content').html('옵션 숨기기');
        }
        else {

            $(this).closest('li').removeClass('if_option_hide').addClass('if_option_hide');
            $(this).closest('label').attr('title', '옵션 보이기');
            $('.ui-tooltip-content').html('옵션 보이기');
        }

        category_apply();
    });*/
});
// } 옵션 숨기기 효과





	// 색상 - 이미지에 따른 파일찾기 / color picker 적용
	$(document).ready(function(){
		// color_type 중 선택된 타입 열기
		$(".color_type:checked").each(function(){
			var check_val = $(this).val();
			var couid = $(this).data("couid");
			$(".right_box[data-couid='"+ couid +"']").hide(); // 동일 couid 모두 닫기
			$(".right_box." + check_val + "[data-couid='"+ couid +"']").show(); // 동일 couid 중 선택 열기
		});
	});

	// 색상 이미지 선택 처리
	$(document).on("click" , ".color_type" , function(){
		var check_val = $(this).val();
		var couid = $(this).data("couid");
		$(".right_box[data-couid='"+ couid +"']").hide(); // 동일 couid 모두 닫기
		$(".right_box." + check_val + "[data-couid='"+ couid +"']").show(); // 동일 couid 중 선택 열기
	});


</script>