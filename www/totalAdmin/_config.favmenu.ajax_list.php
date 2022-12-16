<?php
	include_once("inc.php");
	/*
		$viewDepth = 노출될 페이지의 depth
		$viewUid = 보여질 페이지

	*/

	// reload 일경우
	if( $_mode == 'reload'){
		$rowFavMenu = _MQ("select *from smart_favmenu where fm_uid = '".$_uid."'  ");
		$viewDepth = $rowFavMenu['fm_depth'];
	}


	$que = "
		select *
		from smart_favmenu as fm
		left join smart_admin_menu as am on (fm.fm_menu = am.am_uid)
		left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
		where 1
			and fm.fm_appId = 'totaladmin'
			and fm.fm_admin = '". $siteAdmin['a_uid'] ."'
			and fm.fm_depth = '". $viewDepth ."'
			". ($viewDepth>1 ? " and fm_parent = '".$viewUid."' " : null) ."
		group by fm.fm_uid
		order by fm.fm_menuIdx asc
	";
	$resFavMenu = _MQ_assoc($que);

?>
	<!-- 카테고리 목록박스 -->

	<?php if(count($resFavMenu) > 0) { ?>
		<table class="category_list">
			<colgroup>
				<col width="50"/><col width="*"/><col width="105"/>
			</colgroup>
			<tbody>
			<?php
				foreach($resFavMenu as $k=>$v){
					unset($_error);

					$fmViewClass = $v['fm_view'] == 'Y' ? "blue line" : "gray"; // 노출여부에 따른 클래스명
					$fmViewName = $v['fm_view'] == 'Y' ? "노출" : "숨김"; // 노출여부에 따른 클래스명
					$fmOnclickEvt = $v['fm_depth'] < 3 ? " onclick=\"viewFavMenuList('".$v['fm_depth']."','".$v['fm_uid']."');\" style='cursor:pointer;' ":"";

					if($viewDepth > 1){
						// 메뉴 삭제 체크
						if(!$v['fm_menu']) $_error = '<div class="c_tip">메뉴가 선택 되지 않았습니다.</div>';
						else if($v['am_uid'] == '') $_error = '<div class="c_tip">메뉴가 삭제 되었습니다.</div>';
						// 메뉴 노출여부 체크
						else if($v['am_view'] <> 'Y') $_error = '<div class="c_tip">미사용중인 메뉴 입니다.</div>';
						// 메뉴 접근권한 체크
						else if($v['ams_uid'] == '' && is_master() == false) $_error = '<div class="c_tip">접근 권한이 없는 메뉴 입니다.</div>';
						// 메뉴 파일명이 등록되지 않음
						else if($v['am_link'] == '') $_error = '<div class="c_tip">메뉴에 연결된 페이지가 없습니다.</div>';
					}
			?>
				<!-- 클릭하면 아래 폼 나오고 hit : tr 온클릭 작업시 a링크 삭제가능 -->
				<tr class="favmenu-list-tr <?php echo (in_array($v['fm_uid'],array($locUid1,$locUid2)) == true ? 'hit':''); ?>"  data-depth= "<?php echo $v['fm_depth']; ?>" data-uid="<?php echo $v['fm_uid']; ?>"  >
					<td><span class="c_tag <?php echo $fmViewClass; ?> h22 t2"><?php echo $fmViewName; ?></span></td>
					 <td class="t_left ctg_name" <?php echo $fmOnclickEvt; ?>><span class="fr_tx"><?php echo $v['fm_menuName']; ?></span><?php echo $_error; ?></td>
					<td>
						<a href="#none" onclick="idxFavMenu('up','<?php echo $v['fm_uid']; ?>','<?php echo $v['fm_depth']; ?>'); return false;" class="c_btn h22 icon_up" title="위로"></a>
						<a href="#none" onclick="idxFavMenu('down','<?php echo $v['fm_uid']; ?>','<?php echo $v['fm_depth']; ?>'); return false;" class="c_btn h22 icon_down" title="아래로"></a>
						<a href="#none" onclick="viewFavMenuForm('modify','<?php echo $v['fm_depth']; ?>','<?php echo $v['fm_uid']; ?>'); return false;" class="c_btn h22 t2 scrollto" data-scrollto="view-form">수정</a>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php }else{ ?>

			<?php if($viewDepth != '1' && $viewUid == ''){ ?>
			<div class="category_before">메뉴분류를 먼저 선택해주세요.</div>
			<?php }else{ ?>
			<div class="category_before">등록된 메뉴가 없습니다.</div>
			<?php } ?>

		<?php } ?>
