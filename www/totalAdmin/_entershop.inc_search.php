<?php

	// --------------------- 입점업체 검색폼 부분 ---------------------
	//			해당 파일 include 전 s_query가 정의되어야 함.
	//			예) $s_query = " from smart_individual as indr where 1 ";

	// 추가파라메터
	if(!$arr_param) $arr_param = array();

	// 검색 체크
	$pass_input_type = $pass_input_type == '' ? 'all':$pass_input_type;

	// -- 검색시작 -- {{{
	if($searchMode == "true"){
		// -- 전체검색이라면
		if( $pass_id !="" )		{ $s_query .= " and cp_id like '%".$pass_id."%' "; } // 아이디
		if( $pass_name !="")	{ $s_query .= " and cp_name like '%".$pass_name."%' "; } // 업체명
		if( $pass_ceoname !="")		{ $s_query .= " and cp_ceoname like '%".$pass_ceoname."%' "; } // 업체 대표자명
		if( $pass_email !="")	{ $s_query .= " and cp_email like '%".$pass_email."%' "; } // 담당자 이메일
		if( $pass_tel !="")	{ $s_query .= " and REPLACE(cp_tel, '-','') like '%".rm_str($pass_tel)."%' "; } // 대표전화
		if( $pass_tel2 !="")	{ $s_query .= " and REPLACE(cp_tel2, '-','') like '%".rm_str($pass_tel2)."%' "; } // 담당자 전화번호
	}

	// -- 검색종료 -- }}}


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
					<col width="140"/><col width="*"/><col width="140"/><col width="*"/><col width="140"/><col width="*"/>
				</colgroup>
				<tbody>
				<tr>
					<th>아이디</th>
					<td><input type="text" name="pass_id" class="design"  value="<?php echo $pass_id; ?>"></td>
					<th>업체명</th>
					<td><input type="text" name="pass_name" class="design"  value="<?php echo $pass_name; ?>"></td>
					<th>대표자</th>
					<td><input type="text" name="pass_ceoname" class="design"  value="<?php echo $pass_ceoname; ?>"></td>
				</tr>
				<tr>
					<th>전화</th>
					<td><input type="text" name="pass_tel" class="design"  value="<?php echo $pass_tel; ?>"></td>
					<th>담당자 이메일</th>
					<td><input type="text" name="pass_email" class="design"  value="<?php echo $pass_email; ?>"></td>
					<th>담당자 휴대폰</th>
					<td><input type="text" name="pass_tel2" class="design"  value="<?php echo $pass_tel2; ?>"></td>
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
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</form>