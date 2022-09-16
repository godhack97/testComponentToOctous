<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Profile");
?>

<?
if($USER->IsAuthorized()){
  LocalRedirect(SITE_DIR.'personal/profile/');
}
?>




<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>