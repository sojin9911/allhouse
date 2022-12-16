<?php
$app_current_page_name = ( $_REQUEST["style"] == "b" ? "무통장주문대기관리" : "주문관리" ) ;
include dirname(__FILE__)."/wrap.header.php";


// - 수정 ---
if( $_mode == "modify" ) {
	$que = " select * from smart_order where o_ordernum='{$_ordernum}' ";
	$row = _MQ($que);

	$_member = _MQ(" select * from smart_individual where in_id = '".$row[o_mid]."' ");
}
else{ error_msg('잘못된 접근입니다.'); }
// - 수정 ---


$orderstepArray = array(
	"카드결제" => "<span class='red'>카드결제</span>",
	"가상계좌" => "<span class='orange'>가상계좌</span>",
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





<div class="post_hide_section">

	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container ">

		<!-- ●●●●● 데이터폼 -->
		<div class="data_form">


			<!-- 주문상품정보박스 -->
			<div class="cart_item_list if_nocart if_orderview">
				<!-- 단락타이틀 필요한 경우 -->
				<div class="group_title">주문상품정보</div>

				<ul>

<?PHP

	$arr_product = array();
	$sres = _MQ_assoc("
		select op.* , p.p_name, p.p_img_list, p.p_cuid,p.p_code
		from smart_order_product as op
		inner join smart_product as p on (p.p_code=op.op_pcode)
		where op_oordernum='{$_ordernum}'
	");

	// 현금영수증용 상품명 생성
	$cash_product_name = (count($sres)>0)?$sres[0][p_name].'외 '.(count($sres)-1).'개':$sres[0][p_name];

	foreach( $sres as $k=>$v ){

		// -- 이미지 ---
		$img_src = get_img_src($v[p_img_list]);

		// -- 추가옵션 ---
		$add_option = "";
		$add_option_name = _MQ_assoc("select pao_poptionname from smart_product_addoption where pao_pcode='{$v[op_pcode]}' and pao_depth='1'");
		if($v[op_add_option1]) { $add_option .= '['.$add_option_name[0][pao_poptionname].':'.$v[op_add_option1].']&nbsp;'; }
		if($v[op_add_option2]) { $add_option .= '['.$add_option_name[1][pao_poptionname].':'.$v[op_add_option2].']&nbsp;'; }
		if($v[op_add_option3]) { $add_option .= '['.$add_option_name[2][pao_poptionname].':'.$v[op_add_option3].']&nbsp;'; }
		if($v[op_add_option4]) { $add_option .= '['.$add_option_name[3][pao_poptionname].':'.$v[op_add_option4].']&nbsp;'; }
		if($v[op_add_option5]) { $add_option .= '['.$add_option_name[4][pao_poptionname].':'.$v[op_add_option5].']&nbsp;'; }
		if($v[op_add_option6]) { $add_option .= '['.$add_option_name[5][pao_poptionname].':'.$v[op_add_option6].']&nbsp;'; }
		if($v[op_add_option7]) { $add_option .= '['.$add_option_name[6][pao_poptionname].':'.$v[op_add_option7].']&nbsp;'; }
		if($v[op_add_option8]) { $add_option .= '['.$add_option_name[7][pao_poptionname].':'.$v[op_add_option8].']&nbsp;'; }
		if($v[op_add_option9]) { $add_option .= '['.$add_option_name[8][pao_poptionname].':'.$v[op_add_option9].']&nbsp;'; }
		if($v[op_add_option10]) { $add_option .= '['.$add_option_name[9][pao_poptionname].':'.$v[op_add_option10].']&nbsp;'; }
		// -- 추가옵션 ---

		// -- 배송상품정보 ::: 택배, 송장, 발송일 표기 ---
		$coupon_html = "";
		if( in_array($v[op_sendstatus] , array("배송중" , "배송완료"))  ) {
			$coupon_html ="
				<dd class='thisis_coupon'>
					<div class='thisis_txt'>택배사 : ". $v[op_sendcompany] ."</div>
					<div class='thisis_txt'>송장번호 : ". $v[op_sendnum] ."</div>
					<div class='thisis_txt'>발송일 : ". $v[op_senddate] ."</div>
				</dd>
			";
		}
		// -- 배송상품정보 ---

		// -- 발송여부 --- LMH001
		$app_status = "<span class='texticon_pack checkicon'>";
		if($v[op_cancel]=='Y') { $app_status .= "<span class='dark'>주문취소</span>"; }
		else if($v[op_cancel]=='R') { $app_status .= "<span class='purple'>취소요청중</span>"; }
		else {$app_status .= $paystatusArray[$v[op_sendstatus]];}
		$app_status .= "</span>";
		// -- 발송여부 ---


		// 부분취소 버튼 LMH001
		unset($tmp_delivery_print , $delivery_print , $_cancel_btn , $status_print , $_individual_cancel);
		if($row[o_paystatus]=="Y") {
			switch($v[op_cancel]) {
				case "Y": // 취소완료
					$_cancel_btn = "<dd><span class='button_pack'><a href='#none' onclick='return false;'  class='product_cancel btn_md_red' >취소완료</a></span></dd>";
				break;
				case "R": // 취소요청중
					$_cancel_btn = "<dd><span class='button_pack'><a href='#none' onclick='return false;'  class='product_cancel btn_md_white' >취소요청중</a></span></dd>";
				break;
				case "N":
					// 부분취소 버튼
					$_cancel_btn = "<dd><span class='button_pack'><a href='#none' onclick='return false;'  class='product_cancel btn_md_black' data-ordernum='".$v[op_oordernum]."' data-opuid='".$v[op_uid]."'>부분취소</a></span></dd>";

					// 부분취소 신청 form // <!-- ★★★★★★★★★★★ 부분취소 클릭하면 열림 (2015-11-16) -->
					$_individual_cancel = "
						<div class='part_cancel' ID='opuid_form_".$v[op_uid]."'>
<form name='product_cancel_".$v[op_uid]."'>
<input type='hidden' name='mode' value='cancel'/>
<input type='hidden' name='ordernum' value='".$v[op_oordernum]."'/>
<input type='hidden' name='op_uid' value='".$v[op_uid]."'/>
<input type='hidden' name='paymethod' value='".$row[o_paymethod]."'/>
							<div class='data_form'>

								<!-- 테이블형 div colspan 하고 싶으면 ul에 if_full -->
								<div class='like_table'>
									<ul class=''>
										<li class='opt ess'>환불수단</li>
										<li class='value'>
											<label><input type='radio' name='cancel_type' value='pg' checked/>직접 환불</label>
											<label><input type='radio' name='cancel_type' value='point' />적립금 환불</label>
										</li>
									</ul>
									".(
										in_array($row['o_paymethod'],array('online','virtual')) ?
										"
											<ul class='view_pg'>
												<li class='opt ess'>환불계좌</li>
												<li class='value'>
													<div class='select'>
														<span class='shape'></span>
														". _InputSelect( "cancel_bank" , array_keys($ksnet_bank) , $_member[in_cancel_bank] , "" , array_values($ksnet_bank) , "은행선택") ."
													</div>
													<input type='tel' name='cancel_bank_account' class='input_design' value='".$_member[in_cancel_bank_account]."' placeholder='계좌번호'/>
													<input type='text' name='cancel_bank_name' class='input_design' value='".$_member[in_cancel_bank_name]."' placeholder='예금주'/>
												</li>
											</ul>
										" :
										""
									)."
									<ul class=''>
										<li class='opt '>전달내용</li>
										<li class='value'>
											<textarea cols='' rows='' class='textarea_design' name='cancel_msg' placeholder='관리자에게 전달하실 내용이 있다면 입력해주세요.' ></textarea>
										</li>
									</ul>
								</div>

							</div>
							<!-- / 데이터폼 -->

							<!-- 컨트롤 버튼들 -->
							<div class='order_view_btn'>
								<dl>
									<dd><span class='button_pack'><a href='#none' onclick='return false;' class='btn_md_blue product_cancel_submit' data-opuid='".$v[op_uid]."'>부분취소 신청하기</a></span></dd>
									<dd><span class='button_pack'><a href='#none' onclick='return false;' class='btn_md_white product_cancel_close'  data-opuid='".$v[op_uid]."'>닫기</a></span></dd>
								</dl>
							</div>
</form>
						</div>
					";

				break;
			}
		}

		// -- 배송상태 ---
		if(in_array($v[op_sendstatus] , array('배송중', '배송완료')) && $v[op_sendcompany]) {
			$status_print = "<dd><span class='button_pack'><a href='".$arr_delivery_company[$v[op_sendcompany]].rm_str($v[op_sendnum])."' target='_blank' class='btn_md_black' >배송조회</a></span></dd>";
		}
		// -- 배송상태 ---

		if($v[op_add_delivery_price]>0) {	// 상품 개별 배송비가 있는 상품이면 따로 표기한다.
			$tmp_delivery_print = "<div class='guide_txt'>".number_format($v[op_add_delivery_price])."원</div>";
		} else {
			$tmp_delivery_print = "";
		}

		// -- 배송상태 ---
		if($prev_pcode != $v[op_pcode] && $tmp_delivery_print) {
			$delivery_print =  "
				<div class='item_charge'>
					<dl>
						<dd>
							<span class='opt'>배송비</span>
							<div class='value'>" . $tmp_delivery_print . "</div>
						</dd>
					</dl>
				</div>
			";
		}
		$prev_pcode = $v[op_pcode];
		// -- 배송상태 ---



		//상품수 , 포인트 , 상품금액
		$arr_product['cnt'] += $v['op_cnt'];//상품수
		$arr_product['point'] += $v['op_point'];//포인트
		$arr_product['sum'] += $v['op_price'] * $v['op_cnt'];//상품금액
		$arr_product['add_delivery'] += $v['op_add_delivery_price'];//개별배송비 포함

		$p_names[] = $v[p_name]; // 현금영수증 용 상품명

		echo "
			<li>

				<!-- 상품이름과 사진 -->
				<div class='item_info'>
					<div class='thumb'>". ($img_src ? "<img src='" . $img_src . "' >" : "-") ."</div>
					<div class='name'>" . stripslashes($v[p_name]) . "</div>
				</div>

				<!-- 옵션등과 쿠폰정보 (옵션반복) -->
				<div class='item_name'>

					<!-- 옵션별 상태표시 -->
					<div class='order_view_state'>" . $app_status . "</div>

					<dl>
						<!-- 필수옵션 -->
						<dd class=''>
							<div class='option_name'>
								" . ($v[op_option1] ? "".($v[op_is_addoption]=="Y" ? "추가옵션" : "선택옵션")." : " . trim($v[op_option1]." ".$v[op_option2]." ".$v[op_option3]) :  "옵션없음" ) . "
							</div>

							<!-- 수량조정/가격 -->
							<div class='counter_box'>
								<span class='option_number'>
									<!-- 옵션가격 --><span class='option_price'><strong>" . number_format($v[op_price]) . "</strong>원 <em>X</em></span>
									<!-- 구매개수 --><strong>" . $v[op_cnt] . "</strong>개
								</span>
								<span class='counter_right'>
									<!-- 합계금액 --><span class='option_price'><strong>" . number_format($v[op_price] * $v[op_cnt]) . "</strong>원</span>
								</span>
							</div>

						</dd>

						" . $coupon_html . "

					</dl>

					<!-- 컨트롤 버튼들 -->
					<div class='order_view_btn'>
						<dl>". $_cancel_btn . $status_print . "</dl>
					</div>

					". $_individual_cancel . "


				</div>
				<!-- / 옵션등과 쿠폰정보 (옵션반복) -->

				<!-- 상품가격(배송비) -->
				". $delivery_print ."

			</li>
			<!-- 상품별 li반복 -->
		";
	}


	echo "
		<li>
			<div class='item_charge'>
				<dl>
					<dd>
						<span class='opt'>총합계금액</span>
						<div class='value'>
							<strong>" . number_format($arr_product["sum"] + $row["o_price_delivery"]) . "</strong>원
							<div class='guide_txt'>총상품가격 : " . number_format($arr_product["sum"]) . "원</div>
							<div class='guide_txt'>주문상품수 : " . number_format($arr_product["cnt"]) . "개</div>
							<div class='guide_txt'>총배송비 : " . number_format($row['o_price_delivery'] - $arr_product["add_delivery"]) . "원</div>
							<div class='guide_txt'>개별배송비 : " . number_format($arr_product["add_delivery"]) . "원</div>
						</div>
					</dd>
					<dd>
						<span class='opt'>적립금</span>
						<div class='value'>
							<div class='guide_txt'>" . number_format($arr_product["point"]) . "원</div>
						</div>
					</dd>
				</dl>
			</div>
		</li>
	";

?>
				</ul>
			</div>










<form name=frm method=post action="_order.pro.php">
<input type=hidden name=_mode value='modify'>
<input type=hidden name=_ordernum value='<?=$_ordernum?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type="hidden" name="statusUpdate" value="yes">
<input type="hidden" name="style" value="<?=$style?>">
<input type="hidden" name="_paymethod" value="<?php echo $row['o_paymethod']; ?>">

			<!-- 테이블형 div colspan 하고 싶으면 ul에 if_full -->
			<div class="like_table">


				<!-- 단락타이틀 필요한 경우 -->
				<div class="group_title">결제정보</div>
				<ul class="">
					<li class=" opt">주문번호</li>
					<li class="value"><div class="only_txt"><?=$row[o_ordernum]?></div></li>
				</ul>
				<ul class="">
					<li class=" opt">주문가격</li>
					<li class="value">
						<div class="only_txt">
							구매총액 : <?=number_format($row[o_price_total])?>원 +
							&nbsp;배송비 : <?=number_format($row[o_price_delivery])?>원 =
							<?=number_format($row[o_price_total]+$row[o_price_delivery])?>원
						</div>
						<div class="only_txt" style="clear:both; margin-top:10px;">
							<?php
								$order_discount_cnt = $order_discount_sum = 0;
								foreach($arr_order_discount_field as $cfk=>$cfv){

									echo ($order_discount_cnt == 0 ? NULL : ' + ');
									echo $cfv .' : ' . number_format($row[$cfk]) . '원';

									echo ( $cfk == 'o_price_coupon_individual' && $row['o_coupon_individual_uid'] ? '['.$row['o_coupon_individual_uid'].']' : NULL); // 보너스쿠폰사용액일 경우 추가
									echo ( $cfk == 'o_promotion_price' && $row['o_promotion_code'] ? '['.$row['o_promotion_code'].']' : NULL); // 프로모션코드할인금액일 경우 추가

									$order_discount_cnt ++;//순번
									$order_discount_sum += $row[$cfk];//합계
								}
							?>
							= <?php echo number_format($order_discount_sum); ?>원
						</div>
						<div class="only_txt" style="clear:both; margin-top:10px;">
							계산금액 : <?=number_format($row[o_price_total]+$row[o_price_delivery] - $row[o_price_coupon_individual] - $row[o_price_coupon_product] - $row[o_price_usepoint] - $row[o_promotion_price])?>원
						</div>
						<div class="only_txt" style="clear:both; margin-top:10px;">
							실결제가 : <B><?=number_format($row[o_price_real])?>원</B> /
							&nbsp;적립금 : <?=number_format($row[o_price_supplypoint])?>포인트
						</div>
					</li>
				</ul>
				<ul class="">
					<li class=" opt">결제방식</li>
					<li class="value">
						<div class="only_txt"><?=$arr_payment_type[$row[o_paymethod]]?></div>
						<input type=hidden name="_paystatus" value="<?=$row[o_paystatus]?>">
					</li>
				</ul>
				<ul>
					<li class="opt ">주문상태</li>
					<li class="value">
						<span class='texticon_pack checkicon'><?php echo ($row['o_status']?$orderstepArray[$row['o_status']]:$orderstepArray['결제실패']); ?></span>
					</li>
				</ul>
				<?php if($row['o_status'] == '결제대기' && $row['o_canceled']=='N' && $row['o_paystatus']=='N'){ ?>
					<ul>
						<li class="opt ">입금확인</li>
						<li class="value">
							<span class="button_pack">
								<a href="#none" onclick="if(confirm('입금확인 처리하시겠습니까?')){ document.location.href = '_order.pro.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'payconfirm', '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)); ?>'; } return false;" class="btn_md_blue">입금확인</a>
							</span>
							<?php echo _DescStr_mobile_totaladmin('결제완료가 되지 않음에 따라 적립금 사용/지급, 쿠폰사용 등이 적용되지 않은 상태입니다.'); ?>
							<?php echo _DescStr_mobile_totaladmin('입금확인 시 주문상태가결제완료상태로 변경됩니다.'); ?>
							<?php echo _DescStr_mobile_totaladmin('입금취소는 결제수단이 무통장입금일 경우 사용하시기 바랍니다.'); ?>
						</li>
					</ul>
				<?php }else if(in_array($row['o_status'], array('결제완료', '배송대기')) && $row['o_canceled']=='N' && $row['o_paystatus']=='Y'){ ?>
					<ul>
						<li class="opt ">입금확인</li>
						<li class="value">
							<span class="button_pack">
								<a href="#none" onclick="if(confirm('입금취소 처리하시겠습니까?')){ document.location.href = '_order.pro.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'paycancel', '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)); ?>'; } return false;" class="btn_md_black">입금취소</a>
							</span>
							<?php echo _DescStr_mobile_totaladmin('결제완료에 따라 적립금 사용/지급, 쿠폰사용 등이 적용된 상태입니다.'); ?>
							<?php echo _DescStr_mobile_totaladmin('입금취소 시 주문상태가 결제대기상태로 변경됩니다.'); ?>
							<?php echo _DescStr_mobile_totaladmin('입금취소는 결제수단이 무통장입금일 경우 사용하시기 바랍니다.'); ?>
						</li>
					</ul>
				<?php }else if($row['o_canceled']=='N' && $row['o_paystatus']=='Y'){ ?>
					<ul>
						<li class="opt ">입금확인</li>
						<li class="value">
							<span class="button_pack">
								<a href="#none" onclick="alert('배송이 진행된 주문은 입금취소할 수 없습니다.'); return false;" class="btn_md_black">입금취소</a>
							</span>
							<?php echo _DescStr_mobile_totaladmin('결제완료에 따라 적립금 사용/지급, 쿠폰사용 등이 적용된 상태입니다.'); ?>
							<?php echo _DescStr_mobile_totaladmin('배송이 진행된 주문은 입금취소할 수 없습니다.'); ?>
						</li>
					</ul>
				<?php } ?>
				<?php if($row[o_canceled] != 'Y') { ?>
				<ul>
					<li class="opt ">강제취소</li>
					<li class="value">
						<dd><span class='button_pack'><a href='#none' onclick="if(confirm('PG관리자에서 직접 주문을 취소하였거나 일부 오류로 강제 취소를 하실경우 사용 바랍니다.\n\n계속하시겠습니까?'))  document.location.href = '_order.pro.php?_mode=cancel&force_cancel=1&_ordernum=<?=$_ordernum; ?>&_PVSC=<?=$_PVSC; ?>';"  class='btn_md_black' >강제취소</a></span></dd>
					</li>
				</ul>
				<?php } ?>

				<?php
					// 환불요청이 있을 경우
					if( $row['o_moneyback_status'] <> 'none' ){
				?>
					<div class="group_title">환불요청정보</div>
					<ul>
						<li class="opt ">환불처리상태</li>
						<li class="value">
							<?php echo $row['o_moneyback_status'] == "complete" ? "환불완료" : "환불신청중"; ?>
						</li>
					</ul>
					<ul>
						<li class="opt ">환불계좌</li>
						<li class="value">
							<?php echo $row['o_moneyback_comment']; ?>
						</li>
					</ul>
					<ul>
						<li class="opt ">환불요청시간</li>
						<li class="value">
							<?php echo $row['o_moneyback_date']; ?>
						</li>
					</ul>
					<?php
						// 환불요청이 완료된 경우
						if( $row['o_moneyback_status'] == 'complete'){
					?>
					<ul>
						<li class="opt ">환불처리시간</li>
						<li class="value">
							<?php echo $row['o_moneyback_comdate']; ?>
						</li>
					</ul>
					<?php } ?>
				<?php
					}
				?>



				<div class="group_title">주문자 정보</div>
				<ul class="">
					<li class=" opt">회원타입</li>
					<li class="value">
						<?php echo _InputRadio_totaladmin( "_memtype" , array('Y','N'), $row[o_memtype] , '' , array('회원','비회원') , ''); ?>
					</li>
				</ul>
				<ul class="">
					<li class=" opt">주문자 아이디</li>
					<li class="value">
						<input type="text" name="_mid" class="input_design" placeholder="주문자 아이디를 입력하세요." value="<?=$row['o_mid']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">주문자명</li>
					<li class="value">
						<input type="text" name="_oname" class="input_design" placeholder="주문자명을 입력하세요." value="<?=$row['o_oname']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">휴대폰번호</li>
					<li class="value">
						<input type="text" name="_ohp" class="input_design" placeholder="주문자-휴대폰을 입력하세요." value="<?=$row['o_ohp']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">주문자 이메일</li>
					<li class="value">
						<input type="text" name="_oemail" class="input_design" placeholder="주문자-이메일을 입력하세요." value="<?=$row['o_oemail']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">받는 분 이름</li>
					<li class="value">
						<input type="text" name="_rname" class="input_design" placeholder="수신자명을 입력하세요." value="<?=$row['o_rname']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">받는 분 휴대폰번호</li>
					<li class="value">
						<input type="text" name="_rhp" class="input_design" placeholder="수신자-전화를 입력하세요." value="<?=$row['o_rhp']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">배송지 주소</li>
					<li class="value">
						<input type="text" name="_rpost" class="input_design" placeholder="수신자-우편번호를 입력하세요." value="<?=$row['o_rpost']?>" />
						<input type="text" name="_raddr1" class="input_design" placeholder="수신자-주소1을 입력하세요." value="<?=$row['o_raddr1']?>" />
						<input type="text" name="_raddr2" class="input_design" placeholder="수신자-주소2를 입력하세요." value="<?=$row['o_raddr2']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">도로명 주소</li>
					<li class="value">
						<input type="text" name="_raddr_doro" class="input_design" placeholder="수신자-도로명주소를 입력하세요." value="<?=$row['o_raddr_doro']?>" />
					</li>
				</ul>
				<ul class="">
					<li class=" opt">새우편번호</li>
					<li class="value">
						<input type="text" name="_rzonecode" class="input_design" placeholder="수신자-국가기초구역번호를 입력하세요." value="<?=$row['o_rzonecode']?>" />
					</li>
				</ul>
				<ul class=" ">
					<li class="opt">배송시 유의사항</li>
					<li class="value">
						<textarea cols="" rows="" class="textarea_design" name="_content"><?=htmlspecialchars(stripslashes($row[o_content]))?></textarea>
					</li>
				</ul>
				<ul class=" ">
					<li class="opt">관리자메모</li>
					<li class="value">
						<textarea cols="" rows="" class="textarea_design" name="_admcontent"><?=htmlspecialchars(stripslashes($row[o_admcontent]))?></textarea>
					</li>
				</ul>
				<ul class="">
					<li class=" opt">참고사항</li>
					<li class="value"><div class="only_txt">주문일시 : <?=$row[o_rdate]?></div></li>
				</ul>


				<?php
					if( in_array($row['o_paymethod'], array('online','virtual', 'iche')) ) {
						// 가상계좌 발급 내역
						if($row['o_paymethod'] == 'virtual'){
							$virtual_log = _MQ("select ool_account_num, ool_bank_name, ool_deposit_name, ool_bank_owner from smart_order_onlinelog where ool_ordernum='$_ordernum' and ool_type='R' order by ool_uid desc limit 1");
							$row['o_bank'] = '['.$virtual_log['ool_bank_name'].'] ' . $virtual_log['ool_account_num'] . ($virtual_log['ool_bank_owner']?', '.$virtual_log['ool_bank_owner']:null);
							$row['o_deposit'] = $virtual_log['ool_deposit_name'];
						}

						// 현금영수증 발행 정보 추출
						$cashbill = _MQ("select * from smart_baro_cashbill where bc_ordernum = '{$_ordernum}' and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in (1000,2000,3000) order by bc_uid desc limit 1");
				?>
						<?php if($row['o_paymethod'] <> 'iche'){ ?>
						<ul class="">
							<li class=" opt"><?php echo ($row['o_paymethod'] == 'online' ? '무통장 ' : '가상계좌 '); ?>입금정보</li>
							<li class="value">
								<input type="text" name="_bank" class="input_design" placeholder="입금 계좌정보를 입력하세요." value="<?php echo $row['o_bank']; ?>" />
								<input type="text" name="_deposit" class="input_design" placeholder="입금자명을 입력하세요." value="<?php echo $row['o_deposit']; ?>" />
								<label><input type="checkbox" name="_get_tax" id="js_get_tax" value="Y" <?php echo ($row['o_get_tax'] == 'Y' ? 'checked' : null);?>>현금영수증 발행을 신청합니다.</label>
							</li>
						</ul>
						<?php }else{ ?>
						<ul class="">
							<li class=" opt">현금영수증</li>
							<li class="value">
								<label><input type="checkbox" name="_get_tax" id="js_get_tax" value="Y" <?php echo ($row['o_get_tax'] == 'Y' ? 'checked' : null);?>>현금영수증 발행을 신청합니다.</label>
							</li>
						</ul>
						<?php } ?>


						<div class="group_title js_get_tax_form" style="<?php echo ($row['o_get_tax']=='Y' && $row['o_paymethod']=='online' ? null : 'display:none;'); ?>">현금영수증 신청정보</div>

						<ul class="js_get_tax_form" style="<?php echo ($row['o_get_tax']=='Y' && $row['o_paymethod']=='online' ? null : 'display:none;'); ?>">
							<li class=" opt">거래용도</li>
							<li class="value">
								<label class="design"><input type="radio" id="_tax_TradeUsage1" name="_tax_TradeUsage" value="1" <?php echo ($row['o_tax_TradeUsage']<>'2' ? ' checked' : null); ?>>소득공제(주민번호/휴대폰/카드번호)</label>
								<label class="design"><input type="radio" id="_tax_TradeUsage2" name="_tax_TradeUsage" value="2" <?php echo ($row['o_tax_TradeUsage']=='2' ? ' checked' : null); ?>>지출증빙(사업자번호)</label>
							</li>
						</ul>
						<ul class="js_get_tax_form" style="<?php echo ($row['o_get_tax']=='Y' && $row['o_paymethod']=='online' ? null : 'display:none;'); ?>">
							<li class=" opt">신분확인번호 구분</li>
							<li class="value">
								<label class="design" <?php echo ($row['o_tax_TradeUsage']=='2' ? ' style="display:none"' : null); ?>>
									<input type="radio" id="js_tradeMethod1" name="_tax_TradeMethod" value="1"
										<?php echo ($row['o_tax_TradeMethod']=='1' ? ' checked' : null); ?>
										<?php echo ($row['o_tax_TradeUsage']=='2' ? ' disabled' : null); ?>
										>카드번호(국세청에 등록된 카드번호만 가능)
								</label>
								<!-- <label class="design" <?php echo ($row['o_tax_TradeUsage']=='2' ? ' style="display:none"' : null); ?>>
									<input type="radio" id="js_tradeMethod3" name="_tax_TradeMethod" value="3"
										<?php echo ($row['o_tax_TradeMethod']=='3' ? ' checked' : null); ?>
										<?php echo ($row['o_tax_TradeUsage']=='2' ? ' disabled' : null); ?>
										>주민등록번호
								</label> -->
								<label class="design" <?php echo ($row['o_tax_TradeUsage']=='2' ? ' style="display:none"' : null); ?>>
									<input type="radio" id="js_tradeMethod5" name="_tax_TradeMethod" value="5"
										<?php echo ($row['o_tax_TradeMethod']=='5' || $row['o_tax_TradeUsage']=='' ? ' checked' : null); ?>
										<?php echo ($row['o_tax_TradeUsage']=='2' ? ' disabled' : null); ?>
										>휴대폰번호
								</label>
								<label class="design" <?php echo ($row['o_tax_TradeUsage']<>'2' ? ' style="display:none"' : null); ?>>
									<input type="radio" id="js_tradeMethod4" name="_tax_TradeMethod" value="4"
										<?php echo ($row['o_tax_TradeMethod']=='4' ? ' checked' : null); ?>
										<?php echo ($row['o_tax_TradeUsage']<>'2' ? ' disabled' : null); ?>
										>사업자번호
								</label>
							</li>
						</ul>
						<ul class="js_get_tax_form" style="<?php echo ($row['o_get_tax']=='Y' && $row['o_paymethod']=='online' ? null : 'display:none;'); ?>">
							<li class=" opt">신분확인번호</li>
							<li class="value">
								<input type="text" name="_tax_IdentityNum" class="input_design js_number_valid" placeholder="" value="<?php echo onedaynet_decode($row['o_tax_IdentityNum']); ?>" />
								<input type="hidden" name="_identitynum_valid" value="" /><!-- 신분확인번호 유효성체크 -->
							</li>
						</ul>

						<?php
							// 현금영수증 발행 내역이 있다면
							if(sizeof($cashbill) > 0){
						?>
							<ul class="">
								<li class=" opt">현금영수증 발행정보</li>
								<li class="value">
									<strong>발행상태 : </strong><?php echo ($cashbill['BarobillState']=='1000' ? '<span style="color:blue;">임시저장</span>' : '<span style="color:green;">발행완료</span>'); ?>
									<?php if($cashbill['BarobillState']=='1000'){ ?>
										<strong>접수일 : </strong><?php echo date('Y-m-d h:i',strtotime($cashbill['RegistDT'])); ?>
									<?php }else{ ?>
										<strong>발행일 : </strong><?php echo date('Y-m-d h:i',strtotime($cashbill['IssueDT'])); ?>
									<?php } ?>
									<?php if($cashbill['NTSConfirmNum']){ ?><strong>승인번호 : <?php echo $cashbill['NTSConfirmNum']; ?><?php } ?>
									<?php if($cashbill['IdentityNum']){ ?><strong>신분확인번호 : </strong><?php echo $cashbill['IdentityNum']; ?><?php } ?>
									<strong>발행금액 : </strong><?php echo number_format($cashbill['Amount']); ?>
								</li>
							</ul>
						<?php } ?>

				<?php } ?>

			</div>

		</div>
		<!-- / 데이터폼 -->

	</div>
	<!-- / 내용들어가는 공간 -->



	<!-- ●●●●●●●●●● 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><input type="submit" class="btn_lg_red" value="확인"></span></li>
			<li><span class="button_pack"><a href="_order.list.php?<?=enc('d' , $_PVSC)?>" class="btn_lg_white">목록으로</a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
</form>



<?PHP
	$clque = "select * from smart_order_cardlog where oc_oordernum= '".$_ordernum."'";
	$clr = _MQ($clque);
	if(sizeof($clr) > 0 ) {
		echo "
	<div class='container '>
		<div class='data_form'>
			<div class='like_table'>
				<div class='group_title'>결제기록(카드 / 계좌이체 기록) ::: 참조사항</div>
				<ul class=''>
					<li class=' opt'>결제인증번호</li>
					<li class='value'><div class='only_txt'>". $clr[oc_tid] ."</div></li>
				</ul>
		";
		$ex = explode("§§" , $clr[oc_content]);
		foreach($ex as $k=>$v){
			$ex2 = explode("||" , $v);
			if($ex2[1]){
				echo "
					<ul class=''>
						<li class=' opt'>".$ex2[0]."</li>
						<li class='value'><div class='only_txt'>".$ex2[1]."</div></li>
					</ul>
				";
			}
		}
		echo "</div></div></div>";
	}
?>


</div>





<?// include_once(OD_ADDONS_ROOT.'/newpost/newpost.search_m.php'); ?>

<?
	$clque = "select * from smart_order_cardlog where oc_oordernum= '".$_ordernum."'";
	$clr = _MQ($clque);
	//$paymethod_convert = array('B'=>'online','C'=>'card','V'=>'virtual','L'=>'iche');
?>

<script>

	// 현금영수증을 신청합니다. 버튼을 누르면 odtOrder 테이블의 taxorder 필드 업데이트
//	$('input[name=_get_tax]').on('click',function(){
//		if($(this).is(':checked')) { var tax = 'Y'; } //$('.cash_container').css('display','inline-block');
//		else { var tax = 'N'; }//$('.cash_container').hide();
//		//$.post('_order.form.cashUpdate.php',{tax: tax, ordernum: '<?=$_ordernum?>'});
//		$.ajax({
//			data: {tax:tax, ordernum: '<?=$_ordernum?>'},
//			type: 'POST',
//			cache: false,
//			url: '/totalAdmin/_attach/_order.form.cashUpdate.php',
//			success: function() { window.location.reload(); }
//		});
//	});

	$('#cash_issue').on('click',function(e){ // 현금영수증 발행 버튼
		e.preventDefault();
		if (confirm('<?=$row[o_oname]?>님 <?=$row[o_ohp]?> 번호로 현금영수증 발행을 신청합니다.')) {
			$.ajax({
				data: {
					method:		'AUTH',
					ordernum:	'<?=$_ordernum?>',
					paymethod:	'<?=$row[o_paymethod]?>',
					tid:		'<?=$clr[oc_tid]?>',
					member:		'<?=$row[o_mid]?>',
					amount:		'<?=$row[o_price_real]?>',
					num:		'<?=$row[o_ohp]?>',
					use:		'1', // 발급용도 1 = 소득공제, 2 = 지출증빙
					product:	'<?=$cash_product_name?>', // 상품명
					store:		'<?=$siteInfo[s_company_num]?>' // 상점 사업자등록번호
				},
				type: 'POST',
				cache: false,
				url: '/pages/totalCashReceipt.ajax.php',
				success: function(data) {
					if(data=='AUTH'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
						$('#cash_status').remove();
						window.location.reload();
					} else if(data=='OK') {
						return false;
					} else { // 아니라면 오류 메세지
						alert('현금영수증 발행에 실패했습니다.'+data);
					}
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		} else {
			return false;
		}
	});

	$('.cash_cancel').on('click',function(e){ // 현금영수증 발행 버튼
		e.preventDefault();
		var tid = $(this).attr('data-tid');
		if (confirm('현금영수증 발행을 취소합니다.')) {
			$.ajax({
				data: {
					method:		'CANCEL',
					tid: 		tid,
					ordernum:	'<?=$_ordernum?>',
					paymethod:	'<?=$row[o_paymethod]?>',
					member:		'<?=$row[o_mid]?>',
					amount:		'<?=$row[o_price_real]?>',
					num:		'<?=$row[o_ohp]?>',
					use:		'1', // 발급용도 1 = 소득공제, 2 = 지출증빙
					product:	'<?=$cash_product_name?>', // 상품명
					store:		'<?=$siteInfo[s_company_num]?>' // 상점 사업자등록번호
				},
				type: 'POST',
				cache: false,
				url: '/pages/totalCashReceipt.ajax.php',
				success: function(data) {
					if(data=='CANCEL'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
						$('#cash_status').remove();
						window.location.reload();
					} else if(data=='OK') {
						return false;
					} else { // 아니라면 오류 메세지
						alert('현금영수증 취소에 실패했습니다.'+data);
					}
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		} else {
			return false;
		}
	});

	$("form[name=refund]").validate({
		ignore: "input[type=text]:hidden",
		rules: {
			bank_code: { required: true },
			refund_account: { required: true },
			refund_nm: { required: true },
		},
		messages: {
			bank_code: { required: "은행을 선택하세요" },
			refund_account: { required: "계좌번호를 입력하세요" },
			refund_nm: { required: "예금주명을 입력하세요" },
		}
	});
</script>







<!-- / 부분취소신청 -->
<script>
$(document).ready(function(){

	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------
	$('input[name=cancel_type]').on('change',function(){
		var type = $(this).val();
		if( type=='pg' ) { $(this).closest('.like_table').find('.view_pg').show(); } else { $(this).closest('.like_table').find('.view_pg').hide(); }
	});
	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------

	// 부분취소 닫기
    $('.product_cancel_close').on('click',function(){
		var app_uid = $(this).data("opuid");
		$("#opuid_form_" + app_uid).hide(); // 선택 부분취소 닫기
    });

	// 부분취소 열기
    $('.product_cancel').on('click',function(){
		var app_uid = $(this).data("opuid");
		$("#opuid_form_" + app_uid).show(); // 선택 부분취소 열기
    });

    $('.product_cancel_submit').on('click',function(){

		var arr_type = [ '1', 'pg' , 'point' ]; // 1은 dummy data
		var arr_paymethod = [ '1', 'C' , 'G' ]; // 1은 dummy data
		var app_uid = $(this).data("opuid");
		var app_form = $("form[name='product_cancel_"+app_uid+"']");
		var app_cancel_type = $("form[name='product_cancel_"+app_uid+"'] input[name='cancel_type']").filter(function() {if (this.checked) return this;}).val();

		// 사전 체크
		if( jQuery.inArray( app_cancel_type , arr_type) < 0  ) {alert('환불수단을 선택해주시기 바랍니다.');return false;}//환불수단

		// pg일때만 체크하기 추가 kms 2019-05-29
		if (app_cancel_type == "pg"){
			if( jQuery.inArray( $("form[name='product_cancel_"+app_uid+"'] input[name='paymethod']").val() , arr_paymethod) < 1 && $("form[name='product_cancel_"+app_uid+"'] select[name='cancel_bank']").val() == '' ) {alert('은행을 선택해주시기 바랍니다.');return false;}//환불계좌 - 은행선택 (PG선택시 적용되게 함)
			if( jQuery.inArray( $("form[name='product_cancel_"+app_uid+"'] input[name='paymethod']").val() , arr_paymethod) < 1 && $("form[name='product_cancel_"+app_uid+"'] input[name='cancel_bank_account']").val() == '' ) {alert('계좌번호를 입력해주시기 바랍니다.');return false;}//환불계좌 - 계좌번호 (PG선택시 적용되게 함)
			if( jQuery.inArray( $("form[name='product_cancel_"+app_uid+"'] input[name='paymethod']").val() , arr_paymethod) < 1 && $("form[name='product_cancel_"+app_uid+"'] input[name='cancel_bank_name']").val() == '' ) {alert('예금주를 입력해주시기 바랍니다.');return false;}//환불계좌 - 예금주 (PG선택시 적용되게 함)
		}

		if(confirm("정말 주문을 취소하시겠습니까?")===true) {
			var app_data = app_form.serialize();
            $.ajax({
                data: app_data , type: 'POST' , cache: false,
                url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
                success: function(data) {
                    if(data=='OK') {alert('성공적으로 취소요청 되었습니다.'); location.reload(); return false;}
					else {alert(data);}
                },
                error:function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                }
            });
        }
    });
});
</script>
<!-- / 부분취소신청 -->


<script>



// - 현금영수증 발행신청시 신청항목 입력폼 노출 ----
$('#js_get_tax, input[name=_paymethod]').on('click',function(){
		var _trigger = ($('#js_get_tax').prop('checked') && <?php echo ($row['o_paymethod']=='online' ? 'true' : 'false'); ?> ? true : false); // 현금영수증 신청체크 && 무통장체크 모두 만족할때
		if(_trigger){
			$('.js_get_tax_form').show();// 현금영수증 신청폼 보임
		}else{
			$('.js_get_tax_form').hide();// 현금영수증 신청폼 숨김
		}
});
// - 현금영수증 지출증빙일때는 사업자번호만 선택가능 ---
$('input[name=_tax_TradeUsage]').on('change', function(){
	var _val = $(this).val();

	// 소득공제일때
	if(_val == '1'){
		$('input[name=_tax_TradeMethod]').prop('disabled', false);

		$('#js_tradeMethod5').prop('checked', true); // 기본선택 휴대폰번호
		$('#js_tradeMethod4').prop('disabled', true); // 사업자번호 선택불가
	}
	// 지출증빙일때
	else if(_val=='2'){
		$('input[name=_tax_TradeMethod]').prop('disabled', true);

		$('#js_tradeMethod4').prop('disabled', false); // 사업자번호 선택가능
		$('#js_tradeMethod4').prop('checked', true); // 기본선택 사압자번호
	}
	$('.js_number_valid').trigger('change');

	// 미사용항목 감추기
	$("input[name=_tax_TradeMethod]").closest('label').show();
	$("input[name=_tax_TradeMethod]:disabled").closest('label').hide();
});

$('input[name=_tax_TradeMethod]').on('change', function(){
	$('input[name=_tax_IdentityNum]').val('');
	$('input[name=_identitynum_valid]').val('');
});

// 신분확인번호 유효성체크----
$(document).delegate('.js_number_valid', 'change', function(){
	var _type = $('input[name=_tax_TradeMethod]:checked').val() + '';
	var _val = $(this).val();
	//alert(_type);
	if(_type != undefined && _val.replace(' ','') != ''){
		var result = validate_number(_type,_val);
		if(result === false){
			var msg = '';
			if(_type == '1'){
				//카드 번호가 유효한지 검사
				msg = '잘못된 카드번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
			}
			else if(_type == '3'){
				//주민등록 번호가 유효한지 검사
				msg = '잘못된 주민등록번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
			}
			else if(_type == '4'){
				//사업자등록 번호가 유효한지 검사
				msg = '잘못된 사업자번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
			}
			else if(_type == '5'){
				//휴대폰 번호가 유효한지 검사
				msg = '잘못된 휴대폰번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
			}
			$('input[name=_identitynum_valid]').val('');
			//alert(msg);
		}else{
			$('input[name=_identitynum_valid]').val('1');
		}
	}else{
		$('input[name=_identitynum_valid]').val('');
	}
});
$('.js_number_valid').trigger('change');// 최초실행시 한번실행시킨다


function validate_number(_type, number) {

	//빈칸과 대시 제거
	number = number.replace(/[ -]/g,'');

	var match;
	if(_type == "1"){
		//카드 번호가 유효한지 검사
		match = /^(?:(94[0-9]{14})|(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/.exec(number);
	}
	else if(_type == "3"){
		//주민등록 번호가 유효한지 검사
		match = /^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))[1-4][0-9]{6}$/.exec(number);
	}
	else if(_type == "4"){
		//사업자등록 번호가 유효한지 검사
		match = checkBizID(number);
	}
	else if(_type == "5"){
		//휴대폰 번호가 유효한지 검사
		match = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/.exec(number);
	}

	if(match) {
		return true;
	} else {
		return false;
	}
}

function checkBizID(bizID)  //사업자등록번호 체크
{
	// bizID는 숫자만 10자리로 해서 문자열로 넘긴다.
	var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1);
	var tmpBizID, i, chkSum=0, c2, remander;
	 bizID = bizID.replace(/-/gi,'');

	 for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i);
	 c2 = "0" + (checkID[8] * bizID.charAt(8));
	 c2 = c2.substring(c2.length - 2, c2.length);
	 chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1));
	 remander = (10 - (chkSum % 10)) % 10 ;

	if (Math.floor(bizID.charAt(9)) == remander) return true ; // OK!
	  return false;
}



// 폼 유효성 검사
$(document).ready(function(){
	$('form[name=frm]').validate({
			ignore: '.ignore',
			rules: {
					_memtype: { required: true }
					,_mid: { required: true }
					,_oname: { required: true }
					,_ohp: { required: true }
					,_oemail: { required: true , email: true }
					,_rname: { required: true }
					,_rhp: { required: true }
					,_rzonecode: { required: true }
					,_raddr_doro: { required: true }
					,_raddr2: { required: true }
					,_tax_IdentityNum:{ required: function() { return ($("input[name=_get_tax]:checked").val() == "Y" && <?php echo ($row['o_paymethod']=='online' ? 'true' : 'false'); ?> ? true : false); } }
					,_identitynum_valid:{ required: function() { return ($("input[name=_get_tax]:checked").val() == "Y" && <?php echo ($row['o_paymethod']=='online' ? 'true' : 'false'); ?> ? true : false); } }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

			},
			messages: {
					_memtype: { required: '회원타입을 선택해주시기 바랍니다.' }
					,_mid: { required: '주문자 아이디를 입력해주시기 바랍니다.' }
					,_oname: { required: '주문자명을 입력해주시기 바랍니다.' }
					,_ohp: { required: '주문자 휴대폰번호를 입력해주시기 바랍니다.' }
					,_oemail: { required: '주문자 이메일 주소를 입력해주시기 바랍니다.' , email: '이메일 형식이 올바르지 않습니다.' }
					,_rname: { required: '받는 분 이름을 입력해주시기 바랍니다.' }
					,_rhp: { required: '받는 분 휴대폰번호를 입력해주시기 바랍니다.' }
					,_rzonecode: { required: '우편번호 찾기 버튼을 눌러 배송지 주소(우편번호)를 입력해주시기 바랍니다.' }
					,_raddr_doro: { required: '우편번호 찾기 버튼을 눌러 배송지 주소를 입력해주시기 바랍니다.' }
					,_raddr2: { required: '배송지 주소를 입력해주시기 바랍니다.' }
					,_tax_IdentityNum:{ required: "신분확인번호를 입력해주시기 바랍니다." }
					,_identitynum_valid:{ required: "잘못된 신분확인번호 입니다." }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}

	});
});

</script>

<?php
	include dirname(__FILE__)."/wrap.footer.php";
?>