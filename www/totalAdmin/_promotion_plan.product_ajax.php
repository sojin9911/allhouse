<?php

	include_once("inc.php");

?>


	<input type='hidden' name='smart_promotion_plan_product_setup_mode' value='' />


	<?php
		// 상품정보 추출
		$ppps_que = "
			select p.*, ppps.*
			from smart_promotion_plan_product_setup  as ppps
			left join smart_product as p on (ppps.ppps_pcode = p.p_code)
			where 
				ppps.ppps_ppuid = '". $uid ."'
			order by ppps.ppps_idx asc
		";  //=> SSJ:2017-12-13 상품이 삭제됬을때 선택삭제를 위해 p_code는 smart_promotion_plan_product_setup테이블에서 추출
		$ppps_res = _MQ_assoc($ppps_que);
		if(sizeof($ppps_res) > 0){
	?>
	<table class="table_list">
		<colgroup>
			<col width="40"><col width="70"><col width="120"><col width="90"><col width="*"><col width="100"><col width="80"><col width="70">
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
				<th scope="col">NO</th>
				<th scope="col">순서</th><!-- KAY :: 2021-11-12 :: 순서추가-->
				<th scope="col">이미지</th>
				<th scope="col">상품명</th>
				<th scope="col">판매가</th>
				<th scope="col">재고량</th>
				<th scope="col">노출여부</th>
			</tr>
		</thead> 
		<tbody>
		<?php 
				foreach($ppps_res as $pppsk=>$pppsv){

					$_num = $pppsk + 1 ;

					// 이미지 검사
					$_p_img = get_img_src('thumbs_s_'.$pppsv['p_img_list_square']);
					if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
		?>
			<tr>
				<td>
					<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $pppsv['p_code']; ?>]" class="js_ck class_pcode" value="Y"></label>
				</td>
				<td><?php echo $_num; ?></td>
				<td>
					<div class="lineup-center" style="margin-bottom:5px;">
						<input type="text" name="sort_group[<?php echo $pppsv['p_code'];?>]" value="<?php echo $pppsv['ppps_sort_group']; ?>" class="design number_style sort_group_<?php echo $pppsv['p_code']; ?>" placeholder="" style="width:45px;margin-right:0;">
						<a href="#none" onclick="sort_group('<?php echo $pppsv['p_code'];?>','<?php echo $uid;?>')" class="c_btn h27 " style="width:45px;">수정</a>
					</div>
					<div class="lineup-center">
						<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','up','<?php echo $uid;?>')" class="c_btn h22 icon_up" title="위로"></a>
						<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','down','<?php echo $uid;?>')" class="c_btn h22 icon_down" title="아래로"></a>
						<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','top','<?php echo $uid;?>')" class="c_btn h22 icon_top" title="맨위로"></a>
						<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','bottom','<?php echo $uid;?>')" class="c_btn h22 icon_bottom" title="맨아래로"></a>
					</div>
				</td>
				<td class="img50"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($pppsv['p_name'])); ?>"></td>
				<td class="t_left t_black"><?php echo strip_tags($pppsv['p_name']); ?></td>
				<td class="t_black"><?php echo number_format($pppsv['p_price']); ?>원</td>
				<td><?php echo number_format($pppsv['p_stock']); ?></td>
				<td><div class="lineup-center"><?php echo $arr_adm_button[($pppsv['p_view'] == 'Y' ? '노출' : '숨김')]; ?></div></td>
			</tr>
		<?php
				}
		?>
		</tbody> 
	</table>
	<?php
		}// 상품정보 추출
	?>



	<?php if(sizeof($ppps_res) < 1){ ?>
		<!-- 내용없을경우 -->
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>



<script>
	// 순위조정 up-down-top-bottom
	 function sort_up(pcode,mode,uid) {
		<?php if(pcode && mode){ ?>

			$.ajax({
				url: "_promotion_plan.product_sort.php", 
				cache: false,dataType : 'json', type: "POST", 
				data: {_mode:mode,_uid:uid,pcode:pcode }, 
				success: function(data){
					if(data.rst == 'fail'){
						alert(data.msg);
						return false;
					}
					promotion_plan_product_setup_view(<?php echo $uid;?>);
				},error:function(request,status,error){ console.log(request.responseText); }
			});
		<?php }else{ ?>
			alert('순위조정은 정렬상태가 "노출순위 ▲"인 상태에서만 조정할 수 있습니다,');
		<?php } ?>
	}
	// 순위그룹 수정
	function sort_group(pcode,_uid){
		var group = $('.sort_group_'+ pcode).val()*1;
		if(group <= 0){
			alert('상품 순위를 입력해 주시기 바랍니다.');
			$('.sort_group_'+ pcode).focus();
			return false;
		}

		$.ajax({
			url: "_promotion_plan.product_sort.php", 
			cache: false,dataType : 'json', type: "POST", 
			data: {_mode : 'modify_group',_group:group,_uid:_uid,pcode:pcode }, 
			success: function(data){
				if(data.rst == 'fail'){
					alert(data.msg);
					return false;
				}
				promotion_plan_product_setup_view(<?php echo $uid;?>);
				if(data.msg !=''){
					alert(data.msg);
				}
			},error:function(request,status,error){ console.log(request.responseText); }
		});
	}
</script>