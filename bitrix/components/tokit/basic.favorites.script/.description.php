<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("TOKIT_FAVORITES_ADD_DESC_LIST"),
	"DESCRIPTION" => GetMessage("TOKIT_FAVORITES_ADD_DESC_LIST_DESC"),
	"CACHE_PATH" => "Y",
	"SORT" => 150,
	"PATH" => array(
		"ID" => "tokit",
		"NAME" => GetMessage("TOKIT_FAVORITES_TOP"),
		"CHILD" => array(
			"ID" => "favorites",
			"NAME" => GetMessage("TOKIT_FAVORITES_ADD_DESC_GROUP"),
			"SORT" => 100,
		),
	),
);
?>