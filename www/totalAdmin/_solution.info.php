<?php
include_once('wrap.header.php');

# 출력 데이터 설정
$DiskInfo = CheckDirSize('/'); // 디스크 용량과 파일 수를 구함
?>

<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<th>솔루션</th>
				<td>하이센스 3.0</td>
				<th rowspan="2">솔루션 사용량</th>
				<td rowspan="2">
					<!-- ● 데이터 리스트 (내부정보 나열 if_insum) -->
					<table class="table_form if_insum">
						<thead>
							<tr>
								<th>디스크 용량</th>
								<th>디스크 파일 수</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo SizeText($DiskInfo['DiskSize']); ?></td>
								<td><?php echo number_format($DiskInfo['DiskFile']); ?></td>
							</tr>
						</tbody> 
					</table>
					<!-- <div class="tip_box">
						<?php echo _DescStr('디스크 용량 : 솔루션의 용량을 표기합니다.'); ?>
						<?php echo _DescStr('디스크 파일 : 솔루션의 파일수를 표기합니다.'); ?>
					</div> -->
				</td>
			</tr>
			<tr>
				<th>라이선스</th>
				<td><?php echo $siteInfo['s_license']; ?></td>
			</tr>
			<tr>
				<th>상품 수</th>
				<td>
					<!-- ● 데이터 리스트 (내부정보 나열 if_insum) -->
					<table class="table_form if_insum">
						<thead>
							<tr>
								<th>전체</th>
								<th>노출</th>
								<th>숨김</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong><?php echo number_format(DivisionProduct('all')); ?></strong></td>
								<td><?php echo number_format(DivisionProduct('view')); ?></td>
								<td><?php echo number_format(DivisionProduct('hide')); ?></td>
							</tr>
						</tbody> 
					</table>				
				</td>
				<th>회원 수</th>
				<td>
					<!-- ● 데이터 리스트 (내부정보 나열 if_insum) -->
					<table class="table_form if_insum">
						<thead>
							<tr>
								<th>전체</th>
								<th>정상</th>
								<th>휴면</th>
								<th>탈퇴</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong><?php echo number_format(DivisionMember('all')); ?></strong></td>
								<td><?php echo number_format(DivisionMember('use')); ?></td>
								<td><?php echo number_format(DivisionMember('sleep')); ?></td>
								<td><?php echo number_format(DivisionMember('leave')); ?></td>
							</tr>
						</tbody> 
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php include_once('wrap.footer.php'); ?>