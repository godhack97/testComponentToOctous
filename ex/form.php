<?
//require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Form");

//echo phoneFormat('79163400966');

//echo $APPLICATION->ShowTitle() .'<br>'. $APPLICATION->GetCurPageParam();
$url = SITE_SERVER_PROTOCOL . SITE_SERVER_NAME .'/bitrix/admin/sale_order_view.php?lang=ru&ID=12159';
//telegramToChannel('Тест, заказ <a href="'. $url .'">'. $url .' ссылка</a>', '-1001436057583');

?><?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	".default", 
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"EDIT_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"LIST_URL" => "",
		"SEF_MODE" => "N",
		"SUCCESS_URL" => "",
		"USE_EXTENDED_ERRORS" => "N",
		"WEB_FORM_ID" => "6",
		"COMPONENT_TEMPLATE" => ".default",
		"VARIABLE_ALIASES" => array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		)
	),
	false
);?>
  <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>