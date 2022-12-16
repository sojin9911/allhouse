<?php
# NPay
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../'); // dirname(__FILE__) 다음 경로 주의
$_path_str = $_SERVER['DOCUMENT_ROOT'];
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

/* --------------------------------------------------------------------------- */
// Mysql에 테이블이 있는지 확인 (테이블이 있다면 1반환)
	function IsTable($Table) {

		$sql = " desc " . $Table;
		$result = mysql_query($sql);

		if(@mysql_num_rows($result)) return true;
		else return false;
	}

/* --------------------------------------------------------------------------- */
// Mysql에 테이블에 필드가 있는지 확인 (필드가 있다면 1반환)
	function IsField($Table, $Field) {

		$sql = ' show columns from ' . $Table . ' like \''.$Field.'\' ';
		$result = mysql_query($sql);

		if(@mysql_num_rows($result)) return true;
		else return false;
	}


/* --------------------------------------------------------------------------- */
// Mysql 테이블의 정보 출력 (인덱스, 컬럼 리스트, 컬럼 데이터 반환)
	function IsTableData($Table) {

		// 초기값
		$ColnumNum = 0;
		$IndexNum = 0;

		// 테이블 인덱스 정보
		$IndexResult = mysql_query(' show index from ' . $Table);
		while($IndexData = mysql_fetch_assoc($IndexResult)){

			$Index[$IndexNum] = $IndexData;
			$IndexNum++;
		}


		// 테이블 컬럼 상세 정보
		$ColumnResult = mysql_query(' show columns from ' . $Table);
		while($ColumnData = mysql_fetch_assoc($ColumnResult)){

			$Column['list'][$ColnumNum] = $ColumnData['Field'];
			$Column['data'][$ColumnData['Field']] = $ColumnData;
			$Column['data'][$ColumnData['Field']]['number'] = $ColnumNum;

			$ColnumNum++;
		}


		// 정보를 모두 변수에 담음
		$list['index'] = $Index; // 인덱스 정보
		$list['columns'] = $Column; // 컬럼 정보


		return $list;
	}

// 카트테이블
$CartTable = 'smart_cart';
?>
<ul>
	<?php if($mode == 'smart_setup') { // smart_setup Install ?>
		<?php
		if(IsTable($mode)) {

			$InstallSql['npay_use'] = "ALTER TABLE  `{$mode}` ADD  `npay_use` ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N' COMMENT  '네이버페이 사용여부'; ";
			$InstallSql['npay_mode'] = "ALTER TABLE  `{$mode}` ADD  `npay_mode` ENUM(  'test',  'real' ) NOT NULL DEFAULT  'test' COMMENT  '네이버페이 활성화 모드'; ";
			$InstallSql['npay_id'] = "ALTER TABLE  `{$mode}` ADD  `npay_id` VARCHAR( 255 ) NOT NULL COMMENT  '네이버페이 아이디'; ";
			$InstallSql['npay_all_key'] = "ALTER TABLE  `{$mode}` ADD  `npay_all_key` VARCHAR( 255 ) NOT NULL COMMENT  '네이버공통인증키'; ";
			$InstallSql['npay_key'] = "ALTER TABLE  `{$mode}` ADD  `npay_key` VARCHAR( 255 ) NOT NULL COMMENT  '네이버페이 가맹점인증키'; ";
			$InstallSql['npay_bt_key'] = "ALTER TABLE  `{$mode}` ADD  `npay_bt_key` VARCHAR( 255 ) NOT NULL COMMENT  '네이버페이 버튼 인증키'; ";
			$InstallSql['npay_sync_mode'] = "ALTER TABLE  `{$mode}` ADD  `npay_sync_mode` ENUM(  'test',  'real' ) NOT NULL DEFAULT  'test' COMMENT  '네이버페이 주문연동 모드'; ";
			$InstallSql['npay_lisense'] = "ALTER TABLE  `{$mode}` ADD  `npay_lisense` VARCHAR( 255 ) NOT NULL COMMENT  '네이버페이 주문연동 라이센스키'; ";
			$InstallSql['npay_secret'] = "ALTER TABLE  `{$mode}` ADD  `npay_secret` VARCHAR( 255 ) NOT NULL COMMENT  '네이버페이 주문연동 비밀키'; ";
		?>
		<li class="blue">
			<kbd><?php echo $mode; ?></kbd> 테이블 준비 완료
			<ul>
				<?php
				foreach($InstallSql as $k=>$v) {
					$InstallCk = IsField($mode, $k);
				?>
				<li class="<?php echo ($InstallCk === true?' red':'blue'); ?>">
					<kbd><?php echo $k; ?></kbd>
					<?php if($InstallCk === true) { ?>
						필드가 이미 셋팅되었습니다.
					<?php
					} else {
						_MQ_noreturn($v);
					?>
						필드가 셋팅되었습니다.
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
		</li>
		<?php } else { ?>
		<li class="red">
			<kbd><?php echo $mode; ?></kbd> 테이블이 없습니다. <small>(솔루션이 티켓몰플러스가 아니거나 셋팅이 잘못되었습니다.)</small>
		</li>
		<?php } ?>
	<?php } // smart_setup Install End ?>


	<?php if($mode == 'smart_product') { // smart_product Install ?>
		<?php
		if(IsTable($mode)) {

			$InstallSql['npay_use'] = "ALTER TABLE  `{$mode}` ADD  `npay_use` ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'Y' COMMENT  'NPay 결제 사용유무'; ";
		?>
		<li class="blue">
			<kbd><?php echo $mode; ?></kbd> 테이블 준비 완료
			<ul>
				<?php
				foreach($InstallSql as $k=>$v) {
					$InstallCk = IsField($mode, $k);
				?>
				<li class="<?php echo ($InstallCk === true?' red':'blue'); ?>">
					<kbd><?php echo $k; ?></kbd>
					<?php if($InstallCk === true) { ?>
						필드가 이미 셋팅되었습니다.
					<?php
					} else {
						_MQ_noreturn($v);
					?>
						필드가 셋팅되었습니다.
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
		</li>
		<?php } else { ?>
		<li class="red">
			<kbd><?php echo $mode; ?></kbd> 테이블이 없습니다. <small>(솔루션이 티켓몰플러스가 아니거나 셋팅이 잘못되었습니다.)</small>
		</li>
		<?php } ?>
	<?php } // smart_product Install End ?>


	<?php if($mode == 'smart_order') { // smart_order Install ?>
		<?php
		if(IsTable($mode)) {

			$InstallSql['npay_order'] = "ALTER TABLE `{$mode}` ADD `npay_order` ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N' COMMENT  'NPay로 구매여부'; ";
			$InstallSql['npay_uniq'] = "ALTER TABLE `{$mode}` ADD `npay_uniq` VARCHAR( 255 ) NOT NULL COMMENT  'NPay 구매 고유아이디'; ";
		?>
		<li class="blue">
			<kbd><?php echo $mode; ?></kbd> 테이블 준비 완료
			<ul>
				<?php
				foreach($InstallSql as $k=>$v) {
					$InstallCk = IsField($mode, $k);
				?>
				<li class="<?php echo ($InstallCk === true?' red':'blue'); ?>">
					<kbd><?php echo $k; ?></kbd>
					<?php if($InstallCk === true) { ?>
						필드가 이미 셋팅되었습니다.
					<?php
					} else {
						_MQ_noreturn($v);
					?>
						필드가 셋팅되었습니다.
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
		</li>
		<?php } else { ?>
		<li class="red">
			<kbd><?php echo $mode; ?></kbd> 테이블이 없습니다. <small>(솔루션이 티켓몰플러스가 아니거나 셋팅이 잘못되었습니다.)</small>
		</li>
		<?php } ?>
	<?php } // smart_order Install End ?>


	<?php if($mode == 'smart_order_product') { // smart_order_product Install ?>
		<?php
		if(IsTable($mode)) {

			$InstallSql['npay_order_code'] = "ALTER TABLE  `{$mode}` ADD  `npay_order_code` VARCHAR( 255 ) NOT NULL COMMENT  '네이버페이 상품주문코드';";
			$InstallSql['npay_uniq'] = "ALTER TABLE `{$mode}` ADD `npay_uniq` VARCHAR( 255 ) NOT NULL COMMENT  'NPay 구매 고유아이디'; ";
			$InstallSql['npay_status'] = "ALTER TABLE  `{$mode}` ADD  `npay_status` VARCHAR( 255 ) NOT NULL COMMENT  'NPay 상태 전달값';";
		?>
		<li class="blue">
			<kbd><?php echo $mode; ?></kbd> 테이블 준비 완료
			<ul>
				<?php
				foreach($InstallSql as $k=>$v) {
					$InstallCk = IsField($mode, $k);
				?>
				<li class="<?php echo ($InstallCk === true?' red':'blue'); ?>">
					<kbd><?php echo $k; ?></kbd>
					<?php if($InstallCk === true) { ?>
						필드가 이미 셋팅되었습니다.
					<?php
					} else {
						_MQ_noreturn($v);
					?>
						필드가 셋팅되었습니다.
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
		</li>
		<?php } else { ?>
		<li class="red">
			<kbd><?php echo $mode; ?></kbd> 테이블이 없습니다. <small>(솔루션이 티켓몰플러스가 아니거나 셋팅이 잘못되었습니다.)</small>
		</li>
		<?php } ?>
	<?php } // smart_order_product Install End ?>


	<?php if($mode == 'smart_npay') { // smart_npay Install ?>
		<?php if(IsTable($CartTable)) { ?>
			<li class="blue">
				<kbd><?php echo $CartTable; ?></kbd> 테이블 준비 완료
				<ul>
					<?php
					if(IsTable($mode) === false) {

						_MQ_noreturn("create table `{$mode}` like `{$CartTable}`;");
						_MQ_noreturn("ALTER TABLE  `{$mode}` ADD  `c_uniq` VARCHAR( 255 ) NOT NULL COMMENT  'Npay주문등록 고유키';");
						_MQ_noreturn("alter TABLE  `{$mode}` comment =  '네이버페이 주문관리' ");
					?>
					<li class="blue">
						<kbd><?php echo $CartTable; ?></kbd> ─＞ <kbd><?php echo $mode; ?></kbd> 복사완료
					</li>
					<li class="blue">
						<kbd>c_uniq</kbd> 필드가 셋팅되었습니다.
					</li>
					<?php } else { ?>
					<li class="red">
						<kbd><?php echo $mode; ?></kbd> 테이블이 이미 셋팅되었습니다.
					</li>
					<?php } ?>
				</ul>
			</li>
		<?php } else { ?>
		<li class="red">
			<kbd><?php echo $CartTable; ?></kbd> 테이블이 없습니다. <small>(솔루션이 티켓몰플러스가 아니거나 셋팅이 잘못되었습니다.)</small>
		</li>
		<?php } ?>
	<?php } // smart_npay Install 뚱 ?>
</ul>