<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form class="defaut_form" method="post" action="_config.default.pro.php" onsubmit="return validate_check();">
	<!-- 관리자 정보설정 -->
	<div class="group_title"><strong>관리자 정보설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">관리자 아이디</th>
					<td>
						<input type="text" name="_adid" class="design" value="<?php echo $r['s_adid']; ?>" style="width:185px" required>
					</td>
					<th>관리자 비밀번호</th>
					<td>
						<label class="design"><input type="checkbox" name="_change_apw" class="js_change_apw" value="Y"> 변경</label>
						<div class="js_change_apw_box" style="display: none;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<span class="fr_tx">비밀번호 변경 :</span> <input type="password" name="_adpwd" class="design js_pw_input" value="" style="width:120px">
							<div class="clear_both"></div>
							<span class="fr_tx">비밀번호 확인 :</span> <input type="password" name="_adpwd_ck" class="design js_pw_ckinput" value="" style="width:120px">
							<?php echo _DescStr('6자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.', 'black'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="ess">대표번호</th>
					<td>
						<input type="text" name="_glbtel" class="design" value="<?php echo $r['s_glbtel']; ?>" style="width:185px" required>
						<?php echo _DescStr('SMS 서비스 이용 시 발신번호로 사용됩니다.'); ?>
					</td>
					<th class="ess">관리자 휴대폰</th>
					<td>
						<input type="text" name="_glbmanagerhp" class="design" value="<?php echo $r['s_glbmanagerhp']; ?>" style="width:185px" required>
						<?php echo _DescStr('문의/신고/정산등의 요청을 받으실 휴대폰 번호입니다.'); ?>
					</td>
				</tr>
				<tr>
					<th class="ess">대표 이메일</th>
					<td colspan="3">
						<input type="text" name="_ademail" value="<?php echo $r['s_ademail']; ?>" class="design" style="width:185px" required>
						<?php echo _DescStr('홈페이지 대표 이메일로 사용됩니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 관리자 정보설정 -->



	<!-- 사이트 기본설정 -->
	<div class="group_title"><strong>사이트 기본설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>사이트명</th>
					<td>
						<input type="text" name="_adshop" class="design" value="<?php echo $r['s_adshop']; ?>" style="width:185px">
					</td>
					<!-- 2019-11-23 SSJ :: 대표도메인 설정 추가 -->
					<th>대표도메인</th>
					<td>
						<input type="text" name="_ssl_domain" class="design" value="<?php echo $r['s_ssl_domain']; ?>" style="width:185px">
						<div class="tip_box">
							<?php echo _DescStr("대표도메인은 쇼핑몰을 대표하는 하나의 도메인(주소)를 말합니다."); ?>
							<?php echo _DescStr("대표도메인 설정 시 구글, 네이버와 같은 포털 사이트에서 검색 시 유리하게 적용됩니다."); ?>
							<?php echo _DescStr("대표도메인 설정 시 http(s):// 를 제외한 도메인만 입력해 주시기 바랍니다."); ?>
						</div>
					</td>
					<!-- 2019-11-23 SSJ :: 대표도메인 설정 추가 -->
				</tr>
				<tr>
					<th>회사명</th>
					<td>
						<input type="text" name="_company_name" class="design" value="<?php echo $r['s_company_name']; ?>" style="width:185px">
					</td>
					<th>대표자명</th>
					<td>
						<input type="text" name="_ceo_name" class="design" value="<?php echo $r['s_ceo_name']; ?>" style="width:185px">
					</td>
				</tr>
				<tr>
					<th>사업자등록번호</th>
					<td>
						<input type="text" name="_company_num" class="design" value="<?php echo $r['s_company_num']; ?>" style="width:185px">
						<!-- <?php echo _DescStr('체크 시 입력된 정보가 하단에 노출됩니다.'); ?> -->
					</td>
					<th>통신판매신고번호</th>
					<td>
						<input type="text" name="_company_snum" class="design" value="<?php echo $r['s_company_snum']; ?>" style="width:185px">
						<!-- <?php echo _DescStr('문의/신고/정산 요청에 대한 수신번호로 사용됩니다.'); ?> -->
					</td>
				</tr>
				<tr>
					<th>업태</th>
					<td>
						<input type="text" name="_item1" class="design" value="<?php echo $r['s_item1']; ?>" style="width:185px">
					</td>
					<th>종목</th>
					<td>
						<input type="text" name="_item2" class="design" value="<?php echo $r['s_item2']; ?>" style="width:185px">
					</td>
				</tr>
				<tr>
					<th>주소</th>
					<td>
						<div class="lineup-full">
							<input type="text" name="_company_addr" class="design" value="<?php echo $r['s_company_addr']; ?>">
						</div>
					</td>
					<th>팩스번호</th>
					<td>
						<input type="text" name="_fax" class="design" value="<?php echo $r['s_fax']; ?>" style="width:185px">
					</td>
				</tr>
				<tr>
					<th>사업자 정보 노출</th>
					<td>
						<label class="design"><input type="checkbox" name="_view_network_company_info" value="Y"<?php echo ($r['s_view_network_company_info'] == 'Y'?' checked="checked"':null); ?>> 노출</label>
						<?php echo _DescStr('체크 시 입력된 정보가 하단에 노출됩니다.'); ?>
					</td>
					<th>개인정보관리책임자</th>
					<td>
						<input type="text" name="_privacy_name" class="design" value="<?php echo $r['s_privacy_name']; ?>" style="width:185px">
					</td>
				</tr>
				<tr>
					<th>고객센터 운영시간</th>
					<td>
						<textarea name="_cs_info" rows="4" class="design"><?php echo stripslashes($r['s_cs_info']); ?></textarea>
						<?php echo _DescStr('고객센터 운영시간을 입력해 주세요.'); ?>
					</td>
					<th>관리자 로그인 페이지</th>
					<td>
						<span class="fr_tx">고객센터 전화번호 :</span> <input type="text" name="_login_page_phone" class="design" value="<?php echo $r['s_login_page_phone']; ?>" style="width:185px">
						<div class="clear_both"></div>
						<span class="fr_tx">관리자 이메일주소 :</span> <input type="text" name="_login_page_email" class="design" value="<?php echo $r['s_login_page_email']; ?>" style="width:185px">
						<div class="clear_both"></div>
						<?php echo _DescStr('관리자모드 로그인 페이지에 표시할 정보입니다.'); ?>
					</td>
				</tr>
				<?php // LCY :: 2017-12-09 -- 휴면계정전환 개월 수 이동 ==> 회원관리 > 휴면회원정책 ?>
				<tr>
					<th>로그인 시도횟수</th>
					<td colspan="3">
						<input type="text" name="member_login_cnt" class="design t_center" value="<?php echo $r['member_login_cnt']; ?>" style="width:50px"><span class="fr_tx">회</span>
						<div class="tip_box">
							<?php echo _DescStr('로그인 시도횟수 설정을 "0"으로 설정 시 해당 기능은 작동되지 않습니다.'); ?>
							<?php echo _DescStr('로그인 기록은 사용자페이지 “마이페이지 > 로그인기록” 페이지에서 확인가능합니다.'); ?>
							<?php echo _DescStr('로그인 시도 시 여기서 지정된 횟수 이상 틀릴 시에만 해당 아이피 정보를 기록하게 됩니다.'); ?>
						</div>
					</td>
				</tr>
				<?php // ==== 비회원 구매 설정 추가 통합 kms 2019-06-20 ==== ?>
				<tr>
					<th>비회원구매</th>
					<td colspan="3">
						<?php echo _InputRadio('_none_member_buy', array('Y', 'N'), ($r['s_none_member_buy'] == 'Y'?'Y':'N'), '', array('적용', '미적용'), ''); ?>
						<!-- 2020-03-25 SSJ :: 비회원 바로구매 시 로그인 페이지 경유 설정 추가 -->
						<div class="js_none_member_buy_use" style="<?php echo ($r['s_none_member_buy'] <> 'Y' ? 'display:none;' : null); ?>">
							<div class="dash_line"></div>
							<?php echo _InputRadio('_none_member_login_skip', array('N', 'Y'), ($r['s_none_member_login_skip'] == 'Y'?'Y':'N'), '', array('비회원 바로구매 시 로그인 페이지 경유', '비회원 바로구매 시 주문/결제 바로가기'), ''); ?>
							<script>
								$(document).ready(function(){
									$('input[name=_none_member_buy]').on('click', function(){
										var _v = $(this).val();
										if(_v == 'Y') $('.js_none_member_buy_use').show();
										else  $('.js_none_member_buy_use').hide();
									});
								});
							</script>
						</div>
					</td>
				</tr>
				<?php // ==== 비회원 구매 설정 추가 통합 kms 2019-06-20 ==== ?>
			</tbody>
		</table>
	</div>
	<!-- 사이트 기본설정 -->



	<!-- 다음, 네이버 EP연동 -->
	<div class="group_title"><strong>다음, 네이버 EP연동</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>네이버 EP</th>
					<td>
						<?php echo _InputRadio('_naver_switch', array('Y', 'N'), ($r['s_naver_switch'] == 'Y'?'Y':'N'), '', array('전체 적용', '전체 미적용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('전체 상품에 대한 <em>네이버 EP 노출여부를 설정</em>할 수 있습니다.'); ?>
							<?php echo _DescStr("네이버 EP 노출은 전체설정(환경설정 > 기본설정 > 쇼핑몰 기본정보 > 네이버 EP), 상품개별설정에서 모두 적용되어야 노출됩니다.")?>
							<?php echo _DescStr("전체상품 DB URL : <em>http://". $system['host'] ."/addons/ep/naver/allep.php</em>")?>
							<?php echo _DescStr("요약EP는 3.0 버전에서는 더이상 사용하지 않습니다.")?>
						</div>
					</td>
				</tr>
				<tr>
					<th>다음 EP</th>
					<td>
						<?php echo _InputRadio('_daum_switch', array('Y', 'N'), ($r['s_daum_switch'] == 'Y'?'Y':'N'), '', array('전체 적용', '전체 미적용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('전체 상품에 대한 <em>다음 하우쇼핑 노출여부를 설정</em>할 수 있습니다.'); ?>
							<?php echo _DescStr("다음 EP 노출은 전체설정(환경설정 > 기본설정 > 쇼핑몰 기본정보 > 다음 EP), 상품개별설정에서 모두 적용되어야 노출됩니다.")?>
							<?php echo _DescStr('전체상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/allep.php</em>'); ?>
							<?php echo _DescStr('요약상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/briefep.php</em>'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 다음, 네이버 EP연동 -->



	<!-- 사이트 메타테그 설정 -->
	<div class="group_title"><strong>사이트 메타태그 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>Title</th>
					<td>
						<div class="lineup-full">
							<input type="text" name="_glbtlt" class="design" value="<?php echo $r['s_glbtlt']; ?>">
						</div>
					</td>
				</tr>
				<tr>
					<th>Description</th>
					<td>
						<textarea name="_glbdsc" rows="4" class="design"><?php echo $r['s_glbdsc']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>Keywords</th>
					<td>
						<textarea name="_glbkwd" rows="4" class="design"><?php echo $r['s_glbkwd']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>Meta Tag</th>
					<td>
						<textarea name="_gmeta" rows="4" class="design"><?php echo $r['s_gmeta']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?php echo _DescStr('네이버 웹마스터도구, 구글마스터 도구 등 메타태그를 사용하실 경우 이용하시기 바랍니다.'); ?>
							<?php echo _DescStr('메타태그 및 자바스크립트 이외 삽입 시 오류를 발생 시킬 수 있습니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 사이트 메타테그 설정 -->


	<?php echo _submitBTNsub(); ?>
</form>



<script type="text/javascript">
	// 비밀번호 변경 체크 동작
	$(document).delegate('.js_change_apw', 'change click', function(e) {
		var is_checked = $(this).is(':checked');
		if(is_checked === true) {
			$('.js_change_apw_box').find('input').val('');
			$('.js_change_apw_box').show();
			$('.js_change_apw_box').find('input').eq(0).focus();
		}
		else {
			$('.js_change_apw_box').find('input').val('');
			$('.js_change_apw_box').hide();
		}
	});

	// 등록 검증
	function validate_check() {

		// 비밀번호 변경 체크
		var pw_change = $('.js_change_apw').is(':checked');
		var pw_length = 6; // 최소 비밀번호 글자수
		if(pw_change === true) {
			var pw = $('.js_pw_input').val();
			var pw_ck = $('.js_pw_ckinput').val();
			if(!pw || !pw_ck) { // 변경 비밀번호 입력 체크
				alert('비밀번호를 입력해 주세요.');
				if(!pw) $('.js_pw_input').focus();
				else $('.js_pw_ckinput').focus();
				return false;
			}
			if(pw.length < pw_length || pw_ck.length < pw_length) { // 6자리 비밀번호 확인
				alert(pw_length+'자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.');
				if(pw.length < pw_length) $('.js_pw_input').focus();
				else $('.js_pw_ckinput').focus();
				return false;
			}
			if(pw != pw_ck) { // 비밀번호 일치성 확인
				alert('비밀번호와 비밀번호확인이 일치하지 않습니다.');
				$('.js_pw_ckinput').focus();
				return false;
			}
		}
	}
</script>
<?php include_once('wrap.footer.php'); ?>