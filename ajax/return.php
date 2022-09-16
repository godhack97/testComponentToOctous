<?
$siteId = '';
if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
  $siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site_id']), 0, 2);

if ($siteId){
  define('SITE_ID', $siteId);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Localization\Loc;

  Loader::includeModule('highloadblock');

Bitrix\Main\Loader::includeModule("catalog");
$request = Application::getInstance()->getContext()->getRequest();

  $hlblock = HighloadBlockTable::getById(2)->fetch();
  $entity = HighloadBlockTable::compileEntity($hlblock);
  $PsuTaskControl = $entity->getDataClass();

if($request->getQuery("_type") == 'query' && !empty($request->getQuery("q"))) 
{
  
  $article = $request->getQuery("q");
  $arResult = [];
  $arSelect = ["ID", "NAME", "PROPERTY_ENG_NAIMENOVANIE", "PROPERTY_CML2_ARTICLE"];
  $arFilter = ["IBLOCK_ID" => 2, "PROPERTY_CML2_ARTICLE" => $article, 'ACTIVE' => 'Y'];

  $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
  if($arRes = $res->fetch()) 
  {
    $arOffers = CCatalogSKU::getOffersList($arRes['ID'], 0, [], ['ID', 'NAME', 'CATALOG_PRICE_1']);
    $lang = isset($_COOKIE['MANUAL_LANGUAGE']) && !empty($_COOKIE['MANUAL_LANGUAGE']) ? $_COOKIE['MANUAL_LANGUAGE'] : 'RU';
    foreach($arOffers[$arRes['ID']] as $arOffer) {
      $arPrice = [];
      //$arPrice = CPrice::GetBasePrice($arOffer['ID']);
      $arPrices = \CCatalogProduct::GetByIDEx($arOffer['ID']);
      $price = 0;
      $currency = '';
      switch ($_COOKIE["CATALOG_CURRENCY"]){
        case 'USD':
          $price = $arPrices['PRICES'][6]['PRICE'];
          $currency = $arPrices['PRICES'][6]["CURRENCY"];
          break;
        case 'AED':
          $price = $arPrices['PRICES'][7]['PRICE'];
          $currency = $arPrices['PRICES'][7]["CURRENCY"];
          break;
        case 'RUB':
        default:
          $price = $arPrices['PRICES'][1]['PRICE'];
          $currency = $arPrices['PRICES'][1]["CURRENCY"];
          break;
      }
      
      $productName = $lang !== 'RU' ? (!empty($arOffer['PROPERTY_ENG_NAIMENOVANIE_VALUE']) ? $arOffer['PROPERTY_ENG_NAIMENOVANIE_VALUE'] : $arOffer['NAME']) : $arOffer['NAME'];
      
      $arResult[] = [
        'id' => $arOffer['ID'],
        'text' => $productName,
        'article' => $article,
        'price' => $price,
        'currency' => $currency
      ];
    }
  }
  echo json_encode($arResult);
  
} 
elseif($request->get("event") == 'isOrder') 
{
  
  $orderId = intval($request->get("orderId"));
  $phone = intval($request->get("phone"));
  $arResult['error'] = true;

  //$_COOKIE["CATALOG_CURRENCY"]
  if($orderId > 0 && $phone > 0)
  {
    $order = \Bitrix\Sale\Order::load($orderId);
    if($order) {
      $propertyCollection = $order->getPropertyCollection();
      $phoneProp = $propertyCollection->getPhone()->getValue();
      if($phoneProp == $phone) {
        $arResult['error'] = false;
        $basket = $order->getBasket();
  
        $lang = isset($_COOKIE['MANUAL_LANGUAGE']) && !empty($_COOKIE['MANUAL_LANGUAGE']) ? $_COOKIE['MANUAL_LANGUAGE'] : 'RU';
        
        foreach ($basket as $basketItem) {
          $arProductSku = CCatalogProduct::GetByIDEx($basketItem->getProductId());
          $mxResult = CCatalogSku::GetProductInfo($basketItem->getProductId());
          $arProduct = CCatalogProduct::GetByIDEx($mxResult['ID']);
  
          $arPrices = \CCatalogProduct::GetByIDEx($basketItem->getProductId());
          $price = 0;
          $currency = '';
          switch ($_COOKIE["CATALOG_CURRENCY"]){
            case 'USD':
              $price = $arPrices['PRICES'][6]['PRICE'];
              $currency = $arPrices['PRICES'][6]["CURRENCY"];
              break;
            case 'AED':
              $price = $arPrices['PRICES'][7]['PRICE'];
              $currency = $arPrices['PRICES'][7]["CURRENCY"];
              break;
            case 'RUB':
            default:
              $price = $arPrices['PRICES'][1]['PRICE'];
              $currency = $arPrices['PRICES'][1]["CURRENCY"];
              break;
          }
          //$arCurFormat = CCurrencyLang::GetCurrencyFormat($basketItem->getCurrency());
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
          $productName = $lang !== 'RU' ? (!empty($arProduct['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE']) ? $arProduct['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'].' ('.$arProductSku['PROPERTIES']['COLOR']['VALUE'].' ('.$arProductSku['PROPERTIES']['SIZES']['VALUE_ENUM'].'))' : $basketItem->getField('NAME')) : $basketItem->getField('NAME');
          $arCurFormat = CCurrencyLang::GetCurrencyFormat($currency);
          $arResult['data'][] = [
            'id' => $basketItem->getProductId(),
            'text' => $productName,
            'quantity' => $basketItem->getQuantity(),
            //'price' => str_replace("#", number_format($basketItem->getFinalPrice(), 0, '', ' '), $arCurFormat["FORMAT_STRING"]),
            'price' => str_replace("#", number_format($price, 0, '', ' '), $arCurFormat["FORMAT_STRING"]),
            //'price_number' => number_format($basketItem->getFinalPrice(), 0, '', ''),
            'price_number' => number_format($price, 0, '', ''),
            'size' => $arProductSku['PROPERTIES']['SIZES']['VALUE_ENUM'],
            'articul' => $arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE']
          ];
        }
        $totalPrice = 0;
        foreach ($arResult['data'] as $dItem) {
          $totalPrice += $dItem['price_number'] * $dItem['quantity'];
        }
        $arResult['totalPrice'] = number_format($totalPrice, 0, '', ' ');
      } else {
        $arResult['message'] = 'Ошибка. Не верный номер заказа или телефона';
      }
    } else {
      $arResult['message'] = 'Ошибка. Не верный номер заказа';
    }
  }

  echo json_encode($arResult);
  
}