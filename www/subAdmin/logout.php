<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

@setcookie('AuthCompany', '', time()-100000, '/');
@setcookie('AuthCompany', '', 0, '/', '.'.str_replace('www.', '', $system['host']));
SubAdminLogout();

error_loc(OD_ADMIN_URL.'/index.php');