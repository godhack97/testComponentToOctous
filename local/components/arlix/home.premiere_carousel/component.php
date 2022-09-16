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

  $arSizeCrop = ['width' => 1366, 'height' => 1366];

  $arProductObj = CIBlockElement::getList(['SORT' => 'DESC'], ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'IBLOCK_SECTION_ID' => $arParams['SECTION_ID'], 'ACTIVE' => 'Y'], false, ['nTopCount' => $arParams['COUNT']], []);
  while ($arItem = $arProductObj->GetNextElement()) {
    $arProduct = $arItem->GetFields();
    $arProduct['PROPERTIES'] = $arItem->GetProperties();
    $item = [
      'NAME' => $arProduct['NAME'],
      'ENG_NAME' => $arProduct['PROPERTIES']['NAME_ENG']['VALUE'],
      'DESCRIPTION' => $arProduct['PROPERTIES']['DESCRIPTION']['VALUE'],
      'ENG_DESCRIPTION' => $arProduct['PROPERTIES']['DESCRIPTION_ENG']['VALUE'],
      //'THUMB' => CFile::ResizeImageGet($arProduct['DETAIL_PICTURE'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT)['src'],
    ];


    $productsObj = CIBlockElement::GetByID($arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']);
    if ($rsProduct = $productsObj->GetNextElement()) {
      $selectProduct = $rsProduct->GetFields();
      $arColorObj = CIBlockElement::getList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_COLOR' => $arProduct['PROPERTIES']['SLIDE_COLOR']['VALUE'], 'PROPERTY_PARENT' => $arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']], false, false, []);
      while ($arColorData = $arColorObj->GetNextElement()) {

        $rs = CPrice::GetList(["CATALOG_GROUP_ID" => "ASC"], ["=PRODUCT_ID" => $arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']]);
        while ($arPrice = $rs->Fetch()){
          $arPrice["PRICE"] = floatval($arPrice["PRICE"]);
          $selectProduct['PRICES'][] = $arPrice;
        }
        $item['PRICE'] = $selectProduct['PRICES'][0];

        $arColor = $arColorData->GetFields();
        $arColor['PROPERTIES'] = $arColorData->GetProperties();
        $item['IMAGE_ID'] = $arColor['PROPERTIES']['IMAGES']['VALUE'][0];
        $item['THUMB'] = CFile::ResizeImageGet($arColor['PROPERTIES']['IMAGES']['VALUE'][0], $arSizeCrop, BX_RESIZE_IMAGE_EXACT)['src'];
      }
      $item['LINK'] = $selectProduct['DETAIL_PAGE_URL'].'?setColor='.$arProduct['PROPERTIES']['SLIDE_COLOR']['VALUE'];
    }
    $arResult['ITEMS'][] = $item;
  }

  $this->includeComponentTemplate();
?>
