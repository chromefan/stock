<?php
define('SITE_PATH', getcwd());
define('APP_NAME', 'stock');
define('APP_PATH', './');
define('APP_DEBUG', true);
define('REWRITE_ON', false); //打开REWRITE_ON后 __APP__常量将不包含index.php，须与URL_MODEL配合使用

define ( "GZIP_ENABLE", function_exists ( 'ob_gzhandler' ));
ob_start ( GZIP_ENABLE ? 'ob_gzhandler' : '');
$_SGLOBAL=array();

require( "../ThinkPHP/ThinkPHP.php");

?>
