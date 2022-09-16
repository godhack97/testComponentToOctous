<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("DPD");
?><?$APPLICATION->IncludeComponent(
	"ipol:ipol.dpdPickup", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"USER_LOCATION" => "Орск",
		"SHOP_LOCATION" => "Москва",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>