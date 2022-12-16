<?PHP

	// LMH005

	// 메뉴 지정 변수
	$app_current_link = "_promotion.list.php";

	include_once("wrap.header.php");


	if($_mode == "modify") {
        $row = _MQ(" SELECT * FROM smart_promotion_code WHERE pr_uid='" . $pr_uid . "' ");
	}

?>
	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>프로모션 코드 설정</strong></div>

	<form name="frm" method="post" action="_promotion.pro.php" enctype="multipart/form-data" >
	<input type="hidden" name="_mode" value="<?php echo ($_mode ? $_mode : "add"); ?>"/>
	<input type="hidden" name="pr_uid" value="<?php echo $pr_uid; ?>"/>
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>"/>

		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>사용여부</td>
						<td>
							<?php echo _InputRadio("pr_use", array('N', "Y"), ($row['pr_use']?$row['pr_use']:"Y"), "", array('미사용', "사용") )?>
						</td>
					</tr>
					<tr>
						<th>프로모션코드</td>
						<td>
							<input type="text" name="pr_code" size="30" class="design" <?php echo $_mode=='modify'?'readonly':''?> value="<?php echo $row['pr_code']; ?>">
							<?php echo ($_mode=='modify'?_DescStr("한번 생성한 프로모션코드는 변경할 수 없습니다."):""); ?>
						</td>
					</tr>
					<tr>
						<th>프로모션코드명</td>
						<td>
							<input type="text" name="pr_name" size="30" class="design" value="<?php echo $row['pr_name']; ?>">
							<?php echo _DescStr("코드명은 관리자 참고용으로 사용자에게 노출되지 않습니다."); ?>
						</td>
					</tr>
					<tr>
						<th>할인금액</td>
						<td>
							<?php echo _InputRadio("pr_type", array('A', "P"), ($row['pr_type']?$row['pr_type']:"A"), "", array('할인금액(원)', "할인율(%)") )?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<input type="text" name="pr_amount" size="20" class="design number_style" style="width:85px" value="<?php echo ($row['pr_amount']?$row['pr_amount']:0); ?>"/>
							<span class="fr_tx">
								<span class="type_print type_P" style="display:none;">%</span>
								<span class="type_print type_A">원</span>
							</span>
							<div class="tip_box">
								<div class="type_print type_P" style="display:none;"><?php echo _DescStr("할인율(%)로 선택하면 장바구니에 담긴 상품총액 기준 할인율이 적용됩니다 (배송비제외)."); ?></div>
								<div class="type_print type_A"><?php echo _DescStr("할인금액(원)으로 선택하면 설정한 금액만큼 할인이 적용됩니다. 상품총액이 할인율보다 작을 경우 상품총액만큼 할인이 적용됩니다."); ?></div>
							</div>
							<script>
							var this_type = '';
							$(document).ready(function(){
								this_type = $('input[name=pr_type]:checked').val(); trigger_type();
								$('input[name=pr_type]').on('click',function(){ this_type = $('input[name=pr_type]:checked').val(); trigger_type(); });
							});
							function trigger_type(){
								$('.type_print').hide(); $('.type_print.type_'+this_type).show();
							}
							</script>
						</td>
					</tr>
					<tr>
						<th>만료일</td>
						<td>
							<input type="text" name="pr_expire_date" size="15" readonly class="design js_pic_day" value="<?php echo ($row['pr_expire_date'] ? $row['pr_expire_date'] : date("Y-m-d" , strtotime("+ 30 day"))); ?>" style="width:85px">
						</td>
					</tr>
					<? if($_mode=='modify') { ?>
						<tr>
							<th>생성일</td>
							<td>
								<?php echo date('Y-m-d H:i:s',strtotime($row['pr_rdate'])); ?>
							</td>
						</tr>
						<? if(rm_str($row[pr_edate])>0) { ?>
						<tr>
							<th>수정일</td>
							<td>
								<?php echo date('Y-m-d H:i:s',strtotime($row['pr_edate'])); ?>
							</td>
						</tr>
						<? } ?>
					<? } ?>
				</tbody> 
			</table>

		</div>

		<?php echo _submitBTN($app_current_link)?>

	</form>

	<script language="javascript">
		$(document).ready(function(){
			// -  validate --- 
			$("form[name=frm]").validate({
				ignore: "input[type=text]:hidden",
				rules: {
					pr_code: { required: true },
					pr_name: { required: false },
					pr_amount: { required: true },
					pr_expire_date: { required: true }
				},
				messages: {
					pr_code: { required: "프로모션 코드를 입력하시기 바랍니다." },
					pr_name: { required: "코드명을 입력하시기 바랍니다." },
					pr_amount: { required: "할인율 또는 할인금액을 입력하시기 바랍니다." },
					pr_expire_date: { required: "만료일을 선택하시기 바랍니다." }
				}
			});
			// - validate --- 
		});
	</SCRIPT>



<?PHP
	include_once("wrap.footer.php");
?>