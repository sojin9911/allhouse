<?php include_once('wrap.header.php'); ?>
<form action="_config.device.pro.php" method="post" enctype="multipart/form-data">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>디바이스 선택</th>
					<td>
						<?php echo _InputRadio('s_device_mode', array('A', 'P', 'M'), ($siteInfo['s_device_mode']?$siteInfo['s_device_mode']:'A'), '', array('모두사용', 'PC만 사용', ' Mobile만 사용'), ''); ?>
					</td>
				</tr>

				<?php // -- URL 공유 파비콘 추가 -- 2019-05-23 LCY ?>
				<tr>
					<th>URL 공유 파비콘</th>
					<td>
						<?php echo _PhotoForm('../upfiles/banner', 's_share_favicon', $siteInfo['s_share_favicon'], 'style="width:250px"'); ?>
						<div class="tip_box">
							<?php echo _DescStr("권장사이즈 : 300 x 300 (pixel)"); ?>
							<?php echo _DescStr("권장포맷 : png"); ?>
						</div>
					</td>
				</tr>
				<?php // -- URL 공유 파비콘 추가 -- 2019-05-23 LCY ?>

				<tr>
					<th>PC 파비콘 설정</th>
					<td>
						<?php echo _PhotoForm('../upfiles/banner', 's_favicon', $siteInfo['s_favicon'], 'style="width:250px"'); ?>
						<div class="tip_box">
							<?php echo _DescStr("권장사이즈 : 300 x 300 (pixel)"); ?>
							<?php echo _DescStr("권장포맷 : png"); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>Mobile 홈 아이콘</th>
					<td>
						<?php echo _PhotoForm('../upfiles/banner', 's_home_icon', $siteInfo['s_home_icon'], 'style="width:250px"'); ?>
						<div class="tip_box">
							<?php echo _DescStr("권장사이즈 : 300 x 300 (pixel)"); ?>
							<?php echo _DescStr("권장포맷 : png"); ?>
						</div>
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