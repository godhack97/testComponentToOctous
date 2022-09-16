<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?

/* 
  Битриксовый ID заказа в ORDER_ID, номер отправления в PP_INVOICE_ID, код статуса Pickpoint в STATUS. 
  Можно через агент с нужным интервалом обходить заказы и чекать трекномера. 
  Либо на какое-то событие CMS это подвесить, например, менеджер отправляет заказ в РР и затем переводит его в статус "Отправлено в ТК". 
  Обработчик OnSaleStatusOrder или другого аналогичного по сути события ловит изменение статуса заказа и чекает таблицу b_pp_order_postamat, 
  получая нужную информацию напрямую из нее. 
 */

use Bitrix\Main\Mail\Event;
//CModule::IncludeModule("sale");

$KEY = htmlspecialcharsbx($_REQUEST['key']);
if($KEY != 'nfsy254cj')
  exit('error');

//exit($_SERVER['DOCUMENT_ROOT']);

$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();


$connection = Bitrix\Main\Application::getConnection();
$sqlHelper = $connection->getSqlHelper();
$dbTable = 'b_pp_order_postamat';


$recordset = $connection->query("SELECT * FROM `". $dbTable ."` WHERE `EMAIL_SENT` = 0 AND length(`PP_INVOICE_ID`) != 0 AND `CANCELED` = 0"); // AND `ORDER_ID` = 2194
while ($record = $recordset->fetch())
{
  //  dbg($record['ORDER_ID'], 1);
  
  $arOrder = CSaleOrder::GetByID($record['ORDER_ID']);
  
  $dbProps = CSaleOrderPropsValue::GetList(Array(), Array("ORDER_ID" => $record['ORDER_ID']));
  while ($row = $dbProps->Fetch())
    $arOrder['USER_PROP_' . $row['CODE']] = $row['VALUE'];

  //dbg($arOrder);
  
  if(empty($arOrder['TRACKING_NUMBER']))
    CSaleOrder::Update($record['ORDER_ID'], Array('TRACKING_NUMBER' => $record['PP_INVOICE_ID']));
  
  
  $arDelivery = CSaleDelivery::GetByID($arOrder['DELIVERY_ID']);
  $arDelivery['NAME'] = getDeliveryName($arDelivery, $arOrder['DELIVERY_ID']);

  $LINK_TRACKING = 'Отследить заказ:<br>'
      . '<a href="https://pickpoint.ru/monitoring/">https://pickpoint.ru/monitoring/</a><br>'
      . '<br>';
  
  $arDate = explode(' ', $arOrder['DATE_INSERT']);
  
  $eventParams = Array(
    "EVENT_NAME" => "SALE_ORDER_TRACKING_NUMBER",
    "LID" => "s1",
    "C_FIELDS" => Array(
        "SALE_EMAIL" => $arSite['EMAIL'],
        "EMAIL" => $arOrder['USER_PROP_EMAIL'], // 'do@arlix.ru'
        "ORDER_USER" => $arOrder['USER_PROP_CONTACT_PERSON'],
        "ORDER_ID" => $record['ORDER_ID'],
        "ORDER_DATE_DAY" => $arOrder['DATE_INSERT_FORMAT'],
        "ORDER_DATE_TIME" => $arDate[1],
        "DELIVERY_NAME" => $arDelivery['NAME'],
        "ORDER_TRACKING_NUMBER" => $record['PP_INVOICE_ID'],
        "LINK_TRACKING" => $LINK_TRACKING,
        "ORDER_ACCOUNT_NUMBER_ENCODE" => $record['ORDER_ID'],
        "FLAG_CRON_PICKPOINT" => 'Y',
  ));
  //dbg($eventParams);
  Event::send($eventParams);
  
  
  if($arOrder['PAYED'] == 'Y')
    CSaleOrder::StatusOrder($record['ORDER_ID'], 'PD'); // Оплачен и отправлен
  else
    CSaleOrder::StatusOrder($record['ORDER_ID'], 'DS'); // Отправлен
  
   
  $connection->queryExecute("UPDATE `". $dbTable ."` SET `EMAIL_SENT` = 1 WHERE `ID` = ". $record['ID'] );
}



?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>


