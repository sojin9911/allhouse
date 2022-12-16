<?php


	// ------------------------- 옵션 설정 -------------------------
	// 옵션이 normal 형일 경우 처리
	//			옵션형태 : normal , color , size

	// 다음 차수의 옵션 형태 추출
	$app_option_depth_type = "p_option" . $depth_next . "_type";

	if($r[$app_option_depth_type] == 'normal') {


		$onchange_event = "";

		// 재고없이 옵션만 추출
		if( ( str_replace("depth","",$r['p_option_type_chk']) - $depth ) > 1 ) {
			$onchange_event = "option_select(" . $depth_next . ",'".$code."');";

			foreach( $sres as $k=>$sr ){
				$str_option .= "<option value='". $sr['po_uid'] ."'>".$sr['po_poptionname']."</option>";
		   }
		}

		// 재고 추출
		else {
			$onchange_event = "option_select_add('".$code."');";

			foreach( $sres as $k=>$sr ){
				$str_option .= "
					<option value='". $sr['po_uid'] ."'>
						".$sr['po_poptionname'] . ($sr['po_cnt'] > 0 ? ($isOptionStock ? " (잔여:".number_format($sr['po_cnt']).")" : null) . " / " . number_format($sr['po_poptionprice']) . "원"  : " (품절)")."
					</option>";
			}
		}

		echo "
			<select name=_option_select" . $depth_next . "  onchange=\"". $onchange_event ."\"  ID='option_select" . $depth_next . "_id'>
				<option value=''>상위옵션을 먼저 선택해주세요.(필수)</option>
				" . $str_option . "
			</select>
			<input type='hidden' name='_option_select" . $depth_next . "' ID='option_select" . $depth_next . "_id' value=''>
		";
	}
	// 옵션이 normal 형일 경우 처리


	// 1차 옵션이 color 형일 경우 처리
	else if($r[$app_option_depth_type] == 'color') {

		//<!-- 컬러는 #컬러값을 입력하거나 이미지를 등록할 수 있도록 / 이미지: [모바일]150 * 150, [PC]35 * 35  / 품절일 때 label에 none 추가 / 선택안되게 -->
		foreach( $sres as $k=>$sr ){

			// 품절여부
			$app_soldout_class = ( ( str_replace("depth","",$r['p_option_type_chk']) - $depth_next ) == 0 && $sr['po_cnt'] <= 0 ? 'none' : '');

			//색상 or 이미지
			$app_color_name = (
				$sr['po_color_type'] == 'img' ?
					'background-image:url(\'/upfiles/option/'.$sr['po_color_name'].'\');' :
					'background:' . $sr['po_color_name']
			);

			echo "
				<li>
					<!-- 옵션설명값 & 품절시 none 클래스 처리 -->
					<label title='" . $sr['po_poptionname'] . "' class='" . $app_soldout_class . "'>
						<input type='radio' name='_option_select". $depth ."' onclick=\"option_select_tmp2('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."' ,'".$r['p_code']."')\" /><span class='tx'><span class='shape'  style='" . $app_color_name . "'></span></span>
					</label>
				</li>
			";
		}

		echo "<input type='hidden' name='_option_select" . $depth_next . "' ID='option_select" . $depth_next . "_id' value=''>";
	} // 1차 옵션이 color 형일 경우 처리



	// 1차 옵션이 size 형일 경우 처리
	else if($r[$app_option_depth_type] == 'size') {

		//<!-- 품절일 때 label에 none 추가 / 선택안되게 -->
		foreach( $sres as $k=>$sr ){

			// 품절여부
			$app_soldout_class = ( ( str_replace("depth","",$r['p_option_type_chk']) - $depth_next ) == 0 && $sr['po_cnt'] <= 0 ? 'none' : '');

			echo "
				<li>
					<!-- 품절시 none 클래스 처리 -->
					<label class='" . $app_soldout_class . "'>
						<input type='radio' name='_option_select". $depth ."' onclick=\"option_select_tmp2('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."' ,'".$r['p_code']."')\" /><span class='tx'>". $sr['po_poptionname'] ."</span>
					</label>
				</li>
			";
		}

		echo "<input type='hidden' name='_option_select" . $depth_next . "' ID='option_select" . $depth_next . "_id' value=''>";
	} // 1차 옵션이 size 형일 경우 처리

	// ------------------------- 옵션 설정 -------------------------


/*
<!-- 선택하면 클래스값 if_selected -->
<div class="select if_selected">
	<select name="">
		<option value="0">옵션을 선택해주세요.(필수)</option>
	</select>
</div>
<div class="select">
	<select name="">
		<option value="0">상위옵션을 먼저 선택해주세요.(필수)</option>
	</select>
</div>
<div class="select">
	<select name="">
		<option value="0">상위옵션을 먼저 선택해주세요.(필수)</option>
	</select>
</div>
*/