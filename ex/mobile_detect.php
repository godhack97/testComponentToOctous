<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("MD");
?><? 

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/mobile_detect.php");

$detect = new Mobile_Detect;

var_dump($detect->isMobile());
var_dump($detect->isTablet());



?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>