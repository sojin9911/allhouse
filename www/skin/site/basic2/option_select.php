<?php

	// ------------------------- 옵션 설정 -------------------------
	// 옵션이 normal 형일 경우 처리
	//			옵션형태 : normal , color , size

	// 다음 차수의 옵션 형태 추출
	$app_option_depth_type = "p_option" . $depth_next . "_type";

	if($r[$app_option_depth_type] == 'normal') {

		// 재고없이 옵션만 추출
		if( ( str_replace("depth","",$r['p_option_type_chk']) - $depth ) > 1 ) {
			foreach( $sres as $k=>$sr ){
				$str_option .= "
					<li><label><a href='javascript:void(0);' onclick=\"option_select_tmp('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."','".$sr['po_poptionname']."','".$r['p_code']."')\" class='option'>
						<span class='tx'>".$sr['po_poptionname']."</span>
					</a></label></li>";
		   }
		}

		// 재고 추출
		else {
            if (count($sres) > 1) {
                $gomi = "";
                foreach( $sres as $k=>$sr ){
                    $gomi_link .= "option_select_tmp('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."','".$sr['po_poptionname']."','".$r['p_code']."');";
                }

                $str_option .= "
                    <li><label><a href='javascript:void(0);' onclick=\"{$gomi_link}\" class='option'>
                        <span class='tx'>고미추가</span>
                    </a></label></li>";
            }

            foreach( $sres as $k=>$sr ){
                if ($poname == "색상") {
                    $str_option .= "
                        <li><label><a href='javascript:void(0);' class='option'>
                            <span class='tx'>".$sr['po_poptionname']."</span>
                        </a></label></li>";
                } else {
                    $str_option .= "
                        <li><label><a href='javascript:void(0);' onclick=\"option_select_tmp('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."','".$sr['po_poptionname']."','".$r['p_code']."')\" class='option'>
                            <span class='tx'>".$sr['po_poptionname']."</span>
                        </a></label></li>";
                }
            }
		}

		echo "
				" . $str_option . "
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
						<input type='radio' name='option". $depth ."' onclick=\"option_select_tmp('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."','".$sr['po_poptionname']."','".$r['p_code']."')\" /><span class='tx'><span class='shape'  style='" . $app_color_name . "'></span></span>
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
						<input type='radio' name='option". $depth ."' onclick=\"option_select_tmp('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."','".$sr['po_poptionname']."','".$r['p_code']."')\" /><span class='tx'>". $sr['po_poptionname'] ."</span>
					</label>
				</li>
			";
		}

		echo "<input type='hidden' name='_option_select" . $depth_next . "' ID='option_select" . $depth_next . "_id' value=''>";
	} // 1차 옵션이 size 형일 경우 처리

	// ------------------------- 옵션 설정 -------------------------

/*
<dl>
	<dt>[선택 01] 친환경 물로 붙이는 건식 풀바른 벽지 / 2차 옵션명 / 3차 옵션명</dt>
	<dd class="counter">
		<div class="counter_box">
			<input type="text" name="" class="updown_input" value="1">
			<span class="updown"><a href="" class="btn_up" title="더하기"></a><a href="" class="btn_down" title="빼기"></a></span>
		</div>
	</dd>
	<dd class="price"><span class="price"><strong>9,127,800</strong>원</span></dd>
	<dd class="delete"><a href="" class="btn_delete" title="옵션삭제"></a></dd>
</dl>
<dl>
	<dt><span class="add_tag">추가</span>[선택 01] 친환경 물로 붙이는 건식 풀바른 벽지 / 2차 옵션명 / 3차 옵션명</dt>
	<dd class="counter">
		<div class="counter_box">
			<input type="text" name="" class="updown_input" value="1">
			<span class="updown"><a href="" class="btn_up" title="더하기"></a><a href="" class="btn_down" title="빼기"></a></span>
		</div>
	</dd>
	<dd class="price"><span class="price"><strong>127,800</strong>원</span></dd>
	<dd class="delete"><a href="" class="btn_delete" title="옵션삭제"></a></dd>
</dl>
*/