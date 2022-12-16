<?PHP
	// LMH005
	include_once("wrap.header.php");


	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_name !="" ) { $s_query .= " and pr_name like '%${pass_name}%' "; }
	if( $pass_code !="" ) { $s_query .= " and pr_code like '%${pass_code}%' "; }
	if( $pass_expire !="" ) { $s_query .= " and pr_expire_date='${pass_expire}' "; }
	if( $pass_use !="" ) { $s_query .= " and pr_use='${pass_use}' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_promotion_code $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);
	$res = _MQ_assoc(" select * from smart_promotion_code {$s_query} ORDER BY pr_uid desc limit $count , $listmaxcount ");
?>

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>프로모션코드 검색</strong>
		<div class="btn_box">
			<a href="_promotion.form.php<?php echo URI_Rebuild('?', array('?_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">프로모션코드 등록</a>
		</div>
	</div>

	<form name="searchfrm" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="_mode" value="search">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

		<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
		<div class="data_form if_search">
			<table class="table_form" summary="검색항목">
				<colgroup>
					<col width="180"/><col width="300"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>프로모션코드명</td>
						<td><input type="text" name="pass_name" class="design" value="<?php echo $pass_name; ?>"></td>
						<th>프로모션코드</td>
						<td><input type="text" name="pass_code" class="design" value="<?php echo $pass_code; ?>"></td>
					</tr>
					<tr>
						<th>사용여부</td>
						<td><?php echo _InputRadio('pass_use', array('','Y','N'), $pass_use, '', array('전체','사용','미사용')); ?></td>
						<th>만료일</td>
						<td><input type="text" name="pass_expire" class="design js_pic_day" value="<?php echo $pass_expire; ?>" style="width:85px;" readonly></td>
					</tr>
					<tr>
						<td colspan="4">
							<div class="tip_box">
								<?php echo _DescStr("고객은 주문 시 프로모션코드를 적용할 수 있으며, 만료일 내에 사용해야 합니다."); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>



			<div class="c_btnbox">
				<ul>
					<li>
						<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
					</li>
					<?php if($_mode == 'search') { ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</form>



	<form name="frm" method="post" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e',$s_query); ?>">

		<!-- 리스트영역 -->
		<div class="data_list">

			<!-- 리스트 제어버튼영역 //-->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					<a href="#none" onclick="select_send('delete'); return false;" class="c_btn h27 gray">선택삭제</a>
				</div>
			</div>
			<!-- // 리스트 제어버튼영역 -->

			<table class="table_list" summary="리스트기본">
				<colgroup>
					<col width="40"><col width="70"><col width="70"><col width="180"><col width="*"><col width="120"><col width="90"><col width="90"><col width="160">
				</colgroup>
				<thead>
					<tr>
						<th><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th>NO</th>
						<th>사용여부</th>
						<th>프로모션코드</th>
						<th>코드명</th>
						<th>할인금액</th>
						<th>만료일</th>
						<th>등록일</th>
						<th>관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					if(sizeof($res)>0){
						foreach($res as $k=>$v) {

							$_num = $TotalCount - $count - $k ;
				?>
							<tr>
								<td><label class="design"><input type="checkbox" name="chk_id[<?php echo $v['pr_uid']; ?>]" class="js_ck" value="Y"></label></td>
								<td><?php echo $_num; ?></td>
								<td>
									<div class="lineup-center">
										<?php echo $arr_adm_button[($v['pr_use'] == 'Y' ? '사용' : '미사용')]; ?>
									</div>
								</td>
								<td><?php echo $v['pr_code']; ?></td>
								<td class="t_left"><?php echo ($v['pr_name'] ? $v['pr_name'] : "-"); ?></td>
								<td><?php echo ($v['pr_type']=='P'?$v['pr_amount']."%":number_format($v['pr_amount'])."원"); ?></td>
								<td><?php echo date('Y.m.d',strtotime($v['pr_expire_date'])); ?></td>
								<td><?php echo date('Y.m.d',strtotime($v['pr_rdate'])); ?></td>
								<td>
									<div class="lineup-vertical">
										<a href="_promotion.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pr_uid'=>$v['pr_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">수정</a>
										<a href="#none" onclick="del('_promotion.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pr_uid'=>$v['pr_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 gray">삭제</a>
									</div>
								</td>
							</tr>
				<?
						}
					}
				?>
				</tbody>
			</table>

			<?php if(count($res) <= 0) { ?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

			<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
			<div class="paginate">
				<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
			</div>

		</div>
	</form>


<script>

	// - 타입별 액션 적용 ---
	function type_action(_type , _mode){
		switch(_type){
			// 삭제
			case "delete":
				$("input[name=_mode]").val(_mode + "_delete");
				$("form[name=frm]").attr("action" , "_promotion.pro.php");
				break;
		}
	}
	// - 타입별 액션 적용 ---

	// - 선택적용 ---
	 function select_send(_type) {
		 if($('.js_ck').is(":checked")){
			type_action(_type , "select");
			 document.frm.submit();
		 }
		 else {
			 alert('1명 이상 선택하시기 바랍니다.');
		 }
	 }
	// - 선택적용 ---

</script>


<?PHP
	include_once("wrap.footer.php");
?>


