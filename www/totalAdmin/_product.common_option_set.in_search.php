	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<?php

		// 추가파라메터
		if(!$arr_param) $arr_param = array();

		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_common_option_set as cos where 1 ";


		// -- 검색시작 -- {{{
		if( $searchMode == 'true') { 

			// -- 검색어가 있을경우
			if($pass_input != ''){
				if( $pass_input_type == 'all'){ // 전체일 시
					$s_query .= " and ( cos_name like '%".$pass_input."%'  or ( select count(*) as cnt from smart_common_option where co_suid = cos.cos_uid and co_poptionname like '%".$pass_input."%'  ) > 0  )  ";
				}else if( $pass_input_type == 'title' ){ // 옵션관리명
					$s_query .= " and cos_name like '%".$pass_input."%' ";						
				}else if( $pass_input_type == 'name' ){ // 옵션명
					$s_query .= " and ( select count(*) as cnt from smart_common_option where co_suid = cos.cos_uid and co_poptionname like '%".$pass_input."%'  ) > 0  ";						
				}
			}

			if( $pass_option_type != '' && in_array($pass_option_type,array('option','addoption')) == true ){ $s_query .= " and  cos_type = '".$pass_option_type."'  "; } // 옵션노출방식
			if( $pass_option_depth != '' && in_array($pass_option_depth,array('1','2','3')) == true ) { $s_query .= " and  cos_depth = '".$pass_option_depth."'  "; } // 옵션의 차수

		}
		// -- 검색종료 -- }}} 


		//	==> s_query 리턴됨.
		
		if(!$listmaxcount) $listmaxcount = 50;
		if(!$listpg) $listpg = 1;
		if(!$st) $st = 'cos_uid';
		if(!$so) $so = 'desc';	
		$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스
		$res = _MQ(" select count(*) as cnt  $s_query ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$res = _MQ_assoc(" select * $s_query order by {$st} {$so}  limit $count , $listmaxcount ");
	?>

	<form name="searchfrm" id="searchfrm" method=get action='<?=$_SERVER["PHP_SELF"]?>'>
	<input type=hidden name="searchMode" value="true">	
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
	<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form if_search">
			<table class="table_form">	
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>검색어</th>
						<td>		
							<select name="pass_input_type">
								<option value="all" <?=$pass_input_type == 'all' ? 'selected' : ''?>>-전체검색-</option>
								<option value="title" <?=$pass_input_type == 'title' ? 'selected' : ''?>>옵션관리명</option>
								<option value="name" <?=$pass_input_type == 'name' ? 'selected' : ''?>>옵션명</option>
							</select>										
							<input type="text" name="pass_input" class="design" style="width:150px" value="<?=$pass_input?>" />
						</td>
						<th>옵션노출방식</th>
						<td>
							<?php echo _InputRadio( 'pass_option_type' , array('', 'option', 'addoption'), ($pass_option_type) , '' , array('전체', '기본옵션', '추가옵션') , ''); ?>
						</td>				
					</tr>

					<tr>
						<th>옵션차수</th>
						<td colspan="3">
							<?php echo _InputRadio( 'pass_option_depth' , array('', '1', '2','3'), ($pass_option_depth) , '' , array('전체', '1차옵션', '2차옵션','3차옵션') , ''); ?>
						</td>								
					</tr>
				

				</tbody> 
			</table>
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php 
						if($searchMode == 'true'){ 
							$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
					?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</form>

	<script>

		$(document).ready(optionTypeInit);
		$(document).on('click','[name="pass_option_type"]',optionTypeInit);

		// -- 옵션노출방식에 따른 스크립트처리 :: 추가옵션의 경우 1차 3차 선택불가능 
		function optionTypeInit()
		{
			var chkVal = $('[name="pass_option_type"]:checked').val();
			var chkDepthVal  = $('[name="pass_option_depth"]:checked').val();
			if( chkVal == 'addoption'){ // 추가옵션이라면
				if( chkDepthVal == '1' || chkDepthVal == '3'){ $('#pass_option_depth').prop('checked',true); } 
				$('#pass_option_depth1').attr('disabled','disabled');
				$('#pass_option_depth3').attr('disabled','disabled');
			}else{
				$('#pass_option_depth1').removeAttr('disabled');
				$('#pass_option_depth3').removeAttr('disabled');
			}
		}
	</script>