<?php 
	// 메뉴 고정
	include_once('wrap.header.php');

	$resGroup = _MQ_assoc("select * from smart_member_group_set where 1 order by mgs_rank asc"); // -- 그룹정보를 가져온다.  
	$row = getPromotionEventDelivery(); // -- 설정정보 호출
	// -- 설정된 그룹고유번호를 배열화 
	if( $row['setGroupUid'] != ''){ $arrSetGroupUid = explode(",",$row['setGroupUid']); }
	else{ $arrSetGroupUid = array(); }

?>
<form id="frm" name="frm" method="post" ENCTYPE="multipart/form-data" action="_promotion_event_delivery.pro.php" target="common_frame" onsubmit="return pedSubmit();" >

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>무료배송이벤트</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
			<table class="table_form">	
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>사용상태</th>
						<td>
							<?php echo _InputRadio( 'use' , array('Y','N'), ($row['use'] ? $row['use'] : 'N') , '' , array('사용','중지') , ''); ?>
							<div class="tip_box">
								<?php echo _DescStr('무료배송이벤트의 경우 별도의 무료배송 쿠폰과 중복되지 않으며 우선 적용이 됩니다.'); ?>
								<?php echo _DescStr('무료배송이벤트 경우 주문/결제 시에 최종 적용됩니다.'); ?>
								<?php echo _DescStr('무료배송 이벤트의 경우 배송정책 및 개별배송과 상관없이 설정된 상품에 모두 적용되므로 사용시 주의를 하셔야합니다.(단, 추가 배송비는 적용되지 않습니다.)','black'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<th>이벤트 기간</th>
						<td>
							<span class="fr_tx">시작일 : </span>
							<input type="text" name="sdate" value="<?php echo $row['sdate']?>" class="design js_pic_day js_limit_date" style="width:85px" readonly>
							<span class="fr_tx">-</span>
							<span class="fr_tx">종료일 : </span>
							<input type="text" name="edate" value="<?php echo $row['edate']?>" class="design js_pic_day js_limit_date" style="width:85px" readonly>
							<?php echo _DescStr('이벤트 기간이 지날경우 사용상태에 상관없이 이벤트가 적용 되지 않습니다. '); ?>
						</td>
					</tr>
					
					<tr> 
						<th>최소결제금액</th>
						<td>
							<span class="fr_tx">이벤트 기간동안 주문 상품의 총 결제금액이 최소</span><input type="text" id="minPrice" name="minPrice" class="design number_style" style="width:100px;" value="<?php echo $row['minPrice'] ?>"><span class="fr_tx">원 이상 일 경우 이벤트 적용</span>				
							<div class="tip_box">
								<?php echo _DescStr('최소결제금액의 경우 주문 상품의 총 합계금액입니다.'); ?>
								<?php echo _DescStr('주의) 쿠폰 및 적립금 사용전 상품의 총 합게금액을 판별합니다.'); ?>
							</div>										
						</td>
					</tr>					

					<tr>
						<th>적용대상</th>
						<td>
							<?php echo _InputRadio( 'setMember' , array('all','group'), ($row['setMember'] ? $row['setMember'] : 'all') , ' class="on-set-member" ' , array('제한없음','회원만') , ''); ?>
								
							<div class="set-member-type-group" style="display:none;" data-etc="적용대상이 회원만 일경우">
								<div class="dash_line"><!-- 점선라인 --></div>
								<span class="fr_tx">회원등급 지정 : </span>
								<?php foreach($resGroup as $k=>$v){  ?>
									<label class="design"><input type="checkbox" class='set-group-uid' name="setGroupUid[]" value="<?php echo $v['mgs_uid']?>" <?php echo in_array($v['mgs_uid'],$arrSetGroupUid) == true ? 'checked':''?>><?=$v['mgs_name']?></label>
								<?php } ?>
							</div>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h46 red"><input type="submit" name="" value="확인"></span></li>
			</ul>
		</div>

</form>



	<script type="text/javascript">

		$(document).ready(pedSetMember);
		$(document).on('click','.on-set-member',pedSetMember); // -- 적용대상 선택 시

		// -- 적용회원 선택처리함수
		function pedSetMember()
		{
			var chkVal = $('.on-set-member:checked').val();
			if( chkVal == 'group'){
				$('.set-member-type-group').show();
			}else{
				$('.set-member-type-group').hide();
			}
		}

		// -- 서브밋 이벤트
		function pedSubmit()
		{
			var chkUse = $('[name="use"]:checked').val();
			if( chkUse == 'N'){ return true; }

			// - 입력폼 체크 
			var chkSdate = $('[name="sdate"]').val(); // 이벤트기간 :: 시작일
			var chkEdate = $('[name="edate"]').val();	// 이벤트기간 :: 종료일
			var chkSetMember = $('[name="setMember"]:checked').val();

			// -- 이벤트 기간체크
			if( (chkSdate.replace(/-/g,'')*1 ) >  (chkEdate.replace(/-/g,'')*1 ) ) { 
				$('[name="edate"]').focus();				
				alert('종료일은 시작일보다 작을 수 없습니다.'); return false; 
			} 

			// -- 적용대상이 회원만일경우 최소한개이상 체크 
			if( chkSetMember == 'group'){
				var chkSetMemberLen = $('.set-group-uid:checked').length;
				if( chkSetMemberLen < 1){ alert('적용할 회원등급을 선택해 주세요.'); return false;  }
			}
	

			return true;
		}


	</script>


<?php include_once('wrap.footer.php'); ?>
