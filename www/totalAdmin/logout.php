<?php
include_once(realpath($_SERVER['DOCUMENT_ROOT'].'/include/inc.php'));
@samesiteCookie("AuthAdmin","",time() - 100000,"/");
@samesiteCookie("AuthCompany","",time() - 100000,"/");
AdminLogout();
echo "<script>top.location.href=('/totalAdmin/index.php')</script>";