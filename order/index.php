<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?
  $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/libs/fias/js/core.js");
  $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/libs/fias/js/fias.js");
  //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/libs/fias/js/fias_zip.js");
  $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/libs/fias/css/style.css");
  global $USER;

  use Bitrix\Main\Diag\Debug;
  
  //Debug::startTimeLabel('load_full_component_order');
?>

<? /*if($USER->IsAdmin()):?>
  <div id="cdek-pickups" class="cdek-pickups">
    <?$APPLICATION->IncludeComponent(
    "ipol:ipol.sdekPickup", 
    "",
    array(
      "COMPONENT_TEMPLATE" => "order",
      "NOMAPS" => "N",
      "CNT_DELIV" => "Y",
      "CNT_BASKET" => "Y",
      "FORBIDDEN" => array(
        0 => "courier",
      ),
      "PAYER" => "1",
      "PAYSYSTEM" => "",
      "COUNTRIES" => array(
        0 => "rus",
      ),
      "CITIES" => array(
      ),
      "COMPOSITE_FRAME_MODE" => "A",
      "COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITH_STUB"
    ),
    false
  );?>
  </div>
<?endif */
$GLOBALS['timeExecuteOrder'] = microtime(true);
?>

<div id="order-form-wrap">
  <?$APPLICATION->SetTitle("Корзина"); ?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax", 
	"cart",
	array(
		"ACTION_VARIABLE" => "soa-action",
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
		"DISABLE_BASKET_REDIRECT" => "N",
		"EMPTY_BASKET_HINT_PATH" => "/catalog/",
		"HIDE_ORDER_DESCRIPTION" => "N",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"PATH_TO_AUTH" => "/auth/",
		"PATH_TO_BASKET" => "/basket/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
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
		"SHOW_COUPONS" => "Y",
		"ADDITIONAL_PICT_PROP_8" => "-",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
</div>
<?
  //Debug::endTimeLabel('load_full_component_order');
  
  //Debug::writeToFile(Debug::getTimeLabels());
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>