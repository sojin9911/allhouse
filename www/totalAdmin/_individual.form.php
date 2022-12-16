<?php // -- LCY :: 2017-09-20 -- 운영자관리 폼
		$app_current_link = '_individual.list.php';
		include_once('wrap.header.php');
		if( in_array($_mode,array('modify','add')) == false){
			error_loc_msg("_individual.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다.");
		}

		// -- 모드별 처리
		if( $_mode == 'modify'){ // 수정일 시
			$row = _MQ("select *from smart_individual where in_id = '".$_id."'  ");
			if( count($row) < 1){ error_loc_msg("_individual.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "회원 정보가 없습니다." ); }

			// -- 소셜연동 체크
			if( $row['sns_join'] != 'N'){
				if($row['fb_join'] == 'Y'){ $arrSnsType[] = 'F';  }
				if($row['ko_join'] == 'Y'){ $arrSnsType[] = 'K'; }
				if($row['nv_join'] == 'Y'){ $arrSnsType[] = 'N'; }
			}

			// -- 로그인 횟수를 통해 방문 횟수를 구한다.
			$rowVisit = _MQ("select  count(distinct left(lc_rdate,10)) as cnt from smart_loginchk where lc_mid = '".$row['in_id']."' ");

			// 회원등급 처리
			if( $row['in_mgsuid'] == 0 ){
				$rowMgs = _MQ("select mgs_uid from smart_member_group_set where 1  order by mgs_rank asc limit 0,1"); // 가장낮은등급을 가져온다.
				if( $rowMgs['mgs_uid'] != ''){ // 그룹이 최소 한개가 안될일은 없지만 혹시라도 있을경우를 대비
					_MQ_noreturn("update smart_individual set in_mgsuid = '".$rowMgs['mgs_uid']."', in_mgsdate = now()  where in_id = '".$row['in_id']."' ");
					$row['in_mgsuid'] = $rowMgs['mgs_uid'];
				}
			}

		}else{ // 추가일시

		}

		// -- 회원등급 선택을 위한 처리
		$resMgs = _MQ_assoc("select *from smart_member_group_set where 1  order by mgs_rank asc ");
		$arrGroupInfo = array();
		foreach( $resMgs as $k=>$v){
			$arrGroupInfo[$v['mgs_uid']] = $v['mgs_name'];
		}

?>

		<form action="_individual.pro.php" name="frm" id="frm"  method="post" >
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<input type="hidden" name="_mode" value="<?=$_mode?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
		<input type="hidden" name="tempID" value="<?=$row['in_id']?>"> <?php // -- ajax 모드 ?>
		<?php if($_mode == 'modify') { ?>
		<input type="hidden" name="_id" value="<?=$row['in_id']?>">
		<?php } ?>

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>회원 기본정보</strong><!-- 메뉴얼로 링크 --> </div>


		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">회원등급</th>
						<td>
							<?php echo _InputSelect( "_mgsuid" , array_keys($arrGroupInfo) , $row['in_mgsuid'] , " class='' " , array_values($arrGroupInfo), '' ); ?>
						</td>
						<th>승인</th>
						<td>
							<?php echo _InputRadio( '_auth' , array('Y','N'), (in_array($row['in_auth'], array('Y','N')) == false ? 'N':$row['in_auth']) , '' , array('승인', '미승인') , ''); ?>
						</td>
					</tr>

					<tr>
						<th class="ess">아이디</th>
						<td>
							<?php if($_mode == 'add'){ ?>
							<input type="text" name="_id" class="design" style="" value="" />
							<?php }else{echo $row['in_id']; }?>

						</td>
						<th class="ess">이름</th>
						<td>
							<input type="text" name="_name" class="design" style="" value="<?php echo $row['in_name'] ?>">
						</td>
					</tr>

					<tr>
						<th <?php $_mode == 'add' ? 'class="ess"':''  ?>>비밀번호</th>
						<td>
							<input type="password" name="_pw" class="design" style="width:130px;" value="" />
							<?php echo _DescStr('4자리 이상 입력해 주세요.', 'black'); ?>
						</td>
						<th <?php $_mode == 'add' ? 'class="ess"':''  ?>>비밀번호 확인</th>
						<td>
							<input type="password" name="_rpw" class="design" style="width:130px;" value="">
							<?php echo _DescStr('입력하신 비밀번호를 다시한번 입력해 주세요.', 'black'); ?>
						</td>
					</tr>

					<tr>
						<th>성별</th>
						<td>
						<?php echo _InputRadio( '_sex' , array('M','F'), (in_array($row['in_sex'], array('M','F')) == false ? 'M':$row['in_sex']) , '' , array('남성', '여성') , '');?>
						</td>
						<th>생년월일</th>
						<td>
							<input type="text" name="_birth" class="design js_pic_day" style="width:90px;" value="<?=rm_str($row['in_birth']) < 1 ? '': $row['in_birth'] ?>" readonly>
						</td>
					</tr>

					<tr>
						<th class="ess">휴대폰번호</th>
						<td>
							<input type="text" name="_tel2" class="design" value="<?=$row['in_tel2']?>" />
							<div class="clear_both"></div>
							<?php echo _InputRadio( '_smssend' , array('Y','N'), (in_array($row['in_smssend'], array('Y','N')) == false ? 'N':$row['in_smssend']) , '' , array('수신허용', '수신거부') , ''); ?>
						</td>
						<th class="ess">이메일</th>
						<td>
							<input type="text" name="_email" class="design" value="<?=$row['in_email']?>">
							<div class="clear_both"></div>
							<?php echo _InputRadio( '_emailsend' , array('Y','N'), (in_array($row['in_emailsend'], array('Y','N')) == false ? 'N':$row['in_emailsend']) , '' , array('수신허용', '수신거부') , ''); ?>

						</td>
					</tr>

					<?php // ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----?>
					<tr>
						<th>주소</th>
						<td>
							<input type="text" name="_zonecode" value="<?=$row['in_zonecode']?>" id="_zonecode" class="design t_center" style="width:70px" readonly>
							<a href="#none" onclick="new_post_view(); return false;" class="c_btn h28 black">우편번호 찾기</a>
							<div class="lineup-full">

								<input type="text" name="_address_doro" value="<?=$row['in_address_doro']?>" class="design" readonly id="_addr_doro">
								<input type="text" name="_address2" id="_addr2" class="design" style="" value="<?=$row['in_address2']?>" placeholder="나머지 주소">
							</div>
						</td>
						<th>지번 주소</th>
						<td>
							<input type="hidden" name="_zip1" id="_post1" value="<?=$row['in_zip1']?>" class="design t_center" style="width:50px">
							<input type="hidden" name="_zip2" id="_post2" value="<?=$row['in_zip2']?>" class="design t_center" style="width:50px">
							<div class="lineup-full">
								<input type="text" name="_address1" id="_addr1" class="design" style="" value="<?=$row['in_address1']?>" readonly placeholder="기본주소">
							</div>
							<div class="tip_box">
							<?php echo _DescStr('주소검색을 통해 자동으로 입력됩니다.', ''); ?>
							</div>
						</td>
					</tr>
					<?php // ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----?>

					<tr>
						<th>전화번호</th>
						<td colspan="3">
							<input type="text" name="_tel" class="design" value="<?=$row['in_tel']?>" />
						</td>
					</tr>

					<?php if($_mode == 'modify') { ?>
					<tr>
						<th>적립금</th>
						<td>
							<?php echo number_format($row['in_point']).'원';?>
						</td>
						<th>회원유형</th>
						<td>
							<?php echo $row['in_userlevel'] >= 9 ? '운영자':'일반회원';?>
						</td>
					</tr>

					<tr>
						<th>회원가입일</th>
						<td>
							<?php echo $row['in_rdate'];?>
						</td>
						<th>가입경로</th>
						<td>
							<?php echo $row['in_join_ua']; ?>
						</td>
					</tr>

					<tr>
						<th>최종로그인</th>
						<td>
							<?php echo $row['in_ldate'];?>
						</td>
						<th>방문횟수</th>
						<td>
							<?php echo number_format($rowVisit['cnt']);?>
						</td>
					</tr>

					<tr>
						<th>수신동의/거부 변경일</th>
						<td>
							<?php echo (rm_str($row['m_opt_date']) < 1 ? '없음':$row['m_opt_date']);?>
						</td>
						<th>소셜연동</th>
						<td>
							<?php echo _InputCheckbox( 'arrSnsType' , array('F','K','N'), (($arrSnsType)) , ' disabled ' , array('페이스북','카카오톡','네이버') , '') ?>
						</td>
					</tr>

					<?php } ?>

				</tbody>
			</table>
		</div>

		<?php // -- 사업자 정보 -- 회원의 가입타입이 사업자일경우 -- {{{ ?>

		<?php // -- 사업자 정보 -- 회원의 가입타입이 사업자일경우 -- }}} ?>

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>환불계좌정보</strong></div>
		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">환불은행</th>
						<td>
							<select name="_cancel_bank" class="">
							<?php	foreach($ksnet_bank as $k=>$v) { ?>
							<option value="<?=$k?>" <?=$row[in_cancel_bank]==$k?'selected':''?>><?=$v?></option>
							<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="ess">환불계좌번호</th>
						<td>
							<input type="text" name="_cancel_bank_account" class="design" value="<?=$row['in_cancel_bank_account']?>"/>
						</td>
					</tr>
					<tr>
						<th class="ess">환불예금주명</th>
						<td>
							<input type="text" name="_cancel_bank_name" class="design" value="<?=$row['in_cancel_bank_name']?>"/>
						</td>
					</tr>
				</tbody>
			</table>
		</div>


		<?php if( $_mode == 'modify') { ?>

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>구매정보</strong></div>
		<?php
			if(!$pass_limit) {$pass_limit = 99999;}
			$listmaxcount = $pass_limit ;
			if( !$listpg ) {$listpg = 1 ;}
			$count = $listpg * $listmaxcount - $listmaxcount;

			//  o_canceled='N' and o_paystatus='Y'
			$resOrder = _MQ_assoc("select * from smart_order where 1 and  o_canceled='N' and o_paystatus='Y' and  o_mid ='".$row[in_id]."' order by o_rdate desc ");
			$TotalCount = count($resOrder);
			$Page = ceil($TotalCount / $listmaxcount);
		?>
		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_list">
			<table class="table_list">
				<colgroup>
					<col width="70"/><col width="150"/><col width="150"/><col width="*"/><col width="90"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO</th>
						<th scope="col">구매일시</th>
						<th scope="col">주문번호</th>
						<th scope="col">주문상품정보</th>
						<th scope="col">상세보기</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($resOrder as $k=>$v) {
						$_num = $TotalCount - $count - $k ; // NO 표시


						// -- 모바일구매여부
						$printAgent = $v['mobile'] == 'Y' ? '<span class="c_tag h18 mo">MO주문</span>':'<span class="c_tag h18 t3 pc">PC주문</span>';

						// -- 주문상품 정보를 가져온다.
						$arrPrintProduct =  array();
						$printAddPrdocut = '';
						$resOrderProduct = _MQ_assoc("select op_pcode, op_pname, op_pouid from smart_order_product where op_oordernum = '".$v['o_ordernum']."' group by op_pcode ");
						if(count($resOrderProduct) > 0){
							if(count($resOrderProduct) > 1){ $printAddPrdocut = '이외 '.(count($resOrderProduct)-1).'개'; }
							$arrPrintProduct[] = '<div class="title bold">'.$resOrderProduct[0]['op_pname'].' '.$printAddPrdocut.'</div>';

						}else{
							$arrPrintProduct[] = '상품정보가 없습니다.';
						}

						$printProduct = implode("",$arrPrintProduct);

						// <a href="" class="c_btn h22">상세보기</a>

						$printBtn = '<a href="_order.form.php?_mode=modify&_ordernum='.$v['o_ordernum'].'" class="c_btn h22" target="_blank">상세보기</a>';

				?>
					<tr>
						<td><?=$_num?></td>
						<td><?=$v['o_rdate']?></td>
						<td><?=$v['o_ordernum']?></td>
						<td>
							<?php echo $printAgent; ?>
							<!-- 상품정보 -->
							<div class="order_item">
								<!-- 상품명 -->
								<?php echo $printProduct?>
							</div>
						</td>
						<td>
							<div class="lineup-vertical">
								<?php echo $printBtn; ?>
							</div>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<?php	if(count($resOrder) < 1 ) {   ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">구매내역이 없습니다.</div></div>
			<?php } ?>

		</div>


		<!-- ●단락타이틀 -->
		<div class="group_title" data-name="view-form"><strong>접속정보</strong></div>
			<div class="data_list">
				<table class="table_list">
					<colgroup>
						<col width="60"/><col width="140"/><col width="110"/><col width="70"/><col width="140"/>
						<col width="*"/>
						<col width="160"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col" class="colorset" >NO.</th>
							<th scope="col" class="colorset" >유입일</th>
							<th scope="col" class="colorset" >IP</th>
							<th scope="col" class="colorset" >DEVICE</th>
							<th scope="col" class="colorset" >검색어</th>
							<th scope="col" class="colorset" >유입경로</th>
							<th scope="col" class="colorset" >BROWSER</th>
						</tr>
					</thead>
					<tbody class="view-visit-list">

					</tbody>
			</table>
			<div class="common_none view-visit-list-none" style="display:none;"><div class="no_icon"></div><div class="gtxt">접속 정보가 없습니다.</div></div>
			<div class="paginate view-visit-paginate"></div>
		</div>


		<?php } ?>


		<?php echo _submitBTN('_individual.list.php'); ?>

		</form>



	<div class="ajax-data-box" data-visit-ahref=""></div>
	<script>
	$(document).ready(function(){	viewVisitList();})
	$(document).on('click','.paginate .lineup a',function(){
		var ahref = $(this).attr('href');
		var hasHit = $(this).hasClass('hit');
		$('.ajax-data-box').attr('data-visit-ahref',ahref);
		if(hasHit == true){ return false; }
		else{
			viewVisitList();
		}

		var $root = $('html, body');
		$root.animate({
			scrollTop: $('[data-name="view-form"]').offset().top - 10
		}, 500, 'easeInOutCubic');
		return false;
	});


	// 접속정보를 가져온다.
	function viewVisitList()
	{

		var _id = $('[name="tempID"]').val();
		var ajaxMode = 'viewVisitList';
		var ahref = $('.ajax-data-box').attr('data-visit-ahref');
		var result = $.parseJSON($.ajax({
			url: "_individual.ajax.php",
			type: "get",
			dataType : "json",
			data: {_id :_id , ajaxMode : ajaxMode , ahref : ahref},
			async: false
		}).responseText);

		if(result == undefined){ return false; }
		if( (result.cnt*1) > 0) {
			$('.view-visit-list').html(result.html);
		}else{
			$('.view-visit-list-none').show();
		}

		// -- 페이지네이트
		$('.view-visit-paginate').html(result.paginate);

	}

	// 폼 유효성 검사
	$(document).ready(function(){

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");


		$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					_id: { required: true }
					,_name: { required: true }
					<?php if($mode == 'add'){ ?>
					,_pw: { required: true} }
					,_rpw: { required: true}
					<?php } ?>
					,_htel: { required: true }
					, _email: { required : true, email_check: true }
			},
			messages: {
					_id : { required: '아이디를 입력해 주세요.' }
					,_name : { required: '운영자 이름을 입력해 주세요.' }
					<?php if($mode == 'add'){ ?>
					,_pw : { required: '비밀번호를 입력해 주세요.' }
					,_rpw : { required: '비밀번혹 확인을 입력해 주세요.' }
					<?php } ?>
					,_htel : { required: '휴대폰번호를 입력해 주세요.' }
					, _email: { required : "이메일을 입력해 주세요.", email_check: "유효하지 않은 이메일 주소입니다" }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.

				if( $('input[name="_pw"]').val() != $('input[name="_rpw"]').val()){
					alert("입력하신 비밀번호와 확인 비밀번호가 일치하지 않습니다. ");
					return false;
				}

				form.submit();
			}
		});

	});

	</script>




<?php
	// 주소찾기 - 우편번호찾기 박스
	include_once OD_ADDONS_ROOT."/newpost/newpost.search.php";
	include_once('wrap.footer.php');
?>