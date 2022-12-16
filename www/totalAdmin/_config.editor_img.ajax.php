<?php

	# KAY :: 에디터 이미지 관리 :: 파일 생성
	// 에디터 이미지 사용관리 레이어 팝업 ajax
	include_once("inc.php");

	if($_mode == 'edtimg_pop'){
		// 에디터 이미지 사용관리DB, 파일관리DB 조인 후 uid 일치 값만 추출
		$res = _MQ_assoc("
			SELECT 
				eiu.eiu_uid, eiu_datauid, eiu.eiu_tablename, eif.eif_img, eif.eif_rdate, eif.eif_uid
			FROM smart_editor_images_files as eif
			LEFT JOIN smart_editor_images_use as eiu on (eiu.eiu_eifuid = eif.eif_uid) 
			where 
				eif.eif_uid =  '". $uid ."'
		");
	}

	// 에디터 이미지 사용 타이틀을 위한 배열
	// 다른곳 like 배열과 있는 배열과 다름
	$arr_ei =	array(	
		"product"=>array("table"=>"smart_product","content"=>"p_content","content_m"=>"p_content_m","uid"=>"p_code","title"=>"p_name"),
		"board"=>array("table"=>"smart_bbs","content"=>"b_content","uid"=>"b_uid","title"=>"b_title"),
		"board_template"=>array("table"=>"smart_bbs_template","content"=>"bt_content","uid"=>"bt_uid","title"=>"bt_title"),
		"board_faq"=>array("table"=>"smart_bbs_faq","content"=>"bf_content","uid"=>"bf_uid","title"=>"bf_title"),
		"popup"=>array("table"=>"smart_popup","content"=>"p_content","uid"=>"p_uid","title"=>"p_title"),	
		// 프로모션 content 내용은 다른테이블에 존재.
		"promotion"=>array("table"=>"smart_promotion_plan","uid"=>"pp_uid","title"=>"pp_title"),
		"mailing"=>array("table"=>"smart_mailing_data","content"=>"md_content","uid"=>"md_uid","title"=>"md_title"),
		"normal"=>array("table"=>"smart_normal_page","content"=>"np_content","content_m"=>"np_content_m","uid"=>"np_uid","title"=>"np_title"),
		"setting"=>array("table"=>"smart_product_guide","content"=>"g_content","uid"=>"g_uid","title"=>"g_title")
	);

?>
		<div class="pop_title">에디터 이미지 전체 사용관리<a href="#none" onclick="return false;" class="close btn_close " title="닫기"></a></div>
		<div class="tip_box " style="margin-bottom:20px">
			<div class="c_tip black">기존이미지 패치 시 구분이 없는 경우 이미지 바로가기가 생성되지 않을 수 있습니다.</div>
			<div class="c_tip black">생성되지 않은 바로가기는 다음날 이미지 구분 지정 후 생성됩니다. </div>
		</div>
			<!-- ● 데이터 리스트 -->
			<div class="data_list" style="overflow:auto; height:300px;">
				<table class="table_list ">
					<colgroup>
						<col width="60"><col width="100"><col width="*"><col width="150">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">NO</th>
							<th scope="col">구분</th>
							<th scope="col">이미지 사용처 타이틀</th>
							<th scope="col">이미지변경</th>
						</tr>
					</thead>

					<tbody>
						<?php
							$_num = 0;

							// $res = 에디터 이미지 정보
							 if(sizeof($res) > 0){
								foreach($res as $k=>$r){

									// 바로가기 링크 설정 및 구분명 추출
									$edit_img_info = editor_img_info($r['eiu_tablename'],$r['eiu_datauid']);

									//에디터 구분명 한글
									$ei_use_name = $edit_img_info['name'];

									// 바로가기 버튼
									$ei_use_link = ($edit_img_info['link'] ? '<div class="lineup-vertical"><a href="'.$edit_img_info['link'].'" class="c_btn h27" target="_blank">바로가기</a></div> <!--//바로가기 버튼-->' : '');

									// 에디터 이미지 사용하는 상품, 게시글등의 타이틀(제목)
									$ei_title = $arr_ei[$r['eiu_tablename']];

									if($ei_title){
										
										// 프로모션인 경우 smart_table_text 테이블에서 content 내용 검색후 타이틀 추출
										if($r['eiu_tablename']=='promotion'){
											$ei_datauid = _MQ("select ttt_datauid from smart_table_text where ttt_value like '%{$r['eif_img']}%' AND ttt_datauid ='".$r['eiu_datauid']."' ");
											$ei_title_name = _MQ("select ".$ei_title['title']." from ".$ei_title['table']." where ".$ei_title['uid']."='".$ei_datauid['ttt_datauid']."' ");
										}else{
											// like 검색으로 사용하는 곳 검색 후 타이틀 가져오기
											$ei_title_name = _MQ("select ".$ei_title['title']." from ".$ei_title['table']." where ".$ei_title['content']." like '%{$r['eif_img']}%' AND ".$ei_title['uid']."='".$r['eiu_datauid']."' ");
										}
										$ei_title_name = $ei_title_name[$ei_title['title']];
									}

									// No
									$_num++;
						?>
								<tr>
									<td><?php echo $_num; ?></td>
									<td><?php echo $ei_use_name; ?></td><!-- 사용처 구분 -->
									<td><div style="text-align:left"><?php echo $ei_title_name; ?></div></td><!-- 사용하는곳 타이틀 -->
									<td ><?php echo $ei_use_link; ?></td><!-- 바로가기 -->
								</tr>
						<?php
								}
							}
						?>
					</tbody>
				</table>
				<?php if(sizeof($res) < 1){ ?>
					<!-- 내용없을경우 -->
					<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
				<?php } ?>
			</div>

			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
				</ul>
			</div>
		</form>