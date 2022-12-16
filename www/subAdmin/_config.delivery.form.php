<?php
include_once('wrap.header.php');

# 해당업체의 배송비 정책 확인
$r = _MQ(" select * from smart_company where cp_id = '{$com_id}' ");
?>

<form action="_config.delivery.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>배송비 정책 사용여부</th>
					<td colspan="3">
						<?php echo _InputRadio('_delivery_use', array('Y', 'N'), ($r['cp_delivery_use']?$r['cp_delivery_use']:'N'), ' class="delivery_use"', array('사용함', '사용안함'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('배송비 정책 사용여부를 미사용으로 설정하면 쇼핑몰 기본 배송정책이 적용됩니다.'); ?>
							<?php echo _DescStr('현재 쇼핑몰 배송정책은 <strong>기본배송비 : <u>'.number_format($siteInfo['s_delprice']).'</u>원, 무료배송비 : <u>'.number_format($siteInfo['s_delprice_free']).'</u>원</strong> 입니다.'); ?>
						</div>
					</td>
				</tr>
				<tr class="delivery_use_detail">
					<th>기본배송비</th>
					<td>
						<input type="text" name="_delivery_price" class="design number_style" value="<?php echo $r['cp_delivery_price']; ?>" placeholder="" style="width:80px">
						<span class="fr_tx">원</span>
						<?php echo _DescStr('무료배송일 경우 0원을 입력하세요.'); ?>
					</td>
					<th>무료배송비</th>
					<td>
						<input type="text" name="_delivery_freeprice" class="design number_style" value="<?php echo $r['cp_delivery_freeprice']; ?>" placeholder="" style="width:80px">
						<span class="fr_tx">원</span>
						<?php echo _DescStr('무조건 배송비 적용시 0을 입력하세요.'); ?>
					</td>
				</tr>
				<?php
					// 추가배송비 설정 추가 2017-05-19 :: SSJ {
					// 최초 등록시 운영업체 설정 불러옴
					$r['cp_del_addprice_use'] = ($r['cp_del_addprice_use']?$r['cp_del_addprice_use']:$siteInfo['s_del_addprice_use']);
					$r['cp_del_addprice_use_normal'] = ($r['cp_del_addprice_use_normal']?$r['cp_del_addprice_use_normal']:$siteInfo['s_del_addprice_use_normal']);
					$r['cp_del_addprice_use_unit'] = ($r['cp_del_addprice_use_unit']?$r['cp_del_addprice_use_unit']:$siteInfo['s_del_addprice_use_unit']);
					$r['cp_del_addprice_use_free'] = ($r['cp_del_addprice_use_free']?$r['cp_del_addprice_use_free']:$siteInfo['s_del_addprice_use_free']);
				?>
				<tr class="delivery_use_detail">
					<th>추가배송비 설정</th>
					<td colspan="3">
						<?php echo _InputRadio('_del_addprice_use', array('Y', 'N'), ($r['cp_del_addprice_use']?$r['cp_del_addprice_use']:'N'), ' class="del_addprice_use"', array('사용함','사용안함'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('<em>사용함</em>으로 설정 시 각 도서산간 추가배송비 설정에따라 추가배송비가 적용됩니다.'); ?>
							<?php echo _DescStr('<em>사용안함</em>으로 설정 시 각 도서산간 추가배송비 설정에 관계없이 추가배송비가 적용되지 않습니다.'); ?>
						</div>
						<div class="dash_line del_addprice_detail"<?php echo ($r['cp_del_addprice_use'] == 'N'?' style="display:none;"':null); ?>></div>
						<table class="table_form del_addprice_detail"<?php echo ($r['cp_del_addprice_use'] == 'N'?' style="display:none;"':null); ?>>
							<tbody>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">일반배송 상품에 추가배송비를 적용합니다. (필수적용)</span>
										<div class="clear_both">
											<span class="fr_bullet fr_tx normal">일반배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비를</span>
											<?php echo _InputRadio('_del_addprice_use_normal', array('Y', 'N'), $r['cp_del_addprice_use_normal'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">개별배송 상품에 추가배송비를</span>
										<?php echo _InputRadio('_del_addprice_use_unit', array('Y', 'N'), $r['cp_del_addprice_use_unit'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">무료배송 상품에 추가배송비를 </span>
										<?php echo _InputRadio('_del_addprice_use_free', array('Y', 'N'), $r['cp_del_addprice_use_free'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>

				<?// SSJ ::: 부가세율설정 누락 수정 ::: 2021-11-11 ?>
				<?if($siteInfo['s_vat_delivery'] == 'C'){?>
				<tr>
					<th class="ess">배송비 부가세 적용 여부</th>
					<td colspan="3">
						<?=_InputRadio( "_vat_delivery" , array("Y","N"), $r['cp_vat_delivery'] ? $r['cp_vat_delivery'] : "Y" , "" , array("과세","면세") , "") ?>
						<?=_DescStr("입점업체에 부과되는 배송비에 부가세 적용 여부를 설정합니다.")?>
					</td>
				</tr>
				<?}?>
				<?// SSJ ::: 부가세율설정 누락 수정 ::: 2021-11-11 ?>

				<tr class="delivery_use_detail">
					<th>지정택배사</th>
					<td>
						<?php echo _InputSelect('_delivery_company', array_keys($arr_delivery_company), $r['cp_delivery_company'], '', '', ''); ?>
					</td>
					<th>평균 배송기간</th>
					<td>
						<input type="text" name="_delivery_date" class="design" value="<?php echo $r['cp_delivery_date'];?>" placeholder="" style="width:100px">
					</td>
				</tr>
				<tr class="delivery_use_detail">
					<th>반송주소</th>
					<td colspan="3">
						<input type="text" name="_delivery_return_addr" class="design" value="<?php echo $r['cp_delivery_return_addr']; ?>" placeholder="" style="width:250px">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>

<script type="text/javascript">
	// 배송비 정책 사용여부
	$(document).ready(del_addprice_use);
	$(document).on('click', '.delivery_use', delivery_use);
	function delivery_use() {
		var _val = $('.delivery_use:checked').val();
		if(_val == 'Y') {
			$('.delivery_use_detail').show();
			del_addprice_use();
		}
		else {
			$('.delivery_use_detail').hide();
			$('.del_addprice_detail').hide();
		}
	}

	// 추가배송비 사용여부에따른 노출 설정
	$(document).ready(del_addprice_use);
	$(document).on('click', '.del_addprice_use', del_addprice_use);
	function del_addprice_use() {
		var _val = $('.del_addprice_use:checked').val();
		if(_val == 'Y') $('.del_addprice_detail').show();
		else $('.del_addprice_detail').hide();
	}
</script>
<?php include_once('wrap.footer.php'); ?>