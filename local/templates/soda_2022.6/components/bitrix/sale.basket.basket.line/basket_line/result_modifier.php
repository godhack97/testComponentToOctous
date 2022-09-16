<?php
global $APPLICATION;
CModule::IncludeModule('iblock');
if(is_array($arResult['CATEGORIES']['READY']) && count($arResult['CATEGORIES']['READY'])){
  foreach ($arResult['CATEGORIES']['READY'] as &$item) {
    if ($item['PRODUCT_ID']) {
      $arNoPhotoProductsIds[] = $item['PRODUCT_ID'];
    }
  }
}

if (!empty($arNoPhotoProductsIds))
{
    $getForColorProductPhoto = getForColorProductPhoto($arNoPhotoProductsIds);
    $arNoPhotoProducts = $getForColorProductPhoto['NO_PHOTO_PRODUCTS'];
    $arWithPhotoProducts = $getForColorProductPhoto['WITH_PHOTO_PRODUCTS'];
}

use Bitrix\Sale;

if (!CModule::IncludeModule("sale"))
{
    ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));

    return;
}

$cp = $this->__component; // объект компонента

if (is_object($cp))
{
    // Получаем товары в корзине
    $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    $count = 0;
    $orderBasket = $basket->getOrderableItems();
    $ItemProps = [];

    foreach ($orderBasket as $arItem)
    {
        $basketPropertyCollection = $arItem->getPropertyCollection();
        $ItemProps[$arItem->getId()] = $basketPropertyCollection->getPropertyValues();
        $count += $arItem->getQuantity();
    }

    if(is_array($arResult['CATEGORIES']['READY']) && count($arResult['CATEGORIES']['READY'])){
      foreach ($arResult['CATEGORIES']['READY'] as &$item) {
        if ($ItemProps[$item['ID']])
          $item['PROP'] = $ItemProps[$item['ID']];
      }
  
      unset($item);
    }
    $cp->arResult['PRODUCT_COUNT'] = $count;
}

\CModule::IncludeModule("catalog");
\CModule::IncludeModule("iblock");

$basketCurrency = false;
if(is_array($arResult['CATEGORIES']['READY']) && count($arResult['CATEGORIES']['READY'])){
  foreach ($arResult['CATEGORIES']['READY'] as &$item) {
    
    //dbgz($item['NAME']);
    
    if ($arNoPhotoProducts[$item['PRODUCT_ID']]) {
      $arWithPhotoProductThis = $arNoPhotoProducts[$item['PRODUCT_ID']];
      $itemDetailPicture = $arWithPhotoProducts[$arWithPhotoProductThis['CML2_LINK']][$arWithPhotoProductThis['COLOR']]['DETAIL_PICTURE'];
    }
    if ($itemDetailPicture) {
      $item['DETAIL_PICTURE'] = $itemDetailPicture;
    }
    
    $mxResult = \CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
    
    if (is_array($mxResult)) {
      $res = \CIBlockElement::GetList([], ['ID' => $mxResult['ID']], false, false, ['NAME', 'PROPERTY_ENG_NAIMENOVANIE']);
      if ($ar_res = $res->Fetch()) {
        if (SITE_ID == 's2') {
          $item['NAME'] = $ar_res['PROPERTY_ENG_NAIMENOVANIE_VALUE'];
        } else {
          $item['NAME'] = $ar_res['NAME'];
        }
      }
      
      $resEl = CIBlockElement::GetList([], [
        'ID' => $item['PRODUCT_ID'],
        'IBLOCK_ID' => IBLOCK_OFFERS,
        'ACTIVE_DATE' => 'Y',
        'ACTIVE' => 'Y',
      ], false, array('nPageSize' => 1), [
        'ID', 'IBLOCK_ID', 'NAME', 'SORT', 'DATE_ACTIVE_FROM', 'PROPERTY_*'
      ]);
      if ($obEl = $resEl->GetNextElement()) {
        $arFieldsEl = $obEl->GetProperties();
        $color = $arFieldsEl['COLOR']['VALUE'];
        if (strlen($color) > 0) {
          $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'SORT', 'DATE_ACTIVE_FROM', 'PROPERTY_*'];
          $arFilter = [
            'IBLOCK_ID' => COLORS_ID,
            'ACTIVE_DATE' => 'Y',
            'ACTIVE' => 'Y',
            'PROPERTY_PARENT' => $mxResult['ID'],
            'PROPERTY_COLOR' => $color,
          ];
          $res = CIBlockElement::GetList([], $arFilter, false, array('nPageSize' => 1), $arSelect);
          if ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetProperties();
            $images = $arFields['IMAGES']['VALUE'];
            if ($images && is_array($images) && count($images) > 0) {
              $arPhotoSmall = CFile::ResizeImageGet(
                $images[0],
                [
                  'width' => 124,
                  'height' => 184
                ],
                BX_RESIZE_IMAGE_EXACT, false, false, false, 75
              );
              $item['PICTURE_SRC'] = $arPhotoSmall['src'];
            }
          }
        }
      }
      
    }
    
    if (empty($item['PICTURE_SRC'])) {
      if (!empty($item['PROPERTY_MORE_PHOTO_VALUE'])) {
        $arMoreImages = explode(',', $item['PROPERTY_MORE_PHOTO_VALUE']);
        $arCropImage = CFile::ResizeImageGet($arMoreImages[0], array('width' => 124, 'height' => 184), BX_RESIZE_IMAGE_EXACT, false, false, false, 75);
        $item['PICTURE_SRC'] = $arCropImage['src'];
      } elseif (!empty($item['DETAIL_PICTURE'])) {
        $arCropImage = CFile::ResizeImageGet($arMoreImages[0], array('width' => 124, 'height' => 184), BX_RESIZE_IMAGE_EXACT, false, false, false, 75);
        $item['PICTURE_SRC'] = $arCropImage['src'];
      }
    }
		
		$arResult['USE_EXTEND_CURRENCY'] = isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) && $_COOKIE["CATALOG_CURRENCY"] !== $item['CURRENCY'];
		$item['EXTEND_CURRENCY'] = isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) ? strtoupper($_COOKIE["CATALOG_CURRENCY"]) : $item['CURRENCY'];
		$item['SUM_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($item['SUM_VALUE'], $item['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
		$item['PRICE_FMT_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($item['PRICE'], $item['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
		$item['FULL_PRICE_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($item['PRICE'], $item['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
		$item['PRICE_FORMATED_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($item['PRICE'], $item['CURRENCY'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
		
		if(!$basketCurrency){
			$basketCurrency = $item['CURRENCY'];
		}
  }
}
$arResult['TOTAL_PRICE_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['TOTAL_PRICE_RAW'], $basketCurrency, $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);

unset($item);

//dbgz($arResult["CATEGORIES"]["READY"]);