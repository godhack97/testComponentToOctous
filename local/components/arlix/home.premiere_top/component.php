<?php
  if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
  }

  /** @global CMain $APPLICATION */
  /** @global CUser $USER */
  /** @global CDatabase $DB */
  /** @var CBitrixComponent $this */
  /** @var array $arParams */
  /** @var array $arResult */
  /** @var string $componentName */
  /** @var string $componentPath */
  /** @var string $componentTemplate */
  /** @var string $parentComponentName */
  /** @var string $parentComponentPath */
  /** @var string $parentComponentTemplate */
  $this->setFrameMode(false);

  use Bitrix\Main\Loader;

  Loader::includeModule('iblock');


  $arProductObj = CIBlockElement::getList(['SORT' => 'DESC'], ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'IBLOCK_SECTION_ID' => $arParams['SECTION_ID'], 'ACTIVE' => 'Y'], false, ['nTopCount' => $arParams['COUNT']], []);
  while ($arItem = $arProductObj->GetNextElement()) {
    $arProduct = $arItem->GetFields();
    $arProduct['PROPERTIES'] = $arItem->GetProperties();
    $item = [
      'NAME' => $arProduct['NAME'],
      'THUMB' => CFile::GetPath($arProduct['PROPERTIES']['SLIDE_PREVIEW']['VALUE']),
    ];


    $productsObj = CIBlockElement::GetByID($arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']);
    if ($rsProduct = $productsObj->GetNextElement()) {
      $selectProduct = $rsProduct->GetFields();
      $arColorObj = CIBlockElement::getList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_COLOR' => $arProduct['PROPERTIES']['SLIDE_COLOR']['VALUE'], 'PROPERTY_PARENT' => $arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']], false, false, []);
      while ($arColorData = $arColorObj->GetNextElement()) {
        $arColor = $arColorData->GetFields();
        $arColor['PROPERTIES'] = $arColorData->GetProperties();
      }
      $item['LINK'] = $selectProduct['DETAIL_PAGE_URL'].'?setColor='.$arProduct['PROPERTIES']['SLIDE_COLOR']['VALUE'];
    }
    $arResult['ITEMS'][] = $item;
  }

  $this->includeComponentTemplate();
?>
