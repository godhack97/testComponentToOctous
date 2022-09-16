<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Storage");
?>

<?

define('STORAGE_LIST', '7.8.11.12');

$arStorageList = explode('.', STORAGE_LIST);
dbg($arStorageList);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>