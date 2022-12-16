<?PHP

	if(!$_GET['pt_type']) $_GET['pt_type'] = '상품평가';
	$app_current_link = '_product_talk.list.php?pt_type='.$_GET['pt_type'];
	include_once('wrap.header.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기




?>



	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong><?php echo ($pt_type=='상품평가'?'상품평':'상품문의'); ?> 검색</strong>
	</div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="hidden" name="_mode" value="search">
			<input type="hidden" name="st" value="<?php echo $st; ?>">
			<input type="hidden" name="so" value="<?php echo $so; ?>">
			<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
			<input type="hidden" name="pt_type" value=<?php echo $pt_type; ?>>

			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>상품명</th>
						<td><input type="text" name="pt_pname" class="design" value="<?php echo $pt_pname; ?>" /></td>
						<th>상품코드</th>
						<td><input type="text" name="pt_pcode" class="design" value="<?php echo $pt_pcode; ?>" /></td>
					</tr>
					<tr>
						<th>작성자아이디</th>
						<td><input type="text" name="pt_inid" class="design" value="<?php echo $pt_inid; ?>" /></td>
						<th>본문내용</th>
						<td><input type="text" name="pt_content" class="design" value="<?php echo $pt_content; ?>" /></td>
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


		<table class="table_list">
			<colgroup>
				<col width="80"/><col width="100"/><col width="240"/><col width="150"/>
					<?php if($pt_type == "상품평가"){ ?>
					<col width="100"/>
					<?php } ?>
				<col width="*"/><col width="80"/><col width="80"/><col width="140"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">NO</th>
					<th scope="col" colspan="2">상품정보</th>
					<th scope="col">작성자</th>
					<?php if($pt_type == "상품평가"){ ?>
					<th scope="col">평점</th>
					<?php } ?>
					<th scope="col">본문내용</th>
					<th scope="col">작성일</th>
					<th scope="col">답변</th>
					<th scope="col">비고</th>
				</tr>
			</thead>
			<tbody>

			<?PHP
				// 검색 체크
				$s_query = "from smart_product_talk as pt inner join smart_product as p on (pt.pt_pcode = p.p_code) where pt_depth = '1' ";
				$s_query .= " and pt.pt_type  = '${pt_type}' ";
				if( $pt_uid !="" ) { $s_query .= " and pt.pt_uid = '${pt_uid}' "; }

				if( $_mode == "search" ) {
					if( $pt_pname !="" ) { $s_query_temp .= " and p.p_name like '%${pt_pname}%' "; }
					if( $pt_pcode !="" ) { $s_query_temp .= " and pt.pt_pcode like '%${pt_pcode}%' "; }
					if( $pt_inid !="" ) { $s_query_temp .= " and pt.pt_inid like '%${pt_inid}%' "; }
					if( $pt_inname !="" ) { $s_query_temp .= " and ind.in_name like '%${pt_inname}%' "; }
					if( $pt_content !="" ) { $s_query_temp .= " and pt.pt_content  like '%${pt_content}%' "; }

					$search_query = _MQ_assoc("select  * ".$s_query.$s_query_temp);

					if(count($search_query) > 0){

						$s_pt_uid = array(); // 정보 초기화
						foreach($search_query as $sk => $sv){

							if($sv['pt_depth'] == 1){
								$s_pt_uid[$sk] = $sv['pt_uid'];
							}else{
								$s_pt_uid[$sk] = $sv['pt_relation'];
							}

						}
						$s_query .= "and  find_in_set(pt.pt_uid,'".implode(',',$s_pt_uid)."') > 0";
					}else{
						$s_query  .= $s_query_temp;
					}

				}

				// 데이터 조회
				if(!$listmaxcount) $listmaxcount = 20;
				if(!$listpg) $listpg = 1;
				if(!$st) $st = 'pt_rdate';
				if(!$so) $so = 'desc';
				$count = $listpg * $listmaxcount - $listmaxcount;

				$res = _MQ("select count(*) as cnt ".$s_query);
				$TotalCount = $res['cnt'];
				$Page = ceil($TotalCount/$listmaxcount);

				$que = " select pt.* , p.p_name, p.p_img_list, p.p_cpid {$s_query} order by {$st} {$so} limit $count , $listmaxcount ";
				$res = _MQ_assoc($que);
				foreach($res as $k=>$v){

					$_num = $TotalCount - $count - $k ;

					if($v[pt_intype] == "normal") $in_info = _MQ("select in_name as name from smart_individual where in_id = '".$v['pt_inid']."'");
					if($v[pt_intype] == "admin") $in_info = _MQ("select in_name as name from smart_individual where in_id = '".$v['pt_inid']."'");
					if($v[pt_intype] == "company") $in_info = _MQ("select cp_name as name from smart_company where cp_id = '".$v['pt_inid']."'");

					$_p_img = get_img_src($v['p_img_list']);
					if($_p_img=='') $_p_img = 'images/thumb_no.jpg';

					// 관리자 답변 추출
					$reply_query = _MQ_assoc("select *from smart_product_talk where pt_depth = '2' and pt_relation = '".$v['pt_uid']."' ");

					// 평점 -> 별로 변환
					$eval_str = eval_point_change_star( $v['pt_eval_point'] );
				?>
					<tr>
						<td><?php echo $_num; ?></td>
						<td class="img80"><img src="<?php echo $_p_img; ?>" alt="<?php echo $v['p_name']?>"></td>
						<td class="t_left">
							<span class="block"><?php echo $v['pt_pcode']; ?></span>
							<?php echo stripslashes($v['p_name']); ?>
						</td>
						<td>
							<?php echo showUserInfo($v['pt_inid'],$v['pt_writer'],$v); ?>
						</td>
						<?php if($pt_type == '상품평가'){ ?>
						<td class="t_star"><?php echo $eval_str; ?></td>
						<?php } ?>
						<td class="t_left">
							<!-- <div class="order_item">
								<div class="title" style="width:100%;"><?php echo stripslashes(strip_tags($v['pt_title'])); ?></div>
								<div class="option"><?php echo nl2br(stripslashes(strip_tags($v['pt_content']))); ?></div>
							</div> -->


							<div class="bold">제목 : <?php echo stripslashes(strip_tags($v['pt_title'])); ?></div>
							<div class="normal"><?php echo nl2br(stripslashes(strip_tags($v['pt_content']))); ?></div>
							<br>
							<?php
								//<!-- 관리자답변 (답변없을경우 div전체 숨김) -->
								if(count($reply_query) > 0){
									foreach ( $reply_query as $rpk=>$rpv) {
										$reply_date = date('Y.m.d H:i:s', strtotime($rpv['pt_rdate']));
							?>
										<table class="table_list" style="margin-top:3px;">
											<tr>
												<td class="t_left">
													<strong class="bold">답변</strong> : <?php echo showUserInfo($rpv['pt_inid'],$rpv['pt_writer'],$rpv) ?> , 작성일 : <?php echo $reply_date; ?>
													<div class="dash_line"></div>
													<?php echo nl2br(stripslashes(strip_tags($rpv['pt_content']))); ?>
													<div class="lineup-vertical t_left" style="margin-top:5px;">
														<a href="_product_talk.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pt_type'=>$rpv['pt_type'], 'pt_uid'=>$rpv['pt_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">수정</a>
														<a href="#none" onclick="del('_product_talk.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pt_uid'=>$rpv['pt_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 gray">삭제</a>
													</div>
												</td>
											</tr>
										</table>
							<?php
									}
								}
							?>
						</td>
						<td><?php echo date('Y.m.d H:i:s', strtotime($v['pt_rdate'])); ?></td>
						<!-- 답변상태추가 -->
						<td>
							<div class="lineup-vertical">
							<?php if(count($reply_query) > 0){ ?>
								<span class="c_tag h18 blue">답변완료</span>
							<?php }else{ ?>
								<span class="c_tag gray h18">답변대기</span>
							<?php } ?>
							</div>
						</td>
						<td>
							<div class="lineup-vertical t_left">
								<a href="_product_talk.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', 'pt_type'=>$v['pt_type'], 'pt_uid'=>$v['pt_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">답변</a>
								<a href="_product_talk.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pt_type'=>$v['pt_type'], 'pt_uid'=>$v['pt_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">수정</a>
								<a href="#none" onclick="del('_product_talk.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pt_uid'=>$v['pt_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 gray">삭제</a>
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