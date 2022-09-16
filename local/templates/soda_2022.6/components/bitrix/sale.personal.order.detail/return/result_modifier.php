<?
use \Bitrix\Main\Localization\Loc;
  use Bitrix\Main\Loader;
  use Bitrix\Highloadblock\HighloadBlockTable;
  
  Loader::includeModule('highloadblock');
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


function arrayClearImplode($arr, $delimiter) {
  $arr = array_diff($arr, array(''));
  $arr = implode($delimiter, $arr);
  return $arr;
}

// Считаем количество товаров
$totalQuantity = 0;
  
foreach ($arResult['BASKET'] as $item) {
  $totalQuantity += $item['QUANTITY'];
}
  
$hlblock = HighloadBlockTable::getById(2)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$PsuTaskControl = $entity->getDataClass();

$lang = isset($_COOKIE['MANUAL_LANGUAGE']) && !empty($_COOKIE['MANUAL_LANGUAGE']) ? $_COOKIE['MANUAL_LANGUAGE'] : 'RU';

foreach ($arResult['BASKET'] as &$basketItem) {
  $arProductSku = CCatalogProduct::GetByIDEx($basketItem['PRODUCT_ID']);
  $mxResult = CCatalogSku::GetProductInfo($basketItem['PRODUCT_ID']);
  $arProduct = CCatalogProduct::GetByIDEx($mxResult['ID']);
  if ($arProductSku['PROPERTIES']['COLOR']['VALUE']) {
    $arProductSku['PROPERTIES']['VALUE_ENUM_ID'] = $arProductSku['PROPERTIES']['COLOR']['VALUE'];
    $arProductSku['PROPERTIES']['VALUE_XML_ID'] = $arProductSku['PROPERTIES']['COLOR']['VALUE'];
    $reslist = $PsuTaskControl::getList([
      'filter' => [
        'UF_XML_ID' => $arProductSku['PROPERTIES']['COLOR']['VALUE'],
      ],
    ]);
    if ($el = $reslist->fetch()) {
      $arProductSku['PROPERTIES']['COLOR']['VALUE'] = ($lang !== 'RU') ? $el['UF_NAME_EN'] : $el['UF_NAME'];
      $arProductSku['PROPERTIES']['COLOR']['HEX'] = $el['UF_HEX'];
    }
  }
  $productName = $lang !== 'RU' ? (!empty($arProduct['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']) ? $arProduct['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'].' ('.$arProductSku['PROPERTIES']['COLOR']['VALUE'].' ('.$arProductSku['PROPERTIES']['SIZES']['VALUE_ENUM'].'))' : $basketItem['NAME']) : $basketItem['NAME'];
  $basketItem['NAME'] = $productName;
}
$arResult['TOTAL_QUANTITY'] = $totalQuantity;

switch ($totalQuantity) {
  case 0:
  case 5:
  case 6:
  case 7:
  case 8:
  case 9:
    $arResult['GOODS_COUNT'] = Loc::getMessage('SALE_PERSONAL_PRODUCTS');
    break;
  case 2:
  case 3:
  case 4:
    $arResult['GOODS_COUNT'] = Loc::getMessage('SALE_PERSONAL_PRODUCT');
    break;
  default:
    $arResult['GOODS_COUNT'] = Loc::getMessage('SALE_PERSONAL_PRODUCT_ONE');
}

// Получаем адрес доставки
foreach ($arResult['ORDER_PROPS'] as $arOrderProp) {
  if ($arOrderProp['IS_ADDRESS'] == 'Y') {
    $arResult['DELIVERY_ADDRESS'] = $arOrderProp['VALUE'];
    break;
  }
}