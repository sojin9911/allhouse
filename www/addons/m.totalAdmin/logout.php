<?PHP
include_once('inc.php');

@samesiteCookie("AuthAdmin","",time() - 100000,"/");
@samesiteCookie("AuthCompany","",time() - 100000,"/");
AdminLogout();

error_loc("index.php");
