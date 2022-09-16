<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Auth");
?>
<?
  if($USER->IsAuthorized())
  {
    LocalRedirect(SITE_DIR.'personal/profile/');
    exit;
  }
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>