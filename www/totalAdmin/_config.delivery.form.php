<?php include_once('wrap.header.php'); ?>


<form name="frm" method="post" action="_config.delivery.pro.php" ENCTYPE="multipart/form-data">

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>배송 기본정보 설정</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>기본 배송비</th>
					<td>
						<input type="text" name="_delprice" class="design number_style" value="<?php echo $siteInfo['s_delprice']; ?>" placeholder="" style="width:80px">
						<span class="fr_tx">원</span>
						<?php echo _DescStr('무료배송일 경우 0원을 입력하세요.'); ?>
					</td>
					<th>무료 배송비</th>
					<td>
						<input type="text" name="_delprice_free" class="design number_style" value="<?php echo $siteInfo['s_delprice_free']; ?>" placeholder="" style="width:80px">
						<span class="fr_tx">원</span>
						<?php echo _DescStr('무조건 배송비 적용시 0을 입력하세요.'); ?>
					</td>
				</tr>
				<tr>
					<th>추가배송비 설정</th>
					<td colspan="3">
						<?php echo _InputRadio('_del_addprice_use', array('Y', 'N'), $siteInfo['s_del_addprice_use']? $siteInfo['s_del_addprice_use']:'N', ' class="del_addprice_use"', array('사용함','사용안함'), ''); ?>
						<?php if($SubAdminMode === true){ // 입점업체사용시 안내문구 추가 ?>
							<div class="tip_box">
								<?php echo _DescStr('<em>사용함</em>으로 설정 시 각 입점업체의 도서산간 추가배송비 설정에따라 추가배송비가 적용됩니다.'); ?>
								<?php echo _DescStr('<em>사용안함</em>으로 설정 시 각 입점업체의 도서산간 추가배송비 설정에 관계없이 추가배송비가 적용되지 않습니다.'); ?>
							</div>
						<?php } ?>

						<div class="dash_line del_addprice_detail" style="<?php echo ($siteInfo['s_del_addprice_use']=='N'?'display:none;':null); ?>"></div>
						<table class="table_form del_addprice_detail" style="<?php echo ($siteInfo['s_del_addprice_use']=='N'?'display:none;':null); ?>">
							<tbody>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">일반배송 상품에 추가배송비를 적용합니다. (필수적용)</span>
										<div class="clear_both">
											<span class="fr_bullet fr_tx normal">일반배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비를</span>
											<?php echo _InputRadio('_del_addprice_use_normal', array('Y', 'N'), $siteInfo['s_del_addprice_use_normal'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
										</div>
									</td>
								</tr>

								<?php
									// ----- JJC : 상품별 배송비 : 2018-08-16 -----
								?>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">상품별배송 상품에 추가배송비를 적용합니다. (필수적용)</span>
										<div class="clear_both">
											<span class="fr_bullet fr_tx normal">상품별배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비를</span>
											<?php echo _InputRadio('_del_addprice_use_product', array('Y', 'N'), $siteInfo['s_del_addprice_use_product'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
										</div>
									</td>
								</tr>
								<?php
									// ----- JJC : 상품별 배송비 : 2018-08-16 -----
								?>

								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">개별배송 상품에 추가배송비를</span>
										<?php echo _InputRadio('_del_addprice_use_unit', array('Y', 'N'), $siteInfo['s_del_addprice_use_unit'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">무료배송 상품에 추가배송비를 </span>
										<?php echo _InputRadio('_del_addprice_use_free', array('Y', 'N'), $siteInfo['s_del_addprice_use_free'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
									</td>
								</tr>
							</tbody>
						</table>

					</td>
				</tr>
				<tr>
					<th>지정택배사</th>
					<td>
						<?php echo _InputSelect( '_del_company' , array_keys($arr_delivery_company), $siteInfo['s_del_company'] , '' , '' , ''); ?>
					</td>
					<th>평균 배송기간</th>
					<td>
						<input type="text" name="_del_date" class="design" value="<?php echo $siteInfo['s_del_date'];?>" placeholder="" style="width:100px">
					</td>
				</tr>
				<tr>
					<!-- <th>반품/교환 배송비</th>
					<td>
						<input type="text" name="_del_complain_price" class="design" value="<?php echo $siteInfo['s_del_complain_price']; ?>" placeholder="" style="width:250px">
					</td> -->
					<th>반송주소</th>
					<td colspan="3">
						<input type="text" name="_del_return_addr" class="design" value="<?php echo $siteInfo['s_del_return_addr']; ?>" placeholder="" style="width:250px">
					</td>
				</tr>
				<!-- <tr>
					<th>교환/반품/환불이 가능한 경우</th>
					<td colspan="3">
						<textarea name="_complain_ok" rows="8" cols="" class="design"><?php echo $siteInfo['s_complain_ok']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>교환/반품/환불이 불가능한 경우</th>
					<td colspan="3">
						<textarea name="_complain_fail" rows="8" cols="" class="design"><?php echo $siteInfo['s_complain_fail']; ?></textarea>
					</td>
				</tr> -->
				<tr>
					<td colspan="4">
						<?php echo _DescStr('상품등록시 해당상품의 배송정책을 따로 입력하거나 '.($SubAdminMode===true?'입점업체가 ':null).'직접 설정할 수 있습니다.' , 'black'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품 공통 등록 설정</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>기본 설정 수수료</th>
					<td>
						<input type="text" name="_account_commission" class="design number_style" value="<?php echo $siteInfo['s_account_commission']; ?>" placeholder="" style="width:50px">
						<span class="fr_tx">%</span>
						<div class="tip_box">
							<?php echo _DescStr('설정한 수수료는 '. ($SubAdminMode===true?'입점업체가 ':null) .'상품등록 시 자동으로 적용되는 수수료율을 지정합니다.'); ?>
							<?php echo _DescStr(($SubAdminMode===true?'입점업체가 ':null) .'상품등록을 하면 업체 정산형태는 수수료를 자동선택합니다.'); ?>
						</div>
					</td>
				</tr>
                <!-- SSJ : 자동 배송완료 패치 : 2021-02-01 -->
				<tr>
					<th>자동 배송완료 처리 설정</th>
					<td>
						<input type="text" name="_delivery_auto" class="design number_style" value="<?php echo $siteInfo['s_delivery_auto']; ?>" placeholder="" style="width:50px">
						<span class="fr_tx">일</span>
						<div class="tip_box">
							<?php echo _DescStr('배송상태가 <em>배송중</em>인 주문은 배송중으로 변경된 날로 부터 설정된 기간이 지나면 자동으로 배송완료 상태로 변경됩니다.'); ?>
							<?php echo _DescStr('기간을 <em>0</em>으로 설정할 경우 자동 배송완료 처리 설정은 적용되지 않습니다.'); ?>
						</div>
					</td>
				</tr>
                <!-- // SSJ : 자동 배송완료 패치 : 2021-02-01 -->
			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>자동정산대기 처리 설정</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용 여부</th>
					<td>
						<?php echo _InputRadio('_product_auto_on', array('Y', 'N'), $siteInfo['s_product_auto_on'], '', array('사용함', '사용안함')); ?>
						<?php echo _DescStr('<strong>주의</strong>: <em>사용안함</em>에서 <em>사용함</em>으로 변경 시 모든 데이터가 자동처리 됩니다.' , 'black'); ?>
					</td>
				</tr>
				<tr>
					<th>정산시기 설정</th>
					<td>

						<?php echo _DescStr('배송완료 후 설정된 기간이 지나면 자동으로 정산대기로 넘어갑니다.'); ?>
						<div class="dash_line"></div>
						<table class="table_form" style="width:300px">
							<colgroup>
								<col width="150"><col width="*">
							</colgroup>
							<tbody>
								<?php foreach($arr_paymethod_name as $k=>$v) { ?>
								<tr>
									<th><?php echo $v; ?></th>
									<td>
										<input type="text" name="_product_auto_<?php echo $k; ?>" class="design number_style" value="<?php echo $siteInfo['s_product_auto_'.$k]; ?>" placeholder="" style="width:50px"><span class="fr_tx">일</span>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>

					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">
	// 추가배송비 사용여부에따른 노출 설정
	$(document).on('click', '.del_addprice_use', function() {
		var Value = $(this).val();
		if(Value == 'Y') $('.del_addprice_detail').show();
		else $('.del_addprice_detail').hide();
	});
</script>

<?php include_once('wrap.footer.php'); ?>