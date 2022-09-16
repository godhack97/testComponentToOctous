<? // require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main\Application;
use \Bitrix\Sale\PaySystem;

define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define("DisableEventsCheck", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;


if(count($_GET) && !empty(htmlspecialcharsbx($_GET['mdOrder'])))
{
  /*  DOCS
  https://securepayments.sberbank.ru/wiki/doku.php/integration:api:callback:start
  https://securepayments.sberbank.ru/wiki/doku.php/integration:api:rest:requests:getorderstatusextended  */
  
  file_put_contents("log_request.txt", date("d.m.Y H:i:s") .' '. print_r($_REQUEST, true), FILE_APPEND);
  
  
  $arGet['ORDER_ID']    = htmlspecialcharsbx($_GET['orderNumber']);
  $arGet['paymentWay']  = htmlspecialcharsbx($_GET['paymentWay']);
  $arGet['operation']   = htmlspecialcharsbx($_GET['operation']);
  $arGet['status']      = htmlspecialcharsbx($_GET['status']);
  $arGet['mdOrder']     = htmlspecialcharsbx($_GET['mdOrder']);

  $arOrder = explode('_', $arGet['ORDER_ID']);
  $orderId = intval($arOrder[0]);
  if($orderId > 0 && $arGet['operation'] == 'deposited') {
    $order = \Bitrix\Sale\Order::load($orderId);
    if($order) {
      $propertyCollection = $order->getPropertyCollection();
      foreach ($propertyCollection as $propertyValue) {
        if ($propertyValue->getField('CODE') == "PAY_METHOD" || $propertyValue->getField('CODE') == "CODE_PAY_METHOD") {
          $propertyValue->setValue($arGet['paymentWay']);
        }
      }
      $order->save();
    }
  }

  if (!($arOrder = CSaleOrder::GetByID($arGet['ORDER_ID'])))
  {
    file_put_contents("log_error.txt", date("d.m.Y H:i:s") ." Заказ с кодом ". $arGet['ORDER_ID'] ." не найден. ". print_r($arGet, 1), FILE_APPEND);
  }
  else
  {
    //  CARD - оплата с вводом карточных данных;
    //  CARD_BINDING - оплата связкой;
    //  CARD_MOTO - оплата через колл-центр;
    //  CARD_PRESENT - оплата как cardPresent;
    //  SBRF_SBOL - оплата через Сбербанк Онлайн;
    //  UPOP - оплата через China Union Pay;
    //  FILE_BINDING - оплата через файл;
    //  SMS_BINDING - оплата через смс;
    //  P2P - перевод с карты на карту;
    //  P2P_BINDING - перевод связкой;
    //  PAYPAL - оплата со счёта PayPal;
    //  MTS - оплата со счёта МТС;
    //  APPLE_PAY - Apple Pay;
    //  APPLE_PAY_BINDING - оплата связкой Apple Pay;
    //  ANDROID_PAY - Android Pay;
    //  ANDROID_PAY_BINDING - оплата связкой Android Pay;
    //  GOOGLE_PAY_CARD - Google Pay нетокенизированная;
    //  GOOGLE_PAY_CARD_BINDING - оплата связкой с не токенизированной картой GooglePay;
    //  GOOGLE_PAY_TOKENIZED - Google Pay токенизированная;
    //  GOOGLE_PAY_TOKENIZED_BINDING - оплата связкой с токенизированной картой GooglePay;
    //  SAMSUNG_PAY - Samsung Pay;
    //  SAMSUNG_PAY_BINDING - оплата связкой Samsung Pay;
    //  IPOS - оплата iPOS;
    //  SBERPAY - оплата SberPay;
    //  SBERID - оплата SberID.

//    $arPaymentNames = [
//      'CARD - оплата с вводом карточных данных;
//      'CARD_BINDING - оплата связкой;
//      'CARD_MOTO - оплата через колл-центр;
//      'CARD_PRESENT - оплата как cardPresent;
//      'SBRF_SBOL - оплата через Сбербанк Онлайн;
//      'UPOP - оплата через China Union Pay;
//      'FILE_BINDING - оплата через файл;
//      'SMS_BINDING - оплата через смс;
//      'P2P - перевод с карты на карту;
//      'P2P_BINDING - перевод связкой;
//      'PAYPAL - оплата со счёта PayPal;
//      'MTS - оплата со счёта МТС;
//      'APPLE_PAY - Apple Pay;
//      'APPLE_PAY_BINDING - оплата связкой Apple Pay;
//      'ANDROID_PAY - Android Pay;
//      'ANDROID_PAY_BINDING - оплата связкой Android Pay;
//      'GOOGLE_PAY_CARD - Google Pay нетокенизированная;
//      'GOOGLE_PAY_CARD_BINDING - оплата связкой с не токенизированной картой GooglePay;
//      'GOOGLE_PAY_TOKENIZED - Google Pay токенизированная;
//      'GOOGLE_PAY_TOKENIZED_BINDING - оплата связкой с токенизированной картой GooglePay;
//      'SAMSUNG_PAY - Samsung Pay;
//      'SAMSUNG_PAY_BINDING - оплата связкой Samsung Pay;
//      'IPOS - оплата iPOS;
//      'SBERPAY - оплата SberPay;
//      'SBERID - оплата SberID.
//    ];
      
    $arPaymentMethods = [
      'CARD',
      'CARD_BINDING',
      'CARD_MOTO',
      'CARD_PRESENT',
      'SBRF_SBOL',
      'UPOP',
      'FILE_BINDING',
      'SMS_BINDING',
      'P2P',
      'P2P_BINDING',
      'PAYPAL',
      'MTS',
      'APPLE_PAY',
      'APPLE_PAY_BINDING',
      'ANDROID_PAY',
      'ANDROID_PAY_BINDING',
      'GOOGLE_PAY_CARD',
      'GOOGLE_PAY_CARD_BINDING',
      'GOOGLE_PAY_TOKENIZED',
      'GOOGLE_PAY_TOKENIZED_BINDING',
      'SAMSUNG_PAY',
      'SAMSUNG_PAY_BINDING',
      'IPOS',
      'SBERPAY',
      'SBERID'
    ];
    
    if($arOrder['PAYED'] == 'N' && $arOrder['PAY_SYSTEM_ID'] == 14 && in_array($arGet['paymentWay'], $arPaymentMethods) && $arGet['operation'] == 'deposited' && $arGet['status'] == 1)
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
  
}



if (CModule::IncludeModule("sale"))
{
	$context = Application::getInstance()->getContext();
	$request = $context->getRequest();

	$item = PaySystem\Manager::searchByRequest($request);
	if ($item !== false)
	{
		$service = new PaySystem\Service($item);
		if ($service instanceof PaySystem\Service)
		{
			$result = $service->processRequest($request);
		}
	}
	else
	{
		$debugInfo = http_build_query($request->toArray(), "", "\n");
		if (empty($debugInfo))
		{
			$debugInfo = file_get_contents('php://input');
		}
		PaySystem\Logger::addDebugInfo('Pay system not found. Request: '.($debugInfo ? $debugInfo : "empty"));
	}
}

//$arOrder = CSaleOrder::GetByID(htmlspecialchars($_REQUEST['ORDER_ID']));
//dbgz($_REQUEST);

// echo '<pre>'. print_r($_GET, 1) .'</pre>';

$APPLICATION->FinalActions();


die();