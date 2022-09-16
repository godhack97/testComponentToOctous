<?php
  use \Bitrix\Main\Application;
  use \Bitrix\Main\Diag\Debug;
  use \Bitrix\Sale\PaySystem;
  
  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
  
  global $APPLICATION;
  
  CModule::IncludeModule("network.payment");
  
  $json = file_get_contents("php://input");
  $order = json_decode($json);
  
file_put_contents($_SERVER["DOCUMENT_ROOT"]."/api/data.txt", "\n". print_r($order, 1), FILE_APPEND);

  $networkGate = \Network\Payment\Gateway::getInstance();
  
  $record = $networkGate->getDB()
    ->query("select * from network_order where ORDER_ID = '{$order->order->reference}'")
    ->fetch();
  LocalRedirect('https://sodamoda.ru/bitrix/tools/sale_ps_result.php?PAYMENT=NETWORK&ORDER_ID='.$record['BITRIX_ORDER_ID'].'&CALLBACK_REDIRECT=1&PAYMENT_ID='.$record['BITRIX_ORDER_ID'].'&ref='.$record['ORDER_ID'].'');
  die();