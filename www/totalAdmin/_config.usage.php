<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.usage.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>이용안내(PC)</th>
					<td>
						<textarea name="s_information_use_pc" rows="4" class="design SEditor" style="width:100%; height:500px"><?php echo $r['s_information_use_pc']; ?></textarea>
						<div class="tip_box">
							<?php echo _DescStr('치환자:
								<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
								<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
							'); ?>
							<?php echo _DescStr('적림급 설정 설정은 <a href="./_config.point.form.php"><u>회원관리 - 적립금 관리 - 적림급 설정</u></a>에서 수정 할 수 있습니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>이용안내(Mobile)</th>
					<td>
						<textarea name="s_information_use_mobile" rows="4" class="design SEditor" style="width:100%; height:500px"><?php echo $r['s_information_use_mobile']; ?></textarea>
						<div class="tip_box">
							<?php echo _DescStr('치환자: 
								<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
								<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
								<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
								<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
							'); ?>
							<?php echo _DescStr('적림급 설정 설정은 <a href="./_config.point.form.php"><u>회원관리 - 적림급 설정 관리</u></a>에서 수정 할 수 있습니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="group_title"><strong>회원탈퇴 주의사항</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>회원탈퇴 주의사항</th>
					<td>
						<textarea name="s_leave_guidance" rows="6" class="design"><?php echo $r['s_leave_guidance']; ?></textarea>
						<div class="tip_box">
							<?php echo _DescStr('회원탈퇴 주의사항은 엔터로 구분됩니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<?php echo _submitBTNsub(); ?>

</form>



<script type="text/javascript">
	$(document).delegate('.js_insert_text', 'click', function(e) {
		e.preventDefault();
		var mode = 'textarea';
		var txt = $(this).text();
		var edit = $(this).closest('td').find('textarea');
		if(oEditors.length > 0) {
			$.each(oEditors, function(k, v) {
				if(edit[0] == v.elPlaceHolder) {
					mode = 'edit';
					edit = oEditors[k];
				}
			});
		}
		if(mode == 'textarea') {
			edit.insertAtCaret(txt);
		}
		else {
			if(edit.getEditingMode() != 'WYSIWYG') return alert('에디터모드에서만 사용가능 합니다.\n수동으로 추가하여 주세요');
			edit.exec("PASTE_HTML", [txt]);
		}
	});
</script>
<?php include_once('wrap.footer.php'); ?>