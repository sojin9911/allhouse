<?php 

# 테이블 정의 
$patch_arr = array(); // 패치 배열 초기화  type : c => 칼럼, t => 테이블

/* smart_sms_set 테이블 데이터 추가 */
$patch_arr['smart_sms_set']['type'] = 'D';

/* smart_setup 테이블 칼럼추가 */
$patch_arr['smart_setup']['type'] = 'C';
$patch_arr['smart_setup']['list'] = array('s_daily','s_set_email_txt','s_deny_tel','s_deny_use','s_2year_opt_use','s_2year_opt_title','s_2year_opt_content_top');

/* smart_mailing_data 테이블 칼럼추가 */
$patch_arr['smart_mailing_data']['type'] = 'C';
$patch_arr['smart_mailing_data']['list'] = array('md_adchk');

/* smart_individual 테이블 칼럼추가 */
$patch_arr['smart_individual']['type'] = 'C';
$patch_arr['smart_individual']['list'] = array('m_opt_date');

/* smart_individual_sleep 테이블 칼럼추가 */
$patch_arr['smart_individual_sleep']['type'] = 'C';
$patch_arr['smart_individual_sleep']['list'] = array('m_opt_date');

/* smart_member_080_deny  테이블 추가 */
$patch_arr['smart_member_080_deny']['type'] = 'T';

/* smart_2year_opt_log  테이블 추가 */
$patch_arr['smart_2year_opt_log']['type'] = 'T';


?>
<form name='frm' method='post' action="/addons/action/_action.pro.php">
	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<td class="article">안내페이지</td>
						<td class="conts">
							<?=_DescStr("모비톡 080 수신거부 와 관련하여, 데이터베이스를 일괄적으로 수정 및 추가해 주는 시스템입니다.")?>
							<?=_DescStr("항목부분을 확인하여, 미확인 부분이 있을 시 실행 버튼을 누르셔서 진행해 주세요.")?>
							<?=_DescStr("이미 추가된 항목들은 제외하고 실행이 되기때문에, 미확인 항목이 하나라도 있을 시 실행 버튼을 누르셔서 진행하시면됩니다.")?>
						</td>
					</tr>
				</tbody> 
			</table>
	</div>
	<!-- 검색영역 -->

	<?=_submitBTNsub('수정')?>
	<script>
	$(document).ready(function(){
		$('.input_large').val('실행');
	})
	</script>

</form>

<div class="patch_wrap">
				<!-- 리스트영역 -->
				<div class="content_section_inner">
					

					<div class="ctl_btn_area">
					<?=_DescStr("아래 항목에서 데이터베이스 정보들이 추가되었는지 확인이 가능합니다.")?>
					<?=_DescStr("데이터베이스 패치에 실패할 시 DB패치 문서를 참고해 주세요.",'orange')?>
					</div>


					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset">분류</th>
								<th scope="col" class="colorset">테이블명</th>
								<th scope="col" class="colorset">항목</th>
							</tr>
						</thead> 
						<tbody> 
						<?php 
							foreach($patch_arr as $table=>$key){
								
								if($key['type'] == 'C'){ // 칼럼 추가라면
									$type = "<span class='type c'>Column</span>";
									$list = "<ul>";
									foreach($key['list'] as $k=>$v){
										$chk = is_column($table,$v);
										$list .="<li>".$v."&nbsp;&nbsp;<span class='list ".($chk == ture ? 'true':'false')."'></span></li>";
									}
									$list .= "</ul>";
								}else if($key['type'] == 'D'){
									$type = "<span class='type t'>Data</span>";
									$chk_data['2year_opt'] = _MQ("select ss_uid from smart_sms_set where ss_uid = '2year_opt'  ");
									$chk = $chk_data['2year_opt']['ss_uid'] == '2year_opt' ? true : false;;
									$list = "<span class='list ".($chk == true ? 'true':'false')."'></span>";

								}else{
									$type = "<span class='type t'>Table</span>";

									$chk = is_table($table);
									$list = $chk == true ? 'true':'false';
									$list = "<span class='list ".($chk == true ? 'true':'false')."'></span>";
								}


								echo "<tr>";
								echo "<td class='type'>".$type."</td>";
								echo "<td class='table'>".$table."</td>";
								echo "<td class='list'>".$list."</td>";
								echo "</tr>";

							}
					?>
					</tbody>
				</table>
			</div>
</div>

<style>
	.patch_wrap li{ margin:8px 0;}
	.patch_wrap span.type {  display: inline-block; padding:3px 6px; text-align: center; width:90px; font-weight: bold;}
	.patch_wrap span.type.c { background: #ddd; color: #333;}
	.patch_wrap span.type.t { background: #369; color: #fff;}

	.patch_wrap span.true:before{ content:'[추가완료]'; color:blue;}
	.patch_wrap span.false:before{ content:'[미확인]'; color:red;}

	.patch_wrap td.type{ width:15%;}
	.patch_wrap td.table{ width:15%;}
	.patch_wrap td.list{ width:60%;}
	
</style>


<?php 
# 테이블 검사함수
function is_table($Table) {

	$sql = " desc " . $Table;
	$result = @mysql_query($sql);

	if(@mysql_num_rows($result)) return true;
	else return false;
}	

// 칼럼 검사 합수 ($Table => 테이블명, $Field=>칼럼명 )
function is_column($Table, $Field) {

	if(is_table($Table) == false){
		return false;
	}

	$sql = ' show columns from ' . $Table . ' like \''.$Field.'\' ';
	$result = @mysql_query($sql);

	if(@mysql_num_rows($result)) return true;
	else return false;
}	
?>