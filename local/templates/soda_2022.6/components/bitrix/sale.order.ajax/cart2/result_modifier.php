<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Sale\Order,
  Bitrix\Sale\Basket,
  Bitrix\Sale\Delivery,
  Bitrix\Currency\CurrencyManager,
  Bitrix\Sale\PaySystem,
  Bitrix\Sale;
/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

global $USER;



// Функция сортировки
function sortArrayInSort($x, $y)
{
  return ($x['SORT'] > $y['SORT']);
}
uasort($arResult["JS_DATA"]["ORDER_PROP"]["properties"], 'sortArrayInSort');



if($USER->GetId())
{
  $arResult['DOMESTIC_ACCOUNT'] =  CSaleUserAccount::GetByUserID($USER->GetId(), 'RUB');
  
  if($arResult['DOMESTIC_ACCOUNT'])
  {
    if($arResult["ORDER_TOTAL_PRICE"] < $arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'])
    {
      $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT"] = 0;
    }
    else
    {
      $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT"] = $arResult["ORDER_TOTAL_PRICE"] - $arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'];
    }
    
    $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_FORMATED"] = number_format($arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT"], 0, '', ' ') .' руб.';
  }
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
    $deliveryPrice .= ' руб.';
  else if($deliveryPrice == 0 && in_array($deliveryId, Array(47, 3, 67, 74)))
    $deliveryPrice = 'бесплатно';
    
  $result['COST'] = $deliveryPrice;
  
  $arLocs = CSaleLocation::GetByID($userCityId, LANGUAGE_ID);
  if($arLocs['COUNTRY_NAME'] != 'Россия')
    $result['COST'] = '';
  
  
  if(!empty($deliveryPeriodDescription))
  {
    if(in_array($deliveryId, Array(60, 61))) // DPD
    {
      $deliveryPeriodDescription = (int)str_replace('дн.', '', $deliveryPeriodDescription);
      $deliveryPeriodDescription = $deliveryPeriodDescription .' '. wordMorphos($deliveryPeriodDescription, 'день', 'дня', 'дней');
    }
    
    if(in_array($deliveryId, Array(76))) // DHL
    {
      $deliveryPeriodDescription = (int)str_replace('Приблизительное число дней доставки:', '', $deliveryPeriodDescription);
      $deliveryPeriodDescription = $deliveryPeriodDescription .' '. wordMorphos($deliveryPeriodDescription, 'день', 'дня', 'дней');
    }
    
    if(in_array($deliveryId, Array(47))) // PickPoint
    {
      $intTmp = (int)$deliveryPeriodDescription;
      if(empty($intTmp) || $deliveryPeriodDescription == '0 дней')
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
}


$arResult['DELIVERY_PRICE_CUSTOM'] = $arDeliveryPrice;

//unset($arResult['ORDER_PROP']['USER_PROPS_Y'][2]['VARIANTS']);
//unset($arResult['ORDER_PROP']['USER_PROPS_Y'][2]['~VARIANTS']);
