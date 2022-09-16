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
  use Bitrix\Main\Localization\Loc;
  
  Loader::includeModule('iblock');
  
  $arSizeCrop = ['width' => 2520, 'height' => 1450];
  $arSizeMiniCrop = ['width' => 450, 'height' => 450];
  
  $limitWord = 13;
  $arProductObj = CIBlockElement::getList(['SORT' => 'DESC'], ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'], false, ['nTopCount' => $arParams['COUNT']], []);
  while ($arItem = $arProductObj->GetNextElement()) {
    $arProduct = $arItem->GetFields();
    $arProduct['PROPERTIES'] = $arItem->GetProperties();
    $item = [];
    //Ru
    $wordList = explode(" ", $arProduct['PROPERTIES']['DESCRIPTION']['VALUE']);
    $wordList = array_slice($wordList, 0, $limitWord);
    $newDescription = implode(" ", $wordList);
    //En
    $wordList = explode(" ", $arProduct['PROPERTIES']['DESCRIPTION_ENG']['VALUE']);
    $wordList = array_slice($wordList, 0, $limitWord);
    $newDescriptionEn = implode(" ", $wordList);
    
    $item = [
      'NAME' => $arProduct['NAME'],
      'ENG_NAME' => $arProduct['PROPERTIES']['NAME_ENG']['VALUE'],
      'DESCRIPTION' => $newDescription,
      'ENG_DESCRIPTION' => $newDescriptionEn,
      //'LINK' => $arProduct['PROPERTIES']['LINK_ELEMENT']['VALUE'],
      'THUMB' => CFile::ResizeImageGet($arProduct['DETAIL_PICTURE'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT)['src'],
      'THUMB_MINI' => CFile::ResizeImageGet($arProduct['DETAIL_PICTURE'], $arSizeMiniCrop, BX_RESIZE_IMAGE_EXACT)['src'],
    ];
    $productsObj = CIBlockElement::GetByID($arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']);
    if ($rsProduct = $productsObj->GetNextElement()) {
      $selectProduct = $rsProduct->GetFields();
      $selectProduct['PROPERTIES'] = $rsProduct->GetProperties();
      
      $arPrices = \CCatalogProduct::GetByIDEx($arProduct['PROPERTIES']['SLIDE_PRODUCT']['VALUE']);
  
      $globalCurrencyUser = isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) ? strtoupper($_COOKIE["CATALOG_CURRENCY"]) : (SITE_ID == 's2' ? 'USD' : 'RUB');
      
      switch ($globalCurrencyUser){
        case 'USD':
          $item['PRICE']['PRICE'] = $arPrices['PRICES'][6]['PRICE'];
          $item['PRICE']['CURRENCY'] = $arPrices['PRICES'][6]['CURRENCY'];
          break;
        case 'AED':
          $item['PRICE']['PRICE'] = $arPrices['PRICES'][7]['PRICE'];
          $item['PRICE']['CURRENCY'] = $arPrices['PRICES'][7]['CURRENCY'];
          break;
        case 'RUB':
        default:
          $item['PRICE']['PRICE'] = $arPrices['PRICES'][1]['PRICE'];
          $item['PRICE']['CURRENCY'] = $arPrices['PRICES'][1]['CURRENCY'];
          break;
      }
  
      $item['LINK'] = $selectProduct['DETAIL_PAGE_URL'].'?setColor='.$arProduct['PROPERTIES']['SLIDE_COLOR']['VALUE'];
    }
    
    $arResult['ITEMS'][] = $item;
    $arResult['TITLE'] = Loc::getCurrentLang() == 'ru' ? $arResult['ITEMS'][0]['NAME'] : $arResult['ITEMS'][0]['ENG_NAME'];
  }
  
  $this->includeComponentTemplate();
?>
