<?
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
//$address = 'http://'.$_SERVER[HTTP_HOST].'/addons/rss'; // rss 제공페이지 주소
$address = $system['url'].'/addons/rss'; // rss 제공페이지 주소
$shop_name = $siteInfo['s_adshop']; // rss 타이틀 명

// 카테고리를 가져온다.

 $category_list = _MQ_assoc("select *from smart_category where c_depth = '1' and c_view = 'Y'  order by c_idx asc");
 $category_list = count($category_list) <= 0 ? false: $category_list; // 카테고리의 내용이 없을 시 false
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title><?=$shop_name?> RSS 주소 안내 페이지</title>
<script language = 'JavaScript' src='js/script.js'></script>
<style type='text/css'>
ul li {list-style: none;}
.new_main {max-width:980px; padding:20px; margin:0 auto; overflow:hidden;}
.new_main span,.new_main a {display:inline-block;}
.new_main .lineup {overflow:hidden; display:inline;}
.new_main .sum_title_box {position:relative; color:#5480ac; font-size:17px; font-weight:600; background:transparent url('../images/new_main_bullet.png') left 15px no-repeat; padding-left:20px; height:40px; line-height:40px;}
.new_main .sum_title_box:before{ content:'！';  background:#5480ac; font-size:10px; padding-top:4px;}

.new_main .sum_data {overflow:hidden;}
.new_main .sum_data .table_box {border:2px solid #6f96bd; position:relative; background:#fff; overflow:hidden; }
.new_main .sum_data .table_box table {width:100%;border-collapse: collapse;}
.new_main .sum_data .table_box tr:hover {background:#eee;}
.new_main .sum_data .table_box th {background:#e1e3e5; font-weight:600; color:#43464f; font-size:13px; height:33px; border-left:1px solid #c2c7cb; text-align:center;}
.new_main .sum_data .table_box th:first-child {border-left:0}
.new_main .sum_data .table_box td { text-align:center;color:#666; font-size:13px; border-top:1px solid #c2c7cb; border-left:1px solid #c2c7cb; padding:0 10px; position:relative; height:35px;}
.new_main .sum_data .table_box td:first-child {border-left:0; text-align:center;}
.new_main .sum_data .table_box tbody th {border-top:1px solid #c2c7cb;}
.new_main .sum_data .table_box tbody+ thead {border-top:1px solid #c2c7cb;}
.new_main .sum_data .table_box .num { color:#0061c1; font-size:13px; font-family:'roboto'; font-weight:600; line-height:20px;}
.new_main .sum_data .icon_new {margin:8px}
.new_main .sum_data .table_box a {display:block;}
.new_main .sum_data .table_box .category_url{ cursor:pointer; display:block; }
</style>

<script>
	function selectRange(obj) {
		if (window.getSelection) {
			var selected = window.getSelection();
				selected.selectAllChildren(obj);
		} else if (document.body.createTextRange) {
			var range = document.body.createTextRange();
				range.moveToElementText(obj);
				range.select();
		}
	};
</script>

</head>
<body>

	<div class="new_main">
		<div class="wrapping">
			<!-- 주요게시판현황+1:1문의 -->
			<ul>
				<li class="fl_left">

					<!-- ● 데이터박스하나 -->
					<div class="sum_data">
						<div class="sum_title_box">
							<?=$shop_name?> RSS 주소 안내 페이지
							<!-- 바로가기버튼 -->
							<a href="_bbs.board.list.php?menu_idx=29" class="btn_more" title="게시물 통합관리 바로가기"><span class="shape"></span></a>
						</div>

						<!-- 데이터박스 -->
						<div class="table_box">
							<table>
								<colgroup>
									<col width="20%"/><col width="*"/>
								</colgroup>
								<thead>
									<tr>
										<th scope="col">카테고리</th>
										<th scope="col">RSS 주소</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($category_list as $key=>$val){ ?>
									<tr>
										<th><span class="lineup"><span class="num"><?=$val['c_name']?></span></span></th>
										<td><span class="category_url" onClick="selectRange(this)"><?=$address?>/feeds.php?cuid=<?=$val['c_uid']?></span></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<!-- / 데이터박스 -->
					</div>
					<!-- / ● 데이터박스하나 -->

				</li>

			</ul>
		</div>
	</div>
</body>
</html>

