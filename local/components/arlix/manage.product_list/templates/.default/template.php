<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

global $USER;
$this->setFrameMode(true);

?>
<script>
  window.is_owner = <?=$arParams['IS_OWNER'] !== 'N' ? 'false' : 'true';?>
</script>
<noscript>
  <strong>We're sorry but <%= htmlWebpackPlugin.options.title %> doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
</noscript>
<div id="app"></div>
<script data-skip-moving=true src="<?=$templateFolder.'/vendors.js';?>"></script>
<script data-skip-moving=true src="<?=$templateFolder.'/app.js';?>"></script>