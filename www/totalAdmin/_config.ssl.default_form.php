<?php

	// 2017-07-11 ::: 보안서버 ::: JJC
	include_once('wrap.header.php');

?>


<form name="frm" method='post' action='_config.ssl.pro.php' ENCTYPE='multipart/form-data'>
<input type='hidden' name='pass_menu' value='_config.ssl.default_form'>


	<!--보안서버 설정 -->
	<div class="group_title"><strong>보안서버 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">보안서버 사용여부</th>
					<td colspan='3'>
						<?php
							echo _InputRadio( "_ssl_check" , array("Y","N") ,  (!$siteInfo['s_ssl_check'] ? "N" : $siteInfo['s_ssl_check'] ) , "" , array("사용","미사용") , "");
							echo '
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="tip_box">
									' . _DescStr("
										<strong>보안서버(SSL)구축 의무 강화 안내</strong><br>
										민원이 발생할 경우 정보통신망법에 따라 보안서버(SSL) 구축 의무사항 위반 시 사전경고 없이 최고 3,000만원의 과태료가 부과될 수 있습니다.<br>
										로그인, 회원가입, 게시판, 주문, 결제 시 이름, 전화번호 등을 취급하는 사이트가 이에 해당합니다.
									") . '
								</div>
							';
						?>
					</td>
				</tr>

				<tr>
					<th>보안서버 진행상태</th>
					<td>
						<?=_InputSelect( "_ssl_status" , array('대기' , '진행' , '만료') , $siteInfo['s_ssl_status'] , "" , "" , "-진행상태-")?>
						<div class="tip_box">
							<?=_DescStr("보안서버 진행상태가 진행일 경우에만 보안서버를 사용할 수 있습니다.")?>
						</div>
					</td>
					<th>보안서버 사용기간</th>
					<td>
						<input type="text" name="_ssl_sdate" class="design js_pic_day" value="<?php echo $siteInfo['s_ssl_sdate']; ?>" style="width:90px; cursor:pointer;" readonly>
						<span class="fr_tx">~</span>
						<input type="text" name="_ssl_edate" class="design js_pic_day" value="<?php echo $siteInfo['s_ssl_edate']; ?>" style="width:90px; cursor:pointer;" readonly>
						<div class="tip_box">
							<?=_DescStr("사용기간 이전일 경우 보안서버 진행상태는 대기로 변경됩니다.")?>
							<?=_DescStr("사용기간 이후일 경우 보안서버 진행상태는 만료로 변경됩니다.")?>
						</div>
					</td>
				</tr>

				<tr>
					<th>보안서버 도메인</th>
					<td>
						<span class="fr_tx">https://</span><input type="text" name="" class="design" value="<?php echo $siteInfo['s_ssl_domain']; ?>" style="width:300px" disabled>
						<div class="tip_box">
							<?php
								if($siteInfo['s_ssl_domain'] == ""){
									echo _DescStr("<em>대표도메인</em>이 등록되지 않았습니다.", "red");
									echo '<div class="dash_line"></div><a href="_config.default.form.php" target="_blank" class="c_btn h27 black">대표도메인 설정 바로가기</a>';
								}else{
									echo _DescStr("[환경설정 > 기본설정 > 쇼핑몰 기본정보] 메뉴의 <em>대표도메인</em>항목과 동일한 도메인을 사용합니다.");
								}
							?>
						</div>
					</td>
					<th>보안서버 포트번호</th>
					<td>
						<input type="text" name="_ssl_port" class="design" value="<?php echo $siteInfo['s_ssl_port']; ?>" style="width:50px" ><span class="fr_tx">번</span>
						<div class="tip_box">
							<?=_DescStr("기본포트(443)가 아닐 경우에만 입력해주시면 됩니다.")?>
							<?=_DescStr("예) 포트가 445일 경우 보안서버 URL은 http://www.domain.com:445/ 입니다.")?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 보안서버 설정 -->



	<!--보안서버 인증정보 설정 -->
	<div class="group_title"><strong>보안서버 인증정보 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">보안서버 인증이미지</th>
					<td >
						<div style='clear:both; width:600px;'>
							<!-- ● 데이터 리스트 --><table class='table_list'>
								<colgroup>
									<col width='150'><col width='150'><col width='150'>
									<col width='150'><col width='150'><col width='150'>
								</colgroup>
								<tbody>
									<tr>
										<td >
											<input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgN" value="N" <?=( !$siteInfo['s_ssl_pc_img'] || $siteInfo['s_ssl_pc_img'] == 'N' ? 'checked' : '' )?>>
											미사용
										</td>
										<td >
											<input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgU" value="U" <?=( $siteInfo['s_ssl_pc_img'] == 'U' ? 'checked' : '' )?>>
											UCERT SSL
										</td>
										<td >
											<input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgK" value="K" <?=( $siteInfo['s_ssl_pc_img'] == 'K' ? 'checked' : '' )?>>
											KISA SSL
										</td>
										<td >
											<input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgA" value="A" <?=( $siteInfo['s_ssl_pc_img'] == 'A' ? 'checked' : '' )?>>
											Alpha SSL
										</td>
										<td >
											<input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgC" value="C" <?=( $siteInfo['s_ssl_pc_img'] == 'C' ? 'checked' : '' )?>>
											SECTIGO
										</td>
										<td >
											<input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgE" value="E" <?=( $siteInfo['s_ssl_pc_img'] == 'E' ? 'checked' : '' )?>>
											기타
										</td>
									</tr>
									<tr>
										<td ></td>
										<td >
											<img src="https://www.ucert.co.kr/images/maincenterContent/trustlogo/ucert_black.gif" style="width:55px; height:62px;" alt="UCERT SSL">
										</td>
										<td >
											<img src="https://www.ucert.co.kr/image/trustlogo/s_kisa.gif" style="width:65px; height:63px;" alt="KISA SSL">
										</td>
										<td >
											<img src="https://www.ucert.co.kr/image/trustlogo/alphassl_seal.gif" style="width:100px; height:48px;" alt="Alpha SSL">
										</td>
										<td >
											<img src="https://www.ucert.co.kr/images/seal/seal_sectigo.png" style="height:70px;" alt="SECTIGO">
										</td>
										<td ></td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="tip_box"><?=_DescStr("보안서버 인증이미지 설정에 따라 지정된 PC 사용자페이지에 노출됩니다.")?></div>

					</td>
				</tr>

				<tr class="auth_view_etc" style="<?=( $siteInfo['s_ssl_pc_img'] <> 'E' ? 'display:none;' : '' )?>">
					<th class="ess">보안서버 기타 인증이미지 소스</th>
					<td >
						<textarea name="_ssl_pc_img_etc" rows="3" cols="" class="design" ><?=$siteInfo['s_ssl_pc_img_etc']?></textarea>
						<div class="tip_box">
							<?=_DescStr("보안서버 인증이미지를 기타로 선택할 경우 클릭시 링크를 포함한 인증이미지 소스를 입력하시기 바랍니다.");?>
							<?=_DescStr("소스에 대한 예는 다음과 같습니다.<br><br>&lt;!--KISA Certificate Mark--&gt;<br>&lt;img src=\"https://www.ucert.co.kr/image/trustlogo/s_kisa.gif\" width=\"65\" height=\"63\" align=\"absmiddle\" border=\"0\" style=\"cursor:pointer\" <br>Onclick=javascript:window.open(\"https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum={실넘버}&sealid={실아이디}\",\"mark\",\"scrollbars=no,resizable=no,width=565,height=780\");&gt;<br>&lt;!--KISA Certificate Mark--&gt;");?>
						</div>
					</td>
				</tr>

				<tr class="auth_view_seal" style="<?=( $siteInfo['s_ssl_pc_img'] == 'E' ? 'display:none;' : '' )?>">
					<th class="ess">보안서버 인증 정보</th>
					<td >
						<span class="fr_tx">보안서버 Seal Number</span>
						<input type="text" name="_ssl_pc_sealnum" class="design" value="<?=$siteInfo['s_ssl_pc_sealnum']?>" style="width:400px" >

						<div class="dash_line"><!-- 점선라인 --></div>

						<span class="fr_tx">보안서버 Seal ID</span>
						<input type="text" name="_ssl_pc_sealid" class="design" value="<?=$siteInfo['s_ssl_pc_sealid']?>" style="width:600px" >

						<div class="dash_line"><!-- 점선라인 --></div>

						<div class="tip_box">
							<?=_DescStr("원데이넷을 통해 신청하신 경우 보안서버의 Seal Number와 Seal ID를 입력하시기 바랍니다.")?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- 보안서버 인증정보 설정 -->


	<!--보안서버 설정 -->
	<div class="group_title"><strong>보안서버 안내 및 신청</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr >
					<th>보안서버 안내 및 신청</th>
					<td colspan='3'>
						<a href="http://www.onedaynet.co.kr/p/add_05.html" target='_blank' class="c_btn h27 black">SSL 보안서버 인증서 안내 및 신청</a>
						<div class="tip_box">
							<?=_DescStr("보안서버에 대한 안내사항을 확인하거나 보안서버를 신청하실 수 있습니다.")?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- 보안서버 설정 -->


	<?php echo _submitBTNsub(); ?>

</form>



<script>
	/*  ON/OFF ---------- */
	var onoff = function() {
		if($("input[name='_ssl_pc_img']").filter(function() {if (this.checked) return this;}).val() == "E") {
			$(".auth_view_etc").show();
			$(".auth_view_seal").hide();
		}
		else {
			$(".auth_view_etc").hide();
			$(".auth_view_seal").show();
		}
	}
	$("input[name='_ssl_pc_img']").click(function() {onoff();});
	/*  ON/OFF ---------- */
</script>






<?php

	include_once('wrap.footer.php');

?>