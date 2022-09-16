<?
$siteId = '';
if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
  $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);

if ($siteId){
  define('SITE_ID', $siteId);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

/* DOCS
https://securepayments.sberbank.ru/wiki/doku.php/integration:api:callback:start
https://securepayments.sberbank.ru/wiki/doku.php/integration:api:rest:requests:getorderstatusextended

Array
(
  [orderNumber] => 7208_1
  [paymentWay] => SBRF_SBOL
  [mdOrder] => 8f3d48ce-f3b4-710a-bbd5-c9b001c152ee
  [operation] => deposited
  [status] => 1
  [PHPSESSID] => AIRGLYegl0dQ3Bc6M6O6fc8sA0Y3X3iS
) */

$_GET['orderNumber'] = '7208_1';
$_GET['paymentWay'] = 'SBRF_SBOL';
$_GET['operation'] = 'deposited';
$_GET['status'] = '1';

$arGet['ORDER_ID'] = htmlspecialcharsbx($_GET['orderNumber']);
$arGet['paymentWay'] = htmlspecialcharsbx($_GET['paymentWay']);
$arGet['operation'] = htmlspecialcharsbx($_GET['operation']);
$arGet['status'] = htmlspecialcharsbx($_GET['status']);


if (!($arOrder = CSaleOrder::GetByID($arGet['ORDER_ID'])))
{
  file_put_contents("log_error.txt", date("d.m.Y H:i:s") ." Заказ с кодом ". $arGet['ORDER_ID'] ." не найден. ". print_r($arGet, 1), FILE_APPEND);
}
else
{
  
  if($arOrder['PAYED'] == 'N' && $arOrder['PAY_SYSTEM_ID'] == 14 && $arGet['paymentWay'] == 'SBRF_SBOL' && $arGet['operation'] == 'deposited' && $arGet['status'] == 1)
  {
    
    $arFields = array(
       "PAYED" => "Y",
       "DATE_PAYED" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
       "USER_ID" => $arOrder["USER_ID"],
       "EMP_PAYED_ID" => 4543, // Пользователь SberPay
    );
    $rs = CSaleOrder::Update($arGet['ORDER_ID'], $arFields);
    
    if(!$rs)
      file_put_contents("log_error.txt", date("d.m.Y H:i:s") ." Ошибка обновления заказа ". $arGet['ORDER_ID'] ." ". print_r($arGet, 1) . print_r($arFields, 1), FILE_APPEND);
    
  }
  
}


//use Bitrix\Main\Application;
//
//Bitrix\Main\Loader::includeModule("catalog");
//$request = Application::getInstance()->getContext()->getRequest();
//
//if($request->isPost())
//{
//  $action = $request->getPost("action");
//  $id     = (int)$request->getPost("id");
//  $prop   = $request->getPost("prop");
//
//  $siteDir = SITE_DIR;
//  if(!empty($siteId)) {
//    $siteDir = CSite::GetArrayByID($siteId)['DIR'];
//  }
//
//  if($id > 0 && $action == "ADD2BASKET")
//  {
//    $fields = [
//      'PRODUCT_ID' => $id, // ID товара, обязательно
//      'QUANTITY' => 1, // количество, обязательно
//      'PROPS' => $prop,
//    ];
//
//    $r = Bitrix\Catalog\Product\Basket::addProduct($fields, ['LID' => $siteId]);
//  }
//}





