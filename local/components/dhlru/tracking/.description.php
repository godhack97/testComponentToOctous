<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("DHLRU_TRACKING_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("DHLRU_TRACKING_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 50,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "dhlru",
			"NAME" => GetMessage("DHLRU_GROUP"),
			"SORT" => 30,
			"CHILD" => array(
				"ID" => "dhlru_tracking",
			),
		),
	),
);

?>