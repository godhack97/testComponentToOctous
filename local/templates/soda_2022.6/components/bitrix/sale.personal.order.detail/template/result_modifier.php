<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main,
    Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Sale\PaySystem;
use Bitrix\Main\Localization\Loc;

  Loader::includeModule("iblock");
  
  Loader::includeModule("catalog");
  Loader::includeModule("iblock");
  
  Loader::includeModule('highloadblock');
  
  $hlblock = HighloadBlockTable::getById(2)->fetch();
  $entity = HighloadBlockTable::compileEntity($hlblock);
  $PsuTaskControl = $entity->getDataClass();


// Комбинируем информацию о пользователе
$userName = array(
  $arResult['USER']['LAST_NAME'],
  $arResult['USER']['NAME'],
  $arResult['USER']['SECOND_NAME']
);
$userName = arrayClearImplode($userName, ' ');

$userData = array(
  $userName,
  $arResult['USER']['PERSONAL_PHONE'],
  $arResult['USER']['EMAIL']
);
$userData = arrayClearImplode($userData, ', ');
$arResult['USER']['USER_DATA_FORMATTED'] = $userData;


$userInfo = array();
if(is_array($arResult['ORDER_PROPS']) && count($arResult['ORDER_PROPS']))
foreach($arResult['ORDER_PROPS'] as $arProp)
  switch ($arProp['CODE']){
    case 'CONTACT_PERSON':
    case 'PHONE':
    case 'EMAIL':
      $userInfo[] = $arProp['VALUE'];
      break;
  }
  
$userInfo = arrayClearImplode($userInfo, ', ');
$arResult['USER']['USER_INFO_FORMATTED'] = $userInfo;


function arrayClearImplode($arr, $delimiter) {
  $arr = array_diff($arr, array(''));
  $arr = implode($delimiter, $arr);
  return $arr;
}

// Считаем количество товаров
$totalQuantity = 0;
if(is_array($arResult['BASKET']) && count($arResult['BASKET'])){
  foreach ($arResult['BASKET'] as $item) {
    $totalQuantity += $item['QUANTITY'];
  }
}

$arResult['TOTAL_QUANTITY'] = $totalQuantity;

switch ($totalQuantity) {
  case 0:
  case 5:
  case 6:
  case 7:
  case 8:
  case 9:
    $arResult['GOODS_COUNT'] = 'товаров';
    break;
  case 2:
  case 3:
  case 4:
    $arResult['GOODS_COUNT'] = 'товара';
    break;
  default:
    $arResult['GOODS_COUNT'] = 'товар';
}
  
  if(is_array($arResult['ORDER_PROPS']) && count($arResult['ORDER_PROPS'])){
// Получаем адрес доставки
    foreach ($arResult['ORDER_PROPS'] as $arOrderProp) {
      if ($arOrderProp['IS_ADDRESS'] == 'Y') {
        $arResult['DELIVERY_ADDRESS'] = $arOrderProp['VALUE'];
        break;
      }
    }
  }
  if(is_array($arResult['BASKET']) && count($arResult['BASKET'])){
    foreach ($arResult['BASKET'] as &$item) {
      $newName = false;
      $engColor = false;
      $sizes = false;
      //dbgz($item);
      $colorXmlID = false;
      foreach ($item['PROPS'] as $arProp) {
        if ($arProp['CODE'] == 'COLOR' && !empty($arProp['SKU_VALUE']['XML_ID'])) {
          $colorXmlID = $arProp['SKU_VALUE']['XML_ID'];
        }
        if ($arProp['CODE'] === 'SIZES') {
          $size = $arProp['VALUE'];
        }
      }
      
      $mxResult = \CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
  
      if (is_array($mxResult)) {
        if($colorXmlID === false){
          $res = CIBlockElement::GetProperty(CATALOG_OFFERS_ID, $item['PRODUCT_ID'], ["sort" => "asc"], ["CODE" => "COLOR"]);
          while ($ob = $res->GetNext()) {
            $colorXmlID = $ob['VALUE'];
          }
          $res = CIBlockElement::GetProperty(CATALOG_ID, $mxResult['ID'], ["sort" => "asc"], ["CODE" => "ENG_NAIMENOVANIE"]);
          while ($ob = $res->GetNext()) {
            $newName = $ob['VALUE'];
            $reslist = $PsuTaskControl::getList([
              'filter' => [
                'UF_XML_ID' => $colorXmlID,
              ],
            ]);
            if ($el = $reslist->fetch()) {
              $engColor = $el['UF_NAME_EN'];
            }
          }
        }
        else {
          $res = CIBlockElement::GetProperty(CATALOG_ID, $mxResult['ID'], ["sort" => "asc"], ["CODE" => "ENG_NAIMENOVANIE"]);
          while ($ob = $res->GetNext()) {
            $newName = $ob['VALUE'];
            $reslist = $PsuTaskControl::getList([
              'filter' => [
                'UF_XML_ID' => $colorXmlID,
              ],
            ]);
            if ($el = $reslist->fetch()) {
              $engColor = $el['UF_NAME_EN'];
            }
          }
        }
  
        $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'SORT', 'DATE_ACTIVE_FROM', 'PROPERTY_*'];
        $arFilter = [
          'IBLOCK_ID' => COLORS_ID,
          'ACTIVE_DATE' => 'Y',
          'ACTIVE' => 'Y',
          'PROPERTY_PARENT' => $mxResult['ID'],
          'PROPERTY_COLOR' => $colorXmlID,
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
              BX_RESIZE_IMAGE_EXACT
            );
            $item['PICTURE']['SRC'] = $arPhotoSmall['src'];
          }
        }
        $item['NAME'] = (Loc::getCurrentLang() == 'en') ? $newName.' ('.$engColor.' ('.$size.'))' : $item['NAME'];
      }
    }
    unset($item);
  }


// Подставляем актуальную картинку