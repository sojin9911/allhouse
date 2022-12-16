<?PHP
	$_state = $_state ? $_state : 'issue';
	$app_current_link = '_cashbill.list.php?_state='.$_state;
	include_once('wrap.header.php');

	include_once(OD_ADDONS_ROOT . '/barobill/include/var.php');


	if($_uid){
		$row = _MQ(" select * from smart_baro_cashbill where bc_uid = '${_uid}' ");
		if(!$row) error_msg("잘못된 접근입니다.");
	}

?>


	<div class="new_tab">
		<div class="tab_box">
			<ul>
				<li class="<?=($_state=="autofail" ? "hit" : "")?>">
					<a href="_cashbill.list.php?_state=autofail" class="tab">발행실패</a>
				</li>
				<li class="<?=($_state=="temp" ? "hit" : "")?>">
					<a href="_cashbill.list.php?_state=temp" class="tab">임시저장함</a>
				</li>
				<li class="<?=($_state=="issue" ? "hit" : "")?>">
					<a href="_cashbill.list.php?_state=issue" class="tab">발급보관함</a>
				</li>
				<li class="<?=($_state=="cancel" ? "hit" : "")?>">
					<a href="_cashbill.list.php?_state=cancel" class="tab">취소보관함</a>
				</li>
				<li class="<?=($_state=="fail" ? "hit" : "")?>">
					<a href="_cashbill.list.php?_state=fail" class="tab">전송실패보관함</a>
				</li>
			</ul>
		</div>
	</div>

<form id="frm" name="frm" method="post" ENCTYPE='multipart/form-data' action="_cashbill.pro.php" >
<input type="hidden" name="_mode" value='modify_memo'>
<input type="hidden" name="_uid" value='<?=$_uid?>'>
<input type="hidden" name="_state" value='<?=$_state?>'>
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">

	
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
				<?php if($row["bc_ordernum"]){ ?>
				<tr>
					<td class="article">연동된 주문번호</td>
					<td class="conts">
						<a href="_order.form.php?_mode=modify&_ordernum=<?php echo $row["bc_ordernum"];?>" alt="주문번호" target="infoOrder"><strong><?php echo $row["bc_ordernum"];?></strong></a>
					</td>
				</tr>	
				<?php } ?>
				<tr>
					<td class="article">거래용도</td>
					<td class="conts"><?=$arr_tradeUsage[$row["TradeUsage"]]?></td>
				</tr>
				<tr>
					<td class="article">신분확인번호 구분</td>
					<td class="conts"><?=$arr_TradeMethod[$row["TradeMethod"]]?><?=_DescStr("바로빌 시스템에 아직 적용이되지 않은 부분으로 휴대폰번호/주민등록번호등 모두 공통으로 사용합니다.")?></td>
				</tr>
				<tr>
					<td class="article">신분확인번호<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><?=$row["IdentityNum"]?></td>
				</tr>	
				<tr>
					<td class="article">공급가액<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><?=number_format($row["Amount"])?> 원</td>
				</tr>
				<tr>
					<td class="article">부가세<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><?=number_format($row["Tax"])?> 원</td>
				</tr>
				<tr>
					<td class="article">봉사료<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><?=number_format($row["ServiceCharge"])?> 원</td>
				</tr>
				<tr>
					<td class="article">판매금액</td>
					<td class="conts"><?=number_format($row["Amount"] + $row["Tax"] + $row["ServiceCharge"])?> 원</td>
				</tr>
				<tr>
					<td class="article">품목명</td>
					<td class="conts"><?=$row["ItemName"]?></td>
				</tr>
				<?if($row["NTSConfirmMessage"]){?>
				<tr>
					<td class="article">참고사항</td>
					<td class="conts" style="color:red"><?=$row["NTSConfirmMessage"]?></td>
				</tr>
				<?}?>
			</tbody>
		</table>
	</div>

	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">소비자이메일</td>
					<td class="conts">
						<?=($row["Email"] ? $row["Email"] : "미기재")?>
						<?=_DescStr("주소를 입력하지 않으면 이메일이 발송되지 않습니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">관리자메모</td>
					<td class="conts">
						<textarea name="Memo" class="input_text" style="width:98%;height:80px;"><?=stripslashes($row[Memo])?></textarea>
						<?=_DescStr("소비자에게는 보이지 않습니다.")?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="bottom_btn_area">
		<div class="btn_line_up_center">
			<span class="shop_btn_pack btn_input_blue"><input type="submit" name="" class="input_large" value="관리자메모저장" onclick="submit_btn('save')"></span>
			<span class="shop_btn_pack"><span class="blank_3"></span></span>
			<span class="shop_btn_pack btn_input_gray"><input type="button" name="" class="input_large" value="목록으로" onclick="location.href='_cashbill.list.php?<?=enc("d", $_PVSC)?>'"></span>
		</div>
	</div>


</form>



<?PHP
	include_once('wrap.footer.php');
?>