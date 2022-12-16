<?php

	// 2017-07-11 ::: 보안서버 ::: JJC
	include_once('wrap.header.php');


	//	유서트 인증마크 : https://www.ucert.co.kr/trustlogo/CertMark_UCERT.html?sealnum=91a965ae7982d22b&sealid=05a0154b76cfbaf7c7377e5a07cb95f50e261dc26a620fac
	//	인증기관인증마크 - alpha : https://www.ucert.co.kr/trustlogo/CertMark_Alpha.html?sealnum=91a965ae7982d22b&sealid=05a0154b76cfbaf7c7377e5a07cb95f50e261dc26a620fac
	// 인증기관인증마크 - 코모도 : https://www.ucert.co.kr/trustlogo/sseal_comodo.html?sealnum=7c79a8c75b731962
	//	KISA 인증마크 : https://www.ucert.co.kr/trustlogo/CertMark_KISA.html?sealnum=91a965ae7982d22b&sealid=05a0154b76cfbaf7c7377e5a07cb95f50e261dc26a620fac

	// 원데이넷을 통하지 않는 경우도 고려하여야 함..

?>


<form name="frm" method='post' action='_config.ssl.pro.php' ENCTYPE='multipart/form-data'>
<input type='hidden' name='pass_menu' value='_config.ssl.pc_form'>


	<!-- 관리자 보안서버 설정 -->
	<div class="group_title"><strong>PC 사용자 보안서버 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">보안서버 적용페이지</th>
					<td>
						<?=_InputRadio( "_ssl_pc_loc" , array("N" , "A" , "P") ,  (!$siteInfo['s_ssl_pc_loc'] ? "N" : $siteInfo['s_ssl_pc_loc'] ) , "" , array("미사용" , "전체 페이지" , "개인정보 이용 페이지") , "")?>
						<div class="tip_box">
							<?=_DescStr("개인정보 이용 페이지 : 로그인, 회원가입, 게시판, 주문 결제페이지 등이며 , 아래의 PC 개인정보 이용페이지 섹션에 상세히 기술하였습니다.");?>
						</div>
					</td>
				</tr>


				<tr class="auth_view" style="display:none;">
					<th class="ess">보안서버 추가 적용페이지</th>
					<td >

						<a href="#none" onclick="page_add();" class="c_btn h27 black">보안서버 페이지 추가하기</a>

						<div style='clear:both; padding-top:5px;' ID="page_area">

							<?PHP
								$page_ex = explode("§" , $siteInfo['s_ssl_pc_page']);
								echo "<input type='hidden' name='page_add_cnt' value='" . ($siteInfo['s_ssl_pc_page'] ? sizeof($page_ex) : 1) . "'>";
								if(sizeof($page_ex) > 0 ) {
									echo "
										<!-- ● 데이터 리스트 --><table class='table_list'>
											<colgroup><col width='50'><col width='*'><col width='70'></colgroup>
											<thead>
												<tr>
													<th scope='col'>순번</th>
													<th scope='col'>페이지명</th>
													<th scope='col'>괸리</th>
												</tr>
											</thead>
											<tbody>
									";
									foreach( $page_ex as $k=>$v ){
										echo '
											<tr data-key="'. ($k + 1) .'">
												<td >'. ($k + 1) .'</td>
												<td class="t_left"><input type="text" name="page_value['. ($k + 1) .']" class="design" value="'. $v .'" style="width:100%" ></td>
												<td ><div class="lineup-vertical"><a href="#none" onclick="page_delete('. ($k + 1) .');" class="c_btn h22 gray">삭제</a></div></td>
											</tr>
										';
									}
									echo "
											</tbody>
										</table>
									";
								}
							?>
						</div>
						<div class="tip_box">
							<?=_DescStr("보안서버 적용페이지가 미사용이 아닌 경우 보안서버에 추가로 적용하고자 하는 페이지를 설정할 수 있습니다.")?>
							<?=_DescStr("pn 값을 입력하세요. 예를들어 http://도메인/?pn=member.join.agree 일 경우, pn의 값에 해당하는 <strong>member.join.agree</strong>을 입력하시기 바랍니다. ")?>
							<?=_DescStr("삭제 후 확인버튼을 클릭하여야 반영됩니다.")?>
						</div>

						<SCRIPT LANGUAGE="JavaScript">
							// 이미지 추가
							function page_add(){
								var _cnt = $("input[name='page_add_cnt']").val() * 1 + 1;
								var _str = "";
								_str += "<tr data-key='"+ _cnt  +"'>";
								_str += "	<td >"+ _cnt  +"</td>";
								_str += "	<td class='t_left'><input type='text' name='page_value["+ _cnt +"]' class='design' value='' style='width:100%' ></td>";
								_str += "	<td ><div class='lineup-vertical'><a href='#none' onclick='page_delete("+ _cnt +");' class='c_btn h22 gray'>삭제</a></div></td>";
								_str += "</tr>";
								$("#page_area tr:last").after(_str);
								$("input[name='page_add_cnt']").val(_cnt);
							}

							// 삭제 - 마지막 element 삭제
							function page_delete(_idx){
								//if( confirm("정말 삭제하시겠습니까?") ){
									$("#page_area tr[data-key='"+_idx+"']").remove();
									var _cnt = $("input[name='page_add_cnt']").val() * 1 - 1;
									$("input[name='page_add_cnt']").val(_cnt);
								//}
							}

						<?if(sizeof($page_ex) == 0 ) {?>
							page_add();
						<?}?>

						</SCRIPT>

					</td>
				</tr>


				<tr class="auth_view_img" style="display:none;">
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
											Comodo SSL
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
											<img src="https://www.ucert.co.kr/images/maincenterContent/trustlogo/PositiveSSL_tl_trans.gif" style="width:80px; height:68px;" alt="Comodo SSL">
										</td>
										<td ></td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="tip_box"><?=_DescStr("보안서버 인증이미지 설정에 따라 지정된 PC 사용자페이지에 노출됩니다.")?></div>

					</td>
				</tr>

				<tr class="auth_view_etc" style="display:none;">
					<th class="ess">보안서버 기타 인증이미지 소스</th>
					<td >
						<textarea name="_ssl_pc_img_etc" rows="3" cols="" class="design" readonly="" ><?=$siteInfo['s_ssl_pc_img_etc']?></textarea>
						<div class="tip_box">
							<?=_DescStr("보안서버 인증이미지를 기타로 선택할 경우 클릭시 링크를 포함한 인증이미지 소스를 입력하시기 바랍니다.");?>
							<?=_DescStr("소스에 대한 예는 다음과 같습니다.<br><br>&lt;!--KISA Certificate Mark--&gt;<br>&lt;img src=\"https://www.ucert.co.kr/image/trustlogo/s_kisa.gif\" width=\"65\" height=\"63\" align=\"absmiddle\" border=\"0\" style=\"cursor:pointer\" <br>Onclick=javascript:window.open(\"https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum={실넘버}&sealid={실아이디}\",\"mark\",\"scrollbars=no,resizable=no,width=565,height=780\");&gt;<br>&lt;!--KISA Certificate Mark--&gt;");?>
						</div>
					</td>
				</tr>

				<tr class="auth_view_seal" style="display:none;">
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
	<!-- 검색영역 -->




	<?php
		//보안서버 설정 정보
		include_once("_config.ssl.default_inc.php");
	?>



	<!-- 관리자 보안서버 설정 -->
	<div class="group_title"><strong>PC 개인정보 이용페이지</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>PC 개인정보 이용페이지</th>
					<td >
						<?PHP
							if(sizeof($arr_ssl_pc_page) > 0 ) {
								echo "
									<!-- ● 데이터 리스트 --><table class='table_list'>
										<colgroup><col width='400'><col width='*'><col width='70'></colgroup>
										<thead>
											<tr>
												<th scope='col'>페이지명</th>
												<th scope='col'>파일명</th>
											</tr>
										</thead>
										<tbody>
								";
								foreach( $arr_ssl_pc_page as $k=>$v ){
									echo '
										<tr>
											<td style="text-align:left; margin-left:5px;">'. $v .'</td>
											<td style="text-align:left; margin-left:5px;">'. $k .'</td>
										</tr>
									';
								}
								echo "
										</tbody>
									</table>
								";
							}
						?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- 검색영역 -->


	<?php echo _submitBTNsub(); ?>


</form>



<script>
	/*  ON/OFF ---------- */ // 2019-05-07 SSJ :: onoff()함수 수정
	var onoff = function() {
		if($("input[name='_ssl_pc_loc']").filter(function() {if (this.checked) return this;}).val() == "N") {
			$(".auth_view_img").hide();
		}else{
			$(".auth_view_img").show();
		}

		if($("input[name='_ssl_pc_loc']").filter(function() {if (this.checked) return this;}).val() == "P") {
			$(".auth_view").show();
		}
		else {
			$(".auth_view").hide();
			$(".auth_view_etc").hide();
		}

		if(
			$("input[name='_ssl_pc_loc']").filter(function() {if (this.checked) return this;}).val() == "N"
			||
			$("input[name='_ssl_pc_img']").filter(function() {if (this.checked) return this;}).val() == "N"
			||
			$("input[name='_ssl_pc_img']").filter(function() {if (this.checked) return this;}).val() == "E"
		) {
			$(".auth_view_seal").hide();
		}
		else {
			$(".auth_view_seal").show();
		}

		if(
			$("input[name='_ssl_pc_loc']").filter(function() {if (this.checked) return this;}).val() != "N"
			&&
			$("input[name='_ssl_pc_img']").filter(function() {if (this.checked) return this;}).val() == "E"
		) {
			$(".auth_view_etc").show();
		}
		else {
			$(".auth_view_etc").hide();
		}

	}
	onoff();
	$("input[name='_ssl_pc_loc']").click(function() {onoff();});
	$("input[name='_ssl_pc_img']").click(function() {onoff();});
	/*  ON/OFF ---------- */

</script>


<?php

	include_once('wrap.footer.php');

?>