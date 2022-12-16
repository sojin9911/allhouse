<?php

	include_once('wrap.header.php');
	$r = _MQ("select * from smart_cntlog_config where clc_uid = 1 ");

?>
<form action="_config.cntlog.pro.php" method="post">
<input type="hidden" name="_mode" value="config">
<input type="hidden" name="Now_Connect_Use" value="N">
<input type="hidden" name="Now_Connect_Term" value="30">

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>카운터 사용여부</th>
					<td >
						<?php echo _InputRadio('_counter_use', array('Y', 'N'), ($r['clc_counter_use'] == 'Y'?'Y':'N'), '', array('사용', '미사용'), ''); ?>
					</td>
					<th>전체 방문자수</th>
					<td>
						<input type="text" name="_total_num" class="design " value="<?php echo $r['clc_total_num']; ?>" style="width:100px">
						<span class="fr_tx">명</span>
					</td>
				</tr>

				<tr>
					<th>중복 접속설정</th>
					<td >
						<?php echo _InputRadio('_cookie_use', array('A', 'T', 'O'), ($r['clc_cookie_use']?$r['clc_cookie_use']:'O'), '', array('접속하는대로 증가' , '지정된 시간대로 증가' , '하루에 한번 증가'), ''); ?>
					</td>
					<th>중복접속 시간설정</th>
					<td>
						<input type="text" name="_cookie_term" class="design t_center" value="<?php echo $r['clc_cookie_term']; ?>" style="width:50px">
						<span class="fr_tx">초</span>
					</td>
				</tr>

				<tr>
					<th>관리자 통계포함</th>
					<td>
						<?php echo _InputRadio('_admin_check_use', array('Y', 'N'), ($r['clc_admin_check_use'] == 'Y'?'Y':'N'), '', array('포함', '미포함'), ''); ?>
					</td>
					<th>관리자접속 IP</th>
					<td>
						<input type="text" name="_admin_ip" class="design" value="<?php echo $r['clc_admin_ip']; ?>" style="width:120px">
						<?php echo _DescStr('현재 아이피 : '.$_SERVER['REMOTE_ADDR']); ?>
					</td>
				</tr>
				<tr>
					<th>접속자료 초기화</th>
					<td colspan="3">
						<a href="_config.cntlog.pro.php?_mode=all" onclick="if(!confirm('통계 자료에 대해 정말 초기화 하시겠습니까?')) return false;" class="c_btn h28 gray">전체 접속자료 초기화</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<!-- 저장 -->
	<div class="c_btnbox">
		<ul>
			<li><span class="c_btn h46 red"><input type="submit" value="확인" /></span></li>
		</ul>
	</div>
	<!-- 저장 -->
</form>

<?php include_once('wrap.footer.php'); ?>