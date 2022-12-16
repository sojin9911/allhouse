<?php # 수신거부 고객을 포함하여 재발송 확인 ?> 

<!-- ● 일반 새창 팝업창 1100px -->
<div class="popup sms_chk_again_page" style="width:1000px; background-color: #fafafa; display: none;">

	<div class="pop_title"><strong>SMS 수신여부 재확인</strong></div>


	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">

		
			<!-- 엑셀일괄등록 열림 -->
			<div class="open_excel">
				<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
				<table class="table_form">	
					<colgroup>
						<col width="140"/><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<th>스팸안내사항</th>
							<td>
								<div class="tip_box">
									<div class="c_tip black">발송되는 내용에 광고성, 또는 이벤트성 문구가 삽입되어있는지 확인해 주세요.(새해인사/생일축하/기념일/축하문자/안부인사 등등)</div>
									<div class="c_tip black">주문관련 발송이라 하더라도 발송내용에는 광고성 또는 이벤트성 문구가 삽입되어선 안됩니다.</div>
									<div class="c_tip black">야간의 경우 21시 부터 다음날 8시 전까지 는 별도의 고객 수신동의 가 있어야 합니다.</div>
								</div>
							</td>
						</tr>
						<tr>
							<th>알림</th>
							<td>
								발송대상자 중 수신거부 회원이 [<strong class="_deny_cnt"></strong>명] 포함되어있습니다. <br>
								수신거부 회원을 제외하고 발송하시겠습니까?
							</td>
						</tr>						
					</tbody>
				</table>
			</div>
			

		</div>
		<!-- / 리스트 컨트롤영역 -->
		




		<div class="tip_box">
			<div class="c_tip">스팸안내사항을 반드시 읽어 보신 후 발송을 해주세요.</div>
			<div class="c_tip _type_deny">제외발송을 클릭 시 SMS 수신여부가 수신상태인 회원에게만 발송이 됩니다.</div>			
			<div class="c_tip _type_deny">제외발송을 클릭 시 SMS 수신여부가 수신상태인 회원에게만 발송이 됩니다.</div>
		</div>


	</div>

	<!-- 가운데정렬버튼 -->
	<div class="c_btnbox _type_deny">
		<ul>
			<li><a href="#none" onclick="sms_chk_send('deny'); return false;" class="c_btn h34 black">제외발송</a></li>
			<li><a href="#none" onclick="sms_chk_send('allow'); return false;" class="c_btn h34 black">포함발송</a></li>
			<li><a href="#none" onclick="return false;" class="c_btn h34 black line normal close">닫기</a></li>
		</ul>
	</div>



	<!-- 가운데정렬버튼 -->
	<div class="c_btnbox _type_allow">
		<ul>
			<li><a href="#none" onclick="sms_chk_send('allow'); return false;" class="c_btn h34 black">발송</a></li>
			<li><a href="#none" onclick="return false;" class="c_btn h34 black line normal close">닫기</a></li>
		</ul>
	</div>


</div>

<script>
    function sms_chk_again_view(_deny_cnt){
        if(_deny_cnt > 0){ // 제외회원이 있을경우
            $('._type_deny').show();
            $('._type_allow').hide();
            $('._deny_cnt').text(_deny_cnt);
        }else{ // 없을경우 :: 일반발송
            $('._type_deny').hide();
            $('._type_allow').show();
        }

        $('.sms_chk_again_page').lightbox_me({centered: true, closeEsc: true,onLoad: function() { }});
    }

    // 타입정의 :: dney :: 제외발송, allow :: 포함 및 일반발송 
    function sms_chk_send(_type)
    {
    		_mode =  $("input[name=_mode]").val();

        if(_type  == 'deny'){       // 제외처리, 포함 X 처리 일 시
            $.ajax({
                url: "/addons/080deny/_inc.reconfirm.pro.php",
                type: "POST",
                dataType:'json',
                data: "_mode="+_mode+"&_action=send&_type=deny&pass_var=" + $("form[name=frm]").serialize(),
                success: function(data){
                    if(_mode == 'sms_chk_again_select'){ // 선택회원 발송이라면  
                        if(data.rst == 'success'){ // 성공이라면
                        				
                            for(i=0;i<data.deny_arr.length; i++){ // 제외항목 체크 해제
                               // $('.class_cellular[value="'+data.deny_arr[i]+'"]').prop('checked',false);
                                $('.in-id[value="'+data.deny_arr[i]+'"]').prop('checked',false);
                            }
                         		document.frm.submit();
                            return false;                           
                        }else{
                            alert(data.msg);
                            return false;
                        }

                    }else{ // 검색회원 발송S
                            if(data.rst == 'success'){
                                $("input[name=_search_que]").val(data._search_que); // 검색쿼리를 갱신
                                document.frm.submit();
                                return false;
                            }else{ // 검색회원이 없을 시
                                alert(data.msg);
                                return false;
                            }
                    }
                }
            });
        }else{
                document.frm.submit();
        }

    }
</script>

<?php
    // ---------------------------- 수신거부 고객을 포함하여 발송시 재확인 ----------------------------
?>