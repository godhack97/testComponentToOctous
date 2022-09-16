<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Восстановление пароля");
?>
<?if($USER->IsAuthorized()){
  LocalRedirect('/personal/profile/');
}?>
<?$APPLICATION->IncludeComponent("bitrix:system.auth.forgotpasswd", "", Array(),false);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>