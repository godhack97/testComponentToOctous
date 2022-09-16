<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\Loader;
use tokit\Favorites\Tools;

$APPLICATION->SetTitle("Избранное");
?>

<?$APPLICATION->IncludeComponent("bitrix:menu", "subsections", Array(
    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
    "CHILD_MENU_TYPE" => "subsections",	// Тип меню для остальных уровней
    "DELAY" => "N",	// Откладывать выполнение шаблона меню
    "MAX_LEVEL" => "1",	// Уровень вложенности меню
    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
    "MENU_CACHE_TYPE" => "N",	// Тип кеширования
    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
    "ROOT_MENU_TYPE" => "subsections",	// Тип меню для первого уровня
    "USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
    "COMPONENT_TEMPLATE" => "subsections"
  ),
  false
);?>

<?
if (Loader::includeModule('tokit.favorites')) 
{
  if($USER->IsAuthorized()) 
  {
    $arSelect = ['ID', 'ELEMENT_ID'];
    $arFilter = ['USER_ID' => $USER->GetId()];
    $GLOBALS['arFilter']['ID'] = Tools::getFavList($arSelect, $arFilter);
  } 
  else 
  {
    $favList = json_decode($_COOKIE['favorites']);
    $GLOBALS['arFilter']['ID'] = $favList;
  }
}

if(count($GLOBALS['arFilter']['ID']))
{
  ?>
  <?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section", 
    "small.elements", 
    array(
      "ACTION_VARIABLE" => "action",
      "ADD_PICT_PROP" => "-",
      "ADD_PROPERTIES_TO_BASKET" => "Y",
      "ADD_SECTIONS_CHAIN" => "N",
      "ADD_TO_BASKET_ACTION" => "ADD",
      "AJAX_MODE" => "N",
      "AJAX_OPTION_ADDITIONAL" => "",
      "AJAX_OPTION_HISTORY" => "N",
      "AJAX_OPTION_JUMP" => "N",
      "AJAX_OPTION_STYLE" => "Y",
      "BACKGROUND_IMAGE" => "-",
      "BASKET_URL" => "/personal/basket.php",
      "BROWSER_TITLE" => "-",
      "CACHE_FILTER" => "N",
      "CACHE_GROUPS" => "Y",
      "CACHE_TIME" => "36000000",
      "CACHE_TYPE" => "A",
      "COMPATIBLE_MODE" => "Y",
      "COMPOSITE_FRAME_MODE" => "A",
      "COMPOSITE_FRAME_TYPE" => "AUTO",
      "CONVERT_CURRENCY" => "N",
      "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
      "DETAIL_URL" => "",
      "DISABLE_INIT_JS_IN_COMPONENT" => "N",
      "DISPLAY_BOTTOM_PAGER" => "Y",
      "DISPLAY_COMPARE" => "N",
      "DISPLAY_TOP_PAGER" => "N",
      "ELEMENT_SORT_FIELD" => "sort",
      "ELEMENT_SORT_FIELD2" => "id",
      "ELEMENT_SORT_ORDER" => "asc",
      "ELEMENT_SORT_ORDER2" => "desc",
      "ENLARGE_PRODUCT" => "STRICT",
      "FILTER_NAME" => "arFilter",
      "HIDE_NOT_AVAILABLE" => "N",
      "HIDE_NOT_AVAILABLE_OFFERS" => "N",
      "IBLOCK_ID" => "8",
      "IBLOCK_TYPE" => "catalogs",
      "INCLUDE_SUBSECTIONS" => "Y",
      "LABEL_PROP" => array(
        0 => "SIZES",
        1 => "ROST",
      ),
      "LAZY_LOAD" => "N",
      "LINE_ELEMENT_COUNT" => "3",
      "LOAD_ON_SCROLL" => "N",
      "MESSAGE_404" => "",
      "MESS_BTN_ADD_TO_BASKET" => "В корзину",
      "MESS_BTN_BUY" => "Купить",
      "MESS_BTN_DETAIL" => "Подробнее",
      "MESS_BTN_SUBSCRIBE" => "Подписаться",
      "MESS_NOT_AVAILABLE" => "Нет в наличии",
      "META_DESCRIPTION" => "-",
      "META_KEYWORDS" => "-",
      "OFFERS_FIELD_CODE" => array(
        0 => "",
        1 => "",
      ),
      "OFFERS_LIMIT" => "0",
      "OFFERS_SORT_FIELD" => "sort",
      "OFFERS_SORT_FIELD2" => "id",
      "OFFERS_SORT_ORDER" => "asc",
      "OFFERS_SORT_ORDER2" => "desc",
      "PAGER_BASE_LINK_ENABLE" => "N",
      "PAGER_DESC_NUMBERING" => "N",
      "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
      "PAGER_SHOW_ALL" => "N",
      "PAGER_SHOW_ALWAYS" => "N",
      "PAGER_TEMPLATE" => ".default",
      "PAGER_TITLE" => "Товары",
      "PAGE_ELEMENT_COUNT" => "18",
      "PARTIAL_PRODUCT_PROPERTIES" => "N",
      "PRICE_CODE" => array(
        0 => "Розница",
      ),
      "PRICE_VAT_INCLUDE" => "Y",
      "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
      "PRODUCT_DISPLAY_MODE" => "N",
      "PRODUCT_ID_VARIABLE" => "id",
      "PRODUCT_PROPS_VARIABLE" => "prop",
      "PRODUCT_QUANTITY_VARIABLE" => "quantity",
      "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
      "PRODUCT_SUBSCRIPTION" => "N",
      "PROPERTY_CODE_MOBILE" => array(
      ),
      "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
      "RCM_TYPE" => "personal",
      "SECTION_CODE" => "",
      "SECTION_ID" => $_REQUEST["SECTION_ID"],
      "SECTION_ID_VARIABLE" => "SECTION_ID",
      "SECTION_URL" => "",
      "SECTION_USER_FIELDS" => array(
        0 => "",
        1 => "",
      ),
      "SEF_MODE" => "N",
      "SET_BROWSER_TITLE" => "N",
      "SET_LAST_MODIFIED" => "N",
      "SET_META_DESCRIPTION" => "N",
      "SET_META_KEYWORDS" => "N",
      "SET_STATUS_404" => "N",
      "SET_TITLE" => "N",
      "SHOW_404" => "N",
      "SHOW_ALL_WO_SECTION" => "N",
      "SHOW_CLOSE_POPUP" => "N",
      "SHOW_DISCOUNT_PERCENT" => "N",
      "SHOW_FROM_SECTION" => "N",
      "SHOW_MAX_QUANTITY" => "N",
      "SHOW_OLD_PRICE" => "N",
      "SHOW_PRICE_COUNT" => "1",
      "SHOW_SLIDER" => "N",
      "SLIDER_INTERVAL" => "3000",
      "SLIDER_PROGRESS" => "N",
      "TEMPLATE_THEME" => "blue",
      "USE_ENHANCED_ECOMMERCE" => "N",
      "USE_MAIN_ELEMENT_SECTION" => "N",
      "USE_PRICE_COUNT" => "N",
      "USE_PRODUCT_QUANTITY" => "N",
      "COMPONENT_TEMPLATE" => ".default",
      "LABEL_PROP_MOBILE" => array(
        0 => "SIZES",
        1 => "ROST",
      ),
      "LABEL_PROP_POSITION" => "top-left"
    ),
    false
  );?>

<?
} else {
?>
<p>В списке избранного ничего нет</p>
<?
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>