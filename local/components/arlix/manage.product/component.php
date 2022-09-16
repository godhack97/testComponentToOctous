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
  use \Bitrix\Catalog\StoreProductTable;
  use \Bitrix\Catalog\StoreTable;
  use Bitrix\Highloadblock\HighloadBlockTable;

  Loader::includeModule('highloadblock');
  Loader::includeModule('catalog');

  $arHLBlock = HighloadBlockTable::getById(HL_COLOR_ID)->fetch();
  $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
  $strEntityDataClass = $obEntity->getDataClass();

  // Получим ID товара из GET параметра (редактирование) или NULL (создание)
  $productID = NULL;
  if (isset($_REQUEST['product-id'])) {
    if(preg_match('/\d{4}$/', trim(htmlspecialcharsbx($_REQUEST['product-id'])))){
      $productID = trim(htmlspecialcharsbx($_REQUEST['product-id']));
    }
    else{
      $requestSplit = explode('?', trim(htmlspecialcharsbx($_REQUEST['product-id'])));
      if(count($requestSplit) > 1){
        $productID = $requestSplit[0];
      }
      else{
        $productID = str_replace('?', '', trim(htmlspecialcharsbx($_REQUEST['product-id'])));
      }
    }
  }
  
  // Получение всех доступных опций для выбора ---------------------------------------------
  $arResult['ENUM'] = [];//$arEnums = [];

  // ВСЕ размеры для SELECT
  $enumSizesObj = CIBlockPropertyEnum::GetList(['VALUE' => 'ASC', 'ID' => 'DESC'], ['IBLOCK_ID' => CATALOG_OFFERS_ID, 'CODE' => 'SIZES']);
 
  $last = '';
  while ($tmp = $enumSizesObj->GetNext()) {
    $tval = trim($tmp['VALUE']);
    if ($tval != $last) {
      $arResult['ENUM']['SIZES'][$tmp['ID']] = $last = $tval;
    }
  }

  $rsParentSection = CIBlockSection::GetList(['sort' => 'asc'], ['IBLOCK_ID' => CATALOG_ID, 'DEPTH_LEVEL' => '1', 'ACTIVE' => 'Y']);
  while ($arParentSection = $rsParentSection->GetNext()) {
    $arResult['SECTIONS'][$arParentSection['ID']] = $arParentSection['NAME'];
    $arFilter = ['IBLOCK_ID' => $arParentSection['IBLOCK_ID'],'IBLOCK_SECTION_ID' => $arParentSection['ID'], 'ACTIVE' => 'Y']; // выберет потомков без учета активности
    $rsSect = CIBlockSection::GetList(['left_margin' => 'asc'], $arFilter);
    while ($arSect = $rsSect->GetNext()) {
      $arResult['SECTIONS'][$arSect['ID']] = $arSect['NAME'];
    }
  }
  
  // ВСЕ названия материалов для SELECT
  $enumMaterialsObj = CIBlockElement::getList([], ['IBLOCK_ID' => MATERIALS_ID], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_NAME_EN']);
  while ($tmp = $enumMaterialsObj->GetNext()) {
    $arResult['ENUM']['MATERIALS'][$tmp['ID']] = ['NAME' => $tmp['NAME'], 'NAME_EN' => $tmp['PROPERTY_NAME_EN_VALUE']];
  }

  // ВСЕ цвета для SELECT
  $enumColorsObj = $strEntityDataClass::getList();
  while ($arItemColor = $enumColorsObj->Fetch()) {
    $arResult['ENUM']['COLORS'][$arItemColor['UF_XML_ID']] = ['NAME' => mb_strtoupper($arItemColor['UF_NAME']), 'NAME_EN' => strtoupper($arItemColor['UF_NAME_EN']), 'HEX' => $arItemColor['UF_HEX']];
  }
  
  // Если редактирование - вытащим данные
  if ($productID) {
    // Собираем данные для продукта ----------------------------------------------------------
    $arProduct = CCatalogProduct::GetByIDEx($productID, true);
    
    global $USER;
    
    if(!$arProduct){
      $productsObj = CIBlockElement::GetByID($productID);
      if ($rsProduct = $productsObj->GetNextElement()) {
        $arProduct = $rsProduct->GetFields();
        $arProduct['PROPERTIES'] = $rsProduct->GetProperties();
        $rs = CPrice::GetList(["CATALOG_GROUP_ID" => "ASC"], ["=PRODUCT_ID" => $productID]);
        while ($arPrice = $rs->Fetch()){
          $arPrice["PRICE"] = floatval($arPrice["PRICE"]);
          $arProduct['PRICES'][] = $arPrice;
        }
      }
    }
    
    $arSection = [];
    $obGroups = CIBlockElement::GetElementGroups($productID, true);
    while($arGroup = $obGroups->Fetch()) {
      $arSection[] = $arGroup["ID"];
    }

    //$arResult = [];
    $arResult['ID'] = $productID;
    $arResult['ACTIVE'] = $arProduct['ACTIVE'];
    $arResult['NAME'] = $arProduct['NAME'];
    $arResult['SECTION'] = $arSection;
    $arResult['DETAIL_TEXT'] = $arProduct['DETAIL_TEXT'];
    $arResult['ARTICLE'] = $arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'];
    $arResult['NAME_EN'] = $arProduct['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'];
    $arResult['DETAIL_TEXT_EN'] = html_entity_decode($arProduct['PROPERTIES']['ENG_DESCRIPTION']['VALUE']['TEXT']);
    $arResult['MODERATION'] = $arProduct['PROPERTIES']['MODERATION']['VALUE'];

    // Цены
    foreach ($arProduct['PRICES'] as $arPrice) {
      if ($arPrice['CURRENCY'] == 'USD') {
        $arResult['PRICE_USD'] = $arPrice["PRICE"];//is_float($arPrice["PRICE"]) ? str_replace([','], ['.'], $arPrice["PRICE"]) : round($arPrice["PRICE"], 0);
      }

      if ($arPrice['CURRENCY'] == 'RUB' && (!isset($arResult['PRICE_RUB']) || $arResult['PRICE_RUB'] < $arPrice['PRICE'])) {
        $arResult['PRICE_RUB'] = $arPrice["PRICE"];//is_float($arPrice["PRICE"]) ? str_replace([','], ['.'], $arPrice["PRICE"]) : round($arPrice["PRICE"], 0);
      }
    }
  
    $arPhotoElObj = CIBlockElement::getList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_PARENT' => $productID], false, false, ['ID', 'IBLOCK_ID', 'ACTIVE', 'PROPERTY_IMAGES', 'PROPERTY_COLOR']);
    while ($arPhoto = $arPhotoElObj->GetNext()) {
      $tmp = [];
      $tmp['CODE'] = $arPhoto['PROPERTY_IMAGES_VALUE'];
      $tmp['PATH'] = CFile::ResizeImageGet($arPhoto['PROPERTY_IMAGES_VALUE'], ['width' => 124, 'height' => 184], BX_RESIZE_IMAGE_EXACT)['src'];
      $tmp['FULLPATH'] = CFile::GetPath($arPhoto['PROPERTY_IMAGES_VALUE']);
    
      $arResult['COLORS_SIZES'][$arPhoto['PROPERTY_COLOR_VALUE']]['ACTIVE'] = $arPhoto['ACTIVE'];
      $arResult['COLORS_SIZES'][$arPhoto['PROPERTY_COLOR_VALUE']]['PHOTOS'][] = $tmp;
    }
    // Получим все оферы товара
    $res = CCatalogSKU::getOffersList($productID, 0, ['ACTIVE' => 'Y']);
  
    // Для каждого офера
    foreach ($res[$productID] as $offerID => $val) {
      // Получим данные офера
      $arOffer = CCatalogProduct::GetByIDEx($val['ID'], true);
      
      // Собираем для цвета оффера его размеры
      $arResult['COLORS_SIZES'][$arOffer['PROPERTIES']['COLOR']['VALUE']]['SIZES'][] = $arOffer['PROPERTIES']['SIZES']['VALUE_ENUM'];
      $arResult['COLORS_SIZES'][$arOffer['PROPERTIES']['COLOR']['VALUE']]['SIZES'][] = $arOffer['PROPERTIES']['SIZES']['VALUE_ENUM'];

      if (!in_array($arOffer['PROPERTIES']['SIZES']['VALUE_ENUM'], $arResult['SIZES'][$arOffer['PROPERTIES']['COLOR']['VALUE']])) {
        $arResult['SIZES'][$arOffer['PROPERTIES']['COLOR']['VALUE']][] = trim($arOffer['PROPERTIES']['SIZES']['VALUE_ENUM']);
      }
    }
    
    // Получим все данные о материалах
    $arMaterialObj = CIBlockElement::getList([], ['IBLOCK_ID' => COMPOSITION_ID, 'PROPERTY_PARENT' => $productID], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_MATERIAL', 'PROPERTY_VALUE']);
    while ($arMaterial = $arMaterialObj->GetNext()) {
      // Всё равно процент вытаскиваем - пусть и названия рядом в $arResult будут
      $arResult['MATERIALS'][$arMaterial['PROPERTY_MATERIAL_VALUE']]['NAME'] = $arResult['ENUM']['MATERIALS'][$arMaterial['PROPERTY_MATERIAL_VALUE']]['NAME'];
      $arResult['MATERIALS'][$arMaterial['PROPERTY_MATERIAL_VALUE']]['NAME_EN'] = $arResult['ENUM']['MATERIALS'][$arMaterial['PROPERTY_MATERIAL_VALUE']]['NAME_EN'];
    
      // Процент содержания
      $percent = trim($arMaterial['PROPERTY_VALUE_VALUE'], '% ');
      $arResult['MATERIALS'][$arMaterial['PROPERTY_MATERIAL_VALUE']]['VALUE'] = $percent . ' %';
    }
    //print_r('<pre>');
    //print_r($arResult);
    //print_r('</pre>');
  }

  $this->includeComponentTemplate();
?>
