<?php
	if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
	header('Content-Type: text/xml; charset=UTF-8'); // xm 해더 선언

	echo rss_create($cuid);


?>
<?php
	// 입점업체의 배송비 정책을 가져온다. => $com_id : 입점업체 고유아이디
	function rss_get_com_delivery($com_id)
	{
		$cp_data =_MQ("SELECT cp_delivery_price , cp_delivery_freeprice  FROM smart_company where cp_id = '".$com_id."' AND cp_delivery_use = 'Y' ");

		$com_delivery = array();
		if(count($cp_data) > 0){ // 업체 배송비 정책이 사용일 시
			$com_delivery['price'] = $cp_data['cp_delivery_price'];
			$com_delivery['free_price'] = $cp_data['cp_delivery_freeprice'];
		}else{ // 업체 배송지 정책이 미사용일 시
			$com_delivery['price'] = $siteInfo['s_delprice']; // 기본 배송비
			$com_delivery['free_price'] = $siteInfo['s_delprice_free']; // 무료배송비
		}

		return $com_delivery;
	}

	// 상품의 카테고리의 정보를 가져온다 => $pcode : 상품코드
	function rss_get_category_info($pcode){

		$que = "
			select
				pct.* , ct3.c_name as ct3_name , ct3.c_uid as ct3_catecode, ct2.c_name as ct2_name ,  ct2.c_uid as ct2_catecode , ct1.c_name as ct1_name , ct1.c_uid as ct1_catecode
			from smart_product_category as pct
			left join smart_category as ct3 on (ct3.c_uid = pct.pct_cuid and ct3.c_depth=3)
			left join smart_category as ct2 on (substring_index(ct3.c_parent , ',' ,-1) = ct2.c_uid and ct2.c_depth=2)
			left join smart_category as ct1 on (substring_index(ct3.c_parent , ',' ,1) = ct1.c_uid and ct1.c_depth=1)
			where
				pct.pct_pcode='". $pcode ."'
				order by pct.pct_uid asc
		";
		$r = _MQ($que);

		$category_info[depth1_catename] = $r['ct1_name'];
		$category_info[depth2_catename] = $r['ct2_name'];
		$category_info[depth3_catename] = $r['ct3_name'];

		$category_info[depth1_catecode]  = $r['ct1_catecode'];
		$category_info[depth2_catecode]  = $r['ct2_catecode'];
		$category_info[depth3_catecode]  = $r['ct3_catecode'];

		return $category_info;
	}


	// 카테고리에 해당하는 상품들을 가져온다. => $cuid : 해당카테고리의 1차 고유아이디
	function rss_get_product_list($cuid)
	{

		$product_list = _MQ_assoc("select p.*, c.c_name from smart_category as c
		inner join smart_product_category as pct on (pct.pct_cuid = c.c_uid )
		inner join smart_product as p on (p.p_code = pct.pct_pcode)
		where (find_in_set(".$cuid.",c.c_parent) or c.c_uid = '".$cuid."')
		and p.p_view = 'Y'
		and p.p_stock > 0
		group by pct.pct_pcode
		order by p_rdate desc, p.p_idx asc
		 ");

		return $product_list;
	}

	// 1차 카테고리 이름을 가져온다
	function rss_get_category_name($cuid)
	{
		$cate_name =  _MQ("select c_name from smart_category where c_uid = ".$cuid." and c_depth=1 ");
		return $cate_name['c_name'];
	}


	// rss 데이터 생성
	function rss_create($cuid)
	{
		global $siteInfo; // 전역변수 선언

		$product_list = rss_get_product_list($cuid); // 1차 카테고리 해당되는 상품들을 모두 가져온다.
		$product_list = count($product_list) > 0 ? $product_list : false;
		$cate_name = rss_get_category_name($cuid);


		$content =  "<?xml version='1.0' encoding='UTF-8'?>";
		$content .= "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>";
		$content .= "<channel>";
		$content .= "<title>[".$siteInfo['s_adshop']."] RSS - ".$cate_name."</title>";
		$content .= "<link>http://".$_SERVER['HTTP_HOST']."/addons/rss/feeds.php?".$_SERVER['QUERY_STRING']."</link>";
		$content .="<description>".$siteInfo['s_ademail']."</description>";
		//$content .="<pubDate>".date('D, d M Y H:i:s')."</pubDate>";
		$content .="<pubDate>".date('r',time())."</pubDate>";
		$content .="<generator>Hyssence</generator>";
		$content .="<managingEditor>".$siteInfo['s_adshop']."</managingEditor>";


		if($product_list <> false) {  // 상품들이 있다면
			 foreach($product_list as $key=>$data) {
				// 상품 명
				//$p_name = cutstr($data['p_name'],95);
				$p_name = cutstr($data['p_name']." - ".$data['p_subname'],95);
				$category = rss_get_category_info($data['p_code']);
				$p_price = $data['p_price']; // 상품가격
				$p_point_per = $data['p_point_per']; // 적립률
				$p_content = htmlspecialchars($data['p_content']); // 상품 상세설명
				$link = "http://".$_SERVER[HTTP_HOST]."/?pn=product.view&amp;pcode=".$data[p_code];
				$pub_date = $data['p_mdate'] <> NULL  ? strtotime($data['p_mdate']):strtotime($data['p_rdate']);


				$content .= "<item>";
				$content .= "<title>".$p_name."</title>";
				$content .= "<link>".$link."</link>";
				//$content .= "<description><![CDATA[".$p_content."]]></description>";
				$content .= "<description>".$p_content."</description>";
				$content .= "<category>".$category['depth1_catename']."</category>";
				$content .= "<category>".$category['depth2_catename']."</category>";
				$content .= "<category>".$category['depth3_catename']."</category>";
				//$content .= "<author>".$siteInfo['s_adshop']."</author>";
				$content .= "<guid>".$link."</guid>";
				//$content .= "<comments>http://b.redinfo.co.kr/103#entry103comment</comments>";
				$content .="<pubDate>".date('r',$pub_date)."</pubDate>";
				$content .= "</item>";




			 }
		}else{

		}
		 $content .= '</channel>';
		 $content .= '</rss>';

		 return $content;
	}



?>


