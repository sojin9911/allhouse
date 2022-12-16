<?
	// 2017-07-11 ::: 보안서버 ::: JJC
?>



	<!-- 관리자 보안서버 설정 -->
	<div class="group_title"><strong>보안서버 설정 정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>

				<tr>
					<th>보안서버 사용여부</th>
					<td ><?=($siteInfo['s_ssl_check'] == 'Y' ? '사용' : '미사용')?></td>
					<th>보안서버 진행상태</th>
					<td ><?=$siteInfo['s_ssl_status']?></td>
				</tr>

				<tr>
					<th>보안서버 사용기간</th>
					<td ><?=$siteInfo['s_ssl_sdate']?> ~ <?=$siteInfo['s_ssl_edate']?></td>
					<th>보안서버 도메인</th>
					<td >
						<?php
							$ssl_domain = "https://" . $siteInfo['s_ssl_domain'] . ($siteInfo['s_ssl_port'] ? ":" . $siteInfo['s_ssl_port'] : "");
							echo "<a href='". $ssl_domain ."' target='_blank'>". $ssl_domain ."</a>";
						?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- 검색영역 -->
