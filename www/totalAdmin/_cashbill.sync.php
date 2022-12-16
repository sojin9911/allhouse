<?PHP

	include_once dirname(__FILE__)."/inc.php";


	// -- DB의 현금영수증 정보와 바로빌의 실제데이터와 다를 수 있음 --
	// 1. 현금영수증발행후 바로빌측에서 국세청으로 정보를 전송(하루한번 오후3시경)
	// 2. 바로빌에서 직접 수정한경우 사이트와 정보가 달라짐 
	// => 최근 3개월치정보를 동기화
	$cque = "
		select MgtKey 
		from smart_baro_cashbill
		where bc_type='barobill' and bc_isdelete = 'N'  and BarobillState in ('2000','5000') and TradeDate >= '". date("Y-m-d", strtotime("-1 month")) ."'
	";
	$chk = _MQ_assoc($cque);

	$arr_tax_mgtnum = array();
	if(sizeof($chk) > 0){
		foreach($chk as $v){
			$arr_tax_mgtnum[] = $v[MgtKey];
		}
	}

	// 총개수
	$app_total = sizeof($arr_tax_mgtnum);
	// 성공수
	$app_success = 0;
	// 실패수
	$app_fail = 0;

	if(sizeof($arr_tax_mgtnum) > 0){

		// 현금영수증 정보 재확인/업데이트
		foreach($arr_tax_mgtnum as $app_tax_mgtnum){
	
			$sque = "";

			// 현금영수증 상태 확인
			$mode = "check_state";
			include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

			if ($Result->BarobillState < 0){ //실패

				$app_fail++;
				$msg .= "> ". $app_tax_mgtnum . " - failed<br>";

				continue;

			}else{ //성공
				$sque .= "
					BarobillState = '". $Result->BarobillState ."'
					,TradeDate = '". $Result->TradeDate ."'
					,RegistDT = '". $Result->RegistDT ."'
					,IssueDT = '". $Result->IssueDT ."' 
					,NTSConfirmNum = '". $Result->NTSConfirmNum ."' 
					,NTSSendDT = '". $Result->NTSSendDT ."' 
					,NTSConfirmDT = '". $Result->NTSConfirmDT ."' 
					,NTSConfirmMessage = '". $Result->NTSConfirmMessage ."' 
				";
			}
			
			if($sque){
				_MQ_noreturn(" update smart_baro_cashbill set ${sque} where MgtKey = '".$app_tax_mgtnum."'");
			}

			$app_success++;
			$msg .= "> ". $app_tax_mgtnum . " - success<br>";

		}

	}

?>
<?php 
	if(!$no_msg){
?>
<dt style="font-family:'나눔고딕','돋움'; font-size:17px; font-weight:600; background:transparent url('/pages/images/mailing/bullet.png') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">바로빌 현금영수증정보 동기화</dt>
<dd style="font-family:'나눔고딕','돋움'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
	총데이터 : <?=$app_total?>건&nbsp;
	성공 : <span id="ID_SUCCESS"><?=$app_success?></span>건&nbsp;
	실패 : </span id="ID_FAILE"><?=$app_fail?></span>건&nbsp;
</dd>
<dd id="ID_MSG" style="font-family:'나눔고딕','돋움'; font-size:13px; padding:10px 15px; font-weight:600; color:#888; margin:0; border:1px solid #ddd; border-top:0; max-height:300px;overflow:auto"><?=$msg?></dd>

<script type="text/javascript">
window.opener.location.reload();

<?if($app_fail<1){?>
setTimeout(function(){ 
window.close();
}, 1000);
<?}?>
</script>
<?php
	}
?>





