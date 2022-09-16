<?
//define('NEED_AUTH', true);
//define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Просмотр заказа");
$APPLICATION->AddChainItem('Заказ '.strtok($_GET['ORDER_ID'],'?'));
?>
<?
global $USER;
if((int)$by > 0 && !$USER->IsAuthorized())
{
  //$USER->Authorize($by);
  //LocalRedirect('');
}
elseif(!$USER->IsAuthorized())
{
  //LocalRedirect('/personal/');
}
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
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.detail", 
	"template",
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"ID" => $ORDER_ID,
		"PATH_TO_CANCEL" => "",
		"PATH_TO_COPY" => "",
		"PATH_TO_LIST" => SITE_DIR."personal/order/",
		"PATH_TO_PAYMENT" => SITE_DIR."personal/order/payment/index.php",
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
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>