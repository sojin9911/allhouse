
				<?php

					$row_setup = $siteInfo;
					// ---------------------------------- 수신동의 2년 지난 -  회원 ---------------------------------- //
					if($row_setup['s_2year_opt_use'] == "Y") {
						// JJC : 수정 : 2021-05-17
						//$mr_cnt = _MQ(" select count(*) as cnt from smart_2year_opt_log where ol_status='N'  "); 
						$mr_cnt = _MQ(" select count(*) as cnt from smart_2year_opt_log INNER JOIN smart_individual on (in_id = ol_mid and in_sleep_type = 'N' AND in_out = 'N' and in_userlevel != '9') where ol_status='N'  "); 
						if( $mr_cnt['cnt'] > 0 ) :
				?>

				<div class="main_box_area">
					<!-- 내부 그룹타이틀 -->
					<div class="group_title">
						<?php
							$Uniq = uniqid();
							echo '<script>$(document).ready(function () {setInterval("$(\'.blink_text_'.$Uniq.'\').fadeOut().fadeIn();",1000);});</script>';
							echo '<span class="icon"></span><span class="title blink_text_'.$Uniq.'" style="color:red">수신동의 2년 넘은 회원 메일/문자 발송대기 목록</span>';
						?>
						<span class="btn_area">
							<span class="shop_btn_pack"><a href="_addons.php?pass_menu=2yearOpt/_2year_opt.form" class="small gray" title="바로가기" >바로가기</a></span>
						</span>
					</div>
					<!-- 내부 그룹타이틀 -->


					<!-- 데이터 출력 -->
					<table class="last_TB"></table>
					<table class="last_TB" summary="게시판현황">
						<colgroup>
							<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">대상회원수</td>
								<td class="conts" style=" height:30px; text-align:left; margin-left:5px; ">
									<strong><?=number_format($mr_cnt['cnt'])?></strong>명
									<?=_DescStr("해당 페이지로 이동하여 발송형태를 정하신 후 <strong>발송</strong>해주시기 바랍니다.")?>
								</td>
							</tr>
						</tbody>
					</table>
					<!-- 데이터 출력 -->
				</div>
				<?php 
						endif;  
					}
				?>
				<? // ---------------------------------- 수신동의 2년 지난 -  회원 ---------------------------------- // ?>
