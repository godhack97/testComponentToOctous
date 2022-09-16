<?
$siteId = '';
if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
  $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);

if ($siteId){
  define('SITE_ID', $siteId);
}
?>
<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");?>
<?
use \Bitrix\Main\Localization\Loc;

$EVENT = htmlspecialcharsbx($_POST['event']);
$EMAIL = htmlspecialcharsbx($_POST["SENDER_SUBSCRIBE_EMAIL"]);

if(!empty($EVENT)) 
{
  switch($EVENT)
  {
    case 'sender_subscription_add':
      $mail = htmlspecialcharsEx($EMAIL);
      if(check_email($mail, true))
      {
        if(!CModule::IncludeModule("sender"))
        {
          $arResult['error'] = true;
          $arResult['message'] = Loc::getMessage('AJAX_SUBSCRIBE_NOT_MODULE');
          break;
        }

        \Bitrix\Sender\Subscription::add($EMAIL, []);
        $arResult['error'] = false;
        $arResult['message'] = Loc::getMessage('AJAX_SUBSCRIBE_SUBSCRIBED');
      } 
      else 
      {
        $arResult['error'] = true;
        $arResult['message'] = Loc::getMessage('AJAX_SUBSCRIBE_INVALID_EMAIL');
      }
      break;
  }
}

echo json_encode($arResult);
?>