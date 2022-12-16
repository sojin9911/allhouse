<?php
/*
	// -- 공통으로 과거에 쓰였으나 유지보수를 위해 나누었다.
*/

include_once('inc.php');
# PG정보
if($_pg_type == '' || in_array($_pg_type, array_keys($arr_pg_type) ) == false){exit; }

// $arr_pg_type['inicis'] = '■■■■■■■■■■■■■';
$_pg_type_name = $arr_pg_type[$_pg_type];

# 할부개월수를 가져온다.
$_pg_installment_peroid = $siteInfo['s_pg_installment_peroid']; // 할부기간
$_pg_noinstallment_peroid = $siteInfo['s_pg_noinstallment_peroid']; // 무이자 할부기간

$arrNormalPeroid = array();

// -- PG사별 할부개월 처리 :: 0:2:3:4:5:6:7:8:9:10:11:12
if( in_array($_pg_type,array('inicis','lgpay','daupay','kcp')) == true){   // 이니시스, LG페이, 다우페이 , kcp(일반할부의 경우 미사용)
	for($i=2;$i<=12; $i++){ $arrNormalPeroid[$i] = $i.'개월'; }
	if( $_pg_type == 'kcp'){
		$_pg_installment_peroid = $_pg_installment_peroid == '' ? '0' :  $_pg_installment_peroid;  // 할부개월 KCP의 경우 입력형태
	}else if( $_pg_type == 'lgpay'){ // SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
		$_pg_installment_peroid = $_pg_installment_peroid == '' ? '1' :  $_pg_installment_peroid;  // 할부개월 KCP의 경우 입력형태
	}else{
		$_pg_installment_peroid = $_pg_installment_peroid == '' ? array_keys($arrNormalPeroid) :  explode(",",$_pg_installment_peroid);  // 할부개월
	}
	$_pg_noinstallment_peroid = $_pg_noinstallment_peroid == '' ? array_keys($arrNormalPeroid) :  explode(",",$_pg_noinstallment_peroid); // 무이자
}
if( in_array($_pg_type,array('billgate')) == true){  // 빌게이트  0:3:6:9:12
	for($i=3;$i<=12; $i+=3){ $arrNormalPeroid[$i] = $i.'개월'; }
	$_pg_installment_peroid = $_pg_installment_peroid == '' ? array_keys($arrNormalPeroid) :  explode(",",$_pg_installment_peroid); // 할부개월
	$_pg_noinstallment_peroid = $_pg_noinstallment_peroid == '' ? array_keys($arrNormalPeroid) :  explode(",",$_pg_noinstallment_peroid); // 무이자
}
if( in_array($_pg_type,array('kcp')) == true){   } // KCP :: KCP의 경우 초대 할부 개월수 만 입력 가능하다. ex) 12, 입력시 12개월까지 할부선택가능

// -- 빌게이트 일경우 JDK 설치유무를 판별
if($_pg_type == 'billgate'){
	// 빌게이트 JDK 설치 확인
	@include $_SERVER[DOCUMENT_ROOT]."/pg/pc/billgate/config.php";
	$cmd = sprintf("%s \"%s\" \"%s\" \"%s\"", $COM_CHECK_SUM, "DIFF", $CHECK_SUM, $temp); $checkSum = @exec($cmd);
}

?>
<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
		</colgroup>
		<tbody>


		<?php if( $_pg_type == 'inicis') {  // -- 이니시스 -- ?>
			<tr>
				<th class="ess"><?php echo $_pg_type_name ?> 상점 ID</th>
				<td colspan="3">
					<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" />
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받은 상점 아이디 또는 사이트 코드를 입력하세요.")?>
						<?=_DescStr($_pg_type_name." 테스트 아이디는 <em>INIpayTest</em> 입니다 (테스트 결제 시에는 카드만 가능합니다.)")?>
					</div>
				</td>
			</tr>
			<tr>
				<th class="ess"><?php echo $_pg_type_name; ?> 사인키</th>
				<td colspan="3">
					<input type="text" name="_pg_skey" class="design" value="<?php echo $siteInfo['s_pg_skey']; ?>" style="width:340px" />
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받으신 사인키를 입력해 주세요. ")?>
						<?=_DescStr($_pg_type_name." 테스트 사인키는  <em>SU5JTElURV9UUklQTEVERVNfS0VZU1RS</em> 입니다.")?>
					</div>
				</td>
			</tr>
			<tr>
				<th><?php echo $_pg_type_name ?> 에스크로 코드</th>
				<td colspan="3">
					<input type="text" name="_pg_code_escrow" class="design" value="<?php echo $siteInfo['s_pg_code_escrow']; ?>" />
					<label class="design left20"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y" ? "checked" : NULL;?>>에스크로 가입정보를 노출합니다.</label>
					<div class="tip_box">
						<?=_DescStr("에스크로 사용 시 PG사에서 발급받은 에스크로 아이디를 입력하세요.")?>
						<?=_DescStr("에스크로 가입정보의 경우 PG사 정책에 따라 노출되지 않을 수 있습니다.")?>
					</div>
				</td>
			</tr>
			<tr>
				<th><?php echo $_pg_type_name; ?> 에스크로 사인키</th>
				<td colspan="3">
					<input type="text" name="_pg_escrow_skey" class="design" value="<?php echo $siteInfo['s_pg_escrow_skey']; ?>" style="width:340px" />
					<?=_DescStr($_pg_type_name."에서 발급받으신 에스크로 사인키를 입력해 주세요.")?>
				</td>
			</tr>

            <?php // LCY : 2021-07-04 : 신용카드 간편결제 추가  ?>
            <?php if( count($arr_available_easypay_pg[$siteInfo['s_pg_type']]) > 0) { ?>
            <tr>
                <th>간편결제</th>
                <td colspan="3">
                    <?php echo _InputCheckbox('s_pg_paymethod_easypay', array_keys($arr_available_easypay_pg['inicis']), ($siteInfo['s_pg_paymethod_easypay'] != '' ? explode(",",$siteInfo['s_pg_paymethod_easypay']) : array()) , '', array_values($arr_available_easypay_pg['inicis']), ''); ?>

                    <div id="" class="tip_box"> 
                    <?php   
                        echo _DescStr('PG사 간편결제의 경우 기본 카드결제로 진행되며, 이용가능 PG사에서 별도 계약 후 사용 가능합니다.','black');
                        echo _DescStr('PG사 간편결제 수단 설정 시 주문/결제 페이지내 에서 결제 수단 선택이 가능합니다.');
                    ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
            <?php // LCY : 2021-07-04 : 신용카드 간편결제 추가  ?>

			<tr>
				<th class=""><?php echo $_pg_type_name.' 참고사항' ?></th>
				<td colspan="3">
					<div class="tip_box">
						<?=_DescStr($_pg_type_name." 승인절차가 끝나시면 반드시 상위에 고객님의 key 아이디 및 키파일을 등록하셔야만 정상 결제가 이루어집니다.")?>
						<?=_DescStr("키파일 설치의 경우 ".$_pg_type_name."에서 받으신 키파일을 압축을 푸시면 디렉토리가 생성되며 FTP로 접속하셔서 /pg/pc/inicis/key, /pg/m/inicis/key  경로에 디렉토리까지 포함한 파일 전체를 올려주십시오.")?>
					</div>
				</td>
			</tr>

			<tr>
				<th class="ess">일반 할부 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','일반 할부') , ''); ?>
					<?=_DescStr("일반 할부 설정을 선택해 주세요.")?>
					<div class="pg-installment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">일반 할부 기간 선택 : </span><?php echo _InputCheckbox( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
					</div>
				</td>
			</tr>
			<tr style="display:none;" title="무이자 미사용처리 : 카드사별로 계약 후 문의">
				<th class="ess">무이자 할부 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_noinstallment' , array('Y','N'), ($siteInfo['s_pg_noinstallment'] ? $siteInfo['s_pg_noinstallment'] : 'N') , '' , array('사용','미사용') , ''); ?>
					<?=_DescStr("무이자 할부 설정을 선택해 주세요.")?>
					<div class="pg-noinstallment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">무이자 할부 기간 선택 : </span><?php echo _InputCheckbox( '_pg_noinstallment_peroid' , array_keys($arrNormalPeroid), $_pg_noinstallment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
					</div>
				</td>
			</tr>
			<tr>
				<th>가상계좌 입금내역 통보 URL</th>
				<td colspan="3">
					<input type="text"  id="vacc" class="design" value="http://<?=$system['host'].OD_PROGRAM_DIR?>/shop.order.result_inicis_vacctinput.php" style="width:540px" readonly />
					<a href="#none"  data-clipboard-target="#vacc" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
					<div class="tip_box">
						<?=_DescStr("상단의 URL을 복사하셔서 <em>가맹점관리자 > 거래내역 > 가상계좌 > 입금통보방식선택 메뉴의 입금내역 통보 URL</em> 항목에 넣어주세요.")?>
					</div>
				</td>
			</tr>
		<?php } else if( $_pg_type == 'kcp') {  // ■■■■■■■■■■■KCP■■■■■■■■■■ ?>
			<tr>
				<th class="ess"><?php echo $_pg_type_name; ?> 사이트 코드</th>
				<td colspan="3">
					<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" />
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받은 상점 아이디 또는 사이트 코드를 입력하세요.")?>
						<?=_DescStr($_pg_type_name." 테스트 사이트 코드는 <em>T0000</em> 입니다.")?>
					</div>
				</td>
			</tr>

			<tr>
				<th class="ess"><?php echo $_pg_type_name; ?> 사이트 키</th>
				<td colspan="3">
					<input type="text" name="_pg_key" class="design" value="<?php echo $siteInfo['s_pg_key']; ?>" style="width: 340px;"/>
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받은 사이트 키를 입력하세요.")?>
						<?=_DescStr($_pg_type_name." 테스트 사이트 키는 <em>3grptw1.zW0GSo4PQdaGvsF__</em> 입니다.")?>
					</div>
				</td>
			</tr>
			<tr>
				<th>에스크로 가입정보</th>
				<td colspan="3">
					<label class="design"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y"  ? "checked" : NULL;?>>에스크로 가입정보를 노출합니다.</label>
						<?=_DescStr("PG사의 내부 정책에 따라 노출되지 않을 수 있습니다.")?>
				</td>
			</tr>
			<tr>
				<th class="ess">일반 할부 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','일반 할부') , ''); ?>
					<?=_DescStr("일반 할부 설정을 선택해 주세요.")?>
					<div class="pg-installment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">최대</span>
						<?php echo _InputSelect( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
						<span class="fr_tx">개월 까지 할부</span>

					</div>
				</td>
			</tr>
			<tr style="display:none;" title="무이자 미사용처리 : 카드사별로 계약 후 문의">
				<th class="ess">무이자 할부 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_noinstallment' , array('Y','N'), ($siteInfo['s_pg_noinstallment'] ? $siteInfo['s_pg_noinstallment'] : 'N') , '' , array('사용','미사용') , ''); ?>
					<?=_DescStr("무이자 할부 설정을 선택해 주세요.")?>
					<div class="pg-noinstallment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">무이자 할부 기간 선택 : </span><?php echo _InputCheckbox( '_pg_noinstallment_peroid' , array_keys($arrNormalPeroid), $_pg_noinstallment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>

					</div>
				</td>
			</tr>
			<tr>
				<th>가상계좌 입금내역 통보 URL</th>
				<td colspan="3">
					<input type="text"  id="vacc" class="design" value="http://<?=$system['host'].OD_PROGRAM_DIR?>/shop.order.result_kcp_return.php" style="width:540px" readonly />
					<a href="#none"  data-clipboard-target="#vacc" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
					<div class="tip_box">
						<?=_DescStr("상단의 URL을 복사하셔서 <em>가맹점관리자 > 정보변경 > 공통 URL 정보</em> 항목에 넣어주세요.")?>
						<?=_DescStr("인코딩의 경우 UTF-8로 설정해 주세요.")?>
					</div>
				</td>
			</tr>
		<?php }else if($_pg_type == 'lgpay'){ // ■■■■■■■■■■■LGPAY■■■■■■■■■■ ?>
			<!-- SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22 : 토스페이먼츠로 교체 -->
			<tr>
				<th class="ess"><?php echo $_pg_type_name.' clientKey' ?></th>
				<td colspan="3">
					<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" style="width: 340px;"/>
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받은 clientKey를 입력하세요.")?>
						<?=_DescStr("테스트 clientKey 키는 토스페이먼츠 홈페이지에서 회원가입 후 발급받을 수 있습니다.")?>
						<?//=_DescStr($_pg_type_name." 테스트 ID는 <em>lgdacomxpay</em> 입니다.")?>
					</div>
				</td>
			</tr>

			<tr>
				<th class="ess"><?php echo $_pg_type_name.' secretKey' ?></th>
				<td colspan="3">
					<input type="text" name="_pg_key" class="design" value="<?php echo $siteInfo['s_pg_key']; ?>" style="width: 340px;"/>
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받은 secretKey 를 입력해 주세요.")?>
						<?=_DescStr("테스트 secretKey 키는 토스페이먼츠 홈페이지에서 회원가입 후 발급받을 수 있습니다.")?>
						<?//=_DescStr($_pg_type_name." 테스트 secretKey 키는 <em>95160cce09854ef44d2edb2bfb05f9f3</em> 입니다.")?>
					</div>
				</td>
			</tr>

			<tr>
				<th>에스크로 가입정보</th>
				<td colspan="3">
					<label class="design"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y"  ? "checked" : NULL;?>>에스크로 가입정보를 노출합니다.</label>
						<?=_DescStr("PG사의 내부 정책에 따라 노출되지 않을 수 있습니다.")?>
				</td>
			</tr>
			<tr>
				<th class="ess">일반 할부 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','일반 할부') , ''); ?>
					<?=_DescStr("일반 할부 설정을 선택해 주세요.")?>
					<div class="pg-installment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">일반 할부 기간 선택 : </span>
						<?php echo _InputSelect( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
						<?=_DescStr("최대 할부 개월 수")?>
					</div>
				</td>
			</tr>
			<tr style="display:none;" title="무이자 미사용처리 : 카드사별로 계약 후 문의">
				<th class="ess">무이자 할부 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_noinstallment' , array('Y','N'), ($siteInfo['s_pg_noinstallment'] ? $siteInfo['s_pg_noinstallment'] : 'N') , '' , array('사용','미사용') , ''); ?>
					<?=_DescStr("무이자 할부 설정을 선택해 주세요.")?>
					<div class="pg-noinstallment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">무이자 할부 기간 선택 : </span><?php echo _InputCheckbox( '_pg_noinstallment_peroid' , array_keys($arrNormalPeroid), $_pg_noinstallment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>

					</div>
				</td>
			</tr>
			<!-- // SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22 : 토스페이먼츠로 교체 -->

		<?php }else if($_pg_type == 'billgate'){ // ■■■■■■■■■■■빌게이트(billgate)■■■■■■■■■■ ?>
			<tr>
				<th class="ess"><?php echo $_pg_type_name.' 상점 ID' ?></th>
				<td colspan="3">
					<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" />
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받은 상점 ID를 입력하세요.")?>
						<?=_DescStr($_pg_type_name." 테스트 상점 ID는 <em>glx_api</em> 이며 카드결제만 테스트 가능합니다.")?>
					</div>
				</td>
			</tr>
			<tr>
				<th class="ess">활성화 모드</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_mode' , array('service','test'), ($siteInfo['s_pg_mode'] ? $siteInfo['s_pg_mode'] : 'Y') , '' , array('실결제 모드','테스트 모드') , ''); ?>
					<div class="tip_box">
						<?=_DescStr("테스트 모드로 설정 시 실 결제가 이루어지지 않습니다.")?>
					</div>
				</td>
			</tr>

			<tr>
				<th class=""><?php echo $_pg_type_name.' 참고사항' ?></th>
				<td colspan="3">
						<?=_DescStr(($checkSum)?"JDK (Java Development Kit) 가 정상적으로 설치되어 있습니다.":"<span style='color:red;'>JDK (Java Development Kit) 1.5 버전 이상이 반드시 설치되어 있어야 합니다 (서버 관리자에게 문의하세요).</span>",'black')?>
						<div class="clear_both"></div>
						<?=_DescStr("빌게이트 부가 설정 매뉴얼")?>
						<a href="#none" class="c_btn h22 js_pg_popup if_with_tip" data-mode="config" data-width="1120" data-height="700" data-page="_config.pg_billgate.popup.php">부가설정 매뉴얼 열기</a>
						<div class="clear_both"></div>
						<?=_DescStr("가상계좌 입금내역 통보 URL 신청 안내메일")?>
						<a href="#none" class="c_btn h22 js_pg_popup if_with_tip" data-mode="mail" data-width="1120" data-height="540" data-page="_config.pg_billgate.popup.php">안내메일 양식보기</a>

				</td>
			</tr>

			<tr>
				<th>에스크로 가입정보</th>
				<td colspan="3">
					<label class="design"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y"  ? "checked" : NULL;?>>에스크로 가입정보를 노출합니다.</label>
						<?=_DescStr("PG사의 내부 정책에 따라 노출되지 않을 수 있습니다.")?>
				</td>
			</tr>
			<tr>
				<th class="ess">일반 할부 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','일반 할부') , ''); ?>
					<?=_DescStr("일반 할부 설정을 선택해 주세요.")?>
					<div class="pg-installment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">일반 할부 기간 선택 : </span><?php echo _InputCheckbox( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
					</div>
				</td>
			</tr>
		<?php } else if( $_pg_type == 'daupay') {  // ■■■■■■■■■■■daupay■■■■■■■■■■ ?>
			<tr>
				<th class="ess"><?php echo $_pg_type_name; ?> 가맹점 ID</th>
				<td colspan="3">
					<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" />
					<div class="tip_box">
						<?=_DescStr("PG사에서 발급받은 가맹점 ID를 입력해 주세요.")?>
						<?=_DescStr($_pg_type_name." 테스트 가맹점 ID는 PG사에 요청하여 발급받을 수 있습니다.")?>
					</div>
				</td>
			</tr>

			<tr>
				<th class="ess"><?php echo $_pg_type_name; ?> 가맹점 암호화 키</th>
				<td colspan="3">
					<input type="text" name="_pg_enc_key" class="design" value="<?php echo $siteInfo['s_pg_enc_key']; ?>" style="width: 340px;"/>
					<div class="tip_box">
						<?//=_DescStr("PG사에서 발급받은 가맹점 암호화 키를 입력하세요.")?>
						<?=_DescStr("신용카드와 계좌이체 취소연동을 위해 ".$_pg_type_name." 관리자 페이지에서 암호화 키를 설정하셔야 합니다.")?>
					</div>
				</td>
			</tr>
			<tr>
				<th class="ess">활성화 모드</th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_mode' , array('service','test'), ($siteInfo['s_pg_mode'] ? $siteInfo['s_pg_mode'] : 'Y') , '' , array('실결제 모드','테스트 모드') , ''); ?>
						<?=_DescStr("테스트 모드로 설정 시 실 결제가 이루어지지 않습니다.")?>
				</td>
			</tr>
			<tr>
				<th class=""><?php echo $_pg_type_name.' 참고사항' ?></th>
				<td colspan="3">

						<?=_DescStr("가상계좌와 계좌이체는 에스크로 결제로 기본 적용됩니다.")?>
						<div class="clear_both"></div>
						<?=_DescStr("가상계좌는 PG사와 취소연동이 되지 않으며, 주문취소 후 고객에게 직접 환불 해주셔야 합니다.")?>
						<div class="clear_both"></div>
						<?=_DescStr("정상 서비스를 위해 서버 내 방화벽설정에서 IP 27.102.213.207, 27.102.213.205 에 대한 64001, 46001 포트를 열어주셔야 합니다. (서버업체에 문의)",'black')?>
						<div class="clear_both"></div>
						<?=_DescStr($_pg_type_name." 서비스 시 필요한 정보 안내메일 양식")?>
						<a href="#none" class="c_btn h22 js_pg_popup if_with_tip" data-mode="mail" data-width="1120" data-height="700" data-page="_config.pg_daupay.popup.php">안내메일 양식보기</a>

				</td>
			</tr>
			<tr>
				<th>에스크로 가입정보</th>
				<td colspan="3">
					<label class="design"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y" ? "checked" : NULL;?>>에스크로 가입정보를 노출합니다.</label>
					<div class="tip_box">
						<?=_DescStr("PG사의 내부 정책에 따라 노출되지 않을 수 있습니다.")?>
						<?=_DescStr("결제수단 중 가상계좌와 계좌이체는 에스크로 결제로 기본 적용됩니다.",'black')?>
					</div>
				</td>
			</tr>
			<tr>
				<th class="ess">일반 할부 설정<!-- 구분자 : --></th>
				<td colspan="3">
					<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','일반 할부') , ''); ?>
					<?=_DescStr("일반 할부 설정을 선택해 주세요.")?>
					<div class="pg-installment-peroid" style="display:none;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">일반 할부 기간 선택 : </span><?php echo _InputCheckbox( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
					</div>
				</td>
			</tr>
		<?php } ?>


		<?php // ■■■■■■■■■■■■■■■■공통■■■■■■■■■■■■■■■■■ ?>
			<tr>
				<th class="ess">가상계좌 입금기한</th>
				<td colspan="3">
					<input type="text" name="_pg_virtual_date" class="design" value="<?php echo $siteInfo['s_pg_virtual_date'] == '' ? 3 : $siteInfo['s_pg_virtual_date']; ?>" style="width:80px; text-align:right;" /><span class="fr_tx">일</span>
					<?=_DescStr("가상계좌 입금기한일을 입력해 주세요.")?>
				</td>
			</tr>
			<tr>
				<th class="">가상계좌 부분취소 안내</th>
				<td colspan="3">
					<div class="tip_box">
						<?=_DescStr("가상계좌의 부분취소는 PG사와 취소연동이 되지 않으며, 주문취소 후 고객에게 직접 환불 해야 합니다." , 'black')?>
						<?=_DescStr("1) 주문관리 메뉴에서 부분취소할 상품이 포함된 주문을 검색합니다.")?>
						<?=_DescStr("2) 검색된 주문의 '상세보기' 버튼을 눌러 주문 상세보기 페이지에 접속합니다.")?>
						<?=_DescStr("3) 부분취소할 상품의 '부분취소' 버튼을 눌러 부분취소를(직접환불) 진행합니다.")?>
						<?=_DescStr("4) 부분취소요청관리 메뉴에서 부분취소 요청 내역을 확인합니다.")?>
						<?=_DescStr("3) 취소된 금액을 고객님의 환불계좌에 직접 이체 합니다.")?>
						<?=_DescStr("4) 부분취소요청관리 메뉴에서 '취소처리' 버튼을 눌러 해당 상품을 취소합니다.")?>
					</div>
				</td>
			</tr>
			<tr>
				<th>앱 스키마</th>
				<td colspan="3">
					<input type="text" name="_pg_app_scheme" class="design" value="<?php echo $siteInfo['s_pg_app_scheme']; ?>" style="width:340px;" />
					<div class="tip_box">
						<?=_DescStr("별도의 APP(앱) 가 있을경우 앱 스키마를 입력해주세요.")?>
						<?=_DescStr("앱 스키마 값을 설정하시면 IOS 기기에서 ISP 결제를 할 경우 결제 완료처리 후 정상적으로 설정된 앱 스키마 값을 통해 쇼핑몰 앱으로 돌아갈 수 있습니다. ")?>
						<?=_DescStr("별도의 앱을 사용하지 않으실 경우 빈값으로 설정해 주세요.")?>
						<?=_DescStr("PG사에 따라 앱 스키마 옵션 지원이 안 될 수 있습니다.",'black')?>
					</div>
				</td>
			</tr>
			<tr>
				<th class="ess">현금영수증 설정</th>
				<td colspan="3">
					<?php echo _InputRadio( '_cash_receipt_use' , array('Y','N'), ($siteInfo['s_cash_receipt_use'] ? $siteInfo['s_cash_receipt_use'] : 'N') , '' , array('사용','미사용') , ''); ?>
					<?php echo _DescStr("PG사 정책에 따라 현금영수증에 대한 설정과 상관없이 사용 또는 미사용 될 수 있습니다."); ?>
					<div class="cash-receipt-use-none" style="display: none;"><!-- PG사별로 다른 관계로 해당 상세 설정은 사용하지 않는다 2018-04-16 -->
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">발급필수 설정 : </span>
						<?php echo _InputRadio( '_cash_receipt_sel' , array('Y','N'), ($siteInfo['s_cash_receipt_sel'] ? $siteInfo['s_cash_receipt_sel'] : 'N') , '' , array('사용','미사용') , ''); ?>
						<?=_DescStr("현금영수증 발급에 대한 필수 설정을 할 수 있습니다.")?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">현금영수증 발급 방법 : </span>
						<?php echo _InputRadio( '_cash_receipt_issued_type' , array('auto','admin'), ($siteInfo['s_cash_receipt_issued_type'] ? $siteInfo['s_cash_receipt_issued_type'] : 'auto') , '' , array('자동발급','관리자 발급') , ''); ?>
						<?=_DescStr("현금영수증 신청 시 발급 방법에 대한 설정을 할수 있습니다.")?>
					</div>
				</td>
			</tr>

			<tr>
				<td colspan="4">
					<div class="tip_box">
						<?=_DescStr("가상계좌 결제 후 현금영수증을 신청한 경우, 가맹점관리자 페이지에서 직접 발급하셔야 합니다.")?>
						<?=_DescStr("결제취소 시에는 카드결제 같은 경우에는 사이트에서 취소처리 하시면 PG사와 연동하여 카드사까지 한 번에 취소처리가 됩니다.")?>
						<?=_DescStr("실시간 계좌이체 같은 경우에는 사이트에서 취소처리 후 PG사 관리자모드에서 또 한번 취소처리를 하셔야 합니다.")?>
						<?=_DescStr("복합과세의 경우 반드시 PG와 먼저 복합과세 계약을 신청하셔야 합니다.")?>
					</div>
				</td>
			</tr>
		<?php // ■■■■■■■■■■■■■■■■공통■■■■■■■■■■■■■■■■■ ?>

		</tbody>
	</table>
</div>