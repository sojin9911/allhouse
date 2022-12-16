<?PHP
	$app_mode = "popup";
	include_once("inc.header.php");


	if(!$pass_code) {
		error_msgPopup_s("상품코드값이 넘어오지 않았습니다.");
	}

	// - 데이터가 있는 경우 처리 ---
	$que = "select * from smart_product_req_info where pri_pcode='" . $pass_code . "' order by pri_uid asc  ";
	$res = _MQ_assoc($que);

	// 기본항목이 존재하지 않으면 기본설정을 불러온다
	if(sizeof($res) == 0 ){
		$que = "select * from smart_product_req_info where pri_pcode='_DEFAULT_SETTING_' order by pri_uid asc  ";
		$res = _MQ_assoc($que);
	}

	// 기본항목이 존재하지 않으면 추가한다.
	if(sizeof($res) == 0 ){
		foreach($arr_reqinfo_keys as $req_k => $req_v) {
			$res[] = array('pri_key'=>$req_v, 'pri_value'=>'');
		}
	}

	// 항목 하나 html
	function html_addinfo($_key='', $_value=''){
		$html_addinfo = '
			<tr>
				<td><span class="num"></span></td>
				<td class="option_name"><input type="text" name="_key[]" class="design" placeholder="" value="'. stripslashes($_key) .'" style="width:100%"></td>
				<td><input type="text" name="_value[]" class="design" placeholder="" value="'. stripslashes($_value) .'" style="width:100%"></td>
				<td>
					<div class="lineup-center">
						<a href="#none" class="js_up_addinfo c_btn h22 icon_up" title="위로"></a>
						<a href="#none" class="js_down_addinfo c_btn h22 icon_down" title="아래로"></a>
						<a href="#none" class="js_add_addinfo_next c_btn h22">추가</a><!-- 끼워넣기 추가버튼 -->
						<a href="#none" class="js_delete_addinfo c_btn h22 gray">삭제</a>
					</div>
				</td>
			</tr>
		';
		$html_addinfo = str_replace(array("\n", "\r", "\t"), '', $html_addinfo);
		echo $html_addinfo;
	}

?>



<?// 팝업을 위한 css 추가 --- window.open시 1120px로 띄움 ?>
<style>
	body {min-width:1100px;}
	.wrap {padding-bottom:0px;}
</style>



<div class="popup" style="border:0;">

	<div class="pop_title"><strong>정보제공고시 <?php echo ($pass_code=='_DEFAULT_SETTING_'?'기본항목 ':null); ?>설정</strong></div>

	<form name='frm_reqinfo' method='post' action='_product_reqinfo.pro.php'>
	<input type=hidden name='_mode' value='modify'>
	<input type=hidden name='pass_code' value='<?php echo $pass_code; ?>'>

		<!-- ● 데이터 리스트 -->
		<div class="data_list">

			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" class="js_add_addinfo c_btn h27 icon icon_plus_b">항목추가</a>
					<a href="#none" onclick="document.frm_reqinfo.submit();" class="c_btn h27 gray">정보저장</a>
				</div>
				<?php if($pass_code <> '_DEFAULT_SETTING_' && $AdminPath == 'totalAdmin'){ // SSJ : 전체관리자만 설정가능 : 2022-01-10 ?>
				<div class="right_box">
					<a href="#none" onclick="reqinfo_default_setting_popup(); return false;" class="c_btn h27 dark">기본항목설정</a>
				</div>
				<?php } ?>
			</div>
			<!-- / 리스트 컨트롤영역 -->

			<table class="table_list">
				<colgroup>
					<col width="60"><col width="200"><col width="*"><col width="150">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO</th>
						<th scope="col">항목명</th>
						<th scope="col">항목 내용</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody id="reqinfo_area">
				<?PHP
					foreach($res as $k=>$v){
						echo html_addinfo($v['pri_key'], $v['pri_value']);
					}
					// - 데이터가 있는 경우 처리 ---
				?>
				</tbody>
			</table>


			<div class="tip_box">
				<div class="c_tip">정보 입력 후 반드시 [정보저장]버튼을 클릭하여 변경 및 추가 내용을 저장하시기 바랍니다.</div>
				<div class="c_tip">상품에 필요한 정보제공고시를 항목과 내용으로 등록하며, 등록된 내용은 상품 상세페이지에 노출됩니다.</div>
			</div>

		</div>

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="document.frm_reqinfo.submit();" class="c_btn h34 black">정보저장</a></li>
				<li><a href="#none" onclick="window.close();" class="c_btn h34 black line normal">닫기</a></li>
			</ul>
		</div>

	</form>

</div>


<script>
	// 기본 html
	var html = '<?php echo html_addinfo(); ?>';

	// 마지막에 한줄 추가
	$(document).on('click', '.js_add_addinfo', function(){
		$('#reqinfo_area').append(html);
		init_index();
	});

	// 다음줄에 한줄 추가
	$(document).on('click', '.js_add_addinfo_next', function(){
		$(this).closest('tr').after(html);
		init_index();
	});

	// 한줄 위로
	$(document).on('click', '.js_up_addinfo', function(){
		var up = $(this).closest('tr').prev();

		// 맨위가 아닐때만
		if(up.length>0){
			var cur = $(this).closest('tr');
			up.before(cur);
			init_index();
		}
	});

	// 한줄 아래로
	$(document).on('click', '.js_down_addinfo', function(){
		var down = $(this).closest('tr').next();

		// 맨위가 아닐때만
		if(down.length>0){
			var cur = $(this).closest('tr');
			down.after(cur);
			init_index();
		}
	});

	// 현재행 삭제
	$(document).on('click', '.js_delete_addinfo', function(){
		$(this).closest('tr').remove();
		init_index();
	});

	// 추가/삭제/순서변경시 NO갱신
	function init_index(){
		var idx = 1;
		$('#reqinfo_area').find('tr').each(function(){
			$(this).find('.num').text(idx.toString().comma());
			idx++;
		});
	}
	init_index();

	// 정보제공고시 기본항목 설정창 열기
	function reqinfo_default_setting_popup() {
		window.open('_product_reqinfo.popup.php?pass_code=_DEFAULT_SETTING_','default_setting','width=1120,height=600,scrollbars=yes,top=50,');
	}
</script>



<?PHP
	include_once("inc.footer.php");
?>