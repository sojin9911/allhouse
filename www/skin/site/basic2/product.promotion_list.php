<!-- ◆기획전 탑 -->
<div class="c_section c_promotion">
	<div class="layout_fix">
		
		<div class="title"><strong>쇼핑몰 기획전</strong></div>

		<!-- 기획전리스트 -->
		<div class="promotion_list">

			<? if(sizeof($res) == 0){?>
				<!-- 내용 없을때 ul없어지고 노출 --><div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
			<?}?>

			<ul>

			<?php 

				if(sizeof($res) > 0){
					foreach($res as $k=>$v){


						// 스킨 이미지 PATH
						$app_path = $SkinData['skin_path'] . '/';


						// 타이틀
						$app_title = addslashes($v['pp_title']);


						// 목록 이미지 //<!-- [PC] 기획전 목록 썸네일 (515 * 190) / 기획전목록 썸네일 등록 안했을경우 기획전 상세 배너 불러옴 -->
						$app_img = '';
						$_img = IMG_DIR_BANNER.$v['pp_img'];
						if(file_exists($_SERVER['DOCUMENT_ROOT'].$_img)) {
							$app_img = '<img src="'. $_img.'" alt="'. $app_title .'">';
						}


						// 링크
						$app_link = "/?pn=product.promotion_view&uid=" . $v['pp_uid'];


						// 기획전 상태
						//			시작전 -> D-123
						//			진행중 -> 진행 (진행시 d_day 클래스에 if_day 클래스 추가)
						//			종료후 -> 마감 (종료된 기획전일 경우 d_day 클래스에 if_close 클래스 추가 ,  li에 if_end_promo 클래스 추가)						
						$app_status = $app_li_class = $app_dday_class = $app_close_string = '';
						//종료후
						if($v['pp_edate']<DATE('Y-m-d')) {
							$app_status = '마감'; // 진행상태
							$app_li_class = 'if_end_promo';
							$app_dday_class = 'if_close';
							$app_close_string = '<span class="promo_txt">종료된 기획전입니다.</span>';// 종료문구
						}
						//시작전
						else if($v['pp_sdate']>DATE('Y-m-d')) {
							$app_status = 'D-' . fn_date_diff($v['pp_sdate'],DATE("Y-m-d")); // 진행상태
						}
						//진행중
						else {
							$app_status = '진행'; // 진행상태
							$app_dday_class = 'if_day';
						}


						// 시작일
						$app_sdate = $v['pp_sdate'];
						//종료일
						$app_edate = $v['pp_edate'];


						// 상품갯수 추출
						$sque = " select count(*) as cnt from smart_promotion_plan_product_setup where ppps_ppuid = '". $v['pp_uid'] ."'";  
						$sres = _MQ($sque);
						$app_pcnt = $sres['cnt'];


				?>
					<!-- li 반복 --><li class="<?=$app_li_class?>">
						<div class="promotion_box">
							<a href="<?=$app_link?>" class="upper_link" title="<?=$app_title?>"><img src="<?=$app_path?>images/c_img/blank.gif" alt="" /></a>
							<div class="thumb">
								<!-- 썸네일 보더 및 종료된 기획전 배경 --><div class="promo_bg"></div>
								<!-- 종료된 기획전일 경우에 노출 --><?=$app_close_string?>
								<?=$app_img?>
							</div>
							<!-- 기획전 타이틀 / 총 상품 갯수 -->
							<div class="tit_info">
								<span class="tit"><?=$app_title?></span>
								<span class="total">총 <?=number_format($app_pcnt)?>개 상품</span>
							</div>
							<!-- 디데이 / 기간 -->
							<div class="date_info">
								<!-- 이벤트 기간일 경우 if_day 클래스 추가 및 'D-DAY' 문구 변경 / 마감일 경우 if_close 클래스 추가 및 '마감' 문구 변경 -->
								<span class="d_day <?=$app_dday_class?>"><?=$app_status?></span>
								<span class="date"><?=$app_sdate?> ~ <?=$app_edate?></span>
							</div>
						</div>
					</li>
				<?php
						}
					}
				?>

			</ul>

		</div>


		<!-- 페이지네이트 (상품목록 형) -->
		<div class="c_pagi">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
		<!-- / 페이지네이트 (상품목록 형) -->


	</div>
</div>
<!-- /◆기획전 탑 -->