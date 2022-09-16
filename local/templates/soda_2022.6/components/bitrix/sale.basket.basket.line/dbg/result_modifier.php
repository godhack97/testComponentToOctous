<?php
global $APPLICATION;
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
  $ItemProps=[];
  
  foreach ($orderBasket as $arItem) {
    $basketPropertyCollection = $arItem->getPropertyCollection();
    $ItemProps[$arItem->getId()] = $basketPropertyCollection->getPropertyValues();
    $count += $arItem->getQuantity();
  }

  foreach ($arResult["CATEGORIES"]["READY"] as &$item) {
    if($ItemProps[$item["ID"]])
        $item["PROP"] = $ItemProps[$item["ID"]];
  }
  
  unset($item);
  $cp->arResult['PRODUCT_COUNT'] = $count;
}

\CModule::IncludeModule("catalog");
\CModule::IncludeModule("iblock");



foreach ($arResult["CATEGORIES"]["READY"] as &$item)
{
  $mxResult = \CCatalogSku::GetProductInfo($item["PRODUCT_ID"]);
  
  if(is_array($mxResult)) 
  {
    $res = \CIBlockElement::GetByID($mxResult["ID"]);
    if($ar_res = $res->GetNext()){
      $item["NAME"] = $ar_res['NAME'];
    }
  }
  
  if(!empty($item['PROPERTY_MORE_PHOTO_VALUE']))
  {
    $arMoreImages = explode(',', $item['PROPERTY_MORE_PHOTO_VALUE']);
    $arCropImage = CFile::ResizeImageGet($arMoreImages[0], Array('width' => 124, 'height' => 184), BX_RESIZE_IMAGE_EXACT);
    $item['PICTURE_SRC'] = $arCropImage['src'];
  }
  elseif(!empty($item['DETAIL_PICTURE']))
  {
    $arCropImage = CFile::ResizeImageGet($item['DETAIL_PICTURE'], Array('width' => 124, 'height' => 184), BX_RESIZE_IMAGE_EXACT);
    $item['PICTURE_SRC'] = $arCropImage['src'];
  }
}

unset($item);