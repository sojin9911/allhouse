<?php
include_once('wrap.header.php');

// 상품에서 사용된 해시 리스트를 추출 한다.
$HashData = _MQ(" select group_concat(sp.p_hashtag) as hash from `smart_product` as sp where (1) and p_hashtag != '' ");
$HashData = explode(',', $HashData['hash']);
$HashList = '';
if(count($HashData) <= 0) $HashData = array();
if(count($HashData) > 0) {
	$HashData = array_flip($HashData);
	@ksort($HashData);
	$HashList = "'".implode("', '", array_keys($HashData))."'";
}
?>
<form action="_config.keyword.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>검색 키워드</th>
					<td>
						<input type="text" name="s_recommend_keyword" class="design js_tag" value="<?php echo $siteInfo['s_recommend_keyword']; ?>" style="width:100%;">
						<div class="tip_box">
							<?php echo _DescStr('검색 입력란 안에 노출되는 키워드 입니다. (Enter 혹은 Tab으로 구분)'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>검색 해시태그</th>
					<td>
						<input type="text" name="s_recommend_hashtag" class="design js_hashtag" value="<?php echo $siteInfo['s_recommend_hashtag']; ?>" style="width:100%">
						<div class="tip_box">
							<?php echo _DescStr('검색 입력란 안에 노출되는 해시태그 입니다. (Enter 혹은 Tab으로 구분)'); ?>
							<?php echo _DescStr('상품등록 시 추가된 해시태그만 입력 가능 합니다.', 'black'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="ess">FAQ 인기키워드</th>
					<td>
						<input type="text" name="s_faq_keyword" class="design js_tag" value="<?php echo $siteInfo['s_faq_keyword']; ?>" style="width:100%">
						<div class="tip_box">
							<?php echo _DescStr('고객센터 메인 페이지 FAQ에 노출될 검색 키워드입니다. (Enter 혹은 Tab으로 구분)'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>

<script type="text/javascript">
	$('.js_hashtag').tagEditor({
		autocomplete: {
			delay: 0,
			position: { collision: 'flip' },
			source: [<?php echo $HashList; ?>],
			slect: function(e, u) {
				var tag = u.item.value;
				u.item.value = '';
				$(this).tagEditor('addTag', tag);
			}
		},
		forceLowercase: false,
		beforeTagSave: function(field, editor, tags, tag, val) {
			var list = $.parseJSON('<?php echo json_encode($HashData); ?>');
			if(list[val] === undefined) return false;
		}
	});
</script>
<?php include_once('wrap.footer.php'); ?>