<?php
		$app_current_link = '_entershop.list.php';
		include_once('wrap.header.php');

		if( $SubAdminMode !== true){  error_msg("이용할 수 없는 메뉴입니다."); }

		if( in_array($_mode,array('modify','add')) == false){
			error_loc_msg("_entershop.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다.");
		}

		// -- 모드별 처리
		if( $_mode == 'modify'){ // 수정일 시
			$row = _MQ("select *from smart_company where cp_id = '".$_id."'  ");
			if( count($row) < 1){ error_loc_msg("_entershop.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "운영자 정보가 없습니다." ); }


		}else{ // 추가일시

		}

		// 추가배송비 설정 추가 2017-05-19 :: SSJ {
		// 최초 등록시 운영업체 설정 불러옴
		$row['cp_del_addprice_use'] = $row['cp_del_addprice_use'] ? $row['cp_del_addprice_use'] : $siteInfo['s_del_addprice_use'];
		$row['cp_del_addprice_use_normal'] = $row['cp_del_addprice_use_normal'] ? $row['cp_del_addprice_use_normal'] : $siteInfo['s_del_addprice_use_normal'];
		$row['cp_del_addprice_use_unit'] = $row['cp_del_addprice_use_unit'] ? $row['cp_del_addprice_use_unit'] : $siteInfo['s_del_addprice_use_unit'];
		$row['cp_del_addprice_use_free'] = $row['cp_del_addprice_use_free'] ? $row['cp_del_addprice_use_free'] : $siteInfo['s_del_addprice_use_free'];
		// 추가배송비 설정 추가 2017-05-19 :: SSJ }


?>

<form action="_entershop.pro.php" name="frm" id="frm"  method="post" onsubmit="return entershopSubmit();">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="_mode" value="<?=$_mode?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="tempID" value="<?=$row['cp_id']?>"> <?php // -- ajax 모드 ?>
	<?php if($_mode == 'modify') { ?>
	<input type="hidden" name="_id" value="<?=$row['cp_id']?>">
	<?php } ?>

	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>업체 기본정보</strong><!-- 메뉴얼로 링크 --> </div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>

				<tr>
					<th class="ess">아이디</th>
					<td>
						<?php if($_mode == 'add'){ ?>
						<input type="text" name="_id" class="design" style="" value="" />
						<?php }else{echo $row['cp_id']; }?>

					</td>
					<th class="ess">업체명</th>
					<td>
						<input type="text" name="_name" class="design" style="" value="<?php echo $row['cp_name'] ?>">
					</td>
				</tr>

				<tr>
					<th <?php $_mode == 'add' ? 'class="ess"':''  ?>>비밀번호</th>
					<td>
						<input type="password" name="_pw" class="design" style="width:130px;" value="" />
						<?php echo _DescStr('6자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.', 'black'); ?>
					</td>
					<th <?php $_mode == 'add' ? 'class="ess"':''  ?>>비밀번호 확인</th>
					<td>
						<input type="password" name="_repw" class="design" style="width:130px;" value="">
						<?php echo _DescStr('입력하신 비밀번호를 다시한번 입력해 주세요.', 'black'); ?>
					</td>
				</tr>

				<tr>
					<th>대표자</th>
					<td>
						<input type="text" name="_ceoname" class="design" value="<?=$row['cp_ceoname']?>" />
					</td>
					<th>사업자번호</th>
					<td>
						<input type="text" name="_number" class="design" value="<?=$row['cp_number']?>" />
					</td>
				</tr>

				<tr>
					<th>업태</th>
					<td>
						<input type="text" name="_item1" class="design" value="<?=$row['cp_item1']?>" />
					</td>
					<th>업종</th>
					<td>
						<input type="text" name="_item2" class="design" value="<?=$row['cp_item2']?>" />
					</td>
				</tr>

				<tr>
					<th class="ess">전화</th>
					<td>
						<input type="text" name="_tel" class="design" value="<?=$row['cp_tel']?>" />
					</td>
					<th>팩스</th>
					<td>
						<input type="text" name="_fax" class="design" value="<?=$row['cp_fax']?>" />
					</td>
				</tr>

				<tr>
					<th>홈페이지</th>
					<td>
						<input type="text" name="_homepage" class="design" value="<?=$row['cp_homepage']?>" />
					</td>
					<th class="ess">주소</th>
					<td>
						<input type="text" name="_address" class="design" value="<?=$row['cp_address']?>" style="width:320px;" />
					</td>
				</tr>

				<?php // JJC : 2019-05-15 : 판매자 정보 ?>
				<tr>
					<th>통신판매업번호</th>
					<td colspan="3">
						<input type="text" name="_snumber" class="design" value="<?=$row['cp_snumber']?>" />
					</td>
				</tr>


			</tbody>
		</table>
	</div>


	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>담당자 정보</strong></div>
	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>담당자</th>
					<td>
						<input type="text" name="_charge" class="design" value="<?=$row['cp_charge']?>" />
					</td>
					<th>휴대폰</th>
					<td>
						<input type="text" name="_tel2" class="design" value="<?=$row['cp_tel2']?>" />
					</td>
				</tr>
				<tr>
					<th class="ess">이메일</th>
					<td colspan="3">
						<input type="text" name="_email" class="design" value="<?=$row['cp_email']?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>배송정보 설정</strong></div>
	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">배송비 정책 사용여부</th>
					<td colspan="3">
						<?php echo _InputRadio( '_delivery_use' ,  array("Y","N"),  $row[cp_delivery_use] ? $row[cp_delivery_use] : "N" , '' , array('사용', '미사용') , '');?>
						<div class="tip_box delivery_area_N" style="display: none;">
							<?php echo _DescStr("현재 쇼핑몰 배송정책은 기본배송비 : <em>".number_format($siteInfo[s_delprice])."원</em>, 무료배송비 : <em>".number_format($siteInfo[s_delprice_free])."원</em> 입니다.","black"); ?>
						</div>
					</td>
				</tr>
				<tr class="delivery_area_Y">
					<th>기본배송비</th>
					<td>
						<input type="text" name="_delivery_price" class="design number_style" value="<?=$row['cp_delivery_price']?>" />
						<span class="fr_tx">원</span>
						<?php echo _DescStr('무료배송일 경우 0원을 입력하세요.'); ?>
					</td>
					<th>무료배송비</th>
					<td>
						<input type="text" name="_delivery_freeprice" class="design number_style" value="<?=$row['cp_delivery_freeprice']?>" />
						<span class="fr_tx">원</span>
						<?php echo _DescStr('무조건 배송비 적용시 0을 입력하세요.'); ?>
					</td>
				</tr>

				<tr class="delivery_area_Y">
					<th>추가배송비 설정</th>
					<td colspan="3">
						<?php echo _InputRadio('_del_addprice_use', array('Y', 'N'), $row['cp_del_addprice_use']? $row['cp_del_addprice_use']:'N', ' class="del_addprice_use"', array('사용함','사용안함'), ''); ?>
						<?php if($SubAdminMode === true){ // 입점업체사용시 안내문구 추가 ?>
							<div class="tip_box">
								<?php if($siteInfo['s_del_addprice_use']<>"Y"){ ?>
									<?=_DescStr("운영업체의 추가배송비 설정이 <em>사용안함</em> 으로 설정되었습니다. 해당 설정은 적용되지 않습니다.", "black")?>
								<?php } ?>
								<?=_DescStr("'사용함' 설정시 도서산간 추가배송비 설정에따라 추가배송비가 적용됩니다.")?>
							</div>
						<?php } ?>

						<div class="dash_line del_addprice_detail"></div>
						<table class="table_form del_addprice_detail">
							<tbody>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">일반배송 상품에 추가배송비를 적용합니다. (필수적용)</span>
										<div class="clear_both">
											<span class="fr_bullet fr_tx normal">일반배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비를</span>
											<?php echo _InputRadio('_del_addprice_use_normal', array('Y', 'N'), $row['cp_del_addprice_use_normal'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">개별배송 상품에 추가배송비를</span>
										<?php echo _InputRadio('_del_addprice_use_unit', array('Y', 'N'), $row['cp_del_addprice_use_unit'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fr_bullet fr_tx normal">무료배송 상품에 추가배송비를 </span>
										<?php echo _InputRadio('_del_addprice_use_free', array('Y', 'N'), $row['cp_del_addprice_use_free'], '', array('적용합니다.', '적용하지 않습니다.')); ?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>

				<?// 2017-06-16 ::: 부가세율설정 ::: JJC ?>
				<?if($siteInfo['s_vat_delivery'] == 'C'){?>
				<tr>
					<th class="ess">배송비 부가세 적용 여부</th>
					<td colspan="3">
						<?=_InputRadio( "_vat_delivery" , array("Y","N"), $row[cp_vat_delivery] ? $row[cp_vat_delivery] : "Y" , "" , array("과세","면세") , "") ?>
						<?=_DescStr("입점업체에 부과되는 배송비에 부가세 적용 여부를 설정합니다.")?>
					</td>
				</tr>
				<?}?>
				<?// 2017-06-16 ::: 부가세율설정 ::: JJC ?>

				<tr class="delivery_area_Y">
					<th>지정택배사</th>
					<td>
						<?php echo _InputSelect( '_delivery_company' , array_keys($arr_delivery_company), $row['cp_delivery_company'] , '' , '' , ''); ?>
					</td>
					<th>평균배송기간</th>
					<td>
						<input type="text" name="_delivery_date" class="design" value="<?=$row['cp_delivery_date']?>" />
					</td>
				</tr>

				<tr class="delivery_area_Y">
					<th>반송주소</th>
					<td colspan="3">
						<input type="text" name="_delivery_return_addr" class="design" value="<?=$row['cp_delivery_return_addr']?>" style="width:250px;" />
					</td>
				</tr>

			</tbody>
		</table>
	</div>



	<?php echo _submitBTN('_entershop.list.php'); ?>

</form>



	<div class="ajax-data-box" data-visit-ahref=""></div>
	<script>
	$(document).ready(function(){
		delivery_use_check(); // 배송비 정책 사용/미사용에 따른처리
		delivery_addprice_use_check(); // -- 추가배송비 사용/미사용에 따른처리
	})

	// 배송비 정책 사용/미사용에 따른처리
	$(document).on('click',"input[name=_delivery_use]",function(){
		delivery_use_check();
	 });

	// 추가배송비 사용여부에따른 노출 설정
	$(document).on('click', '.del_addprice_use', function() {
		delivery_addprice_use_check();
	});

	// -- 배송비 정책 사용/미사용에 따른처리
	var delivery_use_check = function() {
		if($("input[name=_delivery_use]:checked").val() == "Y") {
			$(".delivery_area_Y").show();
			$(".delivery_area_N").hide();
		} else {
			$(".delivery_area_Y").hide();
			$(".delivery_area_N").show();
		}
	}

	// -- 추가배송비 사용/미사용에 따른처리
	var delivery_addprice_use_check = function() {
		var Value = $('.del_addprice_use:checked').val();
		if(Value == 'Y') $('.del_addprice_detail').show();
		else $('.del_addprice_detail').hide();
	}




	/*
		- 서브밋 이벤트
	*/
	function entershopSubmit()
	{

		var data = {ajaxMode : 'inputChk'}
		var sa = $('#frm').serializeArray();
		$.each(sa,function(key,val){
			eval('data.'+val.name+'="'+val.value+'"');
		});

		// console.log(data);
		// return false;
		try{
			var result = $.parseJSON($.ajax({
				url: "_entershop.ajax.php",
				type: "post", // 데이터 검증은 post 로 보내야한다.
				dataType : "json",
				data: data,
				async: false
			}).responseText);
		}catch(err){
			console.log(err);
		}

		if(result == undefined){ return false; }
		if(result.rst == 'fail'){
			$('[name="'+result.key+'"]').focus();
			alert(result.msg);
			return false;
		}

    	return true;
	}
	</script>


<?php

		// 주소찾기 - 우편번호찾기 박스
	include_once OD_ADDONS_ROOT."/newpost/newpost.search.php";
	include_once('wrap.footer.php');
?>