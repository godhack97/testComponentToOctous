<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\SystemException;
use \Bitrix\Sale\Order;
use \Bitrix\Sale\PaySystem;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define("DisableEventsCheck", true);


global $APPLICATION, $USER;

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
			// echo "<pre>";
			// print_r($result);
		}
	}
	else
	{
		$debugInfo = implode("\n", $request->toArray());
		PaySystem\Logger::addDebugInfo('Pay system not found. Request: '.$debugInfo);
	}
}

// $APPLICATION->FinalActions();


$arOrder = CSaleOrder::GetByID(htmlspecialchars($_REQUEST['ORDER_ID']));
if(!in_array($arOrder['DELIVERY_ID'], Array(3, 4, 5, 6, 67))) // Курьерские доставки и самовывоз
{
  ?>
  <div class="message-result" style="text-align: center; margin-top: 50px;">Трек номер отправления придёт на электронную почту в течении суток, пожалуйста, ожидайте.</div>
  <?
}
else
{
  ?>
  <div class="message-result" style="text-align: center; margin-top: 50px;">Спасибо за заказ, пожалуйста ожидайте звонка</div>
  <?
}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>