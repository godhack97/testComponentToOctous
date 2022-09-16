<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);

use Bitrix\Main;

if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id'])) {
  $siteID = trim($_REQUEST['site_id']);
  if ($siteID !== '' && preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1) {
    define('SITE_ID', $siteID);
  }
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

global $USER;
$thisUser = $USER->GetID();
$request = Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new Main\Web\PostDecodeFilter);

if (!check_bitrix_sessid() || !$request->isPost())
  return;

if($request->get('event') == 'cart-sharing') {
  require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/classes/CartSharing.php');
  $BasketSharing = new BasketSharing();
  $basketID = $BasketSharing->saveUserBasket();
  $arResult = [];
  if($basketID > 0) {
    $arResult['error'] = false;
    $arResult['id'] = $basketID;
  }

  echo json_encode($arResult);
}

