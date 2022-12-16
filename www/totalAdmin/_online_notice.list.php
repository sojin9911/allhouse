<?php
	include_once("wrap.header.php");


	// -- 은행명 추출 ---
	$arr_bank = array();
	$ex = _MQ_assoc("select * from smart_bank_set order by bs_idx asc");
	foreach( $ex as $k=>$v ){
		$arr_bank[rm_str($v['bs_bank_num'])] = '['. $v['bs_bank_name'] .'] ' . $v['bs_bank_num'];
	}
	if(sizeof($arr_bank) < 1) $arr_bank['none'] = '등록된 입금은행이 없습니다.';



	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_name !="" ) { $s_query .= " and on_name like '%${pass_name}%' "; }
	if( $pass_price !="" ) { $s_query .= " and on_price = '". rm_str($pass_price) ."' "; }
	if( $pass_sdate !="" ) { $s_query .= " and on_date >= '${pass_sdate}' "; }
	if( $pass_edate !="" ) { $s_query .= " and on_date <= '${pass_edate}' "; }
	if( $pass_view !="" ) { $s_query .= " and on_view = '${pass_view}' "; }

	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'on_uid';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_online_notice $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from smart_online_notice {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");


?>



	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">

		<div class="c_tab">
			<ul>
				<li class="hit"><a href="#none" class="btn tab_menu" data-idx="search"><strong>미확인 입금자 검색</strong></a></li>
				<li class=""><a href="#none" class="btn tab_menu" data-idx="config"><strong>환경 설정</strong></a></li>
			</ul>
		</div>

		<!-- 	검색 폼 -->
		<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
			<div class="tab_conts" data-idx="search">
				<!-- 폼테이블 2단 -->
				<table class="table_form">
					<colgroup>
						<col width="180"><col width="*"><col width="180"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th>입금자</th>
							<td><input type="text" name="pass_name" class="design" style="" value="<?php echo $pass_name; ?>"></td>
							<th>입금액</th>
							<td><input type="text" name="pass_price" class="design number_style" style="" value="<?php echo $pass_price; ?>"></td>
						</tr>
						<tr>
							<th>입금일자</th>
							<td>
								<input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day" style="width:85px">
								<span class="fr_tx">-</span>
								<input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day" style="width:85px">
							</td>
							<th>노출여부</th>
							<td>
								<?php echo _InputRadio( "pass_view" , array('', 'Y','N'), $pass_view , "" , array('전체', '노출','숨김') ); ?>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- 폼테이블 2단 -->

				<?php if($siteInfo['s_online_notice_use'] <> 'Y'){ ?>
				<div class="tip_box">
					<?php echo _DescStr('미확인 입금자 메뉴가 <em>사용안함</em>으로 설정되어 있습니다. ' , 'black'); ?>
					<?php echo _DescStr('사용을 원하시면 상단의 <em>환경설정</em>에서 메뉴 사용여부를 "사용함"으로 설정해 주시기 바랍니다.'); ?>
				</div>
				<?php } ?>


				<!-- 가운데정렬버튼 -->
				<div class="c_btnbox">
					<ul>
						<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"></span></li>
						<?php if($mode == 'search'){ ?>
							<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</form>

		<!-- 환경설정 from  -->
		<form action="_online_notice.pro.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="_mode" value="config" />
			<div class="tab_conts" data-idx="config" style="display:none;">
				<!-- 폼테이블 2단 -->
				<table class="table_form">
					<colgroup>
						<col width="180"><col width="*"><col width="180"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th>메뉴 사용여부</th>
							<td>
								<?php echo _InputRadio( "_online_notice_use" , array('Y','N'), $siteInfo['s_online_notice_use']?$siteInfo['s_online_notice_use']:'N' , '' , array('사용함','사용안함') ); ?>
							</td>
							<th>실시간입금 자동등록</th>
							<td>
								<?php echo _InputRadio( "_online_notice_auto" , array('Y','N'), $siteInfo['s_online_notice_auto']?$siteInfo['s_online_notice_auto']:'N' , '' , array('사용함','사용안함') ); ?>
							</td>
						</tr>
						<tr>
							<th>입금자명 부분노출</th>
							<td>
								<?php echo _InputRadio( "_online_notice_privacy" , array('Y','N'), $siteInfo['s_online_notice_privacy']?$siteInfo['s_online_notice_privacy']:'N' , '' , array('사용함','사용안함') ); ?>
							</td>
							<th>입금은행 노출여부</th>
							<td>
								<?php echo _InputRadio( "_online_notice_bank" , array('Y','N'), $siteInfo['s_online_notice_bank']?$siteInfo['s_online_notice_bank']:'N' , '' , array('노출함','노출안함') ); ?>
							</td>
						</tr>
						<tr>
							<th>노출기간</th>
							<td colspan="3">
								<?php echo _InputRadio( "_online_notice_view" , array('3','7','14','30','60'), $siteInfo['s_online_notice_view']?$siteInfo['s_online_notice_view']:'3' , '' , array('3일','7일','14일','30일','60일') ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="4">
								<div class="tip_box">
									<?php echo _DescStr('<em>사용여부</em>를 "사용함"으로 설정하면 고객센터에 <em>미확인 입금자</em>메뉴가 노출됩니다.' , 'black'); ?>
									<?php echo _DescStr('실시간입금 자동등록을 <em>사용함</em>으로 선택 시 실시간입금 확인의 처리대기 목록이 자동으로 추가됩니다. '); ?>
									<?php echo _DescStr('입금자명 부분노출을 <em>사용함</em>으로 선택 시 입금자명중 마지막 글자를 "*"로 노출합니다. <em>ex) 홍길동 > 홍길*</em>'); ?>
									<?php echo _DescStr('입금일 기준으로 노출기간동안만 노출됩니다. '); ?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- 폼테이블 2단 -->


				<!-- 가운데정렬버튼 -->
				<div class="c_btnbox">
					<ul>
						<li><span class="c_btn h34 black "><input type="submit" name="" value="설정저장"></span></li>
					</ul>
				</div>
			</div>
		</form>

	</div>


	<!-- ● 데이터 리스트 -->
	<div class="data_list">


		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
				<a href="#none" onclick="autoInsert(); return false;" class="c_btn h27 black line">실시간입금 내역 일괄등록</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="downloadExcel('select_excel'); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
				<a href="#none" onclick="downloadExcel('search_excel'); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드(<?php echo number_format($TotalCount); ?>)</a>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->


		<form name="frm" method="post" action="_online_notice.pro.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">
			<table class="table_list">
				<colgroup>
					<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
					<col width="40"><col width="70"><col width="160"><col width="160"><col width="*"><col width="160"><col width="140"><col width="80">
					<?php }else{ ?>
					<col width="40"><col width="70"><col width="260"><col width="260"><col width="*"><col width="140"><col width="80">
					<?php } ?>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">NO</th>
						<th scope="col">입금일자</th>
						<th scope="col">입금자</th>
						<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
						<th scope="col">입금은행</th>
						<?php } ?>
						<th scope="col">입금액</th>
						<th scope="col">노출설정</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<!-- 리스트에서 바로 등록 / if_direct_form 제일끝에 td에 클래스값 this_last 꼭 추가 -->
					<tr>
						<td colspan="2">
							<?php echo _DescStr('직접등록'); ?>
						</td>
						<td><div class="lineup-center"><input type="text" name="js_on_date" id="js_on_date" value="" class="design js_pic_day" placeholder="" style="width:85px"></div></td>
						<td><div class="lineup-center"><input type="text" name="js_on_name" id="js_on_name" value="" class="design" placeholder="" style="width:85px"></div></td>
						<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
							<td class="t_left">
								<div class="lineup-center">

									<select name="js_on_bank_type" id="js_on_bank_type">
										<option value="select">입금은행 선택</option>
										<option value="input">직접입력</option>
									</select>

									<select name="js_on_bank_select" id="js_on_bank_select">
										<option value="">- 입금은행 선택 -</option>
										<?php foreach($arr_bank as $_bank_k=>$_bank){ ?>
										<option value="<?php echo ($_bank_k <> 'none' ? $_bank : null); ?>"><?php echo $_bank; ?></option>
										<?php } ?>
									</select>

									<div class="fr_tx" id="js_on_bank_input" style="display:none">
										<input type="text" name="js_on_bank_input" value="" class="design" placeholder="" style="width:218px">
									</div>

								</div>
							</td>
						<?php } ?>
						<td><div class="lineup-center"><input type="text" name="js_on_price" id="js_on_price" value="" class="design number_style" placeholder="" style="width:85px"><span class="fr_tx">원</span></div></td>
						<td>
							<div class="lineup-center">
								<?php echo _InputRadio( "js_on_view" , array('Y','N'), 'Y' , ' id="js_on_view" ' , array('노출','숨김') ); ?>
							</div>
						</td>
						<td class="this_last">
							<div class="lineup-vertical">
								<span class="c_btn h22 black js_online_notice_add"><input type="button" onclick="insertList(); return false;" name="" value="등록"></span>
							</div>
						</td>
					</tr>

					<?PHP
						foreach($res as $k=>$v) {

							$_del = '<a href="#none" onclick="del(\'_online_notice.pro.php?_mode=delete&_uid='. $v['on_uid'] .'&_PVSC='. $_PVSC .'\');" class="c_btn h22 gray">삭제</a>';

							$_num = $TotalCount - $count - $k ;

							$_style = '';
							if($k==0) $_style = ' style="border-top:0;" ';

					?>
						<tr>
							<td<?php echo $_style; ?>><label class="design"><input type="checkbox" name="chk_uid[]" class="js_ck" value="<?php echo $v['on_uid']; ?>"></label></td>
							<td<?php echo $_style; ?>><?php echo $_num; ?></td>
							<td<?php echo $_style; ?>><?php echo date('Y.m.d', strtotime($v['on_date'])); ?></td>
							<td<?php echo $_style; ?> class="t_black"><?php echo $v['on_name']; ?></td>
							<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
							<td<?php echo $_style; ?> class="t_left"><?php echo $v['on_bank']; ?></td>
							<?php } ?>
							<td<?php echo $_style; ?>><?php echo number_format($v['on_price']); ?></td>
							<td<?php echo $_style; ?>>
								<div class="lineup-center">
									<?php echo _InputRadio('ajax_view_' . $v['on_uid'], array('Y', 'N'), $v['on_view'], ' class="js_ajax_modify_view" data-uid="'. $v['on_uid'] .'" ', array('노출', '숨김')); ?>
								</div>
							</td>
							<td<?php echo $_style; ?>>
								<div class="lineup-vertical">
									<?php echo $_del; ?>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</form>

		<?php if(sizeof($res)<1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</div>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>


<script>
	// 검색 / 환경설정 텝메뉴 클릭 이벤트
	var trigger_cont_editor = true;
	$(document).on('click', '.tab_menu', function() {
		var idx = $(this).data('idx');
		// 탭변경
		$('.tab_menu').closest('li').removeClass('hit');
		$('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
		// 입력항목변경
		$('.tab_conts').hide();
		$('.tab_conts[data-idx='+ idx +']').show();
	});


	// 한줄 등록 버튼
	$(document).on('change', '#js_on_bank_type', function(){
		var type = $(this).val();
		if(type == 'select'){
			$('#js_on_bank_select').show();
			$('#js_on_bank_input').hide();
		}else{
			$('#js_on_bank_select').hide();
			$('#js_on_bank_input').show();
		}
	});

	// 선택삭제
	function selectDelete() {
		if($('.js_ck').is(":checked")){
			if(confirm('정말 삭제하시겠습니까?')){
				$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
				$('form[name=frm]').attr('action' , '_online_notice.pro.php');
				document.frm.submit();
			}
		}
		else {
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	}

	// 노출설정 ajax 업로드
	$(document).on('change', '.js_ajax_modify_view', function(){
		var _uid = $(this).data('uid');
		var _val = $('[name=ajax_view_' + _uid + ']:checked').val();
		var parent = $(this).closest('tr');

		$.ajax({
			url : '_online_notice.pro.php'
			,data : '_mode=ajax_modify_view&_uid=' + _uid + '&_val=' + _val
			,type : 'get'
			,dataType : 'text'
			,success : function(data){
				if(data=='success'){
					parent.find('td').animate({backgroundColor: '#d9dee3'},100).animate({backgroundColor: '#fff'},100);
				}else{
					alert('노출설정 변경 시 오류가 발생하였습니다.\n\n새로고침(F5)후 다시 시도해 주시기 바랍니다.');
				}
			}
		});
	});

	// 선택엑셀 다운로드
	function downloadExcel(_mode){
		if(_mode == 'select_excel' && $('.js_ck').is(':checked') === false){
			alert('1개 이상 선택해 주시기 바랍니다.');
			return false;
		}

		$('form[name=frm]').children('input[name=_mode]').val(_mode);
		$('form[name=frm]').attr('action' , '_online_notice.pro.php');
		document.frm.submit();
		return true;
	}
	// 검색엑셀 다운로드

	// 실시간입금 내역 일괄등록
	function autoInsert(){
		if(confirm('[실시간입금 확인]내역중 "처리대기"목록을\n\n미확인 입금자 관리에 등록합니다.\n\n계속 진행하시겠습니까?   ')){
			document.location.href='_online_notice.pro.php?_mode=auto_insert&_PVSC=<?php echo $_PVSC; ?>';
		}
	}

	// 폼 유효성 검사 -- 테이블안에서 작동안하는듯
	$(document).ready(function(){
		$("form[name=add_frm]").validate({
			rules: {
					js_on_date: { required: true }
					,js_on_name: { required: true }
					,js_on_bank_input: { required: function(){ if( $('#js_on_bank_type').val()=='input' ){ return true; }else{ return false; } } }
					,js_on_bank_select: { required: function(){ if( $('#js_on_bank_type').val()=='select' ){ return true; }else{ return false; } } }
					,js_on_price: { required: true , minlength : function(){ if( $('#js_on_price').val()=='0' ){ return 2; }else{ return 0; } } }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

			},
			messages: {
					js_on_date : { required: '입금일자를 입력해주시기 바랍니다.' }
					,js_on_name : { required: '입금자를 입력해주시기 바랍니다.' }
					,js_on_bank_input : { required: '입금은행을 입력해주시기 바랍니다.' }
					,js_on_bank_select : { required: '입금은행을 선택해주시기 바랍니다.'}
					,js_on_price : { required: '입금액을 입력해주시기 바랍니다.' , minlength : '입금액을 입력해주시기 바랍니다.'}
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}

		});
	});

	// 실시간 입금내역 직접등록
	function insertList(){
		var _date = $('input[name=js_on_date]').val();
		var _name = $('input[name=js_on_name]').val();
		var _bank_input = $('input[name=js_on_bank_input]').val();
		var _bank_select = $('select[name=js_on_bank_select]').val();
		var _price = $('input[name=js_on_price]').val();
		var _type = $('#js_on_bank_type').val();
		var _bank = (_type == 'input' ? _bank_input : _bank_select);
		var _view = $('input[name=js_on_view]:checked').val();

		if(_date == ''){
			alert('입금일자를 입력해주시기 바랍니다.');
			$('input[name=js_on_date]').focus();
			return false;
		}
		if(_name == ''){
			alert('입금자를 입력해주시기 바랍니다.');
			$('input[name=js_on_name]').focus();
			return false;
		}
		<?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
		if(_bank == ''){
			alert('입금은행을 등록해주시기 바랍니다.');
			if(_type == 'input') $('input[name=js_on_bank_input]').focus();
			else $('select[name=js_on_bank_select]').focus();
			return false;
		}
		<?php  } ?>
		if(_price == ''){ alert('입금액을 등록해주시기 바랍니다.'); return false; }

		var _url = '_online_notice.pro.php?_mode=add';
		_url += '&_date=' + _date;
		_url += '&_name=' + _name;
		_url += '&_bank=' + _bank;
		_url += '&_price=' + _price;
		_url += '&_view=' + _view;
		document.location.href = encodeURI(_url);
	}

</script>


<?php include_once("wrap.footer.php"); ?>