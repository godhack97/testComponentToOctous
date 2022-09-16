<?
define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Заявление на возврат');
?>

<?
$ORDER_ID = htmlspecialcharsbx($_REQUEST['order']);
if(empty($ORDER_ID)):
?>

  <p>Заказ не найден</p>

<?else:?>

  <?$APPLICATION->IncludeComponent(
    "bitrix:sale.personal.order.detail", 
    "return", 
    array(
      "CURRENT_USER" => $USER->GetID(),
      "RETURN_ITEMS" => htmlspecialcharsbx($_REQUEST['items']),
      "ACTIVE_DATE_FORMAT" => "d.m.Y",
      "CACHE_GROUPS" => "Y",
      "CACHE_TIME" => "3600",
      "CACHE_TYPE" => "A",
      "CUSTOM_SELECT_PROPS" => array(
      ),
      "ID" => $ORDER_ID,
      "PATH_TO_CANCEL" => "",
      "PATH_TO_COPY" => "",
      "PATH_TO_LIST" => "/personal/order/",
      "PATH_TO_PAYMENT" => "/personal/order/payment/index.php",
      "PICTURE_HEIGHT" => "110",
      "PICTURE_RESAMPLE_TYPE" => "1",
      "PICTURE_WIDTH" => "110",
      "PROP_1" => array(
      ),
      "REFRESH_PRICES" => "N",
      "RESTRICT_CHANGE_PAYSYSTEM" => array(
        0 => "0",
      ),
      "SET_TITLE" => "Y",
      "COMPONENT_TEMPLATE" => "template"
    ),
    false
  );?>

<?endif?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>