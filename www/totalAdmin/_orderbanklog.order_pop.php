<?PHP
	$app_mode = "popup";
	include_once("inc.header.php");


	// 자동입금로그 정보 추출
	$r = _MQ(" select * from smart_orderbank_log where ob_uid = '{$_uid}' ");
	if(!$r){
		error_msgPopup_s("잘못된 접근입니다.");
	}

	// 검색 체크
	$s_query = " from smart_order as o where o_paymethod = 'online' and o_paystatus = 'N' and o_canceled!='Y' ";
	$pass_type = $pass_type ? $pass_type : "match";
	if($pass_type == "match"){
		$s_query .= " and (replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."' or replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."') ";
		$s_query .= " and o_price_real = '". $r["ob_orderprice"] ."' ";
		//$s_query .= " and replace(o_bank , '-' , '' ) like '%". $r["ob_account"] ."%' ";
	}
	if($pass_type == "name"){
		$s_query .= " and (replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."' or replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."') ";
	}
	if($pass_type == "price"){
		$s_query .= " and o_price_real = '". $r["ob_orderprice"] ."' ";
	}
	//$s_query .= " and `npay_order` = 'N' "; // 네이버페이 주문제외 2016-05-30 LDD

	$que = "
		select
			o.* ,
			(select count(*) from smart_order_product as op where op.op_oordernum=o.o_ordernum) as op_cnt,
			(
				select
					concat(p.p_name , '§' , p.p_img_list_square)
				from smart_order_product as op
				left join smart_product  as p on ( p.p_code=op.op_pcode )
				where op.op_oordernum=o.o_ordernum order by op.op_uid asc limit 1
			) as p_info,
			(select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1) as ocs_cash
		$s_query
		ORDER BY o_rdate desc
	";
	$res = _MQ_assoc($que);

	// -- 은행명 추출 ---
	$arr_bank = array();
	$ex = _MQ_assoc("select bs_bank_name,bs_bank_num from smart_bank_set order by bs_uid asc");
	foreach( $ex as $k=>$v ){
		$arr_bank[rm_str($v["bs_bank_num"])] = $v["bs_bank_name"];
	}

?>



<div class="popup">

	<div class="pop_title"><strong>실시간입금 확인</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">

		<form name="frm" method="get" action="<?php echo $PHP_SELF?>" target="">
		<input type="hidden" name="form_name" value="<?php echo $formname?>">
		<input type="hidden" name="relation_prop_code" value="<?php echo $relation_prop_code?>">
		<input type="hidden" name="_uid" value="<?php echo $_uid?>">

			<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
			<table class="table_form">
				<colgroup>
					<col width="140"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>입금정보</th>
						<td>
							[<?php echo ($arr_bank[$r["ob_account"]] ? $arr_bank[$r["ob_account"]] : "미확인") ?>] <?php echo $r["ob_account"] ?>,
							<font style='color:#DC5F16'><?php echo $r["ob_ordername"]?></font>,
							<font style='color:#DC5F16'>￦<?php echo number_format($r["ob_orderprice"]); ?></font>
							(<?php echo date("Y-m-d H:i:s", strtotime($r["ob_date"])); ?>)
						</td>
					</tr>
					<tr>
						<th>검색유형</th>
						<td>
							<?php echo _InputRadio("pass_type",array("match", "name", "price", "all"), $pass_type, " onchange='document.frm.submit()' ", array("입금정보로 검색(입금자명+입금금액)","입금자명으로 검색","입금금액으로 검색","무통장주문 전체(입금대기)")); ?>
						</td>
					</tr>
					<tr>
						<th>주문외입금</th>
						<td>
							<a href="#none" onclick="select_order('')" class="c_btn h22 black ">주문외입금</a>
							<div class="tip_box">
								<?php echo _DescStr("입금내역중 주문과 관계없는 입금내역은 <em>주문외입금</em>처리하여 <em>처리대기</em>목록에서 <em>처리완료</em>목록으로 이동시킬수 있습니다."); ?>
								<?php echo _DescStr("주문외입금 처리한 후에도 다시 주문과 연동 시킬 수 있습니다."); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>


			<div class="tip_box">
				<?php echo _DescStr("무통장주문중 입금대기 주문만 검색됩니다."); ?>
				<?php echo _DescStr("검색된 주문중 입금자와 입금금액을 확인하신후 정확하게 선택해주시기 바랍니다."); ?>
			</div>

		</form>

	</div>

	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<form name="relationForm2" method="post" action="_product.relation.pro.php">
		<input type="hidden" name="form_name" value="<?php echo $formname?>">
		<input type="hidden" name="relation_prop_code" value="<?php echo $relation_prop_code?>">
		<input type="hidden" name="o_ordernum" value="<?php echo $o_ordernum?>">
		<?php if(sizeof($res) > 0){ ?>

			<!-- 1차 옵션 -->
			<table class="table_list">
				<colgroup>
					<col width="150"><col width="200"><col width="90"><col width="*"><col width="130"><col width="90"><col width="90">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">주문번호</th>
						<th scope="col">주문자 정보</th>
						<th scope="col">상품이미지</th>
						<th scope="col">상품명</th>
						<th scope="col">결제금액</th>
						<th scope="col">주문일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?PHP
						foreach($res as $k=>$v){
							$ex = explode("§" , $v['p_info']);
							$app_pname = $ex[0];

							// 이미지 검사
							if($ex[1] && file_exists('..' . IMG_DIR_PRODUCT . $ex[1])){
								$_p_img = get_img_src($ex[1]);
							}else{
								$_p_img = 'images/thumb_no.jpg';
							}

							// 회원이름
							$app_user_info = _individual_info($v['o_mid']);
					?>
						<tr>
							<td><?php echo $v['o_ordernum']; ?></td>
							<td class="t_left">
								<div class="order_item">
									<div class="title">
										<?php echo ($v['o_memtype']=='Y' ? $app_user_info['in_name'].'<span class="normal">('.$v['o_mid'].')</span>' : $v['o_deposit'].'<FONT COLOR="red">(비회원)</FONT>'); ?>
									</div>
									<div class="title">
										HP : <span class="normal"><?php echo $v['o_ohp']; ?></span>
									</div>
									<div class="title">
										E-mail : <span class="normal"><?php echo $v['o_oemail']; ?></span>
									</div>
									<div class="title">
										입금자명 : <span class="normal"><?php echo ($v['o_deposit']==$r['ob_ordername'] ? '<span class="c_tag blue h18 line">일치</span>' : null);?> <?php echo $v['o_deposit']; ?></span>
									</div>
								</div>
							</td>
							<td class="img80">
								<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($app_pname)); ?>">
							</td>
							<td class="t_left t_black">
								<?php echo $app_pname . ($v['op_cnt'] > 1 ? " 외 " . ($v['op_cnt']-1) . "개" : ""); ?>
							</td>
							<td class="t_right">
								<?php echo ($v['o_price_real']==$r['ob_orderprice'] ? '<span class="c_tag blue h18 line">일치</span>' : null); ?>
								<?php echo number_format($v['o_price_real']); ?>원
							</td>
							<td><?php echo date('Y.m.d', strtotime($v['o_rdate'])); ?></td>
							<td>
								<div class="lineup-center">
									<a href="#none" onclick="select_order('<?php echo $v['o_ordernum']; ?>')" class="c_btn h22 gray">주문연동</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		<?php }else{ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>
		</form>

	</div>


	<!-- 가운데정렬버튼 -->
	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="window.close();" class="c_btn h34 black line normal">창닫기</a></li>
		</ul>
	</div>

</div>





<SCRIPT LANGUAGE="JavaScript">

	function select_order(_ordernum){

		if(_ordernum == ""){
			if(confirm("선택한 입금내역을 주문외입금처리 합니다.\n주문외입금이 맞습니까?")){
				location.href = '_orderbanklog.order_pop.pro.php?_uid=<?php echo $_uid?>&_type=adminC';
			}
		}else{
			if(confirm("선택한 주문을 입금완료처리 합니다.\n선택한 주문이 맞습니까?")){
				location.href = '_orderbanklog.order_pop.pro.php?_uid=<?php echo $_uid?>&_type=adminO&_ordernum=' + _ordernum;
			}
		}
	}

</script>



<?PHP
	include_once("inc.footer.php");
?>