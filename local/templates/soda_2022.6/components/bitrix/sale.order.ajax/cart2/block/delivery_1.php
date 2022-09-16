<?global $USER;
$arDeliveries = $arResult["JS_DATA"]["DELIVERY"];
//dbgz($arDeliveries);

$arFreeID = [66, 47, 60, 72, 3, 67];
$arDeliveriesLeft = [];
$arDeliveriesRight = []; 

$itemsFlexEnd = false;

foreach($arResult["JS_DATA"]["ORDER_PROP"]["properties"] as $arProp)
{
  if($arProp['CODE'] == 'LOCATION')
  {
    $arLocs = CSaleLocation::GetByID($arProp['VALUE'][0], LANGUAGE_ID);
    if(!in_array($arLocs['CITY_ID'], Array(134, 135)))
    {
      $itemsFlexEnd = true;
    }
  }
}

if(count($arDeliveries) > 1)
{
  foreach ($arDeliveries as $arDelivery)
  {
    if($arDelivery['ID'] == 47)
    {
      $arDelivery['DESCRIPTION'] = str_replace('<a class="btn btn-default"', '<a class="black js-pp-pvz"', $arDelivery['DESCRIPTION']);
      $arDelivery['DESCRIPTION'] = str_replace('Выбрать пункт доставки', 'Выбрать пункт выдачи', $arDelivery['DESCRIPTION']);
    }
    
    if($arDelivery['ID'] == 60)
    {
      $arDelivery['PERIOD_TEXT'] = substr($arDelivery['PERIOD_TEXT'], strpos($arDelivery['PERIOD_TEXT'], '<input'));
      $arDelivery['DESCRIPTION'] .= $arDelivery['PERIOD_TEXT'];
    }
    
    if(in_array($arDelivery['ID'], $arFreeID))
      $arDeliveriesRight[ $arDelivery['ID'] ] = $arDelivery;
    else
      $arDeliveriesLeft[ $arDelivery['ID'] ] = $arDelivery;
  }
}

$isTwoCols = false;
if(!empty($arDeliveriesRight) && !empty($arDeliveriesLeft))
{
  $isTwoCols = true;
  $arDeliveries = $arDeliveriesLeft;
}
?>

<h1>Способы доставки</h1>
<br/>

<?if($isTwoCols):?>
  <div class="items-to-cols">
<?endif?>
    
<div class="items<?if(count($arResult["JS_DATA"]["DELIVERY"]) <= 1):?> count-1<?endif?><?if($itemsFlexEnd):?> items-flex-end<?endif?>">
  <?foreach ($arDeliveries as $delivery):?>
    <div class="payment<?if($delivery['CHECKED'] == 'Y'):?> active<?endif?>">
      <div class="field-radio">
        <label> 
          <input
            id="ID_DELIVERY_ID_<?=$delivery["ID"]?>"
            value="<?=$delivery["ID"]?>"
            type="radio"
            name="DELIVERY_ID"
            <?if($delivery["CHECKED"]=="Y"){?>checked=""<?}?>
            aria-required="true" required="">
          <div class="label"><?=$delivery["OWN_NAME"]?> (<?=$arResult['DELIVERY_PRICE_CUSTOM'][$delivery["ID"]]?>)</div>
          <div class="description"><?=$delivery["DESCRIPTION"]?></div>
          <?if($delivery["ID"] == 72):?>
            <div class="description" id="pvz-insert"></div>
          <?endif?>
        </label>
      </div>
    </div>
  <?endforeach?>
</div>
    
<?if($isTwoCols):?>
    
    <div class="items">
      <?foreach ($arDeliveriesRight as $delivery):?>
        <div class="payment<?if($delivery['CHECKED'] == 'Y'):?> active<?endif?>">
          <div class="field-radio">
            <label> 
              <input
                id="ID_DELIVERY_ID_<?=$delivery["ID"]?>"
                value="<?=$delivery["ID"]?>"
                type="radio"
                name="DELIVERY_ID"
                <?if($delivery["CHECKED"]=="Y"){?>checked=""<?}?>
                aria-required="true" required="">
              <div class="label"><?=$delivery["OWN_NAME"]?> (<?=$arResult['DELIVERY_PRICE_CUSTOM'][$delivery["ID"]]?>)</div>
              <div class="description"><?=$delivery["DESCRIPTION"]?></div>
              <?if($delivery["ID"] == 72):?>
                <div class="description" id="pvz-insert"></div>
              <?endif?>
            </label>
          </div>
        </div>
      <?endforeach?>
    </div>
    
  </div>
<?endif?>
