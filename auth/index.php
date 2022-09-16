<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
?>
<?
  if($USER->IsAuthorized())
  {
    LocalRedirect('/personal/profile/');
    exit;
  }
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>