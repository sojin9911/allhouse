<?php
if(!isset($_REQUEST['_menuType'])) $_REQUEST['_menuType'] = '';
if(!isset($_REQUEST['pass_menu'])) $_REQUEST['pass_menu'] = '';
if($_REQUEST['_menuType'] == '080'){
	$app_current_link = '_addons.php?_menuType=080';
	$pass_menu = $_REQUEST['pass_menu'] == '' ? '080deny/_receipt.form':$_REQUEST['pass_menu'];
}
else if($_REQUEST['_menuType'] == 'smsEmail'){
	$app_current_link = '_addons.php?_menuType=smsEmail';
	$pass_menu = $_REQUEST['pass_menu'] == '' ? '2yearOpt/_2year_opt.form':$_REQUEST['pass_menu'];
}else{
	if( $_REQUEST['pass_menu'] != ''){
		if(in_array($_REQUEST['pass_menu'],array('080deny/_receipt.form','080deny/_member_080deny.list')) == true){
			$_menuType = '080';
		}else{
			$_menuType = 'smsEmail';
		}

	}else{
		$_menuType = '080';
		$pass_menu = $_REQUEST['pass_menu'] == '' ? '080deny/_receipt.form':$_REQUEST['pass_menu'];
	}
	$app_current_link = '_addons.php?_menuType='.$_menuType;
}
include_once('wrap.header.php');


// 추가파라메터
if(!$arr_param) $arr_param = array();



// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
	if(is_array($val)) {
		foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
	}
	else {
		$_PVS .= "&$key=$val";
	}
}
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기


?>

<!-- ● 내부탭 -->
<div class="c_tab">
	<ul>
	<?php foreach($arrAddonsService['tabMenu_'.$_menuType] as $k=>$v){ ?>
		<li class="<?=$k==$pass_menu ? "hit":""?>">
			<a href="_addons.php?_menuType=<?=$_menuType?>&pass_menu=<?=$k?>" class="btn"><strong><?=$v?></strong></a>
		</li>
	<?php } ?>
	</ul>
</div>

<?php
	# 메뉴형태 => 디렉토리경로/파일명
	if($pass_menu) {
		include_once("../addons/" . $pass_menu . ".php");
	}
?>


<?php include_once('wrap.footer.php'); ?>