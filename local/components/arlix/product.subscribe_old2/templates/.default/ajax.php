<?

use \Bitrix\Main\Loader,
  \Bitrix\Main\Localization\Loc,
  \Bitrix\Highloadblock\HighloadBlockTable,
  \Bitrix\Main\Application,
  \Bitrix\Main\Web\Cookie;

define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", "Y");

$siteId = isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']) ? $_REQUEST['site_id'] : '';
$siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $siteId), 0, 2);
if (!empty($siteId) && is_string($siteId)) {
  define('SITE_ID', $siteId);
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if (!check_bitrix_sessid() || !$request->isPost() || !Loader::includeModule('highloadblock')) {
  die();
}

global $USER;
$result = [];


if ($request->get('event') == 'subscribe') {

  $arProp = [];
  if (!empty($request->get('props'))) {
    $strProp = $request->get('props');
    parse_str($strProp, $arProp);
  }

  if (intval($arProp['productId']) > 0 && !empty($arProp['email'])) {
    $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $hlSubscribeTable = $entity->getDataClass();

    $countElements = $hlSubscribeTable::getlist([
      'filter' => ['UF_PRODUCT_ID' => intval($arProp['productId']), 'UF_EMAIL' => htmlspecialcharsbx($arProp['email'])]
    ])->getSelectedRowsCount();

    if ($countElements == 0) {
      $arFields = [
        'UF_PRODUCT_ID' => intval($arProp['productId']),
        'UF_EMAIL' => htmlspecialcharsbx($arProp['email']),
        'UF_SITE_ID' => $siteId
      ];
      $hlResult = $hlSubscribeTable::add($arFields);
      if ($hlResult->isSuccess()) {
        $result['message'] = Loc::getMessage("APS_SUBSCRIBE_SUCCESS");

        $isExistProductId = false;
        $arProductSession = unserialize($_SESSION['PRODUCT_SUBSCRIBE']);
        foreach ($arProductSession as $arProduct) {
          foreach ($arProduct as $productId => $email)
            if ($productId == intval($arProp['productId'])) {
              $isExistProductId = true;
            }
        }
        if (!$isExistProductId) {
          $arProductSession[] = [intval($arProp['productId']) => htmlspecialcharsbx($arProp['email'])];
          $_SESSION['PRODUCT_SUBSCRIBE'] = serialize($arProductSession);
        }
      } else {
        $arError = $hlResult->getErrorMessages();
        $result['message'] = $arError[0];
      }
    } else {
      $result['message'] = Loc::getMessage("APS_SUBSCRIBE_ALREADY");
      $arProductSession = unserialize($_SESSION['PRODUCT_SUBSCRIBE']);
      foreach ($arProductSession as $arProduct) {
        foreach ($arProduct as $productId => $email)
          if ($productId == intval($arProp['productId'])) {
            $isExistProductId = true;
          }
      }
      if (!$isExistProductId) {
        $arProductSession[] = [intval($arProp['productId']) => htmlspecialcharsbx($arProp['email'])];
        $_SESSION['PRODUCT_SUBSCRIBE'] = serialize($arProductSession);
      }
    }
  }

  echo json_encode($result);

} elseif ($request->get('event') == 'subscribe-user') {

  if (!empty($request->get('product_id'))) {
    $productId = intval($request->get('product_id'));
  }

  $currentUserEmail = $USER->GetEmail();

  if ($productId > 0 && !empty($currentUserEmail)) {
    $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $hlSubscribeTable = $entity->getDataClass();

    $countElements = $hlSubscribeTable::getlist([
      'filter' => ['UF_PRODUCT_ID' => $productId, 'UF_EMAIL' => $currentUserEmail]
    ])->getSelectedRowsCount();

    if ($countElements == 0) {
      $arFields = [
        'UF_PRODUCT_ID' => $productId,
        'UF_EMAIL' => $currentUserEmail,
        'UF_SITE_ID' => $siteId
      ];
      $hlResult = $hlSubscribeTable::add($arFields);
      if ($hlResult->isSuccess()) {
        $result['message'] = Loc::getMessage("APS_SUBSCRIBE_SUCCESS");
      } else {
        $arError = $hlResult->getErrorMessages();
        $result['message'] = $arError[0];
      }
    } else {
      $result['message'] = Loc::getMessage("APS_SUBSCRIBE_ALREADY");
    }
  }

  echo json_encode($result);

} elseif ($request->get('event') == 'unsubscribe-user') {

  if (!empty($request->get('product_id'))) {
    $productId = intval($request->get('product_id'));
  }

  $currentUserEmail = $USER->GetEmail();

  if ($productId > 0 && !empty($currentUserEmail)) {
    $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $hlSubscribeTable = $entity->getDataClass();

    $arElements = $hlSubscribeTable::getlist([
      'filter' => ['UF_PRODUCT_ID' => $productId, 'UF_EMAIL' => $currentUserEmail],
      'select' => ['ID']
    ])->fetchAll();

    foreach ($arElements as $arVal) {
      $hlSubscribeTable::delete($arVal['ID']);
    }

    $result['message'] = Loc::getMessage("APS_UNSUBSCRIBE_SUCCESS");
  }

  echo json_encode($result);

} elseif ($request->get('event') == 'unsubscribe-not-auth') {

  if (!empty($request->get('product_id'))) {
    $productId = intval($request->get('product_id'));
  }

  $arProductSession = unserialize($_SESSION['PRODUCT_SUBSCRIBE']);
  foreach ($arProductSession as $key => $arProduct) {
    foreach ($arProduct as $sessionProductId => $email)
      if ($sessionProductId == $productId) {
        $currentUserEmail = $email;
        unset($arProductSession[$key]);
        $_SESSION['PRODUCT_SUBSCRIBE'] = serialize($arProductSession);
      }
  }

  if ($productId > 0 && !empty($currentUserEmail)) {
    $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $hlSubscribeTable = $entity->getDataClass();

    $arElements = $hlSubscribeTable::getlist([
      'filter' => ['UF_PRODUCT_ID' => $productId, 'UF_EMAIL' => $currentUserEmail],
      'select' => ['ID']
    ])->fetchAll();

    foreach ($arElements as $arVal) {
      $hlSubscribeTable::delete($arVal['ID']);
    }

    $result['message'] = Loc::getMessage("APS_UNSUBSCRIBE_SUCCESS");
  }

  echo json_encode($result);

} elseif ($request->get('event') == 'subscribe-phone') {
  $arProp = [];
  if (!empty($request->get('props'))) {
    $strProp = $request->get('props');
    parse_str($strProp, $arProp);
  }

  if (intval($arProp['productId']) > 0 && !empty($arProp['phone']) && Loader::includeModule('form')) {
    $arValues = [
      'form_text_21' => htmlspecialcharsbx($arProp['phone']),
      'form_text_22' => intval($arProp['productId']),
    ];

    $RESULT_ID = CFormResult::Add(2, $arValues);
    CFormResult::SetEvent($RESULT_ID);
    CFormResult::Mail($RESULT_ID);
  }
}