<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->createFrame()->begin("");
?>
<div class="info-nav__item hover favorites-head">
	<a href="<?= \Bitrix\Main\Config\Option::get("tokit.favorites", "personal_url") ?>" class="item-link favorites-link">
		<img class="icon-heart" src="<?= SITE_TEMPLATE_PATH ?>/images/icons/icon-favorite.svg">
		<div class="favorites-count<? if ($arResult["COUNT"] < 1) { ?> is-hide<? } ?>"><?= $arResult["COUNT"] ?></div>
	</a>
</div>