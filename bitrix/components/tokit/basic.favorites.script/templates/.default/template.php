<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arJSParams = array(
	'CUR_IN_FAV' => $arResult['CUR_IN_FAV'],
	'BUTTON' => array(
		'CLASS' => \Bitrix\Main\Config\Option::get("tokit.favorites", "button_class"),
		'CLASS_ACTIVE' => \Bitrix\Main\Config\Option::get("tokit.favorites", "button_active"),
		'URL' => \Bitrix\Main\Config\Option::get("tokit.favorites", "personal_url")
	),
	'COOKIE_NAME' => \Bitrix\Main\Config\Option::get("tokit.favorites", "cookie_name"),
	'AUTHORIZED' => $USER->IsAuthorized()
);
?>
<script type="text/javascript">
	window.paramsFans = <?=CUtil::PhpToJSObject($arJSParams, false, true); ?>;
	toKitFavs.init(<?= CUtil::PhpToJSObject($arJSParams, false, true); ?>);
</script>