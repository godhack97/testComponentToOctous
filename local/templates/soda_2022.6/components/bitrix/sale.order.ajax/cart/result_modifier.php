<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Sale\Order,
  Bitrix\Sale\Basket,
  Bitrix\Sale\Delivery,
  Bitrix\Currency\CurrencyManager,
  Bitrix\Sale\PaySystem,
  Bitrix\Main\Localization\Loc,
  Bitrix\Sale;
  
	use \Bitrix\Main\UserPhoneAuthTable;
/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

global $USER, $APPLICATION;



// Функция сортировки
function sortArrayInSort($x, $y)
{
  return ($x['SORT'] > $y['SORT']);
}
if(is_array($arResult['JS_DATA']['ORDER_PROP']['properties']) && count($arResult['JS_DATA']['ORDER_PROP']['properties'])){
  uasort($arResult['JS_DATA']['ORDER_PROP']['properties'], 'sortArrayInSort');
}



if($USER->GetID())
{
  $arResult['DOMESTIC_ACCOUNT'] =  CSaleUserAccount::GetByUserID($USER->GetID(), 'RUB');

  $arResult['BONUS']['CURRENT'] = intval($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']);
  $arResult['BONUS']['AMOUNT'] = 0;
  $arResult['BONUS']['PAYMENT'] = 0;
  $arResult['BONUS']['TOTAL'] = 0;
  if($arResult['DOMESTIC_ACCOUNT'])
  {
    if($arResult["ORDER_TOTAL_PRICE"] < $arResult['BONUS']['CURRENT'])
    {
      $arResult['BONUS']['AMOUNT'] = intval(($arResult["ORDER_TOTAL_PRICE"] / 100) * PERCENT_PAY_BONUS);
      $arResult['BONUS']['PAYMENT'] = $arResult["ORDER_TOTAL_PRICE"] - $arResult['BONUS']['AMOUNT'];//number_format((), 0,'', ' ').' руб.';
      $arResult['BONUS']['TOTAL'] = $arResult['BONUS']['CURRENT'] - $arResult['BONUS']['AMOUNT'];
    }
    else
    {
      $arResult['BONUS']['AMOUNT'] = intval(($arResult["ORDER_TOTAL_PRICE"] / 100) * PERCENT_PAY_BONUS);
      if($arResult['BONUS']['AMOUNT'] > $arResult['BONUS']['CURRENT']){
        $arResult['BONUS']['AMOUNT'] = $arResult['BONUS']['CURRENT'];
        $arResult['BONUS']['PAYMENT'] = $arResult["ORDER_TOTAL_PRICE"] - $arResult['BONUS']['AMOUNT'];
        $arResult['BONUS']['TOTAL'] = 0;
      }
      else
      {
        $arResult['BONUS']['PAYMENT'] = $arResult["ORDER_TOTAL_PRICE"] - $arResult['BONUS']['AMOUNT'];
        $arResult['BONUS']['TOTAL'] = $arResult['BONUS']['CURRENT'] - $arResult['BONUS']['AMOUNT'];
      }
    }
    // total - (5900 / 2)
  }

  $userPhoneCheck = UserPhoneAuthTable::getList(["filter" =>	["=USER_ID" => $USER->GetID()]])->fetch();
  $arResult['PHONE_CONFIRMED'] = $userPhoneCheck['CONFIRMED'] === 'Y';
}

function getDeliveryPriceForProduct($deliveryId, $userCityId)
{
  $basket = Basket::loadItemsForFUser(Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
  $virtualOrder = Order::create(\Bitrix\Main\Context::getCurrent()->getSite(), Sale\Fuser::getId());
  $virtualOrder->setPersonTypeId(1);
  $virtualOrder->setBasket($basket);

  /** @var \Bitrix\Sale\PropertyValueCollection $orderProperties */
  $orderProperties = $virtualOrder->getPropertyCollection();
  /** @var \Bitrix\Sale\PropertyValue $orderDeliveryLocation */
  $orderDeliveryLocation = $orderProperties->getDeliveryLocation();
  $orderDeliveryLocation->setValue($userCityId); // В какой город "доставляем" (куда доставлять).

  /** @var \Bitrix\Sale\ShipmentCollection $shipmentCollection */
  $shipmentCollection = $virtualOrder->getShipmentCollection();

  $delivery = Sale\Delivery\Services\Manager::getObjectById($deliveryId);
  /** @var \Bitrix\Sale\Shipment $shipment */
  $shipment = $shipmentCollection->createItem($delivery);

  $calcResult = $shipment->calculateDelivery();
  $deliveryPeriodDescription = $calcResult->getPeriodDescription();

  /** @var \Bitrix\Sale\ShipmentItemCollection $shipmentItemCollection */
  $shipmentItemCollection = $shipment->getShipmentItemCollection();
  /** @var \Bitrix\Sale\BasketItem $basketItem */
  
  foreach ($basket as $basketItem) {
    $item = $shipmentItemCollection->createItem($basketItem);
    $item->setQuantity($basketItem->getQuantity());
  }

  $deliveryPrice = number_format($virtualOrder->getDeliveryPrice(), 0,'', ' ');
  if ($deliveryPrice === '') {
    $deliveryPrice = null;
  }

  if($deliveryPrice > 0)
    $deliveryPrice .= ' '.Loc::getMessage('SALE_ORDER_JS_CURRENCY_RUB');
  else if($deliveryPrice == 0 && in_array($deliveryId, Array(47, 3, 67, 74)))
    $deliveryPrice = Loc::getMessage('SALE_ORDER_JS_FREE');

  $result['COST'] = $deliveryPrice;
  
  $arLocs = CSaleLocation::GetByID($userCityId, LANGUAGE_ID);
  if($arLocs['COUNTRY_NAME'] != Loc::getMessage('SALE_ORDER_JS_RUSSIA'))
    $result['COST'] = '';
  
  
  if(!empty($deliveryPeriodDescription))
  {
    if(in_array($deliveryId, Array(60, 61))) // DPD
    {
      $deliveryPeriodDescription = (int)str_replace(Loc::getMessage('SALE_ORDER_JS_D'), '', $deliveryPeriodDescription);
      $deliveryPeriodDescription = $deliveryPeriodDescription .' '. wordMorphos($deliveryPeriodDescription, Loc::getMessage('SALE_ORDER_JS_DA'), Loc::getMessage('SALE_ORDER_JS_DAY'), Loc::getMessage('SALE_ORDER_JS_DAYS'));
    }
    
    if(in_array($deliveryId, Array(76))) // DHL
    {
      $deliveryPeriodDescription = (int)str_replace(Loc::getMessage('SALE_ORDER_JS_DELIVERY_ESTIMATED'), '', $deliveryPeriodDescription);
      $deliveryPeriodDescription = $deliveryPeriodDescription .' '. wordMorphos($deliveryPeriodDescription, Loc::getMessage('SALE_ORDER_JS_DA'), Loc::getMessage('SALE_ORDER_JS_DAY'), Loc::getMessage('SALE_ORDER_JS_DAYS'));
    }
    
    if(in_array($deliveryId, Array(47))) // PickPoint
    {
      $intTmp = (int)$deliveryPeriodDescription;
      if(empty($intTmp) || $deliveryPeriodDescription == Loc::getMessage('SALE_ORDER_JS_ZERO_DAYS'))
        $deliveryPeriodDescription = '';
    }
     
    $result['PERIOD'] = $deliveryPeriodDescription;
  }
  
  return $result;
}


$arDeliveryPrice = [];
foreach($arResult['DELIVERY'] as $key => $val) 
{
  $cacheId = $arResult['USER_VALS']['ORDER_PROP'][2] .'_'. $key;
  $cache = \Bitrix\Main\Application::getInstance()->getManagedCache();
  
  if ($cache->read(86400, $cacheId))
  {
    $arDeliveryPrice[$key] = $cache->get($cacheId)[$key];
  }
  else
  {
    $arDeliveryPrice[$key] = getDeliveryPriceForProduct(
      $key,
      $arResult['USER_VALS']['ORDER_PROP'][2]
    );

    $cache->set($cacheId, array($key => $arDeliveryPrice[$key]));
  }
	if(!$arResult['USE_EXTEND_CURRENCY']){
		$arResult['USE_EXTEND_CURRENCY'] = isset($_COOKIE["CATALOG_CURRENCY"]) && !empty($_COOKIE["CATALOG_CURRENCY"]) && $_COOKIE["CATALOG_CURRENCY"] !== $val['CURRENCY'];
		$arResult['CURRENCY_ORDER'] = $val['CURRENCY'];
	}
}
	foreach ($arDeliveryPrice as &$price){
		if(preg_match('/\d/', $price['COST'])){
			$price['COST_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency(str_replace(' руб.', '', $price['COST']), $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
		}
	}


$arResult['DELIVERY_PRICE_CUSTOM'] = $arDeliveryPrice;
	$arResult['PRICE_WITHOUT_DISCOUNT_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['PRICE_WITHOUT_DISCOUNT_VALUE'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	$arResult['BASKET_PRICE_DISCOUNT_DIFF_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['BASKET_PRICE_DISCOUNT_DIFF_VALUE'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	$arResult['ORDER_TOTAL_PRICE_FORMATED_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	$arResult['ORDER_PRICE_FORMATED_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	$arResult['VAT_SUM_FORMATED_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['VAT_SUM'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	$arResult['DISCOUNT_PRICE_FORMATED_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['DISCOUNT_PRICE'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	$arResult['DELIVERY_PRICE_FORMATED_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	$arResult['PAY_SYSTEM_PRICE_FORMATTED_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($arResult['JS_DATA']['TOTAL']['PAY_SYSTEM_PRICE'], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);

//unset($arResult['ORDER_PROP']['USER_PROPS_Y'][2]['VARIANTS']);
//unset($arResult['ORDER_PROP']['USER_PROPS_Y'][2]['~VARIANTS']);
//dbgz($arResult);
	
	
	$newPriceWithoutDiscount = 0;
	$baseCurrency = false;
	foreach ($arResult['BASKET_ITEMS'] as $basket) {
		if(!$baseCurrency){
			$baseCurrency = $basket['CURRENCY'];
		}
		$mxResult = \CCatalogSku::GetProductInfo($basket['PRODUCT_ID']);
		$res = \CIBlockElement::GetByID($mxResult['ID']);
		$arFields = [];
		if ($ar_res = $res->GetNext()) {
			$itemProduct = $ar_res;
			$arFilter = [
				'IBLOCK_ID' => CATALOG_ID,
				'ACTIVE' => 'Y',
				'=ID' => $itemProduct,
			];
			$res = CIBlockElement::GetList([], $arFilter, false, array('nPageSize' => 1), []);
			if ($ob = $res->GetNextElement()) {
				$arFields = $ob->GetProperties();
			}
			//if(isset($arFields['PROTSENT_IZMENENIYA_TSENY']) && !empty($arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE'])){
			
			//}
			$baseCurrency = $basket['CURRENCY'];
			$basket['IS_CUSTOM_PRICE'] = true;
			$newPriceWithoutDiscount += ($basket['SUM_BASE'] + ($basket['SUM_BASE']/100*$arFields['PROTSENT_IZMENENIYA_TSENY']['VALUE']));
			$basket['SUM_BASE_FORMATED'] = CCurrencyLang::CurrencyFormat($basket['SUM_BASE'], $basket['CURRENCY'], true);
		}
	}
	if($newPriceWithoutDiscount > 0){
		$arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] = $newPriceWithoutDiscount;
		$arResult['PRICE_WITHOUT_DISCOUNT'] = SaleFormatCurrency($arResult['PRICE_WITHOUT_DISCOUNT_VALUE'], $baseCurrency);
		$arResult['PRICE_WITHOUT_DISCOUNT_EXTEND'] = SaleFormatCurrency(CCurrencyRates::ConvertCurrency($newPriceWithoutDiscount, $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]);
	}
//dbgz($arResult["JS_DATA"]["ORDER_PROP"]["properties"]);
//
//global $APPLICATION;
//$VISITOR_ID = $APPLICATION->get_cookie("VISITOR_ID");
	global $USER;
	
	if($USER->IsAdmin()){
		//var_dump($basket['SUM_BASE']);
		//var_dump($arResult['PRICE_WITHOUT_DISCOUNT_VALUE']);
		//var_dump($arResult);
	}


if(empty($arParams['USER_SELECTION']))
{
  if(is_array($arResult['JS_DATA']['ORDER_PROP']['properties']) && count($arResult['JS_DATA']['ORDER_PROP']['properties'])){
    foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as $index => $arProp) {
      //if ($arProp['CODE'] == 'LOCATION')
      //  continue;
    
      $cookieValue = '';
      if (!empty($APPLICATION->get_cookie('ORDER_PROP_' . $arProp['ID'])))
        $cookieValue = $APPLICATION->get_cookie('ORDER_PROP_' . $arProp['ID']);
    
      global $USER;
    
      if ($USER->IsAdmin()) {
        //var_dump("$ arProp['ID']");
        //var_dump($arProp['ID']);
        //var_dump("$ cookieValue");
        //var_dump($cookieValue);
      }
      if (!empty($cookieValue)) {
        $arResult['JS_DATA']['ORDER_PROP']['properties'][$index]['VALUE'][0] = $cookieValue;
      }
    }
  }

  if(!empty($APPLICATION->get_cookie('ORDER_DESCRIPTION')))
    $arResult["JS_DATA"]["ORDER_DESCRIPTION"] = $APPLICATION->get_cookie('ORDER_DESCRIPTION');
  
}


if($arParams['USER_SELECTION'] == 'Y') // Order create
{
  
//  foreach($arResult["JS_DATA"]["ORDER_PROP"]["properties"] as $index => $arProp)
//  {
//    if($arProp['CODE'] == 'LOCATION')
//      continue;
//
//    $arResult["JS_DATA"]["ORDER_PROP"]["properties"][ $index ]['VALUE'][0] = '';
//  }
  
}