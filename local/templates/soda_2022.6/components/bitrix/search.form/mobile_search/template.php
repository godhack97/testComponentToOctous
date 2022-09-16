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
$this->setFrameMode(true);?>
<div class="wrapper">
  <form action="<?=$arResult["FORM_ACTION"]?>" method="get">
    <input class="field-text" type="text" name="q" placeholder="Поиск" value="" />
    <input class="send hover" type="submit" name="send" value="Найти">
  </form>
</div>