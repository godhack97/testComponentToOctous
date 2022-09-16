<?php
  use Bitrix\Main\Diag\Debug;
  
  require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
  
  CModule::IncludeModule("network.payment");
  
  $json = file_get_contents("php://input");
  $order = json_decode($json);
  
  Debug::writeToFile('request-payment', $json);
  $networkGate = \Network\Payment\Gateway::getInstance();
  
  $record = $networkGate->getDB()
    ->query("select * from network_order where ORDER_ID = '{$order->order->reference}'")
    ->fetch();
  LocalRedirect('https://sodamoda.ru/bitrix/tools/sale_ps_result.php?PAYMENT=NETWORK&ORDER_ID='.$record['BITRIX_ORDER_ID'].'&CALLBACK_REDIRECT=1&PAYMENT_ID='.$record['PAYMENT_ID'].'&ref='.$record['ORDER_ID'].'');
  die();
?>