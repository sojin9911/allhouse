<?PHP

	// 팝업형태 적용
	$app_mode = "popup";

	include_once("inc.header.php");

	if( $_uid ) {
		$que = "  select * from smart_delivery_addprice where da_uid='". $_uid ."' ";
		$r = _MQ($que);
		//ViewArr($r);
	}else{
		error_msgPopup_s("잘못된 접근입니다.");
	}
?>

<div class="popup">

	<div class="pop_title"><strong>추가배송비 수정</strong></div>

	<form name="frm" method="post" action="_config.delivery_addprice.pro.php" enctype="multipart/form-data" autocomplete="off" onsubmit="return frm_submit(this);" style="margin:0;padding:0;">
	<input type="hidden" name="_mode" value='modify'>
	<input type="hidden" name="_uid" value='<?=$_uid?>'>
		<!-- ● 데이터 리스트 -->
		<div class="data_list">

			<table class="table_form">
				<colgroup>
					<col width="140"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>상세주소</th>
						<td><input type="text" name="addr" id="_addr1" class="design" placeholder="" value="<?php echo trim(str_replace(trim($r['da_sido']), '', trim($r['da_addr']))); ?>" style="width:450px"></td>
					</tr>
					<tr>
						<th>추가배송비</th>
						<td><input type="text" name="addprice" class="design number_style" placeholder="" value="<?php echo number_format($r['da_price']); ?>" style="width:80px"><span class="fr_tx">원</span></td>
					</tr>
				</tbody> 
			</table>
			
			<div class="tip_box">
				<?=_DescStr("상세주소는 추가배송비가 적용될 행정구역단위까지 입력해주세요. 배송주소와 지역명이 일치해야 추가배송비가 적용됩니다.")?>
				<?=_DescStr("추가배송비가 0원이면 도서산간지역은 추가되지만 추가배송비가 적용되지는 않습니다.")?>
				<?=_DescStr("오타 및 띄어쓰기를 잘못하였을경우 정상적으로 적용되지 않습니다. 상세주소 수정시 주의해주시기 바랍니다.", "orange")?>
			</div>

		</div>

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" name="" value="저장" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black">확인</a> --></li>
				<li><a href="#none" id="close" class="c_btn h34 black line normal" accesskey="x">닫기</a></li>
			</ul>
		</div>
	</form>
	
</div>


<script type="text/javascript">
	// 닫기버튼
	$('#close').on('click',function(){ self.opener = self; window.close(); });
</script>


<?php include_once("inc.footer.php"); ?>