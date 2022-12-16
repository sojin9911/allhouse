<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_mailing_data.list.php';
include_once('wrap.header.php');


	if( !$_mduid ) {
		error_msg("잘못된 접근입니다.");
	}

	$row = _MQ("select * from smart_mailing_data where md_uid='${_mduid}' ");

	// 저장한 정보 불러오기 --> $app_profile 로 저장됨
	include_once("..".IMG_DIR_NORMAL."/mailing.profile.php");
	$ex_app_profile = array_filter(array_unique(explode("," , $app_profile)));
	$_cnt = sizeof($ex_app_profile);

?>



	<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_mailing_profile.pro.php">
	<input type=hidden name="_mode" value="<?php echo $_mode; ?>">
	<input type=hidden name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type=hidden name="_cnt" value="<?php echo $_cnt; ?>" hname="메일링 적용된 회원" required>
	<input type=hidden name="_mduid" value="<?php echo $_mduid; ?>">

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>메일링 회원 적용</strong></div>

		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>메일링 회원</th>
						<td>
							<span class="fr_tx">현재 메일링 적용된 회원 총 <font id="app_cnt"><?php echo number_format($_cnt); ?></font>명</span>
							<?php echo _DescStr('기존에 등록한 메일링 회원과 다르게 보낼 경우, 전체 삭제하고 재설정해주세요.'); ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="#none" onclick="window.open('_mailing_profile.individual_list.php', 'individual_popup', 'width=1120,height=800,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes');" class="c_btn h27 black">개인회원 검색</a>
							<a href="#none" onclick="del('_mailing_profile.pro.php?_mduid=<?php echo $_mduid; ?>&_mode=profile_delete&_PVSC=<?php echo $_PVSC; ?>');" class="c_btn h27">선택회원 전체 삭제</a>
						</td>
					</tr>
				</tbody>
			</table>

			<?php echo _submitBTN($app_current_link,null,'',false); ?>

		</div>


	</form>



	<!-- 리스트영역 -->
	<div class="data_list">

		<table class="table_list">
			<colgroup>
				<col width="40"><col width="120"><col width="*"><col width="200"><col width="200"><col width="90">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">NO</th>
					<th scope="col">기록일</th>
					<th scope="col">발송이메일</th>
					<th scope="col">발송상태</th>
					<th scope="col">발송일자</th>
					<th scope="col">삭제</th>
				</tr>
			</thead>
			<tbody>
			<?PHP
				$mpres = _MQ_assoc(" select  * from smart_mailing_profile where mp_mduid='{$_mduid}' ORDER BY mp_uid desc ");
				if(sizeof($mpres) < 1) echo "<tr><td colspan=6 height=45>발송 내역이 없습니다.</td></tr>";
				foreach($mpres as $k=>$mpr){

					$_num = sizeof($mpres) - $k ;

					echo "
						<tr>
							<td>". $_num ."</td>
							<td>" . substr($mpr['mp_rdate'],0,10) . "</td>
							<td>" . str_replace(","," , ",cutstr_new($mpr['mp_email'],100 , "...")). "</td>
							<td>
								<div class='lineup-vertical'>
									" . ($mpr['mp_status']=="Y" ?
											"<span class='c_tag red h18 line'>발송완료</span>"
											:
											"<a href='#none' onclick='send_mailing(".$mpr['mp_uid'].")' class='c_btn h22 blue'>발송하기</a>"
									) . "
								</div>
							</td>
							<td><div class='lineup-vertical'>" . ($mpr['mp_status']=="Y" ? $mpr['mp_sdate'] : '<span class="c_tag h18 black line">발송대기</span>') . "</div></td>
							<td>
								<div class='lineup-vertical'>
									" . ($mpr['mp_status']<>"Y" ?
											'<a href="#none" onclick="del(\'_mailing_profile.pro.php'. URI_Rebuild('?', array('_mode'=>'delete', '_mduid'=>$_mduid, '_mpuid'=>$mpr['mp_uid'], '_PVSC'=>$_PVSC)) .'\')" class="c_btn h22 gray">삭제</a>'
											:
											'<span class="c_tag h18 gray line">삭제불가</span>'
									) . "
								</div>
							</td>
						</tr>
					";
				}
			?>
			</tbody>
		</table>

	</div>


	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>메일링 내용</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>메일링 제목</th>
					<td>
						<?php echo $row['md_title']; ?>
					</td>
				</tr>
				<tr>
					<th style="vertical-align:top">메일링 제목</th>
					<td>
						<?php echo stripslashes($row['md_content']); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<script>
	// 메일발송하기
	function send_mailing(uid) {

		if(!confirm("발송하시겠습니까?")) return false;

		common_frame.location.href="_mailing_profile.pro.php?_mode=sendpro&_mduid=<?php echo $_mduid; ?>&_uid="+uid;

	}
</script>

<?php include_once('wrap.footer.php'); ?>