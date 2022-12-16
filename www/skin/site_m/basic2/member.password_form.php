<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section ">

    <form name="frm_cpw" class="frm_cpw" action="<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php" method="post" target="common_frame">
        <input type="hidden" name="_mode" value="password_change">
        <input type="hidden" name="_site_access" value="<?php echo sha1($_id); ?>">
        <!-- ◆비밀번호 변경안내 -->
        <div class="c_complete my_password">
            <div class="complete_box ">

                <div class="tit">비밀번호를 변경을 안내해 드립니다.</div>
                <div class="sub_txt">
                    회원님께서는 장기간 비밀번호를 변경하지 않고, 동일한 비밀번호를 사용 중이십니다.<br/>
                    정기적인 비밀번호 변경으로 회원님의 개인정보를 보호해 주세요.
                </div>

            </div>
            <div class="c_form">
                <table>
                    <tbody>
                    <tr>
                        <!-- 필수일 경우 th에 ess 클래스 추가 -->
                        <th class="ess"><span class="tit ">현재 비밀번호</span></th>
                        <td>
                            <div class="input_box">
                                <input type="password" name="_pw" class="input_design" placeholder="" autocomplete="new-password"/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- 필수일 경우 th에 ess 클래스 추가 -->
                        <th class="ess"><span class="tit ">새 비밀번호</span></th>
                        <td >
                            <div class="input_box">
                                <input type="password" name="_cpw" class="input_design" placeholder="" autocomplete="new-password" />
                                <div class="tip_txt ">
                                    <?php
                                    $pw_length_text = '숫자, 영문';
                                    if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) $pw_length_text .= '(대문자 '.$siteInfo['join_pw_up_length'].'자 이상 포함)';
                                    if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) $pw_length_text .= ', 특수문자(~!@#$%^&*()_+|<>?:{} 중 '.$siteInfo['join_pw_up_length'].'자 이상)';
                                    if($pw_max_length > 0) $pw_length_text .= '을 포함하여 '.$pw_min_length.'자~'.$pw_max_length.'자 이내로 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
                                    else $pw_length_text .= '을 포함하여 '.$pw_min_length.'자 이상 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
                                    echo $pw_length_text;
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="ess"><span class="tit ">새 비밀번호 확인</span></th>
                        <td >
                            <div class="input_box">
                                <input type="password" name="_rcpw" class="input_design" placeholder="" autocomplete="new-password" />
                                <div class="tip_txt ">동일하게 다시 한 번 입력해주세요.</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="c_btnbox ">
                <ul>
                    <li><a href="#none" onclick="$(this).closest('form').submit(); return false;" class="c_btn h55 color">지금 변경하기</a></li>
                    <li><a href="/" class="c_btn h55 black">다음에 변경하기</a></li>
                </ul>
            </div>
        </div>
        <!-- /비밀번호 변경안내 -->
    </form>


    <!-- ◆페이지 이용도움말 -->
    <div class="c_user_guide">
        <div class="guide_box">
            <dl>
                <dt>비밀번호 변경 안내사항</dt>
                <dd><strong><?php echo htmlspecialchars($siteInfo['s_adshop']); ?></strong>에서는 소중한 개인정보 보호를 위해 비밀번호 변경안내 정책이 시행되고 있습니다.</dd>
                <dd>비밀번호를 변경하신 지 <strong><?php echo number_format($siteInfo['member_cpw_period']); ?>개월</strong>이 지난 경우에 아래과 같이 변경안내를 드리고 있습니다.</dd>
                <dd>"다음에 변경하기" 버튼을 눌러 변경을 연기하시면 다음에 다시 안내해 드립니다.</dd>
                <dd>조금 불편하시더라도, <strong>지금 비밀번호를 변경하시면</strong> 더욱 안전한 웹사이트 이용이 가능합니다.</dd>
            </dl>
        </div>
    </div>
</div>
<!-- /공통페이지 섹션 -->


<script type="text/javascript">
    $(document).ready(function() {
        // - 대문자 검증
        jQuery.validator.addMethod('upper_alpha', function(value, element, length) {
            var pattern = /[A-Z]/;
            var mc = value.match(pattern);
            if(mc == null) return this.optional(element) || false;
            return this.optional(element) || (mc.length < length?false:true);
        }, '비밀번호에는 대문자가 {0}개 이상 포함되어야합니다');

        // - 특수문자 검증
        jQuery.validator.addMethod('special_string', function(value, element, length) {
            var pattern = /[~!@#$%^&*()_+|<>?:{}]/;
            var mc = value.match(pattern);
            if(mc == null) return this.optional(element) || false;
            return this.optional(element) || (mc.length < length?false:true);
        }, '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 {0}개 이상 포함되어야합니다');

        $('.frm_cpw').validate({
            ignore: 'input[type=text]:hidden',
            rules: {
                _pw: { required : true }
                , _cpw: {
                    required : true
                    , minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
                    <?php if($siteInfo['join_id_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
                    , maxlength: <?php echo ((int)$siteInfo['join_id_limit_max']); ?>
                    <?php } ?>
                    <?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
                    , upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
                    <?php } ?>
                    <?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
                    , special_string: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
                    <?php } ?>
                }
                , _rcpw: {
                    required : true
                    , minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
                    <?php if($siteInfo['join_id_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
                    , maxlength: <?php echo ((int)$siteInfo['join_id_limit_max']); ?>
                    <?php } ?>
                    <?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
                    , upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
                    <?php } ?>
                    <?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
                    , special_string: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
                    <?php } ?>
                    , equalTo: '.js_rcpw'
                }
            },
            messages: {
                _pw: { required : '현재 비밀번호를 입력해주세요' }
                , _cpw: {
                    required : '새 비밀번호를 입력해주세요'
                    , minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
                    <?php if($siteInfo['join_id_limit_max'] > 4) { ?>
                    , maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_id_limit_max']); ?>자 까지만 입력가능합니다'
                    <?php } ?>
                    <?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
                    , upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
                    <?php } ?>
                    <?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
                    , special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
                    <?php } ?>
                }
                , _rcpw: {
                    required : '새 비밀번호 확인을 입력해주세요'
                    , minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
                    <?php if($siteInfo['join_id_limit_max'] > 4) { ?>
                    , maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_id_limit_max']); ?>자 까지만 입력가능합니다'
                    <?php } ?>
                    <?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
                    , upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
                    <?php } ?>
                    <?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
                    , special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
                    <?php } ?>
                    , equalTo: '비밀번호가 일치하지않습니다'
                }
            }
        });
    });
</script>