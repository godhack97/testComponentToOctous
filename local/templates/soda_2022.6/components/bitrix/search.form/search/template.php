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
<div class="search hover">
  <div class="name">поиск</div>
  <div class="field">
    <form action="<?=$arResult["FORM_ACTION"]?>">
      <input type="text" name="q" value="" class="query" size="15" maxlength="50" />
      <input name="s" class="send hover" type="submit" value="Найти" />
    </form>
  </div>
  <div class="overlap js-set-search"></div>
</div>