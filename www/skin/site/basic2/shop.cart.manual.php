<style>
.c_cart_list .option ul {float:left; width:initial;}
.input_design {width:100% !important;}
.c_cart_list .cart_table th {text-align:center;}
.manual_btn {
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 5px 8px;
}
</style>
<script>
var room = 0;
function manual_fields(cm_brand, cm_item_name, cm_color, cm_size, cm_cnt) {
    if (!cm_brand) cm_brand = "";
    if (!cm_item_name) cm_item_name = "";
    if (!cm_color) cm_color = "";
    if (!cm_size) cm_size = "";
    if (!cm_cnt) cm_cnt = "1";

    room++;
    var objTo = document.getElementById("manual_fields")
    var divmemo = document.createElement("tr");
    divmemo.setAttribute("class", "removeclass" + "_" + room);
    if (room == 1) {
        btn_div = '<button onclick="manual_fields();" class="btn manual_btn" type="button"> 사이즈 추가 </button>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box"><input type="text" class="input_design" placeholder="" name="cm_brand[]"  value="'+cm_brand+'" required></div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box"><input type="text" class="input_design" placeholder="" name="cm_item_name[]"  value="'+cm_item_name+'" required></div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box"><input type="text" class="input_design" placeholder="예) 블루" name="cm_color[]"  value="'+cm_color+'" required></div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box"><input type="text" class="input_design" placeholder="예) L" name="cm_size[]"  value="'+cm_size+'" required style="text-transform: uppercase;"></div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box"><input type="text" class="input_design" placeholder="" name="cm_cnt[]"  value="'+cm_cnt+'" required></div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box">'+btn_div+'</div>';
    } else {
        btn_div = '<button class="btn manual_btn" type="button" onclick="remove_manual_fields('+room+');"> 삭제 </button>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box">&nbsp;</div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box">&nbsp;</div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box">&nbsp;</div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box"><input type="text" class="input_design" placeholder="예) L" name="cm_size[]"  value="'+cm_size+'"  required style="text-transform: uppercase;"></div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box"><input type="text" class="input_design" placeholder="" name="cm_cnt[]"  value="'+cm_cnt+'" required></div>';
        divmemo.innerHTML =  divmemo.innerHTML + '<td class="input_box">'+btn_div+'</div>';
    }

    objTo.appendChild(divmemo);
}

function remove_manual_fields(rid) {
    if ($("#cm_no"+rid).val()) {
    }
    $('.removeclass' +'_' + rid).remove();
}

function manual_submit() {
    f = document.mform;
    if (confirm("수기주문 추가 하시겠습니까?")) {
        var url = "/program/shop.cart.pro.php";
        var formData = new FormData($("#mform")[0]);
        $.ajax({url:url, type:"POST", cache:false, contentType : false, processData : false, data:formData,
            success:function(data) {
                if (data == "ok") {
                    window.location.reload();
                } else {
                    alert("[오류] "+data);
                }
            },
            error:function(xhr, status, exception) {}
       });
       return false;
    }


}
</script>
<div class="c_section c_shop view_section" style="margin-top:10px;">
	<div class="layout_fix" style="width:1100px;">

		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<div class="title">장바구니</div>
			<!-- 단계별 페이지 -->
			<div class="c_process hide">
				<ul>
					<!-- 해당 페이지 hit -->
					<li class="hit"><span class="num">01</span><span class="tit">장바구니</span></li>
					<li><span class="num">02</span><span class="tit">주문/결제</span></li>
					<li><span class="num">03</span><span class="tit">주문완료</span></li>
				</ul>
			</div>
		</div>
		<!-- /공통페이지 타이틀 -->

		<!--일반상품/수기상품 버튼 수기상품은 링크가 다른 곳으로 넘어가는 것 같아 모양만 만들었습니다-->
		<div class="cart_btn-div">
			<ul  class="cart_btn-ul">
				<li >
					<a href="/?pn=shop.cart.list" class="cart_btn-product">일반상품</a>
				</li>
				<li class="btn-bg-blue">
					<a href="/?pn=shop.cart.manual" class="cart_btn-order">수기주문</a>
				</li>
			</ul>
		</div>

        <div>
            <div class="c_cart_list" style="width:100%">
                <form method="post" id="mform" name="mform" enctype="multipart/form-data">
                <input type="hidden" name="mode" value="manual_add">
                <div class="cart_table c_form ">
                <table  id="manual_fields">
                    <colgroup>
                        <col width="200"><col width="*"><col width="110"><col width="100"><col width="100"><col width="130">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">브랜드명</th>
                            <th scope="col">상품명</th>
                            <th scope="col">컬러</th>
                            <th scope="col">치수정보</th>
                            <th scope="col">수량</th>
                            <th scope="col">비고</th>
                        </tr>
                    </thead>
                </table>
                </div>
                </form>
            </div>
            <script>
                manual_fields();
            </script>
            <div style="width:100%; height:50px; text-align:center">
                <a href="#none" onclick="manual_submit();return false;" class="c_btn h45  color" style="float:initial;">수기주문 추가</a>
            </div>
        </div>


		<div id="ID_cart_display">
			<?php
				// 장바구니 최초 접속 시 checkbox 모두 선택
				$app_cart_init = true;
				include OD_PROGRAM_ROOT."/shop.cart.manual.ajax.php";
			?>
		</div>


		<?php // LDD NPAY { ?>
			<?php
			$NPayTrigger = 'N';
			if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && sizeof($arr_cart) > 0) $NPayTrigger = 'Y';
			if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'test' && $nt == 'test' && sizeof($arr_cart) > 0) $NPayTrigger = 'Y';
			if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'test' && $nt != 'test') $NPayTrigger = 'N'; // 버튼+주문연동 작업
			if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'real') $NPayTrigger = 'Y'; // 버튼+주문연동 작업
			if(sizeof($arr_cart) <= 0) $NPayTrigger = 'N';

			// LCY : 네이버페이 사용유무 추가 : 2020-10-20
			$npayChk = _MQ("select count(*) as cnt from smart_cart as c inner join smart_product as p on(p.p_code = c.c_pcode) where  c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and p.npay_use = 'Y' ");
			if( $npayChk['cnt'] < 1){
				$NPayTrigger = 'N';
			}

			if($NPayTrigger == 'Y') {
			?>
			<div style="padding-bottom:40px; text-align:center;">
				<script type="text/javascript" src="//<?php echo ($siteInfo['npay_mode'] == 'test'?'test-':null); ?>pay.naver.com/customer/js/naverPayButton.js" charset="UTF-8"></script>
				<script type="text/javascript">
				//<![CDATA[
					function NPayBuy() {

						var cart_ck = 0;
						var pcode = '';
						$('.cls_code:checked').each(function(){

							cart_ck += 1;
							pcode += ','+$(this).val();
						});
						if(cart_ck <= 0) return alert('네이버페이로 구매하실 상품을 선택 하세요.');
						if(!confirm('상품중 네이버페이로 구매 가능 한 상품만 진행됩니다.\n계속하시겠습니까?')) return false;

						location.href = ('/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=cart');
						//var LocationUrl = '/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=cart';
						//window.open(LocationUrl, '', "scrollbars=yes, width=1200, height=500");
					}
					naver.NaverPayButton.apply({
						BUTTON_KEY: "<?php echo $siteInfo['npay_bt_key']; ?>", // 페이에서 제공받은 버튼 인증 키 입력
						TYPE: "A", // 버튼 모음 종류 설정
						COLOR: 1, // 버튼 모음의 색 설정
						COUNT: 1, // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
						ENABLE: "Y", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
						BUY_BUTTON_HANDLER: NPayBuy, // 구매하기 버튼
						"":"",
					});
				//]]>
				</script>
			</div>
			<?php } ?>
		<?php // } LDD NPAY ?>




		<?php if(count($get_pro_wish)>0){ ?>
			<!-- ◆ 공통영역의 상품리스트(내가 찜한 상품) / 로그인하면 나옴 / 로그인 전이나 찜한상품 없을때 div 숨김 -->
			<div class="c_item_list">
				<div class="c_other_item">
					내가 찜한상품
					<!-- 찜한상품 페이지로 이동 -->
					<a href="/?pn=mypage.wish.list" class="more">더보기</a>
				</div>

				<div class="item_list">
					<ul>
						<?php
						foreach($get_pro_wish as $k=>$v) {
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
			</div>
		<?php } ?>



		<?php if(count($get_pro_push) > 0){ ?>
			<!-- ◆ 공통영역의 상품리스트(다른 고객이 많이 찾은 상품) / 장바구니 상품 없을때만 노출 / 안나올땐 div 숨김 -->
			<div class="c_item_list hide">

				<div class="c_other_item">다른 고객이 많이 찾은 상품</div>

				<!-- ◆ 상품리스트 / 관리자에서 다른고객이 많이 찾은상품을 선택한 상품 8개 노출 -->
				<div class="item_list">
					<ul>
						<?php
						foreach($get_pro_push as $k=>$v) {
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
				<!-- / 상품리스트 -->

			</div>
			<!-- / 공통영역의 상품리스트 -->
		<?php } ?>


	</div>
</div>






<?php // 장바구니 스크립트 ?>
<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function(){
		set_cart_cnt();
		get_cart_price();
	});


	// 카트 -> 주문서작성
	function cart_submit() {
		$('input[name=mode]').val('select_buy');
		document.frm.action = '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php';
		document.frm.submit();
	}

	// === 비회원 구매 설정 kms 2019-06-25 ====
	// 카트 -> 컨펌 -> 주문서작성
	function cart_confirm_submit() {
		$('input[name=mode]').val('select_buy');
		if(confirm("구매하기는 로그인 후 이용하실 수 있습니다.\n\n로그인 페이지로 이동하시겠습니까?")){
			document.frm.action = '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php';
			document.frm.submit();
		}

	}
	// === 비회원 구매 설정 kms 2019-06-25 ====

	// - 개별상품 삭제 (클릭 시) ---
	function cart_delete(cuid) {
		if(confirm('정말 삭제하시겠습니까?')){
			$('input[name=mode]').val('select_onlydelete');
			$('input[name=cuid]').val(cuid);
			var param = $('form[name=frm]').serialize();

			$.ajax({
				url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
				data : param,
				type : 'POST',
				dataType : 'TEXT',
				success : function(data){
					switch(data){
						case 'ok' :
							// 성공 - 카트 reload
							cart_view();
							window.location.reload();
							break;
						default :
							alert('서버와의 통신중 오류가 발생하였습니다.');
							break;
					}
					return false;
				}
			});
		}
	}
	// - 선택상품 삭제 ---


	// - 선택상품 수량변경 --- type => up, down
    function cart_modify(cuid,type) {

        var cnt_org = $('#cart_cnt_'+cuid).val()*1; // 2019-07-24 SSJ :: 변경전 수량 저장
        var cnt = 0;

        if(type == 'up'){ // 수량 증가
            cnt = cnt_org + 1;
        }else if(type == 'down'){ // 수량 감소
            cnt = cnt_org - 1 ;
        }

        if(cnt <= 0){
            return false;
        }

        $('#cart_cnt_'+cuid).val(cnt);

        $('input[name=mode]').val('select_modify');
        $('input[name=cuid]').val(cuid);
        var param = $('form[name=frm]').serialize();

        $.ajax({
            url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
            data : param,
            type : 'POST',
            dataType : 'TEXT',
            success : function(data){
                // 2019-07-24 SSJ :: 에러 발생 시 수량 이전으로 회귀
                if(data != 'ok') $('#cart_cnt_'+cuid).val(cnt_org);

                switch(data){
                    case 'error1' :
                        alert('수정하실 수량은 0보다 커야 합니다.');
                        break;
                    case 'soldout' :
                        alert('장바구니 담긴 상품중 품절 된 상품이 있습니다.');
                        break;
                    case 'notenough' :
                        alert('해당 상품의 재고량이 부족합니다.');
                        break;
                    case 'ok' :
                        // 성공 - 카트 reload
                        cart_view();
                        break;
                    default :
                        alert('서버와의 통신중 오류가 발생하였습니다.');
                        break;
                }
                return false;
            }
        });

        //document.frm.action = "<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php";
        //document.frm.submit();
    }
    // - 선택상품 수량변경 ---


	// 장바구니 상품 비우기
	function cart_remove_all()
	{
		var chk_confirm = confirm('장바구니에 든 상품을 전부 비우시겠습니까?');

		if(chk_confirm == true){
			$(".cls_code:checkbox").prop('checked',true);
			cart_delete_submit();
		}
	}

	// - 선택상품 삭제 (옵션또한 모두 삭제) ---
	function cart_select_delete() {

		var chk_confirm = confirm('선택하신 상품을 장바구니에서 삭제 하시겠습니까?');
		if(!chk_confirm) return;

		cart_delete_submit();

	}
	// - 선택상품 삭제 --

	// - 선택상품 삭제 , 장바구니 비우기 공통 ----
	function cart_delete_submit(){
		if($('.cls_code:checkbox:checked').length == 0 ) {

			alert('1개 이상 선택해주시기 바랍니다.');
		}
		else {

			$('input[name=mode]').val('select_delete');
			var param = $('form[name=frm]').serialize();

			$.ajax({
				url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
				data : param,
				type : 'POST',
				dataType : 'TEXT',
				success : function(data){
					switch(data){
						case 'error1' :
							alert('1개이상 선택해주시기 바랍니다.');
							break;
						case 'ok' :
							// 성공 - 카트 reload
							cart_view();
							window.location.reload();
							break;
						default :
							alert('서버와의 통신중 오류가 발생하였습니다.');
							break;
					}
					return false;
				}
			});
		}
	}
	// - 선택상품 삭제 , 장바구니 비우기 공통 ----


	// -- 전체선택 / 반전 ----
	$(document).on('change', '.js_allcheck', function(){
		$parent = $(this).closest('.c_cart_list');
		if( $parent.find('.js_allcheck').val() == 'Y' ) {
			$parent.find(".cls_code").attr("checked",false);
			$parent.find('.js_allcheck').val('N').prop('checked',false);
		} else {
			$parent.find(".cls_code").attr("checked",true);
			$parent.find('.js_allcheck').val('Y').prop('checked',true);
		}

		set_cart_cnt();
		get_cart_price();
	});
	// -- 개별선택 ----
	$(document).on('change', '.cls_code', function(){
		$parent = $(this).closest('.c_cart_list');
		if($parent.find('.cls_code:not(:checked)').length > 0){
			$parent.find('.js_allcheck').val('N').prop('checked',false);
		}else{
			$parent.find('.js_allcheck').val('Y').prop('checked',true);
		}

		set_cart_cnt();
		get_cart_price();
	});


	// 카트 선택된 상품 수
	function set_cart_cnt(){
		//var total = $('.cls_code').length;
		//$('.glb_cart_cnt, .js_cart_cnt').text(total);
		var selected = $('.cls_code:checked').length;
		$('.js_cart_selected').text(selected);
	}


	// 카트 총 결제금액 계산
	function get_cart_price(){
		var cart_price = 0, cart_delivery = 0, cart_total = 0;
		$('.cls_code:checked').each(function(){
			cart_price += $('input[name=cart_price_'+$(this).val()+']').val()*1;
			cart_delivery += $('input[name=cart_delivery_'+$(this).val()+']').val()*1;
		});

		cart_total = cart_price + cart_delivery;

		$('#cart_price').text(String(cart_price).comma());
		$('#cart_delivery').text(String(cart_delivery).comma());
		$('#cart_total').text(String(cart_total).comma());
	}


	// 카트 불러오기 - ajax
	function cart_view(){
		var param = $('form[name=frm]').serialize();
		$.ajax({
			url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.manual.ajax.php',
			data : param,
			type : 'POST',
			dataType : 'HTML',
			success : function(data){
				$('#ID_cart_display').html(data);

				// 체크박스 초기화
				$('.cls_code').trigger('change');
				return false;
			}
		});
	}


</script>
<?php // 장바구니 스크립트 ?>



<script>
<?php if(is_login()) { // 로그인일 시 만   ?>
	function _cart_product_wish(sel,_pcode){

		if(_pcode == '' || _pcode == undefined){
			alert('상품 코드를 찾을 수 없습니다.');
			return false;
		}
		var _mode = 'all';
		var ajax_data = 'mode='+_mode+'&pcode='+_pcode+'&_datatype=json';

		$.ajax({
			url:"<?php echo OD_PROGRAM_URL; ?>/product.wish.pro.php",
			async:false,
			type:'POST',
			data:ajax_data,
			success: function(data){

				if($(sel).hasClass('if_wish') == true){
					 $(sel).removeClass('if_wish');
				}else{
					$(sel).addClass('if_wish');
				}

			},error: function(xhr, status, error){
					var error_confirm=confirm('데이터 전송 오류입니다. 확인을 누르시면 페이지가 새로고침됩니다.');
					if(error_confirm==true){
						document.location.reload();
					}
				}


			});

		return false;

	}
<?php }else{ ?>

	function _cart_product_wish()
	{
		alert('로그인 사용자만 이용할 수 있습니다.');

		location.href = "/?pn=member.login.form&_rurl=<?php echo urlencode("/?".$_SERVER[QUERY_STRING]); ?>";

		return false;
	}

<?php }?>
</script>