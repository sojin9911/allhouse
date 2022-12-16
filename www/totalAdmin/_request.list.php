<?PHP
	if(!$_GET['pass_menu']) $_GET['pass_menu'] = 'inquiry';
	$app_current_link = '_request.list.php?pass_menu='.$_GET['pass_menu'];
	include_once('wrap.header.php');
?>

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong><?php echo ($pass_menu=='inquiry'?'1:1문의':'제휴문의'); ?> 검색</strong>
	</div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="hidden" name="_mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
		<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>회원 아이디</th>
						<td><input type="text" name="pass_id" class="design"  value="<?php echo $pass_id; ?>" /></td>
						<th>답변상태</th>
						<td><?php echo _InputSelect( "pass_status" , array('답변대기','답변완료'), $pass_status , "  " ,  "" , '-선택-'); ?></td>
					</tr>
					<tr>
						<th>제목</th>
						<td colspan="3"><input type="text" name="pass_title" class="design"  value="<?php echo $pass_title; ?>" style="width:500px" /></td>
					</tr>
					<tr>
						<td colspan="4">
							<div class="tip_box">
								<?php echo _DescStr("회원에게 답변 또는 해당글 수정을 할 시 처리 항목의 <em>관리</em> 버튼을 누르시면 됩니다."); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>



			<div class="c_btnbox">
				<ul>
					<li>
						<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
					</li>
					<?php if($_mode == 'search') { ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

		</form>
	</div>
	<!-- // 검색영역 -->


	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<!-- 리스트 제어버튼영역
		<div class="top_btn_area">
			<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택</a></span>
			<span class="shop_btn_pack"><span class="blank_3"></span></span>
			<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택해제</a></span>
		</div>
		<!-- // 리스트 제어버튼영역 -->


		<table class="table_list">
			<colgroup>
				<col width="70"/>
				<?php if( in_array($pass_menu , array("inquiry")) ){ ?>
				<col width="135"/>
				<?php } ?>
				<?php if( in_array($pass_menu , array("partner")) ){ ?>
				<col width="135"/><col width="120"/><col width="200"/>
				<?php } ?>
				<col width="*"/><col width="100"/><col width="90"/><col width="100"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">번호</th>
					<?php if( in_array($pass_menu , array("inquiry")) ){ ?>
					<th scope="col">회원 아이디</th>
					<?php } ?>
					<?php if( in_array($pass_menu , array("partner")) ){ ?>
					<th scope="col">이름/상호명</th>
					<th scope="col">연락처</th>
					<th scope="col">이메일</th>
					<?php } ?>
					<th scope="col">문의제목</th>
					<th scope="col">답변상태</th>
					<th scope="col">등록일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
			<?php
				// 검색 체크
				$s_query = " from smart_request where r_menu='{$pass_menu}' ";
				if( $_mode == "search" ) {
					if( $pass_title !="" ) { $s_query .= " and r_title like '%{$pass_title}%' "; }
					if( $pass_status !="" ) { $s_query .= " and r_status='{$pass_status}' "; }
					if( $pass_id !="" ) { $s_query .= " and r_inid like '%{$pass_id}%' "; }
				}


				$listmaxcount = 30 ;
				if( !$listpg ) {$listpg = 1 ;}
				$count = $listpg * $listmaxcount - $listmaxcount;


				$res = _MQ(" select count(*) as cnt  $s_query ");
				$TotalCount = $res[cnt];
				$Page = ceil($TotalCount / $listmaxcount);

				$res = _MQ_assoc(" select * {$s_query} ORDER BY r_rdate desc limit $count , $listmaxcount ");
				foreach($res as $k=>$row){

					$_mod = "<input type=button value='수정' class=btn onclick='location.href=(\"\");'>";
					$_del = "<input type=button value='삭제' class=btn onclick='del(\"_request.pro.php?pass_menu={$pass_menu}&_mode=delete&_uid=$row[r_uid]&_PVSC=${_PVSC}\");'>";

					$_num = $TotalCount - $count - $k ;

			?>
					<tr>
						<td><?php echo $_num; ?></td>
						<?php if( in_array($pass_menu , array("inquiry")) ){ ?>
						<td><?php echo showUserInfo($row['r_inid']); ?></td>
						<?php } ?>
						<?php if( in_array($pass_menu , array("partner")) ){ ?>
						<td><?php echo $row['r_comname']; ?></td>
						<td><?php echo $row['r_hp']; ?></td>
						<td><?php echo $row['r_email']; ?></td>
						<?php } ?>
						<td class="t_left t_black"><?php echo strip_tags($row['r_title']); ?></td>
						<td>
							<div class="lineup-vertical">
							<?php if($row['r_status'] == "답변대기") { ?>
								<span class="c_tag gray h18">답변대기</span>
							<?php }else{ ?>
								<span class="c_tag h18 blue">답변완료</span>
							<?php } ?>
							</div>
						</td>
						<td><?php echo substr($row['r_rdate'],0,10); ?></td>
						<td>
							<div class="lineup-vertical t_left">
								<a href="_request.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pass_menu'=>$pass_menu, '_uid'=>$row['r_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">관리</a>
								<a href="#none" onclick="del('_request.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pass_menu'=>$pass_menu, '_uid'=>$row['r_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 gray">삭제</a>
							</div>
						</td>
					</tr>
			<?php } ?>
			</tbody>
		</table>

		<?php if(count($res) <= 0) { ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>

	</div>

<?PHP
	include_once('wrap.footer.php');
?>