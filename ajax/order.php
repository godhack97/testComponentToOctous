<?
$siteId = '';
if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
  $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);

if ($siteId){
  define('SITE_ID', $siteId);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("Корзина"); ?><?

//$arResult['start'] = microtime(true);
  $phoneProp = false;
  if(isset($_REQUEST['ORDER_PROP_17'])){
    $phoneProp = $_REQUEST['ORDER_PROP_17'];
  }
  elseif(isset($_REQUEST['ORDER_PROP_11'])){
    $phoneProp = $_REQUEST['ORDER_PROP_11'];
  }

if(!preg_match("/^[0-9]{11,15}+$/", $phoneProp) && $_REQUEST['soa-action'] == 'saveOrderAjax')
{
  echo "ERROR_PHONE";
} 
else 
{
  $APPLICATION->IncludeComponent(
    "bitrix:sale.order.ajax",
    "cart",
    array(
      "ACTION_VARIABLE" => "soa-action",
      "USER_SELECTION"=>$_REQUEST["use_user_selection"]=="Y"?"Y":"N",
      "COL_PAYMENT" => htmlspecialcharsbx($_REQUEST['COL_PAYMENT']),
      "ADDITIONAL_PICT_PROP_2" => "-",
      "ALLOW_APPEND_ORDER" => "Y",
      "ALLOW_AUTO_REGISTER" => "Y",
      "ALLOW_NEW_PROFILE" => "N",
      "ALLOW_USER_PROFILES" => "N",
      "BASKET_IMAGES_SCALING" => "adaptive",
      "BASKET_POSITION" => "after",
      "COMPATIBLE_MODE" => "N",
      "DELIVERIES_PER_PAGE" => "9",
      "DELIVERY_FADE_EXTRA_SERVICES" => "N",
      "DELIVERY_NO_AJAX" => "H",
      "DELIVERY_NO_SESSION" => "Y",
      "DELIVERY_TO_PAYSYSTEM" => "p2d",
      "DISABLE_BASKET_REDIRECT" => "Y",
      "EMPTY_BASKET_HINT_PATH" => "/catalog/",
      "HIDE_ORDER_DESCRIPTION" => "N",
      "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
      "PATH_TO_AUTH" => "/auth/",
      "PATH_TO_BASKET" => "/basket/",
      "PATH_TO_PAYMENT" => "payment.php",
      "PATH_TO_PERSONAL" => "/personal/",
      "PAY_FROM_ACCOUNT" => "Y",
      "PAY_SYSTEMS_PER_PAGE" => "9",
      "PICKUPS_PER_PAGE" => "5",
      "PICKUP_MAP_TYPE" => "yandex",
      "PRODUCT_COLUMNS_HIDDEN" => "",
      "PRODUCT_COLUMNS_VISIBLE" => array(
        0 => "PREVIEW_PICTURE",
        1 => "PROPS",
      ),
      "PROPS_FADE_LIST_1" => "",
      "SEND_NEW_USER_NOTIFY" => "Y",
      "SERVICES_IMAGES_SCALING" => "adaptive",
      "SET_TITLE" => "Y",
      "SHOW_BASKET_HEADERS" => "Y",
      "SHOW_COUPONS_BASKET" => "N",
      "SHOW_COUPONS_DELIVERY" => "N",
      "SHOW_COUPONS_PAY_SYSTEM" => "N",
      "SHOW_DELIVERY_INFO_NAME" => "Y",
      "SHOW_DELIVERY_LIST_NAMES" => "Y",
      "SHOW_DELIVERY_PARENT_NAMES" => "Y",
      "SHOW_MAP_IN_PROPS" => "N",
      "SHOW_NEAREST_PICKUP" => "N",
      "SHOW_NOT_CALCULATED_DELIVERIES" => "L",
      "SHOW_ORDER_BUTTON" => "always",
      "SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
      "SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
      "SHOW_PICKUP_MAP" => "N",
      "SHOW_STORES_IMAGES" => "Y",
      "SHOW_TOTAL_ORDER_BUTTON" => "Y",
      "SHOW_VAT_PRICE" => "N",
      "SKIP_USELESS_BLOCK" => "Y",
      "SPOT_LOCATION_BY_GEOIP" => "Y",
      "TEMPLATE_LOCATION" => "popup",
      "TEMPLATE_THEME" => "blue",
      "USER_CONSENT" => "N",
      "USER_CONSENT_ID" => "1",
      "USER_CONSENT_IS_CHECKED" => "N",
      "USER_CONSENT_IS_LOADED" => "N",
      "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
      "USE_CUSTOM_ERROR_MESSAGES" => "N",
      "USE_CUSTOM_MAIN_MESSAGES" => "N",
      "USE_ENHANCED_ECOMMERCE" => "N",
      "USE_PHONE_NORMALIZATION" => "Y",
      "USE_PRELOAD" => "N",
      "USE_PREPAYMENT" => "N",
      "USE_YM_GOALS" => "N",
      "COMPONENT_TEMPLATE" => "cart2",
      "CURRENT_PAGE" => SITE_DIR."order/",
    ),
    false
  );
}

//dbgz('Аякс скрипта: '.round(microtime(true) - $arResult['start'], 4).' сек.');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>