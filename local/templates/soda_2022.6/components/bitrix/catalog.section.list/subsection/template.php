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

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$arViewModeList = $arResult['VIEW_MODE_LIST'];

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
?>
<? if (0 < $arResult["SECTIONS_COUNT"]) : ?>

	<div class="subsections-list">
		<a href="<? echo $arResult['SECTION']['SECTION_PAGE_URL']; ?>" class="sub-item black<?= (!$arParams['ACTIVE_TOP_SECTION']) ? ' active' : '' ?>"><?= Loc::getMessage("CATALOG_SECTION_SHOW_ALL") ?></a>

		<? foreach ($arResult['SECTIONS'] as &$arSection) : ?>
			<a href="<? echo $arSection['SECTION_PAGE_URL']; ?>" class="sub-item black<?= ($arParams['ACTIVE_TOP_SECTION'] == $arSection['ID']) ? ' active' : '' ?>">
				<?= (Loc::getCurrentLang() == 'en') ? $arSection['UF_NAME_EN'] : $arSection['NAME']; ?>
			</a>
		<? endforeach ?>

	</div>

<? endif ?>