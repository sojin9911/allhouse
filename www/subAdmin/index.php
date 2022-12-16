<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
if($_COOKIE['AuthCompany']) error_loc(OD_SUB_ADMIN_URL.'/_product.list.php');
else error_loc(OD_ADMIN_URL);