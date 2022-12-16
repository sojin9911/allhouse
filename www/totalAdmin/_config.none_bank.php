<?php
include_once('wrap.header.php');

// 검색 조건
$s_query = '';
if($pass_bank_name) $s_query .= " and `bs_bank_name` like '%{$pass_bank_name}%' ";
if($pass_user_name) $s_query .= " and `bs_user_name` like '%{$pass_user_name}%' ";
if($pass_bank_num) $s_query .= " and `bs_bank_num` like '%{$pass_bank_num}%' ";

// 데이터 조회
$listmaxcount = 20;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_bank_set where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select * from smart_bank_set where (1) {$s_query} order by bs_idx asc ");
?>
<!-- 검색폼-->
<div class="group_title">
	<strong>무통장 검색</strong>
	<div class="btn_box"><a href="#none" class="c_btn h46 red" onclick="$('.js_insert').toggle(); return false;">무통장등록</a></div>

	<form action="_config.none_bank.pro.php" method="post">
		<input type="hidden" name="_mode" value="insert">
		<div class="open_excel js_insert" style="display: none">
			<!-- 추가폼 -->
			<table class="table_form">
				<colgroup>
					<col width="180">
					<col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>무통장등록</th>
						<td>
							<span class="fr_tx">은행이름 :</span>
							<input type="text" name="_bank_name" class="design">
							<div class="clear_both"></div>
							<span class="fr_tx">계좌주명 :</span>
							<input type="text" name="_user_name" class="design">
							<div class="clear_both"></div>
							<span class="fr_tx">계좌번호 :</span>
							<input type="text" name="_bank_num" class="design">
							<div class="dash_line"><!-- 점선라인 --></div>
							<span class="c_btn h27 black"><input type="submit" value="추가"></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<div class="data_form if_search">
		<input type="hidden" name="_mode" value="search">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>은행이름</th>
					<td>
						<input type="text" name="pass_bank_name" class="design" value="<?php echo $pass_bank_name; ?>">
					</td>
					<th>계좌주명</th>
					<td>
						<input type="text" name="pass_user_name" class="design" value="<?php echo $pass_user_name; ?>">
					</td>
				</tr>
				<tr>
					<th>계좌번호</th>
					<td colspan="3">
						<input type="text" name="pass_bank_num" class="design" value="<?php echo $pass_bank_num; ?>">
					</td>
				</tr>
			</tbody>
		</table>

		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" value="검색"></span></li>
				<?php if($_mode == 'search'){ ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form>



<form name="frm" action="_config.none_bank.pro.php" method="post">
	<input type="hidden" name="_mode" method="modify">
	<div class="data_list">
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
				<a href="#none" onclick="selectModify(); return false;" class="c_btn h27 gray">선택수정</a>
			</div>
		</div>
		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="70"/><col width="120"/>
				<col width="150"/><col width="150"/><col width="*"/>
				<col width="100"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">NO</th>
					<th scope="col">순서</th>
					<th scope="col">은행이름</th>
					<th scope="col">계좌주명</th>
					<th scope="col">계좌번호</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($r) <= 0) { ?>
					<tr>
						<td colspan="7" style="height:45px;"><div class="common_none"><div class="no_icon"></div><div class="gtxt">추가된 무통장입금 은행 정보가 없습니다.</div></div></td>
					</tr>
				<?php } else { ?>
					<?php foreach($r as $k=>$v) { $_num = $TotalCount-$count-$k; ?>
						<tr>
							<td>
								<label class="design"><input type="checkbox" name="_uid[<?php echo $v['bs_uid']; ?>]" class="js_ck" value="Y"></label>
							</td>
							<td><?php echo $_num; ?></td>
							<td>
								<div class="lineup-center">
									<a href="#none" class="c_btn h22 icon_up js_sort" data-mode="up" data-uid="<?php echo $v['bs_uid']; ?>" data-idx="<?php echo $v['bs_idx']; ?>" title="위로"></a>
									<a href="#none" class="c_btn h22 icon_down js_sort" data-mode="down" data-uid="<?php echo $v['bs_uid']; ?>" data-idx="<?php echo $v['bs_idx']; ?>" title="아래로"></a>
									<a href="#none" class="c_btn h22 icon_top js_sort" data-mode="top" data-uid="<?php echo $v['bs_uid']; ?>" data-idx="<?php echo $v['bs_idx']; ?>" title="맨위로"></a>
									<a href="#none" class="c_btn h22 icon_bottom js_sort" data-mode="bottom" data-uid="<?php echo $v['bs_uid']; ?>" data-idx="<?php echo $v['bs_idx']; ?>" title="맨아래로"></a>
								</div>
							</td>
							<td>
								<div class="lineup-full"><input type="text" class="design" name="_bank_name[<?php echo $v['bs_uid']; ?>]" value="<?php echo $v['bs_bank_name']; ?>"></div>
							</td>
							<td>
								<div class="lineup-full"><input type="text" class="design" name="_user_name[<?php echo $v['bs_uid']; ?>]" value="<?php echo $v['bs_user_name']; ?>"></div>
							</td>
							<td>
								<div class="lineup-full"><input type="text" class="design" name="_bank_num[<?php echo $v['bs_uid']; ?>]" value="<?php echo $v['bs_bank_num']; ?>"></div>
							</td>
							<td>
								<div class="lineup-vertical">
									<a href="#" class="c_btn h22 js_modify" data-uid="<?php echo $v['bs_uid']; ?>">수정</a>
									<a href="#none" class="c_btn h22 gray js_delete" data-uid="<?php echo $v['bs_uid']; ?>">삭제</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
</form>

<script type="text/javascript">
// 순서변경
$(document).on('click', '.js_sort', function(e) {
	e.preventDefault();
	var su = $(this)
	var _mode = su.data('mode');
	var _uid = su.data('uid');
	var _idx = su.data('idx');
	$.ajax({
		data: {
			_mode: 'sort_'+_mode,
			_uid: _uid,
			_idx: _idx
		},
		type: 'POST',
		cache: false,
		url: '_config.none_bank.pro.php',
		success: function(data) {
			if(data == 'success') return window.location.reload(true);
			alert(data);
		}
	});
});

// 선택수정
$(document).on('click', '.js_modify', function(e) {
	e.preventDefault();
	var su = $(this).closest('tr');
	var _uid = $(this).data('uid');
	var _bank_name = encodeURI(su.find('input[name^=_bank_name]').val());
	var _user_name = encodeURI(su.find('input[name^=_user_name]').val());
	var _bank_num = encodeURI(su.find('input[name^=_bank_num]').val());
	if(!_bank_name || !_user_name || !_bank_num) return alert('정보를 모두 입력해주세요.');
	location.href = '_config.none_bank.pro.php?_mode=ind_modify&_uid='+_uid+'&_bank_name='+_bank_name+'&_user_name='+_user_name+'&_bank_num='+_bank_num;
});

// 선택삭제
$(document).on('click', '.js_delete', function(e) {
	e.preventDefault();
	var su = $(this).closest('tr');
	var _uid = $(this).data('uid');
	if(!confirm('정말삭제 하시겠습니까?')) return;
	location.href = '_config.none_bank.pro.php?_mode=ind_delete&_uid='+_uid;
});

// 선택 수정
function selectModify() {
	if($('.js_ck').is(":checked")){
		$("form[name=frm]").children("input[name=_mode]").val("modify");
		document.frm.submit();
	}
	else {
		alert('1개 이상 선택해 주시기 바랍니다.');
	}
}

// 선택삭제
function selectDelete() {
	if($('.js_ck').is(":checked")){
		if(confirm("정말 삭제하시겠습니까?")){
			$("form[name=frm]").children("input[name=_mode]").val("delete");
			document.frm.submit();
		}
	}
	else {
		alert('1개 이상 선택해 주시기 바랍니다.');
	}
}
</script>
<?php include_once('wrap.footer.php'); ?>