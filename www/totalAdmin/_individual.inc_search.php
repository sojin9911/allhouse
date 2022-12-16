<?php

	// --------------------- 회원 검색폼 부분 ---------------------
	//			해당 파일 include 전 s_query가 정의되어야 함.
	//			예) $s_query = " from smart_individual as indr where 1 ";

	// 추가파라메터
	if(!$arr_param) $arr_param = array();

	// 회원타입을 선택하지 않으면 전체선택
	if( count($pass_type) < 1){ $pass_type = array('D','F','K','N'); }

	// 검색 체크
	$pass_input_type = $pass_input_type == '' ? 'all':$pass_input_type;

		// -- 회원등급 선택을 위한 처리
	$resMgs = _MQ_assoc("select *from smart_member_group_set where 1  order by mgs_rank asc ");
	$arrGroupInfo = array();
	foreach( $resMgs as $k=>$v){
		$arrGroupInfo[$v['mgs_uid']] = $v['mgs_name'];
	}


	// -- 검색시작 -- {{{
	if($pass_input != ''){
		// -- 전체검색이라면
		if( $pass_input_type == 'all'){
			$s_query .= " and ( in_id like '%".$pass_input."%' or in_name like '%".$pass_input."%'
			".(rm_str($pass_input) != '' ? " or replace(in_tel,'-','') like '%".rm_str($pass_input)."%' ":"")."
			".(rm_str($pass_input) != '' ? " or replace(in_tel2,'-','') like '%".rm_str($pass_input)."%'":"")."
			".(rm_str($pass_input) != '' ? " or in_email like '%".$pass_input."%'":"")."
			) ";
		}else{
			if( $pass_input_type =="id" )		{ $s_query .= " and in_id like '%".$pass_input."%' "; } // 아이디
			if( $pass_input_type =="name")	{ $s_query .= " and in_name like '%".$pass_input."%' "; } // 성명
			if( $pass_input_type =="tel")		{ $s_query .= " and REPLACE(in_tel, '-','') like '%".rm_str($pass_input)."%' "; } // 전화번호
			if( $pass_input_type =="tel2")	{ $s_query .= " and REPLACE(in_tel2, '-','') like '%".rm_str($pass_input)."%' "; } // 휴대폰
			if( $pass_input_type =="email")	{ $s_query .= " and in_email like '%".$pass_input."%' "; } // 휴대폰
		}
	}

	// -- pass_type 이 있을경우
	if( count($pass_type) > 0){
		$arrTypeQue = array();
		if( in_array('D',$pass_type) == true ){ $arrTypeQue[] = " sns_join = 'N' "; } // 일반
		if( in_array('F',$pass_type) == true ){ $arrTypeQue[] = " fb_join = 'Y' "; } // 페이스북
		if( in_array('K',$pass_type) == true ){ $arrTypeQue[] = " ko_join = 'Y' "; } // 카카오톡
		if( in_array('N',$pass_type) == true ){ $arrTypeQue[] = " nv_join = 'Y' "; } // 네이버
		$s_query .=" and (".implode(" or ",$arrTypeQue).") ";
	}

	// -- 이메일 수신 여부
	if(  in_array($pass_emailsend,array('Y','N')) == true ){
		$s_query .= " and in_emailsend = '${pass_emailsend}' ";
	}

	// -- SMS 수신여부
	if(  in_array($pass_smssend,array('Y','N')) == true ){
		$s_query .= " and in_smssend = '${pass_smssend}' ";
	}

	// -- 회원등급
	if( $pass_mgsuid != ''){
		$s_query .= " and in_mgsuid = '${pass_mgsuid}' ";
	}

	// -- 승인여부
	if(  in_array($pass_auth,array('Y','N')) == true ){
		$s_query .= " and in_auth = '${pass_auth}' ";
	}


	// -- 휴면회원일 시 추가
	if( $CURR_FILENAME == '_individual_sleep.list.php'){
			if($pass_insmailing ) { // 휴면메일 발송이 있을경우
				$s_query .= " and ins_mailing = '${pass_insmailing}' ";
			}
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
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>검색어</th>
						<td>
							<select name="pass_input_type">
								<option value="all" <?=$pass_input_type == 'all' ? 'selected' : ''?>>-전체검색-</option>
								<option value="id" <?=$pass_input_type == 'id' ? 'selected' : ''?>>아이디</option>
								<option value="name" <?=$pass_input_type == 'name' ? 'selected' : ''?>>성명</option>
								<option value="email" <?=$pass_input_type == 'email' ? 'selected' : ''?>>이메일</option>
								<option value="tel" <?=$pass_input_type == 'tel' ? 'selected' : ''?>>전화</option>
								<option value="tel2" <?=$pass_input_type == 'tel2' ? 'selected' : ''?>>휴대폰</option>
							</select>
							<input type="text" name="pass_input" class="design" style="width:150px" value="<?=$pass_input?>" />
						</td>
						<th>회원구분</th>
						<td>
							<?php echo _InputCheckbox( 'pass_type' , array('D','F','K','N'), (($pass_type)) , '' , array('일반','페이스북','카카오톡','네이버') , '') ?>
						</td>
					</tr>
					<tr>
						<th>이메일 수신 여부</th>
						<td>
							<?php echo _InputRadio( 'pass_emailsend' , array('', 'Y', 'N'), ($pass_emailsend) , '' , array('전체', '수신허용', '수신거부') , ''); ?>
						</td>
						<th>문자(SMS) 수신 여부</th>
						<td>
							<?php echo _InputRadio( 'pass_smssend' , array('', 'Y', 'N'), ($pass_smssend) , '' , array('전체', '수신허용', '수신거부') , ''); ?>
						</td>
					</tr>
					<tr>
						<th>회원등급</th>
						<td>
							<?php  echo _InputSelect( "pass_mgsuid" , array_keys($arrGroupInfo) , $pass_mgsuid , " class='' " , array_values($arrGroupInfo), '' );  ?>
						</td>
						<th>승인여부</th>
						<td>
							<?php echo _InputRadio( 'pass_auth' , array('', 'Y', 'N'), ($pass_auth) , '' , array('전체', '승인', '미승인') , ''); ?>
						</td>
					</tr>
					<?php if( $CURR_FILENAME == '_individual_sleep.list.php'){ ?>
					<tr>
						<th>휴면메일발송여부</th>
						<td>
							<?php echo _InputRadio( 'pass_insmailing' , array('', 'Y', 'N'), ($pass_insmailing) , '' , array('전체', '발송', '미발송') , ''); ?>
						</td>
						<th></th>
						<td>

						</td>
					</tr>
					<?php } ?>
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