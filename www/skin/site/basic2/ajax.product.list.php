<?php
// $ActiveListColClass: 리스트의 단수를 설정 합니다.
/*
	리스트형 보기는 if_col1을 사용하면되며
	리스트 형태가 1단이라면 기본 리스트 형태는 리스트형으로 하고 썸네일형 버튼을 클릭 하면 4단이 나오면 된다.
*/
if(count($res) > 0) {
?>
	<!-- ◆ 상품리스트 : 기본 6단 / 5단 if_col5  -->
	<div class="item_list<?php echo $ActiveListColClass; ?>">
		<ul>
			<?php
			foreach($res as $k=>$v) {
			?>
				<li class="js_active_list_col">
				<?php 
					$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
					$locationFile = basename(__FILE__); // 파일설정
					include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
				?>
				</li>
			<?php } ?>
		</ul>
	</div>
	<!-- / ◆ 상품리스트 -->
<?php } else { ?>
	<!-- 내용없을경우 -->
	<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
<?php } ?>