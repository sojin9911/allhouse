<?php

	// 브랜드별 상품목록  --> 브랜드 메뉴박스

?>

	<!-- li6개 채워서 ul반복 -->
	<ul>
		<?php
			// 브랜드 초성 정보 합체
			$arr_prefix = array_merge($arr_prefix_kor , $arr_prefix_eng , array('기타'));
			$cnt = 0; // 줄바꿈을 위한 체크변수
			$arr_prev_prefix = array(); // 바로 앞 prefix 배열 저장
			foreach($arr_prefix as $k=>$v){
				if(sizeof($arr_brand_prefix[$v]) > 0){

					// 스펠링타이틀의 경우
					if(!($arr_prev_prefix[$v] > 0)) {
						echo ($cnt%6 == 0 && $cnt <> 0 ? '</ul><ul>' : ''); // 줄바꿈
						echo '<li class="spell"><span class="keyword" >'. $v .'</span></li>';
						$cnt ++;
						$arr_prev_prefix[$v]++;
					}

					// 일반 브랜드의 경우
					if(sizeof($arr_brand_prefix[$v]) > 0 ) {
						foreach($arr_brand_prefix[$v] as $sk=>$sv){
							echo ($cnt%6 == 0 && $cnt <> 0 ? '</ul><ul>' : ''); // 줄바꿈
							echo '<li><a href="/?pn=product.brand_list&uid='. $sk .'" class="btn">'. $sv .'</a></li>';
							$cnt ++;
						}
					}

				}
			}

			// 빈줄 채우기
			if($cnt%6 <> 0 ) {
				for( $i=0; $i<(6 - $cnt%6) ; $i++ ){
					echo '<li></li>';
				}
			}
		?>
	</ul>