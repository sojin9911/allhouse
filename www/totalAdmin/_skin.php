<?php
include_once('wrap.header.php');
// $_skin_list -> include/inc.path.php


// 사용중인 스킨을 가장 우선 순위로 노출
$_skin_list_tmp[$siteInfo['s_skin']] = $_skin_list[$siteInfo['s_skin']];
unset($_skin_list[$siteInfo['s_skin']]);
$_skin_list_tmp = array_merge($_skin_list_tmp, $_skin_list);
$_skin_list = $_skin_list_tmp;
unset($_skin_list_tmp);
?>
<div class="data_list">
	<table class="table_list">
		<colgroup>
			<col width="350"><col width="250"><col width="*"><col width="100"><col width="100">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">PC 화면</th>
				<th scope="col">Mobile 화면</th>
				<th scope="col">스킨 정보</th>
				<th scope="col">미리보기</th>
				<th scope="col">적용</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($_skin_list as $k=>$v) {
				$SkinInfo = array();
				if(file_exists(OD_SKIN_ROOT.'/site/'.$k.'/skin.xml')) $SkinInfo = xml2array(file_get_contents(OD_SKIN_ROOT.'/site/'.$k.'/skin.xml'));
				if(!$SkinInfo['skin']['title']) $SkinInfo['skin']['title'] = $k;
			?>
				<tr<?php echo ($k == $siteInfo['s_skin']?' class="skin_hit"':null); ?>><!-- 현재 사용중인 스킨은 tr:skin_hit 클래스 추가 / 선택한 스킨은 항상 제일 상단에 위치 -->
					<td class=""><?php echo (file_exists(OD_SKIN_ROOT.'/site/'.$k.'/thumb.png')?'<img src="'.OD_SKIN_URL.'/site/'.$k.'/thumb.png'.'" data-img="'.OD_SKIN_URL.'/site/'.$k.'/thumb.png'.'" class="js_thumb_popup" alt="">':null); ?></td>
					<td class=""><?php echo (file_exists(OD_SKIN_ROOT.'/site_m/'.$k.'/thumb.png')?'<img src="'.OD_SKIN_URL.'/site_m/'.$k.'/thumb.png'.'" data-img="'.OD_SKIN_URL.'/site_m/'.$k.'/thumb.png'.'" class="js_thumb_popup" alt="">':null); ?></td>
					<td>
						<div class="order_item">
							<!-- 상품명 -->
							<div class="bold">
								<?php if($SkinInfo['skin']['date']) { ?>
									<span class="preview_icon">
										<img src="images/log_guide.gif" class="js_tooltip" data-content="<?php echo _DescStr('스킨 등록일 : '.$SkinInfo['skin']['date']); ?>" alt="" style="vertical-align:middle;">
									</span>
								<?php } ?>
								<?php echo $SkinInfo['skin']['title']; ?>
							</div>
							<?php
							if($SkinInfo['skin']['info']) {
								$Info = explode(PHP_EOL, trim($SkinInfo['skin']['info']));
								if(count($Info) <= 0) $Info = array();
								echo '<div class="dash_line"><!-- 점선라인 --></div>';
								foreach($Info as $kk=>$vv) {
									$vv = trim($vv);
									if(!$vv) continue;
									echo '<div class="option bullet">'.trim($vv).'</div>';
								}
							}
							?>
						</div>
					</td>
					<td>
						<div class="lineup-center">
							<a href="/?_pskin=<?php echo $k; ?>" class="c_btn h26 line t5" target="_blank">미리보기</a>
						</div>
					</td>
					<td>
						<div class="lineup-center">
							<?php if($k == $siteInfo['s_skin']) { ?>
								<a href="/?_pskin=<?php echo $k; ?>" class="c_btn h26 line t3 red" onclick="alert('이미 적용된 스킨입니다.'); return false;">사용중</a>
							<?php } else { ?>
								<a href="/?_pskin=<?php echo $k; ?>" class="c_btn h26 line t3 black js_active_skin" data-skin="<?php echo $k; ?>">적용</a>
							<?php } ?>
						</div>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td colspan="5">
					<div class="tip_box">
						<?php echo _DescStr('스킨 변경 시 PC, 모바일 모두 변경됩니다.', 'black'); ?>
						<?php echo _DescStr('스킨 변경 시 배너를 다시 설정해야 합니다. (단, 스킨별 직접 설정한 배너는 초기화 되지 않습니다.)', 'black'); ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>



<script type="text/javascript">
	$(document).delegate('.js_active_skin', 'click', function(e) {
		e.preventDefault();
		var _skin = $(this).data('skin');
		location.href = '_skin.pro.php?_mode=update&set_skin='+_skin;
	});
</script>
<?php include_once('wrap.footer.php'); ?>