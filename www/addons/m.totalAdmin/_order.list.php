<?php
// 페이지 표시
$app_current_page_name = ( $_REQUEST["style"] == "b" ? "무통장주문대기관리" : "주문관리" ) ;
if( $_REQUEST["style"] == "b" ){
	 $_REQUEST["view"] = "online";
}
include dirname(__FILE__)."/wrap.header.php";


// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기

// 추가파라메터
if(!$arr_param) $arr_param = array();

// 결제상태 추가 -- 결제실패
$arr_order_status[] = "결제실패";

// 기본결제상태 지정
if($_REQUEST['view'] == 'online') {
	if($pass_paystatus == '') $pass_paystatus = 'A';
}else{
	if($pass_paystatus == '') $pass_paystatus = 'Y';
}

// 검색 체크
$s_query = " from smart_order as o left join smart_individual as indr on (indr.in_id=o.o_mid) where o_canceled!='Y' and `npay_order` = 'N' ";
if( $pass_ordernum !="" ) { $s_query .= " and o_ordernum like '%${pass_ordernum}%' "; }
if( $pass_pname !="" ) {
	$s_query .= "
		and (
				select count(*)
				from smart_order_product as op
				where op.op_oordernum = o.o_ordernum
					and concat(op.op_pcode,ifnull(op.op_pname,''),ifnull(op.op_option1,''),ifnull(op.op_option2,''),ifnull(op.op_option3,'')) like '%${pass_pname}%'
		) > 0
	";
}
if( $pass_mid !="" ) { $s_query .= " and o_mid like '%${pass_mid}%' "; }
if( $pass_oname !="" ) { $s_query .= " and o_oname like '%${pass_oname}%' "; }
if( $pass_rname !="" ) { $s_query .= " and o_rname like '%${pass_rname}%' "; }
if( $pass_deposit !="" ) { $s_query .= " and o_deposit like '%${pass_deposit}%' "; }
if( $pass_memtype !="" ) { $s_query .= " and o_memtype='${pass_memtype}' "; }
if( $pass_paymethod !="" ) { $s_query .= " and o_paymethod='${pass_paymethod}' "; }
if( $pass_paystatus !="A" ) { $s_query .= " and o_paystatus='${pass_paystatus}' "; }
if( $pass_status !="" ) { $s_query .= " and o_status='${pass_status}' "; }
if( $pass_sdate !="" ) { $s_query .= " and o_rdate>='${pass_sdate}' "; }
if( $pass_edate !="" ) { $s_query .= " and o_rdate<='". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."' "; }
//if( $pass_get_tax =="Y" ) { $s_query .= " and o_get_tax='Y' and ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') = 'AUTH' "; }
//else if( $pass_get_tax =="N" ) { $s_query .= " and o_get_tax='Y' and ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') != 'AUTH' "; }
if( $pass_get_tax =="Y" ) {
	$s_query .= "
		and o_get_tax='Y'
		and (
			ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') = 'AUTH'
			or
			(select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) > 0
		)
	";
}
else if( $pass_get_tax =="N" ) {
	$s_query .= "
		and o_get_tax='Y'
		and (
			ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') != 'AUTH'
			and
			(select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) = 0
		)
	";
}

// 입금대기 주문 목록 검색 지정
if($_REQUEST['view'] == 'online') {
	// 결제방식 - 무통장, 가상계좌
	$s_query .= " and o_paymethod in ('online', 'virtual') ";
	// 결제상태 - 결제대기
	$s_query .= " and o_paystatus='N' ";
	// 주문상태 - 결제대기
	$s_query .= " and o_status='결제대기' ";

	// 가상계좌 주문 체크 - 입금계좌 정보가 있는 주문만
	$s_query .= " and if(o_paymethod='virtual', (select count(*) as cnt from smart_order_onlinelog as ool where ool.ool_ordernum=o.o_ordernum), 1) > 0 ";
}

if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = "if(o_paydate!='0000-00-00 00:00:00', o_paydate, o_rdate)"; // 결제완료일 우선 정렬
$st = stripslashes($st);
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

$res = _MQ(" select count(*) as cnt {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount / $listmaxcount);

$que = "
	select
		o.* , indr.in_id, indr.in_name,
		(select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1) as ocs_cash,
		(select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) as bc_cnt
	{$s_query}
	order by {$st} {$so} limit $count , $listmaxcount
";
$res = _MQ_assoc($que);

$orderstepArray = array(
	"카드결제" => "<span class='red'>카드결제</span>",
	"가상계좌" => "<span class='ygreen'>가상계좌</span>",
	"무통장입금" => "<span class='sky'>무통장입금</span>",
	"계좌이체" => "<span class='brown'>계좌이체</span>",
	"전액적립금결제" => "<span class='purple'>적립금결제</span>",
	"휴대폰결제" => "<span class='cyan'>휴대폰결제</span>",

	"결제대기" => "<span class='gray'>결제대기</span>",
	"결제완료" => "<span class='blue'>결제완료</span>",
	"결제확인" => "<span class='red'>결제확인</span>",

	"현금영수증 요청" => "<span class='gray'>현금영수증</span>",
	"현금영수증 발행" => "<span class='blue'>현금영수증</span>",

	"결제대기"=>"<span class='blue'>결제대기</span>",
	"결제완료"=>"<span class='purple'>결제완료</span>",
	"배송대기"=>"<span class='light'>배송대기</span>",
	"배송준비" => "<span class='ygreen'>배송준비</span>",
	"배송중"=>"<span class='green'>배송중</span>",
	"배송완료"=>"<span class='orange'>배송완료</span>",
	"주문취소"=>"<span class='light'>주문취소</span>",
	"결제실패"=>"<span class='purple'>결제실패</span>",
);
?>



<form role="search" name="searchfrm" method="post" action="<?=$_SERVER["PHP_SELF"]?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="search_type" value="close">
	<input type="hidden" name="style" value="<?=$style?>"/>
	<!-- 상단에 들어가는 검색등 공간 검색닫기를 누르면  if_closed 처음설정을 닫혀있도록 해도 좋을듯.. -->
	<div class="page_top_area if_closed">

		<div class="title_box"><span class="txt">SEARCH</span>
			<div class="before_search">
				<button type="submit" class="btn_search"></button>
				<input type="search" name="pass_oname_tmp" value="<?=$pass_oname_tmp?>" class="input_design" placeholder="주문자명 검색">
			</div>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_close" title="검색닫기">상세검색닫기<span class="shape"></span></a>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_open" title="검색열기">상세검색열기<span class="shape"></span></a>
		</div>

		<!-- ●●●●● 검색폼 -->
		<div class="cm_search_form">
			<ul>
				<li>
					<span class="opt">주문번호</span>
					<div class="value"><input type="text" name="pass_ordernum" class="input_design" value="<?php echo $pass_ordernum; ?>"></div>
				</li>
				<li>
					<span class="opt">주문자명</span>
					<div class="value"><input type="text" name="pass_oname" class="input_design" value="<?php echo $pass_oname; ?>"></div>
				</li>
				<li>
					<span class="opt">주문자 아이디</span>
					<div class="value"><input type="text" name="pass_mid" class="input_design" value="<?php echo $pass_mid; ?>"></div>
				</li>
				<li>
					<span class="opt">입금자명</span>
					<div class="value"><input type="text" name="pass_deposit" class="input_design" value="<?php echo $pass_deposit; ?>"></div>
				</li>
				<li>
					<span class="opt">수령자명</span>
					<div class="value"><input type="text" name="pass_rname" class="input_design" value="<?php echo $pass_rname; ?>"></div>
				</li>
				<li>
					<span class="opt">회원타입</span>
					<div class="value"><?php echo _InputRadio_totaladmin( "pass_memtype" , array('','Y','N'), $pass_memtype , "" , array('전체','회원','비회원')); ?></div>
				</li>
				<?php if($style != 'b') { // 입금대기 주문 목록 지정 ?>
					<li>
						<span class="opt">결제수단</span>
						<div class="value">
							<div class="select">
								<span class="shape"></span>
								<?php echo _InputSelect( "pass_paymethod" , array_keys($arr_payment_type), $pass_paymethod , "" , array_values($arr_payment_type) , '전체'); ?>
							</div>
						</div>
					</li>
					<li>
						<span class="opt">주문상태</span>
						<div class="value">
							<div class="select">
								<span class="shape"></span>
								<?php echo _InputSelect( "pass_status" , $arr_order_status , $pass_status , "" , "" , '전체'); ?>
							</div>
						</div>
					</li>
					<li>
						<span class="opt">결제상태</span>
						<div class="value">
							<?php echo _InputRadio_totaladmin( "pass_paystatus" , array('A','Y','N'), $pass_paystatus , "" , array('전체','결제완료','결제대기') , ''); ?>
						</div>
					</li>
				<?php } else { ?>
					<li>
						<span class="opt">결제수단</span>
						<div class="value"><?php echo _InputRadio_totaladmin( "pass_paymethod" , array('','online','virtual'), $pass_paymethod , "" , array('전체','무통장입금','가상계좌') , ''); ?></div>
					</li>
				<?php } ?>
				<li>
					<span class="opt">주문상품</span>
					<div class="value">
						<input type="text" name="pass_pname" class="input_design" style="" value="<?php echo $pass_pname; ?>">
					</div>
				</li>
				<li>
					<span class="opt">주문일</span>
					<div class="value">
						<input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="input_design js_pic_day" style="width:85px">
						<span class="fr_tx" style="float:left; padding: 0 10px; line-height:35px">-</span>
						<input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="input_design js_pic_day" style="width:85px" data-position="bottom right">
					</div>
				</li>
				<li>
					<span class="opt">현금영수증</span>
					<div class="value">
						<?php echo _InputRadio_totaladmin( "pass_get_tax" , array('','N','Y'), $pass_get_tax , "" , array('전체','발행대기','발행완료')); ?>
					</div>
				</li>


				<li class="ess">
					<span class="opt">목록수</span>
					<div class="value"><div class="select"><span class="shape"></span><?=_InputSelect( "listmaxcount" , array(5,10,20,30,50) , $listmaxcount , "" , "" , "")?></div></div>
				</li>
			</ul>

			<!-- ●●●●● 가운데정렬버튼 -->
			<div class="cm_bottom_button">
				<ul>
					<li><span class="button_pack"><input type="submit" class="btn_md_blue" value="검색하기"></span></li>
					<?if($mode == "search") :?><li><span class="button_pack"><a href="_order.list.php?style=<?=$style?>" class="btn_md_black">전체목록</a></span></li><?endif;?>
				</ul>
			</div>
			<!-- / 가운데정렬버튼 -->
		</div>
	</div>
	<!-- / 상단에 들어가는 검색등 공간 -->
</form>







<form name=frm method=post action="_order.pro.php" ><!-- target="common_frame" -->
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">
<input type=hidden name=_ordernum_array id="_ordernum_array" value=''>
<input type=hidden name="style"value='<?=$style?>'>

<?
	if(sizeof($res) == 0 ) :
		echo "<div class='cm_no_conts'><div class='no_icon'></div><div class='gtxt'>등록된 내용이 없습니다.</div></div>";
	else :
?>
	<!-- 리스트 제어영역 -->
	<div class="top_ctrl_area">
		<label class="allcheck" title="모두선택"><input type="checkbox" name="allchk" /></label>
		<!-- 제어버튼 -->
		<span class="ctrl_button">
			<span class="button_pack"><a href="#none" onclick="mass_cancel();" class="btn_sm_white">선택주문취소</a></span>
		</span>
		<!-- / 제어버튼 -->
	</div>
	<!-- / 리스트 제어영역 -->
<? endif;?>


	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container">

		<!-- ●●●●● 데이터리스트 주문리스트 추가 if_order -->
		<div class="data_list if_order">

<?PHP
	foreach($res as $k=>$v) {

		$_mod = "<span class='button_pack'><a href='_order.form.php?_mode=modify&_ordernum=" . $v[o_ordernum] . "&_PVSC=" . $_PVSC . "' class='btn_sm_blue'>상세보기</a></span>";
		$_del = ($v[o_canceled] == "N" ? "<span class='button_pack'><a href='#none' onclick='del(\"_order.pro.php?_mode=cancel&_ordernum=" . $v[o_ordernum] . "&_PVSC=" . $_PVSC . "\");' class='btn_sm_black'>주문취소</a></span>" : "");

		$_num = $TotalCount - $count - $k ;

		$ex = explode("§" , $v[p_info]);
		$app_pname = $ex[0];
		$app_pimg_list = get_img_src($ex[1]);
		$app_pimg = "";
		if($app_pimg_list) {
			$app_pimg = "<img src='". $app_pimg_list ."' width=80>";
		}

        // 회원이름
        $app_user_info = _individual_info($v[o_mid]);

        // 현금영수증 발행여부 확인
		if($v[o_get_tax]=='Y') {
			$ssque = "select ocs_method from smart_order_cashlog where ocs_ordernum='". $v[ordernum] ."' order by ocs_uid desc limit 1";
			$ssres = _MQ($ssque);
			if($ssres[ocs_method]=='AUTH') { $cash_status = '현금영수증 발행'; } else { $cash_status = '현금영수증 요청'; }
		}
		else { $cash_status = ''; }

		// -- 상품정보 추출 ---
		$tmp_content = ""; // 상품정보 - 문장
		$tmp_pname = ""; // 첫번째 옵션 상품명 임시 저장
		$sque = "
			SELECT op.* , p.p_name
			from smart_order_product as op
			inner join smart_product  as p on ( p.p_code=op.op_pcode )
			where op.op_oordernum='". $v[o_ordernum] ."' order by op.op_uid asc
		";
		$sres = _MQ_assoc($sque);
		foreach($sres as $sk=>$sv) {

			// -- 발송상태 --- LMH001
			if($sv[op_cancel]=='Y') { $app_op_status = "<span class='dark'>주문취소</span>"; }
			else {$app_op_status = $orderstepArray[$sv[op_sendstatus]];}
			// -- 발송상태 ---

			// 옵션값 추출(OrderNumValue:주문번호 offset:주문일련번호)
			// 해당상품에 대한 옵션내역이 있으면
			$itemName = $sv[p_name] . ($sv[op_option1] ? "<div class='sub_option'>(".($sv[op_is_addoption]=="Y" ? "추가" : "선택") . ":" . trim($sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3]) .")" . $sv[op_cnt]."개</div>" : " ".$sv[op_cnt]."개 ");


			// 쿠폰이미지/주의사항이 등록되어있지않으면 취소선으로 표시한다.
			$tmp_content .= "<li><span class='texticon_pack'>". $app_op_status ."</span>" . $itemName . "</li>";

        }
		// -- 상품정보 추출 ---



		unset($_paystatus_ , $orderstep); // 변수 초기화

        // -- 결제상태 ---
        if( $v[o_paystatus] =="Y" ) { $_paystatus_ = $orderstepArray['paystatus'] ; }
        else if(in_array($v[o_paymethod] , array("online" , "point"))) {$_paystatus_ = $orderstepArray['ready'];}
        else {$_paystatus_ = $orderstepArray['wait']; }
        // -- 결제상태 ---

        // -- 결제진행사항 ---
		$orderstep = $orderstepArray[$v[o_status]];
        // -- 결제진행사항 ---


		echo "
			<dl>
				<dd>
					<div class='first_box'>
						<label class='check'><input type='checkbox' name='chk_ordernum[".$v[o_ordernum]."]' value='Y' class=class_ordernum /></label>
						<span class='number'>no.". $_num ."</span>
						<span class='date'>주문일 : ". date("y.m.d",strtotime($v[o_rdate])) ."</span>
					</div>
					<!--  주문정보 -->
					<div class='order_info'>
						<div class='ordernum'>주문번호 : ". $v[o_ordernum] ."</div>
						<div class='name'>주문자 : <span class='txt'>".$v[o_oname]."</span><strong>(".( $v[o_memtype] == "Y" ? $v[o_mid] : "비회원" ).")</strong></div>
						<div class='tel'>연락처 : " . (rm_str($v[o_otel]) > 0 ? "<a href='tel:".$v[o_otel]."'>".$v[o_otel]."</a>" : "") . (rm_str($v[o_ohp]) > 0 ? "<a href='tel:".$v[o_ohp]."'>".$v[o_ohp]."</a>" : "") . "</div>
						<div class='price'>결제정보 : <span class='pay'>". $arr_payment_type[$v[o_paymethod]] ."</span><span class='value'>" . ($v[o_price_real] > 0 ? number_format($v[o_price_real])."원" : "전액적립금") . "</span></span></div>
						<div class='order_item'>
							<div class='order_where'>
								<span class='texticon_pack checkicon'>".($v['mobile'] == 'Y' ? "<span class='green'>모바일주문</span>" : "<span class='blue'>PC주문</span>")."</span>
								<!--
								". ($_paystatus_ ? "<span class='texticon_pack checkicon'>". $_paystatus_ ."</span>" : "") ."
								". ($orderstep ? "<span class='texticon_pack checkicon'>". $orderstep ."</span>" : "") ."
								-->

								<span class='texticon_pack checkicon'>".($v['o_status']?$orderstepArray[$v['o_status']]:$v['결제실패'])."</span>
							</div>
							<ul>
								" . $tmp_content . "
							</ul>
						</div>
					</div>
				</dd>
				<dt>
					<div class='btn_box'>
						<ul>
							<li>". $_mod ."</li>
							<li>". $_del ."</li>
						</ul>
					</div>
				</dt>
			</dl>
		";
	}
?>

		</div>
		<!-- / 데이터리스트 -->

	</div>
	<!-- / 내용들어가는 공간 -->
</form>


	<?=pagelisting_mobile_totaladmin($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>


<SCRIPT>
	// - 결제승인 ---
	 function select_auth_send() {
		 if($('.class_ordernum').is(":checked")){
			$("input[name=_mode]").val("auth");
			$("form[name=frm]")[0].submit();
		 }
		 else {
			 alert('1건 이상 선택시 결제승인이 가능합니다..');
		 }
	 }
	// - 결제승인 ---
	 // - 선택취소 ---
	  function mass_cancel() {
	 	var c=confirm('정말 주문을 취소하시겠습니까?');
	 	if(c) {
			if($('.class_ordernum:checked').length > 0 ){
				$("input[name=_mode]").val("mass_cancel");
				$("form[name=frm]")[0].submit();
			}
			else {
				alert('취소할 주문을 1건 이상 선택해주세요.');
			}
		}
	 }
	 // - 선택취소 ---
	// - 전체선택해제 ---
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_ordernum').attr('checked',true);
			}
			else {
				$('.class_ordernum').attr('checked',false);
			}
		});
	});
	// - 전체선택해제 ---
</SCRIPT>
<?php include dirname(__FILE__)."/wrap.footer.php"; ?>