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
  use Bitrix\Highloadblock\HighloadBlockTable;

  Loader::includeModule('highloadblock');

  $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(HL_COLOR_ID)->fetch();
  $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
  $strEntityDataClass = $obEntity->getDataClass();

  // Получим GET параметр поиска
  $searchData = $arParams['REQUEST'];
  $isOwner = $arParams['IS_OWNER'];//trim(htmlspecialcharsbx($_GET['is_owner']));

  $userID = $USER->GetID();

  // Собираем фильтр
  $arFilter = [];
  $arFilter['IBLOCK_ID'] = CATALOG_ID;
  if($isOwner !== 'N'){
    $arFilter['PROPERTY_PRODUCT_OWNER'] = $userID;
  }
  $arResult['RESPONSE'] = [
    'items' => [],
    'page' => 1
  ];
  
  switch ($searchData){
    case 'newProduct':
      // Получаем выборку
      $days = intval(\Bitrix\Main\Config\Option::get('sodamoda.core', 'product_is_new_days'));
      $arFilter['>=DATE_CREATE'] = array(ConvertTimeStamp(time() - 86400 * $days, 'FULL'));
      $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'ACTIVE', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
      
      if ($productsObj->result->num_rows == 0) {
        unset($arFilter['PROPERTY_CML2_ARTICLE']);
        $arFilter['NAME'] = '%' . $searchData . '%';
        $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'ACTIVE', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
      }
      break;
    case 'deactiveProduct':
      // Получаем выборку
      $arFilter['ACTIVE'] = 'N';
      $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'ACTIVE', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
      if ($productsObj->result->num_rows == 0) {
        unset($arFilter['PROPERTY_CML2_ARTICLE']);
        $arFilter['NAME'] = '%' . $searchData . '%';
        $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'ACTIVE', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
      }
      break;
    default:
      if ($searchData != '') {
        // Получаем выборку
        $arFilter['PROPERTY_CML2_ARTICLE'] = '%' . $searchData . '%';
        $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'ACTIVE', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
        if ($productsObj->result->num_rows == 0) {
          unset($arFilter['PROPERTY_CML2_ARTICLE']);
          $arFilter['NAME'] = '%' . $searchData . '%';
          $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'ACTIVE', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
        }
      }
      else {
        $productsObj = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'ACTIVE', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_PRODUCT_OWNER']);
      }
      break;
  }

  // Собираем нужные данные
  $arOffersCS = [];
  while ($arItem = $productsObj->GetNext()) {
    $productID = $arItem['ID'];

    $arOffersCS[$productID]['ID'] = $productID;
    $arOffersCS[$productID]['NAME'] = $arItem['NAME'];
    $arOffersCS[$productID]['ARTICLE'] = $arItem['PROPERTY_CML2_ARTICLE_VALUE'];

    // Получим все оферы товара
    $res = CCatalogSKU::getOffersList($productID, 0, ['ACTIVE' => 'Y']);

    foreach ($res[$productID] as $val) {
      // Получим данные офера
      $arOffer = CCatalogProduct::GetByIDEx($val['ID'], true);

      // Собираем для цвета оффера его размеры
      $arOffersCS[$productID]['COLORS_SIZES'][$arOffer['PROPERTIES']['COLOR']['VALUE']]['SIZES'][] = $arOffer['PROPERTIES']['SIZES']['VALUE_ENUM'];

      // Найдём имя цвета (если ещё не было найдено)
      if (!isset($arOffersCS[$productID]['COLORS_SIZES'][$arOffer['PROPERTIES']['COLOR']['VALUE']]['COLOR_NAME'])) {
        $resData = $strEntityDataClass::getList(['select' => ['ID', 'UF_NAME'], 'filter' => ['UF_XML_ID' => $arOffer['PROPERTIES']['COLOR']['VALUE']]]);
        $arItemC = $resData->Fetch();
        $arOffersCS[$productID]['COLORS_SIZES'][$arOffer['PROPERTIES']['COLOR']['VALUE']]['COLOR_NAME'] = $arItemC['UF_NAME'];
      }
    }

    // Получим фото из инфоблока фотографий по ID товара (Родитель) и Цвету товара (если ещё не было найдено 1 фото)
    // Получим фото из инфоблока фотографий по ID товара (Родитель) и Цвету товара (если ещё не было найдено 1 фото)
    if(!isset($arOffersCS[$productID]['PREVIEW_PHOTO']))
    {
      $arPhotoElObj = CIBlockElement::getList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_PARENT' => $productID], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_IMAGES']);
    
      // Возьмём 1 фото (без проверки на доступность файла - берём первое фото)
      $previewPhoto = $arPhotoElObj->GetNext()['PROPERTY_IMAGES_VALUE'];
      $arOffersCS[$productID]['PREVIEW_PHOTO'] = CFile::ResizeImageGet($previewPhoto, ['width' => 124, 'height' => 184], BX_RESIZE_IMAGE_EXACT)['src'];
    
      // Возьмём 1 фото (с проверкой на доступность файла) ---------------------------------------------------------------------
      // $previewPhoto = false;
      // while($tmp = $arPhotoElObj->GetNext())
      // {
      // $previewPhoto = $tmp['PROPERTY_IMAGES_VALUE'];
    
      // if(file_exists($_SERVER['DOCUMENT_ROOT'].CFile::GetPath($tmp['PROPERTY_IMAGES_VALUE'])))
      // {
      // $arResult['ITEMS'][$key]['PREVIEW_PHOTO'] = CFile::GetPath($tmp['PROPERTY_IMAGES_VALUE']);
      // break;
      // }
      // }
    
      // // Если всё ещё не нашли - нет фото, кидаем ссылку на битое
      // if(!isset($arResult['ITEMS'][$key]['PREVIEW_PHOTO']))
      // $arResult['ITEMS'][$key]['PREVIEW_PHOTO'] = CFile::GetPath($previewPhoto);
      // если есть уверенность, что битых файлов фото нет - этот кусок закомментить, раскомментить кусок выше
      // -----------------------------------------------------------------------------------------------------------------------
    }
    $previewPhoto = CFile::ResizeImageGet($previewPhoto, ['width' => 124, 'height' => 184], BX_RESIZE_IMAGE_EXACT)['src'];
    if(empty($arOffersCS[$productID]['PREVIEW_PHOTO'])){
      $previewPhoto = SITE_TEMPLATE_PATH.'/images/default.jpg';
    }
    
    $colorsList = [];
    $sizesList = [];
    foreach ($arOffersCS[$productID]['COLORS_SIZES'] as $val) {
      if (!in_array($val['COLOR_NAME'], $colorsList)) {
        $colorsList[] = $val['COLOR_NAME'];
      }

      foreach ($val['SIZES'] as $size) {
        if (!in_array($size, $sizesList)) {
          $sizesList[] = $size;
        }
      }
    }
    sort($sizesList);
    $arResult['RESPONSE']['items'][] = [
      'id' => $productID,
      'active' => $arItem['ACTIVE'],
      'name' => $arItem['NAME'],
      'article' => $arItem['PROPERTY_CML2_ARTICLE_VALUE'],
      'preview_photo' => $previewPhoto,
      'colors' => implode(',', $colorsList),
      'sizes' => implode(',', $sizesList),
    ];
  }

  $arResult['ITEMS'] = $arOffersCS;

  $this->includeComponentTemplate();
?>
